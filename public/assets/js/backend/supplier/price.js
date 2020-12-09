define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'supplier/price/index' + location.search,
                    add_url: 'supplier/price/add',
                    edit_url: 'supplier/price/edit',
                    del_url: 'supplier/price/del',
                    multi_url: 'supplier/price/multi',
                    table: 'goods',
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
                        {field: 'id', title: __('Id'),operate:false},
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
                        {field: 'price',class: 'now-price', title:'当前单价',operate:false},
                        {field: 'price', title: __('调后单价'),operate:false, formatter: function (value, row, index) {
                            value = value === null ? '' : value;
                            return '<input type="text" value="' + value + '">';
                        }}
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                showExport: false,
                // commonSearch:false,
            });
            $("#table").on("blur","input",function(e){
                let supplier_id = $("#supplier").val();

                let goods_sn = $(e.target).parents("tr").find("td").eq(0).text();
                const prevpire = $(e.target).parents("tr").find("td.now-price");
                const self = this;
                Fast.api.ajax({
                    url:'supplier/price/update_price',
                    data:{
                        goods_sn:goods_sn,
                        price:self.value,
                        supplier_id:supplier_id
                    },
                    loading:false,
                    success: function(){
                        //成功的回调
                        // $(".btn-refresh").trigger("click");
                        prevpire.text(self.value);
                        return false;
                    }
                });
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