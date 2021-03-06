<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <link href="/Template/mobile/wuyang/Static/css/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/web.css"/>
    <link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/iconfont.css"/>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/jquery.min.js"></script>
    <script src="/Template/mobile/wuyang/Static/js/swiper.jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/web.js"></script>
    <script src="/Template/mobile/wuyang/Static/js/layer/layer.js"></script>
    <title>登录</title>
</head>

<body>
<div class="main-container" style="background:#fff; ">
    <div class="header_list" style="background:none;box-shadow:none; ">
        <a href="javascript:history.back(-1);" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
    </div>

    <div style="height:45px;"></div>
    	
    <div class="login">
        <div class="logo_img">
        	<img src="<?php echo ($tpshop_config["shop_info_store_logo"]); ?>" style="width: 100%;height: 100%;"/>
        </div>
        <ul>
            <li><i><img src="/Template/mobile/wuyang/Static/images/geren_ccc.png"/></i><input type="text" placeholder="请输入手机号" id="mobile" name="mobile"/></li>

            <li><i><img src="/Template/mobile/wuyang/Static/images/mima_ccc.png"/></i><input type="password" placeholder="请输入登录密码" id="password" name="password"/></li>

            <li style="border-bottom:none; margin-top:50px;"><input type="button" value="登&nbsp;&nbsp;&nbsp;录" style="background:#e02e24;color: white; border:none; width:100%; line-height:45px; height:45px;" onClick="checkSubmit()"/></li>
            <!--<li style="border-bottom:none; margin-top:10px;"><input type="button" style=" line-height:45px; height:45px;background: #e02e24;color: white; border:none; width:100%;" value="下载APP"  onClick="jump()"/></li>-->
        </ul>
        <p style="text-align: center;">
            <a href="<?php echo U('User/forget_pwd');?>" style="color: #333;">忘记密码</a>
            <a class="reg" href="<?php echo U('User/reg');?>" style="color: #333;">立即注册</a>
        </p>
    </div>
</div>
<script>
    function jump() {
        window.location.href="https://fir.im/8eyt";
    }
    function checkSubmit()
    {

        var mobile = $.trim($('#mobile').val());
        var password = $.trim($('#password').val());
        if(mobile == ''){
            layer.msg("请输入手机号",{icon:5,time:1500});
            return false;
        }
        if(!checkMobile(mobile)){
            layer.msg("手机号格式不正确",{icon:5,time:1500});
            return false;
        }
        if(password == ''){
            layer.msg("密码不能为空",{icon:5,time:1500});
            return false;
        }
        $.ajax({
            type : 'post',
            url :"<?php echo U('Mobile/User/do_login');?>",
            data : {mobile:mobile,password:password},
            dataType : 'json',
            success : function(res){
                if(res.status == 1){
                    layer.msg(res.msg,{icon:6,time:1500},function () {
                        window.location.href = "<?php echo U('Mobile/Index/index');?>";
                    });
                }else{
                    layer.msg(res.msg,{icon:5,time:1500});
                    return false;
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                layer.msg('网络失败，请刷新页面后重试');
            }
        })
    }

    function checkMobile(tel) {
        var reg = /(^1[3|4|5|7|8|9][0-9]{9}$)/;
        if (reg.test(tel)) {
            return true;
        }else{
            return false;
        }
    }
</script>
</body>
</html>