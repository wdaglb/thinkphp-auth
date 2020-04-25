<?php
/**
 * +----------------------------------------------------------------------
 * | Name: keAdmin
 * | Author King east To 1207877378@qq.com
 * +----------------------------------------------------------------------
 */

namespace ke\auth\model;


use ke\auth\exception\ErrorException;
use ke\auth\Token;
use think\Exception;
use think\exception\PDOException;
use think\facade\Request;
use think\Db;
use const http\Client\Curl\AUTH_ANY;

/**
 * Class Auth
 * @package ke\auth
 * @property-read int $id
 * @property-read string $username
 * @property-read string $password
 * @property-read string $nickname
 * @property-read string $phone
 * @property-read int $status
 * @property-read Access $access
 * @property-read Role $role
 */
class Auth
{
    const TABLE_ADMIN = 'admin';
    const TABLE_ADMIN_ROLE = 'admin_role';
    const TABLE_ADMIN_ROLE_ACCESS = 'admin_role_access';
    const TABLE_ADMIN_ROLE_PERMISSION = 'admin_role_permission';
    const TABLE_ADMIN_POLICY = 'admin_policy';

    private static $handle;

    /**
     * 令牌存储的信息
     * @var array
     */
    private $tok_info = [];

    /**
     * 实时获取的信息
     * @var array
     */
    private $adm_info = [];

    /**
     * @var Access
     */
    private $accessHandle;

    /**
     * @var Role
     */
    private $roleHandle;

    /**
     * @return Auth
     */
    public static function instance()
    {
        if (!static::$handle) {
            static::$handle = new self;
        }
        return static::$handle;
    }


    public function __get($name)
    {
        if ($name === 'access') {
            if (is_null($this->accessHandle)) {
                $this->accessHandle = new Access($this);
            }
            return $this->accessHandle;
        } else if ($name === 'role') {
            if (is_null($this->roleHandle)) {
                $this->roleHandle = new Role($this);
            }
            return $this->roleHandle;
        }
        return $this->getUserInfo($name);
    }


    /**
     * 初始化
     * @param string $token
     * @return bool
     * @throws ErrorException
     */
    public function init($token)
    {
        $tok = new Token();
        $result = $tok->verify($token);
        if (!$result) {
            throw new ErrorException('令牌授权失效');
        }
        $this->tok_info = $result;
        $admin = Db::name(static::TABLE_ADMIN)->where('id', $result['id'])->find();
        if (!$admin) {
            throw new ErrorException('账户不存在');
        }
        if ($admin['status'] == 1) {
            throw new ErrorException('账户被禁用');
        }
        $this->adm_info = $admin;

        return true;
    }


    /**
     * 登录
     * @param string $username
     * @param string $password
     * @return bool
     * @throws ErrorException
     */
    public function login($username, $password)
    {
        $admin = Db::name(static::TABLE_ADMIN)->where('username', $username)->find();
        if (!$admin) {
            throw new ErrorException("账号“{$username}”不存在");
        }
        if ($admin['status'] == 1) {
            throw new ErrorException("账号“{$username}”被禁用");
        }
        if (!password_verify($password, $admin['password'])) {
            throw new ErrorException('密码错误');
        }
        Db::name(static::TABLE_ADMIN)
            ->where('id', $admin['id'])
            ->update([
                'login_ip' => Request::ip(true),
                'login_time' => $_SERVER['REQUEST_TIME'],
                'login_count' => $admin['login_count'] + 1,
            ]);

        $this->adm_info = $admin;

        $token = new Token();

        unset($admin['password']);
        return $token->create($admin);
    }


    /**
     * 添加用户
     * @param string $username
     * @param string $password
     * @param string $nickname
     * @param array $info
     * @return string
     * @throws ErrorException
     */
    public function addUser($username,
                            $password,
                            $nickname,
                            array $info = [])
    {
        if (Db::name(Auth::TABLE_ADMIN)
            ->where('username', $username)
            ->value('id')) {
            throw new ErrorException('账号已存在');
        }
        $data = Db::name(Auth::TABLE_ADMIN)
            ->insert(array_merge([
                'username'=>$username,
                'password'=>password_hash($password, PASSWORD_DEFAULT),
                'nickname'=>$nickname,
            ], $info));
        if (!$data) {
            throw new ErrorException('添加失败');
        }

        return Db::getLastInsID();
    }


    /**
     * 添加用户角色ID
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    public function addUserForRoleId($userId, $roleId)
    {
        return Db::name(Auth::TABLE_ADMIN_ROLE_ACCESS)
            ->insert([
                'admin_id'=>$userId,
                'role_id'=>$roleId
            ]);
    }


    /**
     * 获取登陆信息
     * @param string $col
     * @return mixed
     */
    public function getUserInfo($col = null)
    {
        if (!is_null($col)) {
            return $this->adm_info[$col];
        }
        return $this->adm_info;
    }


    /**
     * 获取令牌信息
     * @param string $col
     * @return array
     */
    public function getTokenInfo($col = null)
    {
        if (!is_null($col)) {
            return $this->tok_info[$col];
        }
        return $this->tok_info;
    }


    /**
     * 获取用户的角色ID列表
     * @param int $user_id 用户ID，留空使用当前登录的用户
     * @return int[]
     */
    public function getRolesId($user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = $this->getUserInfo('id');
        }
        return Db::name(Auth::TABLE_ADMIN_ROLE_ACCESS)
            ->where('admin_id', $user_id)
            ->column('role_id');
    }


    /**
     * 新建角色
     * @param string $name
     * @param string $remark
     * @return Role
     */
    public function createRole(string $name, string $remark): Role
    {
        return (new Role($this))->create($name, $remark);
    }

}
