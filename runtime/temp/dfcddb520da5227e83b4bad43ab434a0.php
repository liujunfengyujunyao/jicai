<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:71:"/www/wwwroot/ssh/public/../application/admin/view/auth/group/scope.html";i:1617076903;s:59:"/www/wwwroot/ssh/application/admin/view/layout/default.html";i:1616141118;s:56:"/www/wwwroot/ssh/application/admin/view/common/meta.html";i:1616141118;s:58:"/www/wwwroot/ssh/application/admin/view/common/script.html";i:1616141118;}*/ ?>
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
                                <style>
.order-list > label{
   display: block;
}
</style>
<form id="edit-form" class="form-horizontal form-ajax" role="form" method="POST" action="">
    <?php echo token(); ?>
    <input type="hidden" name="row[rules]" value="" />
    <div class="form-group" hidden>
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Parent'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_select('row[pid]', $groupdata, $row['pid'], ['class'=>'form-control selectpicker', 'data-rule'=>'required', 'data-id'=>$row['id'], 'data-pid'=>$row['pid']]); ?>
        </div>
    </div>
    <!--    <div class="form-group">-->
    <!--        <label for="c-type" class="control-label col-xs-12 col-sm-2">所属部门:</label>-->
    <!--        <div class="col-xs-12 col-sm-8">-->

    <!--            <select id="c-type" data-rule="required" class="form-control selectpicker" name="row[department_id]">-->
    <!--                <?php if(is_array($departmentList) || $departmentList instanceof \think\Collection || $departmentList instanceof \think\Paginator): if( count($departmentList)==0 ) : echo "" ;else: foreach($departmentList as $key=>$vo): ?>-->
    <!--                <option value="<?php echo $vo['id']; ?>" <?php if(in_array(($vo['id']), is_array($row['department_id'])?$row['department_id']:explode(',',$row['department_id']))): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>-->
    <!--                <?php endforeach; endif; else: echo "" ;endif; ?>-->
    <!--            </select>-->

    <!--        </div>-->
    <!--    </div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">可查看供应商:</label>
        <div class="col-xs-12 col-sm-8">

            <div class="radio">
                <?php if(is_array($supplierList) || $supplierList instanceof \think\Collection || $supplierList instanceof \think\Paginator): if( count($supplierList)==0 ) : echo "" ;else: foreach($supplierList as $key=>$v): ?>
                <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[supplier_ids][]" type="checkbox" value="<?php echo $v['id']; ?>" <?php if(in_array(($v['id']), is_array($supplier_checked)?$supplier_checked:explode(',',$supplier_checked))): ?>checked<?php endif; ?> /> <?php echo $v['supplier_name']; ?></label>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">可查看部门订单:</label>
        <div class="col-xs-12 col-sm-8">

            <div class="radio order-list">
                <?php if(is_array($departmentList) || $departmentList instanceof \think\Collection || $departmentList instanceof \think\Paginator): if( count($departmentList)==0 ) : echo "" ;else: foreach($departmentList as $key=>$vo): ?>
                <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[department_ids][]" type="checkbox" value="<?php echo $vo['id']; ?>" <?php if(in_array(($vo['id']), is_array($department_checked)?$department_checked:explode(',',$department_checked))): ?>checked<?php endif; ?> /> <?php echo $vo['name']; ?></label>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>
    <div class="form-group hidden layer-footer">
        <label class="control-label col-xs-12 col-sm-2 col-xs-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>