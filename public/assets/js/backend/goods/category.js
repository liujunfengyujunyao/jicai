define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/category/index' + location.search,
                    add_url: 'goods/category/add',
                    edit_url: 'goods/category/edit',
                    // del_url: 'goods/category/del',
                    multi_url: 'goods/category/multi',
                    table: 'goodscategory',
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
                        //{checkbox: true},
                        //{field: 'category_id', title: __('Category_id')},
                        {field: 'id', title: __('序号'), formatter: function(value, row, index){
                                return ++index;
                            }},
                        {field: 'category_name', title: __('Category_name'),operate: 'LIKE %...%'},
                        {field: 'pid', title: __('一级分类'),searchList: $.getJSON("goods/category/source1")},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'create_admin', title: __('Create_admin'),operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'update_admin', title: __('Update_admin'),operate:false},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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