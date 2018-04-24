<?php

namespace App\Core;

class Container {

   private static $dep = [];

   public static function bind($key, $value)
   {
      static::$dep[$key] = $value;
   }

   public static function bindFile($key, $value)
   {
      self::bind($key, require $value);
   }

   public static function get($key)
   {
      if (! array_key_exists($key, static::$dep)) {
         throw new \Exception ("App::get({$key}) failed because {$key} does not exist");
      }

      return static::$dep[$key];
   }
}
