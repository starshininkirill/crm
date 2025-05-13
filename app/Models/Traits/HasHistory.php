<?php

namespace App\Models\Traits;

use App\Models\History;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

trait HasHistory
{
    public static function bootHasHistory()
    {
        static::created(function ($model) {
            $model->recordHistory('created');
        });

        static::updated(function ($model) {
            $model->recordHistory('updated');
        });

        static::deleted(function ($model) {
            $model->recordHistory('deleted');
        });
    }

    public static function getLatestHistoricalRecords($date, array $withRelations = [])
    {
        $query = History::whereIn('id', function ($query) use ($date) {
            $query->select(DB::raw('MAX(id)'))
                ->from('histories')
                ->where('historyable_type', self::class);

            if ($date) {
                $query->whereDate('created_at', '<=', $date);
            }

            $query->groupBy('historyable_id');
        });

        return $query->get()->map(function ($history) use ($withRelations, $date) {
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
        $model->exists = $history->action !== 'created';
        return $model;
    }
}
