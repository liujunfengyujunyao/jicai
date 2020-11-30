<?php

namespace app\admin\controller\stock;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 盘点管理管理
 *
 * @icon fa fa-check
 */
class Check extends Backend
{
    
    /**
     * Check模型对象
     * @var \app\admin\model\stock\Check
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\stock\Check;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
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

            list($where, $sort, $order, $offset, $limit) = $this->buildparams('nickname,fa_check.createtime');

//            halt($where);
//            $total = $this->model
//                ->field("fa_check.*,fa_admin.nickname,fa_admin.id as admin_id")
//                ->join('__ADMIN__','fa_check.check_admin=fa_admin.id','LEFT')
//                ->where($where)
//                ->order($sort, $order)
//                ->count();

                $list = DB::name('check')
                ->field("fa_check.*,fa_admin.nickname")
                ->join('__ADMIN__','fa_check.check_admin=fa_admin.id','LEFT')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

//            $list = collection($list)->toArray();

            foreach($list as $key => &$value){
                $value['fa_check.status'] = $value['status'];
                $value['fa_check.createtime'] = $value['createtime'];
                $value['audit_admin'] = DB::name('admin')->where(['id'=>$value['audit_admin']])->value('nickname');
            }
            $result = array("total" => count($list), "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {

        $params = $this->request->param();

        if(!empty($params['check_id'])){
            $this->view->assign("check_id",$params['check_id']);
        }else{
            $this->view->assign("check_id",0);
        }

        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            $params = $this->request->param();

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
//            $total = $this->model
//                ->where($where)
//                ->order($sort, $order)
//                ->count();
            $list = DB::name('stock')
                ->field("fa_stock.id,fa_goods.goods_sn,fa_goods.goods_name,fa_goods.spec,fa_goods.unit,fa_stock.unit_price,fa_stock.stock_number")
                ->join("__GOODS__","fa_stock.goods_id=fa_goods.id",'LEFT')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $list = collection($list)->toArray();
            foreach($list as $key => &$value){
                $value['fa_goods.goods_name'] = $value['goods_name'];
                if(!empty($params['check_id'])){
                    //从编辑页面进入的add页面
                    $check_goods = DB::name('check_goods')
                        ->where(['stock_id'=>$value['id'],'check_id'=>$params['check_id']])
                        ->find();
                    $value['check_number'] = $check_goods['check_number'];
                    $value['remark'] = $check_goods['remark'];
                }
            }



            $result = array("total" => count($list), "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        $this->view->assign("check_id",$row['id']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $params = $this->request->param();
            $check_id = json_decode($params['filter'],true)['check_id'];
            $list = DB::name('check_goods')
                ->field('t1.*,t2.status')
                ->alias('t1')
                ->join('__CHECK__ t2','t1.check_id=t2.id','LEFT')
                ->where(['t1.check_id'=>$check_id])
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => count($list), "rows" => $list);
            return json($result);
        }
        $check = DB::name('check')->where(['id'=>$row['id']])->find();
        if($check['status'] == "0"){
            $status = '待确认';
        }elseif($check['status'] == "1"){
            $status = "已确认";
        }else{
            $status = "已取消";
        }
        $check_admin = DB::name('admin')->where(['id'=>$check['check_admin']])->value('nickname');
        $this->view->assign('status',$status);
        $this->view->assign('check_admin',$check_admin);
        $this->view->assign('createtime',date('Y-m-d H:i:s',$check['createtime']));
        $this->assignConfig('check_id',$row['id']);//传给queryParams
        return $this->view->fetch();
    }



    public function ajax_add()
    {
        $params = $this->request->param();

        $stock_id = $params['id'];
        $stock = DB::name('stock')->find($stock_id);
        $goods = DB::name('goods')->where(['id'=>$stock['goods_id']])->find();
        $is_delivery = DB::name('delivery_goods')
            ->alias('t1')
            ->join('__DELIVERY__ t2','t1.delivery_id=t2.id','LEFT')
            ->where(['t2.status'=>"0"])
            ->column('t1.goods_id');
        if(in_array($stock['goods_id'],$is_delivery)){
            $this->error('存在未处理的领料单里有该商品');
        }
        $amount = $params['check_number'] * $stock['unit_price'];

        if($params['check_id'] == '0'){

            //新增
            $insert = [
                'check_admin' => $this->auth->id,
                'amount' => $amount,
                'status' => "0",
                'count' => $params['check_number'],
                'createtime' => time(),
            ];
            $check_id = DB::name('check')->insertGetId($insert);
            $check_goods = [
                'stock_id' => $params['id'],
                'goods_id' => $stock['goods_id'],
                'goods_name' => $goods['goods_name'],
                'goods_sn' => $goods['goods_sn'],
                'spec' => $goods['spec'],
                'unit' => $goods['unit'],
                'unit_price' => $stock['unit_price'],
                'stock_number' => $stock['stock_number'],
                'check_number' => $params['check_number'],
                'remark' => $params['remark'],
                'check_id' => $check_id,
                'createtime' => time()
            ];
            $result = DB::name('check_goods')->insert($check_goods);
        }else{
            //修改
            $is_isset = DB::name('check_goods')
                ->where(['check_id'=>$params['check_id'],'stock_id'=>$params['id']])
                ->find();

            $check_id = $params['check_id'];
            if($is_isset){
                //领料商品表存在此商品(修改)
                $result = DB::name('check_goods')
                    ->where(['id'=>$is_isset['id']])
                    ->update(['check_number'=>$params['check_number'],'remark'=>$params['remark'],'createtime'=>time()]);
            }else{

//              领料商品表不存在此商品(新增)
                $check_goods = [
                    'stock_id' => $params['id'],
                    'goods_id' => $stock['goods_id'],
                    'goods_name' => $goods['goods_name'],
                    'goods_sn' => $goods['goods_sn'],
                    'spec' => $goods['spec'],
                    'unit' => $goods['unit'],
                    'unit_price' => $stock['unit_price'],
                    'stock_number' => $stock['stock_number'],
                    'check_number' => $params['check_number'],
                    'remark' => $params['remark'],
                    'check_id' => $params['check_id'],
                    'createtime' => time()
                ];

                $result = DB::name('check_goods')->insert($check_goods);
            }
//            $stock = DB::name('check_goods')
//                ->where(['check_id'=>$params['id']])
//                ->select();
//            $amount = 0;
//            foreach($stock as $key => $value){
//                $unit_price = DB::name('stock')
//                    ->where(['goods_id'=>$value['goods_id']])
//                    ->value('unit_price');
//                $amount += $unit_price * $value['check_number'];
//            }
//            DB::name('check')
//                ->where(['id'=>$params['check_id']])
//                ->update(['amount'=>$amount]);
            $check_goods = DB::name('check_goods')
                ->where(['check_id'=>$params['check_id']])
                ->select();

            $amount = 0;
            foreach($check_goods as $key => $value){
                $amount += $value['unit_price'] * $value['check_number'];
            }

            $result = DB::name('check')
                ->where(['id'=>$params['check_id']])
                ->update(['amount'=>$amount,'count'=>count($check_goods)]);

        }
        if($result !== false){
            $this->success('操作成功','',['check_id'=>$check_id]);
        }else{
            $this->error('网络错误');
        }
    }

    /*
     * 编辑盘点明细
     * */
    public function ajax_edit()
    {
        $params = $this->request->param();
        $update = [
            'remark' => $params['remark'],
            'check_number' => $params['check_number'],
        ];
        $check_goods = DB::name('check_goods')->where(['id'=>$params['id']])->find();
        $result = DB::name('check_goods')
            ->where(['id'=>$params['id']])
            ->update($update);


        $data = DB::name('check_goods')
            ->where(['check_id'=>$check_goods['check_id']])
            ->select();

        $amount = 0;
        foreach($data as $key => $value){
            $amount += $value['unit_price'] * $value['check_number'];
        }


        $check_update = [
            'amount' => $amount,
            'count' => count($data),
            'createtime' => time()
        ];
        DB::name('check')->where(['id'=>$check_goods['check_id']])->update($check_update);
        if($result !== false){
            $this->success('编辑完成');
        }else{
            $this->error('网络错误');
        }
    }

    /*
     * 删除盘点明细
     * 全部相同check_id的明细被删除是否删除check
     * */
    public function ajax_del()
    {
        $params = $this->request->param();
        $check_goods = DB::name('check_goods')->where(['id'=>$params['id']])->find();
        $count = DB::name('check_goods')->where(['check_id'=>$check_goods['check_id']])->count();
        if($count<=1){
            $this->error('不得少于一条');
        }
        $result = DB::name('check_goods')->where(['id'=>$params['id']])->delete();
        $data = DB::name('check_goods')
            ->where(['check_id'=>$check_goods['check_id']])
            ->select();
        $amount = 0;
        foreach($data as $key => $value){
            $amount += $value['unit_price'] * $value['check_number'];
        }


        $check_update = [
            'amount' => $amount,
            'count' => count($data),
            'createtime' => time()
        ];
        DB::name('check')->where(['id'=>$check_goods['check_id']])->update($check_update);

        if($result !== false){
            $this->success('删除完成');
        }else{
            $this->error('删除失败');
        }
    }

    /*
     *  取消
     * */
    public function reject()
    {
        $check_id = $this->request->param('ids');
        $audit_admin = $this->auth->id;
        $result = DB::name('check')->where(['id'=>$check_id])->update(['status'=>"2","audittime"=>time(),'audit_admin'=>$audit_admin]);
        if($result !== false){
            $this->success('已取消');
        }else{
            $this->error('网络错误');
        }
    }

    /*
     * 确认
     * */
    public function through()
    {
        $check_id = $this->request->param('ids');
        $check_goods = DB::name('check_goods')
            ->where(['check_id'=>$check_id])
            ->select();
        foreach($check_goods as $key => $value){
            $result = DB::name('stock')
                ->where(['id'=>$value['stock_id']])
                ->update(['stock_number'=>$value['check_number']]);
        }
        $audit_admin = $this->auth->id;
        $result = DB::name('check')
            ->where(['id'=>$check_id])
            ->update(['status'=>"1",'audittime'=>time(),'audit_admin'=>$audit_admin]);
        if($result !== false){
            $this->success('操作完成');
        }else{
            $this->error('网络错误');
        }

    }
}
