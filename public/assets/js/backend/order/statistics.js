define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/statistics/index' + location.search,
                    // add_url: 'order/order/add',
                    // edit_url: 'order/order/edit',
                    // del_url: 'order/order/del',
                    multi_url: 'order/order/multi',
                    table: 'order',
                }
            });

            var table = $("#table");
            table.on("pre-body.bs.table",function(){
                var bt=table.data("bootstrap.table");
                if (bt){
                    bt.$toolbar.find(".export").find(".icon-share").text("  导出  ");
                }
            });
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'id', stitle: __('Id'),operate:false},
                        // {field: 'goods_id', title: __('Id'),operate:false},
                        {field: 'index_id', title: __('序号'), formatter: function(value, row, index){
                                return ++index;
                            },operate:false},
                        {field: 'cate_name', title: '一级分类',operate:false},
                        {field: 'scate_name', title: '二级分类',operate:false},
                        {field: 'goods_sn', title: '商品编码',operate:false},
                        {field: 'goods_name', title: '商品名称',operate:false},
                        {field: 'spec', title: '规格',operate:false},
                        {field: 'unit', title: '单位',operate:false},
                        {field: 'mean', title: '平均单价',operate:false},
                        {field: 'needqty', title: '订单数量',operate:false},
                        {field: 'order_price', title: '订单金额',operate:false},
                        {field: 'sendqty', title: '收货数量',operate:false},
                        {field: 'send_price', title: '收货金额',operate:false},
                        // {field: 'order_amount', title: __('Order_amount'), operate:'BETWEEN'},
                        {field: 'createtime', title: '下单时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,visible:false},
                        {field: 'sendtime', title: '送货时间', operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime,datetimeFormat:'YYYY-MM-DD',visible:false,defaultValue:this.today(0)+' 00:00:00 - '+this.today(0)+' 23:59:59'},
                        // {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'department_id', title: '部门',visible:false,searchList: $.getJSON("order/statistics/department_list")},
                        {field: 'supplier_id', title: '供货商',visible:false,searchList: $.getJSON("order/statistics/supplier_list")},

                    ]
                ],

                search:false,
                showToggle: false,
                showColumns: false,
                searchFormVisible: true,
                // showExport: false
                exportTypes:['excel']

            });
            // 监听事件
            $(document).on("click", ".btn-myexcel-export", function () { //监听刚刚的按钮btn-myexcel-export的动作
                var myexceldata=table.bootstrapTable('getSelections');//获取选中的项目的数据 格式是json
                myexceldata=JSON.stringify(myexceldata);//数据转成字符串作为参数
                //直接url访问，不能使用ajax，因为ajax要求返回数据，和PHPExcel一会浏览器输出冲突！将数据作为参数
                top.location.href="statistics/exportStatisticsExcel?data="+myexceldata;
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
                Form.api.bindevent($("form[role=form]"),function(data,ret){
                    console.log(data);//需要携带的参数
                    // console.log(ret);
                    var obj = new Function("return" + data)();//转换后的JSON对象
                    var url = ret.url+'?send_time='+obj.send_time+'&department_id='+obj.department_id+'&supplier_id='+obj.supplier_id;
                    // console.log(url)
                    // console.log(obj.send_time);//json name
                    Backend.api.addtabs(url,{iframeForceRefresh: true});//新建选项卡
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
    return Controller;
});

