<?php

namespace App\Transformers;

use App\Core\Container;
use App\Transformers\BaseTransformer;
use Symfony\Component\Console\Input\InputInterface;

class HomesteadTransformer extends BaseTransformer {

   private $file;

   public function __construct(string $file)
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

   private function updateFolders(string $file, string $folder)
   {
      $map = $this->getLocalPath($folder);
      $to = $this->getVMPath($folder);

      if ($this->foundInColumn($map, $file, 'map') || $this->foundInColumn($to, $file, 'to')) {
         throw new \Exception("Folder or VM Location already exists in Folders...aborting");
      }

      return $this->doMapping($file, $map, $to);
   }

   private function updateSites($file, $folder, $uri, $pubdir='')
   {
      $to = $this->getVMPath($folder, $pubdir);

      if ($this->foundInColumn($uri, $file, 'map') || $this->foundInColumn($to, $file, 'to')) {
         throw new \Exception("URI or Folder Name already exists in sites...aborting");
      }

      return $this->doMapping($file, $uri, $to);
   }

   private function updateDatabase(string $file, string $dbName)
   {
      if (in_array($dbName, $file)) {
         throw new \Exception('Database Already Exists...Aborting');
      }

      if (! is_null($dbName)) {
         $file[] = $dbName;
      }

      return $file;
   }

   private function foundInColumn(string $needle, string $haystack, string $column)
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

   private function getLocalPath(string $folder) : string
   {
      return trim(Container::get('config')['local_base_path'] . '/'. $folder, '/');
   }

   private function getVMPath(string $folder, string $pubdir='') : string
   {
      return rtrim(Container::get('config')['vm_base_path'] .'/'.$folder.'/'.$pubdir, '/');
   }
}
