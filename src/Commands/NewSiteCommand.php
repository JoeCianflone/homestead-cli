<?php
namespace App\Commands;

use App\Core\Container;
use Symfony\Component\Yaml\Yaml;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputInterface, InputArgument, InputOption};

class NewSiteCommand extends Command {

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
      $yamlHomestead = Yaml::parseFile(Container::get('config')['homestead_path'] .'/'. Container::get('config')['homestead_yaml']);
      $toLocation = Container::get('config')['vm_base_path'] . '/' . $input->getArgument('folder');

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

      $fsHosts = new Filesystem(new Local(Container::get('config')['hosts_path']));
      $fsHomestead = new Filesystem(new Local(Container::get('config')['homestead_path']));

      $hosts = $fsHosts->read(Container::get('config')['hosts_file']);
      $hosts .= PHP_EOL;
      $hosts .= Container::get('config')['vm_ip'] . ' ' . $input->getArgument('uri');

      $fsHomestead->put(Container::get('config')['homestead_yaml'], Yaml::dump($yamlHomestead));
      $fsHosts->put(Container::get('config')['hosts_file'], $hosts);
   }
}
