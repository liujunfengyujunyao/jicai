<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:77:"D:\WWW\servercopy\ssh\public/../application/admin\view\order\order\index.html";i:1627021782;s:64:"D:\WWW\servercopy\ssh\application\admin\view\layout\default.html";i:1627021771;s:61:"D:\WWW\servercopy\ssh\application\admin\view\common\meta.html";i:1627021770;s:63:"D:\WWW\servercopy\ssh\application\admin\view\common\script.html";i:1627021770;}*/ ?>
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
    
    <div class="panel-heading">
        <?php echo build_heading(null,FALSE); ?>
        <ul class="nav nav-tabs" data-field="status">
            <li class="active"><a href="#t-all" data-value="" data-toggle="tab"><?php echo __('All'); ?></a></li>
            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>
            <li><a href="#t-<?php echo $key; ?>" data-value="<?php echo $key; ?>" data-toggle="tab"><?php echo $vo; ?></a></li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>


    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">
                <div class="widget-body no-padding">
                    <div id="toolbar" class="toolbar">
                        <a href="javascript:;" class="btn btn-primary btn-refresh" title="<?php echo __('Refresh'); ?>" ><i class="fa fa-refresh"></i> </a>
                        <a href="javascript:;" class="btn btn-success btn-add <?php echo $auth->check('order/order/add')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> ????????????</a>
                        <a href="order/order/daoru" class="btn btn-success btn-dialog  <?php echo $auth->check('order/order/add')?'':'hide'; ?>" title="????????????"><i class="fa fa-user-plus"></i> ????????????</a>
                        <a class="btn btn-success btn-myexcel-export  btn-disabled disabled" href="javascript:;"><i class="fa fa-user"></i> ??????</a><!--?????????????????????btn-myexcel-export??????????????????????????????????????????????????????????????????????????????????????? exportOrderExcel???????????????????????????-->
                        <a href="javascript:;" class="btn btn-success btn-chociePrint" title="????????????" >????????????</a>
<!--                        <a href="javascript:;" class="btn btn-success btn-next <?php echo $auth->check('order/order/next')?'':'hide'; ?>" title="<?php echo __('Add'); ?>" ><i class="fa fa-plus"></i> ?????????</a>-->
<!--                        <a href="javascript:;" class="btn btn-success btn-edit btn-disabled disabled <?php echo $auth->check('order/order/edit')?'':'hide'; ?>" title="<?php echo __('Edit'); ?>" ><i class="fa fa-pencil"></i> <?php echo __('Edit'); ?></a>-->
<!--                        <a href="javascript:;" class="btn btn-danger btn-del btn-disabled disabled <?php echo $auth->check('order/order/del')?'':'hide'; ?>" title="<?php echo __('Delete'); ?>" ><i class="fa fa-trash"></i> <?php echo __('Delete'); ?></a>-->
<!--                        <a href="javascript:;" class="btn btn-danger btn-import <?php echo $auth->check('order/order/import')?'':'hide'; ?>" title="<?php echo __('Import'); ?>" id="btn-import-file" data-url="ajax/upload" data-mimetype="csv,xls,xlsx" data-multiple="false"><i class="fa fa-upload"></i> <?php echo __('Import'); ?></a>-->

<!--                        <div class="dropdown btn-group <?php echo $auth->check('order/order/multi')?'':'hide'; ?>">-->
<!--                            <a class="btn btn-primary btn-more dropdown-toggle btn-disabled disabled" data-toggle="dropdown"><i class="fa fa-cog"></i> <?php echo __('More'); ?></a>-->
<!--                            <ul class="dropdown-menu text-left" role="menu">-->
<!--                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=normal"><i class="fa fa-eye"></i> <?php echo __('Set to normal'); ?></a></li>-->
<!--                                <li><a class="btn btn-link btn-multi btn-disabled disabled" href="javascript:;" data-params="status=hidden"><i class="fa fa-eye-slash"></i> <?php echo __('Set to hidden'); ?></a></li>-->
<!--                            </ul>-->
<!--                        </div>-->

                        
                    </div>
                    <table id="table" class="table table-striped table-bordered table-hover table-nowrap"

                           data-operate-confirm_order="<?php echo $auth->check('order/order/confirm_order'); ?>"
                           data-operate-cancel_order="<?php echo $auth->check('order/order/cancel_order'); ?>"
                           data-operate-addtabs="<?php echo $auth->check('order/order/addtabs'); ?>"
                           width="100%">
                    </table>
                </div>
            </div>

        </div>
    </div>
    <div id="printView" style="display: none;">
        <style media="print"> 
          #viewss{
              font-family: "????????????";
          }
          @page
            {   
                size: 215mm 145mm;   /* auto is the initial value */
                margin:0mm 10mm;
                margin-right: 5mm;  
            }
            body /* ????????????????????????????????????????????????????????????????????? */
            {
                background-color:#FFFFFF;
                border: solid 0px black ;
                margin: 6px;  /* this affects the margin on the content before sending to printer */
            }
        </style> 
        <style>
           .top-title{text-align:center;font-size: 26px;}
           .top-list{width:100%;margin-bottom: 5px;display: flex;justify-content: space-around;}
           .top-list:nth-child(even){margin-bottom: 10px;}
           .top-list-item{float:left;display:flex;font-weight: normal;}
           /* .top-list-item:nth-child(2){width:18%}
           .top-list-item:nth-child(3){width:36%}
           .top-list-item:nth-child(4){width:14%} */
           .top-list-item>label{display: inline-block;color: #333;font-weight: normal;}
           .top-list-item>span{display: inline-block;text-align: center;font-size: 13px;}
           .top-list-item:nth-child(2)>span{width: 100%;}
           .bottom-list-item{width:20%;float:left;font-size: 15px;font-weight: 400;color:#333}
           .bottom-list-item:nth-child(1){width: 35%;}
           .bottom-list-item>span{padding:0 20px}
           .bottom-list-item>span:nth-child(1){padding-left:60px}
           .layui-table{border-collapse:collapse;border-spacing:0;margin: 10px 0 5px;width:100%;background-color:#fff;color:#333}
           .layui-table th{white-space:pre-wrap}
           .layui-table td,.layui-table th{word-wrap:break-word;border:1px solid #999!important;text-align:center;padding:0px 5px;height: 22px;
    font-weight: normal;}
           /* .endbox:nth-child(odd){page-break-before:always;} */
           .endbox{page-break-before:always;}
           .endbox{padding-top: 30px;}
        </style>
        <div id="viewss">

        </div>
        <!-- <div class="top-title">????????????????????????????????????????????????</div>
        <div style="text-align: right;"><span data-type="order_sn"></span></div>
        <div class="top-list">
          <div class="top-list-item"><label>?????????</label><span data-type="??????"></span></div>
          <div class="top-list-item"><span data-type="????????????"></span></div>
          <div class="top-list-item"><label>????????????</label><span data-type="?????????"></span></div>
          <div class="top-list-item"><label>?????????</label><span data-type="??????"></span></div>
        </div>
        <table class="layui-table">
            <colgroup>
            <col width="80">
            <col width="160">
            <col width="60">
            <col width="100">
            <col width="100">
            <col width="50">
            <col width="50">
            <col width="100">
            <col>
            </colgroup>
          <thead>
           <tr>
              <th>??????</th>
              <th>????????????</th>
              <th>??????</th>
              <th>????????????</th>
              <th>????????????</th>
              <th colspan="2">??????</th>
              <th>??????</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <div class="top-list">
            <div class="bottom-list-item">???????????????</div>
            <div class="bottom-list-item">????????????</div>
            <div class="bottom-list-item">???????????????</div>
            <div class="bottom-list-item">????????????</div>
            <div class="bottom-list-item">????????????</div>
        </div>
      </div> -->
</div>
<!-- <script src="/lodop/LodopFuncs.js"></script>
<object  id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
    <embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</object> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>