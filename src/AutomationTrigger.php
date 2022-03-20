<?php

namespace Scheel\Automation;

use Illuminate\Support\Collection;
use Scheel\Automation\Models\Automation;

abstract class AutomationTrigger
{
    public function __construct(public Collection $params)
    {
    }

    /**
     * @param \Scheel\Automation\Models\Automation $automation
     * @return Collection<\Scheel\Automation\Contracts\Automatable>
     */
    abstract public function discoverAutomatable(Automation $automation): Collection;
}
