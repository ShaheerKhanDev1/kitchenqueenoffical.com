<?php
declare(strict_types=1);
// Real settings belong in the ignored local file on your server.
$privateConfig = __DIR__ . '/brevo_config.local.php';
if (!is_file($privateConfig)) {
    throw new RuntimeException('Missing private configuration. Copy brevo_config.example.php to brevo_config.local.php and configure it.');
}
return require $privateConfig;
