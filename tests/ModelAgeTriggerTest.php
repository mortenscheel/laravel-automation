<?php

namespace Scheel\Automation\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use function now;
use Scheel\Automation\Examples\LogMessageAction;
use Scheel\Automation\Examples\ModelAgeTrigger;
use Scheel\Automation\Models\Automation;
use Scheel\Automation\Models\AutomationLog;

class ModelAgeTriggerTest extends TestCase
{
    use RefreshDatabase;

    private Automation $automation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->automation = Automation::create([
            'trigger_class' => ModelAgeTrigger::class,
            'trigger_params' => [
                'model' => AutomatableTestModel::class,
                'age' => 60 * 60 * 24,
            ],
            'action_class' => LogMessageAction::class,
            'action_params' => [
                'message' => 'Hello :name',
                'placeholders' => [
                    ':name' => 'name',
                ],
            ],
        ]);
    }

    public function test_it_doesnt_trigger_prematurely()
    {
        AutomatableTestModel::create();
        $this->travelTo(now()->addSeconds(60 * 60 * 12), function () {
            $this->assertCount(0, $this->automation->getTrigger()->discoverAutomatable($this->automation));
        });
    }

    public function test_it_triggers_on_age_limit()
    {
        AutomatableTestModel::create();
        $this->travelTo(now()->addSeconds(60 * 60 * 24), function () {
            $this->assertCount(1, $this->automation->getTrigger()->discoverAutomatable($this->automation));
        });
    }

    public function test_it_doesnt_retrigger_completed_automations()
    {
        $model = AutomatableTestModel::create();
        $model->automationLogs()->save(new AutomationLog([
            'automation_id' => $this->automation->id,
        ]));
        $this->travelTo(now()->addSeconds(60 * 60 * 24), function () {
            $this->assertCount(0, $this->automation->getTrigger()->discoverAutomatable($this->automation));
        });
    }
}
