<?php

namespace MyVendor\MyApi\Module;

use BEAR\Package\PackageModule;
use Ray\Di\AbstractModule;
use Ray\AuraSqlModule\AuraSqlModule; // この行を追加

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new PackageModule);

        // この2行を追加
        $dbConfig = 'sqlite:' . dirname(dirname(__DIR__)). '/var/db/post.sqlite3';
        $this->install(new AuraSqlModule($dbConfig));
    }
}