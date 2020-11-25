<?php

namespace app\admin\controller\auth;

use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use app\common\controller\Backend;
use fast\Random;
use fast\Tree;
use think\Db;
use think\Validate;

/**
 * 管理员管理
 *
 * @icon fa fa-users
 * @remark 一个管理员可以有多个角色组,左侧的菜单根据管理员所拥有的权限进行生成
 */
class Admin extends Backend
{

    /**
     * @var \app\admin\model\Admin
     */
    protected $model = null;
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Admin');

        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);

        $groupList = collection(AuthGroup::where('id', 'in', $this->childrenGroupIds)->select())->toArray();

        Tree::instance()->init($groupList);
        $groupdata = [];
        if ($this->auth->isSuperAdmin()) {
            $result = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
            foreach ($result as $k => $v) {
                $groupdata[$v['id']] = $v['name'];
            }
        } else {
            $result = [];
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n) {
                $childlist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['id']));
                $temp = [];
                foreach ($childlist as $k => $v) {
                    $temp[$v['id']] = $v['name'];
                }
                $result[__($n['name'])] = $temp;
            }
            $groupdata = $result;
        }

        $this->view->assign('groupdata', $groupdata);
        $this->assignconfig("admin", ['id' => $this->auth->id]);
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
//            halt(12313);
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            $childrenGroupIds = $this->childrenGroupIds;
            $groupName = AuthGroup::where('id', 'in', $childrenGroupIds)
                ->column('id,name');
            $authGroupList = AuthGroupAccess::where('group_id', 'in', $childrenGroupIds)
                ->field('uid,group_id')
                ->select();

            $adminGroupName = [];
            foreach ($authGroupList as $k => $v) {
                if (isset($groupName[$v['group_id']])) {
                    $adminGroupName[$v['uid']][$v['group_id']] = $groupName[$v['group_id']];
                }
            }
            $groups = $this->auth->getGroups();

            foreach ($groups as $m => $n) {
                $adminGroupName[$this->auth->id][$n['id']] = $n['name'];
            }

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where('id', 'in', $this->childrenAdminIds)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where('id', 'in', $this->childrenAdminIds)
                ->field(['password', 'salt', 'token'], true)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $k => &$v) {
                $v['department'] = DB::name('department')->where('id',$v['department_id'])->value('name');
                $groups = isset($adminGroupName[$v['id']]) ? $adminGroupName[$v['id']] : [];
                $v['groups'] = implode(',', array_keys($groups));
                $v['groups_text'] = implode(',', array_values($groups));
            }
            unset($v);

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
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
//                if (!Validate::is($params['password'], '\S{6,16}')) {
//                    $this->error(__("Please input correct password"));
//                }

                $params['salt'] = Random::alnum();
                $params['password'] = "123456";
                $params['password'] = md5(md5($params['password']) . $params['salt']);
                $params['avatar'] = '/assets/img/avatar.png'; //设置新管理员默认头像。
                $result = $this->model->validate('Admin.add')->save($params);

                if ($result === false) {

                    $this->error($this->model->getError());
                }
                $insert = [
                    'username' => $params['username'],
                    'nickname' => $params['nickname'],
                    'password' => $params['password'],
                    'mobile' => $params['username'],
                    'status' => "normal",
                    'salt' => $params['salt'],
                    'email' => $params['email']
                ];
                DB::name('user')->insert($insert);
//                $group = $this->request->post("group/a");
//
//                //过滤不允许的组别,避免越权
//                $group = array_intersect($this->childrenGroupIds, $group);
//                $dataset = [];
//                foreach ($group as $value) {
//                    $dataset[] = ['uid' => $this->model->id, 'group_id' => $value];
//                }
//                model('AuthGroupAccess')->saveAll($dataset);
                $this->success();
            }
            $this->error();
        }
        $department = DB::name('department')
            ->field('id,name')
            ->where(['status'=>1])
            ->select();
        $this->view->assign('department', $department);
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        if (!in_array($row->id, $this->childrenAdminIds)) {
            $this->error(__('You have no permission'));
        }
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
//                if ($params['password']) {
//                    if (!Validate::is($params['password'], '\S{6,16}')) {
//                        $this->error(__("Please input correct password"));
//                    }
//                    $params['salt'] = Random::alnum();
//                    $params['password'] = md5(md5($params['password']) . $params['salt']);
//                } else {
//                    unset($params['password'], $params['salt']);
//                }
                //这里需要针对username和email做唯一验证
                $adminValidate = \think\Loader::validate('Admin');
                $adminValidate->rule([
                    'username' => 'require|regex:\w{3,12}|unique:admin,username,' . $row->id,
                    'email'    => 'require|email|unique:admin,email,' . $row->id,
                    'password' => 'regex:\S{32}',
                ]);
                $result = $row->validate('Admin.edit')->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }

                // 先移除所有权限
//                model('AuthGroupAccess')->where('uid', $row->id)->delete();
//
//                $group = $this->request->post("group/a");
//
//                // 过滤不允许的组别,避免越权
//                $group = array_intersect($this->childrenGroupIds, $group);
//
//                $dataset = [];
//                foreach ($group as $value) {
//                    $dataset[] = ['uid' => $row->id, 'group_id' => $value];
//                }
//                model('AuthGroupAccess')->saveAll($dataset);

                $this->success();
            }
            $this->error();
        }
        $grouplist = $this->auth->getGroups($row['id']);
        $groupids = [];
        foreach ($grouplist as $k => $v) {
            $groupids[] = $v['id'];
        }
        $department = DB::name('department')
            ->field('id,name')
            ->where(['status'=>1])
            ->select();
        $this->assignconfig('admin_id',$ids);

        $this->view->assign('department', $department);
        $this->view->assign("row", $row);
        $this->view->assign("groupids", $groupids);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $ids = array_intersect($this->childrenAdminIds, array_filter(explode(',', $ids)));
            // 避免越权删除管理员
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->model->where('id', 'in', $ids)->where('id', 'in', function ($query) use ($childrenGroupIds) {
                $query->name('auth_group_access')->where('group_id', 'in', $childrenGroupIds)->field('uid');
            })->select();
            if ($adminList) {
                $deleteIds = [];
                foreach ($adminList as $k => $v) {
                    $deleteIds[] = $v->id;
                }
                $deleteIds = array_values(array_diff($deleteIds, [$this->auth->id]));
                if ($deleteIds) {
                    $this->model->destroy($deleteIds);
                    model('AuthGroupAccess')->where('uid', 'in', $deleteIds)->delete();
                    $this->success();
                }
            }
        }
        $this->error(__('You have no permission'));
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        // 管理员禁止批量操作
        $this->error();
    }

    /**
     * 下拉搜索
     */
    public function selectpage()
    {
        $this->dataLimit = 'auth';
        $this->dataLimitField = 'id';
        return parent::selectpage();
    }

    //权限管理按钮
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

            $total = DB::name('auth_group')

                ->where('id','>',1)

                ->count();

            $list = DB::name('auth_group')
                ->alias('t1')
                ->field('t1.id a_id,t1.name a_name,t1.department_id,t1.status,t2.id t_id,t2.name department_name,t1.commit')
                ->where('t1.id','>',1)

                ->join('__DEPARTMENT__ t2','t1.department_id=t2.id','LEFT')
                ->select();

            //已任职职位

            $groups = DB::name('auth_group_access')
                ->where(['uid'=>$admin_id])
                ->column('group_id');
            foreach($list as $key => &$value){
                if(in_array($value['a_id'],$groups)){
                    $value['state'] = true;
                }else{
                    $value['state'] = false;
                }
            }

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

    /*
     * 编辑权限
     * */
    public function admin_auth()
    {
        $params = request()->param();
        DB::name('auth_group_access')
            ->where('uid',$params['admin_id'])
            ->delete();
        if(!empty($params['id'])){
            foreach($params['id'] as $k => $v){
                DB::name('auth_group_access')->insert(['uid'=>$params['admin_id'],'group_id'=>$v]);
            }
        }
        $this->success('权限重置完成');
    }

    /*
     * 重置密码
     * */
    public function repassword()
    {
        $admin_id = request()->param('admin_id');
        $update = [
            'password' => '771b6400e41b367251435bb7c93484db',
            'salt' => 'eGVlWt'
        ];
        DB::name('admin')->where('id',$admin_id)->update($update);
        $this->success();
    }

    public function select_list()
    {

        $list = DB::name('admin')->field('id,nickname as name')->where(['status'=>'normal'])->select();

        return json(['list'=>$list,'total'=>count($list)]);
    }

}
