<?php


use Phinx\Migration\AbstractMigration;

class AdminRolePermission extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('admin_role_permission', [
            'comment'=>'角色权限',
            'id'=>false
        ]);
        $table->addColumn('role_id', 'integer', ['comment'=>'角色ID']);
        $table->addColumn('policy', 'string', ['comment'=>'策略', 'limit'=>64]);
        $table->addIndex(['role_id', 'policy']);
        $table->save();
    }
}
