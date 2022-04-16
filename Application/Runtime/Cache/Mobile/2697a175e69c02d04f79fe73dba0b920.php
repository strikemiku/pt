<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html >
<html>
<head>
<meta name="Generator" content="TPSHOP v1.1" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="applicable-device" content="mobile">
<title><?php echo ($tpshop_config['shop_info_store_name']); ?></title>
<meta http-equiv="keywords" content="<?php echo ($tpshop_config['shop_info_store_keyword']); ?>" />
<meta name="description" content="<?php echo ($tpshop_config['shop_info_store_desc']); ?>" />
<link rel="stylesheet" href="/Template/mobile/wuyang/Static/old/css/public.css">
<link rel="stylesheet" href="/Template/mobile/wuyang/Static/old/css/user.css">
<script type="text/javascript" src="/Template/mobile/wuyang/Static/old/js/jquery.js"></script>
<script src="/Public/js/global.js"></script>
<script src="/Public/js/mobile_common.js"></script>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/old/js/modernizr.js"></script>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/old/js/layer.js" ></script>

</head>

<link rel="stylesheet" href="/Template/mobile/wuyang/Static/old/css/flow.css">
<style>
    body, button, input, select, textarea {
        font-size: 14px;font-family: arial,微软雅黑;
    }
    .g-Total {
        background: #F4F4F4;border-bottom: 1px solid #DCDCDC; line-height: 30px;
        padding: 5px 10px 0; font-size: 14px;
    }
    a.gray9, .gray9 {color: #999;}
    .g-member {background: #F4F4F4;padding: 10px 5px;}
    .g-Recharge ul {zoom: 1;}
    .g-Recharge li a.z-sel {
        border: 1px solid #F60; color: #666;
    }
    .g-Recharge li .z-initsel {
        border: 1px solid #F60;
    }
    .g-Recharge li .z-init {
        width: 90%;padding: 7px 0; text-align: center;border: none;
    }
    .g-Recharge li .z-initsel input {
        color: #666;
    }
    .g-Recharge li a.z-sel s, .g-Recharge li .z-initsel s {
        width: 18px;height: 18px;
        position: absolute; right: -1px; bottom: -1px;
        display: inline-block;background-position: 0 0;
    }
    .g-Recharge li:nth-child(3n-1) {
        width: 34%;text-align: center;
    }
    .g-Recharge li:nth-child(3n-3) {
        text-align: right;
    }
    .g-Recharge li {
        width: 33%;float: left;margin-bottom: 10px;
    }
    .g-pay-ment {
        overflow: hidden;
    }
    .m-round {
        border: 1px solid #DCDCDC;border-radius: 5px;
        background: #fff;box-shadow: 1px 1px 1px #e7e7e7;
    }
    .mt10 {
        margin-top: 10px;
    }
    .g-Recharge li a, .g-Recharge li .z-initsel, .g-Recharge li b {
        color: #959595;width: 90%;
        line-height: 30px;display: inline-block;
        background: #fff;border-radius: 5px;
        text-align: center;border: 1px solid #EAEAEA;
        box-shadow: 1px 1px 1px #EDEDED; position: relative;
    }
    .z-sel s, .z-initsel s, .z-Btn-Close b, .z-Btn-Rotation b, .z-Btn-del b, .z-Btn-ok b {
        background: url(/Template/mobile/wuyang/Static/images/member-icon.png?v=130825);background-size: 40px auto;
    }
    .z-bank-Round {
        width: 16px;
        height: 16px;border: 1px solid #bbb;
        background: #fff;border-radius: 16px;
        display: inline-block;margin-right: 8px;
        box-shadow: 0 1px 1px #ccc inset;
    }
    .z-bank-Roundsel s {
        width: 12px;height: 12px;border-radius: 12px;display: inline-block;
        background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0,#ff8a00),color-stop(1,#f60));
    }
    .f-Recharge-btn {display: block;}
    a.orgBtn {
        color: white;;
        background-color: #FF0000;
        font-weight: 800}
    .orgBtn {
        display: block; width: 100%;
        -webkit-box-sizing: border-box;
        height: 43px;line-height: 43px;
        text-align: center; color: #753f00;
        border-radius: 5px;font-size: 16px;
    }
    .clearfix { display: block;}
    .z-bank-Roundsel {
        width: 16px;height: 16px;line-height: 20px;border: 1px solid #ccc;background: #F6F5F5;
        border-radius: 16px;display: inline-block;text-align: center;margin-right: 8px;
    }
    .g-pay-ment li {
        line-height: 36px;border-top: 1px dotted #CBCBCB;max-height: 40px;
        padding: 0 10px;overflow: hidden;margin-top: -1px;
    }
    .clearfix:after {
        content: ".";display: block;height: 0;clear: both;visibility: hidden;
    }
    .orange {color: #f60;}
    /**************底部区域开始*************/

    .footer{position:fixed;height:50px;width:100%;bottom:0;left:0;z-index: 99;padding-bottom: 0;}

    .nav_list{ background:#fff; padding:5px 0;}
    .nav_list::after{ content:""; display: block; clear: both;}

    .nav_list ul li{ float:left; width:25%; text-align:center; position: relative}

    .nav_list ul li i{ color:#5a5c60; font-size:24px; display: block; width: 28px; height: 28px; margin: 0 auto;}

    .nav_list ul li .img{width: 55px;height: 55px;background:#fff;overflow:hidden;box-shadow:0 0 10px rgba(0,0,0,0.1);border-radius:50%;margin: -15px auto 0;padding:8px;box-sizing:border-box;border: 1px solid #eee;}
    .nav_list ul li .img img{ width:100%; height:100%;}

    .nav_list ul li.on i{color: #00ae4b;}

    .nav_list ul li .nav_title{ font-size:14px; color:#999; margin-top: -6px;}
    .nav_list ul li .kan_img{width: 55px;border: 5px solid #fff3e5;margin: 0 auto;background: #fff;border-radius: 50%;position: relative;top: -21px;}

    .nav_list ul li.on .nav_title{color: #726054;}
    .nav_list ul li:nth-child(1) i{ background: url(/Template/mobile/wuyang/Static/images/list_img01.png) no-repeat top; background-size: 100%;}
    .nav_list ul li:nth-child(2) i{ background: url(/Template/mobile/wuyang/Static/images/list_img02.png) no-repeat top; background-size: 100%;}
    .nav_list ul li:nth-child(4) i{ background: url(/Template/mobile/wuyang/Static/images/list_img03.png) no-repeat top; background-size: 100%;}
    .nav_list ul li:nth-child(5) i{ background: url(/Template/mobile/wuyang/Static/images/list_img04.png) no-repeat top; background-size: 100%;}
    .nav_list ul li.on:nth-child(1) i{ background: url(/Template/mobile/wuyang/Static/images/list_img01.png) no-repeat bottom; background-size: 100%;}
    .nav_list ul li.on:nth-child(2) i{ background: url(/Template/mobile/wuyang/Static/images/list_img02.png) no-repeat bottom; background-size: 100%;}
    .nav_list ul li.on:nth-child(4) i{ background: url(/Template/mobile/wuyang/Static/images/list_img03.png) no-repeat bottom; background-size: 100%;}
    .nav_list ul li.on:nth-child(5) i{ background: url(/Template/mobile/wuyang/Static/images/list_img04.png) no-repeat bottom; background-size: 100%;}

    .shipping_title img{
        -webkit-tap-highlight-color:rgba(0,0,0,0);
        -webkit-tap-highlight-color: transparent;
        outline:none;
        text-decoration: none;
    }
    /**************底部区域结束*************/
</style>

<header>
    <div class="tab_nav">
        <div class="header">
            <div class="h-left"><a class="sb-back" href="<?php echo U('User/index');?>" title="返回"></a></div>
            <div class="h-mid">充值申请</div>
            <!--<div class="h-right" style="line-height: 45px;">-->
            <!--<a href="<?php echo U('recharge_log');?>" style="color: white">充值明细</a>-->
            <!--</div>-->
        </div>
    </div>
</header>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/old/js/mobile.js" ></script>
<div class="goods_nav hid" id="menu">
      <div class="Triangle">
        <h2></h2>
      </div>
      <ul>
        <li><a href="<?php echo U('Index/index');?>"><span class="menu1"></span><i>首页</i></a></li>
        <li><a href="<?php echo U('Goods/categoryList');?>"><span class="menu2"></span><i>分类</i></a></li>
        <li><a href="<?php echo U('Cart/cart');?>"><span class="menu3"></span><i>购物车</i></a></li>
        <li style=" border:0;"><a href="<?php echo U('User/index');?>"><span class="menu4"></span><i>我的</i></a></li>
   </ul>
 </div> 
<div id="tbh5v0">
    <form method="post"  id="recharge_form" action="<?php echo U('Mobile/Payment/getRecharge');?>">
        <section class="clearfix g-member">
            <div class="g-Recharge">
                <ul id="ulOption">
                    <li money="200"><a href="javascript:;" class="z-sel">200元<s></s></a></li>
                    <li money="300"><a href="javascript:;">300元<s></s></a></li>
                    <li money="400"><a href="javascript:;">400元<s></s></a></li>
                    <li money="500"><a href="javascript:;">500元<s></s></a></li>
                    <li money="1000"><a href="javascript:;">1000元<s></s></a></li>
                    <li>
                        <b>
                            <input type="text" id="input_val" oninput="this.value=this.value.replace(/\D/g,'').replace(/^0+(?=\d)/,'')" class="z-init" placeholder="自定义金额" maxlength="8" value="<?php echo ($order["account"]); ?>"><s></s>
                        </b>
                    </li>
                </ul>
            </div>
            <div class="clearfix mt10 m-round g-pay-ment g-bank-ct" style="width: 100%">
                <ul id="ulBankList" class="nav nav-list-sidenav">
                    <li class="gray6">选择充值方式</li>
                    <?php if(is_array($paymentList)): foreach($paymentList as $k=>$v): if($v[code] == 'alipayMobile' or $v[code] == 'weixin'): ?><li class="clearfix" name="payment_name" style="margin-bottom: 4px; margin-top: 4px;padding: 12px 10px;line-height: 25px;">
                                <input type="radio"  value="pay_code=<?php echo ($v['code']); ?>" class="c_checkbox_t" name="pay_radio"  />
                                <div class="fl shipping_title">
                                    <img src="/plugins/<?php echo ($v['type']); ?>/<?php echo ($v['code']); ?>/<?php echo ($v['icon']); ?>" onClick="change_pay(this);" style="width: 40px;height: 40px;"/>
                                    <?php if($v[code] == 'alipayMobile'): ?>支付宝<?php endif; ?>
                                    <?php if($v[code] == 'weixin'): ?>微信<?php endif; ?>
                                </div>
                            </li><?php endif; endforeach; endif; ?>
                </ul>
            </div>
            <div class="mt10 f-Recharge-btn" >
                <a id="btnSubmit"  class="orgBtn"  onclick="recharge_submit()">立即充值</a>
            </div>
            <input type="hidden" name="account" id="add_money" value="200">
            <input type="hidden" name="order_id" value="<?php echo ($order["order_id"]); ?>">
        </section>
    </form>

</div>
</body>
<script src="/Template/mobile/wuyang/Static/js/layer/layer.js"></script>
<script type="text/javascript">
    $(function () {
        $(".g-Recharge ul li").click(function () {
            $(this).find('a').addClass('z-sel');
            $(this).siblings().find('a').removeClass('z-sel');
            $('.gray6').find('em').html($(this).attr('money'));
            $('#add_money').val($(this).attr('money'));
        });
    });

    function change_pay(obj)
    {
        $(obj).parent().siblings('input[name="pay_radio"]').trigger('click');
    }
    var is_sub = false;
    function recharge_submit(){
        var input_val = $('#input_val').val();
        if(input_val>0){
            $('#add_money').val(input_val);
        }
        if(is_sub){
            layer.msg("已提交，请勿重复提交");return false;
        }
        is_sub=true;
        var account = $('#add_money').val();
        if(parseInt(account)<0 || account==''){
            layer.msg("请输入正确的金额");
            is_sub = false;
            return false;
        }
        var checked = $(".c_checkbox_t:checked").val();
        if(!checked){
            layer.msg("请选择充值方式");
            is_sub = false;
            return false;
        }
        $('#recharge_form').submit();
    }
</script>
</html>