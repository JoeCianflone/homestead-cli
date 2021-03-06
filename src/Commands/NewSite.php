<?php
namespace App\Commands;

use App\Core\Container;
use Symfony\Component\Yaml\Yaml;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use App\Transformers\{HostsTransformer, HomesteadTransformer};
use Symfony\Component\Console\Input\{InputInterface, InputArgument, InputOption};

class NewSite extends Base {

   public function __construct()
   {
      parent::__construct();
   }

   protected function initialize(InputInterface $input, OutputInterface $output)
   {
      $this->checkConfig()->checkHost();
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
         ->addOption('php', null, InputOption::VALUE_REQUIRED, "Version of PHP you want to use", "7.2")
         ->addOption('pubdir', null, InputOption::VALUE_REQUIRED, "Public folder for homestead");
   }

   protected function execute(InputInterface $input, OutputInterface $output)
   {
      $homestead = new HomesteadTransformer(Yaml::parseFile($this->config->get('homestead_path') .'/'. $this->config->get('homestead_yaml')));
      $updatedYamlFile = $homestead->transform($input);

      $hosts = new HostsTransformer($this->hosts->read($this->config->get('hosts_file')));
      $updatedHostsFile = $hosts->transform($input);

      $this->writeFilesToDisk(Yaml::dump($updatedYamlFile), $updatedHostsFile);

   }

   private function writeFilesToDisk($homestead, $hosts)
   {
      $this->homestead->put($this->config->get('homestead_yaml'), $homestead);
      $this->hosts->put($this->config->get('hosts_file'), $hosts);

      $this->indexer->saveToDisk();
   }
}
