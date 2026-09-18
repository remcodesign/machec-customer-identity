<?php

namespace App\Mail;

use Machec\Contracts\Mail\MachecMailable;

class CustomerPasswordResetAcknowledgementMail extends MachecMailable
{
    protected function subjectLine(): string
    {
        return '[customer] We received your password reset request';
    }

    protected function bodyView(): string
    {
        return 'mail.customer-password-reset-acknowledgement';
    }
}
