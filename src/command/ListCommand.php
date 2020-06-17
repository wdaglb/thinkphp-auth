<?php


namespace ke\auth\command;


use ke\auth\model\KePolicy;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class ListCommand extends Command
{
    public function configure()
    {
        $this->setName('policy:list')
            ->setDescription('Query policy list');
    }

    public function execute(Input $input, Output $output)
    {
        $output->writeln('id | parent_id | name | text | sort | time');
        $list = KePolicy::select();
        foreach ($list as $item) {
            $output->writeln(implode(' | ', $item->toArray()));
        }
    }
}