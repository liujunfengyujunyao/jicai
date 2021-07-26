<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:70:"/www/wwwroot/ssh/public/../application/admin/view/auth/admin/edit.html";i:1626678822;s:59:"/www/wwwroot/ssh/application/admin/view/layout/default.html";i:1616141118;s:56:"/www/wwwroot/ssh/application/admin/view/common/meta.html";i:1616141118;s:58:"/www/wwwroot/ssh/application/admin/view/common/script.html";i:1616141118;}*/ ?>
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
                                <form id="edit-form" class="form-horizontal form-ajax" role="form" data-toggle="validator" method="POST" action="">
    <?php echo token(); ?>

<!--    <div class="form-group">-->
<!--        <label for="c-type" class="control-label col-xs-12 col-sm-2">所属部门:</label>-->
<!--        <div class="col-xs-12 col-sm-8">-->

<!--            <select id="c-type" data-rule="required" class="form-control selectpicker" name="row[department_id]">-->
<!--                <?php if(is_array($department) || $department instanceof \think\Collection || $department instanceof \think\Paginator): if( count($department)==0 ) : echo "" ;else: foreach($department as $key=>$vo): ?>-->
<!--                <option value="<?php echo $vo['id']; ?>" <?php if(in_array(($vo['id']), is_array($row['department_id'])?$row['department_id']:explode(',',$row['department_id']))): ?>selected<?php endif; ?>><?php echo $vo['name']; ?></option>-->
<!--                <?php endforeach; endif; else: echo "" ;endif; ?>-->
<!--            </select>-->

<!--        </div>-->
<!--    </div>-->

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">所属部门:</label>
        <div class="col-xs-12 col-sm-8">
<!--            <input id="c-auth_ids" data-rule="required" data-source="auth/department/index" data-multiple="true" class="form-control selectpage" name="row[department_id]" type="text" value="">-->
            <input id="c-auth_ids" data-rule="required" data-source="auth/department/select_list" data-multiple="true" class="form-control selectpage" name="row[department_id]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="control-label col-xs-12 col-sm-2">用户名称:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="username" name="row[username]" value="<?php echo htmlentities($row['username']); ?>" data-rule="required;username" />
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="control-label col-xs-12 col-sm-2"><?php echo __('Email'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="email" class="form-control" id="email" name="row[email]" value="<?php echo htmlentities($row['email']); ?>" data-rule="required;email" />
        </div>
    </div>
    <div class="form-group">
        <label for="nickname" class="control-label col-xs-12 col-sm-2"><?php echo __('Nickname'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="nickname" name="row[nickname]" autocomplete="off" value="<?php echo htmlentities($row['nickname']); ?>" data-rule="required" />
        </div>
    </div>

<!--    <div>-->
<!--        <div style="margin-left: 100px;border:1px solid #000;background-color: #2c3e50;width:70px;color: #FFF;text-align: center; " id="repassword">密码重置</div>-->
<!--    </div>-->
    <div class="form-group">
        <label for="password" class="control-label col-xs-12 col-sm-2"><?php echo __('Password'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="password" class="form-control" id="password" name="row[password]" autocomplete="new-password" value="" placeholder="<?php echo __('不修改密码请留空'); ?>" />
        </div>
    </div>
    <!--<div class="form-group">-->
        <!--<label for="loginfailure" class="control-label col-xs-12 col-sm-2"><?php echo __('Loginfailure'); ?>:</label>-->
        <!--<div class="col-xs-12 col-sm-8">-->
            <!--<input type="number" class="form-control" id="loginfailure" name="row[loginfailure]" value="<?php echo $row['loginfailure']; ?>" data-rule="required" />-->
        <!--</div>-->
    <!--</div>-->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[status]', ['normal'=>'启用', 'hidden'=>'停用'], $row['status']); ?>
        </div>
    </div>
    <div class="form-group hidden layer-footer">
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