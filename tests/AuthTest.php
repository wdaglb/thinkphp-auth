<?php


namespace ke\auth\tests;


use ke\auth\model\Auth;
use think\Db;
use think\facade\Env;

class AuthTest extends TestCase
{
    public function testRun()
    {
        $this->login();

        $auth = Auth::instance();

        $auth->getUserInfo();
        $auth->getTokenInfo();
    }

}
