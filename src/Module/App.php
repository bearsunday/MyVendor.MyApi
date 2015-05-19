<?php

namespace MyVendor\MyApi\Module;

use BEAR\QueryRepository\HttpCacheInject; // この行を追加
use BEAR\Sunday\Extension\Application\AbstractApp;
use Ray\Di\Di\Inject;

class App extends AbstractApp
{
    use HttpCacheInject; // この行を追加
}
