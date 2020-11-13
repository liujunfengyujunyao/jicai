<?php

namespace app\admin\controller\goods;

use app\common\controller\Backend;
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
halt($where);
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
    public function test()
    {
        $prices = [7,1,5,3,6,4];
        $num = 0;
        for ($i = 1; $i < count($prices); $i++) {
        if ($prices[$i] > $prices[$i-1])
            $num += $prices[$i] - $prices[$i-1];
        }
        echo $num;
    }
}
