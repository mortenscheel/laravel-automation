<?php

namespace Scheel\Automation\Tests;

use function dispatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Scheel\Automation\Examples\ModelAgeTrigger;
use Scheel\Automation\Examples\SendMailableAction;
use Scheel\Automation\Models\Automation;

class SendMailableActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_an_email()
    {
        $automation = Automation::create([
            'trigger_class' => ModelAgeTrigger::class,
            'trigger_params' => [
                'model' => AutomatableUser::class,
                'age' => 60 * 60 * 24,
            ],
            'action_class' => SendMailableAction::class,
            'action_params' => [
                'mailable' => WelcomeEmail::class,
                'mailable_params' => [
                    'name',
                ],
            ],
        ]);
        \Mail::fake();
        $user = AutomatableUser::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => \Hash::make('password'),
        ]);
        $log = $automation->logActionDispatched($user);
        $action = $automation->getAction($log);
        dispatch($action);
        \Mail::assertSent(WelcomeEmail::class, function (WelcomeEmail $mail) use ($user) {
            return $mail->name === $user->name;
        });
    }
}
