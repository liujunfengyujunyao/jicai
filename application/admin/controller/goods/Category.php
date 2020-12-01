<?php

namespace app\admin\controller\goods;

use app\common\controller\Backend;
use think\Db;
/**
 * 商品分类管理
 *
 * @icon fa fa-circle-o
 */
class Category extends Backend
{
    protected $noNeedRight = ['first_cate','second_cate','source1','source2'];
    /**
     * Category模型对象
     * @var \app\admin\model\goods\Category
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\goods\Category;
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
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach($list as $key => &$value){
                $value['pid'] = DB::name('goodscategory')->find($value['pid'])['category_name'];
                $value['update_admin'] = DB::name('admin')->where(['id'=>$value['update_admin']])->value('nickname');
                $value['create_admin'] = DB::name('admin')->find($value['create_admin'])['nickname'];
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
                $params['create_admin'] = $this->auth->id;
                $params['update_admin'] = $this->auth->id;
                $params['updatetime'] = time();
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
        $row['first_cate'] = DB::name('goodscategory')->find($row['pid'])['category_name'];
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

    /*
     * selectpage会请求两次 第一次为获取默认值 第二次获取列表
     * */
    public function first_cate()
    {

        if($this->request->request("keyValue") === "0"){
            return json(['total'=>1, 'list'=>[
                ['id'=>0, 'category_name'=>""]
            ]
            ]);
        }
        if($this->request->request("keyValue")){


            $category_name = DB::name('goodscategory')->find($this->request->request("keyValue"))['category_name'];
//            $list = DB::name('goods_category')->where(['category_id'=>$this->request->request("keyValue")])->select();
            return json(['total'=>1, 'list'=>[
                ['id'=>$this->request->request("keyValue"), 'category_name'=>$category_name]
            ]
            ]);
        }

        $list = DB::name('goodscategory')->where(['pid'=>0])->select();

        return json(['list'=>$list,'total'=>count($list)]);
    }

    public function second_cate()
    {

        $cate_id = $this->request->request()['custom']['cate_id'];

        $list = DB::name('goodscategory')->where(['pid'=>$cate_id])->select();

        return json(['list'=>$list,'total'=>count($list)]);
    }

    /*
     * 首页搜索功能
     * ####返回格式必须是id,name 这种格式
     *
     * */
    public function source1()
    {
        $json = cache('first_cate');
        if($json===false){
            $list = DB::name('goodscategory')->field('id,category_name as name')->where(['pid'=>0])->select();
            $json = json($list);
            cache('first_cate');
        }
        return $json;
    }

    public function source2()
    {
        $json = cache('second_cate');
        if($json===false){
            $list = DB::name('goodscategory')->field('id,category_name as name')->where("pid > 0")->select();
            $json = json($list);
            cache('first_cate');
        }
        return $json;
    }


}
