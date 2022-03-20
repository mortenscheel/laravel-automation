<?php

namespace Scheel\Automation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use function now;
use function report;
use Scheel\Automation\Models\AutomationLog;
use Throwable;

abstract class AutomationAction implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected AutomationLog $log
    ) {
    }

    /**
     * @return bool
     */
    abstract protected function executeAction(): bool;

    final public function handle()
    {
        try {
            $success = $this->executeAction();
        } catch (Throwable $e) {
            report($e);
            $success = false;
        }
        if ($success) {
            $this->log->update(['executed_at' => now()]);
        } else {
            $this->log->update(['failed_at' => now()]);
        }
    }
}
