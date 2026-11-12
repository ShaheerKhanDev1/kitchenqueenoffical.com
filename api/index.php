<?php
ini_set('display_errors','0');
set_exception_handler(static function($e){error_log('KQ API: '.$e->getMessage());http_response_code(500);header('Content-Type: application/json');echo json_encode(['ok'=>false,'message'=>'Server error. Please retry.']);});
require __DIR__.'/public/index.php';
