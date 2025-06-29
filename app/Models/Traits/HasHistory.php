<?php

namespace App\Models\Traits;

use App\Models\History;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait HasHistory
{
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';

    public static function bootHasHistory()
    {
        static::created(function ($model) {
            $model->recordHistory(self::ACTION_CREATED);
        });

        static::updated(function ($model) {
            $model->recordHistory(self::ACTION_UPDATED);
        });

        static::deleted(function ($model) {
            $model->recordHistory(self::ACTION_DELETED);
        });
    }


    public static function getLatestHistoricalRecordsQuery($date, array $withRelations = [])
    {
        return History::whereIn('id', function ($query) use ($date) {
            $query->select(DB::raw('MAX(id)'))
                ->from('histories')
                ->where('historyable_type', self::class);

            if ($date) {
                $query->whereDate('created_at', '<=', $date);
            }

            $query->groupBy('historyable_id');
        })->with('historyable');
    }

    public static function getLatestHistoricalRecords($date, array $withRelations = []): Collection
    {
        $baseHistories = self::getLatestHistoricalRecordsQuery($date)->get();

        if ($baseHistories->isEmpty()) {
            return collect();
        }

        $models = $baseHistories->mapWithKeys(function ($history) {
            $model = self::recreateFromHistory($history);
            // Temporarily store the history record to access its 'action' property later
            $model->setRelation('latestHistory', $history);
            return [$model->getKey() => $model];
        });

        self::loadHistoricalRelationsForCollection($models, $withRelations, $date);

        return $models->filter(function ($model) {
            // Filter out models that were marked as deleted
            return optional($model->latestHistory)->action !== self::ACTION_DELETED;
        })->values();
    }


    public static function recreateFromQuery($histories, array $withRelations = [], $date = null)
    {
        return $histories->get()->map(function ($history) use ($withRelations, $date) {
            return self::recreateModelWithRelations($history, $withRelations, $date);
        });
    }

    public function history(): MorphMany
    {
        return $this->morphMany(History::class, 'historyable');
    }

    public function scopeHistorical($query, $date = null, array $withRelations = [])
    {
        $modelIds = $query->pluck($query->getModel()->getQualifiedKeyName());

        if ($modelIds->isEmpty()) {
            return collect();
        }

        $date = $date ? Carbon::parse($date) : Carbon::now();

        // Get the latest history records for the queried models
        $histories = History::whereIn('id', function ($subQuery) use ($date, $modelIds) {
            $subQuery->select(DB::raw('MAX(id)'))
                ->from('histories')
                ->where('historyable_type', self::class)
                ->whereIn('historyable_id', $modelIds)
                ->whereDate('created_at', '<=', $date)
                ->groupBy('historyable_id');
        })->get();

        $models = $histories->mapWithKeys(function ($history) {
            $model = self::recreateFromHistory($history);
            $model->setRelation('latestHistory', $history);
            return [$model->getKey() => $model];
        });

        self::loadHistoricalRelationsForCollection($models, $withRelations, $date);

        return $models->filter(function ($model) {
            return optional($model->latestHistory)->action !== self::ACTION_DELETED;
        })->values();
    }


    public function getVersionAtDate($date, array $withRelations = [])
    {
        $history = $this->history()
            ->whereDate('created_at', '<=', $date)
            ->latest()
            ->first();

        if (!$history) {
            return null;
        }

        return self::recreateModelWithRelations($history, $withRelations, $date);
    }

    protected function recordHistory($action)
    {
        $data = $this->getAttributes();

        History::create([
            'historyable_type' => get_class($this),
            'historyable_id' => $this->getKey(),
            'action' => $action,
            'new_values' => $data,
        ]);
    }

    protected static function loadHistoricalRelationsForCollection(Collection $models, array $relations, $date): void
    {
        if (empty($relations) || $models->isEmpty()) {
            return;
        }

        $modelInstance = $models->first();

        foreach ($relations as $relationName) {
            if (!$modelInstance->isValidRelation($relationName)) {
                continue;
            }

            /** @var Relation $relation */
            $relation = $modelInstance->$relationName();
            $relatedModelClass = get_class($relation->getRelated());
            
            if (!$relation instanceof BelongsTo) {
                // This optimization currently supports BelongsTo.
                // Add logic for other relation types if needed.
                continue;
            }

            $foreignKey = $relation->getForeignKeyName();
            $ownerKey = $relation->getOwnerKeyName();

            $foreignKeyValues = $models->pluck($foreignKey)->filter()->unique()->values();

            if ($foreignKeyValues->isEmpty()) {
                continue;
            }
            
            $relatedHistoricalModels = $relatedModelClass::getLatestHistoricalRecords($date)
                ->keyBy($ownerKey);
            
            $models->each(function ($model) use ($relationName, $foreignKey, $relatedHistoricalModels) {
                $relatedModel = $relatedHistoricalModels->get($model->{$foreignKey});
                $model->setRelation($relationName, $relatedModel);
            });
        }
    }

    protected static function recreateModelWithRelations($history, array $withRelations, $date)
    {
        $model = self::recreateFromHistory($history);

        $tempInstance = $model->exists ? $model : new static;

        foreach ($withRelations as $relation) {
            if ($tempInstance->isValidRelation($relation)) {
                $model->loadHistoricalRelation($model, $relation, $date);
            }
        }

        return $model;
    }


    protected function loadHistoricalRelation($model, $relation, $date)
    {
        $related = $model->$relation;
        
        if ($related && method_exists($related, 'getVersionAtDate')) {
            $model->setRelation(
                $relation,
                $related->getVersionAtDate($date)
            );
        } elseif (is_iterable($related)) {
            $collection = $related->map(function ($item) use ($date) {
                return method_exists($item, 'getVersionAtDate')
                    ? $item->getVersionAtDate($date)
                    : $item;
            });
            $model->setRelation($relation, $collection);
        }
    }

    protected function isValidRelation($relation)
    {
        return method_exists($this, $relation) &&
            $this->$relation() instanceof Relation;
    }


    protected static function recreateFromHistory(History $history)
    {
        $model = new static;
        $model->forceFill($history->new_values);

        foreach ($model->getCasts() as $attribute => $cast) {
            if (array_key_exists($attribute, $history->new_values)) {
                $model->setAttribute($attribute, $model->castAttribute($attribute, $history->new_values[$attribute]));
            }
        }

        $model->exists = $history->action !== 'created';
        return $model;
    }
}
