<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\Db;
/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{

    /**
     * Order模型对象
     * @var \app\admin\model\order\Order
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\Order;
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
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['department', 'supplier'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['department', 'supplier'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {

                $row->getRelation('department')->visible(['id', 'name']);
                $row->getRelation('supplier')->visible(['id', 'supplier_name', 'linkman', 'mobile']);
            }
            $list = collection($list)->toArray();
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
            $arr['send_time'] = strtotime($params['row']['sendtime']);
//            halt($params);
            $arr['department_id'] = $params['row']['department_id'];
            $arr['supplier_id'] = $params['row']['supplier_id'];
            $data = json_encode($arr, JSON_UNESCAPED_UNICODE);

            $this->success('', url("order/order/next"), $data);
        }
        return $this->view->fetch();
    }

    /**
     * 新建订单
     * send_time 发货时间
     * supplier_id 供应商ID
     * department_id 部门ID
     */
    public function next()
    {
        $params = $this->request->param();


        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {

            @$goods_name = json_decode($params['filter'], true)['goods_name'];
//            $goods_name ?$like = ['t2.goods_name','like', '%' . $goods_name . '%']:$like="1=1";
//            $like = $goods_name?['t2.goods_name'=>['like', $goods_name]]:'1=1';
            $like = $goods_name ? ['t2.goods_name' => ['like', '%' . $goods_name . '%']] : '1=1';
            //如果发送的来源是Selectpage，则转发到Selectpage
//            if ($this->request->request('keyField')) {
//                return $this->selectpage();
//            }

            $send_time = $params['_'];
            $params = json_decode($params['filter'], true);//搜索条件
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = DB::name('supplier_goods')
                ->field('t1.goods_id,t1.supplier_id,t2.goods_name,t2.status,t1.price')
                ->alias('t1')
                ->join('__GOODS__ t2', 't1.goods_id=t2.id', 'LEFT')
                ->where(['t1.supplier_id' => $params['supplier.id'], 't2.status' => '1'])
                ->where($like)
                ->limit($offset, $limit)
                ->select();
            $total = DB::name('supplier_goods')
                ->alias('t1')
                ->join('__GOODS__ t2', 't1.goods_id=t2.id', 'LEFT')
                ->where(['t1.supplier_id' => $params['supplier.id'], 't2.status' => '1'])
                ->where($like)
                ->count();

//            foreach ($list as $row) {
//
////                $row->getRelation('department')->visible(['id','name']);
//                $row->getRelation('supplier')->visible(['supplier_id','supplier_name','linkman','mobile']);
//            }
            foreach ($list as $key => &$value) {
                $goods = DB::name('goods')->find($value['goods_id']);
                $value['goods_sn'] = $goods['goods_sn'];
                $value['spec'] = $goods['spec'];
                $value['unit'] = $goods['unit'];
            }
            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function next2()
    {
        $params = $this->request->param();
//        halt($params);
        if(isset($params['ids'])){
            $order_id = $params['ids'];
            $supplier_order = DB::name('order')->find($order_id);
        }


        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax())
        {

            $order_id = json_decode($params['filter'],true)['order_id'];
            $supplier_order = DB::name('order')->find($order_id);

            @$goods_name = json_decode($params['filter'],true)['goods_name'];
            $like = $goods_name?['t2.goods_name'=>['like', '%'.$goods_name.'%']]:'1=1';
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }

            $send_time = $params['_'];
            $params = json_decode($params['filter'],true);//搜索条件
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = DB::name('supplier_goods')
                ->field('t1.goods_id,t1.supplier_id,t2.goods_name,t2.status,t1.price')
                ->alias('t1')
                ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
                ->where(['t1.supplier_id'=>$supplier_order['supplier_id'],'t2.status'=>'1'])
                ->where($like)
                ->limit($offset, $limit)
                ->select();
            $total = DB::name('supplier_goods')
                ->alias('t1')
                ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
                ->where(['t1.supplier_id'=>$supplier_order['supplier_id'],'t2.status'=>'1'])
                ->where($like)
                ->count();
//            foreach ($list as $row) {
//
////                $row->getRelation('department')->visible(['id','name']);
//                $row->getRelation('supplier')->visible(['supplier_id','supplier_name','linkman','mobile']);
//            }
            foreach($list as $key => &$value){
                $goods = DB::name('goods')->find($value['goods_id']);
                $value['goods_sn'] = $goods['goods_sn'];
                $value['spec'] = $goods['spec'];
                $value['unit'] = $goods['unit'];
                $value['order_count'] = DB::name('order_goods')
                    ->where(['order_id'=>$order_id,'goods_id'=>$value['goods_id']])
                    ->value('needqty');
                $price = DB::name('supplier_goods')->where(['goods_id'=>$value['goods_id'],'supplier_id'=>$supplier_order['supplier_id']])->value('price');
                $value['order_amount'] = $price * $value['order_count'];
                $value['remark'] = DB::name('order_goods')
                    ->where(['order_id'=>$order_id,'goods_id'=>$value['goods_id']])
                    ->value('remark');

            }
            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        $this->assignconfig('supplier_id',$supplier_order['supplier_id']);
        $this->assignconfig('send_time',$supplier_order['supplier_id']);
        $this->assignconfig('department_id',$supplier_order['department_id']);
        $this->assignConfig('order_id',$order_id);//传给queryParams
        $this->assign('order_id',$order_id);
        return $this->view->fetch();

    }


    /*
     * 编辑订单
     * next.html有#order_id记录是否已经形成订单
     *
     * */
    public function ajax_add()
    {

        $params = $this->request->param();
        if(empty($params['order_count'])){
            $this->error('下单数量不能为空');
        }
        $order_id = $params['order_id'];
        if($order_id == 0){
            $price = DB::name('supplier_goods')
                ->where(['supplier_id'=>$params['supplier_id'],'goods_id'=>$params['goods_id']])
                ->value('price');
            $order_amount = $price * $params['order_count'];
            //新建订单
            $insert = [
                'order_sn' => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'department_id' => $params['department_id'],
                'supplier_id' => $params['supplier_id'],
                'order_amount' => $order_amount,
                'createtime' => time(),
                'sendtime' => $params['send_time'],
                'status' => "0"
            ];
            $order_id = DB::name('order')->insertGetId($insert);
            $supplier_goods = [
                'order_id' => $order_id,
                'goods_id' => $params['goods_id'],
                'needqty' => $params['order_count'],
                'remark' => $params['remark'],
            ];
            $result = DB::name('order_goods')->insert($supplier_goods);


        }else{
            $is_isset = DB::name('order_goods')
                ->where(['order_id'=>$params['order_id'],'goods_id'=>$params['goods_id']])
                ->find();
            if($is_isset){
                //订单商品表存在此商品(修改)
                $result = DB::name('order_goods')
                    ->where(['id'=>$is_isset['id']])
                    ->update(['needqty'=>$params['order_count'],'remark'=>$params['remark']]);
            }else{
                //订单商品表不存在此商品(新增)
                $insert = [
                    'order_id' => $params['order_id'],
                    'goods_id' => $params['goods_id'],
                    'needqty' => $params['order_count'],
                    'remark' => $params['remark']
                ];
                $result = DB::name('order_goods')->insert($insert);
            }
            $goods = DB::name('order_goods')
                ->where(['order_id'=>$params['order_id']])
                ->select();
            $order_amount = 0;
            foreach($goods as $key => $value){
                $price = DB::name('supplier_goods')
                    ->where(['supplier_id'=>$params['supplier_id'],'goods_id'=>$value['goods_id']])
                    ->value('price');
                $order_amount += $price * $value['needqty'];
            }
            DB::name('order')
                ->where(['id'=>$params['order_id']])
                ->update(['order_amount'=>$order_amount]);

        }
        if($result !== false){
            $this->success('操作成功','',['order_id'=>$order_id]);
        }else{
            $this->error('网络错误');
        }

    }
}
