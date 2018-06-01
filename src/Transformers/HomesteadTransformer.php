<?php

namespace App\Transformers;

use App\Transformers\BaseTransformer;
use Symfony\Component\Console\Input\InputInterface;

class HomesteadTransformer extends BaseTransformer {

   public function __construct($file)
   {
      parent::__construct();
      $this->file = $file;
   }

   public function transform(InputInterface $input)
   {
      $map = $this->generateNewMapping($input);
      $items = $map[$input->getArgument('uri')];

      $this->indexer->set($map);

      $this->file['folders'][] = [
         "map" => $items['local'],
         "to" => $items['vm']
      ];

      $this->file['sites'][] = [
         "map" => $items['uri'],
         "to" => $items['vmPublic'],
         "php" => $items['php']
      ];

      $this->file['databases'] = $this->addDatabase($this->file['databases'], $input->getOption('database'));

      return $this->file;
   }


   private function addDatabase($file, $db)
   {
      if (is_null($db)) {
         return $file;
      }

      if (in_array($db, $file)) {
         throw new \Exception('Database Already Exists...Aborting');
      }

      $file[] = $db;
      return $file;
   }

   private function generateNewMapping(InputInterface $input)
   {
      if ($this->indexer->exists($input->getArgument('uri'))) {
         throw new \Exception("URI exists aborting");
      }

      return [
         "{$input->getArgument('uri')}" => [
            "uri" => $input->getArgument('uri'),
            "local" => $this->config->get('local_base_path')."/{$input->getArgument('folder')}",
            "vm" => rtrim($this->config->get('vm_base_path')."/{$input->getArgument('folder')}", '/'),
            "vmPublic" => rtrim($this->config->get('vm_base_path')."/{$input->getArgument('folder')}/{$input->getOption('pubdir')}", '/'),
            "php" => $input->getOption('php'),
         ]
      ];
   }
}
