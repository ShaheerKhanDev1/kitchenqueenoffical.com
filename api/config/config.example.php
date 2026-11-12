<?php
/**
 * Kitchen Queen API configuration.
 * Live Production — kitchenqueenoffical.com
 *
 * IMPORTANT: GoDaddy/cPanel is showing UNPREFIXED names:
 *   Database: kitchenqueen_db
 *   User:     YOUR_DB_USER
 * Do NOT use  prefix on this account.
 */
declare(strict_types=1);

$db_host = 'localhost';
$db_port = 3306;
$db_name = 'kitchenqueen_db';
$db_user = 'YOUR_DB_USER';
$db_pass = 'CHANGE_ME';

$db = [
    'host'     => 'localhost',
    'hostname' => 'localhost',
    'port'     => 3306,
    'name'     => 'kitchenqueen_db',
    'database' => 'kitchenqueen_db',
    'dbname'   => 'kitchenqueen_db',
    'user'     => 'YOUR_DB_USER',
    'username' => 'YOUR_DB_USER',
    'pass'     => 'CHANGE_ME',
    'password' => 'CHANGE_ME',
    'charset'  => 'utf8mb4',
];

// If host ever moves back to prefixed cPanel names
$db_fallback = [
    'host'     => '127.0.0.1',
    'hostname' => '127.0.0.1',
    'port'     => $db_port,
    'name'     => 'kitchenqueen_db',
    'database' => 'kitchenqueen_db',
    'dbname'   => 'kitchenqueen_db',
    'user'     => 'YOUR_DB_USER',
    'username' => 'YOUR_DB_USER',
    'pass'     => $db_pass,
    'password' => $db_pass,
    'charset'  => 'utf8mb4',
];

return [
    'app_name' => 'Kitchen Queen Otp',
    'env'      => 'production',
    'timezone' => 'Asia/Karachi',

    'db'          => $db,
    'db_fallback' => $db_fallback,

    'host'     => '127.0.0.1',
    'hostname' => '127.0.0.1',
    'port'     => 3306,
    'name'     => 'kitchenqueen_db',
    'database' => 'kitchenqueen_db',
    'dbname'   => 'kitchenqueen_db',
    'user'     => 'YOUR_DB_USER',
    'username' => 'YOUR_DB_USER',
    'pass'     => 'CHANGE_ME',
    'password' => 'CHANGE_ME',
    'charset'  => 'utf8mb4',

    'app_secret' => 'CHANGE_ME',

    'session' => [
        'admin_hours'    => 24 * 7,
        'customer_hours' => 24 * 30,
    ],

    'cors' => [
        'https://kitchenqueenoffical.com',
        'https://www.kitchenqueenofficial.com',
        'https://kitchenqueenoffical.com',
        'https://www.kitchenqueenoffical.com',
        'https://dashboard.kitchenqueenoffical.com',
        'https://dashboard.kitchenqueenoffical.com',
        'http://localhost',
        'http://127.0.0.1',
        'http://localhost:5500',
        'http://127.0.0.1:5500',
    ],

    'upload' => [
        'dir'       => dirname(__DIR__) . '/storage/uploads',
        'max_bytes' => 5 * 1024 * 1024,
        'mimes'     => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    ],

    'otp' => [
        'ttl_minutes'  => 10,
        'max_attempts' => 5,
        'dev_otp'      => false,
        'demo_codes'   => [],
    ],

    'mail' => [
        'enabled'   => true,
        'host'      => 'smtp.gmail.com',
        'port'      => 587,
        'secure'    => 'tls',
        'user'      => 'you@example.com',
        'pass'      => 'CHANGE_ME',
        'from_name' => 'Kitchen Queen',
        'admin_to'  => 'you@example.com',
        'admin_user' => 'you@example.com',
        'admin_pass' => 'CHANGE_ME',
        'admin_from_name' => 'Kitchen Queen Orders',
        'admin_from' => 'orders@kitchenqueenofficial.com',
    ],

    'whatsapp' => [
        'enabled'              => false,
        'phone_number_id'      => 'YOUR_PHONE_NUMBER_ID',
        'access_token'         => 'CHANGE_ME',
        'graph_version'        => 'v21.0',
        'utility_template'     => 'order_payment_alert',
        'template_lang'        => 'en',
        'otp_template'         => 'order_payment_alert',
        'otp_template_lang'    => 'en',
        'otp_template_button'  => false,
        'admin_number'         => 'YOUR_ADMIN_NUMBER',
    ],

    'order' => [
        'prefix'     => 'KQ-',
        'start_from' => 1,
    ],
];
