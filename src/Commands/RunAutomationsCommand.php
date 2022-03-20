<?php

namespace Scheel\Automation\Commands;

use Illuminate\Console\Command;
use Scheel\Automation\Facades\LaravelAutomation;

class RunAutomationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automation:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run configured automations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $progress = $this->output->createProgressBar();
        LaravelAutomation::dispatchAutomationActions(fn () => $progress->advance());
        $progress->clear();

        return self::SUCCESS;
    }
}
