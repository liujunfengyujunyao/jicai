define(['jquery', 'bootstrap', 'backend', 'table', 'form','bootstrap-datetimepicker'], function ($, undefined, Backend, Table, Form,datetimepicker) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order/index' + location.search,
                    add_url: 'order/order/add',
                    daoru_url: 'order/order/daoru',
                    // edit_url: 'order/order/edit',
                    // del_url: 'order/order/del',
                    multi_url: 'order/order/multi',
                    table: 'order',
                }
            });

            var table = $("#table");
            // var LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'index_id', title: __('序号'), formatter: function(value, row, index){
                                return ++index;
                            },operate:false,class:"index_id"},
                        {field: 'id', title: __('Id'),operate:false,visible:false},
                        {field: 'order_sn', title: __('Order_sn'),operate:false},
                        {field: 'department.id', title: __('Department.name'),searchList: $.getJSON("order/order/department_list")},
                        {field: 'supplier.supplier_name', title: __('Supplier.supplier_name'),operate:false},
                        {field: 'supplier.linkman', title: __('Supplier.linkman'),operate:false},
                        {field: 'supplier.mobile', title: __('Supplier.mobile'),operate:false},
                        {field: 'order_amount', title: __('Order_amount'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'sendtime', title: __('Sendtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:'YYYY-MM-DD',defaultValue:this.today(0)+' 00:00:00 - '+this.today(0)+' 23:59:59'},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'department_id', title: __('Department_id'),visible:false,operate:false},
                        {field: 'supplier_id', title: __('Supplier_id'),visible:false,operate:false},
                        {field: 'department.id', title: __('Department.id'),visible:false,operate:false},
                        {field: 'supplier.id', title: __('Supplier.id'),visible:false,operate:false},
                        {
                            field: 'buttons',
                            width: "120px",
                            title: "操作",
                            operate:false,
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'confirm_order',
                                    text: __('确认收货'),
                                    hidden:function(row){
                                        return row.status=="2" || row.status=="1" ? true : false;
                                    },
                                    title: __('确认收货'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-leaf',
                                    url: 'order/order/confirm_order',
                                    confirm: '确认收货',
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
                                {
                                    name: 'cancel_order',
                                    text: __('取消订单'),
                                    hidden:function(row){
                                        return row.status=="2" || row.status=="1" ? true : false;
                                    },
                                    title: __('取消订单'),
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-magic',
                                    url: 'order/order/cancel_order',
                                    confirm: '确认取消',
                                    success: function (data, ret) {
                                        table.bootstrapTable('refresh');//局部刷新

                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'addtabs',
                                    text: __('编辑'),
                                    title: __('编辑'),
                                     extend: 'data-area= \'["100%", "100%"]\'',
                                    classname: 'btn btn-xs btn-warning btn-dialog',
                                    icon: 'fa fa-folder-o',
                                    url: 'order/order/next2',
                                },{
                                    name: 'addtabs',
                                    text: __('打印'),
                                    title: __('打印'),
                                    classname: 'btn btn-xs btn-warning btn-addprint',
                                    icon: 'fa fa-folder-o',
                                }
                            ],
                            formatter: Table.api.formatter.buttons,
                            // formatter:function(value,row,index){
                            //     var that = $.extend({},this);
                            //     var table = $(that.table).clone(true);
                            //     if(row.status=="2"){
                            //         $(table).data("")
                            //     }
                            // }
                        }
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],

                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                showExport:false

            });

            $(document).on("click", ".btn-myexcel-export", function () { //监听刚刚的按钮btn-myexcel-export的动作
                var myexceldata=table.bootstrapTable('getSelections');//获取选中的项目的数据 格式是json
                myexceldata=JSON.stringify(myexceldata);//数据转成字符串作为参数
                //直接url访问，不能使用ajax，因为ajax要求返回数据，和PHPExcel一会浏览器输出冲突！将数据作为参数
                top.location.href="order/exportOrderExcel?data="+myexceldata;
            });
            $(document).on("click", ".btn-daoru", function () { //监听刚刚的按钮btn-myexcel-export的动作

                //直接url访问，不能使用ajax，因为ajax要求返回数据，和PHPExcel一会浏览器输出冲突！将数据作为参数
                top.location.href="order/daoru";
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {

            Controller.api.bindevent();

        },
        daoru: function () {

            Controller.api.bindevent();

        },
        edit: function () {
            Controller.api.bindevent();
        },
        next:function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order/index' + location.search,
                    // index_url:'order/order/index' + loadLoaction.supplier_id,
                    // loadLoaction
                    // add_url: 'order/order/add',
                    // edit_url: 'order/order/edit',
                    // del_url: 'order/order/del',
                    // multi_url: 'order/order/multi',
                    next_url:'order/order/next',
                    table: 'order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.next_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        // {checkbox: true},
                        {field: 'index_id', title: __('序号'), formatter: function(value, row, index){
                                return ++index;
                            },operate:false,class:"index_id"},
                        {field: 'goods_id', title: __('Id'),operate:false,class:"goods_id"},
                        {field: 'goods_sn', title: __('商品编号'),operate:false},
                        {field: 'goods_name', title: __('商品名称'),operate: 'LIKE %...%'},
                        {field: 'spec', title: __('规格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'price', title: __('单价'),operate:false,class:"price"},
                        // {field: 'order_count', title: '下单数量'},
                        // {field: 'order_amount', title: '下单金额'},
                        // {field: 'remark', title: '备注'},
                        {field: 'department.id', title: __('Department.id'),visible:false,operate:false},
                        {field: 'supplier.id', title: __('Supplier.id'),visible:false,operate:false},
                        {
                            field: 'order_count',
                            class: 'order_count',
                            title: '下单数量',
                            operate:false,
                            formatter:(value)=>{
                                return `<input value='${!value?"":value}' 
                                               type="text" class="change-input" data-type='order_count'>`
                            }
                        },
                        {
                            field: 'order_amount',
                            class:"order_amount",
                            title: '下单金额',
                            operate:false
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
                            width: "150px",
                            title: '添加',
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'click',
                                    title: __('点击执行事件'),
                                    text:'保存',
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data) {
                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const locatinObj = loadLoaction(); //地址数据
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);
                                                const order_count = $(ele).find("td .change-input").eq(0).val();
                                                const remark = $(ele).find("td .change-input").eq(1).val()
                                                const goods_id = ele.find("td.goods_id").text();
                                                let order_id = $('#order_id').val();

                                                Fast.api.ajax({
                                                    url:'order/order/ajax_add',
                                                    data:{
                                                        order_count:order_count,
                                                        remark:remark,
                                                        supplier_id:locatinObj.supplier_id,
                                                        send_time:locatinObj.send_time,
                                                        department_id:locatinObj.department_id,
                                                        goods_id:goods_id,
                                                        order_id:order_id
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
                                                    console.log(ret);
                                                    var order_id = ret.data.order_id
                                                    console.log(order_id);
                                                    $("#order_id").val(order_id);
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
                            operate:false
                        }
                    ]
                ],
                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    //filter.后跟的是在ajax里使用的名称只需修改这两行
                    filter.order_id = $("#order_id").val();
                    filter.supplier_id = Config.supplier_id
                    filter.cate_id = Config.cate_id
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
        next2:function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/order/index' + location.search,
                    next_url:'order/order/next2',
                    table: 'order',
                }
            });
            let order_status = $("#order_status").val();
            if(order_status == '已收货' || order_status == '已取消'){
                console.log(order_status);
                $("#c-sendtime2").attr({"disabled":"disabled"});
                $("#next3").hide();
            }

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.next_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        //
                        // {field: 'index_id', title: __('序号'), formatter: function(value, row, index){
                        //         return ++index;
                        //     },operate:false,class:"index_id"},
                        {field: 'id', title: __('ID'),operate:false,class:"id"},
                        {field: 'goods_sn', title: __('商品编号'),operate:false},
                        {field: 'goods_name', title: __('商品名称'),operate: false},
                        {field: 'spec', title: __('规格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'price', title: __('单价'),operate:false,class:"price",formatter:(value)=>{
                                return `<input value='${!value?"":value}' 
                                           type="text" class="change-input" data-type='price'>`
                            }},
                        {field: 'department.id', title: __('Department.id'),visible:false,operate:false},
                        {field: 'supplier.id', title: __('Supplier.id'),visible:false,operate:false},
                        {
                            field: 'order_count',
                            class:"order_count",
                            title: '下单数量',
                            operate:false,
                            formatter:(value)=>{
                                return `<input value='${!value?"":value}' 
                                               type="text" class="change-input" data-type='order_count'>`
                            }
                        },
                        {
                            field: 'order_amount',
                            class:"order_amount",
                            title: '下单金额',
                            operate:false
                        },
                        {
                            field: 'takeorder_count',
                            title: '收货数量',
                            operate:false,
                            formatter:(value)=>{
                                return `<input value='${!value?"":value}' 
                                               type="text" class="change-input" data-type='takeorder_count'>`
                            }
                        },
                        {
                            field: 'takeorder_amount',
                            class:"takeorder_amount",
                            title: '收货金额',
                            operate:false
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
                            field: '操作',
                            width: "150px",
                            title: '操作',
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'click',
                                    text:"保存",
                                    // hidden:function(row){
                                    //     return row.status==1 || row.status==2 ? true : false;
                                    // },
                                    title: __('点击执行事件'),
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data) {
                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const locatinObj = loadLoaction(); //地址数据
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);
                                                const price = $(ele).find("td .change-input").eq(0).val();
                                                const sendqty = $(ele).find("td .change-input").eq(2).val();
                                                const order_count = $(ele).find("td .change-input").eq(1).val();
                                                const remark = $(ele).find("td .change-input").eq(3).val()
                                                const id = ele.find("td.id").text();
                                                let order_id = $('#order_id').val();
                                                Fast.api.ajax({
                                                    url:'order/order/ajax_edit',
                                                    data:{
                                                        order_count:order_count,
                                                        remark:remark,
                                                        supplier_id:Config.supplier_id,
                                                        send_time:Config.send_time,
                                                        department_id:Config.department_id,
                                                        id:id,
                                                        order_id:order_id,
                                                        price:price,
                                                        sendqty:sendqty
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调

                                                    alert(ret.msg);
                                                    console.log(ret);
                                                    var order_id = ret.data.order_id
                                                    console.log(order_id);
                                                    $("#order_id").val(order_id);
                                                    table.bootstrapTable('refresh', {});
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
                                        return row.status==1 || row.status==2 ? true : false;
                                    },
                                    title: __('点击执行事件'),
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data) {
                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const locatinObj = loadLoaction(); //地址数据
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);
                                                const goods_id = ele.find("td.goods_id").text();
                                                const id = ele.find("td.id").text();
                                                let order_id = $('#order_id').val();
                                                Fast.api.ajax({
                                                    url:'order/order/ajax_del',
                                                    data:{
                                                        id:id,
                                                        supplier_id:Config.supplier_id,
                                                        send_time:Config.send_time,
                                                        goods_id:goods_id,
                                                        order_id:order_id
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
                                                    table.bootstrapTable('refresh', {});
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
                            operate:false
                        }
                    ]
                ],

                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    //filter.后跟的是在ajax里使用的名称只需修改这两行
                    filter.order_id = Config.order_id;
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
            Controller.api.bindevent();
        },
        next3:function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    next_url:'order/order/next3' + location.search,
                    // index_url:'order/order/index' + loadLoaction.supplier_id,
                    // loadLoaction
                    // add_url: 'order/order/add',
                    // edit_url: 'order/order/edit',
                    // del_url: 'order/order/del',
                    // multi_url: 'order/order/multi',
                    table: 'order',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.next_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        // {checkbox: true},
                        // {field: 'index_id', title: __('序号'), formatter: function(value, row, index){
                        //         return ++index;
                        //     },operate:false,class:"index_id"},
                        {field: 'goods_id', title: __('ID'),operate:false,class:"goods_id"},
                        {field: 'goods_sn', title: __('商品编号'),operate:false},
                        {field: 'goods_name', title: __('商品名称'),operate: 'LIKE %...%'},
                        {field: 'spec', title: __('规格'),operate:false},
                        {field: 'unit', title: __('单位'),operate:false},
                        {field: 'price', title: __('单价'),operate:false,class:"price"},
                        {field: 'department.id', title: __('Department.id'),visible:false,operate:false},
                        {field: 'supplier.id', title: __('Supplier.id'),visible:false,operate:false},
                        {
                            field: 'order_count',
                            class: 'order_count',
                            title: '下单数量',
                            operate:false,
                            formatter:(value)=>{
                                return `<input value='${!value?"":value}' 
                                               type="text" class="change-input" data-type='order_count'>`
                            }
                        },
                        {
                            field: 'order_amount',
                            class:"order_amount",
                            title: '下单金额',
                            operate:false
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
                            width: "150px",
                            title: '添加',
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'click',
                                    title: __('点击执行事件'),
                                    text:'点击添加',
                                    hidden:function(row){
                                        return row.send_status==1? true : false;
                                    },
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data) {
                                        layer.confirm("确认更新数据",
                                            {btn: ['确定', '取消']}, function () {
                                                const locatinObj = loadLoaction(); //地址数据
                                                const ele = $($(data.tableId).find("tr")[data.rowIndex+1]);
                                                const order_count = $(ele).find("td .change-input").eq(0).val();
                                                const remark = $(ele).find("td .change-input").eq(1).val()
                                                const goods_id = ele.find("td.goods_id").text();
                                                let order_id = $('#order_id').val();
                                                Fast.api.ajax({
                                                    url:'order/order/next_add',
                                                    data:{
                                                        order_count:order_count,
                                                        remark:remark,
                                                        supplier_id:Config.supplier_id,
                                                        send_time:Config.send_time,
                                                        department_id:Config.department_id,
                                                        goods_id:goods_id,
                                                        order_id:order_id
                                                    }
                                                }, function(data, ret){
                                                    //成功的回调
                                                    alert(ret.msg);
                                                    console.log(ret);
                                                    var order_id = ret.data.order_id
                                                    console.log(order_id);
                                                    $("#order_id").val(order_id);
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
                            operate:false
                        }
                    ]
                ],
                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    //filter.后跟的是在ajax里使用的名称只需修改这两行
                    filter.order_id = Config.order_id;
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
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"),function(data,ret){
                    console.log(data);//需要携带的参数
                    // console.log(ret);
                    var obj = new Function("return" + data)();//转换后的JSON对象
                    var url = ret.url+'?send_time='+obj.send_time+'&department_id='+obj.department_id+'&supplier_id='+obj.supplier_id+'&cate_id='+obj.cate_id;
                    // console.log(url)
                    // console.log(obj.send_time);//json name
                    Backend.api.addtabs(url,"新增订单","fa fa-circle-o fa-fw");//新建选项卡
                });
                Form.api.bindevent($("#add-form"), function(data, ret){//绑定时间
                    //给表单绑定新的回调函数 接收 控制器 success(msg,url,data)或者error(msg,url,data)
                    
                    Fast.api.close(data);//在这里关闭当前弹窗
                    parent.location.reload();//这里刷新父页面，可以换其他代码
                    // parent.$("#table").bootstrapTable('refresh',{});
                    alert(ret.msg);
                }, function(data, ret){
                   console.error("错误");
                });
            }   
        },
         today:function(AddDayCount){
            var dd = new Date();
            dd.setDate(dd.getDate()+AddDayCount);
            var y = dd.getFullYear();
            var m = dd.getMonth()+1;
            var d= dd.getDate();
            //判断 月
            if(m < 10){
                m = "0" + m;
            }else{
                m = m;
            }
            //判断 日
            if (d < 10){
                d = "0" + d;
            }else{
                d = d;
            }
            return y+"-"+m+"-"+d;
        }
    };
      var day = new Date();
    day.setTime(day.getTime()+24*60*60*1000);
    day = day.getFullYear()+"-" + (day.getMonth()+1) + "-" + day.getDate()+" 00:00";
    var day1 = new Date();
    day1.setTime(day1.getTime());
    var s1 = day1.getFullYear()+"-" + (day1.getMonth()+1) + "-" + day1.getDate()+" 00:00";
    $("#c-sendtime").attr("data-date-min-date",s1);
    $("#c-sendtime").attr("data-date-default-date",day);
    $("#c-sendtime").datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $("#c-sendtime2").datetimepicker({
        format: 'YYYY-MM-DD',
    });
    $('#c-sendtime2').on('dp.change',function(e){
        console.log($(this).val());
        let order_id = $('#order_id').val();
        let send_time = $(this).val();
        console.log(order_id);
        Fast.api.ajax({
            url:'order/order/ajax_time',
            data:{
                send_time:send_time,
                order_id:order_id
            }
        }, function(data, ret){
            //成功的回调
            alert(ret.msg);
            return false;
        }, function(data, ret){
            //失败的回调
            alert(ret.msg);
            return false;
        });
    });
    return Controller;
});

$("#table").on("blur",".change-input",function(e){
    const type = $(e.target).attr("data-type");
    const value = $(e.target).val();
    console.log(value);
    const obj = loadLoaction();
    const parents = $(e.target).parents("tr");
    const priceVal = parents.find("td.price input").val()||parents.find("td.price").text();
    const ordercountVal = parents.find("td.order_count input").val()||parents.find("td.order_count").text();
    switch (type){
        case 'order_count':
            parents.find("td.order_amount").text((Number(value)*priceVal));
            break;
        case 'price':
            parents.find("td.order_amount").text((Number(value)*ordercountVal));
            break;
        case 'takeorder_count':
            parents.find("td.takeorder_amount").text((Number(value)*priceVal));
            break;

    }
});

$("#table").on("click",".btn-addprint",function(e){
    const patent = $(e.target).parents("tr");
    console.log(patent.find("td").eq(2).text());
     console.log(patent.find("td").eq(1).text());
    Fast.api.ajax({
        url:'order/order/pr_order',
        data:{
            index_id:patent.find("td").eq(1).text(),
            id:patent.find("td").eq(2).text()
        }
    }, function(data, ret){
        //成功的回调
        console.log(data,ret);
        printTable(data);
        return false;
    }, function(data, ret){
        //失败的回调
        alert(ret.msg);
        return false;
    });
    setTimeout(()=>{

    },300);

});

function printTable(data){

    const list= data.info;
    $("#printView tbody,.top-list-item>span").empty();
    $(".top-list-item [data-type='部门']").text(data.department_name||"");
    $(".top-list-item [data-type='下单时间']").text(data.createtime||"");
    // $(".top-list-item [data-type='送货时间']").text(renderDate(data.sendtime)||"");
    $(".top-list-item [data-type='供应商']").text(data.supplier_name||"");
    // $(".top-list-item [data-type='联系人']").text(data.linkman||"");
    // $(".top-list-item [data-type='联系电话']").text(data.mobile||"");
    $(".top-list-item [data-type='类别']").text(data.cate_name||"");
    $("[data-type='order_sn']").text(data.order_sn||"");
    // let total = 0;
    if(list.length>0){
        list.map((item,index)=>{
           $("#printView tbody").append(`<tr>
            <td>${index+1}</td>
            <td>${item.goods_name}</td>
            <td>${item.unit}</td>
            <td>${item.needqty}</td>
            <td>${item.sendqty}</td>
            <td colspan="2">${item.price}</td>
            <td>${item.send_price}</td>
            </tr>`);
            // total += Number(item.send_price);
        });
    }else{
        $("#printView tbody").append(`<tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td colspan="2"></td>
        <td></td>
        </tr>`);

    }
    $("#printView tbody").append(`<tr>
        <td>小计大写</td>
        <td colspan="4" style="text-align: left;">${data.cn_amount}</td>
        <td>小计:</td>
        <td colspan="3">${data.amount}</td>
        </tr>`);
    var newStr = $("#printView").html();//获取打印部分
    var win = window.open("","新建打印窗口","height=500,width=700,top=100");//新建窗口
    win.document.body.innerHTML = newStr;//打印内容写到新建窗口中
    win.print();//执行打印
    win.close();
}

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

function renderDate(datetime){
    if(!datetime){
        return "";
    }
    var day = new Date(datetime*1000);
    return day.getFullYear()+"-" + (day.getMonth()<10?"0"+day.getMonth():day.getMonth()) + "-" + (day.getDate()<10?"0"+day.getDate():day.getDate());
}