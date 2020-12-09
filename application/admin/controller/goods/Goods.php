<?php

namespace app\admin\controller\goods;

use app\common\controller\Backend;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use think\Db;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Goods extends Backend
{
    
    /**
     * Goods模型对象
     * @var \app\admin\model\goods\Goods
     */
    protected $model = null;
    protected $searchFields = 'goods_name,goods_sn';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\goods\Goods;
        $this->view->assign("isStockList", $this->model->getIsStockList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("packagingTypeList", $this->model->getPackagingTypeList());
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
                    ->with(['goodscategory'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['goodscategory'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            foreach($list as $key => &$value){
                $value['cate'] = DB::name('goodscategory')->where(['id'=>$value['cate_id']])->value('category_name');
                $value['scate'] = DB::name('goodscategory')->where(['id'=>$value['scate_id']])->value('category_name');

            }

            foreach ($list as $row) {

                $row->getRelation('goodscategory')->visible(['category_name']);
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
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }

                $params['updatetime'] = time();
                $params['goods_sn'] = $this->goods_sn($params['scate_id']);

//                halt($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);

                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);

        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                $params['update_admin'] = $this->auth->id;

                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
//        $row['pid'] = DB::name('goods_category')->where('pid',$row['pid'])->value('category_name');

        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 生成goods_sn
     */
    public function goods_sn($cate_id)
    {
        //
        $last_good_id = DB::name('goods')
            ->where(['scate_id'=>$cate_id])
            ->order('createtime desc')
            ->limit(1)
            ->value('id');
        $num = str_pad(strrev($cate_id).'.'.strrev($last_good_id+1),7,'0',STR_PAD_BOTH);
        $arr = explode('.',$num);
        $num = strrev($arr[0]).strrev($arr[1]);
        $goods = DB::name('goods')->where(['goods_sn'=>$num])->find();
        if($goods){
            $num++;
        }
        return $num;
    }

    /*
     * 商品导入
     * */
    public function daoru()
    {
        if ($this->request->isAjax()) {
            $params = $this->request->param();

            $arr['excel_path'] = $_SERVER['DOCUMENT_ROOT'] . $params['row']['client_path'];
            $data = json_encode($arr, JSON_UNESCAPED_UNICODE);
            $result = $this->importExecl($arr['excel_path']);

            unset($result[1]);//删除标题行
            $result = second_array_unique_bykey($result,"B");
            foreach($result as $k => $v){
                $is_cate = DB::name('goodscategory')->where(['pid'=>0,'category_name'=>trim($v["E"])])->find();
                $is_scate = DB::name('goodscategory')->where(['category_name'=>trim($v["F"])])->where(['pid'=>array('gt',0)])->find();
                if(!$is_cate){
                    $this->error("一级分类" . $v["E"] . "不存在");
                }
                if(!$is_scate){
                    $this->error("二级分类" . $v["F"] . "不存在");
                }

                if(trim($v["H"]) != "非标品" && trim($v["H"]) != "标品"){
                    $this->error("不存在此包装类型-->" . trim($v["H"]));
                }
                if(trim($v["G"]) != "是" && trim($v["G"]) != "否"){
                    $this->error("是否有库存（是、否)-->填写错误:" . trim($v["G"]));
                }
            }
            foreach($result as $ke => $val){
                $isset = DB::name('goods')->where(['goods_name'=>$val["B"]])->find();
                if($isset){
                   $this->error("此商品已经存在-->" . $val["B"]);
                }
            }

            foreach($result as $key => $value){
                if(trim($value['G']) == "否"){
                    $is_stock = "0";
                }else{
                    $is_stock = "1";
                }
                if(trim($value['H']) == "非标品"){
                    $packaging_type = "0";
                }else{
                    $packaging_type = "1";
                }
                $cate = DB::name('goodscategory')->where(['category_name'=>$value["E"]])->find();
                $scate = DB::name('goodscategory')->where(['category_name'=>$value["F"]])->find();
                $insert = [
                    'goods_name' => trim($value["B"]),
                    'goods_sn' => $this->goods_sn($scate['id']),
                    'spec' => $value["C"],
                    'unit' => $value["D"],
                    'cate_id' => $cate['id'],
                    'scate_id' => $scate['id'],
                    'is_stock' => $is_stock,
                    'status' => "1",
                    'remark' => trim($value["I"]),
                    'createtime' => time(),
                    'updatetime' => time(),
                    'packaging_type' => $packaging_type
                ];
                $result = DB::name('goods')->insert($insert);
            }
            if($result){
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
