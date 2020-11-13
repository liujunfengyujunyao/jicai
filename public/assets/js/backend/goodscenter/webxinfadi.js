define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goodscenter/webxinfadi/index' + location.search,
                    add_url: 'goodscenter/webxinfadi/add',
                    edit_url: 'goodscenter/webxinfadi/edit',
                    del_url: 'goodscenter/webxinfadi/del',
                    multi_url: 'goodscenter/webxinfadi/multi',
                    table: 'webxinfadi',
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
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'name', title: __('Name')},
                        {field: 'classify', title: __('Classify'),operate:false},
                        {field: 'specifications', title: __('Specifications')},
                        {field: 'unit', title: __('Unit'),operate:false},
                        {field: 'highestprice', title: __('Highestprice'), operate:false},
                        {field: 'bottomprice', title: __('Bottomprice'), operate:false},
                        {field: 'averageprice', title: __('Averageprice'),operate:false},
                        {field: 'updatetimeintdatesuffix', title: __('Updatetimeintdatesuffix'),operate:false, addclass:'datetimerange'},
                        {field: 'goodsnumber', title: __('Goodsnumber')},
                        {field: 'goodsname', title: __('Goodsname')},
                        {field: 'goodsspecifications', title: __('Goodsspecifications'),operate:false},
                        {field: 'goodsprice', title: __('Goodsprice'), operate:false},
                        {field: 'goodscostprice', title: __('Goodscostprice'), operate:false},
                        {field: 'goodsunit', title: __('Goodsunit'),operate:false},
                        {field: 'goodsmatching', title: __('Goodsmatching'),searchList:[__('All'),__('Yes'),__('No')]},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: function (value,row) {
                            if(row.goodsmatching=="是"){
                              return '<button class="btn">取消匹配</button>';
                            }else{
                              return '<button class="btn btn-success">建立匹配</button>';  
                            }
                            
                         }}
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