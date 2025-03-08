<?php

$env = parse_ini_file(__DIR__ . '/../.env', true);

if (!$env) {
    die("Error: Unable to load environment file.");
}

define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USERNAME', $env['DB_USERNAME']);
define('DB_PASSWORD', $env['DB_PASSWORD']);
define('PAYSTACK_CALLBACK', $env['PAYSTACK_CALLBACK']);
define('PAYSTACK_SECRET', $env['PAYSTACK_SECRET']);
define('PAYSTACK_PUBLIC', $env['PAYSTACK_PUBLIC']);
define('CONTENT_TYPE', $env['CONTENT_TYPE']);
define('COUPLE_PAY',(int) $env['COUPLE_PAY']);
define('SINGLE_PAY', (int) $env['SINGLE_PAY']);
define('EMAIL_HOST', $env['EMAIL_HOST']);
define('EMAIL_USERNAME', $env['EMAIL_USERNAME']);
define('EMAIL_PASSWORD', $env['EMAIL_PASSWORD']);
define('EMAIL_PORT', $env['EMAIL_PORT']);
define('PAYSTACK_CURLOPT_URL', $env['PAYSTACK_CURLOPT_URL']);
define('CHARGE', (int) $env['CHARGE']);
define('PERCENTAGE', (float) $env['PERCENTAGE']);
define('EMAIL_NAME', $env['EMAIL_NAME']);
define('PAYSTACK_URL', $env['PAYSTACK_URL']);






?>