<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:78:"/data/www/jicai.sxxd365.com/public/../application/admin/view/index/forget.html";i:1626686927;s:67:"/data/www/jicai.sxxd365.com/application/admin/view/common/meta.html";i:1626686926;s:69:"/data/www/jicai.sxxd365.com/application/admin/view/common/script.html";i:1626686926;}*/ ?>
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
    <style type="text/css">
        body {
            color:#999;
            background:url('<?php echo $background; ?>');
            background-size:cover;
        }
        a {
            color:#fff;
        }
        .login-panel{margin-top:150px;}
        .login-screen {
            max-width:400px;
            padding:0;
            margin:100px auto 0 auto;

        }
        .login-screen .well {
            border-radius: 3px;
            -webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: rgba(255,255,255, 0.2);
        }
        .login-screen .copyright {
            text-align: center;
        }
        @media(max-width:767px) {
            .login-screen {
                padding:0 20px;
            }
        }
        .profile-img-card {
            width: 100px;
            height: 100px;
            margin: 10px auto;
            display: block;
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            border-radius: 50%;
        }
        .profile-name-card {
            text-align: center;
        }

        #login-form {
            margin-top:20px;
        }
        .input-group{
            margin: 15px 0;
        }
        #login-form .input-group {
            margin-bottom:15px;
        }


        .bottom{
            display: flex;
            justify-content: space-between;
            /*width: 500px;*/
            /*height: 500px;*/
            /*background-color: red;*/
        }
        button[disabled]
        {
            color: gray;
        }
        button{
            color: black;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-wrapper">
        <div class="login-screen">
            <div class="well">
                <div class="login-form">
                    <img id="profile-img" class="profile-img-card" src="/assets/img/avatar.png" />
                    <!--<img id="profile-img" class="profile-img-card" src="http://www.sxw365.cn./images/logo.png" />-->
                    <p id="profile-name" class="profile-name-card"></p>

                    <form action="" method="post" id="forget-form">
                        <div id="errtips" class="hide"></div>
                        <?php echo token(); ?>
                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></div>
                            <input type="text" class="form-control" id="pd-form-username" placeholder="手机号码" name="username" autocomplete="off" value="" data-rule="<?php echo __('Username'); ?>:required;mobile" />
                        </div>


                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></div>
                            <input type="text" name="captcha" id="captcha" class="form-control" placeholder="<?php echo __('Captcha'); ?>" data-rule="<?php echo __('Captcha'); ?>:required;length(4)" style="width: 150px;"/>
                            <span class="input-group-addon" style="padding:0;border:none;cursor:pointer;">
                                        <img src="<?php echo rtrim('/', '/'); ?>/index.php?s=/captcha" width="100" height="30" onclick="this.src = '<?php echo rtrim('/', '/'); ?>/index.php?s=/captcha&r=' + Math.random();"/>
                                    </span>
                        </div>
                        <!--手机验证码-->
                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-option-horizontal" aria-hidden="true"></span></div>
                            <input type="text" name="phoneCaptcha" id="phoneCaptcha" class="form-control" placeholder="手机验证码" data-rule="<?php echo __('Captcha'); ?>:length(6)" style="width: 150px;"/>
                            <span class="input-group-addon" style="padding:0;border:none;cursor:pointer;">
                                        <button id="pushCaptcha" type="button" style="height: 31px" disabled>获取验证码</button>
                                    </span>
                        </div>

                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                            <input type="password" class="form-control" id="pd-form-newpassword1" placeholder="新密码" name="password1" autocomplete="off" value="" data-rule="<?php echo __('Password'); ?>:required;password" />
                        </div>


                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                            <input type="password" class="form-control" id="pd-form-newpassword2" placeholder="确认新密码" name="password2" autocomplete="off" value="" data-rule="<?php echo __('Password'); ?>:required;password" />
                        </div>



                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-lg btn-block">确认</button>
                        </div>
                        <div><a href="<?php echo rtrim('/', '/'); ?>/admin.php/index/login">返回登录页</a></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
</body>
</html>
