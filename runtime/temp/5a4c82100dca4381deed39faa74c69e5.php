<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"D:\WWW\zbjg\public/../application/admin\view\stock\deliverygoods\delivery_edit.html";i:1606800284;s:54:"D:\WWW\zbjg\application\admin\view\layout\default.html";i:1604979994;s:51:"D:\WWW\zbjg\application\admin\view\common\meta.html";i:1604979993;s:53:"D:\WWW\zbjg\application\admin\view\common\script.html";i:1604979993;}*/ ?>
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
    .panel.panel-default.panel-intro{
        margin-left: -15px;
    }
    .change-input {
        width: 100px;
        text-align: center;
    }

    .commonsearch-table {
        display: none;
    }
    form.form-horizontal .control-label {
        font-weight: normal;
        padding-right: 0;
        padding-left: 0;
    }
    input:read-only{
        background: none!important;
        border: none;
    }
    .datetimepicker{
        border: 1px solid #ccc!important;
    }
</style>
<div class="panel panel-default panel-intro">
    <?php echo build_heading(); ?>
    <div class="commonsearch-table" style="display: block;">
        <form class="form-horizontal form-commonsearch nice-validator n-default n-bootstrap" novalidate="" method="post"
              action="">
            <div class="row">
                <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-3">
                    <label for="department.name" class="control-label col-xs-4">
                        领料部门
                    </label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" value="<?php echo $department_name; ?>" readonly>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-3">
                    <label for="department.name" class="control-label col-xs-4">
                        领料人
                    </label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" value="<?php echo $apply_name; ?>" readonly>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-3">
                    <label for="department.name" class="control-label col-xs-4">
                        领料时间
                    </label>
                    <div class="col-xs-8">
                        <input type="text" class="form-control" value="<?php echo $createtime; ?>" readonly>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-6 col-md-4 col-lg-3">
                    <label for="department.name" class="control-label col-xs-4">
                        状态
                    </label>
                    <div class="col-xs-8">
                        <input id="delivery_status" type="text" class="form-control" value="<?php echo $status; ?>" readonly>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding">
                    <div id="toolbar" class="toolbar">
                        <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                        <a href="/admin.php/stock/deliverygoods/next?delivery_id=<?php echo $delivery_id; ?>"  id="next" data-area='["100%","100%"]' class="btn btn-success btn-add <?php echo $auth->check('stock/deliverygoods/delivery_edit')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> <?php echo __('添加领料商品'); ?></a>
                        <!--                        <a href="javascript:;" class="btn btn-success btn-edit btn-disabled disabled <?php echo $auth->check('stock/delivery_goods/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Edit'); ?></a>-->
                        <!--                        <a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('stock/delivery_goods/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>-->
                        <!--                        <a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('stock/delivery_goods/import')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a>-->

                        <!--                        <div class="dropdown btn-group <?php echo $auth->check('stock/delivery_goods/multi')?'':'hide'; ?>">-->
                        <!--                            <a class="btn btn-primary btn-more dropdown-toggle btn-disabled disabled" data-toggle="dropdown"><i class="fa fa-cog"></i> <?php echo __('More'); ?></a>-->
                        <!--                            <ul class="dropdown-menu text-left" role="menu">-->
                        <!--                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=normal"><i class="fa fa-eye"></i> <?php echo __('Set to normal'); ?></a></li>-->
                        <!--                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=hidden"><i class="fa fa-eye-slash"></i> <?php echo __('Set to hidden'); ?></a></li>-->
                        <!--                            </ul>-->
                        <!--                        </div>-->


                    </div>

                    <input type="hidden" id="delivery_id" value="<?php echo $delivery_id; ?>">
                    <table id="table" class="table table-striped table-bordered table-hover table-nowrap"
                           data-operate-edit="<?php echo $auth->check('stock/delivery_goods/edit'); ?>"
                           data-operate-del="<?php echo $auth->check('stock/delivery_goods/del'); ?>"
                           width="100%">
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>