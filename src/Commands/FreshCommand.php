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

      Container::resolve('config')->set('homestead_path', $helper->ask($input, $output, new Question('Path to homestead (/user/JoeCianflone/Homestead): ', '/user/JoeCianflone/Homestead'))) ;
      Container::resolve('config')->set('homestead_yaml', $helper->ask($input, $output, new Question('YAML file name (Homestead.yaml): ', 'Homestead.yaml')));
      Container::resolve('config')->set('hosts_path', $helper->ask($input, $output, new Question('Path to Hosts file (/etc): ', '/etc')));
      Container::resolve('config')->set('hosts_file', $helper->ask($input, $output, new Question('Hostsfile name  (hosts): ', 'hosts')));
      Container::resolve('config')->set('vm_ip', $helper->ask($input, $output, new Question('Homesteads IP Address (192.168.10.10): ', '192.168.10.10')));
      Container::resolve('config')->set('vm_base_path', $helper->ask($input, $output, new Question('VM Base Path (/home/vagrant/sites): ', '/home/vagrant/sites')));
      Container::resolve('config')->set('local_base_path', $helper->ask($input, $output, new Question('Local folder base path (~/Sites): ', '~/Sites')));

      Container::resolve('config')->saveToDisk();
   }
}
