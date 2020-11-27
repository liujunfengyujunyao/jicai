define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/deliverylog/index' + location.search,
                    // add_url: 'stock/deliverylog/add',
                    // edit_url: 'stock/deliverylog/edit',
                    // del_url: 'stock/deliverylog/del',
                    // multi_url: 'stock/deliverylog/multi',
                    table: 'delivery_log',
                }
            });

            var table = $("#table");
            table.on("pre-body.bs.table",function(){
                var bt=table.data("bootstrap.table");
                if (bt){
                    bt.$toolbar.find(".export").find(".icon-share").text("  导出  ");
                }
            });
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        // {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        {field: 'index_id', title: __('序号'), formatter: function(value, row, index){
                                return ++index;
                            },operate:false},
                        {field: 'department_name', title: __('Department_name'),searchList: $.getJSON("stock/deliverylog/department_list")},
                        {field: 'apply_name', title: __('Apply_name'),operate: 'LIKE %...%'},
                        {field: 'goods_sn', title: __('Goods_sn'),operate:false},
                        {field: 'goods_name', title: __('Goods_name'),operate: 'LIKE %...%'},
                        {field: 'spec', title: __('Spec'),operate:false},
                        {field: 'unit', title: __('Unit'),operate:false},
                        {field: 'unit_price', title: __('Unit_price'),operate:false},
                        {field: 'delivery_number', title: __('Delivery_number'),operate:false},
                        {field: 'delivery_amount', title: __('Delivery_amount'),operate:false},
                        {field: 'remark', title: __('Remark'),operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                // showExport: false
                exportTypes:['excel']
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
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});