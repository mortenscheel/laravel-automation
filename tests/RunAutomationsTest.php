<?php

namespace Scheel\Automation\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Scheel\Automation\Examples\ModelAgeTrigger;
use Scheel\Automation\Models\Automation;

class RunAutomationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_run_automations_command()
    {
        $first = AutomatableTestModel::create([
            'created_at' => now()->subSeconds(30),
        ]);
        $second = AutomatableTestModel::create([
            'created_at' => now()->subSeconds(60),
        ]);
        Automation::create([
            'trigger_class' => ModelAgeTrigger::class,
            'trigger_params' => [
                'model' => AutomatableTestModel::class,
                'age' => 60,
            ],
            'action_class' => UpdatesTriggeredStatusAction::class,
            'action_params' => [],
        ]);
        \Artisan::call('automation:run');
        $this->assertDatabaseHas('automatable_test_models', ['id' => $first->id, 'triggered' => false]);
        $this->assertDatabaseHas('automatable_test_models', ['id' => $second->id, 'triggered' => true]);
    }
}
