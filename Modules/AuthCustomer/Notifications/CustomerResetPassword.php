<?php

namespace Modules\AuthCustomer\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as Notification;

class CustomerResetPassword extends Notification
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
            ->greeting(__('authcustomer::auth.reset_password_email.greeting'))
            ->subject(__('authcustomer::auth.reset_password_email.subject'))
            ->line(__('authcustomer::auth.reset_password_email.first_info'))
            ->action(__('authcustomer::auth.reset_password_email.reset_button'), config('authcustomer.client_customer_url') . '/auth/password/reset/'
                . $this->token . '?email=' . urlencode($notifiable->email))
            ->line(__('authcustomer::auth.reset_password_email.link_expire', ['count' => config('auth.passwords.customer.expire')]))
            ->line(__('authcustomer::auth.reset_password_email.last_info'));
    }
}
