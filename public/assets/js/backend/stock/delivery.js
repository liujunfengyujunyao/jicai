define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/delivery/index' + location.search,
                    add_url: 'stock/delivery/add',
                    edit_url: 'stock/delivery/edit',
                    del_url: 'stock/delivery/del',
                    multi_url: 'stock/delivery/multi',
                    table: 'delivery',
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
                        // {checkbox: true},
                        {field: 'id', title: __('ID'),operate:false},
                        {field: 'fa_delivery.department_id', title: __('部门'),searchList: $.getJSON("order/statistics/department_list")},
                        {field: 'fa_admin.nickname', title: __('Apply_admin'),operate: 'LIKE %...%'},
                        {field: 'delivery_amount', title: __('Delivery_amount'), operate:false},
                        {field: 'audit_admin', title: __('Audit_admin'),operate:false},
                        {field: 'fa_delivery.createtime', title: __('出库时间'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'audittime', title: __('Audittime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,operate:false},
                        {field: 'fa_delivery.status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            buttons: [
                                {
                                    name: 'addtabs',
                                    title: __('添加领料商品'),
                                    text:'编辑',
                                    classname: 'btn btn-xs btn-warning btn-addtabs',
                                    icon: 'fa fa-folder-o',

                                    //url: 'example/bootstraptable/detail'
                                    url: "stock/deliverygoods/delivery_edit"
                                    // url: "stock/deliverygoods/index?delivery_id="+Fast.api.query("")
                                    //url: "supplier/price/index"
                                }
                            ],
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate}
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                showExport: false
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"),function(data,ret){
                    console.log(data);//需要携带的参数
                    // console.log(ret);
                    var obj = new Function("return" + data)();//转换后的JSON对象
                    var url = ret.url+'?apply_admin='+obj.apply_admin+'&department_id='+obj.department_id;
                    // console.log(url)
                    // console.log(obj.send_time);//json name
                    Backend.api.addtabs(url,{iframeForceRefresh: true});//新建选项卡
                });
            }
        }
    };
    return Controller;
});