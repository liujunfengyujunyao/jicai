<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"D:\WWW\servercopy\ssh\public/../application/admin\view\order\statistics\index.html";i:1627021782;s:64:"D:\WWW\servercopy\ssh\application\admin\view\layout\default.html";i:1627021771;s:61:"D:\WWW\servercopy\ssh\application\admin\view\common\meta.html";i:1627021770;s:63:"D:\WWW\servercopy\ssh\application\admin\view\common\script.html";i:1627021770;}*/ ?>
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
                                <div class="panel panel-default panel-intro">

<!--    <div class="panel-heading">-->
<!--        <?php echo build_heading(null,FALSE); ?>-->
<!--        <ul class="nav nav-tabs" data-field="status">-->
<!--            <li class="active"><a href="#t-all" data-value="" data-toggle="tab"><?php echo __('All'); ?></a></li>-->
<!--            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>-->
<!--            <li><a href="#t-<?php echo $key; ?>" data-value="<?php echo $key; ?>" data-toggle="tab"><?php echo $vo; ?></a></li>-->
<!--            <?php endforeach; endif; else: echo "" ;endif; ?>-->
<!--        </ul>-->
<!--    </div>-->


    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding">
                    <div id="toolbar" class="toolbar">
                        <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>

<!--                        <a class="btn btn-success btn-myexcel-export  btn-disabled disabled <?php echo $auth->check('lvtotals1/exportOrderExcel')?'':'hide'; ?>" href="javascript:;"><i class="fa fa-user"></i> ??????</a>&lt;!&ndash;?????????????????????btn-myexcel-export??????????????????????????????????????????????????????????????????????????????????????? exportOrderExcel???????????????????????????&ndash;&gt;-->

                    </div>
                    <table id="table" class="table table-striped table-bordered table-hover table-nowrap"
                           data-operate-edit="<?php echo $auth->check('order/order/edit'); ?>"
                           data-operate-del="<?php echo $auth->check('order/order/del'); ?>"
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