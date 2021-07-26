define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/address/index' + location.search,
                    add_url: 'auth/address/add',
                    edit_url: '',
                    del_url: 'auth/address/del',
                    multi_url: 'auth/address/multi',
                    table: 'address',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'department_id', title: __('客户名称')},
                        {field: 'receiver', title: __('Receiver')},
                        {field: 'phone', title: __('Phone')},

                        {field: 'address', title: __('地址')},
                        {field: 'weigh', title: __('Weigh'),visible:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                // showSearch: false,
                search:false,
                showToggle: false,
                showColumns: false,
                // searchFormVisible: true,
                showExport: false,
                showSearch: false
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

        },
        add: function () {
            
            Controller.api.bindevent();
            // $("#add-form").on("click",".city-picker-span",function(){
                $("#c-city").on("cp:updated", function() {
                    $("#address").val($("#c-city").val().replace(/\//g,""));
                });
            // });
        },
        oadd: function () {
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