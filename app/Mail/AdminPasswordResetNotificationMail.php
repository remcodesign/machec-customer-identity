<?php

namespace App\Mail;

use Machec\Contracts\Mail\MachecMailable;

class AdminPasswordResetNotificationMail extends MachecMailable
{
    public function __construct(
        private readonly string $requestedEmail,
    ) {}

    protected function subjectLine(): string
    {
        return '[admin] Password reset requested';
    }

    protected function bodyView(): string
    {
        return 'mail.admin-password-reset-notification';
    }

    protected function bodyData(): array
    {
        return ['requestedEmail' => $this->requestedEmail];
    }
}
