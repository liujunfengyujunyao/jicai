define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/department/index' + location.search,
                    add_url: 'auth/department/add',
                    edit_url: 'auth/department/edit',
                    // del_url: 'auth/department/del',
                    multi_url: 'auth/department/multi',
                    table: 'department',
                }
            });

            var table = $("#table");

            $(".btn-add").data("area", ["100%", "100%"]);

            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-editone").data("area", ["100%", "100%"]);
            });
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        // {field: 'title', title: __('名称'), align: 'left', formatter: Controller.api.formatter.title},
                        {field: 'title', title: __('名称'), align: 'left',operate:false},
                        {
                            field: 'jobs',
                            title:"岗位",
                            operate:false,
                            table:table,
                            events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'detail',
                                    text:function (row) {
                                        return row.jobs
                                    },
                                    title:function (row) {
                                        return row.jobs
                                    },
                                    classname: 'btn-dialog',
                                    url: function (row) {
                                        return '/admin.php/auth/jobs/department_jobs?department_id='+row.id
                                    }
                                }
                            ],
                            formatter: Table.api.formatter.buttons,
                            operate: "LIKE"
                        },
                        {field: 'department_number', title: __('Department_number'),operate:false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        // {field: 'createtime', title: __('创建时间'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('修改时间'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'weigh', title: __('Weigh')},
                        {   field: 'operate',
                            title: __('Operate'),
                            operate:false,
                            table: table,
                            buttons: [
                                {
                                    name: '分配供应商',
                                    title: __('分配供应商'),
                                    text:'分配供应商',
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-address-card',
                                    // url: 'example/bootstraptable/detail',
                                    url:'auth/department/auth',

                                },
                                {
                                    name: 'addtabs',
                                    text: __('同级'),
                                    title: __('同级'),
                                    extend: 'data-area= \'["80%", "40%"]\'',
                                    classname: 'btn btn-xs btn-warning btn-dialog',
                                    icon: 'fa fa-plus',
                                    //url: 'example/bootstraptable/detail'
                                    url: "auth/department/add_p?department_id="+Fast.api.query("")
                                    //url: "supplier/price/index"
                                },
                                {
                                    name: 'addtabs',
                                    text: __('下级'),
                                    title: __('下级'),
                                    extend: 'data-area= \'["50%", "50%"]\'',
                                    classname: 'btn btn-xs btn-warning btn-dialog',
                                    icon: 'fa fa-plus',
                                    //url: 'example/bootstraptable/detail'
                                    url: "auth/department/add_x?department_id="+Fast.api.query(""),
                                    callback: function (data) {
                                        Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
                                    }
                                    //url: "supplier/price/index"
                                },
                                {
                                    name: 'addtabs',
                                    text: __('添加岗位'),
                                    title: __('添加岗位'),
                                    extend: 'data-area= \'["100%", "100%"]\'',
                                    classname: 'btn btn-xs btn-warning btn-dialog',
                                    icon: 'fa fa-plus',
                                    //url: 'example/bootstraptable/detail'
                                    url: "auth/department/add_job?department_id="+Fast.api.query("")
                                    //url: "supplier/price/index"
                                },
                            ],
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            // pagination: false,
                            // search: false,
                            // commonSearch: false,


                        }
                    ]
                ],
                // commonSearch: false,
                // visible: false,
                // showToggle: false,
                // showColumns: false,
                // search:false,
                // showExport: false,
                // pagination:false,

                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: false,
                showSearch: false,
                //删除分页
                pagination:false
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            $(".nav.nav-tabs li").eq(2).find("a").trigger("click");
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'auth/department/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'auth/department/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'auth/department/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        auth:function(){

            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/department/auth',
                    add_url: 'auth/department/supplier_auth',
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
                        {field: 'id', title: 'ID'},
                        {field: 'supplier_name', title: __('岗位名称')},
                        {field: 'address', title: __('地址')},
                        {field: 'linkman', title: __('联系人')},
                        {field: 'status', title: __("Status"), formatter: Table.api.formatter.status},
                        {field: 'commit', title: __('备注')},
                    ]
                ]
            });
            $(document).on('click', '.btn-callback', function () {
                var data= $("#table").bootstrapTable('getAllSelections');
                var ids=[];
                for(var i=0;i<data.length;i++){
                    ids.push(data[i].id);
                }
                // console.log(ids)

                $.ajax({
                    type: 'post',
                    async: false,
                    url: "auth/department/supplier_auth",
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
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        add_job: function () {
            Controller.api.bindevent();
        },
        add_x: function () {
            Controller.api.bindevent();
        },
        add_p: function () {
            Controller.api.bindevent();
        },
        api: {
            formatter: {
                title: function (value, row, index) {
                    return row.status == 'hidden' ? "<span class='text-muted'>" + value + "</span>" : value;
                },

            },
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
