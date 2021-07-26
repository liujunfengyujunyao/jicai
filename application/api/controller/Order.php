<?php

namespace app\api\controller;
set_time_limit(0);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods", "*");//允许任何method
header("Access-Control-Allow-Headers", "*");//允许任何自定义header
header("Access-Control-Allow-Credentials", "true");//允许跨域cookie
use app\common\controller\Api;
use think\Db;
use think\Validate;

/**
 * 会员接口
 */
class Order extends Api
{
    protected $noNeedLogin = ['test','test2','consignee_sign','delivery_sign'];//不需要登录
    protected $noNeedRight = '*';//不需要鉴权

    public function _initialize()
    {
        parent::_initialize();
        $user = $this->auth->getUserinfo();
        //username
//        halt($user);
        if($user['id'] != 1){
            $uid = DB::name('admin')
                ->where(['username'=>$user['username']])
                ->value('id');

            $group_ids = DB::name('auth_group_access')->where(['uid'=>$uid])->column('group_id');
            $this->department_ids = implode(',',array_filter(DB::name('auth_group')->where("id","in",$group_ids)->column('department_ids')));
            $this->supplier_ids = implode(',',array_filter(DB::name('auth_group')->where("id","in",$group_ids)->column('supplier_ids')));

//            halt($this->department_ids);
        }else{
            $where1 = "1=1";
            $hwere2 = "1=1";
        }
    }

    /**
     * 会员中心
     */
    public function index()
    {
        $this->success('', ['welcome' => $this->auth->nickname]);
    }


    /*
     * 配置信息接口
     *
     * */
    public function static_file()
    {
        $user = $this->auth->getUserinfo();
        if($user['user_id'] != 1){
                $where1['id'] = ['in',$this->department_ids];
                $where2['id'] = ['in',$this->supplier_ids];
        }else{
                $where1 = "1=1";
                $where2 = "1=1";
        }
       
        
        // if($user['id'] != 1){
        //     $group_ids = DB::name('auth_group_access')->where(['uid'=>$user['user_id']])->column('group_id');
        //     $department_ids = implode(',',array_filter(DB::name('auth_group')->where("id","in",$group_ids)->column('department_ids')));
        //     $supplier_ids = implode(',',array_filter(DB::name('auth_group')->where("id","in",$group_ids)->column('supplier_ids')));
      
        //     $where1['id'] = ['in',$department_ids];
        //     $where2['id'] = ['in',$supplier_ids];
        // }else{
        //     $where1 = "1=1";
        //     $hwere2 = "1=1";
        // }
        // halt($group_ids);
        //部门列表
        $result['department_list'] = DB::name('department')->field('id as department_id,name as department_name')->where($where1)->where(['status'=>"1"])->select();
        //供应商列表
        $result['supplier_list'] = DB::name('supplier')->field('id as supplier_id,supplier_name')->where($where2)->where(['status'=>"1"])->select();

        $result['goods_category'] = DB::name('goodscategory')->field("id as cate_id,category_name")->where(['pid'=>0,'status'=>"1"])->select();
        foreach($result['goods_category'] as $key => &$value){
            $value['second_cate'] = DB::name('goodscategory')->field("id as scate_id,category_name")->where(['pid'=>$value['cate_id'],'status'=>"1"])->select();
        }

        $result['cate_name'] = DB::name('order')->where(['status'=>"0"])->column('cate_name');
        $result['cate_name'] = array_values(array_unique($result['cate_name']));
        $this->success('', $result);
    }


    /*
     * 收货管理
     * @send_time : 送货时间
     * @department_id : 部门ID
     * @supplier_id : 供货商ID
     * */
    public function order_list()
    {
        $params = $this->request->param();
        $start_time = strtotime(date('Y-m-d',time()));
        $end_time = strtotime(date('Y-m-d',time()))+60*60*24-1;
//        halt([$start_time,$end_time]);
        isset($params['send_time']) ? $where['sendtime'] = ['between',[strtotime($params['send_time']),strtotime($params['send_time'])+60*60*24-1]]:$where['sendtime'] = ['between',[$start_time, $end_time]];
        isset($params['department_id']) ? $where['department_id'] = $params['department_id'] : NULL;
        isset($params['supplier_id']) ? $where['supplier_id'] = $params['supplier_id'] : NULL;
        isset($params['cate_name']) ? $where['cate_name'] = $params['cate_name'] : NULL;
        // if($)
        //部门,供应商,送货时间(date),订单金额,商品总数,收货进度10/20
        $user = $this->auth->getUserinfo();
        if($user['user_id'] != 1){
                $where1['supplier_id'] = ['in',$this->supplier_ids];
                $where1['department_id'] = ['in',$this->department_ids];
        }else{
                $where1 = "1=1";
        }
       
        $result = DB::name('order')
            ->field('id as order_id,department_id,supplier_id,sendtime,order_amount,cate_name')
            ->where(['status'=>"0"])
            ->where($where)
            ->where($where1)
            ->select();

        foreach($result as $key => &$value){
//            $value['goods_count'] = DB::name('order_goods')->field('sum(needqty) as good_count')->where(['order_id'=>$value['order_id']])->select()[0]['good_count'];
            $value['goods_count'] = DB::name('order_goods')->where(['order_id'=>$value['order_id']])->count();
//            $value['progress'] = DB::name('order_goods')->field('sum(sendqty) as progess')->where(['order_id'=>$value['order_id']])->select()[0]['progess'];
            $value['progress'] = DB::name('order_goods')->where(['order_id'=>$value['order_id'],'status'=>"1"])->count();
            if(is_null($value['progress'])){
                $value['progress'] = "0";
            }
            $value['department_name'] = DB::name('department')->find($value['department_id'])['name'];
            $value['supplier_name'] = DB::name('supplier')->find($value['supplier_id'])['supplier_name'];
            $value['sendtime'] = date('Y-m-d',$value['sendtime']);
            unset($value['department_id']);
            unset($value['supplier_id']);

        }
        $this->success('',$result);
    }

    /*
     * 订单详情
     * */
    public function order_detail()
    {
        $order_id = $this->request->param('order_id');

        $result['info'] = DB::name('order')
            ->alias('t1')
            ->field('t2.name as department_name,t3.supplier_name,t3.linkman,t3.mobile,t1.sendtime,t1.status,t1.count_sn,t1.consignee_sign,delivery_sign')

            ->join('__DEPARTMENT__ t2','t1.department_id=t2.id','LEFT')
            ->join('__SUPPLIER__ t3','t1.supplier_id=t3.id','LEFT')
            ->where(['t1.id'=>$order_id])
            ->find();
        if($result['info']['status'] == "0"){
            $result['info']['status'] = "待收货";
        }elseif($result['info']['status'] == "1"){
            $result['info']['status'] = "已收货";
        }else{
            $result['info']['status']  = "已取消";
        }
//        foreach($result['info'] as $key => &$value){
//            if($value['status'] == "0"){
//                $value['status'] = '待收货';
//            }elseif($value['status'] == "1"){
//                $value['status'] = "已收货";
//            }else{
//                $value['status'] = "已取消";
//            }
//        }
        $result['list'] = DB::name('order_goods')
            ->field('id,goods_sn,goods_name,spec,unit,price,needqty,order_price,sendqty,send_price,remark')
            ->where(['order_id'=>$order_id])
            ->select();
        foreach($result['list'] as $k => &$v){
            if(is_null($v['sendqty'])) $v['sendqty'] = "0";
        }
        $this->success('',$result);
    }


    /*
     * 收货列表
     * */
    public function delivery_list()
    {
        $params = $this->request->param();
        $where = [
            't1.order_id' => $params['order_id'],
        ];
        isset($params['cate_id']) ? $where['t1.cate_id'] = $params['cate_id'] : NULL;
        isset($params['scate_id']) ? $where['t1.scate_id'] = $params['scate_id'] : NULL;
        isset($params['status']) ? $where['t1.status'] = $params['status'] : NULL;
        isset($params['packaging_type']) ? $where['t2.packaging_type'] = strval($params['packaging_type']) : NULL;
        $result = DB::name('order_goods')
            ->alias('t1')
            ->field('t1.id,t1.goods_name,t1.spec,t1.unit,t1.needqty,t1.sendqty,t1.status,t1.remark,t2.packaging_type,t2.cate_id,t2.scate_id')
            ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
            ->where($where)
            ->select();
        foreach($result as $k => &$v){
            if(is_null($v['sendqty'])) $v['sendqty'] = "0";
        }
        $this->success('',$result);
    }

    /*
     * 提交收货数量
     *
     * */
    public function delivery_number()
    {
//        $this->error(__('Please login first'), null, 401);
        $params = $this->request->param();
        $order_id = $params['id'];
        if(isset($params['remark']))  $update['remark'] = $params['remark'];
        $order_goods = DB::name('order_goods')->find($order_id);
//        if($order_goods['needqty'] < $params['sendqty']){
//            $this->error('提交数量大于订单数量');
//        }else{
            $update['sendqty'] = $params['sendqty'];
            $update['status'] = 1;
            $update['send_price'] = $params['sendqty'] * $order_goods['price'];
            $result = DB::name('order_goods')->where(['id'=>$order_id])->update($update);
//        }
        if($result !== false){
            $this->success('提交成功');
        }else{
            $this->error('网络错误');
        }

    }

 /*
     * 上传订单收货人签名
     * */
    public function consignee_sign()
    {
        
        $params = $this->request->param();
        if(!isset($params['order_id']) || is_null(request()->file('sign'))){
            $this->error('缺少参数');
        }
        $order_id = $params['order_id'];

        $file = request()->file('sign');

        $info = $file->move(ROOT_PATH.'public'.DS.'uploads');
        if(!$order_id){
            $this->error('缺少订单号');
        }
        
        if($info){
            $path = "http://" . $_SERVER['HTTP_HOST'] . DS . 'uploads' . DS . $info->getSaveName();
            $update = ['consignee_sign' => $path];
            DB::name('order')->where(['id'=>$order_id])->update($update);
            $this->success($path); 
        }else{
            $this->error($file->getError());
        }
    }
    /*
     * 上传订单送货人签名
     * */
    public function delivery_sign()
    {

        $params = $this->request->param();
        if(!isset($params['order_id']) || is_null(request()->file('sign'))){
            $this->error('缺少参数');
        }
        $order_id = $params['order_id'];

        $file = request()->file('sign');

        $info = $file->move(ROOT_PATH.'public'.DS.'uploads');
        if(!$order_id){
            $this->error('缺少订单号');
        }

        if($info){
            $path = "http://" . $_SERVER['HTTP_HOST'] . DS . 'uploads' . DS . $info->getSaveName();
            $update = ['delivery_sign' => $path];
            DB::name('order')->where(['id'=>$order_id])->update($update);
            $this->success($path);
        }else{
            $this->error($file->getError());
        }
    }
public function test()
    {
        ini_set('max_execution_time', 6000);
        $sendtime = [
//            '1609776000','1609948800','1610208000'
            '1609776000'
        ];
        $list = DB::name('order')
//            ->where(['sendtime'=>'1610208000'])
//            ->field('id')
            ->where('sendtime','in',$sendtime)
//            ->whereOr(['sendtime'=>'1609948800'])
//            ->whereOr(['sendtime'=>'1610208000'])
            ->column('id');
//        halt($list);
        $data = DB::name('order_goods')
            ->field('id,price,order_id,goods_id,order_price,needqty,sendqty')
            ->where('order_id','in',$list)
            ->select();
  $diff_order_id = [];
        $diff_order_goods_id = [];
        foreach($data as $key => $value){
            $supplier_id = DB::name('order')->where(['id'=>$value['order_id']])->value('supplier_id');
            $price = DB::name('supplier_goods')->where(['supplier_id'=>$supplier_id])->where(['goods_id'=>$value['goods_id']])->value('price');


            if($price != $value['price']){
                halt([$value['price'],$price,$value['id']]);
                array_push($diff_order_id,$value['order_id']);
                array_push($diff_order_goods_id,$value['id']);
                $update_price = (float)$price * $value['needqty'];
                $update_price2 = (float)$price * $value['sendqty'];

                DB::name('order_goods')->where(['id'=>$value['id']])->update(['order_price'=>$update_price,'send_price'=>$update_price2,'price'=>$price]);
            }
        }
halt(111);
        foreach($list as $k => $v){
            $order_amount = DB::name('order_goods')
                ->where(['order_id'=>$v])
                ->sum('order_price');
            
            DB::name('order')->where(['id'=>$v])->update(['order_amount'=>$order_amount]);
        }
        dump($diff_order_goods_id);

        halt($diff_order_id);
    }

    public function test2()
    {
        $data = 123456;
        echo md5(md5($data).null);
    }

}
