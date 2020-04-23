<?php


namespace ke\auth\tests;


use ke\auth\model\Auth;

class RoleTest extends TestCase
{
    public function testCreate()
    {
        $this->login();
        $auth = Auth::instance();

        $role = $auth->role->create('测试角色', '角色备注');
        $role->addPermission(1);
        $role->save();
    }

}