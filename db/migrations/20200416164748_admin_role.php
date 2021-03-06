<?php


use Phinx\Migration\AbstractMigration;

class AdminRole extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('admin_role', ['comment'=>'用户角色']);
        $table->addColumn('name', 'string', ['limit'=>32, 'comment'=>'角色名']);
        $table->addColumn('remark', 'string', ['limit'=>255, 'comment'=>'描述', 'default'=>'']);
        $table->addColumn('permission', 'text', ['null'=>true, 'comment'=>'拥有权限']);
        $table->addColumn('sort',
            \Phinx\Db\Adapter\MysqlAdapter::PHINX_TYPE_SMALL_INTEGER, [
                'default'=>0
            ]);
        $table->addColumn('create_time', 'integer');
        $table->save();
    }
}
