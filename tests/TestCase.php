<?php

namespace Scheel\Automation\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Mail\Mailable;
use Orchestra\Testbench\TestCase as Orchestra;
use Scheel\Automation\AutomationAction;
use Scheel\Automation\Concerns\AutomatableTrait;
use Scheel\Automation\Contracts\Automatable;
use Scheel\Automation\LaravelAutomationServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelAutomationServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function getEnvironmentSetUp($app)
    {
        \Schema::create('automatable_test_models', function (Blueprint $table) {
            $table->id();
            $table->boolean('triggered')->default(false);
            $table->timestamps();
        });
    }
}

class AutomatableTestModel extends Model implements Automatable
{
    use AutomatableTrait;
    protected $guarded = [];
    protected $casts = ['triggered' => 'bool'];
}
class AutomatableUser extends \Illuminate\Foundation\Auth\User implements Automatable
{
    use AutomatableTrait;
    protected $guarded = [];
    protected $table = 'users';

    public function getFooAttribute()
    {
        return 'yo';
    }
}
class UpdatesTriggeredStatusAction extends AutomationAction
{
    protected function executeAction(): bool
    {
        $this->log->automatable->update(['triggered' => true]);

        return true;
    }
}
class WelcomeEmail extends Mailable
{
    public function __construct(public string $name)
    {
    }

    public function build()
    {
        return $this->html("Hello $this->name");
    }
}
