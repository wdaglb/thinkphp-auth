<?php


namespace ke\auth\logic;

use ke\auth\exception\AuthException;
use ke\auth\model\KeUser;
use think\facade\Request;

/**
 * Class KeAuth
 * @package ke\auth\logic
 */
class Auth
{
    /**
     * @var KeUser
     */
    protected $user;

    /**
     * 登陆
     * @param string $username
     * @param string $password
     * @param int $expire_in
     * @return KeUser
     * @throws AuthException
     */
    public function login($username, $password, $expire_in = 2880)
    {
        $user = KeUser::where('username', $username)->find();
        if (!$user) {
            throw new AuthException('用户不存在');
        }
        if (!$user->verifyPassword($password)) {
            throw new AuthException('密码错误');
        }
        if ($user->status == 1) {
            throw new AuthException("账号“{$username}”被禁用");
        }
        $user->save([
            'login_ip'=> Request::ip(true),
            'login_time'=>$_SERVER['REQUEST_TIME'],
            'login_count'=>$user->login_count + 1,
        ]);

        // 签发令牌
        (new TokenManager())->create($user->id, $expire_in);

        $this->user = $user;
        return $user;
    }


    /**
     * 注销登陆
     */
    public function logout()
    {
        (new TokenManager())->remove();
    }


    /**
     * 初始化当前场景
     * 登陆后初始化用
     * @return KeUser
     * @throws AuthException
     */
    public function init()
    {
        $key = (new TokenManager())->verify();

        $user = KeUser::where('id', $key)->find();
        if (!$user) {
            throw new AuthException('用户不存在');
        }
        if ($user->status == 1) {
            throw new AuthException('账号被禁用');
        }
        $this->user = $user;
        return $user;
    }


    /**
     * 单例
     * @return $this
     */
    public static function instance()
    {
        static $hd;
        if (!$hd) {
            $hd = new static;
        }
        return $hd;
    }


}
