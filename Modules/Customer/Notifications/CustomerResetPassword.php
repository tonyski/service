<?php

namespace Modules\Customer\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as Notification;

class CustomerResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct($token)
    {
        $this->token = $token;

        $this->onConnection('redis');
        $this->onQueue('emails');
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting(__('customer::auth.reset_password_email.greeting'))
            ->subject(__('customer::auth.reset_password_email.subject'))
            ->line(__('customer::auth.reset_password_email.first_info'))
            ->action(__('customer::auth.reset_password_email.reset_button'), config('customer.client_customer_url') . '/auth/password/reset/'
                . $this->token . '?email=' . urlencode($notifiable->email))
            ->line(__('customer::auth.reset_password_email.link_expire', ['count' => config('auth.passwords.customer.expire')]))
            ->line(__('customer::auth.reset_password_email.last_info'));
    }
}
