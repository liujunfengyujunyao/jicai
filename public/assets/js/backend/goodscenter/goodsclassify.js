define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goodscenter/goodsclassify/index' + location.search,
                    add_url: 'goodscenter/goodsclassify/add',
                    edit_url: 'goodscenter/goodsclassify/edit',
                    del_url: '',
                    multi_url: 'goodscenter/goodsclassify/multi',
                    table: 'goodsclassify',
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
                        {field: 'id', title: __('Id'), operate:false},
                        {field: 'name', title: __('Name')},
                        {field: 'higherlevel', title: __('Higherlevel')},
                        {field: 'number', title: __('Number'), operate:false},
                        {field: 'status', title: __('Status'),searchList:[__('All')]},
                        {field: 'goodscreater', title: __('Goodscreater'), operate:false},
                        {field: 'goodscreattimeintdatesuffix', title: __('Goodscreattimeintdatesuffix'),operate:false, addclass:'datetimerange'},
                        {field: 'goodsupdater', title: __('Goodsupdater'), operate:false},
                        {field: 'goodsupdatertimeintdatesuffix', title: __('Goodsupdatertimeintdatesuffix'), operate:false, addclass:'datetimerange'},
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