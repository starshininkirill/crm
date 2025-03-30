<?php

namespace App\Models\Traits;

use App\Models\History;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;

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

    public function history(): MorphMany
    {
        return $this->morphMany(History::class, 'historyable')->latest();
    }

    public function getHistoricalVersion($historyId)
    {
        $history = $this->history()->find($historyId);
        return $history ? $this->recreateFromHistory($history) : null;
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

        $model = $this->recreateFromHistory($history);

        foreach ($withRelations as $relation) {
            if ($this->isValidRelation($relation)) {
                $this->loadHistoricalRelation($model, $relation, $date);
            }
        }

        return $model;
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

    protected function recreateFromHistory(History $history)
    {
        $model = new static;
        $model->forceFill($history->new_values);
        $model->exists = $history->action !== 'created';
        return $model;
    }
}
