# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Developer Options

Fitur ini membantu developer saat pengembangan, debugging, dan testing aplikasi.

### Cara Mengaktifkan Mode Developer

- Edit file `.env` di root project:
  ```
  CI_ENVIRONMENT = development
  ```
  Untuk mode production:
  ```
  CI_ENVIRONMENT = production
  ```

### Konfigurasi Database

- Edit bagian database di `.env` (hapus tanda `#` di depan baris):
  ```
  database.default.hostname = 
  database.default.database = 
  database.default.username = 
  database.default.password = 
  database.default.DBDriver = MySQLi
  database.default.port = 3306
  ```

### Fitur Developer di Dashboard

- **Monitoring Compliance Last Modified User**  
  Melihat perubahan terakhir oleh user di dashboard monitoring.

- **Async Export Job**  
  Submit dan monitoring export report secara async di halaman Report.

- **Debug Log**  
  Jika mode developer aktif, error detail dan log akan muncul di halaman.

### Endpoint Testing

- **API Report Helpdesk**  
  - `/report/ticket-detail`
  - `/report/sla-detail`
  - `/report/sla-response-comparison`
  - `/report/sla-resolution-comparison`

- **Admin Tools**  
  - `/admin/report_user` untuk simulasi export report.
  - `/admin/delete_report_job/{id}` untuk hapus job export.

### Tips Debugging

- Gunakan browser Developer Tools (F12) untuk inspect JS dan network.
- Cek log error di folder `writable/logs/`.
- Pastikan variable global seperti `BASE_URL` sudah tersedia di view untuk JS eksternal.

### Build & Dependency

- Untuk toggle antara release dan development dependency CodeIgniter, gunakan:
  ```
  php builds release
  php builds development
  ```
  lalu jalankan `composer update`.

---

**Catatan:**  
- Jangan upload file `.env` ke repo.
- Semua data sensitif harus diganti jika pernah ter-push ke GitHub.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
"# hc-helpdesk" 
