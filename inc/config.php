<?php

error_reporting(E_ALL);

session_start();

define("URL", "http://localhost/Praktik%20Matkul%20PBWL%20Sem%205/project6/website-ECommerce/");
define("AST", URL . "/layouts/assets");

define("ROOT", dirname(__DIR__) . DIRECTORY_SEPARATOR);

define("UPLOAD_DIR", ROOT . "uploads" . DIRECTORY_SEPARATOR . "produk" . DIRECTORY_SEPARATOR);
define("UPLOAD_URL", URL . "/uploads/produk");

define("DB_HOST", "localhost");
define("DB_NAME", "dbproject6");
define("DB_USER", "root");
define("DB_PASS", "");

require_once ROOT. "vendor/autoload.php";

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
