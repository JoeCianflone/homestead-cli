<?php
namespace App\Core;

use App\Core\Container;

class Config {

   public static function get(string $key) : string
   {
      return Container::get('config')[$key];
   }

}
