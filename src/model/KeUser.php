<?php


namespace ke\auth\model;

use ke\auth\exception\AuthException;
use ke\auth\logic\Auth;
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
     * 关联角色列表
     * @return KeRole[]
     */
    public function role()
    {
        static $list;
        if (is_null($list)) {
            $role_id = KeRoleAccess::where('admin_id', $this->id)->column('role_id');
            $list = KeRole::where('id', 'in', $role_id)->select();
        }

        return $list;
    }


    /**
     * 清空角色
     * @throws \Exception
     */
    public function clearRole()
    {
        KeRoleAccess::where('admin_id', $this->id)->delete();
    }


    /**
     * 添加角色
     * @param $id
     */
    public function addRole($id)
    {
        KeRoleAccess::create([
            'admin_id'=>$this->id,
            'role_id'=>$id,
        ]);
    }


    /**
     * 获取权限列表
     * @return array
     */
    public function getPolicys()
    {
        static $list;
        if (is_null($list)) {
            $role_id = $this->role()->column('id');

            $policyTable = (new KePolicy())->getTable();

            $model = (new KeRolePermission())->db()
                ->alias('p')
                ->join($policyTable . ' policy', 'p.policy_id=policy.id')
                ->where('p.role_id', 'in', $role_id);

            $list = $model->column('policy.name');
        }
        return $list;
    }


    /**
     * 检索策略
     * @param string|array $policy 匹配策略
     * @param string $exp and必须匹配所有策略，or只有一条匹配成功则返回true
     * @return bool
     * @throws AuthException
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
