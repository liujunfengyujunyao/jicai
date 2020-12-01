define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/goods/index' + location.search,
                    add_url: 'goods/goods/add',
                    edit_url: 'goods/goods/edit',
                    del_url: 'goods/goods/del',
                    multi_url: 'goods/goods/multi',
                    table: 'goods',
                }
            });

            var table = $("#table");

            table.on("pre-body.bs.table",function(){
                var bt=table.data("bootstrap.table");
                if (bt){
                    bt.$toolbar.find(".export").find(".icon-share").text("  导出  ");
                }
            });
// 在声明 Controller  之前 请求后台, 然后再循环 处理就可以了


            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        //{checkbox: true},
                        //{field: 'id', title: __('Id')},
                        {field: 'id', title: __('序号'), formatter: function(value, row, index){
                                return ++index;
                            }},
                        {field: 'goods_name', title: __('Goods_name'),operate: 'LIKE %...%'},
                        {field: 'goods_sn', title: __('Goods_sn'),operate: 'LIKE %...%'},
                        {field: 'spec', title: __('Spec'),operate:false},
                        {field: 'unit', title: __('Unit'),operate:false},
                        {field: 'cate_id', title: __('一级分类'),searchList: $.getJSON("goods/category/source1"),visible:false},
                        {field: 'scate_id', title: __('二级分类'),searchList: $.getJSON("goods/category/source2"),visible:false},
                        {field: 'cate', title: __('Cate_id'),operate:false},
                        {field: 'scate', title: __('Scate_id'),operate:false},
                        {field: 'is_stock', title: __('Is_stock'), searchList: {"0":__('Is_stock 0'),"1":__('Is_stock 1')}, formatter: Table.api.formatter.normal},
                        {field: 'packaging_type', title: __('包装类型'), searchList: {"0":__('非标品'),"1":__('标品')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'remark', title: __('Remark'),operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,operate:false},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,operate:false},
                        //{field: 'goodscategory.category_name', title: __('Goodscategory.category_name')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                exportTypes:['excel']
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {//一级分类发生改变清空二级分类
            $('#c-cate_id').on('change',function () {
                $("#c-scate_id").selectPageClear();
            });
            $("#c-scate_id").data("params", function(){
                const cateid  = $("input[name='row[cate_id]']").val();
                return {custom: {cate_id:cateid}};
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $('#c-cate_id').on('change',function () {
                $("#c-scate_id").selectPageClear();
            });
            $("#c-scate_id").data("params", function(){
                const cateid  = $("input[name='row[cate_id]']").val();
                return {custom: {cate_id:cateid}};
            });
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