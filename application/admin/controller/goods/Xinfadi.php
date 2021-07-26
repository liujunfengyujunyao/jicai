<?php

namespace app\admin\controller\goods;

use app\common\controller\Backend;
use think\Db;

/**
 * 新发地菜品管理
 *
 * @icon fa fa-circle-o
 */
class Xinfadi extends Backend
{

    /**
     * Xinfadi模型对象
     * @var \app\admin\model\goods\Xinfadi
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\goods\Xinfadi;

    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    public static function getDbSxxdConnection()
    {
        return DB::connect('db_sxxd');
    }
    /**
     * 查看
     */
    public function index()
    {
//        $where['id'] = ['between','13423,13654'];
//        $data = self::getDbSxxdConnection()
//            ->table('goods')
//            ->where($where)
//            ->select();
////        die;
//
//        $res = DB::name('goodscategory')->select();
//
//        foreach($data as $key => $value){
//            $insert = [
//                'id' => $value['id'],
//                'goods_name' => $value['goods_name'],
//                'goods_sn' => $value['goods_sn'],
//                'spec' => $value['spec'],
//                'unit' => $value['unit'],
//                'cate_id' => $value['cate_id'],
//                'scate_id' => $value['scate_id'],
//                'is_stock' => '1',
//                'status' => '0',
//                'remark' => $value['remark'],
//                'createtime' => $value['update_time'],
//                'updatetime' => $value['update_time'],
//                'price' => $value['sale_price'],
//                'packaging_type' => strval($value['attr']-1)
//            ];
//            DB::name('goods')->insert($insert);
//
//        }
//        halt($data);


        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model
                ->with('goods')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with('goods')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
//halt($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function goods_list()
    {
        //当前是否为关联查询
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);

        $xinfadi_id = $this->request->get('ids');

        $this->view->assign("xinfadi_id",$xinfadi_id);
        if ($this->request->isAjax())
        {

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = DB::name('goods')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = DB::name('goods')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //匹配商品
    public function mate($ids)
    {
        $params = $this->request->get();
        $goods_id = $params['ids'];
        $xinfadi_id = $params['xinfadi_id'];
        $update = [
            'goods_id' => $goods_id,
            'status' => 1,
        ];
        $result = DB::name('goods_xinfadi')
            ->where(['id'=>$xinfadi_id])
            ->update($update);
        if($result !== false){
            $this->success();
        }else{
            $this->error();
        }
    }

    //取消匹配
    public function cancel_mate($ids)
    {
        $update = [
            'status' => 0,
            'goods_id' => null
        ];
        $result = DB::name('goods_xinfadi')
            ->where(['id'=>$ids])
            ->update($update);
        if($result !== false){
            $this->success();
        }else{
            $this->error();
        }
    }

}
