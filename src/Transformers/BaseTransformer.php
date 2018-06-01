<?php
namespace App\Transformers;

use App\Core\Container;

class BaseTransformer {

   protected $config;
   protected $map;
   protected $file;

   public function __construct() {
      $this->config = Container::resolve('config');
      $this->map = Container::resolve('map');
   }
}
