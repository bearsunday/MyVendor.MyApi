<?php

namespace MyVendor\MyApi\Module;

use BEAR\Package\PackageModule;
use Koriym\Now\Now;
use Koriym\Now\NowInterface;
use Koriym\Now\NowProvider;
use Koriym\QueryLocator\QueryLocatorModule;
use Ray\AuraSqlModule\AuraSqlQueryModule;
use Ray\Di\AbstractModule;
use Ray\AuraSqlModule\AuraSqlModule; // この行を追加

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new PackageModule);
        $this->install(new AuraSqlModule('mysql:host=127.0.0.1;dbname=api_db', 'root'));
        $this->install(new AuraSqlQueryModule('mysql'));
        $this->install(new QueryLocatorModule(dirname(dirname(__DIR__)) . '/var/db/sql'));
        $this->bind(NowInterface::class)->to(Now::class);
        $this->bind(\DateTimeInterface::class)->toProvider(NowProvider::class);
    }
}
