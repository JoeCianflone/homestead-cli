<?php

namespace App\Transformers;

use App\Transformers\BaseTransformer;
use Symfony\Component\Console\Input\InputInterface;

class HostsTransformer extends BaseTransformer {

   public function __construct($file)
   {
      parent::__construct();

      $this->file = $file;
   }

   public function transform(InputInterface $input)
   {
      $newLine = $this->config->get('vm_ip') . ' ' . $input->getArgument('uri');

      $pattern = preg_quote($newLine, '/');
      $pattern = "/^.*$pattern.*\$/m";

      if(preg_match($pattern, $this->file)){
         throw new \Exception("{$input->getArgument('uri')} exists in Hosts file...aborting");
      }

      $this->file .= PHP_EOL . $newLine;

      return $this->file;
   }

}
