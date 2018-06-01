<?php
/**
 * Container
 * Just a simple IoC Container
 */
namespace App\Core;

class Container {

   private static $dep = [];

   public static function bind($key, $value)
   {
      static::$dep[$key] = $value;
   }

   public static function resolve($key)
   {
      if (! array_key_exists($key, static::$dep)) {
         throw new \Exception ("App::get({$key}) failed because {$key} does not exist");
      }

      return static::$dep[$key];
   }
}
