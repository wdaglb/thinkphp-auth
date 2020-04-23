<?php

require_once __DIR__ . '/vendor/topthink/framework/base.php';

// 应用初始化
\think\Container::get('app')->path(__DIR__ . '/src')->initialize();
