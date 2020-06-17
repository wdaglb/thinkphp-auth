<?php


namespace ke\auth\model;


use think\Model;

class KeRole extends Model
{
    protected $pk = 'id';
    protected $name = 'admin_role';


    /**
     * 获取当前角色权限
     * @return array
     */
    public function permissions()
    {
        static $list;
        if (is_null($list)) {
            $policyTable = (new KePolicy())->getTable();

            $model = (new KeRolePermission())->db()
                ->alias('p')
                ->join($policyTable . ' policy', 'p.policy_id=policy.id')
                ->where('p.role_id', $this->id);

            $list = $model->column('policy.name');
        }

        return $list;
    }


    /**
     * 添加权限ById
     * @param int $id
     */
    public function addPermissionById($id)
    {
        KeRoleAccess::create([
            'role_id'=>$this->id,
            'policy_id'=>$id,
        ]);
    }


    /**
     * 添加权限byName
     * @param string $name
     */
    public function addPermissionByName($name)
    {
        $id = KePolicy::where('name', $name)->value('id');
        KeRoleAccess::create([
            'role_id'=>$this->id,
            'policy_id'=>$id,
        ]);
    }


    /**
     * 清空角色权限
     * @throws \Exception
     */
    public function clearPermission()
    {
        KeRoleAccess::where('role_id', $this->id)->delete();
    }


    /**
     * 删除权限ById
     * @param int $id
     * @throws \Exception
     */
    public function delPermissionById($id)
    {
        KeRoleAccess::where('role_id', $this->id)
            ->where('policy_id', $id)
            ->delete();
    }


    /**
     * 删除权限ByName
     * @param string $name
     * @throws \Exception
     */
    public function delPermissionByName($name)
    {
        $id = KePolicy::where('name', $name)->value('id');
        KeRoleAccess::where('role_id', $this->id)
            ->where('policy_id', $id)
            ->delete();
    }

}