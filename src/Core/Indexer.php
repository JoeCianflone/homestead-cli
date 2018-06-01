<?php
namespace App\Core;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class Indexer {

   private $map = [];
   private $fs;
   private $fullPath;
   private $file;

   public function __construct($path, $file)
   {
      $this->fs = new Filesystem(new Local($path));
      $this->file = $file;
      $this->fullPath = $path . '/' . $file;

      if ($this->fs->has($this->file)) {
         $this->map = unserialize($this->fs->read($this->file));
      }
   }

   public function exists($key) : bool
   {
      foreach ($this->map as $item) {
         if (array_key_exists($key, $item)) {
            return true;
         }
      }

      return false;
   }

   public function set($arr)
   {
      $this->map[] = $arr;
   }

   public function saveToDisk() : void
   {
      $this->fs->put($this->file, serialize($this->map));

      // The NewSiteCommand runs under sudo, we change this back to
      // the normal user because this file doesn't need elevated
      // permissions for any reason.
      chown($this->fullPath, get_current_user());
   }
}
