<?php

namespace app\admin\controller\order;
use Complex\Exception;
use app\common\controller\Backend;
use PhpOffice\PhpSpreadsheet\Writer\Xls\Parser;
use think\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{
    protected $noNeedRight = ['ajax_edit','ajax_add','ajax_del','next','next2','next3','next_add','ajax_time','department_list','pr_order','cate_list'];
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
     * 默认生成的控制器所继承的父类中有inde/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    public function department_list()
    {


            $list = DB::name('department')->field('id,name')->where(['status'=>'1'])->select();
            $json = json($list);

        return $json;
    }
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
            $order = "asc";
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

          foreach($list as $key => &$value){
              $value['department']['id'] = $value['department']['name'];
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

            $arr['send_time'] = strtotime($params['row']['sendtime']);
//            halt($params);
            $arr['department_id'] = $params['row']['department_id'];
            $arr['supplier_id'] = $params['row']['supplier_id'];
            $arr['cate_id'] = $params['row']['cate_id'];
            $data = json_encode($arr, JSON_UNESCAPED_UNICODE);
//            halt($data);
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

        if(!empty($params['order_id'])){
            $this->view->assign("order_id",$params['order_id']);
        }else{
            $this->view->assign('order_id',"0");
        }

        @$this->assignconfig('supplier_id',$params['supplier_id']);
        @$this->assignconfig('cate_id',$params['cate_id']);

        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            if(isset(json_decode($params['filter'], true)['order_id'])){
                $order_id = json_decode($params['filter'], true)['order_id'];
//                $this->assignconfig('order_id',$order_id);
            }


            @$goods_name = json_decode($params['filter'], true)['goods_name'];
//            $goods_name ?$like = ['t2.goods_name','like', '%' . $goods_name . '%']:$like="1=1";
//            $like = $goods_name?['t2.goods_name'=>['like', $goods_name]]:'1=1';
            $like = $goods_name ? ['t2.goods_name' => ['like', '%' . $goods_name . '%']] : NULL;
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
                ->where(['t1.supplier_id' => $params['supplier_id'], 't2.status' => '1'])
                ->where(['t2.cate_id'=>$params['cate_id']])
                ->where($like)
                ->limit($offset, $limit)
                ->select();
            $total = DB::name('supplier_goods')
                ->alias('t1')
                ->join('__GOODS__ t2', 't1.goods_id=t2.id', 'LEFT')
                ->where(['t1.supplier_id' => $params['supplier_id'], 't2.status' => '1'])
                ->where($like)
                ->where(['t2.cate_id'=>$params['cate_id']])
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
                if(isset($order_id)){
                    $value['order_count'] = DB::name('order_goods')
                        ->where(['order_id'=>$order_id,'goods_id'=>$value['goods_id']])
                        ->value('needqty');
                    $price = DB::name('supplier_goods')->where(['goods_id'=>$value['goods_id'],'supplier_id'=>$params['supplier_id']])->value('price');
                    $value['order_amount'] = $price * $value['order_count'];
                    $value['remark'] = DB::name('order_goods')
                        ->where(['order_id'=>$order_id,'goods_id'=>$value['goods_id']])
                        ->value('remark');
                }

            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch();
    }


    /*
     * 编辑订单
     * */
//    public function next2()
//    {
//        $params = $this->request->param();
//        if(isset($params['ids'])){
//            $order_id = $params['ids'];
//            $supplier_order = DB::name('order')->find($order_id);
//        }
//
//
//        //当前是否为关联查询
//        $this->relationSearch = true;
//        //设置过滤方法
//        $this->request->filter(['strip_tags', 'trim']);
//        if ($this->request->isAjax())
//        {
//
//            $order_id = json_decode($params['filter'],true)['order_id'];
//            $supplier_order = DB::name('order')->find($order_id);
//
//            @$goods_name = json_decode($params['filter'],true)['goods_name'];
//            $like = $goods_name?['t2.goods_name'=>['like', '%'.$goods_name.'%']]:'1=1';
//            //如果发送的来源是Selectpage，则转发到Selectpage
//            if ($this->request->request('keyField'))
//            {
//                return $this->selectpage();
//            }
//
//            $send_time = $params['_'];
//            $params = json_decode($params['filter'],true);//搜索条件
//            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
//            $list = DB::name('supplier_goods')
//                ->field('t1.goods_id,t1.supplier_id,t2.goods_name,t2.status,t1.price')
//                ->alias('t1')
//                ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
//                ->where(['t1.supplier_id'=>$supplier_order['supplier_id'],'t2.status'=>'1'])
//                ->where($like)
//                ->limit($offset, $limit)
//                ->select();
//            $total = DB::name('supplier_goods')
//                ->alias('t1')
//                ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
//                ->where(['t1.supplier_id'=>$supplier_order['supplier_id'],'t2.status'=>'1'])
//                ->where($like)
//                ->count();
////            foreach ($list as $row) {
////
//////                $row->getRelation('department')->visible(['id','name']);
////                $row->getRelation('supplier')->visible(['supplier_id','supplier_name','linkman','mobile']);
////            }
//            foreach($list as $key => &$value){
//                $goods = DB::name('goods')->find($value['goods_id']);
//                $value['goods_sn'] = $goods['goods_sn'];
//                $value['spec'] = $goods['spec'];
//                $value['unit'] = $goods['unit'];
//                $value['order_count'] = DB::name('order_goods')
//                    ->where(['order_id'=>$order_id,'goods_id'=>$value['goods_id']])
//                    ->value('needqty');
//                $price = DB::name('supplier_goods')->where(['goods_id'=>$value['goods_id'],'supplier_id'=>$supplier_order['supplier_id']])->value('price');
//                $value['order_amount'] = $price * $value['order_count'];
//                $value['remark'] = DB::name('order_goods')
//                    ->where(['order_id'=>$order_id,'goods_id'=>$value['goods_id']])
//                    ->value('remark');
//                if(is_null($value['order_count'])) unset($list[$key]);
//
//            }
//            $list = collection($list)->toArray();
//            $result = array("total" => $total, "rows" => $list);
//            return json($result);
//        }
//
//        //需要ajax返回回来的参数
//        $this->assignconfig('supplier_id',$supplier_order['supplier_id']);
//        $this->assignconfig('send_time',$supplier_order['supplier_id']);
//        $this->assignconfig('department_id',$supplier_order['department_id']);
//        $this->assignConfig('order_id',$order_id);//传给queryParams
////        $info = DB::name('')
//        $this->assign('order_id',$order_id);
//        return $this->view->fetch();
//
//    }

    public function next2()
    {
        $params = $this->request->param();

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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = DB::name('order_goods')
                ->field('id,goods_sn,goods_name,spec,unit,price,needqty as order_count,order_price as order_amount,sendqty as takeorder_count,send_price as takeorder_amount,remark,status')
                ->where(['order_id'=>$order_id])
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => count($list), "rows" => $list);
            return json($result);
        }
        //需要ajax返回回来的参数
        $this->assignconfig('supplier_id',$supplier_order['supplier_id']);
        $this->assignconfig('send_time',$supplier_order['sendtime']);
        $this->assignconfig('department_id',$supplier_order['department_id']);
        $this->assignConfig('order_id',$order_id);//传给queryParams
//        $info = DB::name('')
        $department = DB::name('department')->where(['id'=>$supplier_order['department_id']])->find();
        $supplier = DB::name('supplier')->where(['id'=>$supplier_order['supplier_id']])->find();
        $order = DB::name('order')->where(['id'=>$order_id])->find();
        if($order['status'] == '0'){
            $status = "未收货";
        }elseif($order['status'] == "1"){
            $status = "已收货";
        }else{
            $status = "已取消";
        }
        $send_time = date("Y-m-d",$order['sendtime']);
        $this->assign('department_name',$department['name']);
        $this->assign('supplier_name',$supplier['supplier_name']);
        $this->assign("linkman",$supplier['linkman']);
        $this->assign('mobile',$supplier['mobile']);
        $this->assign('status',$status);
        $this->assign('send_time',$send_time);
        $this->assign('order_id',$order_id);
        return $this->view->fetch();

    }

    public function next3()
    {
        $params = $this->request->param();
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            $order_id = json_decode($params['filter'],true)['order_id'];
            $supplier_order = DB::name('order')->where(['id'=>$order_id])->find();
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            @$goods_name = json_decode($params['filter'], true)['goods_name'];
            $like = $goods_name ? ['t2.goods_name' => ['like', '%' . $goods_name . '%']] : NULL;

            $list = DB::name('supplier_goods')
                ->field('t1.goods_id,t1.supplier_id,t2.goods_name,t2.goods_sn,t2.spec,t2.unit,t2.status,t1.price')
                ->alias('t1')
                ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
                ->where(['t1.supplier_id'=>$supplier_order['supplier_id'],'t2.status'=>'1'])
                ->where($like)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $array = DB::name('order_goods')
                ->where(['order_id'=>$order_id])
                ->column('goods_id');

            foreach($list as $key => &$value){
                $value['order_count'] = $value['order_amount'] = null;
                $is_send = DB::name('order_goods')->where(['order_id'=>$order_id,'goods_id'=>$value['goods_id'],'status'=>1])->find();
                if($is_send){
                    $value['send_status'] = 1;
                }
                foreach($array as $k => $v){
                    if($value['goods_id'] == $v){
                        $data = DB::name('order_goods')
                            ->where(['goods_id'=>$v,'order_id'=>$order_id])
                            ->find();
                        $value['order_count'] = $data['needqty'];
                        $value['order_amount'] = $data['order_price'];
                        $value['remark'] = $data['remark'];
                        $value['price'] = $data['price'];
                    }
                }
            }

            $result = array("total" => count($list), "rows" => $list);

            return json($result);
        }
        $this->assignConfig('order_id',$params['order_id']);//传给queryParams
        $this->assign('order_id',$params['order_id']);
        return $this->view->fetch();
    }

    /*
     *
     * 编辑页面->新增
     * */
    public function next_add()
    {
        $params = $this->request->param();

        if(empty($params['order_count'])){
            $this->error('下单数量不能为空');
        }
        $order = DB::name('order')->where(['id'=>$params['order_id']])->find();
        $supplier_goods = DB::name('supplier_goods')->where(['supplier_id'=>$order['supplier_id'],'goods_id'=>$params['goods_id']])->find();
        $order_goods = DB::name('order_goods')
            ->where(['order_id'=>$params['order_id'],'goods_id'=>$params['goods_id']])
            ->find();
        if($order_goods){
            //存在,修改
            $order_price = $order_goods['price'] * $params['order_count'];
            $update = [
                'needqty' => $params['order_count'],
                'order_price' => $order_price
            ];
            DB::name('order_goods')
                ->where(['goods_id'=>$params['goods_id'],'order_id'=>$params['order_id']])
                ->update($update);
        }else{
            //不存在,新增
            $order_price = $supplier_goods['price'] * $params['order_count'];
            $goods = DB::name('goods')
                ->where(['id'=>$params['goods_id']])
                ->find();
            $insert = [
                'order_id' => $params['order_id'],
                'goods_id' => $goods['id'],
                'cate_id' => $goods['cate_id'],
                'cate_name' => DB::name('goodscategory')->where(['id'=>$goods['cate_id']])->value('category_name'),
                'scate_id' => $goods['scate_id'],
                'scate_name' => DB::name('goodscategory')->where(['id'=>$goods['scate_id']])->value('category_name'),
                'needqty' => $params['order_count'],
                'remark' => $params['remark'],
                'goods_name' => $goods['goods_name'],
                'spec' => $goods['spec'],
                'unit' => $goods['unit'],
                'price' => $supplier_goods['price'],
                'goods_sn' => $goods['goods_sn'],
                'status' => 0,
                'order_price' => $order_price
            ];
            DB::name('order_goods')->insert($insert);
        }

        $data = DB::name('order_goods')
            ->where(['order_id'=>$params['order_id']])
            ->select();
        $order_amount = 0;
        foreach($data as $key => $value){
            $order_amount += $value['price'] * $value['needqty'];
        }
        $result = DB::name('order')
            ->where(['id'=>$params['order_id']])
            ->update(['order_amount'=>$order_amount]);
        if($result !== false){
            $this->success('操作完成');
        }else{
            $this->error('网络错误');
        }

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

        $goods = DB::name('goods')->find($params['goods_id']);
        if(empty($params['supplier_id'])){
            $params['supplier_id'] = DB::name('order')->where(['id'=>$order_id])->value('supplier_id');
        }
            $price = DB::name('supplier_goods')
                ->where(['supplier_id'=>$params['supplier_id'],'goods_id'=>$params['goods_id']])
                ->value('price');



        if($order_id == 0){
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
                'goods_name' => $goods['goods_name'],
                'spec' => $goods['spec'],
                'unit' => $goods['unit'],
                'price' => $price,
                'order_price' => $price * $params['order_count'],
                'goods_sn' => $goods['goods_sn'],
                'status' => 0,
                'back_price' => 0,
                'send_price' => 0,
                'cate_id' => $goods['cate_id'],
                'scate_id' => $goods['scate_id'],
                'cate_name' => DB::name('goodscategory')->find($goods['cate_id'])['category_name'],
                'scate_name' => DB::name('goodscategory')->find($goods['scate_id'])['category_name']
            ];

            $result = DB::name('order_goods')->insert($supplier_goods);


        }else{
            $is_isset = DB::name('order_goods')
                ->where(['order_id'=>$params['order_id'],'goods_id'=>$params['goods_id']])
                ->find();
            if($is_isset){
                //订单商品表存在此商品(修改)
                $order_price = $is_isset['price'] * $params['order_count'];
                $result = DB::name('order_goods')
                    ->where(['id'=>$is_isset['id']])
                    ->update(['needqty'=>$params['order_count'],'remark'=>$params['remark'],'order_price'=>$order_price]);
            }else{
                //订单商品表不存在此商品(新增)
                $insert = [
                    'order_id' => $params['order_id'],
                    'goods_id' => $params['goods_id'],
                    'needqty' => $params['order_count'],
                    'remark' => $params['remark'],
                    'goods_name' => $goods['goods_name'],
                    'spec' => $goods['spec'],
                    'unit' => $goods['unit'],
                    'price' => $price,
                    'order_price' => $price * $params['order_count'],
                    'goods_sn' => $goods['goods_sn'],
                    'status' => 0,
                    'back_price' => 0,
                    'send_price' => 0,
                    'cate_id' => $goods['cate_id'],
                    'scate_id' => $goods['scate_id'],
                    'cate_name' => DB::name('goodscategory')->find($goods['cate_id'])['category_name'],
                    'scate_name' => DB::name('goodscategory')->find($goods['scate_id'])['category_name']
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

    //这个收货数量如果更改  变不变订单状态
    public function ajax_edit()
    {
        $params = $this->request->param();
        $order_goods = DB::name('order_goods')->where(['id'=>$params['id']])->find();
        $order_id = $order_goods['order_id'];
//        if($order_goods['status'] != 0){
//            $this->error('此条详情不能修改');
//        }
        if(!is_numeric($params['order_count'])||!is_numeric($params['price'])||!is_numeric($params['sendqty'])){
            $this->error('非法字符');
        }
        if($params['sendqty'] > 0){
            $status = 1;
            $order_price = $params['price'] * $params['order_count'];
            $send_price = $params['price'] * $params['sendqty'];
        }else{
            $status = 0;
            $order_price = $params['price'] * $params['order_count'];
            $send_price = 0;
        }
        $update = [
            'needqty' => $params['order_count'],
            'remark' => $params['remark'],
            'sendqty' => $params['sendqty'],
            'price' => $params['price'],
            'order_price' => $order_price,
            'status' => $status,
            'send_price' => $send_price
        ];
        DB::name('order_goods')->where(['id'=>$params['id']])->update($update);
        $goods = DB::name('order_goods')
            ->where(['order_id'=>$order_id])
            ->select();

        $order_amount = 0;
        foreach($goods as $key => $value){
            $order_amount += $value['price'] * $value['needqty'];

        }
        $result = DB::name('order')
            ->where(['id'=>$order_id])
            ->update(['order_amount'=>$order_amount]);
        if($result !== false){
            $this->success('编辑完成','',['order_id'=>$order_id]);
        }else{
            $this->error('网络错误');
        }

    }

    /*
     * next2修改送货日期
     * */
    public function ajax_time()
    {
        $params = $this->request->param();
        $send_time = strtotime($params['send_time']);
        $order = DB::name('order')->where(['id'=>$params['order_id']])->find();
        if($order['status'] != "0"){
            $this->error('该订单状态不允许修改');
        }
        $result = DB::name('order')
            ->where(['id'=>$params['order_id']])
            ->update(['sendtime'=>$send_time]);
        if($result !== false){
            $this->success('送货日期修改完成');
        }else{
            $this->error('网络错误');
        }

    }

    /*
     * ajax删除next2中行
     * */
    public function ajax_del()
    {
        $params = $this->request->param();

        $order_goods = DB::name('order_goods')->where(['id'=>$params['id']])->find();
        $count = DB::name('order_goods')->where(['order_id'=>$order_goods['order_id']])->count();
        if($count <= 1){
            $this->error('不得小于一条');
        }
        DB::name('order_goods')->where(['id'=>$params['id']])->delete();
        $goods = DB::name('order_goods')
            ->where(['order_id'=>$order_goods['order_id']])
            ->select();

        $order_amount = 0;
        foreach($goods as $key => $value){
            $order_amount += $value['price'] * $value['needqty'];

        }
        $result = DB::name('order')
            ->where(['id'=>$order_goods['order_id']])
            ->update(['order_amount'=>$order_amount]);
        if($result !== false){
            $this->success('删除完成');
        }else{
            $this->error('网络错误');
        }
    }


    /*
     * 需要先安卓端回填goods_order中sendqty发货数量 send_price收货金额 status 1已收货
     * 确认订单
     * 如果订单内商品  is_stock==1 存入到stock表
     * */
    public function confirm_order()
    {
        $order_id = $this->request->param('ids');
        $order_goods = DB::name('order_goods')->where(['order_id'=>$order_id])->select();
        $result = DB::name('order_goods')
            ->field('t1.goods_id,t2.goods_name,t1.sendqty,t1.price,t1.status')
            ->alias('t1')
            ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
            ->where(['t1.order_id'=>$order_id])
            ->where(['t2.is_stock'=>"1"])
//                ->group('t1.goods_id')
            ->select();

		$results = DB::name('order_goods')
            ->field('t1.goods_id,t2.goods_name,t1.sendqty,t1.price,t1.status')
            ->alias('t1')
            ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
            ->where(['t1.order_id'=>$order_id])
            // ->where(['t2.is_stock'=>"1"])
//                ->group('t1.goods_id')
            ->select();

        foreach($results as $k => $v) {
            if ($v['status'] != "1") {
                $this->error('存在未完成收货,不允许修改状态');
            }
        }
        foreach($result as $k => $v) {


            $stock = DB::name('stock')->where(['goods_id'=>$v['goods_id']])->find();
            if($stock){
                $update = [
                    'stock_number' => $stock['stock_number'] + $v['sendqty'],
                    'unit_price' => (($stock['unit_price'] + $stock['stock_number']) + ($v['sendqty']*$v['price'])) / ($stock['stock_number'] + $v['sendqty']),
                ];
                DB::name('stock')->where(['goods_id'=>$stock['goods_id']])->update($update);
            }else{
                $insert = [
                    'goods_id' => $v['goods_id'],
                    'unit_price' => $v['price'],
                    'stock_number' => $v['sendqty']
                ];
                DB::name('stock')->insert($insert);
            }

        }




        $result = DB::name('order')
            ->where(['id'=>$order_id])
            ->update(['status'=>"1"]);
        if($result !== false){
            $this->success('已确认');
        }else{
            $this->error('网络错误');
        }
    }

    /*
     * 取消订单
     * */
    public function cancel_order()
    {
        $order_id = $this->request->param('ids');
        $result = DB::name('order')
            ->where(['id'=>$order_id])
            ->update(['status'=>'2']);
        DB::name('order_goods')
            ->where(['order_id'=>$order_id])
            ->update(['status'=>2]);
        if($result !== false){
            $this->success('已取消');
        }else{
            $this->error('网络错误');
        }
    }




    /*
     *  导出excel
     * */
    public function exportOrderExcel($data)
    {

        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename="."订单导出".".xls");
        $head = ['序号','一级分类','二级分类','商品编号','商品名称','规格','单位','下单数量','收货数量','单价','订单金额','收货金额','备注'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $data = json_decode($data,true);
        $info = $this->model->getOrderInfo($data);
//        $sheet->fromArray(['客户订单'],null,'G1');
        $sheet->fromArray(['客户订单'],null,'G1');
//        $sheet->fromArray($data,null,'A1');
        $top = 2;//第一行为1  +  G1
        $next = 0;//上边距初始值
        for($a=1;$a<=count($info);$a++){
            $top_margin = count($info[$a-1]);//上间距
            if($a == 1){
                $sheet->fromArray(['收货部门:'.$data[$a-1]['department']['name']],null,'A3');
                $sheet->fromArray(['下单时间:'.date('Y-m-d H:i:s',$data[$a-1]['createtime'])],null,'F3');
                $sheet->fromArray(['送货时间:'.date('Y-m-d H:i:s',$data[$a-1]['sendtime'])],null,'I3');
                $sheet->fromArray(['供应商名称:'.$data[$a-1]['supplier']['supplier_name']],null,'A4');
                $sheet->fromArray(['联系人:'.$data[$a-1]['supplier']['linkman']],null,'F4');
                $sheet->fromArray(['联系电话:'.$data[$a-1]['supplier']['mobile']],null,'I4');
                $sheet->fromArray($head,null,'A6');
                $sheet->fromArray($info[$a-1], "0", 'A7');
                $next = 6+$top_margin+1+1+2;//11 +2是为了美观  与逻辑无关
                $sheet->fromArray(['合计:'],null,'J'.(7+$top_margin));
                $sheet->fromArray([$data[$a-1]['order_amount']],"0",'K'.(7+$top_margin));
            }else{
                $sheet->fromArray(['收货部门:'.$data[$a-1]['department']['name']],null,'A'.($next+1));
                $sheet->fromArray(['下单时间:'.date('Y-m-d H:i:s',$data[$a-1]['createtime'])],null,'F'.($next+1));
                $sheet->fromArray(['送货时间:'.date('Y-m-d H:i:s',$data[$a-1]['sendtime'])],null,'I'.($next+1));
                $sheet->fromArray(['供应商名称:'.$data[$a-1]['supplier']['supplier_name']],null,'A'.($next+1+1));
                $sheet->fromArray(['联系人:'.$data[$a-1]['supplier']['linkman']],null,'F'.($next+1+1));
                $sheet->fromArray(['联系电话:'.$data[$a-1]['supplier']['mobile']],null,'I'.($next+1+1));
                $sheet->fromArray($head,null,'A'.($next+4));
                $sheet->fromArray($info[$a-1], "0", 'A'.($next+5));
                $sheet->fromArray(['合计:'],null,'J'.($next+4+$top_margin+1));
                $sheet->fromArray([$data[$a-1]['order_amount']],"0",'K'.($next+4+$top_margin+1));

                $next += 4+$top_margin+1+1+2;
            }
        }
        $writer = IOFactory::createWriter($spreadsheet, "Xls");
        ob_end_clean();//解决乱码
        $writer->save("php://output");
    }

    /*
     * 打印数据
     * */
    public function pr_order()
    {
        $params = $this->request->param();
        $order = DB::name('order')->where(['order_sn'=>$params['id']])->find();
        $start_time = strtotime(date('Y-m-d',$order['createtime']));

        $end_time = $order['createtime'];
        $where['createtime'] =  ['between time', [$start_time, $end_time]];

        $count = DB::name('order')->where($where)->count();

        $supplier = DB::name('supplier')->where(['id'=>$order['supplier_id']])->find();
        $data['department_name'] = DB::name('department')->where(['id'=>$order['department_id']])->value('name');
        $data['createtime'] = date('Y-m-d',$order['createtime']);
        $data['supplier_name'] = $supplier['supplier_name'];
        $order_good = DB::name('order_goods')->where(['order_id'=>$order['id']])->find();
//        $data['cate_name'] = $order_good['cate_name'];
        $data['cate_name'] = $order['cate_name'];
        $data['order_sn'] = "WJ-" . $data['createtime'] . "-" . $count;
        $data['amount'] = DB::name('order_goods')->where(['order_id'=>$order['id']])->sum('send_price');
        $data['cn_amount'] = num_to_rmb( $data['amount']);
        $data['info'] = DB::name('order_goods')
            ->field('goods_name,unit,needqty,sendqty,price,send_price')
            ->where(['order_id'=>$order['id']])
            ->select();
        foreach($data['info'] as $key => &$value){
            if(is_null($value['sendqty'])){
                $value['sendqty'] = "0.00";
            }
        }
        $this->success('','',$data);
    }

    public function cate_list()
    {
        $list = DB::name('goodscategory')->field('id,category_name as name')->where(['status'=>'1','pid'=>0])->select();

        return json(['list'=>$list,'total'=>count($list)]);
    }
    public function daoru()
    {
        if ($this->request->isAjax()) {
//            $params = $this->request->param();
//            $send_time = strtotime($params['sendtime']);
//
//            $send_time = strtotime()
            $params = $this->request->param();
//            halt($params);
            $arr['send_time'] = strtotime($params['row']['sendtime']);
//            halt($params);
            $arr['department_id'] = $params['row']['department_id'];
            $arr['supplier_id'] = $params['row']['supplier_id'];
            $arr['cate_id'] = $params['row']['cate_id'];
            $arr['excel_path'] = $_SERVER['DOCUMENT_ROOT'] . $params['row']['client_path'];
            $data = json_encode($arr, JSON_UNESCAPED_UNICODE);
            $result = $this->importExecl($arr['excel_path']);
//            halt($result);
            $goods_names = DB::name('supplier_goods')
                ->alias('t1')
                ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
                ->where(['t1.supplier_id'=>$arr['supplier_id']])
//                ->where(['t2.cate_id'=>$arr['cate_id']])
                ->column('t2.goods_name');
//halt($goods_names);
            unset($result[1]);//删除标题行

            //判断该供应商是否已经维护该商品价格
            foreach($result as $key => $value){
                if(!in_array($value["B"],$goods_names)){
                    $this->error($value["B"]."-->尚未维护价格");
                }
            }

            //创建订单
            $order_insert = [
                'order_sn' => date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'department_id' => $arr['department_id'],
                'supplier_id' => $arr['supplier_id'],
                'order_amount' => 0,//order_goods表插入完成后需要修改
                'createtime' => time(),
                'sendtime' => $arr['send_time'],
                'status' => "0",
                'cate_name' => DB::name('goodscategory')->where(['id'=>$arr['cate_id']])->value('category_name'),
            ];
            $order_id = DB::name('order')->insertGetId($order_insert);
            $amount = 0;
            foreach($result as $k => $v){
                $goods = DB::name('goods')->where(['goods_name'=>$v["B"]])->find();

                $supplier_goods = DB::name('supplier_goods')->where(['goods_id'=>$goods['id']])->find();

                $insert = [
                    'order_id' => $order_id,
                    'goods_id' => $goods['id'],
                    'needqty' => $v["E"],
                    'remark' => $v["F"],
                    'goods_name' => $goods['goods_name'],
                    'spec' => $goods['spec'],
                    'unit' => $goods['unit'],
                    'price' => $supplier_goods['price'],
                    'order_price' => $supplier_goods['price'] * $v["E"],
                    'goods_sn' => $goods['goods_sn'],
                    'status' => 0,
                    'back_price' => 0,
                    'send_price' => 0,
                    'cate_id' => $arr['cate_id'],
                    'scate_id' => $goods['scate_id'],
                    'cate_name' => DB::name('goodscategory')->find($goods['cate_id'])['category_name'],
                    'scate_name' => DB::name('goodscategory')->find($goods['scate_id'])['category_name']
                ];
                DB::name('order_goods')->insert($insert);
                $amount += $insert['order_price'];
            }
            $res = DB::name('order')->where(['id'=>$order_id])->update(['order_amount'=>$amount]);
            $this->success('导入完成');
        }else{
            return $this->view->fetch();
        }

    }
    public function importExecl($filePath = '',$sheet = 0,$columnCnt = 0, &$options = [])
    {
        try {
            /* 转码 */
            $filePath = iconv("utf-8", "gb2312", $filePath);

            if (empty($filePath) or !file_exists($filePath)) {
//                throw new \Exception('文件不存在!');
                $this->error('文件不存在!');
            }

            /** @var Xlsx $objRead */
            $objRead = IOFactory::createReader('Xlsx');

            if (!$objRead->canRead($filePath)) {
                /** @var Xls $objRead */
                $objRead = IOFactory::createReader('Xls');

                if (!$objRead->canRead($filePath)) {
//                    throw new \Exception('只支持导入Excel文件！');
                    $this->error('只支持导入Excel文件！');
                }
            }

            /* 如果不需要获取特殊操作，则只读内容，可以大幅度提升读取Excel效率 */
            empty($options) && $objRead->setReadDataOnly(true);
            /* 建立excel对象 */
            $obj = $objRead->load($filePath);
            /* 获取指定的sheet表 */
            $currSheet = $obj->getSheet($sheet);

            if (isset($options['mergeCells'])) {
                /* 读取合并行列 */
                $options['mergeCells'] = $currSheet->getMergeCells();
            }

            if (0 == $columnCnt) {
                /* 取得最大的列号 */
                $columnH = $currSheet->getHighestColumn();
                /* 兼容原逻辑，循环时使用的是小于等于 */
                $columnCnt = Coordinate::columnIndexFromString($columnH);
            }

            /* 获取总行数 */
            $rowCnt = $currSheet->getHighestRow();
            $data   = [];

            /* 读取内容 */
            for ($_row = 1; $_row <= $rowCnt; $_row++) {
                $isNull = true;

                for ($_column = 1; $_column <= $columnCnt; $_column++) {
                    $cellName = Coordinate::stringFromColumnIndex($_column);
                    $cellId   = $cellName . $_row;
                    $cell     = $currSheet->getCell($cellId);

                    if (isset($options['format'])) {
                        /* 获取格式 */
                        $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
                        /* 记录格式 */
                        $options['format'][$_row][$cellName] = $format;
                    }

                    if (isset($options['formula'])) {
                        /* 获取公式，公式均为=号开头数据 */
                        $formula = $currSheet->getCell($cellId)->getValue();

                        if (0 === strpos($formula, '=')) {
                            $options['formula'][$cellName . $_row] = $formula;
                        }
                    }

                    if (isset($format) && 'm/d/yyyy' == $format) {
                        /* 日期格式翻转处理 */
                        $cell->getStyle()->getNumberFormat()->setFormatCode('yyyy/mm/dd');
                    }

                    $data[$_row][$cellName] = trim($currSheet->getCell($cellId)->getFormattedValue());

                    if (!empty($data[$_row][$cellName])) {
                        $isNull = false;
                    }
                }

                /* 判断是否整行数据为空，是的话删除该行数据 */
                if ($isNull) {
                    unset($data[$_row]);
                }
            }

            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }


}
