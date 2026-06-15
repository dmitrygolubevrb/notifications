<?php

return [
    'sms_driver' => env('NOTIFICATION_SMS_DRIVER', 'mock'),
    'email_driver' => env('NOTIFICATION_EMAIL_DRIVER', 'mock'),
    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],
    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY'),
        'from' => env('SENDGRID_FROM_EMAIL'),
        'from_name' => env('SENDGRID_FROM_NAME', env('APP_NAME', 'Notification Service')),
        'subject' => env('SENDGRID_DEFAULT_SUBJECT', 'Notification'),
        'sandbox' => env('SENDGRID_SANDBOX_MODE', true),
    ],
];
