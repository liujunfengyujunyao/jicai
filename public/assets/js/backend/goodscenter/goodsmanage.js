define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goodscenter/goodsmanage/index' + location.search,
                    add_url: 'goodscenter/goodsmanage/add',
                    edit_url: 'goodscenter/goodsmanage/edit',
                    del_url: '',
                    multi_url: 'goodscenter/goodsmanage/multi',
                    table: 'goodsmanage',
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
                        {field: 'id', title: __('Id'), operate:false},
                        {field: 'goodsnumber', title: __('Goodsnumber')},
                        {field: 'name', title: __('Name')},
                        {field: 'price', title: __('Price'), operate:false},
                        {field: 'classifyone', title: __('Classifyone')},
                        {field: 'classifytwo', title: __('Classifytwo')},
                        {field: 'number', title: __('Number')},
                        {field: 'status', title: __('Status'),searchList:[__('All'),__('Grounding'),__('Undercarriage'),__('Pause')]},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
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