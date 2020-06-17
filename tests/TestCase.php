<?php


namespace ke\auth\tests;

use ke\auth\logic\Auth;
use PHPUnit\Framework\TestCase as BaseTestCase;

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

        $this->assertTrue(true);

        $auth = Auth::instance();

        return $auth->login('admin', '123456');
    }
}
