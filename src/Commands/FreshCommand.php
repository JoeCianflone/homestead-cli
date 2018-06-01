<?php
namespace App\Commands;

use App\Core\Container;
use App\Commands\BaseCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputInterface, InputArgument, InputOption};


class FreshCommand extends BaseCommand {

   public function __construct()
   {
      parent::__construct();
   }

   protected function configure()
   {
      $this
         ->setName('fresh')
         ->setDescription('Generates the base config and setup for the CLI tool')
         ->setHelp('This command allows you to set up the CLI tool');

   }

   protected function execute(InputInterface $input, OutputInterface $output)
   {
      $helper = $this->getHelper('question');

      $defaults = $this->systemDefaults();

      $this->config->set('homestead_path', $helper->ask($input, $output, new Question('Path to homestead ('.$defaults['homestead_path'].'): ', $defaults['homestead_path']))) ;
      $this->config->set('homestead_yaml', $helper->ask($input, $output, new Question('YAML file name (Homestead.yaml): ', 'Homestead.yaml')));
      $this->config->set('hosts_path', $helper->ask($input, $output, new Question('Path to Hosts file ('.$defaults['hosts_path'].'): ', $defaults['hosts_path'])));
      $this->config->set('hosts_file', $helper->ask($input, $output, new Question('Hostsfile name  (hosts): ', 'hosts')));
      $this->config->set('vm_ip', $helper->ask($input, $output, new Question('Homesteads IP Address (192.168.10.10): ', '192.168.10.10')));
      $this->config->set('vm_base_path', $helper->ask($input, $output, new Question('VM Base Path (/home/vagrant/sites): ', '/home/vagrant/sites')));
      $this->config->set('local_base_path', $helper->ask($input, $output, new Question('Local folder base path ('.$defaults['local_base_path'].'): ', $defaults['local_base_path'])));

      $this->config->saveToDisk();
   }

   private function systemDefaults() : array
   {
      $defaults = [];
      $home = getenv('HOME');

      if (! empty($home)) {
         $defaults['homestead_path'] = rtrim($home, '/') . '/Homestead';
         $defaults['hosts_path'] = '/etc';
         $defaults['local_base_path'] = rtrim($home, '/') . '/Sites';
      } else if (! empty($_SERVER['HOMEDRIVE']) && ! empty($_SERVER['HOMEPATH'])) {
         $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
         $defaults['homestead_path'] = rtrim($home, '\\/') . '\Homestead';
         $defaults['hosts_path'] = 'C:\Windows\System32\drivers\etc';
         $defaults['local_base_path'] = rtrim($home, '\\/') . '\Sites';
      }

      return $defaults;
   }
}
