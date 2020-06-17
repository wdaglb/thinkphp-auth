<?php


namespace ke\auth\tests;


use ke\auth\exception\AuthException;
use ke\auth\logic\Auth;
use think\Db;
use think\facade\Env;

class AuthTest extends TestCase
{
    public function testRun()
    {
        $this->login();

        $user = Auth::instance()->init();

        $has = $user->hasAuth(['All', 'we'], 'and');

        var_dump($has);

    }

}
