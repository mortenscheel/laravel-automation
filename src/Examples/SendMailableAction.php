<?php

namespace Scheel\Automation\Examples;

class SendMailableAction extends \Scheel\Automation\AutomationAction
{
    protected function executeAction(): bool
    {
        $params = $this->log->automation->action_params;
        $mailable_class = $params->get('mailable');
        $recipient = $this->log->automatable;
        $mailable_params = [];
        foreach ($params->get('mailable_params', []) as $mailable_param) {
            $mailable_params[] = data_get($recipient, $mailable_param);
        }
        $mailable = new $mailable_class(...$mailable_params);
        \Mail::to($recipient)->send($mailable);

        return true;
    }
}
