# Laravel Automation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mortenscheel/laravel-automation.svg?style=flat-square)](https://packagist.org/packages/mortenscheel/laravel-automation)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/mortenscheel/laravel-automation/run-tests?label=tests)](https://github.com/mortenscheel/laravel-automation/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/mortenscheel/laravel-automation/Check%20&%20fix%20styling?label=code%20style)](https://github.com/scheel/laravel-automation/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mortenscheel/laravel-automation.svg?style=flat-square)](https://packagist.org/packages/mortenscheel/laravel-automation)

Allow users to configure dynamic automation flows in your Laravel app. Inspired by [If this then that](https://ifttt.com/), you define a number of triggers and actions, and then allow your users to combine and configure them.

## Quick start

Install the package with composer:

```bash
composer require mortenscheel/laravel-automation
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="automation-migrations"
php artisan migrate
```

Run automations automatically via Laravel's scheduler in `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // ...
    $schedule->command('automation:run');
}
```

## Concepts

### `AutomationTrigger` classes
Custom classes that are responsible for discovering models that meet their criteria.

### `AutomationAction` classes
Custom Job classes that perform an action on a model.

### `Automation` model
A concrete automation workflow that combines an `AutomationTrigger` with an `AutomationAction`. 
Also includes (optional) parameters for both the trigger and the action.

### `AutomationLog` model
A record that is created when an `Automation` is performed on a specific model.

### `Automatable` interface
In order to make your models automatable, they must implement the `Automatable` interface.

## Examples
### Send a welcome email 15 minutes after a new user is created
The `Automation` model might look something like this
```php
Automation::create([
    'trigger_class' => ModelAgeTrigger::class,
    'trigger_params' => [
        'model' => User::class,
        'age' => 60 * 15,
    ],
    'action_class' => SendMailableAction::class,
    'action_params' => [
        'mailable' => WelcomeEmail::class,
        'mailable_params' => [
            'name',
        ],
    ],
]);
```
The trigger class only need to implement a single method:
```php
class ModelAgeTrigger extends \Scheel\Automation\AutomationTrigger
{
    public function discoverAutomatable(Automation $automation): Collection
    {
        $class = $this->params->get('model');
        return $class::query()->where('created_at', '<=', now()->subSeconds($this->params->get('age')))
            ->whereDoesntHave('automationLogs', fn ($logs) => $logs->where('automation_id', $automation->id))
            ->get();
    }
}
```
The action class is also rather simple:
```php
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
```

## Todo
This package is a work in progress, and feedback or pull requests will be appreciated.
These are some of the areas that I would like to improve:
- Add more example triggers and actions (with tests).
- Allow using a custom `Automation` child class (wip).
- Add more documentation.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Morten Scheel](https://github.com/mortenscheel)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
