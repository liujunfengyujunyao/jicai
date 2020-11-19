<?php

namespace app\admin\model\order;

use think\Model;
use think\Db;

class Order extends Model
{

    

    

    // 表名
    protected $name = 'order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'sendtime_text',
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2')];
    }


    public function getSendtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['sendtime']) ? $data['sendtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setSendtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function department()
    {
        return $this->belongsTo('app\admin\model\auth\Department', 'department_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function supplier()
    {
        return $this->belongsTo('app\admin\model\supplier\Supplier', 'supplier_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    //获取excel订单商品明细
    public function getOrderInfo($data)
    {
        $result = [];
        foreach($data as $key => $value){
            $order_goods = DB::name('order_goods')
                ->where(['order_id'=>$value['id']])
                ->select();
            foreach($order_goods as $k => $v){
                $goods = DB::name('goods')->find($v['goods_id']);
                $result[$key][$k]['index_id'] = $k+1;
                $result[$key][$k]['cate'] = DB::name('goods')
                    ->alias('t1')
                    ->join('__GOODSCATEGORY__ t2','t1.cate_id=t2.id')
                    ->where(['t1.id'=>$goods['id']])
                    ->value('t2.category_name');
                $result[$key][$k]['scate'] = DB::name('goods')
                    ->alias('t1')
                    ->join('__GOODSCATEGORY__ t2','t1.scate_id=t2.id')
                    ->where(['t1.id'=>$goods['id']])
                    ->value('t2.category_name');
                $result[$key][$k]['goods_sn'] = $goods['goods_sn'];
                $result[$key][$k]['goods_name'] = $goods['goods_name'];
                $result[$key][$k]['spec'] = $goods['spec'];
                $result[$key][$k]['unit'] = $goods['unit'];
                $result[$key][$k]['order_count'] = $v['needqty'];
                if($value['status']=="1"){
                    $result[$key][$k]['send_count']=$v['needqty'];
                }else{
                    $result[$key][$k]['send_count']="'" . "0";
                }
                $result[$key][$k]['price'] = $v['price'];
                $result[$key][$k]['order_price'] = $v['order_price'];
                if($value['status']=="1"){
                    $result[$key][$k]['send_price']=$v['order_price'];
                }else{
                    $result[$key][$k]['send_price']="'" . "0";
                }
                $result[$key][$k]['remark'] = $v['remark'];

            }
        }
        return $result;

    }
}
