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
        $table->addColumn('policy_id', 'integer', ['comment'=>'策略ID']);
        $table->addIndex(['role_id', 'policy_id']);
        $table->insert([
            [
                'role_id'=>1,
                'policy_id'=>1
            ]
        ]);
        $table->save();
    }
}
