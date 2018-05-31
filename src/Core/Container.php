<?php
/**
 * Container
 * Just a simple IoC Container
 */
namespace App\Core;

class Container {

   private static $dep = [];

   public static function bind(string $key, string $value) : void
   {
      static::$dep[$key] = $value;
   }

   public static function bindFile(string $key, string $value) : void
   {
      static::$dep[$key] = require $value;
   }

   public static function get(string $key) : array
   {
      if (! array_key_exists($key, static::$dep)) {
         throw new \Exception ("App::get({$key}) failed because {$key} does not exist");
      }

      return static::$dep[$key];
   }
}
