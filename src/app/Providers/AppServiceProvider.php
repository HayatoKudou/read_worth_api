<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage())
                ->greeting('こんにちは')
                ->subject('メール認証')
                ->line('下のボタンをクリックして、メールアドレスを確認してください。')
                ->action('メール認証', $url)
                ->line('身に覚えがなければご放念ください。')
                ->salutation('Read Worth');
        });

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(config('app.url') . route('password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false));
            return (new MailMessage())
                ->greeting('こんにちは')
                ->subject('パスワード再設定のお知らせ')
                ->line('下のボタンをクリックして、パスワードを再設定してください。')
                ->action('パスワード再設定', $url)
                ->line('身に覚えがなければご放念ください。')
                ->salutation('Read Worth');
        });
    }
}
