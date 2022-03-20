<?php

namespace Scheel\Automation\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Scheel\Automation\AutomationAction;
use Scheel\Automation\AutomationTrigger;
use Scheel\Automation\Contracts\Automatable;

/**
 * @property string $trigger_class
 * @property \Illuminate\Support\Collection $trigger_params
 * @property string $action_class
 * @property \Illuminate\Support\Collection $action_params
 * @property bool $active
 */
class Automation extends Model
{
    protected $fillable = ['trigger_class', 'trigger_params', 'action_class', 'action_params', 'active'];
    protected $casts = ['trigger_params' => 'collection', 'action_params' => 'collection', 'active' => 'bool'];

    public function logs()
    {
        return $this->hasMany(AutomationLog::class);
    }

    public function getTrigger(): AutomationTrigger
    {
        return new $this->trigger_class($this->trigger_params);
    }

    public function getAction(AutomationLog $log): AutomationAction
    {
        return new $this->action_class($log);
    }

    public function getLogForModel(Automatable $model): ?AutomationLog
    {
        return $model->automationLogs->firstWhere('automation_id', $this->id);
    }

    public function logActionDispatched(Automatable $model): AutomationLog
    {
        $log = new AutomationLog([
            'automation_id' => $this->id,
            'dispatched_at' => now(),
        ]);
        $model->automationLogs()->save($log);

        return $log;
    }

    public function scopeActive(Builder $builder)
    {
        $builder->where('active', true);
    }
}
