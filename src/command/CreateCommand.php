<?php

namespace ke\auth\command;

use ke\auth\Auth;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

class CreateCommand extends Command
{
    public function configure()
    {
        $this->setName('policy:create')
            ->addArgument('name', Argument::REQUIRED, 'Set policy name')
            ->addArgument('text', Argument::REQUIRED, 'Set policy text')
            ->addOption('parent', Option::VALUE_IS_ARRAY)
        ;
    }

    public function execute(Input $input, Output $output)
    {
        $name = $input->getArgument('name');
        $db = Db::name(Auth::TABLE_ADMIN_POLICY);
        if ($db->where('name', $name)->value('id')) {
            $output->writeln($name . ' is exist!');
            return;
        }
        $pid = 0;
        if ($input->hasOption('parent')) {
            $pid = $db->where('name', $input->getOption('parent'))->value('id');
            if (empty($pid)) {
                $output->writeln($name . ' is not exist!');
                return;
            }
        }

        $data = $db->insert([
            'parent_id'=>$pid,
            'name'=>$name,
            'text'=>$input->getArgument('text'),
            'sort'=>$_SERVER['REQUEST_TIME'],
            'create_time'=>$_SERVER['REQUEST_TIME']
        ]);

        $output->writeln('policy:' . $data . '->' . $name);
    }
}
