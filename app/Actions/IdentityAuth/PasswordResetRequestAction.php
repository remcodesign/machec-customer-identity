<?php

namespace App\Actions\IdentityAuth;

use App\Data\Requests\PasswordResetRequestData;
use App\Mail\AdminPasswordResetNotificationMail;
use App\Mail\CustomerPasswordResetAcknowledgementMail;
use Illuminate\Support\Facades\Mail;

class PasswordResetRequestAction
{
    /**
     * Always notify the fixed admin address for manual handling; optionally
     * also acknowledge the requesting customer (D79 — no token/link yet,
     * there is no self-service reset broker).
     */
    public function handle(PasswordResetRequestData $data): void
    {
        Mail::to(config('mail.admin_address'))
            ->send(new AdminPasswordResetNotificationMail($data->email));

        if (config('services.notify_customer_on_password_request')) {
            Mail::to($data->email)
                ->send(new CustomerPasswordResetAcknowledgementMail);
        }
    }
}
