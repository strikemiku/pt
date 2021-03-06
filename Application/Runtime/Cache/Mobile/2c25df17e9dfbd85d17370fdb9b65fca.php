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
    <!--<script src="/Public/js/global.js"></script>-->
    <title>注册会员</title>
    <style>
        .reg ul li select {
            width: 100%;
            height: 40px;
            border: 1px solid #d5d5d5;
            color: #666;
            font-size: 14px;
            border-radius: 3px;
            padding: 0 10px;
            background: #fff;
        }
    </style>
</head>

<body>
<div class="main-container" style=" background:#fff">
    <div class="header_list">
        <a href="javascript:history.back(-1);" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
        <h3>注册会员</h3>
    </div>

    <div style="height:45px;"></div>
    <div class="reg">
        <form action="" method="" >
            <ul>
                <li><i><img src="/Template/mobile/wuyang/Static/images/geren_ccc.png"/></i><input type="text" placeholder="请输入姓名" id="nickname" name="nickname"/></li>
                <li><i><img src="/Template/mobile/wuyang/Static/images/sj_img.png"/></i><input type="text" placeholder="请输入手机号" id="mobile" name="mobile"/></li>
                <li>
                	<i><img src="/Template/mobile/wuyang/Static/images/yzm_img.png"/></i>
                    <input type="text" id="code" name="code" placeholder="短信验证码"/>
                    <div class="yzm " id="btn"  onclick="settime(this)">发送验证码</div>
                </li>
                <li>
                <i><img src="/Template/mobile/wuyang/Static/images/mima_ccc.png"/></i>
                <input type="password" placeholder="登录密码" id="pwd" name="pwd"/></li>
                <li>
                <i><img src="/Template/mobile/wuyang/Static/images/mima_ccc.png"/></i>
                <input type="password" placeholder="确认密码" id="repwd" name="repwd"/></li>
                <li>
                <i><img src="/Template/mobile/wuyang/Static/images/yq_img.png"/></i>
                    <input type="text" placeholder="请输入邀请码" id="pcode" name="pcode" value="<?php echo ($pInfo['user_code']); ?>"<?php if($pInfo['user_code'] != null): ?>readonly<?php endif; ?> />
                </li>
                <li style="text-align: left;color:#666; margin-top: 20px; font-size: 12px; border-bottom:none;">
                    <input type="checkbox" style="width: 16px;height: 16px;" id="check_xieyi" name="check_xieyi"/>
                    同意并接受<a style="color:#e02e24;" href="/Mobile/User/xyDetail/cat_id/7">《<?php echo ($xy["title"]); ?>》</a>
                </li>
                <li><input type="button" style="background-color:#e02e24; display:block;  line-height:45px; height:45px;float:none; margin:20px auto 0; color: white;" onClick="checkSubmit()" value="立即注册"/></li>

            </ul>
            <p class="login" style="margin-top: 10px;color: #333;">已有账号？<a href="<?php echo U('User/login');?>">去登陆</a></p>
        </form>
    </div>
</div>
</body>
</html>
<script>

</script>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/js/aui.js"></script>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/js/city.js"></script>
<script>
    /**
     * 获取城市
     * @param t  省份select对象
     */
    function get_city(t){
        var parent_id = $(t).val();
        if(!parent_id > 0){
            return;
        }
        $('#twon').empty().css('display','none');
        var url = '/index.php?m=Home&c=Api&a=getRegion&level=2&parent_id='+ parent_id;
        $.ajax({
            type : "GET",
            url  : url,
            error: function(request) {
                alert("服务器繁忙, 请联系管理员!");
                return;
            },
            success: function(v) {
                v = '<option value="0">选择城市</option>'+ v;
                $('#city').empty().html(v);
            }
        });
    }
    /**
     * 获取地区
     * @param t  城市select对象
     */
    function get_area(t){
        var parent_id = $(t).val();
        if(!parent_id > 0){
            return;
        }
        var url = '/index.php?m=Home&c=Api&a=getRegion&level=3&parent_id='+ parent_id;
        $.ajax({
            type : "GET",
            url  : url,
            error: function(request) {
                alert("服务器繁忙, 请联系管理员!");
                return;
            },
            success: function(v) {
                v = '<option value="0">选择区域</option>'+ v;
                $('#district').empty().html(v);
            }
        });
    }
    // 获取最后一级乡镇
    function get_twon(obj){
        var parent_id = $(obj).val();
        var url = '/index.php?m=Home&c=Api&a=getTwon&parent_id='+ parent_id;
        $.ajax({
            type : "GET",
            url  : url,
            success: function(res) {
                if(parseInt(res) == 0){
                    $('#twon').empty().css('display','none');
                }else{
                    $('#twon').css('display','block');
                    $('#twon').empty().html(res);
                }
            }
        });
    }
    // author :凌寒 2018年9月18日14:43:24 正则校验手机号
    function checkMobile(tel) {
        var reg = /(^1[3|4|5|6|7|8|9][0-9]{9}$)/;
        if (reg.test(tel)) {
            return true;
        }else{
            return false;
        };
    }
    function isCardNo(card) {

        var pattern = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;

        return pattern.test(card);

    }
    function check_code(user_code) {

        var reg =/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,18}$/;

        if(reg.test(user_code)) {
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
            layer.msg("请输入您的手机号",{icon:5,time:1500});
            return false;
        }else{
            if(!checkMobile(phone)){
                layer.msg('手机号格式不正确!',{icon:5,time:1500});
                return false;
            }else{
                $.post("/Mobile/User/check_phone",{phone:phone},function(data){
                    if(data=='2'){
                        layer.msg('手机号已经注册',{icon:5,time:1500});
                        return false;
                    }else if(data=='1'){
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
    // author :凌寒 2018年5月17日13:53:02 执行注册方法
    function checkSubmit()
    {
        var nickname = $.trim($('#nickname').val());
        var mobile = $.trim($('#mobile').val());
        var code = $.trim($('#code').val());
        var pwd = $.trim($('#pwd').val());
        var repwd = $.trim($('#repwd').val());
        var pcode = $.trim($('#pcode').val());
        if(nickname == ''){
            layer.msg("请输入姓名",{icon:5,time:1500});
            return false;
        }
        if(mobile == ''){
            layer.msg("请输入手机号",{icon:5,time:1500});
            return false;
        }
        if(!checkMobile(mobile)){
            layer.msg('手机号码不匹配!',{icon:5,time:1500});
            return false;
        }
        if(code == ''){
            layer.msg("请输入验证码",{icon:5,time:1500});
            return false;
        }
        if(pwd == ''){
            layer.msg('请输入登录密码!',{icon:5,time:1500});
            return false;
        }
        if(repwd == ''){
            layer.msg('请输入确认密码!',{icon:5,time:1500});
            return false;
        }
        if(pwd != repwd){
            layer.msg('两次密码输入的不一致',{icon:5,time:1500});
            return false;
        }
        if(pcode == ''){
            layer.msg('请输入邀请码!',{icon:5,time:1500});
            return false;
        }
        if($('#check_xieyi').is(':checked')==false) {
            layer.msg('请先同意并接受《注册协议》');
            return false;
        }
        $.ajax({
            type : 'post',
            url : '/index.php?m=Mobile&c=User&a=reg&t='+Math.random(),
            data : {nickname:nickname,mobile:mobile,code:code,pwd:pwd,pcode:pcode},
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