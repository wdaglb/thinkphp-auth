<?php


use Phinx\Migration\AbstractMigration;

class AdminPolicy extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('admin_policy', ['comment'=>'策略列表']);
        $table->addColumn('parent_id', 'integer', ['comment'=>'上级规则', 'default'=>0]);
        $table->addColumn('name', 'string', ['limit'=>64, 'comment'=>'策略']);
        $table->addColumn('text', 'string', ['limit'=>64, 'comment'=>'说明']);
        $table->addColumn('sort', 'integer', ['comment'=>'顺序', 'default'=>0]);
        $table->addColumn('create_time', 'integer', ['comment'=>'创建时间']);
        $table->save();
    }
}
