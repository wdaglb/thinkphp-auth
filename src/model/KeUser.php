<?php


namespace ke\auth\model;

use ke\auth\exception\AuthException;
use ke\auth\logic\Auth;
use think\db\Query;
use think\Model;

class KeUser extends Model
{
    protected $name = 'admin';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $type = [
        'id'=>'integer'
    ];


    public function getPermissionAttr()
    {
        $data = $this->getData('permission');
        if (empty($data)) {
            return [];
        }

        return explode(',', $data);
    }


    public function setPermissionAttr($data)
    {
        return implode(',', $data);
    }


    /**
     * 密码修改器
     * @param string $val
     * @return false|string|null
     */
    public function setPasswordAttr($val)
    {
        return password_hash($val, PASSWORD_DEFAULT);
    }


    /**
     * 校验密码正确性
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->getData('password'));
    }


    /**
     * 密码修改
     * @param string $old
     * @param string $new
     * @return bool
     * @throws AuthException
     */
    public function changePassword($old, $new)
    {
        if (!$this->verifyPassword($old)) {
            throw new AuthException('原密码错误');
        }
        $this->setAttr('password', $new);
        $this->save();

        return true;
    }


    /**
     * 关联角色明细
     * @return \think\model\relation\HasMany
     */
    public function roleAccess()
    {
        return $this->hasMany(KeRoleAccess::class, 'admin_id', 'id');
    }


    /**
     * 获取权限列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPolicys()
    {
        static $list;
        if (is_null($list)) {
            $list = $this->getAttr('permission') ?? [];
            $temps = $this->roleAccess()
                ->with(['detail'=>function (Query  $query) {
                    $query->field('id,permission');
                }])
                ->where('scope', 'user')
                ->field('id,role_id')
                ->select();
            foreach ($temps as $tmp) {
                $list = array_merge($list, $tmp->detail->permission);
            }
            $list = array_unique($list);
        }
        return $list;
    }


    /**
     * 检索策略
     * @param string|array $policy 匹配策略
     * @param string $exp and必须匹配所有策略，or只有一条匹配成功则返回true
     * @return bool
     * @throws AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function hasAuth($policy, $exp = 'and')
    {
        $auth = Auth::instance();
        if ($auth->isCreateUser()) {
            return true;
        }

        $list = $this->getPolicys();

        if (is_string($policy)) {
            $res = in_array($policy, $list);
            if (!$res) {
                throw new AuthException('没有权限操作');
            }
            return true;
        }

        foreach ($policy as $name) {
            $bool = in_array($name, $list);
            if (!$bool && $exp === 'and') {
                throw new AuthException('没有权限操作');
            }
            if ($bool && $exp === 'or') {
                return true;
            }
        }
        return true;
    }

}
