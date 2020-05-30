<?php

if (class_exists('\\think\\Console')) {
    \think\Console::addDefaultCommands([
        \ke\auth\command\CreateCommand::class,
        \ke\auth\command\ListCommand::class,
        \ke\auth\command\PublishCommand::class,
    ]);
}
