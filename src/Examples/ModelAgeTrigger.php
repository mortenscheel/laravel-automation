<?php

namespace Scheel\Automation\Examples;

use Illuminate\Support\Collection;
use function now;
use Scheel\Automation\Models\Automation;

class ModelAgeTrigger extends \Scheel\Automation\AutomationTrigger
{
    public function discoverAutomatable(Automation $automation): Collection
    {
        $class = $this->params->get('model');

        return $class::query()->where('created_at', '<=', now()->subSeconds($this->params->get('age')))
            ->whereDoesntHave('automationLogs', fn ($logs) => $logs->where('automation_id', $automation->id))
            ->get();
    }
}
