<?php

namespace app\admin\controller\auth;

use app\common\controller\Backend;
use fast\Tree;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Address extends Backend
{
    
    /**
     * Address模型对象
     * @var \app\admin\model\auth\Address
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\auth\Address;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    //省
    public function first_area()
    {
        if($this->request->request("keyValue") === "0"){
            return json(['total'=>1, 'list'=>[
                ['id'=>0, 'code'=>""]
            ]
            ]);
        }
        if($this->request->request("keyValue")){
            $name = DB::name('areas')->find($this->request->request("keyValue"))['name'];
            return json(['total'=>1, 'list'=>[
                ['id'=>$this->request->request("keyValue"), 'name'=>$name]
            ]
            ]);
        }
        // 必须将结果集转换为数组
        $list = collection(DB::name('areas')
            ->field('code as id,name')
            ->where(['pcode'=>0])
            ->order('code ASC')
            ->select())->toArray();
//
        return json(['list'=>$list,'total'=>count($list)]);
    }

    public function second_area()
    {
        $pcode = $this->request->request()['custom']['pcode'];
//        halt($this->request->request());
        $list = DB::name('areas')
            ->field('code as id,name')
            ->where(['pcode'=>$pcode])
            ->select();

        return json(['list'=>$list,'total'=>count($list)]);
    }

    public function third_area()
    {
        $pcode = $this->request->request()['custom']['pcode'];
//        halt($this->request->request());
        $list = DB::name('areas')
            ->field('code as id,name')
            ->where(['pcode'=>$pcode])
            ->select();

        return json(['list'=>$list,'total'=>count($list)]);
    }
///auth/department/first_department

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




    public function oadd()
    {
        $params = $this->request->param();

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }

                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }
                    $update = [
                        ''
                    ];
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
        $this->view->assign('department_id',$params['department_id']);
        return $this->view->fetch();
    }

    public function new_add()
    {

        if ($this->request->isPost()) {

            $params = $this->request->param();
            if ($params) {
                $update = [
                    'address' => $params['address'],
                    'phone' => $params['phone'],
                    'receiver' => $params['receiver']
                ];
                $result = DB::name('department')
                    ->where(['id'=>$params['department_id']])
                    ->update($update);
//                $address_id = DB::name('address')->insertGetId($params);
                if ($result !== false) {
                    $this->success(1);
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

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
                $value['department_id'] = DB::name('department')->where(['id'=>$value['department_id']])->value('name');
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

}
