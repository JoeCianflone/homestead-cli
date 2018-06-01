<?php
namespace App\Commands;

use App\Core\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputInterface, InputArgument, InputOption};

class BaseCommand extends Command {

   protected $homestead;
   protected $hosts;
   protected $indexer;
   protected $config;

   public function __construct()
   {
      parent::__construct();

      $this->config = Container::resolve('config');
      $this->indexer = Container::resolve('indexer');

      if (! $this->config->isEmpty()) {
         $this->homestead = new Filesystem(new Local($this->config->get('homestead_path')));
         $this->hosts = new Filesystem(new Local($this->config->get('hosts_path')));
      }
   }

   public function checkHost() : Object
   {
      if (! is_writable($this->config->get('hosts_path').'/'.$this->config->get('hosts_file'))) {
         throw new \Exception("Cannot write to Host file, probably forgot sudo");
      }

      return $this;
   }

   public function checkConfig() : Object
   {
      if ($this->config->isEmpty()) {
           throw new \Exception("No config found, please run vm fresh");
      }

      return $this;
   }
}
