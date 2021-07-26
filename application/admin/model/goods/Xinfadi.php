<?php

namespace app\admin\model\goods;

use think\Model;


class Xinfadi extends Model
{





    // 表名
    protected $name = 'goods_xinfadi';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function goods()
    {
        return $this->belongsTo('Goods','goods_id','id',[],'LEFT')->setEagerlyType(0);
    }











}
