<?php

namespace app\admin\controller\auth;

use app\common\controller\Backend;
use fast\Tree;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 部门管理
 *
 * @icon fa fa-circle-o
 */
class Department extends Backend
{
    protected $noNeedRight = ['select_list'];
    /**
     * Department模型对象
     * @var \app\admin\model\auth\Department
     */
    protected $model = null;
    public $icon = array('│', '├', '└');
//    public $nbsp = "&nbsp;";
    public $nbsp = " ";

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\auth\Department;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    public function select_list()
    {

        $list = DB::name('department')->where(['status'=>1])->select();

        return json(['list'=>$list,'total'=>count($list)]);
    }

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
            if($this->auth->id == 1){
                $department_ids = DB::name('department')->column('id');
            }else{
                $group_ids = DB::name('auth_group_access')->where(['uid' => $this->auth->id])->column('group_id');
//                $group_ids = DB::name('auth_group_access')->where(['uid' => 10])->column('group_id');
                $department_ids = implode(',', array_filter(DB::name('auth_group')->where('id', 'in', $group_ids)->column('department_ids')));
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->where('id','in',$department_ids)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('id','in',$department_ids)
                ->order('weigh DESC,id ASC')
//                ->limit($offset, $limit)
                ->select();
            ;
            $ruleList = collection($list)->toArray();

            $result = [];
            $key = 0;

            // 必须将结果集转换为数组
//            $ruleList = collection(DB::name('department')->order('weigh DESC,id ASC')->select())->toArray();

//            halt($ruleList);
            foreach ($ruleList as $k => &$v) {
                $v['title'] = __($v['name']);
            }

            unset($v);

            Tree::instance()->init($ruleList);

            $ruleList = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');

            foreach($ruleList as $key => &$value){
                $job_ids = DB::name('jobs')->where(['department_id'=>$value['id']])->column('id');
                if($job_ids){
                    $value['jobs'] =  count($job_ids) . '个岗位' ;
                }else{
                    $value['jobs'] = "";
                }

            }

            $result = array("total" => $total, "rows" => $ruleList);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function getTreeArray($myid, $itemprefix = '')
    {
        $childs = $this->getChild($myid);
        $n = 0;
        $data = [];
        $number = 1;
        if ($childs) {
            $total = count($childs);
            foreach ($childs as $id => $value) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->icon[2];
                    $k = $itemprefix ? $this->nbsp : '';
                } else {
                    $j .= $this->icon[1];
                    $k = $itemprefix ? $this->icon[0] : '';
                }
                $spacer = $itemprefix ? $itemprefix . $j : '';
                $value['spacer'] = $spacer;
                $data[$n] = $value;
                $data[$n]['childlist'] = $this->getTreeArray($id, $itemprefix . $k . $this->nbsp);
                $n++;
                $number++;
            }
        }
        return $data;
    }
    //变更供应商
    public function auth()
    {
        $ids = request()->param('ids');

        if ($this->request->isAjax()) {
            //获取admin_id的步骤:auth方法渲染数据前先获取参数,再使用assignconfig把参数插入到视图,bootstrapTable初始化表格时加入queryParams参数
            $admin_id = json_decode(request()->param('filter'),true)['admin_id'];

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }

            // $total = DB::name('auth_group')

            //     ->where('id','>',1)

            //     ->count();
            $total = DB::name('supplier')
                ->count();


            $list = DB::name('supplier')
                ->field('id,supplier_name,address,linkman,status,remark')
                ->select();

            $supplier_ids = explode(',',DB::name('department')
                ->where(['id'=>$admin_id])
                ->value('supplier_ids'));
            foreach($list as $key => &$value){
                if(in_array($value['id'],$supplier_ids)){
                    $value['state'] = true;
                }else{
                    $value['state'] = false;
                }
            }
            //隐藏供应商岗位
            // foreach($list as $k => &$v){
            //     if($v['a_id'] == 5){
            //         unset($list[$k]);
            //     }
            // }
            $list = array_values($list);
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
//        $group_id = DB::name('auth_group_access')
//            ->where('uid',$ids)
//            ->column('group_id');

        $this->assignconfig('admin_id',$ids);
//        $this->assignconfig('group_id',$group_id);
        return $this->view->fetch();
    }
    //分配供应商
    public function supplier_auth()
    {
        $params = request()->param();
        $supplier_ids = implode(',',$params['id']);
        $result = DB::name('department')
            ->where(['id'=>$params['admin_id']])
            ->update(['supplier_ids'=>$supplier_ids]);

        if($result !== false){
            $this->success('分配完成');
        } else{
            $this->error('网络错误');
        }

    }
    /**
     * 添加平级
     */
    public function add_p($ids = null)
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
        //同级PID相同
        $pid = DB::name('department')->where(['id'=>$ids])->value('pid');
        $this->view->assign('pid',$pid);
        return $this->view->fetch();
    }

    /**
     * 添加下级
     */
    public function add_x($ids = null)
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
        //同级PID相同

        $this->view->assign('pid',$ids);
        return $this->view->fetch();
    }

    /**
     * 添加岗位
     */
    public function add_job($ids = null)
    {

        if ($this->request->isPost()) {

            $params = $this->request->post("row/a");
            if ($params) {

                $params = $this->preExcludeFields($params);
//                halt($params);
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
                    $params['createtime'] = $params['updatetime'] = time();
                    $result = DB::name('jobs')->insert($params);
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
        //同级PID相同

        $this->view->assign('department_id',$ids);
        return $this->view->fetch();
    }
    public function getAllNextId($id,$data=[]){
        $pids = DB::name('department')->where('pid',$id)->column('id');
        if(count($pids)>0){
            foreach($pids as $v){
                $data[] = $v;
                $data = $this->getAllNextId($v,$data); //注意写$data 返回给上级
            }
        }
        if(count($data)>0){
            return $data;
        }else{
            return [-1];
        }
    }
    /*
     * selectpage会请求两次 第一次为获取默认值 第二次获取列表
     * */
    public function first_department2()
    {
        $department_id = input('id');
        if($this->request->request("keyValue") === "0"){
            return json(['total'=>1, 'list'=>[
                ['id'=>0, 'department_name'=>""]
            ]
            ]);
        }
        if($this->request->request("keyValue")){


            $department_name = DB::name('department')->find($this->request->request("keyValue"))['name'];
//            $list = DB::name('goods_category')->where(['category_id'=>$this->request->request("keyValue")])->select();
            return json(['total'=>1, 'list'=>[
                ['id'=>$this->request->request("keyValue"), 'name'=>$department_name]
            ]
            ]);
        }

        $sids = $this->getAllNextId($department_id);

        $list = DB::name('department')->where(['status'=>"1"])->select();
        // 必须将结果集转换为数组
        $ruleList = collection(DB::name('department')
            ->where(['status'=>"1"])
            ->where('id','neq',$department_id)
            ->where('pid','not in',$sids)
            ->order('weigh DESC,id ASC')
            ->select())->toArray();//            halt($ruleList);
        foreach ($ruleList as $k => &$v) {
            $v['name'] = __($v['name']);
        }
        unset($v);
        Tree::instance()->init($ruleList);
        $list = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
//        halt($list);
        return json(['list'=>$list,'total'=>count($list)]);
    }

    public function first_department()
    {


        if($this->request->request("keyValue") === "0"){
            return json(['total'=>1, 'list'=>[
                ['id'=>0, 'department_name'=>""]
            ]
            ]);
        }
        if($this->request->request("keyValue")){


            $department_name = DB::name('department')->find($this->request->request("keyValue"))['name'];
//            $list = DB::name('goods_category')->where(['category_id'=>$this->request->request("keyValue")])->select();
            return json(['total'=>1, 'list'=>[
                ['id'=>$this->request->request("keyValue"), 'name'=>$department_name]
            ]
            ]);
        }



//      dump($sids);
//      halt($list);
        // 必须将结果集转换为数组
        $ruleList = collection(DB::name('department')
            ->where(['status'=>"1"])
            ->order('weigh DESC,id ASC')
            ->select())->toArray();
//            halt($ruleList);
        foreach ($ruleList as $k => &$v) {
            $v['name'] = __($v['name']);
        }
        unset($v);
        Tree::instance()->init($ruleList);
        $list = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
//        halt($list);
        return json(['list'=>$list,'total'=>count($list)]);
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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
