define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/xinfadilog/index' + location.search,
                    add_url: 'goods/xinfadilog/add',
                    edit_url: 'goods/xinfadilog/edit',
                    del_url: 'goods/xinfadilog/del',
                    multi_url: 'goods/xinfadilog/multi',
                    table: 'goods_xinfadi_log',
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
                        // {field: 'xinfadi_id', title: __('新发地编号'),operate: 'LIKE %...%'},
                        // {field: 'goods_id', title: __('商品编号'),operate: 'LIKE %...%'},
                        {field: 'name', title: __('新发地菜品名'),operate: 'LIKE %...%'},
                        {field: 'spec', title: __('规格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'category', title: __('类别'),operate:false},
                        {field: 'min_price', title: __('最低价'), operate:'BETWEEN',operate:false},
                        {field: 'avg_price', title: __('平均价'), operate:'BETWEEN',operate:false},
                        {field: 'max_price', title: __('最高价'), operate:'BETWEEN',operate:false},
                        // {field: 'status', title: __('匹配状态'), searchList: {"0":__('未匹配'),"1":__('匹配')}, formatter: Table.api.formatter.status,operate: false},
                        {field: 'update_date', title: __('更新日期'), operate:'RANGE', addclass:'datetimerange'},

                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'goods.goods_id', title: __('编号'),operate:false},
                        {field: 'goods.goods_name', title: __('菜名'),operate: 'LIKE %...%'},
                        {field: 'goods.spec', title: __('规格'),operate:false},
                        {field: 'goods.price', title: __('售价'),operate:false},
                        // {field: 'goods.status', title: __('商品状态'),searchList: {"0":__('停用'),"1":__('启用')},formatter: Table.api.formatter.status,operate:false},
                    ]
                ],
                search:false,
                showToggle: false,
                // showColumns: false,
                searchFormVisible: true
            });
      
            // 为表格绑定事件
            Table.api.bindevent(table);
            $(".xinfadi-return").click(function(){
                history.back(0);
            });
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