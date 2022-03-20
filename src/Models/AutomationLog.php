<?php

namespace Scheel\Automation\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationLog extends Model
{
    protected $fillable = [
        'automation_id',
        'automatable_type',
        'automatable_id',
        'dispatched_at',
        'executed_at',
        'failed_at',
    ];
    protected $casts = ['dispatched_at' => 'datetime', 'executed_at' => 'datetime', 'failed_at' => 'datetime'];

    public function automation()
    {
        return $this->belongsTo(Automation::class);
    }

    public function automatable()
    {
        return $this->morphTo();
    }
}
