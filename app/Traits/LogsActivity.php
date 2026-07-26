<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log an activity.
     */
    protected function logActivity(string $action, $subject = null, array $properties = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Boot the trait - register model events.
     */
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            if (Auth::check()) {
                $model->logActivity('created', $model);
            }
        });

        static::updated(function ($model) {
            if (Auth::check()) {
                $model->logActivity('updated', $model, [
                    'old' => $model->getOriginal(),
                    'new' => $model->getAttributes(),
                ]);
            }
        });

        static::deleted(function ($model) {
            if (Auth::check()) {
                $model->logActivity('deleted', $model);
            }
        });
    }
}
