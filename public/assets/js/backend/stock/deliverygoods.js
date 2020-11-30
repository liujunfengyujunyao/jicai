define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/delivery_goods/index' + location.search,
                    add_url: 'stock/delivery_goods/add',
                    edit_url: 'stock/delivery_goods/edit',
                    del_url: 'stock/delivery_goods/del',
                    multi_url: 'stock/delivery_goods/multi',
                    table: 'delivery_goods',
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
                        {field: 'id', title: __('Id')},
                        {field: 'delivery_id', title: __('Delivery_id')},
                        {field: 'goods_id', title: __('Goods_id')},
                        {field: 'stock_id', title: __('Stock_id')},
                        {field: 'delivery_number', title: __('Delivery_number'), operate:'BETWEEN'},
                        {field: 'remark', title: __('Remark')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
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
        delivery_add: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/deliverygoods/delivery_add' + location.search,

                    table: 'delivery_goods',
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
                        {field: 'stock_id', title: __('序号'),class:"stock_id",operate:false},
                        {field: 'goods_sn', title: __('商品编号'),operate:false},
                        {field: 'fa_goods.goods_name', title: __('商品名称'),operate: 'LIKE %...%',visible:false},
                        {field: 'goods_name', title: __('商品名称'),operate: 'LIKE %...%',operate:false},
                        {field: 'spec', title: __('规格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'unit_price', title: __('单价'),operate:false},
                        {field: 'stock_number', title: __('库存数量'),operate:false},
                        {
                            field: 'delivery_number',
                            title: '领料数量',
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
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data) {
                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const locatinObj = loadLoaction(); //地址数据
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);

                                                const delivery_number = $(ele).find("td .change-input").eq(0).val();
                                                const remark = $(ele).find("td .change-input").eq(1).val()
                                                const stock_id = ele.find("td.stock_id").text();
                                                // console.log(ele)
                                                let apply_admin = $('#apply_admin').val();
                                                let delivery_id = $('#delivery_id').val();
                                                let department_id = $('#department_id').val();
                                                Fast.api.ajax({
                                                    url:'stock/deliverygoods/ajax_add',
                                                    data:{
                                                        stock_id:stock_id,
                                                        remark:remark,
                                                        apply_admin:apply_admin,
                                                        delivery_number:delivery_number,
                                                        delivery_id:delivery_id,
                                                        department_id:department_id
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
                                                    console.log(ret);
                                                    var delivery_id = ret.data.delivery_id
                                                    console.log(delivery_id);
                                                    $("#delivery_id").val(delivery_id);
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
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    //filter.后跟的是在ajax里使用的名称只需修改这两行
                    filter.delivery_id = $("#delivery_id").val();
                    // filter.supplier_id = Config.supplier_id
                    //opop后跟的也是ajax里使用的名称，后面是条件
                    op.order_id = '=';
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    // console.log(params);
                    return params;
                },
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                showExport: false
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        delivery_edit: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/deliverygoods/delivery_edit' + location.search,

                    table: 'delivery_goods',
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
                        {field: 'id', title: __('序号'),class:"stock_id",operate:false},
                        {field: 'goods_sn', title: __('商品编号'),operate:false},
                        {field: 'fa_goods.goods_name', title: __('商品名称'),operate: 'LIKE %...%',visible:false},
                        {field: 'goods_name', title: __('商品名称'),operate: 'LIKE %...%',operate:false},
                        {field: 'spec', title: __('规格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'unit_price', title: __('单价'),operate:false},
                        {field: 'stock_number', title: __('当前库存'),operate:false},
                        {
                            field: 'delivery_number',
                            title: '领料数量',
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
                            title: '操作',
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'click',
                                    text:'编辑',
                                    hidden:function(row){
                                        return row.status=="1" || row.status=="2" ? true : false;
                                    },
                                    title: __('点击执行事件'),
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data,row) {

                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const locatinObj = loadLoaction(); //地址数据
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);

                                                const delivery_number = $(ele).find("td .change-input").eq(0).val();
                                                const remark = $(ele).find("td .change-input").eq(1).val()
                                                const id = row.id;
                                                // console.log(ele)
                                                let apply_admin = $('#apply_admin').val();
                                                let delivery_id = $('#delivery_id').val();
                                                let department_id = $('#department_id').val();
                                                Fast.api.ajax({
                                                    url:'stock/deliverygoods/ajax_edit',
                                                    data:{
                                                        id:id,
                                                        remark:remark,
                                                        apply_admin:apply_admin,
                                                        delivery_number:delivery_number,
                                                        delivery_id:delivery_id,
                                                        department_id:department_id
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
                                                    console.log(ret);
                                                    var delivery_id = ret.data.delivery_id
                                                    console.log(delivery_id);
                                                    $("#delivery_id").val(delivery_id);
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
                                },
                                {
                                    name: 'click2',
                                    text:"删除",
                                    hidden:function(row){
                                        return row.status=="1" || row.status=="2" ? true : false;
                                    },
                                    title: __('点击执行事件'),
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data,row) {
                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const id = row.id;
                                                Fast.api.ajax({
                                                    url:'stock/deliverygoods/ajax_del',
                                                    data:{
                                                        id:id,
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
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
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],

                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    //filter.后跟的是在ajax里使用的名称只需修改这两行
                    filter.delivery_id = $("#delivery_id").val();
                    // filter.supplier_id = Config.supplier_id
                    //opop后跟的也是ajax里使用的名称，后面是条件
                    op.order_id = '=';
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    // console.log(params);
                    return params;
                },

                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                showExport: false,
                commonSearch:false,
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            let delivery_status = $("#delivery_status").val();
            if(delivery_status == '已确认' || delivery_status == '已取消'){
                console.log(delivery_status);
                $(".change-input").attr({"disabled":"disabled"});
                console.log($("#c1").val());
                $("#next").hide();
            }
        },
        next: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'stock/deliverygoods/next' + location.search,

                    table: 'delivery_goods',
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
                        {field: 'stock_id', title: __('序号'),class:"stock_id",operate:false},
                        {field: 'goods_sn', title: __('商品编号'),operate:false},
                        {field: 'fa_goods.goods_name', title: __('商品名称'),operate: 'LIKE %...%',visible:false},
                        {field: 'goods_name', title: __('商品名称'),operate: 'LIKE %...%',operate:false},
                        {field: 'spec', title: __('规格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'unit_price', title: __('单价'),operate:false},
                        {field: 'stock_number', title: __('当前库存'),operate:false},
                        {
                            field: 'delivery_number',
                            title: '领料数量',
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
                            hidden:function(row){
                                return row.send_status==1? true : false;
                            },
                            width: "150px",
                            title: '操作',
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'click',
                                    text:'添加',
                                    title: __('点击执行事件'),
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data,row) {

                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const locatinObj = loadLoaction(); //地址数据
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);

                                                const delivery_number = $(ele).find("td .change-input").eq(0).val();
                                                const remark = $(ele).find("td .change-input").eq(1).val()
                                                const id = row.stock_id;
                                                // console.log(ele)
                                                let apply_admin = $('#apply_admin').val();
                                                let delivery_id = $('#delivery_id').val();
                                                let department_id = $('#department_id').val();
                                                Fast.api.ajax({
                                                    url:'stock/deliverygoods/next_edit',
                                                    data:{
                                                        id:id,
                                                        remark:remark,
                                                        apply_admin:apply_admin,
                                                        delivery_number:delivery_number,
                                                        delivery_id:delivery_id,
                                                        department_id:department_id
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
                                                    console.log(ret);
                                                    var delivery_id = ret.data.delivery_id
                                                    console.log(delivery_id);
                                                    $("#delivery_id").val(delivery_id);
                                                    // table.bootstrapTable('refresh');
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

                            ],
                            formatter: Table.api.formatter.buttons,
                            operate:false,

                        }
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    //filter.后跟的是在ajax里使用的名称只需修改这两行
                    filter.delivery_id = $("#delivery_id").val();
                    // filter.supplier_id = Config.supplier_id
                    //opop后跟的也是ajax里使用的名称，后面是条件
                    op.delivery_id = '=';
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    // console.log(params);
                    return params;
                },
                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                showExport: false,

            });

            // 为表格绑定事件
            Table.api.bindevent(table);
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