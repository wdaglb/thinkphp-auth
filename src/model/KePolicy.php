<?php


namespace ke\auth\model;


use think\Model;

class KePolicy extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = false;
    protected $name = 'admin_policy';

    protected $type = [
        'id'=>'integer',
        'parent_id'=>'integer',
        'sort'=>'integer'
    ];

}
