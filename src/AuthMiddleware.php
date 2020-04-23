<?php


namespace ke\auth;


use ke\auth\command\CreateCommand;
use think\Console;
use think\Request;

class AuthMiddleware
{
    public function handle(Request $request, callable $next)
    {
        return $next($request);
    }

}
