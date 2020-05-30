<?php


use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class Admin extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('admin', ['comment'=>'管理表']);
        $table->addColumn('username', 'string', ['limit'=>64, 'comment'=>'账号名']);
        $table->addColumn('password', 'string', ['limit'=>255, 'comment'=>'密码']);
        $table->addColumn('nickname', 'string', ['limit'=>20, 'comment'=>'名字']);
        $table->addColumn('avatar', 'string', ['limit'=>255, 'comment'=>'头像']);
        $table->addColumn('phone', 'string', ['limit'=>11, 'comment'=>'手机号']);
        $table->addColumn('status', MysqlAdapter::PHINX_TYPE_BOOLEAN, ['comment'=>'状态：0正常，1禁用', 'default'=>0]);
        $table->addColumn('create_time', MysqlAdapter::PHINX_TYPE_INTEGER,
            ['comment'=>'创建时间', 'default'=>0, 'signed'=>0]);
        $table->addColumn('update_time', MysqlAdapter::PHINX_TYPE_INTEGER,
            ['comment'=>'更新时间', 'default'=>0, 'signed'=>0]);
        $table->addColumn('login_ip', MysqlAdapter::PHINX_TYPE_BIG_INTEGER, [
            'default'=>0,
            'signed'=>0
        ]);
        $table->addColumn('login_time', MysqlAdapter::PHINX_TYPE_BIG_INTEGER, [
            'default'=>0,
            'signed'=>0,
            'comment'=>'最后一次登陆时间'
        ]);
        $table->addColumn('login_count', 'integer', [
            'comment'=>'登陆次数',
            'default'=>0,
            'signed'=>0
        ]);
        $table->addColumn('login_fail_time', MysqlAdapter::PHINX_TYPE_BIG_INTEGER, [
            'comment'=>'登陆错误时间',
            'default'=>0,
            'signed'=>0
        ]);
        $table->addColumn('login_fail_count', MysqlAdapter::PHINX_TYPE_BOOLEAN, [
            'comment'=>'登陆错误次数',
            'default'=>0,
            'signed'=>0,
            'limit'=>4
        ]);

        $table->addIndex(['username'], ['unique'=>true]);

        $table->insert([
            'username'=>'admin',
            'password'=>password_hash('123456', PASSWORD_DEFAULT),
            'nickname'=>'超级管理员',
            'phone'=>'13000000000',
            'status'=>0,
            'create_time'=>$_SERVER['REQUEST_TIME']
        ]);
        $table->save();
    }
}
