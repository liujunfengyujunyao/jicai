<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Statistics extends Backend
{
    protected $noNeedRight = ['department_list','supplier_list','exportOrderExcel'];
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
            $order_ids = DB::name('order')
                ->where($where)
                ->where('status','neq','2')
                ->column('id');
            $order_goods = DB::name('order_goods')
                ->field('goods_id,cate_name,scate_name,goods_sn,goods_name,spec,unit,sum(needqty) as needqty,sum(order_price) as order_price,sum(sendqty) as sendqty,sum(send_price) as send_price')
                ->where("order_id","in",$order_ids)
                ->group('goods_id')
//                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $count = DB::name('order_goods')
                ->field('goods_id,cate_name,scate_name,goods_sn,goods_name,spec,unit,sum(needqty) as needqty,sum(order_price) as order_price,sum(sendqty) as sendqty,sum(send_price) as send_price')
                ->where("order_id","in",$order_ids)
                ->group('goods_id')
//                ->order($sort, $order)

                ->count();
            foreach($order_goods as $key => &$value){

                if($value['sendqty']=='0.00' || is_null($value['sendqty'])){
                    $value['mean'] = 0;
                }else{
                    $value['mean'] = round($value['send_price'] / $value['sendqty'],2);
                }

            }
            $result = array("total" => $count, "rows" => $order_goods);

            return json($result);

        }
        return $this->view->fetch();
    }

    public function department_list()
    {
        $json = cache('department');
        if($json===false){
            $list = DB::name('department')->field('id,name')->where(['status'=>'1'])->select();
            $json = json($list);
            cache('department');
        }
        return $json;
    }

    public function supplier_list()
    {
        $json = cache('supplier');
        if($json===false){
            $list = DB::name('supplier')->field('id,supplier_name as name')->where(['status'=>'1'])->select();
            $json = json($list);
            cache('supplier');
        }
        return $json;
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
}
