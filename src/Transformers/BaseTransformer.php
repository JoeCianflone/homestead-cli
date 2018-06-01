<?php
namespace App\Transformers;

use App\Core\Container;

class BaseTransformer {

   protected $file;
   protected $config;
   protected $indexer;

   public function __construct() {
      $this->config = Container::resolve('config');
      $this->indexer = Container::resolve('indexer');
   }
}
