<?php
namespace App\Commands;

use Symfony\Component\Yaml\Yaml;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use App\Transformers\{HostsTransformer, HomesteadTransformer};
use Symfony\Component\Console\Input\{InputInterface, InputArgument, InputOption};

class RebuildIndexCommand extends BaseCommand {

   public function __construct()
   {
      parent::__construct();
   }

   protected function configure()
   {
      $this
         ->setName('rebuild')
         ->setDescription('Rebuids the internal map needed for other features')
         ->setHelp('');

   }

   protected function execute(InputInterface $input, OutputInterface $output)
   {
      $homestead = Yaml::parseFile($this->config->get('homestead_path') .'/'. $this->config->get('homestead_yaml'));
      $newMap = [];

      foreach ($homestead['folders'] as $folder) {
         foreach ($homestead['sites'] as $site) {
            if (strpos($site['to'], $folder['to']) === 0) {
               $newMap[] = [
                  "{$site['map']}" => [
                     "uri" => $site['map'],
                     "local" => $folder['map'],
                     "vm" => rtrim($folder['to'], '/'),
                     "vmPublic" => rtrim($site['to'], '/'),
                     "php" => isset($site['php']) ? $site['php'] : '7.2'
                  ]
               ];
            }
         }
      }

      $this->indexer->set($newMap);
      $this->indexer->saveToDisk();
   }
}
