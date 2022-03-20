<?php

namespace Scheel\Automation\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/** @mixin \Illuminate\Database\Eloquent\Model */
interface Automatable
{
    public function automationLogs(): MorphMany;
}
