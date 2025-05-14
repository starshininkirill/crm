<?php

namespace App\Models\Traits;

use App\Models\History;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
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

    public static function getLatestHistoricalRecords($date, array $withRelations = [])
    {
        return self::getLatestHistoricalRecordsQuery($date, $withRelations)
            ->get()
            ->map(function ($history) use ($withRelations, $date) {
                return self::recreateModelWithRelations($history, $withRelations, $date);
            });
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

    protected function isValidRelation($relation)
    {
        return method_exists($this, $relation) &&
            $this->$relation() instanceof Relation;
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
