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
        if ($this->request->isAjax())
        {

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['department','supplier'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['department','supplier'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                
                $row->getRelation('department')->visible(['id','name']);
				$row->getRelation('supplier')->visible(['id','supplier_name','linkman','mobile']);
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
        if($this->request->isAjax()){
//            $params = $this->request->param();
//            $send_time = strtotime($params['sendtime']);
//
//            $send_time = strtotime()
            $params = $this->request->param();
            $arr['send_time'] = strtotime($params['row']['sendtime']);
//            halt($params);
            $arr['department_id'] = $params['row']['department_id'];
            $arr['supplier_id'] = $params['row']['supplier_id'];
            $data = json_encode($arr,JSON_UNESCAPED_UNICODE);

            $this->success('',url("order/order/next"),$data);
        }
//        if ($this->request->isPost()) {
//            halt(1231312312);
//            $params = $this->request->post("row/a");
//            if ($params) {
//                $params = $this->preExcludeFields($params);
//
//                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
//                    $params[$this->dataLimitField] = $this->auth->id;
//                }
//                $result = false;
//                Db::startTrans();
//                try {
//                    //是否采用模型验证
//                    if ($this->modelValidate) {
//                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
//                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
//                        $this->model->validateFailException(true)->validate($validate);
//                    }
//                    $result = $this->model->allowField(true)->save($params);
//                    Db::commit();
//                } catch (ValidateException $e) {
//                    Db::rollback();
//                    $this->error($e->getMessage());
//                } catch (PDOException $e) {
//                    Db::rollback();
//                    $this->error($e->getMessage());
//                } catch (Exception $e) {
//                    Db::rollback();
//                    $this->error($e->getMessage());
//                }
//                if ($result !== false) {
//                    $this->success();
//                } else {
//                    $this->error(__('No rows were inserted'));
//                }
//            }
//            $this->error(__('Parameter %s can not be empty', ''));
//        }
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
        if ($this->request->isAjax())
        {
            @$goods_name = json_decode($params['filter'],true)['goods_name'];
//            $goods_name ?$like = ['t2.goods_name','like', '%' . $goods_name . '%']:$like="1=1";
//            $like = $goods_name?['t2.goods_name'=>['like', $goods_name]]:'1=1';
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
                ->where(['t1.supplier_id'=>$params['supplier.id'],'t2.status'=>'1'])
                ->where($like)
                ->limit($offset, $limit)
                ->select();
            $total = DB::name('supplier_goods')
                ->alias('t1')
                ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
                ->where(['t1.supplier_id'=>$params['supplier.id'],'t2.status'=>'1'])
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
            }
            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    public function test()
    {
        halt(66666666666);
        return $this->view->fetch();
    }
}
