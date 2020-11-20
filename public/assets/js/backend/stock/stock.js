define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/stock/index' + location.search,
                    add_url: 'stock/stock/add',
                    edit_url: 'stock/stock/edit',
                    del_url: 'stock/stock/del',
                    multi_url: 'stock/stock/multi',
                    table: 'stock',
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
                        {field: 'goods_id', title: __('Goods_id'),visible:false,operate:false},
                        {field: 'cate_name', title: '一级分类',operate:false},
                        {field: 'scate_name', title: '二级分类',operate:false},
                        {field: 'goods_sn', title: '商品编号',operate:false},
                        {field: 't2.goods_name', title: '商品名称',operate: 'LIKE %...%'},
                        {field: 'spec', title: '规格',operate:false},
                        {field: 'unit', title: '单位',operate:false},
                        {field: 'unit_price', title: '单价',operate:false},
                        {field: 'stock_number', title: __('Stock_number'),operate:false},
                        {field: 't2.cate_id', title: '一级分类',visible:false,searchList: $.getJSON("goods/category/source1")},
                        {
                            field: 't2.scate_id',
                            title: '二级分类',
                            visible:false,
                            searchList: $.getJSON("goods/category/source2")
                        },
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