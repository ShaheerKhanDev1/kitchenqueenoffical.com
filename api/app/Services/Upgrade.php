<?php
namespace App\Services;
use App\Core\Database;
final class Upgrade {
 public static function run(): void {
  if (Settings::get('repair_version') === '2026-10-28') return;
  Database::query("CREATE TABLE IF NOT EXISTS mail_jobs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id INT UNSIGNED NOT NULL, kind VARCHAR(32) NOT NULL, status VARCHAR(16) NOT NULL DEFAULT 'pending', attempts INT NOT NULL DEFAULT 0, available_at DATETIME NOT NULL, locked_at DATETIME NULL, sent_at DATETIME NULL, UNIQUE KEY order_kind(order_id,kind), KEY pending(status,available_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  Database::query("CREATE TABLE IF NOT EXISTS checkout_requests (request_key CHAR(64) PRIMARY KEY, user_id INT UNSIGNED NOT NULL, order_id INT UNSIGNED NULL, created_at DATETIME NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");


        Database::query(
            "CREATE TABLE IF NOT EXISTS categories (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                category_name VARCHAR(80) NOT NULL,
                has_addons TINYINT NOT NULL DEFAULT 0,
                sort_order INT NOT NULL DEFAULT 0,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_category (category_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        Database::query(
            "INSERT IGNORE INTO categories (category_name, has_addons, sort_order) VALUES
            ('Salan / Curry', 1, 1),
            ('Daal & Sabzi', 1, 2),
            ('Breads / Naan', 0, 3),
            ('Rice / Biryani', 0, 4),
            ('Desserts', 0, 5),
            ('Drinks', 0, 6)"
        );

  Database::query("INSERT IGNORE INTO settings (`key`,value,updated_at) VALUES ('order_sequence','99',NOW())");
  Database::query("INSERT IGNORE INTO menu_items (id,name,slug,category,price,serves,description,image,tag,is_hot,is_available,spice_option,extras_option,sort_order,created_at,updated_at) VALUES ('custom','Custom Thali','custom-thali','Custom',0,1,'Build your thali','','',0,0,0,0,9999,NOW(),NOW())");
  $catalog = json_decode(file_get_contents(dirname(__DIR__,2).'/config/catalog.json'),true);
  foreach ($catalog as $i) {
   if (Database::one('SELECT id FROM menu_items WHERE id=:id', ['id'=>$i['id']])) continue;
   Database::insert('menu_items',['id'=>$i['id'],'name'=>$i['name'],'slug'=>$i['id'],'category'=>$i['cat'],'price'=>$i['price'],'was_price'=>$i['was']??null,'serves'=>$i['serves']??1,'description'=>$i['desc']??'','image'=>'images/'.$i['img'],'tag'=>$i['tag']??'','is_hot'=>!empty($i['hot'])?1:0,'is_available'=>1,'spice_option'=>1,'extras_option'=>1,'sort_order'=>100,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
  }
  Settings::set('repair_version' , '2026-10-28');
 }
}
