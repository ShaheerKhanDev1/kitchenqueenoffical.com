-- Kitchen Queen structure only. No production records.
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('owner','staff') NOT NULL DEFAULT 'staff',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `admin_login_attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `ident` varchar(190) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `success` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `admin_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(30) NOT NULL,
  `title` varchar(160) NOT NULL,
  `body` varchar(500) DEFAULT NULL,
  `order_id` int(10) UNSIGNED DEFAULT NULL,
  `dedupe_key` varchar(80) DEFAULT NULL,
  `is_read` tinyint(4) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `admin_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(128) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `areas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `delivery_fee` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL,
  `min_order` int(11) NOT NULL DEFAULT 0,
  `free_over` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_name` varchar(80) NOT NULL,
  `has_addons` tinyint(4) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `checkout_requests` (
  `request_key` char(64) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `closed_dates` (
  `id` int(10) UNSIGNED NOT NULL,
  `closed_date` date NOT NULL,
  `reason` varchar(160) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `coupon_redemptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `coupon_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `discount` int(11) NOT NULL DEFAULT 0,
  `status` varchar(12) NOT NULL DEFAULT 'used',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customer_notes` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `note` text DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `customer_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(30) NOT NULL,
  `title` varchar(160) NOT NULL,
  `body` varchar(500) DEFAULT NULL,
  `is_read` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `daily_availability` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_item_id` varchar(40) NOT NULL,
  `avail_date` date NOT NULL,
  `is_offered` tinyint(4) NOT NULL DEFAULT 1,
  `portions` int(11) DEFAULT NULL,
  `reserved` int(11) NOT NULL DEFAULT 0,
  `is_sold_out` tinyint(4) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `delivery_slots` (
  `id` int(10) UNSIGNED NOT NULL,
  `label` varchar(40) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 20,
  `days` varchar(7) NOT NULL DEFAULT '1234567',
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `expenses` (
  `id` int(10) UNSIGNED NOT NULL,
  `expense_date` date NOT NULL,
  `category` varchar(30) NOT NULL,
  `amount` int(11) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `mail_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `kind` varchar(32) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'pending',
  `attempts` int(11) NOT NULL DEFAULT 0,
  `available_at` datetime NOT NULL,
  `locked_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `menu_items` (
  `id` varchar(20) NOT NULL,
  `name` varchar(160) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `category` varchar(40) NOT NULL,
  `price` int(11) NOT NULL,
  `was_price` int(11) DEFAULT NULL,
  `serves` tinyint(4) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `tag` varchar(40) DEFAULT NULL,
  `is_hot` tinyint(1) NOT NULL DEFAULT 0,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `spice_option` tinyint(1) NOT NULL DEFAULT 0,
  `extras_option` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `ingredients` text DEFAULT NULL,
  `allergens` varchar(255) DEFAULT NULL,
  `spice_levels` varchar(160) DEFAULT NULL,
  `prep_notice_hours` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `menu_item_sizes` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_item_id` varchar(40) NOT NULL,
  `label` varchar(40) NOT NULL,
  `price` int(11) NOT NULL,
  `serves` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `channel` enum('whatsapp','dashboard_popup','sound') NOT NULL,
  `event` enum('new_order','payment_pending','payment_approved') NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `status` enum('queued','sent','failed','seen') NOT NULL DEFAULT 'queued',
  `seen_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_no` varchar(20) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(80) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(120) DEFAULT NULL,
  `order_type` enum('delivery','pickup') NOT NULL,
  `area_id` bigint(20) UNSIGNED DEFAULT NULL,
  `area_name` varchar(120) DEFAULT NULL,
  `address_line` varchar(255) NOT NULL DEFAULT '',
  `address_note` varchar(255) DEFAULT NULL,
  `status` enum('awaiting_payment','confirmed','accepted','preparing','ready','out_for_delivery','completed','cancelled','payment_rejected','pending','delivered') NOT NULL DEFAULT 'pending',
  `payment_method` enum('cod','easypaisa') NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'unpaid',
  `screenshot_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subtotal` int(11) NOT NULL,
  `discount` int(11) NOT NULL DEFAULT 0,
  `delivery_fee` int(11) NOT NULL DEFAULT 0,
  `tax` int(11) NOT NULL DEFAULT 0,
  `total` int(11) NOT NULL,
  `promo_code` varchar(30) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `store_was_online` tinyint(1) NOT NULL DEFAULT 1,
  `placed_at` datetime NOT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `order_status` varchar(20) DEFAULT NULL,
  `tracking_token` char(40) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `slot_id` int(10) UNSIGNED DEFAULT NULL,
  `slot_label` varchar(60) DEFAULT NULL,
  `slot_start` datetime DEFAULT NULL,
  `is_advance` tinyint(4) NOT NULL DEFAULT 0,
  `landmark` varchar(160) DEFAULT NULL,
  `coupon_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_ref` varchar(80) DEFAULT NULL,
  `claimed_amount` int(11) DEFAULT NULL,
  `proof_submitted_at` datetime DEFAULT NULL,
  `payment_reviewed_at` datetime DEFAULT NULL,
  `payment_reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `payment_reject_reason` varchar(255) DEFAULT NULL,
  `payment_collected_at` datetime DEFAULT NULL,
  `cancel_reason` varchar(255) DEFAULT NULL,
  `cancelled_by` varchar(20) DEFAULT NULL,
  `cancel_requested_at` datetime DEFAULT NULL,
  `cancel_request_reason` varchar(255) DEFAULT NULL,
  `refund_amount` int(11) DEFAULT NULL,
  `refund_note` varchar(255) DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `rider_id` int(10) UNSIGNED DEFAULT NULL,
  `rider_name` varchar(80) DEFAULT NULL,
  `rider_phone` varchar(20) DEFAULT NULL,
  `dispatched_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `estimated_delivery` varchar(60) DEFAULT NULL,
  `failed_reason` varchar(255) DEFAULT NULL,
  `retry_note` varchar(255) DEFAULT NULL,
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `reservation_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` varchar(20) NOT NULL,
  `name` varchar(160) NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `spice` varchar(30) DEFAULT NULL,
  `extras` varchar(120) DEFAULT NULL,
  `line_total` int(11) NOT NULL,
  `size_label` varchar(40) DEFAULT NULL,
  `size_id` int(10) UNSIGNED DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `admin_name` varchar(80) DEFAULT NULL,
  `note` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_reservations` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `kind` varchar(10) NOT NULL,
  `ref_id` varchar(40) NOT NULL,
  `res_date` date NOT NULL,
  `qty` int(11) NOT NULL,
  `status` varchar(12) NOT NULL DEFAULT 'held',
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `released_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `order_status_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `from_status` varchar(30) DEFAULT NULL,
  `to_status` varchar(30) NOT NULL,
  `changed_by` enum('system','admin','customer') NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `kind` varchar(12) NOT NULL DEFAULT 'order',
  `admin_name` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `otp_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(120) NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `purpose` enum('login','admin_alert_test') NOT NULL DEFAULT 'login',
  `expires_at` datetime NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `payment_proofs` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `upload_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_ref` varchar(80) DEFAULT NULL,
  `claimed_amount` int(11) DEFAULT NULL,
  `payer_number` varchar(30) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'under_review',
  `reject_reason` varchar(255) DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `promo_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(30) NOT NULL,
  `discount_type` enum('percent','flat') NOT NULL,
  `discount_value` int(11) NOT NULL,
  `min_subtotal` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `expires_at` datetime DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `max_discount` int(11) DEFAULT NULL,
  `starts_at` datetime DEFAULT NULL,
  `per_customer_limit` int(11) DEFAULT NULL,
  `applies_to` varchar(12) NOT NULL DEFAULT 'all',
  `applies_ids` text DEFAULT NULL,
  `description` varchar(160) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(80) NOT NULL,
  `stars` tinyint(4) NOT NULL,
  `text` text NOT NULL,
  `reply` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `dish_name` varchar(160) DEFAULT NULL,
  `moderation` varchar(16) NOT NULL DEFAULT 'published',
  `moderation_note` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `riders` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `settings` (
  `key` varchar(80) NOT NULL,
  `value` text NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `slot_usage` (
  `id` int(10) UNSIGNED NOT NULL,
  `slot_id` int(10) UNSIGNED NOT NULL,
  `slot_date` date NOT NULL,
  `reserved` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `uploads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kind` enum('easypaisa_slip') NOT NULL DEFAULT 'easypaisa_slip',
  `file_path` varchar(255) NOT NULL,
  `mime` varchar(80) NOT NULL,
  `size_bytes` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `phone_e164` varchar(20) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `last_login_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `visits` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(45) NOT NULL,
  `visit_date` date NOT NULL,
  `visited_at` datetime NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `path` varchar(180) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admins_email` (`email`);

ALTER TABLE `admin_login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ident` (`ident`,`created_at`),
  ADD KEY `idx_ip` (`ip`,`created_at`);

ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_dedupe` (`dedupe_key`),
  ADD KEY `idx_unread` (`is_read`,`id`);

ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admin_sessions_token` (`token`),
  ADD KEY `idx_admin_sessions_admin` (`admin_id`);

ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_areas_name` (`name`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_category` (`category_name`);

ALTER TABLE `checkout_requests`
  ADD PRIMARY KEY (`request_key`);

ALTER TABLE `closed_dates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_date` (`closed_date`);

ALTER TABLE `coupon_redemptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_order` (`order_id`),
  ADD KEY `idx_coupon` (`coupon_id`,`status`),
  ADD KEY `idx_user` (`user_id`);

ALTER TABLE `customer_notes`
  ADD PRIMARY KEY (`user_id`);

ALTER TABLE `customer_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`,`is_read`),
  ADD KEY `idx_order` (`order_id`);

ALTER TABLE `daily_availability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_item_date` (`menu_item_id`,`avail_date`),
  ADD KEY `idx_date` (`avail_date`);

ALTER TABLE `delivery_slots`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`expense_date`);

ALTER TABLE `mail_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_kind` (`order_id`,`kind`),
  ADD KEY `pending` (`status`,`available_at`);

ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_menu_cat` (`category`),
  ADD KEY `idx_menu_avail` (`is_available`);

ALTER TABLE `menu_item_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_item` (`menu_item_id`);

ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_status_ch` (`status`,`channel`),
  ADD KEY `idx_notif_order` (`order_id`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_orders_no` (`order_no`),
  ADD UNIQUE KEY `uniq_tracking_token` (`tracking_token`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_placed` (`placed_at`),
  ADD KEY `idx_orders_pay` (`payment_method`,`payment_status`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `fk_orders_area` (`area_id`),
  ADD KEY `idx_order_status` (`order_status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_delivery_date` (`delivery_date`),
  ADD KEY `idx_transaction_ref` (`transaction_ref`);

ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_oi_order` (`order_id`);

ALTER TABLE `order_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`);

ALTER TABLE `order_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_status` (`status`,`expires_at`);

ALTER TABLE `order_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_osl_order` (`order_id`),
  ADD KEY `fk_osl_admin` (`admin_id`);

ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_otp_phone` (`phone`);

ALTER TABLE `payment_proofs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_ref` (`transaction_ref`),
  ADD KEY `idx_status` (`status`);

ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_promo_code` (`code`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rev_order` (`order_id`),
  ADD KEY `fk_rev_user` (`user_id`);

ALTER TABLE `riders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

ALTER TABLE `slot_usage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_slot_date` (`slot_id`,`slot_date`);

ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_up_order` (`order_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_phone` (`phone`);

ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_ip_date` (`ip`,`visit_date`),
  ADD KEY `idx_visit_date` (`visit_date`);

ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `admin_login_attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `admin_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `admin_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `areas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `closed_dates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `coupon_redemptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `daily_availability`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `delivery_slots`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `expenses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `mail_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `menu_item_sizes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `order_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `order_reservations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `order_status_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `otp_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `payment_proofs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `promo_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `riders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `slot_usage`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `uploads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `visits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `admin_sessions`
  ADD CONSTRAINT `fk_admin_sessions_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

ALTER TABLE `order_status_logs`
  ADD CONSTRAINT `fk_osl_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_osl_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_rev_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rev_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `uploads`
  ADD CONSTRAINT `fk_up_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS=1;
