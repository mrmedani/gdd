<?php

namespace App\Domains\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', ['token' => $this->token, 'email' => $notifiable->email]);

        $locale = $notifiable->locale ?? 'fr';

        $subject = match ($locale) {
            'ar' => 'إعادة تعيين كلمة المرور',
            'en' => 'Reset Password',
            default => 'Réinitialisation du mot de passe',
        };

        $greeting = match ($locale) {
            'ar' => 'مرحباً!',
            'en' => 'Hello!',
            default => 'Bonjour !',
        };

        $line1 = match ($locale) {
            'ar' => 'لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بك.',
            'en' => 'We received a request to reset your password.',
            default => 'Nous avons reçu une demande de réinitialisation de votre mot de passe.',
        };

        $actionText = match ($locale) {
            'ar' => 'إعادة تعيين كلمة المرور',
            'en' => 'Reset Password',
            default => 'Réinitialiser le mot de passe',
        };

        $line2 = match ($locale) {
            'ar' => 'إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة.',
            'en' => 'If you did not request a password reset, you can ignore this message.',
            default => 'Si vous n\'avez pas demandé de réinitialisation, vous pouvez ignorer ce message.',
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line1)
            ->action($actionText, $url)
            ->line($line2);
    }
}
