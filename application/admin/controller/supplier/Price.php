<?php

namespace app\admin\controller\supplier;

use app\common\controller\Backend;
use think\Db;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Price extends Backend
{
    
    /**
     * Price模型对象
     * @var \app\admin\model\supplier\Price
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\supplier\Price;
        $this->view->assign("isStockList", $this->model->getIsStockList());
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
        $this->relationSearch = false;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);

        $supplier_id = $this->request->get('ids');
        $this->view->assign("supplier_id",$supplier_id);
        if ($this->request->isAjax())
        {

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

//            foreach ($list as $row) {
//                $row->visible(['id','goods_name','goods_sn','spec','unit','cate_id','scate_id','is_stock','status']);
//
//            }
            //此供应商编辑过的商品
            $supplier_goods_ids = DB::name('supplier_goods')
                ->where(['supplier_id'=>$supplier_id])
                ->column('goods_id');

            foreach($list as $key => &$value){
                $value['cate'] = DB::name('goodscategory')->where(['id'=>$value['cate_id']])->value('category_name');
                $value['scate'] = DB::name('goodscategory')->where(['id'=>$value['scate_id']])->value('category_name');
                //将被编辑过的商品价格修改
                if(in_array($value['id'],$supplier_goods_ids)){
                    $value['price'] = DB::name('supplier_goods')
                        ->where(['goods_id'=>$value['id'],'supplier_id'=>$supplier_id])
                        ->value('price');

                }
            }
            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /*
     * 获取不到ID
     * 先用goods_sn代替
     * */
    public function update_price()
    {
        $params = $this->request->param();
        $supplier_goods = DB::name('supplier_goods')
            ->where(['goods_id'=>$params['goods_sn'],'supplier_id'=>$params['supplier_id']])
            ->find();

        if($supplier_goods){
            $result = DB::name('supplier_goods')
                ->where(['id'=>$supplier_goods['id'],'supplier_id'=>$params['supplier_id']])
                ->update(['price'=>$params['price']]);

        }elseif(!$supplier_goods && !$params['price']){
            $result = true;

        }else{
            $insert = [
                'supplier_id' => $params['supplier_id'],
                'goods_id' => $params['goods_sn'],
                'price' => $params['price'],
                'updatetime' => time()
            ];
            $result = DB::name('supplier_goods')->insert($insert);
        }
        if($result !== false){
            $this->success('编辑完成');
        }else{
            $this->error('网络错误');
        }

    }
}
