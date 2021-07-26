<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:71:"/www/wwwroot/ssh/public/../application/admin/view/auth/address/add.html";i:1626431036;s:59:"/www/wwwroot/ssh/application/admin/view/layout/default.html";i:1616141118;s:56:"/www/wwwroot/ssh/application/admin/view/common/meta.html";i:1616141118;s:58:"/www/wwwroot/ssh/application/admin/view/common/script.html";i:1616141118;}*/ ?>
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
                                <!-- <div class="input-row">
    <input type="radio" class="input-row" id="input-1"><label for="input-1">地址1</label>
</div>
<div class="input-row">
    <input type="radio" class="input-row" id="input-2"><label for="input-2">地址2</label>
</div> -->
<style>
    form.form-horizontal .control-label{
        font-weight: normal;
        text-align: right;
        margin-bottom: 0;
        margin-top: 0;
        padding-top: 7px;
    }
    label{
        margin: 10px;
    }
    @media screen and (max-height: 800px){
        .sp_result_area {
            max-height: 200px;
            overflow-y: scroll;
        }
    }
</style>
<!-- <div class="input-row">
    <input type="radio" name="input-row" id="input-own" onclick="addMet.confirmAdress()"><label for="input-own">使用新地址</label>
</div> -->
<form id="add-form" class="form-horizontal" role="form">
    <div class="form-group">
        <label class="control-label col-xs-2 col-sm-2">商户名称:</label>
        <div class="col-xs-8 col-sm-8">
            <input id="c-first_department" data-rule="required" data-source="auth/department/first_department"
                class="form-control selectpage" name="row[first_department]" type="text" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-2 col-sm-2">收货人:</label>
        <div class="col-xs-8 col-sm-8">
            <input id="receiver" data-rule="required"
            class="form-control" name="row[receiver]" type="text" value="" autocomplete="off">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-2 col-sm-2">收货人电话:</label>
        <div class="col-xs-8 col-sm-8">
            <input id="phone" maxlength="11" class="form-control" name="row[phone]" type="text" value="" autocomplete="off" data-rule="required;phone" data-rule-phone="[/^(1(([3456789][0-9])|(47)))\d{8}$/,'手机号格式错误']">
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-2 col-sm-2">地 址:</label>
        <div class="col-xs-8 col-sm-8">
            <input id="c-city" class="form-control" data-toggle="city-picker" name="row[city]" type="text" value="" data-responsive="true"/>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-2 col-sm-2">详细地址:</label>
        <div class="col-xs-8 col-sm-8">
            <input id="address" data-rule="required"
                class="form-control" name="row[address]" type="text" value="" autocomplete="off">
        </div>
    </div>


    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <!--            <button  class="btn btn-success btn-embossed"><?php echo __('OK'); ?></button>-->
            <button id="next" class="btn btn-success btn-embossed"><?php echo __('OK'); ?></button>
            <!--            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>-->
        </div>
    </div>
</form>
<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
<script>
     $("#next").click(function(){
            const obj = {
                address: $("#address").val(),
                department_id: $("#c-first_department").val(),
                phone: $("#phone").val(),
                receiver: $("#receiver").val(),
            }
             $.ajax({
                url: "auth/address/new_add",
                type: "post",
                data:obj,
                success: (res) => {
                    if(res.msg!=""){
                        layer.alert("添加成功");
                        parent.$(".layui-layer-iframe").remove()
                    }
                }
            });
            //

    });

    var addMet = {
        adress:{},
        data: [],
        init() {
            $.ajax({
                url: "index",
                type: "post",
                success: (res) => {
                    if (res.rows.length > 0) {
                        this.data = res.rows;
                        const ownRow = $("#input-own").parent(".input-row");
                        res.rows.map((v, index) => {
                            ownRow.prepend(`<div class="input-row">
                            <input type="radio" name="input-row" id="input-${index}" onclick="addMet.confirmAdress(${index})"><label for="input-${index}">${v.address}</label>
                        </div>`);
                        });
                    }
                }
            });
        },
        confirmAdress(index){
         if(index || index === 0){
            
            this.adress =  this.data[index];
            console.log(this.adress);
            // $("#next").removeClass("disabled");
            $("#add-form").hide();
         }else{
            $("#add-form").show();
            // $("#next").addClass("disabled");
            this.adress = {};
            $("#c-city").on("cp:updated", function() {
                $("#address").val($("#c-city").val().replace(/\//g,""));
            });
         }
          
        }
    }
    //addMet.init();
    $(function(){
        $("#c-city").on("cp:updated", function() {
                $("#address").val($("#c-city").val().replace(/\//g,""));
            });
    })
</script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>