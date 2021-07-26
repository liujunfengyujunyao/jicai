define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/admin/index',
                    add_url: 'auth/admin/add',
                    edit_url: 'auth/admin/edit',
                    // del_url: 'auth/admin/del',
                    multi_url: 'auth/admin/multi',
                }
            });

            var table = $("#table");

            //在表格内容渲染完成后回调的事件
            table.on('post-body.bs.table', function (e, json) {
                $("tbody tr[data-index]", this).each(function () {
                    if (parseInt($("td:eq(1)", this).text()) == Config.admin.id) {
                        $("input[type=checkbox]", this).prop("disabled", true);
                    }
                });
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'state', checkbox: true, },
                        {field: 'id', title: 'ID'},
                        {field: 'username', title: __('登录账号')},
                        {field: 'nickname', title: __('用户名称')},
                        {field: 'department', title: __('所属部门'), operate:false, formatter: Table.api.formatter.label},
                        {field: 'groups_text', title: __('岗位'), operate:false, formatter: Table.api.formatter.label},
                        // {field: 'email', title: __('Email')},
                        {field: 'status', title: __("Status"), formatter: Table.api.formatter.status},
                        // {field: 'logintime', title: __('Login time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'operate', title: __('Operate'), table: table,
                            buttons: [
                                {
                                    name: '分配岗位',
                                    title: __('分配岗位'),
                                    text:'分配岗位',
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-address-card',
                                    // url: 'example/bootstraptable/detail',
                                    url:'auth/admin/auth',

                                }
                            ],
                            events: Table.api.events.operate, formatter: function (value, row, index) {
                                if(row.id == Config.admin.id){
                                    return '';
                                }
                                return Table.api.formatter.operate.call(this, value, row, index);
                            }}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Form.api.bindevent($("form[role=form]"));
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
            $(document).on('click', '#repassword', function () {

                $.ajax({
                    type: 'post',
                    async: false,
                    url: "auth/admin/repassword",
                    data: {"admin_id":Config.admin_id},
                    dataType: 'json',
                    success: function (data, ret) {
                        Layer.alert('重置完成');

                        // table.bootstrapTable('refresh');
                        //如果需要阻止成功提示，则必须使用return false;
                        //return false;
                    },
                    error: function (data, ret) {
                        Layer.alert(ret.msg);
                        return false;
                    }
                });

                // Fast.api.close($("input[name=callback]").val());
                // Fast.api.close(ids);

            });
        },
        auth:function(){

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/admin/auth',
                    add_url: 'auth/admin/admin_auth',
                    // edit_url: 'auth/admin/edit',
                    // del_url: 'auth/admin/del',
                    // multi_url: 'auth/admin/multi',
                },
                commonSearch: false,
                visible: false,
                showToggle: false,
                showColumns: false,
                search:false,
                showExport: false,
            });

            var table = $("#table");

            //在表格内容渲染完成后回调的事件
            // table.on('post-body.bs.table', function (e, json) {
            //     $("tbody tr[data-index]", this).each(function () {
            //         if (parseInt($("td:eq(1)", this).text()) == Config.admin.id) {
            //             $("input[type=checkbox]", this).prop("disabled", true);
            //         }
            //     });
            // });

            // 初始化表格
            table.bootstrapTable({

                url: $.fn.bootstrapTable.defaults.extend.index_url,
                queryParams:function(params){
                    //加入queryParams参数
                    params.filter=JSON.stringify({'admin_id': Config.admin_id});
                    params.op=JSON.stringify({'admin_id':'='});
                    return{
                        search:params.search,
                        sort:params.sort,
                        order:params.order,
                        filter:params.filter,
                        op:params.op,
                        offset:params.offset,
                        limit : params.limit,
                    };
                },
                columns: [
                    [
                        {field: 'state', checkbox: true},
                        {field: 'a_id', title: 'ID'},
                        {field: 'a_name', title: __('岗位名称')},
                        {field: 'department_name', title: __('所属部门')},
                        {field: 'status', title: __("Status"), formatter: Table.api.formatter.status},
                        {field: 'commit', title: __('备注')},
                    ]
                ]
            });
            $(document).on('click', '.btn-callback', function () {
                var data= $("#table").bootstrapTable('getAllSelections');
                var ids=[];
                for(var i=0;i<data.length;i++){
                    ids.push(data[i].a_id);
                }
                // console.log(ids)

                $.ajax({
                    type: 'post',
                    async: false,
                    url: "auth/admin/admin_auth",
                    data: { "id": ids,"admin_id":Config.admin_id},
                    dataType: 'json',
                    success: function (data, ret) {
                        // Layer.alert(ret.msg + ",返回数据：" + JSON.stringify(data));
                        // Fast.api.close('SUCCESS');

                        window.parent.location.reload();
                        // table.bootstrapTable('refresh');
                        //如果需要阻止成功提示，则必须使用return false;
                        //return false;
                    },
                    error: function (data, ret) {
                        // console.log(data, ret);
                        Layer.alert(ret.msg);
                        return false;
                    }
                });

                // Fast.api.close($("input[name=callback]").val());
                // Fast.api.close(ids);

            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        }
    };
    return Controller;
});
