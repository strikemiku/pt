<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no, email=no"/>
    <meta charset="UTF-8">
    <title>支付成功</title>
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/core.css">
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/icon.css">
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/home.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link href="iTunesArtwork@2x.png" sizes="114x114" rel="apple-touch-icon-precomposed">
</head>
<body>
<style>
    .zhifu_suc{ width:95%; margin:20% auto 0; position:relative;}
    .zhifu_suc .zhifu_suc_img{ width:60px; height:60px; background:#fff; border-radius:50%; border:5px solid #da4644;position:absolute; top:-30px; left:50%; margin-left:-30px; box-shadow:0 5px 5px rgba(0,0,0,0.3);}
    .zhifu_suc .zhifu_suc_img img{ width:100%; height:100%;}
    .zhifu_suc_txt{padding:40px 10px 20px; text-align:center;}
    .zhifu_suc_txt h2{ font-size:16px; color:#da4644; margin-bottom:10px;}
    .zhifu_suc_txt .zhifu_con{ font-size:14px; text-align:left; width:70%; margin:0 auto;}
    .zhifu_suc_txt .zhifu_con p{ color:#666; margin-bottom:10px;}
    .zhifu_suc_txt .zhifu_con p:last-child{ margin-bottom:0;}
    .zhifu_suc_txt .zhifu_con p em { color:#EB5211; font-size:16px; font-weight:700;}
    .ckdd_but{ width:60%; margin:0 auto; line-height:40px; background:#da4644; display:block; border-radius:5px; color:#fff; font-size:14px; text-align:center;}

</style>
<section class="aui-address-content">
    <div class="zhifu_suc">
        <div class="zhifu_suc_img">
            <img src="/Template/mobile/wuyang/Static/new/images/chenggong.png"/>
        </div>
        <!--失败-->
        <!--<div class="zhifu_suc_img">
            <img src="img/shibai.png"/>
        </div>-->

        <div class="zhifu_suc_txt">
            <h2>充值失败！请重新支付</h2>
            <div class="zhifu_con">
                <p>订单号：<?php echo ($order['order_sn']); ?></p>
                <p>充值金额：<em><?php echo ($order['account']); ?></em>元</p>
            </div>
        </div>
    </div>
    <a class="ckdd_but" href="<?php echo U('/Mobile/User/index');?>">个人中心</a>
</section>

</body>
</html>