<?php

namespace app\admin\model\goods;

use think\Model;


class Goods extends Model
{

    

    

    // 表名
    protected $name = 'goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'is_stock_text',
        'status_text'
    ];
    

    
    public function getIsStockList()
    {
        return ['0' => __('Is_stock 0'), '1' => __('Is_stock 1')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2')];
    }

    public function getPackagingTypeList()
    {
        return ['0' => '非标品','1'=>'标品'];
    }


    public function getIsStockTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_stock']) ? $data['is_stock'] : '');
        $list = $this->getIsStockList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function goodscategory()
    {
        return $this->belongsTo('app\admin\model\goods\Category', 'cate_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
