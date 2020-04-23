<?php


namespace ke\auth\command;


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
        $list = Db::name('admin_policy')->select();
        foreach ($list as $item) {
            $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
            $output->writeln(implode(' | ', $item));
        }
    }
}