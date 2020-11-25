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



}
