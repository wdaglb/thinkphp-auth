<?php


namespace ke\auth\tests;

use ke\auth\model\Auth;
use PHPUnit\Framework\TestCase as BaseTestCase;
use think\App;
use think\Container;
use think\Db;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Env;
use think\Loader;

class TestCase extends BaseTestCase
{
    protected $app;

    protected $migrate = true;

    protected function createApplication()
    {
    }

    protected function login()
    {
        $this->createApplication();

        $auth = Auth::instance();

        $token = $auth->login('admin', '123456');

        $this->assertNotTrue($token === false, $auth->getError());

        $this->assertTrue($auth->init($token), $auth->getError());

    }
}
