<?php


namespace ke\auth\model;


use ke\auth\exception\ErrorException;
use think\Db;
use think\Exception;

class Role
{
    private $auth;

    private $info = [];

    private $permission = [];

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }


    /**
     * 添加角色
     * @param string $name 角色名称
     * @param string $remark 备注信息
     * @return $this
     */
    public function create(string $name, string $remark)
    {
        $this->info = [
            'name'=>$name,
            'remark'=>$remark
        ];
        return $this;
    }


    /**
     * 查询角色
     * @param int $id
     * @return $this
     * @throws ErrorException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get(int $id)
    {
        $data = Db::name(Auth::TABLE_ADMIN_ROLE)
            ->where('id', $id)
            ->find();
        if (!$data) {
            throw new ErrorException('角色不存在');
        }
        $this->info = $data;
        return $this;
    }


    /**
     * 添加权限ID
     * @param int $id
     * @return $this
     */
    public function addPermission(int $id)
    {
        $this->permission[] = $id;
        return $this;
    }


    /**
     * 批量添加权限
     * @param array $arr
     * @return $this
     */
    public function batchAddPermission(array $arr)
    {
        $this->permission = array_merge($this->permission, $arr);
        return $this;
    }


    /**
     * 删除权限
     * @param int $id
     * @return $this
     */
    public function deletePermission(int $id)
    {
        $idx = array_search($id, $this->permission);
        if ($idx !== false) {
            array_splice($this->permission, $idx);
        }
        return $this;
    }


    /**
     * 保存角色更改
     * @throws ErrorException
     */
    public function save()
    {
        Db::startTrans();
        try {
            $db = Db::name(Auth::TABLE_ADMIN_ROLE);
            if (isset($this->info['id'])) {
                $data = $db->where('id', $this->info['id'])->find();
                if (!$data) {
                    throw new Exception('角色不存在');
                }
                $data->save([
                    'name'=>$this->info['name'],
                    'remark'=>$this->info['remark']
                ]);
                // 清理旧的权限
                Db::name(Auth::TABLE_ADMIN_ROLE_PERMISSION)
                    ->where('role_id', $this->info['id'])
                    ->delete();
            } else {
                $data = $db->insert([
                    'name'=>$this->info['name'],
                    'remark'=>$this->info['remark']
                ]);
                if (!$data) {
                    throw new Exception('添加失败');
                }
                $this->info['id'] = Db::getLastInsID();
            }

            // 加入权限
            foreach ($this->permission as $pid) {
                Db::name(Auth::TABLE_ADMIN_ROLE_PERMISSION)
                    ->insert([
                        'role_id'=>$this->info['id'],
                        'policy_id'=>$pid
                    ]);
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw new ErrorException($e->getMessage());
        }
    }


    /**
     * 删除角色
     * @throws ErrorException
     */
    public function delete()
    {
        Db::startTrans();
        try {
            Db::name(Auth::TABLE_ADMIN_ROLE)
                ->where('id', $this->info['id'])
                ->delete();
            // 清理旧的权限
            Db::name(Auth::TABLE_ADMIN_ROLE_PERMISSION)
                ->where('role_id', $this->info['id'])
                ->delete();

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw new ErrorException($e->getMessage());
        }
    }

}
