<?php

namespace App\Transformers;

use App\Core\Container;
use Symfony\Component\Console\Input\InputInterface;

class HomesteadTransformer {

   private $file;

   public function __construct($file)
   {
      $this->file = $file;
   }

   public function transform(InputInterface $input)
   {
      $this->file['folders'] = $this->updateFolders($this->file['folders'], $input->getArgument('folder'));
      $this->file['sites'] = $this->updateSites($this->file['sites'], $input->getArgument('folder'), $input->getArgument('uri'), $input->getOption('pubdir'));
      $this->file['databases'] = $this->updateDatabase($this->file['databases'], $input->getOption('database'));

      return $this->file;
   }

   private function updateFolders($file, $folder)
   {
      $location = $this->getLocation($folder);

      if ($this->foundInColumn($folder, $file, 'map') || $this->foundInColumn($location, $file, 'to')) {
         throw new \Exception("Folder or VM Location already exists in Folders...aborting");
      }

      return $this->doMapping($file, $folder, $location);
   }

   private function updateSites($file, $folder, $uri, $pubdir='')
   {
      $location = $this->getLocation($folder, $pubdir);

      if ($this->foundInColumn($uri, $file, 'map') || $this->foundInColumn($location, $file, 'to')) {
         throw new \Exception("URI or Folder Name already exists in sites...aborting");
      }

      return $this->doMapping($file, $uri, $location);
   }

   private function updateDatabase($file, $dbName)
   {
      if (in_array($dbName, $file)) {
         throw new \Exception('Database Already Exists...Aborting');
      }

      if (! is_null($dbName)) {
         $file[] = $dbName;
      }

      return $file;
   }

   private function foundInColumn($needle, $haystack, $column)
   {
      return in_array($needle, array_column($haystack, $column));
   }

   private function doMapping($file, $map, $to)
   {
      $file[] = [
         'map' => $map,
         'to' => $to
      ];

      return $file;
   }

   private function getLocation($folder, $pubdir='')
   {
      return trim(Container::get('config')['vm_base_path'] .'/'.$folder.'/'.$pubdir,'/');
   }
}
