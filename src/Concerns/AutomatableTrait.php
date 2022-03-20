<?php

namespace Scheel\Automation\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Scheel\Automation\Models\AutomationLog;

/** @mixin \Illuminate\Database\Eloquent\Model */
trait AutomatableTrait
{
    public function automationLogs(): MorphMany
    {
        return $this->morphMany(AutomationLog::class, 'automatable');
    }
}
