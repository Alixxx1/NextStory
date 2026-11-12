<?php

return [
    'enforce' => env('LICENSE_ENFORCE', true),
    // يتفعل على سيرفر المالك فقط: لوحة المالك + API التراخيص + السحابة والموبايل
    'hq_enabled' => env('HQ_ENABLED', false),
    // سر التوقيع والتشفير (AES-256 + HMAC): غيّره في .env على سيرفر المالك وعلى كل نسخة بنفس القيمة
    'secret' => env('LICENSE_SECRET', 'ns-license-secret-change-me-2026'),
    'portal' => env('HQ_PORTAL_PATH', 'ns-hq-portal'),
    // المفتاح العام (base64 لـ PEM) للتحقق من توقيع المالك؛ الخاص HQ_PRIVATE_KEY عند المالك فقط
    'public_key' => env('LICENSE_PUBLIC_KEY', 'LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0KTUlJQklqQU5CZ2txaGtpRzl3MEJBUUVGQUFPQ0FROEFNSUlCQ2dLQ0FRRUEwUmtqa0Fma2Q3ZTY3QWlLS0dLbAo2d2psMHhmZmlBaWpoQUtwZ2xsejJ5K01neGhuQURTK1c1S2tER1l0dFlNWHRnK0JqRGROME5kUENlTlZadUZnClowVktPWTRKVExYcllZRlVUNFJhQU96cSsxSDRmWEU0bWJRNlJTMnhnVE5iYWpWY1JRcW5kV0FPRzBTTlZDdm0KbVVnY21GY1ZHblEvaFZvMG9Zb2JESzExd1BHNUttOVQrVDJtMDdMSEtQQTZKaENyajY3TDFYaGJGQjg0TnA4Swo2SFdyQ2Y5WG5MS3FHTlI4MW81WmFveFJEWWhseGMxTTdqdmNKTkFhd21uU2dtWnlmN3RtcWM2Yno2dHZ4blhCClV2di9IemJxdHpXOUkxSW4vekVTQ0FVVXlBMytscWdTOTg4SWRZK0lpa1h2cERrYWhZMjZpYlA1UzhvcnpoNUgKS1FJREFRQUIKLS0tLS1FTkQgUFVCTElDIEtFWS0tLS0tCg=='),
    'private_key' => env('HQ_PRIVATE_KEY'),
    'server' => env('LICENSE_SERVER_URL', ''),
    'heartbeat_hours' => 0.25,
    'admin_email' => env('HQ_ADMIN_EMAIL', 'admin@nextstore.local'),
    'admin_username' => env('HQ_ADMIN_USERNAME', 'admin'),
    'admin_password' => env('HQ_ADMIN_PASSWORD', 'NextStore#2026'),
];
