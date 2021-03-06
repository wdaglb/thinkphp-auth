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
     * @var TokenManager
     */
    protected $token;


    public function __construct()
    {
        $this->token = new TokenManager();
    }


    /**
     * 登录令牌
     * @return false|string
     */
    public function loginToken()
    {
        return $this->token->get();
    }


    /**
     * 登陆
     * @param string $username
     * @param string $password
     * @param int $expire_in
     * @return $this
     * @throws AuthException
     */
    public function login($username, $password, $expire_in = 7200)
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
        $this->token->create($user->id, $expire_in);

        $this->user = $user;
        return $this;
    }


    /**
     * 注销登陆
     */
    public function logout()
    {
        $this->user = [];
        $this->token->remove();
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
        $key = $this->token->verify();

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
     * 刷新登录态有效时长
     * 登陆后初始化用
     * @param int $expire_in 有效时长
     * @return true
     * @throws AuthException
     */
    public function refresh($expire_in = 7200)
    {
        try {
            $this->token->refresh($expire_in);

            return true;
        } catch (\InvalidArgumentException $e) {
            throw new AuthException($e->getMessage());
        }
    }


    /**
     * 获取用户资料
     * @return KeUser|false
     */
    public function getInfo()
    {
        if (empty($this->user)) {
            return false;
        }
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
