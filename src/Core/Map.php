<?php namespace App\Core;

use App\Core\Container;

class Map {

   public static function get(string $key) : string
   {
      return Container::get('map')[$key];
   }

}
