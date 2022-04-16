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
    <title>个人中心</title>
    <style>
        #newBridge .nb-icon-wrap {
             position:relative;
            /*box-sizing: border-box;*/
            /*z-index: 2147483646;*/
        }
        .money_two .money_r{
            width: 33.3%;
        }
        .geren_list ul li{
            width: 25%;
        }
    </style>
</head>
<body>
<div class="main-container">
    <div class="header_list">
        <a href="javascript:history.back(-1);" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
        <h3>个人中心</h3>
        <a href="<?php echo U('User/set');?>" style="position:absolute; top:10px; right:10px; width:30px; height:30px; display:block;"><img style=" display:block; width:30px; height:30px;" src="/Template/mobile/wuyang/Static/images/grimg_001.png"/></a>
    </div>
    <div style="height:45px;"></div>
    <div class="geren_top">
        <div class="geren_xinxi">
            <div class="touxiang">
                <?php if($user["head_pic"] != null): ?><img src="<?php echo ($user["head_pic"]); ?>" alt="" style="width: 80px;height: 80px;"/>
                    <?php else: ?>
                    <img src="<?php echo ($tpshop_config["shop_info_store_logo"]); ?>" alt="" style="width: 80px;height: 80px;"/><?php endif; ?>
            </div>
            <div class="text">
				<h3>
                    <?php echo (getSubstr($user["nickname"],0,5)); ?>
                    <div class="ej_con">
                        <img src="/Template/mobile/wuyang/Static/images/dj_hgimg.png"><?php echo ($level_name); ?>
                    </div>
                </h3>
           		<p style="margin-top: 10px;">专属邀请码:&nbsp;&nbsp; <?php echo ($user['user_code']); ?> <a href="<?php echo U('User/qrcode');?>"><em>立即分享</em></a> </p>
            </div>
        </div>
    </div>
    <div class="hy_ktcon">
    	<a href="<?php echo U('User/updLevel');?>">
    		<img src="/Template/mobile/wuyang/Static/images/hy_ktimg.png">
    	</a>
    </div>
    <div class="money money_two clearfixd">
        <div class="money_r">
            <a href="<?php echo U('User/money_log');?>">
                <p>余额</p>
                <span><?php echo ((isset($user["user_money"]) && ($user["user_money"] !== ""))?($user["user_money"]):"0.00"); ?></span>
            </a>
        </div>
        <div class="money_r">
            <a href="<?php echo U('User/jifen_log');?>">
                <p>积分</p>
                <span><?php echo ((isset($user["pay_points"]) && ($user["pay_points"] !== ""))?($user["pay_points"]):"0.00"); ?></span>
            </a>
        </div>
        <div class="money_r">
            <a href="<?php echo U('User/active_log');?>">
                <p>活跃度</p>
                <span><?php echo ((isset($user["active_num"]) && ($user["active_num"] !== ""))?($user["active_num"]):"0.00"); ?></span>
            </a>
        </div>
        <!--<div class="money_r">-->
            <!--<a href="<?php echo U('User/recom_log');?>">-->
                <!--<p>推荐值</p>-->
                <!--<span><?php echo ((isset($user["recom_num"]) && ($user["recom_num"] !== ""))?($user["recom_num"]):"0.00"); ?></span>-->
            <!--</a>-->
        <!--</div>-->
        <!--<div class="money_r">-->
            <!--<a href="<?php echo U('User/devote_log');?>">-->
                <!--<p>贡献值</p>-->
                <!--<span><?php echo ((isset($user["devote_num"]) && ($user["devote_num"] !== ""))?($user["devote_num"]):"0.00"); ?></span>-->
            <!--</a>-->
        <!--</div>-->
    </div>
    <div class="pd_ddnum_nav" style="padding-top:0;">
        <div class="aui-me-content-order">
            <a href="<?php echo U('Mobile/User/pt_order');?>" class="aui-well aui-fl-arrow">
                <div class="aui-well-bd">我的拼团</div>
                <div class="aui-well-ft">查看详情</div>
            </a>
        </div>
    	<div class="con">
			<div class="one">
                <a href="/Mobile/User/pt_order/status/1">
                    <span><?php echo ($user["doPin"]); ?></span>
                    <p>拼团中</p>
                </a>
			</div>
            <div class="one">
                <a href="/Mobile/User/pt_order/status/3">
                    <span><?php echo ($user["winPin"]); ?></span>
                    <p>拼团成功</p>
                </a>
            </div>
            <div class="one">
                <a href="/Mobile/User/pt_order/status/4">
                    <span><?php echo ($user["falsePin"]); ?></span>
                    <p>拼团失败</p>
                </a>
            </div>
    	</div>
    </div>

    <div class="aui-me-content-order" style=" box-shadow:0 -5px 5px rgba(0,0,0,0.02)">
        <a href="<?php echo U('Mobile/User/order_list');?>" class="aui-well aui-fl-arrow">
            <div class="aui-well-bd">我的订单</div>
            <div class="aui-well-ft">查看详情</div>
        </a>
    </div>
    <section class="aui-grid-content">
        <div class="aui-grid-row"  style="background:#fff; box-shadow:0 0 5px rgba(0,0,0,0.1)">
            <a href="<?php echo U('/Mobile/User/order_list',array('type'=>'WAITPAY'));?>" class="aui-grid-row-item">
                <i class="aui-icon-large aui-icon-large-sm aui-icon-wallet"></i>
                <p class="aui-grid-row-label">待支付</p>
            </a>
            <a href="<?php echo U('/Mobile/User/order_list',array('type'=>'WAITSEND'));?>" class="aui-grid-row-item">
                <i class="aui-icon-large aui-icon-large-sm aui-icon-goods"></i>
                <p class="aui-grid-row-label">待发货</p>
            </a>
            <a href="<?php echo U('/Mobile/User/order_list',array('type'=>'WAITRECEIVE'));?>" class="aui-grid-row-item">
                <i class="aui-icon-large aui-icon-large-sm aui-icon-receipt"></i>
                <p class="aui-grid-row-label">待收货</p>
            </a>
            <a href="<?php echo U('/Mobile/User/order_list',array('type'=>'WAITCCOMMENT'));?>" class="aui-grid-row-item">
                <i class="aui-icon-large aui-icon-large-sm aui-icon-refund"></i>
                <p class="aui-grid-row-label">已完成</p>
            </a>
        </div>
        <div class="geren_list">
            <ul>
                <li>
                    <a href="<?php echo U('User/qrcode');?>">
                        <i class="iconfont gonggao"><img src="/Template/mobile/wuyang/Static/images/grimg_002.png"/></i></i>
                        <p>分享好友</p>
                    </a>
                </li>
                <li>
                    <a href="<?php echo U('User/team');?>">
                        <i class="iconfont ewm"><img src="/Template/mobile/wuyang/Static/images/grimg_004.png"/></i>
                        <p>我的团队</p>
                    </a>
                </li>
                <li>
                    <a href="<?php echo U('recharge');?>">
                        <i class="iconfont jiangjin"><img src="/Template/mobile/wuyang/Static/images/grimg_003.png"/></i></i>
                        <p>余额充值</p>
                    </a>
                </li>
                <li>
                    <a href="<?php echo U('User/withdrawals');?>">
                        <i class="iconfont jiangjin"><img src="/Template/mobile/wuyang/Static/images/grimg_005.png"/></i></i>
                        <p>余额提现</p>
                    </a>
                </li>
                <!--<li>-->
                    <!--<a href="<?php echo U('User/jf_tx');?>">-->
                        <!--<i class="iconfont jiangjin"><img src="/Template/mobile/wuyang/Static/images/grimg_005.png"/></i></i>-->
                        <!--<p>积分提现</p>-->
                    <!--</a>-->
                <!--</li>-->
                <li>
                    <a href="<?php echo U('User/jj_hz');?>">
                        <i class="iconfont tuandui"><img src="/Template/mobile/wuyang/Static/images/grimg_004.png"/></i>
                        <p>积分互转</p>
                    </a>
                </li>

                <!--<li>-->
                    <!--<a href="<?php echo U('User/loan');?>">-->
                        <!--<i class="iconfont jiangjin"><img src="/Template/mobile/wuyang/Static/images/grimg_006.png"/></i></i>-->
                        <!--<p>股东退股</p>-->
                    <!--</a>-->
                <!--</li>-->
                <!--<li>-->
                    <!--<a href="<?php echo U('User/money_log');?>">-->
                        <!--<i class="iconfont jiangjin"><img src="/Template/mobile/wuyang/Static/images/grimg_007.png"/></i></i>-->
                        <!--<p>余额明细</p>-->
                    <!--</a>-->
                <!--</li>-->
                <!--<li>-->
                    <!--<a href="<?php echo U('User/jifen_log');?>">-->
                        <!--<i class="iconfont jiangjin"><img src="/Template/mobile/wuyang/Static/images/grimg_006.png"/></i></i>-->
                        <!--<p>积分明细</p>-->
                    <!--</a>-->
                <!--</li>-->
                <li>
                    <a href="<?php echo U('User/address_list');?>">
                        <i class="iconfont shouhuo"><img src="/Template/mobile/wuyang/Static/images/grimg_008.png"/></i></i>
                        <p>收货地址</p>
                    </a>
                </li>
                <li>
                    <a href="<?php echo U('User/gong');?>">
                        <i class="iconfont ewm"><img src="/Template/mobile/wuyang/Static/images/grimg_010.png"/></i>
                        <p>系统消息</p>
                    </a>
                </li>
                <li>
                    <a href="<?php echo U('User/contact');?>">
                        <i class="iconfont gonggao"><img src="/Template/mobile/wuyang/Static/images/grimg_009.png"/></i></i>
                        <p>联系我们</p>
                    </a>
                </li>
                <script>
                    //分享房间
                    function share_two() {
                        var level = "<?php echo ($user["level"]); ?>";
                        level = parseInt(level);
                        if(level == 2){
                            window.location.href="<?php echo U('User/qrcode_home');?>";
                        }else{
                            layer.msg("团长才能分享房间！");
                            return false;
                        }
                    }
                </script>
            </ul>
        </div>
        <div class="tuichu_con">
            <a href="<?php echo U('User/logout');?>" class="sousou" style="color: white">
                <input class="tuichu" type="button" value="退出登录">
            </a>
        </div>
    </section>
    <!--底部区域开始-->
    <!--底部区域开始-->
<div style=" height:55px;"></div>
<div class="footer">
    <div class="nav_list">
        <ul class="">
            <li style="width: 25%" <?php if($controller == 'Index' and $action == 'index'): ?>class="on "<?php endif; ?>>
                <a href="<?php echo U('Index/index');?>">
                    <div class="icon"><i class="iconfont">&#xe609;</i></div>
                    <div class="nav_title">
                        首页
                    </div>
                </a>
            </li>
            <li style="width: 25%" <?php if($controller == 'Goods'): ?>class="on "<?php endif; ?>>
            <a href="<?php echo U('Goods/list2');?>">
                <div class="icon"><i class="iconfont">&#xe628;</i></div>
                <div class="nav_title">
                    积分专区
                </div>
            </a>
            </li>
            <li style="width: 25%" <?php if($controller == 'User' and $action == 'updLevel'): ?>class="on "<?php endif; ?>>
                <a href="<?php echo U('User/updLevel');?>">
                    <div class="icon"><i class="iconfont">&#xe6d0;</i></div>
                    <div class="nav_title">
                        优享会员
                    </div>
                </a>
            </li>
            <li style="width: 25%" <?php if($controller == 'User' and $action != 'updLevel'): ?>class="on "<?php endif; ?>>
                <a href="<?php echo U('User/index');?>">
                    <div class="icon"><i class="iconfont">&#xe646;</i></div>
                    <div class="nav_title">
                        我的
                    </div>
                </a>
            </li>


        </ul>
    </div>
</div>
<!--底部区域结束-->
<script>

</script>
    <!--底部区域结束-->
</div>
</body>
</html>