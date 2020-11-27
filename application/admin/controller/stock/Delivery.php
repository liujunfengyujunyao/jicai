<?php

namespace app\admin\controller\stock;

use app\common\controller\Backend;
use think\Db;

/**
 * 领料申请管理
 *
 * @icon fa fa-circle-o
 */
class Delivery extends Backend
{
    
    /**
     * Delivery模型对象
     * @var \app\admin\model\stock\Delivery
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\stock\Delivery;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $sort = "fa_delivery.id";
            $total = $this->model
                ->join('__ADMIN__','fa_admin.id=fa_delivery.apply_admin','LEFT')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->field("fa_admin.nickname,fa_delivery.*")
                ->join('__ADMIN__','fa_admin.id=fa_delivery.apply_admin','LEFT')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

            foreach($list as $key => &$value){
                $value['fa_delivery.department_id'] = DB::name('department')->find($value['department_id'])['name'];
                $value['fa_admin.nickname'] = DB::name('admin')->find($value['apply_admin'])['nickname'];
                $value['fa_delivery.createtime'] = $value['createtime'];
                $value['fa_delivery.status'] = $value['status'];
                $value['fa_delivery.audit_admin'] = DB::name('admin')->where(['id'=>$value['audit_admin']])->find()['nickname'];
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isAjax()) {
//            $params = $this->request->param();
//            $send_time = strtotime($params['sendtime']);
//
//            $send_time = strtotime()
            $params = $this->request->param();


            $arr['department_id'] = $params['row']['department_id'];
            $arr['apply_admin'] = $params['row']['apply_admin'];
            $data = json_encode($arr, JSON_UNESCAPED_UNICODE);

            $this->success('', url("stock/deliverygoods/delivery_add"), $data);
        }
        return $this->view->fetch();
    }


    /*
     * 同意出库
     * */
    public function through()
    {
        $delivery_id = $this->request->param('ids');
        $delivery = DB::name('delivery')->find($delivery_id);
        try {
            Db::startTrans();
            $delivery_goods = DB::name('delivery_goods')
                ->where(['delivery_id'=>$delivery_id])
                ->select();
            foreach($delivery_goods as $key => $value){
                $stock = DB::name('stock')->where(['goods_id'=>$value['goods_id']])->find();
                if($stock['stock_number'] < $value['delivery_number']){
                    Db::rollback();
                    $this->error('库存不足');
                }
                $result = DB::name('stock')->where(['goods_id'=>$value['goods_id']])->setDec('stock_number',$value['delivery_number']);
                $goods = DB::name('goods')->where(['id'=>$value['goods_id']])->find();
                $insert_log = [
                    'department_name' => DB::name('department')->where(['id'=>$delivery['department_id']])->find()['name'],
                    'apply_name' => DB::name('admin')->where(['id'=>$delivery['apply_admin']])->find()['nickname'],
                    'createtime' => time(),
                    'goods_sn' => $goods['goods_sn'],
                    'goods_name' => $goods['goods_name'],
                    'spec' => $goods['spec'],
                    'unit' => $goods['unit'],
                    'unit_price' => $stock['unit_price'],
                    'delivery_number' => $value['delivery_number'],
                    'delivery_amount' => $value['delivery_amount'],
                    'remark' => $value['remark']
                ];
                DB::name('delivery_log')->insert($insert_log);
                if (!$result) {
                    throw new Exception('中途错误');
                }
            }
            $audit_admin = $this->auth->id;
            DB::name('delivery')->where(['id'=>$delivery_id])->update(['status'=>"1","audittime"=>time(),'audit_admin'=>$audit_admin]);
            //记录领取日志


            //任意一个表写入失败都会抛出异常：
            Db::commit();
            $this->success('操作完成');
        } catch (Exception $e) {
            //如获取到异常信息，对所有表的删、改、写操作，都会回滚至操作前的状态：
            Db::rollback();
            $this->error($e);
        }
    }

    /*
     * 取消出库
     * */
    public function reject()
    {
        $delivery_id = $this->request->param('ids');
        $audit_admin = $this->auth->id;
        $result = DB::name('delivery')->where(['id'=>$delivery_id])->update(['status'=>"2","audittime"=>time(),'audit_admin'=>$audit_admin]);
        if($result !== false){
            $this->success('已取消');
        }else{
            $this->error('网络错误');
        }
    }



}
