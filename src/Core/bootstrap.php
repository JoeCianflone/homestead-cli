<?php

use App\Core\{Config, Indexer, Container};

Container::bind('config', new Config(__DIR__ . '/../', '.env.config'));
Container::bind('indexer', new Indexer(__DIR__ . '/../', '.env.index'));
