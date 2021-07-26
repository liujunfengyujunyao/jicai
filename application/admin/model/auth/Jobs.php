<?php

namespace app\admin\model\auth;

use think\Model;


class Jobs extends Model
{

    

    

    // 表名
    protected $name = 'jobs';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







}
