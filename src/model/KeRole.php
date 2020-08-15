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
        'id'=>'integer',
        'login_time'=>'timestamp',
        'login_fail_time'=>'timestamp',
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


    public function getLoginIpAttr()
    {
        return long2ip($this->getData('login_ip'));
    }


    public function setLoginIpAttr($ip)
    {
        return ip2long($ip);
    }


    /**
     * 添加权限
     * @param string|array $name
     * @return KeRole
     */
    public function addPermission($name)
    {
        $permission = $this->getAttr('permission') ?? [];
        if (is_array($name)) {
            $permission = array_merge($permission, $name);
        } else {
            $permission[] = $name;
        }

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
