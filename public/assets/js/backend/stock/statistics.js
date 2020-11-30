define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/statistics/index' + location.search,
                    add_url: 'stock/statistics/add',
                    edit_url: 'stock/statistics/edit',
                    del_url: 'stock/statistics/del',
                    multi_url: 'stock/statistics/multi',
                    table: 'check_goods',
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
                        {field: 'index_id', title: __('序号'), formatter: function(value, row, index){
                                return ++index;
                            },operate:false},
                        // {field: 'id', title: __('ID'),operate: false},
                        {field: 'fa_check_goods.createtime', title: __('盘点时间'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'fa_admin.nickname', title: __('盘点人'),operate:'LIKE %...%'},
                        {field: 'goods_sn', title: __('商品编号'),operate: false},
                        {field: 'fa_check_goods.goods_name', title: __('商品名称'),operate:'LIKE %...%'},
                        {field: 'spec', title: __('规格'),operate: false},
                        {field: 'unit', title: __('单位'),operate: false},
                        {field: 'unit_price', title: __('单价'),operate: false},
                        {field: 'stock_number', title: __('库存数量'),operate: false},
                        {field: 'check_number', title: __('盘后数量'),operate: false},
                        {field: 'remark', title: __('备注'),operate: false},
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