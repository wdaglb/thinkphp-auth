<?php


namespace ke\auth\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;

class PublishCommand extends Command
{
    public function configure()
    {
        $this->setName('ke-auth:publish')
            ->setDescription('Publish KeAuth');
    }

    public function execute(Input $input, Output $output)
    {
        $destination = App::getRootPath() . '/db/migrations/';
        if(!is_dir($destination)){
            mkdir($destination, 0755, true);
        }
        $source = __DIR__.'/../../db/migrations/';
        $handle = dir($source);

        while($entry=$handle->read()) {
            if(($entry!=".")&&($entry!="..")){
                if(is_file($source.$entry)){
                    copy($source.$entry, $destination.$entry);
                }
            }
        }

        $output->writeln('publish success!');
    }

}