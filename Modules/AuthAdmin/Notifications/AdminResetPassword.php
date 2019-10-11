<?php

namespace Modules\AuthAdmin\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as Notification;

class AdminResetPassword extends Notification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting(__('authadmin::auth.reset_password_email.greeting'))
            ->subject(__('authadmin::auth.reset_password_email.subject'))
            ->line(__('authadmin::auth.reset_password_email.first_info'))
            ->action(__('authadmin::auth.reset_password_email.reset_button'), config('authadmin.client_admin_url') . '/auth/password/reset/'
                . $this->token . '?email=' . urlencode($notifiable->email))
            ->line(__('authadmin::auth.reset_password_email.link_expire', ['count' => config('auth.passwords.admin.expire')]))
            ->line(__('authadmin::auth.reset_password_email.last_info'));
    }
}
