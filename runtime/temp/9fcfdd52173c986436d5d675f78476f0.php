<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"D:\WWW\servercopy\ssh\public/../application/admin\view\order\order\order_list.html";i:1627032858;s:64:"D:\WWW\servercopy\ssh\application\admin\view\layout\default.html";i:1627021771;s:61:"D:\WWW\servercopy\ssh\application\admin\view\common\meta.html";i:1627021770;s:63:"D:\WWW\servercopy\ssh\application\admin\view\common\script.html";i:1627021770;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEMO</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://sh.liujunfeng.work/assets/css/backend.css">
</head>
<style>
    body {
        font-size: 13px!important;
        background: #f1f4f6;
    }
    .panel-intro{
        height: calc(100% - 70px);
    }
    pre {
        display: none;
    }
    .content{
        min-height: auto;
    }
    .row {
        margin-left: -15px;
        margin-right: -15px;
    }
  
    #ribbon {
        overflow: hidden;
        padding: 15px 15px 0 15px;
        position: relative;
    }

    .btn-warning {
        color: #fff !important;
        background-color: #f39c12;
        border-color: #f39c12;
    }

    .btn-warning:focus,
    .btn-warning.focus {
        color: #fff;
        background-color: #c87f0a;
        border-color: #7f5006;
    }

    .btn {
        padding: 1px 5px;
        font-size: 11px;
        line-height: 1.5;
        border-radius: 2px;
        color: #fff !important;
        ;
    }

    td>input {
        overflow: visible;
        width: 90px;
    }
    .fixed-table-body{
        height: calc(100% - 120px);
    }
    #table{font-size: 13px;}
</style>

<body>

    <div class="panel panel-default panel-intro">
        <div class="panel-body">
            <table id="table" class="table table-striped table-bordered table-hover table-nowrap">
            </table>
            <div class="content" style="position: relative;">
                <p>订单总金额：<span class="order-price"></span></p>
                <p>订单备注：<span class="order-remark"><input type="text" class="order_remark" value="" onblur="" style="width: 30rem;"></span></p>
                <p>收货地址：<?php echo $address; ?></p>
                <p>送货部门：<span ></span><?php echo $department_name; ?></p>
                <p>送货时间：<span ></span><?php echo $send_time; ?></p>
                <div style="position: absolute;right: 0;top: 10;margin: 0;float: right;"> 
                    <button onclick="tableArr.submit()" class="btn btn-success" style="padding: 5px 12px;border-radius: 4px;">提交</button>
                    <button onclick="tableArr.exit()" class="btn btn-default"  style="padding: 5px 12px;border-radius: 4px;color: #333!important;margin-left:1rem">取消</button>
                </div>
            </div>

        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.15.3/dist/bootstrap-table.min.js"></script>
    <script>

        var data = [];

        tableArr = {
            data: data,
            ids:null,
            sku:[],
            table: $('#table'),
            init() {
                this.ids = location.search.split("&").filter(v=>{return v.includes("supplier_id")})[0].split("=")[1]
                this.table.bootstrapTable({
                    columns: [{
                        field: 'id',
                        title: '序号'
                    }, {
                        field: 'goods_name',
                        title: '商品名称'
                    }, {
                        field: 'goods_sn',
                        title: '商品编号'
                    }, {
                        field: 'supplier_name',
                        title: '供应商'
                    }, {
                        field: 'spec',
                        title: '规格'
                    }, {
                        field: 'unit',
                        title: '单位'
                    }, {
                        field: 'price',
                        title: '价格'
                    }, {
                        field: 'order_count',
                        title: '数量',
                        formatter: function (val, obj, index) {
                            return `<input type='text' class="order_count" value='${val}'  onblur="tableArr.editCount(${index})">`;
                        }
                    }, {
                        field: 'first_class',
                        title: '一级分类',
                    }, {
                        field: 'pack_type',
                        title: '包装类型',
                    }, {
                        field: 'remark',
                        title: '商品备注',
                        formatter: function (val, obj, index) {
                            return `<input type='text' value='${val}' class="remark" onblur="tableArr.editRemark(${index})">`;
                        }
                    }, {
                        field: '操作',
                        title: '操作',
                        formatter: function (val, obj, index) {
                            return `<a class="btn btn-xs btn-warning btn-dialog remove-tr"
                         onclick="tableArr.del(${obj.id})">
                        <i class="fa fa-folder-o"></i>删除</a>`;
                        }
                    }
                    ],
                    data: data,
                    formatNoMatches:function(){
                        return "没有查询到数据";
                    }
                });
                this.addLastTr();
            },
            add(obj){
                if(!this.sku.includes(obj.id)){
                    this.sku.push(obj.id);
                }
                obj.order_count = 0;
                obj.remark = "";
                this.table.bootstrapTable('insertRow', {index: 0, row: obj});
                this.data.unshift(obj);
                this.addLastTr();
                $(".layui-layer-close").trigger("click");
            },
            addAdress(id,address){
                
                $(".addressText").text(address).attr("data-id",id);
                $(".layui-layer-iframe").remove();
            },
            del(id) {
                this.table.bootstrapTable('remove', { field: "id", values: [parseInt(id)] });
                this.data = this.data.filter(v=>{
                   return v.id!=id;
                });
                if(this.sku.includes(id)){
                    this.sku.splice(this.sku.indexOf(id),1);
                }
                this.addLastTr();
            },
            submit(){
                const department_id = location.search.split("&").filter(v=>{return v.includes("department_id")})[0].split("=")[1];
                const send_time = location.search.split("&").filter(v=>{return v.includes("send_time")})[0].split("=")[1]
                let arr = this.data.filter(v=>{return v.order_count=="0"});
                if(this.data.length===0){
                    layer.alert("请选择商品");
                    return;
                }
                if(arr.length!=0 ){
                    layer.alert("请选择商品数量");
                    return;
                }
                if(!$(".addressText").attr("data-id")){
                    layer.alert("请选择地址");
                    return;
                }
                console.log(this.data);
                let sendData = [];
                this.data.map(v=>{
                    let obj = {};
                    obj.sku = v.id;
                    obj.count = v.order_count;
                    obj.remark = v.remark;
                    sendData.push(obj);
                });
                $.ajax({
                    url:"order/order/order_submit",
                    data:{
                        list:sendData,
                        address:$(".addressText").attr("data-id"),
                        order_remark: $(".order_remark").val(),
                        order_amount: $(".order-price").text(),
                        department_id:department_id,
                        send_time:send_time
                    },
                    type:'post',
                    success:(res)=>{
                        layer.alert(res.msg);
                       if(res.msg=="创建完成"){
                         tableArr.exit()
                       }

                    },
                    erro:(err)=>{
                        console.log(err);
                    }
                });

            },
            exit(){
                parent.location.href = location.origin + "/admin.php/order/order?ref=addtabs";
            },
            editCount(index) { //修改备注
                const val = this.table.find("tbody tr").eq(index).find(".order_count").val();
                this.table.bootstrapTable('updateCell', {index: index, field: 'order_count', value: val});
                this.addLastTr();
            },
            editRemark(index) { //修改备注
                const val = this.table.find("tbody tr").eq(index).find(".remark").val();
                this.table.bootstrapTable('updateCell', {index: index, field: 'remark', value: val});
                this.addLastTr();
            },
            addorder(){
                //const ids = location.search.split("&").filter(v=>{return v.includes("supplier_id")})[0].split("=")[1];
                layer.open({
                    type: 2,
                    content: `next`,
                    area: ['100%', '100%']
                });
            },
            addLastTr() {

                let sumprice = this.data.reduce(function (total, currentValue, currentIndex, arr) {
                    return total + Number(currentValue.order_count);
                }, 0);
                let totalprice = this.data.reduce(function (total, currentValue, currentIndex, arr) {
                    return total + (Number(currentValue.price)*Number(currentValue.order_count));
                }, 0);
                $(".order-price").text(totalprice.toFixed(2));
                this.table.find("tbody").append(`<tr data-index="update">
                    <td style="">合计</td>
                    <td style=""></td>
                    <td style=""></td>
                    <td style=""></td>
                    <td style=""></td>
                    <td style=""></td>
                    <td style=""></td>
                    <td>${sumprice}</td>
                    <td></td><td></td><td></td>
                    <td>
                        <a class="btn btn-xs btn-success btn-dialog" data-url="order/order/next?supplier_id=${tableArr.ids}&sku=${tableArr.sku.join(",")}">
                        <i class="fa fa-folder-o"></i>新增</a>
                    </td>
                </tr>`);
            }

        }
        tableArr.init();

    </script>
</body>

</html>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>