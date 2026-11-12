<?php
namespace App\Services;
use App\Core\Database;
final class MailQueue {
 public static function enqueue(int $id, string $kind): void {
  Database::query('INSERT IGNORE INTO mail_jobs (order_id,kind,available_at) VALUES (:id,:kind,NOW())',['id'=>$id,'kind'=>$kind]);
 }
 public static function drain(int $limit=10, ?int $orderId=null): void {
  // A database lease makes the CLI retry worker and immediate request dispatch safe together.
  $params=[]; $filter='';
  if($orderId!==null){$filter=' AND order_id=:oid';$params['oid']=$orderId;}
  $jobs=Database::all("SELECT * FROM mail_jobs WHERE attempts<6 AND ((status='pending' AND available_at<=NOW()) OR (status='sending' AND locked_at<DATE_SUB(NOW(),INTERVAL 5 MINUTE)))".$filter.' ORDER BY id LIMIT '.max(1,min(50,$limit)), $params);
  foreach($jobs as $job){
   $claimed=Database::query("UPDATE mail_jobs SET status='sending',locked_at=NOW(),attempts=attempts+1 WHERE id=:id AND (status='pending' OR (status='sending' AND locked_at<DATE_SUB(NOW(),INTERVAL 5 MINUTE)))",['id'=>$job['id']])->rowCount();
   if(!$claimed) continue;
   $ok=false;
   try {
    $o=Database::one('SELECT * FROM orders WHERE id=:id',['id'=>$job['order_id']]);
    $items=Database::all('SELECT * FROM order_items WHERE order_id=:id',['id'=>$job['order_id']]);
    if($o) $ok=$job['kind']==='admin_order' ? Mailer::sendNewOrder($o,$items) : Mailer::sendCustomerOrder($o,$items,$job['kind']);
   } catch(\Throwable $e) { error_log('KQ mail job failed id='.(int)$job['id']); }
   Database::query("UPDATE mail_jobs SET status=:s, sent_at=:sent, available_at=DATE_ADD(NOW(),INTERVAL 1 MINUTE),locked_at=NULL WHERE id=:id",['s'=>$ok?'sent':'pending','sent'=>$ok?date('Y-m-d H:i:s'):null,'id'=>$job['id']]);
  }
 }
}
