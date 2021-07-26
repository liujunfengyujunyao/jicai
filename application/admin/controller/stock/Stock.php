<?php

namespace app\admin\controller\stock;

use app\common\controller\Backend;
use think\Db;

/**
 * 库存管理
 *
 * @icon fa fa-circle-o
 */
class Stock extends Backend
{
    
    /**
     * Stock模型对象
     * @var \app\admin\model\stock\Stock
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\stock\Stock;

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
            $filter = $this->request->get("filter",'');
            $filter = json_decode($filter,true);


            $list = DB::name('stock')
                ->field('t1.id,t1.goods_id,t2.goods_sn,t2.goods_name,t2.spec,t2.unit,t1.unit_price,t1.stock_number,t2.cate_id,t2.scate_id')
                ->alias('t1')
                ->join('__GOODS__ t2','t1.goods_id=t2.id','LEFT')
                ->limit($offset, $limit)
                ->where($where)
                ->select();
            foreach($list as $key =>&$value){
                
                @$value['cate_name'] = DB::name('goodscategory')->find($value['cate_id'])['category_name'];
                @$value['scate_name'] = DB::name('goodscategory')->find($value['scate_id'])['category_name'];
                
                
                $value['t2.goods_name'] = $value['goods_name'];

            }

//            foreach($list as $row){
//                $row->visible(['t1.id,t1.goods_id,t2.goods_sn,t2.goods_name as t2.goods_name,t2.spec,t2.unit,t1.unit_price,t1.stock_number,t2.cate_id,t2.scate_id']);
//            }

            $result = array("total" => count($list), "rows" => $list);

            return json($result);


        }
        return $this->view->fetch();
    }
}
