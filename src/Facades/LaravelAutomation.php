<?php

namespace Scheel\Automation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Scheel\Automation\LaravelAutomation
 */
class LaravelAutomation extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-automation';
    }
}
