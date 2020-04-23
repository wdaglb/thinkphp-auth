<?php


use Phinx\Migration\AbstractMigration;

class AdminRoleAccess extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('admin_role_access', [
            'comment'=>'角色明细',
            'id'=>false
        ]);
        $table->addColumn('admin_id', 'integer', ['comment'=>'用户ID']);
        $table->addColumn('role_id', 'integer', ['comment'=>'角色ID']);
        $table->addIndex(['admin_id', 'role_id']);
        $table->insert([
            'admin_id'=>1,
            'role_id'=>1
        ]);
        $table->save();
    }
}
