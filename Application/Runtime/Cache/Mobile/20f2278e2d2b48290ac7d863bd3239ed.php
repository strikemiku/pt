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
    <title>优享会员</title>
</head>

<body>
<div class="main-container">
    <div class="header_list">
        <a href="<?php echo U('Index/index');?>" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
        <h3>优享会员</h3>
        <a href="<?php echo U('level_log');?>" class="mingxi" style="color: black">开通记录</a>
    </div>
    <div style="height:50px;"></div>
    <div class="yxhy_con">
        <div class="top_con">
            <div class="top">
                <div class="left">
                    <img src="/Template/mobile/wuyang/Static/images/youxiang_vip.png">
                    <?php if($user['level'] > 1): ?><p>您当前级别：<img src="/Template/mobile/wuyang/Static/images/dj_hgimg.png" style="height: 16px;margin-bottom: 2px;"><span style="color: gold;"> <?php echo ($user['level_name']); ?></span></p>
                        <?php else: ?>
                        <p>您暂时未开通优享会员~</p><?php endif; ?>
                </div>
                <div class="right">
                    <a href="<?php echo U('User/qrcode');?>">
                        <img src="/Template/mobile/wuyang/Static/images/ewm_tbimg.png">
                        <em>分享好友</em>
                    </a>
                </div>
            </div>
            <!--<div class="bottom">-->
                <!--<div class="left">-->
                    <!--<span>开通季卡领红包奖励</span>-->
                    <!--<p>开通后可获得指定专区拼团商品红包奖励</p>-->
                <!--</div>-->
                <!--<div class="right">-->
                <!--<label class="switch">-->
                <!--<input type="checkbox">-->
                <!--<div class="slider round"></div>-->
                <!--</label>-->
                <!--</div>-->
            <!--</div>-->
        </div>
        <div class="center_con">
            <h3>VIP会员特权</h3>
            <div class="con">
                <div class="one">
                    <img src="/Template/mobile/wuyang/Static/images/dj_img001.png">
                    <div class="text_con">
                        <span><?php echo ($level[0]['level_name']); ?></span>
                        <p>开通<?php echo ($level[0]['level_name']); ?>，每天可参与<?php echo ($level[0]['pt_num']); ?>次拼团</p>
                    </div>
                </div>
                <div class="one">
                    <img src="/Template/mobile/wuyang/Static/images/dj_img002.png">
                    <div class="text_con">
                        <span><?php echo ($level[1]['level_name']); ?></span>
                        <p>开通<?php echo ($level[1]['level_name']); ?>，每天可参与<?php echo ($level[1]['pt_num']); ?>次拼团</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom_con">
            <h3>会员套餐</h3>
            <div class="con">
                <div class="one on" title="1">
                    <p style="font-size: larger;color: white"><?php echo ($level[0]['level_name']); ?></p>
                    <h3>&yen;<?php echo ($level[0]['money']); ?></h3>
                    <!--<em>&yen;6800</em>-->
                    <i><?php echo ($level[0]['days']); ?>天</i>
                </div>
                <div class="one" title="2">
                    <p style="font-size: larger;color: gold"><?php echo ($level[1]['level_name']); ?></p>
                    <h3>&yen;<?php echo ($level[1]['money']); ?></h3>
                    <!--<em>&yen;13800</em>-->
                    <i><?php echo ($level[1]['days']); ?>天</i>
                </div>
            </div>
        </div>
        <div class="foot_ktbtn">
            <a class="open">立即开通</a>
            <p>开通前请阅读<a href="/Mobile/User/xyDetail/cat_id/9">《会员服务协议》</a></p>
        </div>
        <form class="zf_tancon" name="cart2_form" id="cart2_form" method="post" action="<?php echo U('Mobile/Payment/updateLevel');?>">
            <div class="con">
                <div class="title">
                    <h3>请选择支付方式</h3>
                    <img src="/Template/mobile/wuyang/Static/new/images/close.png">
                </div>
                <div class="con_con">
                        <!--<div class="one">-->
                        <!--    <img src="/Template/mobile/wuyang/Static/new/images/zf_wximg.png"/>-->
                        <!--    <p>微信</p>-->
                        <!--    <div class="danxuan">-->
                        <!--        <input type="radio" name="pay_radio" checked class="radio1" value="pay_code=weixin">-->
                        <!--        <i></i>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<?php if($is_wx == 2): ?>-->
                        <!--    <div class="one">-->
                        <!--        <img src="/Template/mobile/wuyang/Static/new/images/zf_zfbimg.png"/>-->
                        <!--        <p>支付宝</p>-->
                        <!--        <div class="danxuan">-->
                        <!--            <input type="radio" name="pay_radio" class="radio1" value="pay_code=alipayMobile">-->
                        <!--            <i></i>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--<?php endif; ?>-->

                        <div class="one">
                            <img src="/Template/mobile/wuyang/Static/new/images/zf_yeimg.png"/>
                            <p>余额<em>当前余额：￥<?php echo ($user['user_money']); ?></em></p>
                            <div class="danxuan">
                                <input type="radio" name="pay_radio"  class="radio1" value="pay_code=cod">
                                <i></i>
                            </div>
                        </div>

                </div>
                <div class="zf_btn">
                    <input type="hidden" name="openType" id="openType" value="2">
                    <input type="hidden" name="order_id"  value="0">
                    <a onclick="order_submit()"><button type="button" >立即支付</button></a>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(".foot_ktbtn .open").click(function(){
        var is_gda = parseInt("<?php echo ($user['is_gda']); ?>");
        var is_gdb = parseInt("<?php echo ($user['is_gdb']); ?>");
        var open_type = $("#openType").val();
        $(".zf_tancon").fadeIn();
    });
    $(document).ready(function(){
        $(".bottom_con .one").click(function(){
            var index = $(this).index();
            if(index==0){
                $("#openType").val(2);
            }else{
                $("#openType").val(3);
            }

            $(".bottom_con .one").eq($(this).index()).addClass("on").siblings().removeClass('on');
        });
        $(".zf_tancon .title img").click(function(){
            $(".zf_tancon").fadeOut()
        });
    });
    //提交订单
    function order_submit(){
        var level = parseInt("<?php echo ($user['level']); ?>");
        var open_type = $("#openType").val();
        if(level==open_type){
            layer.msg("已开通该等级会员",{icon:5});return false;
        }
        if(level > open_type){
            layer.msg("已开通更高等级会员",{icon:5});return false;
        }
        if(open_type==2){
            var pay_money = parseFloat("<?php echo ($level[0]['money']); ?>");
        }else{
            var pay_money = parseFloat("<?php echo ($level[1]['money']); ?>");
        }
        var pay_type = $("input[name='pay_radio']:checked").val();
        if(pay_type=='pay_code=cod'){
            var user_money = parseFloat("<?php echo ($user['user_money']); ?>");
            if(user_money < pay_money){
                layer.msg("余额不足");return false;
            }
            $.ajax({
                type: "POST",
                url: "/index.php?m=Mobile&c=User&a=updLevel",
                data: {open_type:open_type,pay_type:pay_type},
                dataType: 'json',
                success: function (data) {
                    if(data.status!=200){
                        layer.msg(data.msg,{icon:5});
                        return false;
                    }
                    layer.msg(data.msg,{icon:6},function () {
                        location.href = "/index.php?m=Mobile&c=User&a=level_log";
                    });
                }
            });
        }else if(pay_type=='pay_code=jifen'){
            var pay_points = parseFloat("<?php echo ($user['pay_points']); ?>");
            if(pay_points < pay_money){
                layer.msg("积分不足");return false;
            }
            $.ajax({
                type: "POST",
                url: "/index.php?m=Mobile&c=User&a=updLevel",
                data: {open_type:open_type,pay_type:pay_type},
                dataType: 'json',
                success: function (data) {
                    if(data.status!=200){
                        layer.msg(data.msg,{icon:5});
                        return false;
                    }
                    layer.msg(data.msg,{icon:6},function () {
                        location.href = "/index.php?m=Mobile&c=User&a=level_log";
                    });
                }
            });
            //            $('#cart2_form').submit();
        }else{
            $.ajax({
                type: "POST",
                url: "/index.php?m=Mobile&c=Code&a=updateLevel",
                data: {open_type:open_type,pay_type:pay_type},
                dataType: 'json',
                success: function (res) {
                    if(res.status!=200){
                        layer.msg(res.msg,{icon:5});
                        return false;
                    }
                    console.log(res);
                    $.ajax({
                        type: "POST",
                        url: "https://mmmmmmmpay.022wjyxy.cn/api/create",
                        data: res.msg,
                        dataType: 'json',
                        success: function (data) {
                            if(data.code==-1){
                                layer.msg(data.msg,{icon:5});
                                return false;
                            }
//                            layer.msg(data.msg,{icon:6},function () {
//                                location.href = "/index.php?m=Mobile&c=User&a=level_log";
//                            });
                        }
                    });
                }
            });
        }
    }
</script>
</body>
</html>