<?php


namespace ke\auth\model;


use think\Model;

class KeRole extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $name = 'admin_role';
    protected $type = [
        'id'=>'integer'
    ];


    public function getPermissionAttr()
    {
        $data = $this->getData('permission');

        return explode(',', $data);
    }


    public function setPermissionAttr($data)
    {
        return implode(',', $data);
    }


    /**
     * 添加权限
     * @param string $name
     * @return KeRole
     */
    public function addPermission($name)
    {
        $permission = $this->getAttr('permission');
        $permission[] = $name;

        $this->setAttr('permission', $permission);

        return $this;
    }


    /**
     * 清空角色权限
     * @return KeRole
     * @throws \Exception
     */
    public function clearPermission()
    {
        $this->setAttr('permission', []);
        return $this;
    }


    /**
     * 删除权限
     * @param string $name
     * @return KeRole
     * @throws \Exception
     */
    public function delPermission($name)
    {
        $permission = $this->getAttr('permission');
        $idx = array_search($name, $permission);
        array_splice($permission, $idx, 1);
        $this->setAttr('permission', $permission);
        return $this;
    }

}
