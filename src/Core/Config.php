<?php
namespace App\Core;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class Config {

   private $cfg = [];
   private $fs;
   private $file;

   public function __construct($path, $file)
   {
      $this->fs = new Filesystem(new Local($path));
      $this->file = $file;

      if ($this->fs->has($this->file)) {
         $this->cfg = unserialize($this->fs->read($this->file));
      }

   }

   public function get(string $key) : string
   {
      if (! array_key_exists ($key, $this->cfg)) {
         throw new \Exception("Config file not found, run vm fresh first");
      }

      return $this->cfg[$key];
   }

   public function set(string $key, string $value) : void
   {
      $this->cfg[$key] = $value;
   }

   public function saveToDisk() : void
   {
      $this->fs->put($this->file, serialize($this->cfg));
   }

   public function isEmpty() : bool
   {
      return count($this->cfg) <= 0;
   }

   public function dump()
   {
      return $this->cfg;
   }
}
