<?php
namespace App\Transformers;

use App\Core\Container;

class BaseTransformer {

   public $config;
   public $map;

   public function __construct() {
      $this->config = Container::get('config');
      $this->map = Container::get('map');

   }
}
