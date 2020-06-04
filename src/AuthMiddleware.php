<?php


namespace ke\auth;


use ke\auth\command\CreateCommand;
use ke\auth\model\Auth;
use think\Console;
use think\facade\Cookie;
use think\Request;

class AuthMiddleware
{
    public function handle(Request $request, callable $next)
    {
        $auth = Auth::instance();
        try {
            $auth->init();
        } catch (exception\ErrorException $e) {
            return response()->code(401);
        }
        return $next($request);
    }

}
