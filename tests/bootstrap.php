<?php

use MyVendor\MyApi;
use BEAR\Package\Bootstrap;
use Doctrine\Common\Annotations\AnnotationRegistry;

error_reporting(E_ALL);

load: {
    $loader = require dirname(__DIR__) . '/vendor/autoload.php';
    AnnotationRegistry::registerLoader([$loader, 'loadClass']);
}

constants: {
    // set the application path into the globals so we can access it in the tests.
    $_ENV['APP_DIR'] = dirname(__DIR__);
    $_ENV['TEST_DIR'] = __DIR__;
    $_ENV['TMP_DIR'] = __DIR__ . '/tmp';
    // set the resource client
    $app = (new Bootstrap)->getApp('MyVendor\MyApi', 'app');
    $GLOBALS['RESOURCE'] = $app->resource;
}

database: {
    try {
        $pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER']);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS {$GLOBALS['DB_DBNAME']}; use {$GLOBALS['DB_DBNAME']}");
        $pdo->exec('SET SESSION query_cache_limit=4194304;');
        $pdo->exec("SET @@GLOBAL.sql_mode='NO_ENGINE_SUBSTITUTION'");
    } catch (PDOException $e) {
        echo "Database connection failed: user:{$GLOBALS['DB_USER']} passwd:{$GLOBALS['DB_PASSWD']}" . PHP_EOL;
        exit(1);
    }
    register_shutdown_function(function () use ($pdo) {
        $pdo->exec("DROP DATABASE {$GLOBALS['DB_DBNAME']};");
    });
    // crate table
    $sql = file_get_contents($_ENV['APP_DIR'] . '/var/db/scheme.sql');
    $pdo->exec($sql);
    // disconnect
    $pdo = null;
}
