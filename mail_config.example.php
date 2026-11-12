<?php
/**
 * Kitchen Queen - Gmail SMTP settings
 * Customer OTP mail + Admin order inbox.
 */
return [
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
    'domain_from' => 'noreply@kitchenqueenoffical.com',
];
