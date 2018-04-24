<?php
namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class NewSiteCommand extends Command {

   private $config;

   public function __construct($config)
   {
      parent::__construct();

      $this->config = $config;
   }

   protected function configure()
   {
      $this
         ->setName('new')
         ->setDescription('Creates a new site in Homestead')
         ->setHelp('This command allows you to create a new site in Homestead')

         ->addArgument('folder', InputArgument::REQUIRED, "Folder where your code lives")
         ->addArgument('uri', InputArgument::REQUIRED, "URI for your local code")

         ->addOption('database', null, InputOption::VALUE_REQUIRED, "Add a DB to your YAML file too")
         ->addOption('pubdir', null, InputOption::VALUE_REQUIRED, "Public folder for homestead");
   }

   protected function execute(InputInterface $input, OutputInterface $output)
   {
      /**
       * find and open our homestead.yaml file
       * parse the yaml into something PHP can read
       * add folders, sites and database to the PHP
       *
       * open hosts file
       * add new entry
       *
       * save both Hosts and YAML
       */

      $yamlHomestead = Yaml::parseFile($this->config['homestead_path'] .'/'. $this->config['homestead_yaml']);
      $toLocation = $this->config['vm_base_path'] . '/' . $input->getArgument('folder');

      $yamlHomestead['folders'][] = [
         'map' => $input->getArgument('folder'),
         'to' => $toLocation
      ];

      if ($input->getOption('pubdir')) {
         $toLocation .= '/'.$input->getOption('pubdir');
      }

      $yamlHomestead['sites'][] = [
         'map' => $input->getArgument('uri'),
         'to' => $toLocation
      ];

      if ($input->getOption('database')) {
         $yamlHomestead['databases'][] = $input->getOption('database');
      }

      $fsHosts = new Filesystem(new Local($this->config['hosts_path']));
      $fsHomestead = new Filesystem(new Local($this->config['homestead_path']));

      $hosts = $fsHosts->read($this->config['hosts_file']);
      $hosts .= PHP_EOL;
      $hosts .= $this->config['vm_ip'] . ' ' . $input->getArgument('uri');

      $fsHomestead->put($this->config['homestead_yaml'], Yaml::dump($yamlHomestead));
      $fsHosts->put($this->config['hosts_file'], $hosts);
   }
}
