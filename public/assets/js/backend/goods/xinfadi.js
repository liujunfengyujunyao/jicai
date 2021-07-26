define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/xinfadi/index' + location.search,
                    // add_url: 'goods/xinfadi/add',
                    // edit_url: 'goods/xinfadi/edit',
                    // del_url: 'goods/xinfadi/del',
                    multi_url: 'goods/xinfadi/multi',
                    table: 'goods_xinfadi',
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
                        {field: 'id', title: __('新发地编号'),operate: 'LIKE %...%'},
                        {field: 'goods_id', title: __('商品编号'),operate: 'LIKE %...%'},
                        {field: 'name', title: __('新发地菜品名'),operate: 'LIKE %...%'},
                        {field: 'spec', title: __('新发地规格'),operate:false},
                        {field: 'unit', title: __('新发地单位'),operate:false},
                        {field: 'min_price', title: __('最低价'), operate:'BETWEEN',operate:false},
                        {field: 'avg_price', title: __('平均价'), operate:'BETWEEN',operate:false},
                        {field: 'max_price', title: __('最高价'), operate:'BETWEEN',operate:false},
                        {field: 'category', title: __('分类'),operate:false},
                        {field: 'status', title: __('匹配状态'), searchList: {"0":__('未匹配'),"1":__('匹配')}, formatter: Table.api.formatter.status},
                        {field: 'update_date', title: __('更新日期'), operate:'RANGE', addclass:'datetimerange',operate:false},
                        {field: 'add_user', title: __('操作人'),operate:false},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'goods.goods_name', title: __('商品名称'),operate: 'LIKE %...%'},
                        {field: 'goods.spec', title: __('商品规格'),operate:false},
                        {field: 'goods.unit', title: __('商品单位'),operate:false},
                        {field: 'goods.price', title: __('商品售价'),operate:false},
                        {field: 'goods.status', title: __('商品状态'),searchList: {"0":__('停用'),"1":__('启用')},formatter: Table.api.formatter.status,operate:false},
                        {   field: 'operate',
                            title: __('Operate'),
                            operate:false,
                            table: table,
                            buttons: [
                                {
                                    name: 'addtabs',
                                    text: __('匹配'),
                                    title: __('匹配'),
                                    extend: 'data-area= \'["100%", "100%"]\'',
                                    classname: 'btn btn-xs btn-warning btn-dialog btn-matching',
                                    icon: 'fa fa-folder-o',
                                    //url: 'example/bootstraptable/detail'
                                    url: "goods/xinfadi/goods_list?xinfadi_id="+Fast.api.query(""),
                                    //url: "supplier/price/index"
                                },
                                {
                                    name: 'confirm_order',
                                    text: __('取消匹配'),
                                    hidden:function(row){
                                        return row.status==0 ? true : false;
                                    },
                                    title: __('取消匹配'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-leaf',
                                    url: 'goods/xinfadi/cancel_mate',
                                    confirm: '确认取消',
                                    success: function (data, ret) {
                                        table.bootstrapTable('refresh');//局部刷新
                                        // Layer.alert(ret.msg);
                                        //如果需要阻止成功提示，则必须使用return false;
                                        // return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },

                            ],
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            // pagination: false,
                            // search: false,
                            // commonSearch: false,


                        }
                    ]
                ],
                search:false,
                showToggle: false,
                // showColumns: false,
                searchFormVisible: true
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            $(table).on("click",".btn-matching",function(e){
                var value = $(e.target).parents("tr").find("td").eq(2).text();
                if($(e.target).parent("td").find('[title="取消匹配"]').length===1){
                    sessionStorage.setItem("hasMatch",true);
                }else{
                    sessionStorage.setItem("hasMatch",false);
                }
                sessionStorage.setItem("matchingName",value);
            })
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        goods_list: function () {

            let xinfadi_id = $("#xinfadi_id").val();
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods/xinfadi/goods_list' + location.search,
                    // add_url: 'goods/xinfadi/add',
                    // edit_url: 'goods/xinfadi/edit',
                    // del_url: 'goods/xinfadi/del',

                    table: 'goods',
                }
            });
            let matching_val = null;
            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [

                        {field: 'id', title: __('ID'),operate:false},
                        {field: 'goods_name', title: __('商品名称'),operate: 'LIKE %...%'},
                        {field: 'goods_sn', title: __('商品编号'),operate:false},
                        {field: 'price', title: __('价格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'spec', title: __('规格'),operate:false},

                        {field: 'status', title: __('状态'), searchList: {"0":__('停用'),"1":__('启用')}, formatter: Table.api.formatter.status},

                        {   field: 'operate',
                            title: __('Operate'),
                            operate:false,
                            table: table,
                            buttons: [
                                {
                                    name: 'confirm_order',
                                    text: __('匹配'),
                                    hidden:function(row){
                                        return row.status==0 ? true : false;
                                    },
                                    title: __('匹配'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-leaf',
                                    url: "goods/xinfadi/mate?xinfadi_id="+xinfadi_id,
                                    confirm:function(e){
                                        //console.log(sessionStorage.getItem("hasMatch"))
                                        if(sessionStorage.getItem("hasMatch")==="true"){
                                            return '该商品已经匹配过，再次匹配会覆盖之前的信息，是否继续？';
                                        }else{
                                            //console.log(e);
                                            return null;
                                        }


                                    },
                                    success: function (data, ret) {
                                        //parent.$("btn-refresh").trigger("click");

                                        // table.bootstrapTable('refresh');//局部刷新
                                        // Layer.alert(ret.msg);
                                        //如果需要阻止成功提示，则必须使用return false;
                                        // return false;
                                        parent.window.$("#table").bootstrapTable('refresh');//局部刷新
                                        sessionStorage.clear();
                                        parent.window.$(".layui-layer-iframe").find(".layui-layer-close").trigger("click");
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },

                            ],
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            // pagination: false,
                            // search: false,
                            // commonSearch: false,


                        }
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true
            });
            Controller.api.bindevent();
            //处理事件
            let text = sessionStorage.getItem("matchingName");
            if(text){
                sessionStorage.removeItem("matchingName");
                $('input[name="goods_name"]').val(text);
                $('.form-horizontal').submit();

            }
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});