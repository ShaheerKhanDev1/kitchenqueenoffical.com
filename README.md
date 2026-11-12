# Kitchen Queen Website

PHP food ordering website with menu browsing, OTP login, checkout, order tracking and customer reviews.

## Requirements

- PHP 8.0 or newer with PDO MySQL, mbstring, OpenSSL and cURL.
- MySQL/MariaDB and an Apache-compatible host with rewrite support and .htaccess enabled.
- An SMTP account or configured Brevo account for email.

## Private configuration

Copy each example to its matching local filename on the server, then enter your own settings:

| Example | Private file |
| --- | --- |
| api/config/config.example.php | api/config/config.local.php |
| mail_config.example.php | mail_config.local.php |
| api/app/Services/brevo_config.example.php | api/app/Services/brevo_config.local.php |

All *.local.php files are ignored by Git. Never upload private settings to GitHub. The tracked loader files require these local files. Replace every CHANGE_ME and YOUR_DB_USER value. Use a new random app_secret (for example, generate it with `php -r "echo bin2hex(random_bytes(32));"`). Keep repeated database values consistent, and configure both customer and admin email settings. Brevo is disabled in the example; leave it disabled when using SMTP.

## Database

Create an empty database and import database/schema.sql once. Both repositories include the same 33-table structure for convenience; they share one database, so do not import it twice. The schema contains no production records, passwords, sessions, OTPs, payment proofs or customer data. Menus, delivery areas and other business settings must be configured for a fresh installation. The original production dump remains separate and must never be committed.

## Deployment

Deploy this project to the main website domain with index.php in the document root. The browser calls /api on the same domain. The matching dashboard repository is https://github.com/ShaheerKhanDev1/dashboard.kitchenqueenoffical.com.

Keep existing .htaccess files and ensure api/storage/uploads is writable by PHP. The supplied PHPMailer files are retained. On a fresh database, create the first admin through the dashboard auth/register.php page while setup is private, then configure menu items, areas, payment details and store settings. Configure email before testing OTP login. For an existing live site, retain its database and private settings; do not replace production data with the empty schema.

## Verification

Prepared from the supplied ZIP backup. Private literals, mail logs and uploaded payment files were removed from this distribution. All 169 PHP files across both projects passed syntax checks with PHP 8.2.12, and all six private-configuration loaders passed missing-file and example-file checks; end-to-end ordering and email delivery still require a configured PHP/MySQL environment. This preparation is not a complete application security audit.
