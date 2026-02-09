<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::createAuditTrail($model, 'INSERT');
        });

        static::updated(function ($model) {
            self::createAuditTrail($model, 'UPDATE');
        });

        static::deleted(function ($model) {
            self::createAuditTrail($model, 'DELETE');
        });
    }

    private static function createAuditTrail($model, $action)
    {
        $userId = Auth::id() ?? null;
        
        $auditTrail = new AuditTrail();
        $auditTrail->table_name = $model->getTable();
        $auditTrail->record_id = $model->getKey();
        $auditTrail->action = $action;
        
        if ($action === 'INSERT') {
            $auditTrail->new_values = $model->getAttributes();
        } elseif ($action === 'UPDATE') {
            $auditTrail->old_values = $model->getOriginal();
            $auditTrail->new_values = $model->getChanges();
        } elseif ($action === 'DELETE') {
            $auditTrail->old_values = $model->getOriginal();
        }
        
        $auditTrail->user_id = $userId;
        $auditTrail->ip_address = Request::ip();
        $auditTrail->user_agent = Request::userAgent();
        $auditTrail->save();
    }
}