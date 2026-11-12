<?php
namespace App\Services;
use App\Core\Database;
use App\Core\Response;
final class Pricing {
 public const EXTRAS = ['Extra Raita'=>50,'Extra Naan'=>80,'Extra Salad'=>80,'Disposable Cutlery'=>20];
 public const THALI = ['chicken biryani'=>350,'sweet Dahi Phulki'=>280,'Tandoori Naan (4 pcs)'=>200,'Plain white rice'=>250,'Chicken Karahi'=>850,'Beef Haleem'=>480,'Aloo Qeema'=>450,'Aloo Gosht'=>450,'Shami Kabab (6 pcs)'=>500,'Mint Raita'=>90,'Green Salad'=>70,'Daal Chawal'=>350,'Desi Kheer'=>180,'Cold Drink'=>150,'Kheer + Drink'=>330,'Chicken Qourma'=>450];
 public static function thali(array $extras): int {
  if (count($extras)<2 || count($extras)>4) Response::error('Choose 2–4 thali items',422);
  $sum=0; $seen=[];
  foreach($extras as $extra) {
   $label=(string)($extra['label'] ?? '');
   if (!isset(self::THALI[$label]) || isset($seen[$label])) Response::error('Invalid thali selection',422);
   $seen[$label]=true; $sum+=self::THALI[$label];
  }
  return $sum;
 }
 public static function extras(mixed $extras): int {
  if (!is_array($extras) || count($extras)>12) Response::error('Invalid add-ons',422);
  $sum=0; $seen=[];
  foreach($extras as $extra) {
   $label=trim((string)(is_array($extra)?($extra['label']??''):$extra));
   if (isset($seen[$label])) Response::error('Duplicate add-on',422);
   $seen[$label]=true;
   if (isset(self::EXTRAS[$label])) { $sum+=self::EXTRAS[$label]; continue; }
   if (preg_match('/^(Daal|Salan): .{1,70}$/u',$label)) continue;
   $item=Database::one("SELECT price FROM menu_items WHERE name=:n AND is_available=1 AND (category IN ('sides','Sides & Raita','Breads / Naan') OR name LIKE '%naan%' OR name LIKE '%roti%')",['n'=>$label]);
   if (!$item) Response::error('Add-on unavailable. Please rebuild this cart item.',422);
   $sum+=(int)$item['price'];
  }
  return $sum;
 }
}
