<?php


use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class AdminLogin extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('admin_loginlog', ['comment'=>'登陆日志']);
        $table->addColumn('admin_id', 'integer');
        $table->addColumn('ip', MysqlAdapter::PHINX_TYPE_BIG_INTEGER);
        $table->addColumn('create_time', 'integer');
        $table->addIndex(['admin_id']);
        $table->save();
    }
}
