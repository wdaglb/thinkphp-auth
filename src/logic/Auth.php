<?php


namespace ke\auth\logic;

use ke\auth\exception\AuthException;
use ke\auth\model\KeUser;
use think\facade\Request;

/**
 * Class KeAuth
 * @package ke\auth\logic
 * @method bool verifyPassword(string $password)
 * @method bool changePassword(string $old, string $new)
 * @method bool save(array $data = [], array $where = [], string $sequence = null)
 */
class Auth
{
    /**
     * 配置
     * @var array
     */
    protected $config = [
        // 创始人ID
        // 创始人拥有所有权限，不可被删除，不可被普通管理修改信息
        'create_uid'=>[1],
    ];

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
     * 设置配置
     * - 需要在init调用前设置，否则不生效
     * @param array $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }


    /**
     * 获取配置
     * @param string $key
     * @return array
     */
    public function getConfig($key = null)
    {
        if (!is_null($key)) {
            return $this->config[$key];
        }
        return $this->config;
    }


    /**
     * 判断id是否为创始人
     * @param null|int $id 留空使用已登录ID
     * @return bool
     */
    public function isCreateUser($id = null)
    {
        if (is_null($id)) {
            $id = $this->user->id;
        }
        return in_array($id, $this->getConfig('create_uid'));
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
     * 获取用户资料
     * @return KeUser
     */
    public function getInfo()
    {
        return $this->user->hidden([
            'password'
        ]);
    }


    public function __get($name)
    {
        return $this->user->$name;
    }


    public function __call($name, $arguments)
    {
        return call_user_func_array([
            $this->user,
            $name,
        ], $arguments);
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
