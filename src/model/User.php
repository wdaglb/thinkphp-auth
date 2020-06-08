<?php


namespace ke\auth\model;


use think\Model;

class User extends Model
{
    protected $table = Auth::TABLE_ADMIN;
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $type = [
        'login_time'=>'timestamp',
    ];

    public function getLoginIpAttr()
    {
        return long2ip($this->getData('login_ip'));
    }

    public function setLoginIpAttr($val)
    {
        return ip2long($val);
    }

    public function verifyPassword(string $password)
    {
        $pwd = $this->getData('password');

        return password_verify($password, $pwd);
    }

    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}
