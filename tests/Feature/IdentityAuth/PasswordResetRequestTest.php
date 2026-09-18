<?php

use App\Mail\AdminPasswordResetNotificationMail;
use App\Mail\CustomerPasswordResetAcknowledgementMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('a reset request always emails the configured admin address', function (): void {
    config(['services.notify_customer_on_password_request' => false]);
    Mail::fake();
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/password/reset-request', ['email' => $user->email]);

    $response->assertStatus(202);
    Mail::assertSent(AdminPasswordResetNotificationMail::class, fn ($mail) => $mail->hasTo(config('mail.admin_address')));
    Mail::assertNotSent(CustomerPasswordResetAcknowledgementMail::class);
});

test('a reset request also emails the customer when NOTIFY_CUSTOMER_ON_PASSWORD_REQUEST is true', function (): void {
    config(['services.notify_customer_on_password_request' => true]);
    Mail::fake();
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/password/reset-request', ['email' => $user->email]);

    $response->assertStatus(202);
    Mail::assertSent(AdminPasswordResetNotificationMail::class);
    Mail::assertSent(CustomerPasswordResetAcknowledgementMail::class, fn ($mail) => $mail->hasTo($user->email));
});

test('a reset request for an email not in users still returns 202 and sends only the admin email', function (): void {
    config(['services.notify_customer_on_password_request' => false]);
    Mail::fake();

    $response = $this->postJson('/api/v1/password/reset-request', ['email' => 'nobody@example.com']);

    $response->assertStatus(202);
    Mail::assertSent(AdminPasswordResetNotificationMail::class);
    Mail::assertNotSent(CustomerPasswordResetAcknowledgementMail::class);
});
