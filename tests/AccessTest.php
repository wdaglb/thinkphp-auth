<?php


namespace ke\auth\tests;


use ke\auth\model\Auth;

class AccessTest extends TestCase
{
    public function testRole()
    {
        $this->login();

        $auth = Auth::instance();

        $list = $auth->access->getRule();
        // var_dump($list);

        $this->assertTrue($auth->access->hasRule('All'));

        $this->assertNotTrue($auth->access->hasRule('All2'));

        $this->assertNotTrue($auth->access->hasRule(['All2']));

        $this->assertNotTrue($auth->access->hasRule(['All2', 'All']));

        $this->assertTrue($auth->access->hasRule(['All2', 'All'], 'or'));
    }

}