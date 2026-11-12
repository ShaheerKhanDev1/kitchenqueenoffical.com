<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('KQ_BOOTSTRAP_ONLY', true);
require dirname(__DIR__).'/public/index.php';
App\Services\MailQueue::drain(20);
