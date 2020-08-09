<?php


namespace ke\auth\model;


use think\Model;

class KeRoleAccess extends Model
{
    protected $name = 'admin_role_access';


    /**
     * 角色信息
     * @return \think\model\relation\HasOne
     */
    public function detail()
    {
        return $this->hasOne(KeRole::class, 'id', 'role_id');
    }
}
