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
    <title>找回密码</title>
    <style>
		.tel_con ul li i{display:block;float:left;width: 21px;height: 21px;margin-top:8px;}
		.tel_con ul li i img{ width:100%;}
		.tel_con ul li input{ width: calc(100% - 30px);border:none;float:left;height:40px;color:#666;font-size:14px;border-radius:40px;padding:0 10px;background:none;}

	</style>
</head>

<body>
<div class="main-container">
    <div class="header_list">
        <a href="javascript:history.back(-1);" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
        <h3>找回密码</h3>
    </div>

    <div style="height:45px;"></div>

    <div class="tel_con">
        <ul >
            <li><i><img src="/Template/mobile/wuyang/Static/images/sj_img.png"/></i><input type="number" placeholder="请输入手机号" name="mobile" id="mobile" /></li>
            <li><i><img src="/Template/mobile/wuyang/Static/images/yzm_img.png"/></i><input type="text" name="code" id="code" placeholder="短信验证码"  />
                <div class="yzm" id="btn" onclick="settime(this)" style="background:#e02e24;">发放验证码</div>
            </li>
            <li><i><img src="/Template/mobile/wuyang/Static/images/mima_ccc.png"/></i><input type="password" name="pwd" id="pwd" placeholder="请输入新密码"  /></li>
            <li><i><img src="/Template/mobile/wuyang/Static/images/mima_ccc.png"/></i><input type="password" name="repwd" id="repwd" placeholder="确认新密码"  /></li>
        </ul>
        <div class="tel_btn"><input type="button" style="background-color: red;color: white;" onClick="checkSubmit()" value="立即提交"/></div>
    </div>
</div>
<script>

    // author :凌寒 2018年9月18日14:43:24 正则校验手机号
    function checkMobile(tel) {
        var reg = /(^1[3|4|5|7|8][0-9]{9}$)/;
        if (reg.test(tel)) {
            return true;
        }else{
            return false;
        };
    }
    // author :凌寒 2018年9月18日14:43:24 发送验证码
    var mytime= null;
    function settime(obj) {
        var phone = $.trim($('#mobile').val());
        if(phone == ''){
            layer.msg('请输入手机号!',{icon:5,time:1500});
            return false;
        }else{
            if(!checkMobile(phone)){
                layer.msg('手机号格式不正确!',{icon:5,time:1500});
                return false;
            }else{
                $.post("/Mobile/User/check_phone",{phone:phone},function(data){
                    if(data== '1'){
                        layer.msg('手机号未注册!',{icon:5,time:1500});
                        return false;
                    }else if(data=='2'){
                        $("#btn").html("获取中...");
                        countdown=60;
                        if(mytime==null){
                            mytime=setInterval(function(){
                                $("#btn").html("重新发送(" + countdown + ")");
                                countdown--;
                                if(countdown<0){
                                    clearInterval(mytime);
                                    $("#btn").html("发送验证码");
                                    countdown = 60;
                                    mytime=null;
                                }
                            },1000)
                        }
                        $.post("/Mobile/User/send_message",{phone:phone},function(data){
                            console.log(data);
                        });
                    }
                });
            }
        }
    }
    //找回密码方法
    function checkSubmit()
    {
        var mobile = $.trim($('#mobile').val());
        var code = $.trim($('#code').val());
        var pwd = $.trim($('#pwd').val());
        var repwd = $.trim($('#repwd').val());
        if(mobile == ''){
            layer.msg("请输入手机号",{icon:5,time:1500});
            return false;
        }
        if(!checkMobile(mobile)){
            layer.msg('手机号错误!',{icon:5,time:1500});
            return false;
        }
        if(code == ''){
            layer.msg("验证码不能为空",{icon:5,time:1500});
            return false;
        }
        if(pwd == ''){
            layer.msg('新密码不能为空!',{icon:5,time:1500});
            return false;
        }
        if(repwd == ''){
            layer.msg('确认密码不能为空!',{icon:5,time:1500});
            return false;
        }
        if(pwd != repwd){
            layer.msg('两次密码输入的不一致',{icon:5,time:1500});
            return false;
        }
        $.ajax({
            type : 'post',
            url : '/index.php?m=Mobile&c=User&a=forget_pwd&t='+Math.random(),
            data : {mobile:mobile,pwd:pwd,repwd:repwd,code:code},
            dataType : 'json',
            success : function(res){
                if(res.status == 200){
                    layer.msg(res.msg,{icon:6,time:1500},function () {
                        location.href="/Mobile/User/login";
                    });
                }else{
                    layer.msg(res.msg,{icon:5,time:1500});
                    return false;
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                layer.msg('网络失败，请刷新页面后重试',{icon:5,time:1500});
            }
        })
    }
</script>
</body>
</html>