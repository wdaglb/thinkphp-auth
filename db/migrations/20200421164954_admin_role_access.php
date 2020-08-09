<?php


use Phinx\Migration\AbstractMigration;

class AdminRoleAccess extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('admin_role_access', [
            'comment'=>'角色明细',
        ]);
        $table->addColumn('scope', 'string', ['limit'=>32, 'comment'=>'类型']);
        $table->addColumn('admin_id', 'integer', ['comment'=>'用户ID']);
        $table->addColumn('role_id', 'integer', ['comment'=>'角色ID']);
        $table->addIndex(['admin_id', 'role_id']);
        $table->save();
    }
}
