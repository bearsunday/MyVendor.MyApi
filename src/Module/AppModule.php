<?php

namespace MyVendor\MyApi\Module;

use BEAR\Package\PackageModule;
use Ray\AuraSqlModule\AuraSqlQueryModule;
use Ray\Di\AbstractModule;
use Ray\AuraSqlModule\AuraSqlModule; // この行を追加

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new PackageModule);

        // この2行を追加
        $this->install(new AuraSqlModule('mysql:host=127.0.0.1;dbname=api_db', 'root'));
        $this->install(new AuraSqlQueryModule('mysql'));
    }
}
