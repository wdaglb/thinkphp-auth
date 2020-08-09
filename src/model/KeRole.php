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


    /**
     * 获取当前角色权限
     * @return array
     */
    public function permissions()
    {
        static $list;
		
        if (is_null($list)) {
            $model = (new KeRolePermission())->db()
                ->where('role_id', $this->id);

            $list = $model->column('policy');
        }

        return $list;
    }


    /**
     * 添加权限
     * @param string $name
     */
    public function addPermission($name)
    {
        KeRolePermission::create([
            'role_id'=>$this->id,
            'policy'=>$name,
        ]);
    }


    /**
     * 清空角色权限
     * @throws \Exception
     */
    public function clearPermission()
    {
        KeRolePermission::where('role_id', $this->id)->delete();
    }


    /**
     * 删除权限
     * @param string $name
     * @throws \Exception
     */
    public function delPermission($name)
    {
        KeRolePermission::where('role_id', $this->id)
            ->where('policy', $name)
            ->delete();
    }

}
