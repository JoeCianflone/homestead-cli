<?php

use App\Core\Config;
use App\Core\Container;

Container::bind('config', new Config(__DIR__ . '/../', 'cfg.php'));

// App\Core\Container::bindFile('map', 'src/map.php');
