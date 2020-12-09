<?php

namespace app\admin\controller\supplier;

use app\common\controller\Backend;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use think\Db;

/**
 * 供应商管理
 *
 * @icon fa fa-circle-o
 */
class Supplier extends Backend
{
    protected $noNeedRight = ['select_list'];
    /**
     * Supplier模型对象
     * @var \app\admin\model\supplier\Supplier
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\supplier\Supplier;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    public function select_list()
    {

        $list = DB::name('supplier')->field('id,supplier_name as name')->where(['status'=>1])->select();

        return json(['list'=>$list,'total'=>count($list)]);
    }

    /*
     * 价格导入
     * */
    public function daoru()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->param();
            $supplier_id = $params['row']['supplier_id'];
            $arr['excel_path'] = $_SERVER['DOCUMENT_ROOT'] . $params['row']['client_path'];
            $data = json_encode($arr, JSON_UNESCAPED_UNICODE);
            $result = $this->importExecl($arr['excel_path']);

            unset($result[1]);//删除标题行

            foreach($result as $k => $v){
                $isset = DB::name('goods')->where(['goods_name'=>trim($v['B'])])->find();
                if(!is_numeric($v["E"])){
                    $this->error('商品' . $v["B"] . "提供的价格有误-->" . $v["E"]);
                }
                if(!$isset){
                    $this->error('商品不存在-->' . $v["B"]);
                }
            }

            foreach($result as $key => $value){
                $goods = DB::name('goods')->where(['goods_name'=>trim($value['B'])])->find();
                $supplier_goods = DB::name('supplier_goods')->where(['supplier_id'=>$supplier_id,'goods_id'=>$goods['id']])->find();

                if($supplier_goods){

                    //存在,修改
                    $update = [
                        'price' => trim($value["E"]),
                        'updatetime' => time(),
                    ];

                    $res = DB::name('supplier_goods')->where(['id'=>$supplier_goods['id']])->update($update);
                }else{
                    //新增
                    $insert = [
                        'supplier_id' => $supplier_id,
                        'goods_id' => $goods['id'],
                        'price' => trim($value["E"]),
                        'updatetime' => time(),
                    ];
                    $res = DB::name('supplier_goods')->insert($insert);

                }

            }
            if($res !== false){
                $this->success('导入完成');
            }
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
