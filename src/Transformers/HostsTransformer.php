<?php

namespace App\Transformers;

use App\Core\Container;
use Symfony\Component\Console\Input\InputInterface;

class HostsTransformer {

   private $file;

   public function __construct($file)
   {
      $this->file = $file;
   }

   public function transform(InputInterface $input)
   {
      $newLine = Container::get('config')['vm_ip'] . ' ' . $input->getArgument('uri');

      $pattern = preg_quote($newLine, '/');
      $pattern = "/^.*$pattern.*\$/m";

      if(preg_match($pattern, $this->file)){
         throw new \Exception("{$newLine} exists in Hosts file...aborting");
      }

      $this->file .= PHP_EOL . $newLine;

      return $this->file;
   }

}
