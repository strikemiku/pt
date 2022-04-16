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
    <title>拼团订单</title>
</head>

<body>
<div class="main-container">
    <div class="header_list">
        <a href="<?php echo U('User/index');?>" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
        <h3>拼团订单</h3>
    </div>
    <div style="height:50px;"></div>
    <div class="ptdd_list">
    	<div class="nav">
    		<ul>
                <a href="/Mobile/User/pt_order/status/1"><li style="color: black;width: 16%;" <?php if($status == 1): ?>class="on"<?php endif; ?> >拼团中</li></a>
                <a href="/Mobile/User/pt_order/status/2"><li style="color: black;width: 16%;" <?php if($status == 2): ?>class="on"<?php endif; ?> >拼团成功</li></a>
                <a href="/Mobile/User/pt_order/status/3"><li style="color: black;width: 16%;" <?php if($status == 3): ?>class="on"<?php endif; ?> >未成团</li></a>
                <a href="/Mobile/User/pt_order/status/4"><li style="color: black;width: 16%;" <?php if($status == 4): ?>class="on"<?php endif; ?> >拼团失败</li></a>
    		</ul>
    	</div>
    	<div class="con">
    		<div class="con_one">
                <?php if(is_array($list)): foreach($list as $key=>$v): ?><div class="one">
                        <a href="javascript:void(0);">
                            <div class="top_con">
                                <img src="/Template/mobile/wuyang/Static/images/pd_titleimg.png">
                                <span><?php echo (date("Y-m-d H:i:s",$v['add_time'])); ?></span>
                                <em>
                                    <?php if($v[status] == 1): ?>拼团中
                                        <?php elseif($v[status] == 2): ?>
                                        拼团成功
                                        <?php elseif($v[status] == 3): ?>
                                        未成团
                                        <?php elseif($v[status] == 4): ?>
                                        拼团失败<?php endif; ?>
                                </em>
                            </div>
                            <div class="cen_con">
                                <img src="<?php echo ($v['img']); ?>">
                                <div class="text">
                                    <span><?php echo ($v['goods_name']); ?></span>
                                    <p>￥<?php echo ($v['pt_price']); ?><em>￥<?php echo ($v['market_price']); ?></em></p>
                                </div>
                            </div>
                            <div class="bottom_con">
                                <p>拼团编号:<?php echo ($v['home_code']); ?></p>
                                <!--<button type="button">复制</button>-->
                            </div>
                        </a>
                    </div><?php endforeach; endif; ?>
    		</div>
    	</div>
    </div>
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
    
    
    <script>
 $(document).ready(function(){
        $(".nav li").click(function(){
        $(".nav li").eq($(this).index()).addClass("on").siblings().removeClass('on');
		$(".con .con_one").hide().eq($(this).index()).show();

        });
    });
 </script> 

    
</div>
</body>
</html>