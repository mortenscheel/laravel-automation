<?php

namespace Scheel\Automation;

use Scheel\Automation\Models\Automation;

class LaravelAutomation
{
    public function dispatchAutomationActions(\Closure $callback = null)
    {
        $this->queryAutomationModel()->active()->get()->shuffle()->each(function (Automation $automation) use ($callback) {
            $trigger = $automation->getTrigger();
            foreach ($trigger->discoverAutomatable($automation) as $model) {
                $log = $automation->logActionDispatched($model);
                $action = $automation->getAction($log);
                dispatch($action);
                if ($callback) {
                    $callback($action, $model);
                }
            }
        });
    }

    public function queryAutomationModel()
    {
        /** @var Automation $model */
        $model = app(config()->get('automation.automation_model'));

        return $model->newQuery();
    }
}
