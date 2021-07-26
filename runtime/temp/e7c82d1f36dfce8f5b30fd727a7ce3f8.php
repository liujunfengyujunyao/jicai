<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"/data/www/jicai.sxxd365.com/public/../application/admin/view/goods/goods/edit.html";i:1626686966;s:70:"/data/www/jicai.sxxd365.com/application/admin/view/layout/default.html";i:1626686927;s:67:"/data/www/jicai.sxxd365.com/application/admin/view/common/meta.html";i:1626686926;s:69:"/data/www/jicai.sxxd365.com/application/admin/view/common/script.html";i:1626686926;}*/ ?>
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
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Goods_name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-goods_name" data-rule="required" class="form-control" name="row[goods_name]" type="text" value="<?php echo htmlentities($row['goods_name']); ?>">
        </div>
    </div>
    <!--<div class="form-group">-->
        <!--<label class="control-label col-xs-12 col-sm-2"><?php echo __('Goods_sn'); ?>:</label>-->
        <!--<div class="col-xs-12 col-sm-8">-->
            <!--<input id="c-goods_sn" data-rule="required" class="form-control" name="row[goods_sn]" type="text" value="<?php echo htmlentities($row['goods_sn']); ?>">-->
        <!--</div>-->
    <!--</div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Spec'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-spec" data-rule="required" class="form-control" name="row[spec]" type="text" value="<?php echo htmlentities($row['spec']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Unit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-unit" data-rule="required" class="form-control" name="row[unit]" type="text" value="<?php echo htmlentities($row['unit']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Cate_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-cate_id" data-rule="required" data-source="goods/category/first_cate" data-primary-key="id"
                   data-field="category_name" class="form-control selectpage" name="row[cate_id]" type="text" value="<?php echo htmlentities($row['cate_id']); ?>" style="display:block;">

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Scate_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-scate_id" data-rule="required" data-source="goods/category/second_cate" data-primary-key="id"
                   data-field="category_name" class="form-control selectpage" name="row[scate_id]" type="text" value="<?php echo htmlentities($row['scate_id']); ?>" style="display:block;">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Is_stock'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-is_stock" data-rule="required" class="form-control selectpicker" name="row[is_stock]">
                <?php if(is_array($isStockList) || $isStockList instanceof \think\Collection || $isStockList instanceof \think\Paginator): if( count($isStockList)==0 ) : echo "" ;else: foreach($isStockList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['is_stock'])?$row['is_stock']:explode(',',$row['is_stock']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">包装类型:</label>
        <div class="col-xs-12 col-sm-8">

            <select  id="c-packaging_type" data-rule="required" class="form-control selectpicker" name="row[packaging_type]">
                <?php if(is_array($packagingTypeList) || $packagingTypeList instanceof \think\Collection || $packagingTypeList instanceof \think\Paginator): if( count($packagingTypeList)==0 ) : echo "" ;else: foreach($packagingTypeList as $key=>$vo): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['packaging_type'])?$row['packaging_type']:explode(',',$row['packaging_type']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>
            <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['status'])?$row['status']:explode(',',$row['status']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>

        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Remark'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-remark" class="form-control" name="row[remark]" type="text" value="<?php echo htmlentities($row['remark']); ?>">
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
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