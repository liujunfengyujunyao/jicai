define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order/index' + location.search,
                    add_url: 'order/order/add',
                    // edit_url: 'order/order/edit',
                    // del_url: 'order/order/del',
                    multi_url: 'order/order/multi',
                    table: 'order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'order_sn', title: __('Order_sn')},
                        {field: 'department_id', title: __('Department_id')},
                        {field: 'supplier_id', title: __('Supplier_id')},
                        {field: 'order_amount', title: __('Order_amount'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'sendtime', title: __('Sendtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'department.id', title: __('Department.id')},
                        {field: 'department.name', title: __('Department.name')},
                        {field: 'supplier.id', title: __('Supplier.id')},
                        {field: 'supplier.supplier_name', title: __('Supplier.supplier_name')},
                        {field: 'supplier.linkman', title: __('Supplier.linkman')},
                        {field: 'supplier.mobile', title: __('Supplier.mobile')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
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
        next:function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order/index' + location.search,
                    add_url: 'order/order/add',
                    edit_url: 'order/order/edit',
                    del_url: 'order/order/del',
                    multi_url: 'order/order/multi',
                    next_url:'order/order/next',
                    table: 'order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.next_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'index_id', title: __('序号'), formatter: function(value, row, index){
                                return ++index;
                            },operate:false},
                        {field: 'id', title: __('Id'),visible:false,operate:false},
                        {field: 'goods_sn', title: __('商品编号'),operate:false},
                        {field: 'goods_name', title: __('商品名称'),operate: 'LIKE %...%'},
                        {field: 'spec', title: __('规格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'price', title: __('单价'),operate:false},
                        {field: 'department.id', title: __('Department.id'),visible:false,operate:false},
                        {field: 'supplier.id', title: __('Supplier.id'),visible:false},
                        {
                            field: 'order_count',
                            title: '下单数量',
                            operate:false
                        },
                        {
                            field: 'order_amount',
                            title: '下单金额',
                            operate:false
                        },
                        {
                            field: 'remark',
                            title: '备注',
                            operate:false
                        },
                        {
                            field: '添加',
                            width: "150px",
                            title: '添加',
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    title: __('发送Ajax'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-magic',
                                    confirm: '确认发送Ajax请求？',
                                    url: 'order/order/ajax_update',
                                    success: function (data, ret) {
                                        Layer.alert(ret.msg + ",返回数据：" + JSON.stringify(data));
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                }],
                            formatter: Table.api.formatter.buttons,
                            operate:false
                        }
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        next2:function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"),function(data,ret){
                    console.log(data);//需要携带的参数
                    // console.log(ret);
                    var obj = new Function("return" + data)();//转换后的JSON对象
                    var url = ret.url+'?send_time='+obj.send_time+'&department_id='+obj.department_id+'&supplier_id='+obj.supplier_id;
                    // console.log(url)
                    // console.log(obj.send_time);//json name
                    Backend.api.addtabs(url,{iframeForceRefresh: true});//新建选项卡
                });
            }
        }
    };
    return Controller;
});