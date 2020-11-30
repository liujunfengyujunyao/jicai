define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/check/index' + location.search,
                    add_url: 'stock/check/add'+ location.search,
                    table: 'check',
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

                        {field: 'id', title: __('ID'),operate: false},
                        {field: 'nickname', title: __('Check_admin'),operate: 'LIKE %...%'},
                        {field: 'amount', title: __('Amount'), operate: false},
                        {field: 'audit_admin', title: __('Audit_admin'),operate: false},
                        {field: 'fa_check.status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'count', title: __('Count'),operate: false},
                        {field: 'fa_check.createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'audittime', title: __('Audittime'),operate: false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            buttons: [
                                {
                                    name: 'addtabs',
                                    title: __('添加领料商品'),
                                    text:'编辑',

                                    classname: 'btn btn-xs btn-warning btn-addtabs',
                                    icon: 'fa fa-folder-o',

                                    //url: 'example/bootstraptable/detail'
                                    url: "stock/check/edit"
                                    // url: "stock/deliverygoods/index?delivery_id="+Fast.api.query("")
                                    //url: "supplier/price/index"
                                },
                                {
                                    name: 'ajax',
                                    title: __('发送Ajax'),
                                    text:"确认",
                                    hidden:function(row){
                                        return row.status=="2" || row.status=="1" ? true : false;
                                    },
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-leaf',
                                    confirm: '确认？',
                                    url: 'stock/check/through',
                                    success: function (data, ret) {
                                        // Layer.alert(ret.msg);
                                        table.bootstrapTable('refresh');//局部刷新
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'ajax',
                                    title: __('发送Ajax'),
                                    text:"取消",
                                    hidden:function(row){
                                        return row.status=="2" || row.status=="1" ? true : false;
                                    },
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-magic',
                                    confirm: '确认取消？',
                                    url: 'stock/check/reject',
                                    success: function (data, ret) {
                                        // Layer.alert(ret.msg);
                                        table.bootstrapTable('refresh');//局部刷新
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                            ],

                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate
                        }
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                showExport: false
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/check/add' + location.search,
                    table: 'check',
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

                        {field: 'id', title: __('ID'),class:"id",operate:false},
                        {field: 'goods_sn', title: __('商品编号'),operate:false},
                        {field: 'fa_goods.goods_name', title: __('商品名称'), operate: 'LIKE %...%'},
                        {field: 'spec', title: __('规格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'unit_price', title: __('单价'), operate:false},
                        {field: 'stock_number', title: __('库存数量'),operate:false},
                        {
                            field: 'check_number',
                            title: '盘后数量',
                            operate:false,
                            formatter:(value)=>{
                                return `<input value='${!value?"":value}' 
                                               type="text" class="change-input" data-type='delivery_number'>`
                            }
                        },
                        {
                            field: 'remark',
                            title: '备注',
                            operate:false,
                            formatter:(value)=>{
                                return `<input value='${!value?"":value}' 
                                               type="text" class="change-input" data-type='remark'>`
                            }
                        },
                        {
                            field: '添加',
                            text:'添加',
                            width: "150px",
                            title: '添加',
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'click',
                                    title: __('点击执行事件'),
                                    text:'添加',
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data) {
                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);
                                                const id = ele.find("td.id").text();
                                                const check_number = $(ele).find("td .change-input").eq(0).val();
                                                const remark = $(ele).find("td .change-input").eq(1).val()
                                                let check_id = $('#check_id').val();
                                                Fast.api.ajax({
                                                    url:'stock/check/ajax_add',
                                                    data:{
                                                        id:id,
                                                        remark:remark,
                                                        check_number:check_number,
                                                        check_id:check_id
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
                                                    console.log(ret);
                                                    var check_id = ret.data.check_id

                                                    $("#check_id").val(check_id);
                                                    return false;
                                                }, function(data, ret){
                                                    //失败的回调
                                                    alert(ret.msg);
                                                    return false;
                                                });

                                                layer.closeAll();
                                            });

                                    }
                                }
                            ],
                            formatter: Table.api.formatter.buttons,
                            operate:false,

                        }
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                showExport: false
            });

            Controller.api.bindevent(table);
        },
        edit: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/check/edit' + location.search,
                    add_url: 'stock/check/add'+ location.search,
                    table: 'check_goods',
                }
            });
            let status = $("#next").val();
            if(status == '已确认' || status == '已取消'){

                // $("#c-sendtime2").attr({"disabled":"disabled"});
                $("#next-btn").hide();
            }
            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [

                        {field: 'id', title: __('ID'),class:"id"},
                        {field: 'goods_sn', title: __('商品编号')},
                        {field: 'goods_name', title: __('商品名称'), operate:false},
                        {field: 'spec', title: __('规格')},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'unit_price', title: __('单价'), operate:false},
                        {field: 'stock_number', title: __('库存数量')},
                        {
                            field: 'check_number',
                            title: '盘后数量',
                            operate:false,
                            formatter:(value)=>{
                                return `<input value='${!value?"":value}' 
                                               type="text" class="change-input" data-type='delivery_number'>`
                            }
                        },
                        {
                            field: 'remark',
                            title: '备注',
                            operate:false,
                            formatter:(value)=>{
                                return `<input value='${!value?"":value}' 
                                               type="text" class="change-input" data-type='remark'>`
                            }
                        },
                        {
                            field: '添加',
                            text:'添加',
                            width: "150px",
                            title: '添加',
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'click',
                                    text:'编辑',
                                    hidden:function(row){
                                        return row.status=='1' || row.status=='2' ? true : false;
                                    },
                                    title: __('点击执行事件'),
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data) {
                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);
                                                const id = ele.find("td.id").text();
                                                const check_number = $(ele).find("td .change-input").eq(0).val();
                                                const remark = $(ele).find("td .change-input").eq(1).val()
                                                let check_id = $('#check_id').val();
                                                Fast.api.ajax({
                                                    url:'stock/check/ajax_edit',
                                                    data:{
                                                        id:id,
                                                        remark:remark,
                                                        check_number:check_number,
                                                        check_id:check_id
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
                                                    console.log(ret);
                                                    var check_id = ret.data.check_id

                                                    $("#check_id").val(check_id);
                                                    return false;
                                                }, function(data, ret){
                                                    //失败的回调
                                                    alert(ret.msg);
                                                    return false;
                                                });

                                                layer.closeAll();
                                            });

                                    }
                                },
                                {
                                    name: 'click2',
                                    title: __('点击执行事件'),
                                    hidden:function(row){
                                        return row.status=='1' || row.status=='2' ? true : false;
                                    },
                                    text:'删除',
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data) {
                                        layer.confirm("确认删除?",
                                            {btn: ['确定', '取消']}, function () {
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);
                                                const id = ele.find("td.id").text();
                                                const check_number = $(ele).find("td .change-input").eq(0).val();
                                                const remark = $(ele).find("td .change-input").eq(1).val()
                                                let check_id = $('#check_id').val();
                                                Fast.api.ajax({
                                                    url:'stock/check/ajax_del',
                                                    data:{
                                                        id:id,
                                                        remark:remark,
                                                        check_number:check_number,
                                                        check_id:check_id
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
                                                    console.log(ret);
                                                    var check_id = ret.data.check_id

                                                    $("#check_id").val(check_id);
                                                    table.bootstrapTable('refresh');
                                                    return false;
                                                }, function(data, ret){
                                                    //失败的回调
                                                    alert(ret.msg);
                                                    return false;
                                                });

                                                layer.closeAll();
                                            });

                                    }
                                }
                            ],
                            formatter: Table.api.formatter.buttons,
                            operate:false,

                        }
                    ]
                ],
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                showExport: false,
                commonSearch:false,
                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    //filter.后跟的是在ajax里使用的名称只需修改这两行
                    filter.check_id = Config.check_id;
                    //opop后跟的也是ajax里使用的名称，后面是条件
                    op.check_id = '=';
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    // console.log(params);
                    return params;
                },

            });
            $(document).on("click", ".btn-myexcel-export", function () { //监听刚刚的按钮btn-myexcel-export的动作

                top.location.href="add";
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
function loadLoaction(local){
    local = window.location.search.substring(1);
    const localArr = local.split("&");
    const localObj = {};
    localArr.map(v=>{
        const arr = v.split("=");
        localObj[arr[0]] = arr[1];
    });
    return localObj;
}