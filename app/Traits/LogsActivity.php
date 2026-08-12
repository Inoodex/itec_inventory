<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            ActivityLog::log(
                description: class_basename($model) . " #{$model->getKey()} was created",
                subject: $model,
                properties: ['attributes' => $model->getAttributes()],
                event: 'created'
            );
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            ActivityLog::log(
                description: class_basename($model) . " #{$model->getKey()} was updated",
                subject: $model,
                properties: [
                    'old' => array_intersect_key($model->getOriginal(), $changes),
                    'attributes' => $changes,
                ],
                event: 'updated'
            );
        });

        static::deleted(function ($model) {
            ActivityLog::log(
                description: class_basename($model) . " #{$model->getKey()} was deleted",
                subject: $model,
                properties: ['attributes' => $model->getAttributes()],
                event: 'deleted'
            );
        });
    }
}
