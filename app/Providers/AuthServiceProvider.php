<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('[Taurus] Verify Email Address')
                ->greeting(new HtmlString("Hi {$notifiable->name}, <br> Thanks for getting started with our Taurus Application!"))
                ->line(new HtmlString("We need a little more information to complete your registration, including a confirmation of your email address.<br><br> Click below to confirm your email address:"))
                ->salutation(new HtmlString("Kind Regards,<br>Taurus teams"))
                ->action('Confirm Email Address', $url);
        });
    }
}
