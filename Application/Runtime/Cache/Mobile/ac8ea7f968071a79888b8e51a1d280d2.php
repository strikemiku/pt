<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <link href="/Template/mobile/wuyang/Static/css/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/css/barrager.css">
    <link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/web.css"/>
    <link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/iconfont.css"/>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/jquery.min.js"></script>
    <script src="/Template/mobile/wuyang/Static/js/layer/layer.js"></script>
    <script src="/Template/mobile/wuyang/Static/js/swiper.jquery.min.js" type="text/javascript"></script>
    <script src="/Public/js/global.js"></script>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/web.js"></script>
    <title>积分专区</title>
    <style>
        .tab_nav ul li.on {
            background: #d12625;
            color: #fff;
            box-sizing: border-box;
        }
        .tab_nav ul li {
            width: 50%;
            float: left;
            padding: 6px 0;
            text-align: center;
            font-size: 14px;
            color: #fff;
            border-radius: 0;
            background: #333;
        }
        .tuijian ul {
            padding: 10px 10px 0;
        }

        .clearfixd {
            *zoom: 1;
            overflow: hidden;
        }
        .tuijian ul li {
            width: calc(50% - 5px);
            background-size: 100% 180px;
            padding: 0;
            margin-right: 10px;
            margin-bottom: 10px;
            background: #fff;
            box-sizing: border-box;
        }
        .fr_l {
            float: left;
        }
        .tuijian ul li{width:calc(50% - 5px);background-size:100% 180px;padding: 0;margin-right:10px;margin-bottom:10px; background:#fff;  box-sizing:border-box;}

        .tuijian ul li:nth-child(2n){ margin-right:0;}
        .tuijian ul li .tuijian_top{padding: 5px  5px;background-size: 100% 100%;}
        /* .tuijian ul li img{ width:100%; border-radius:8px; border: 2px solid #00ae4b;} */
        .tuijian ul li img{ width:100%; border-radius:8px; border: 2px solid #f04533;}

        .tuijian ul li .tuijian_list_top{ padding:0 10px;}
        .tuijian ul li .tuijian_list_top h1{font-size: 12px;margin:10px auto 0;color: #333;height: 40px;line-height:1.8em;text-align:left;display:block;width:100%;overflow:hidden;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;font-weight: 800;}
        /* .tuijian ul li .tuijian_list_top h1 em{ background: #00ae4b; color:#ffffc7; font-size:12px; padding:2px 5px; margin-right:5px;} */
        .tuijian ul li .tuijian_list_top h1 em{ background: #f04533; color:#ffffc7; font-size:12px; padding:2px 5px; margin-right:5px;}

        .tuijian ul li .tuijian_list_top .nr_txt{width:100%;margin: 5px auto;}

        .tuijian ul li .tuijian_list_top i{display:inline-block;width: 62px;overflow:hidden;font-style: normal;height: 20px;line-height: 20px;float: left;margin-top: 3px;font-weight: 800;}
        .tuijian ul li .tuijian_list_top p{width:100%;font-size: 14px;line-height:1.8em;color:#333;text-align:left;}
        .tuijian ul li .tuijian_list_top .nr_txt span{border:1px solid #d0171e; margin-top:5px;float: right;font-size: 12px;color: #d2151b;}
        .tuijian ul li .tuijian_list_top .nr_txt p{ width: auto;  float:left;}
        .tuijian ul li .tuijian_list_top p em{font-size: 12px;}
        .tuijian ul li .goumai_con{width:70%;border-radius:50px;padding: 5px 5px;box-sizing:border-box;margin: 0 auto 0;}
        .tuijian ul li .goumai_con .left{ float:left; }
        .tuijian ul li .goumai_con .left p{font-size:16px;color:#fff;line-height: 29px; float:left;margin-right: 5px;}
        .tuijian ul li .goumai_con .left em{font-size:12px;color:#fff;margin-top: 7px; float:left; }
        .tuijian ul li .goumai_con .right{ float:none; border-radius:0; padding:5px 0;}
        .tuijian ul li .goumai_con .right p{ font-size:12px; color:#333; float:none; margin-right:5px;}
        .tuijian ul li .goumai_con .right p span{ font-size:14px;}
        .tuijian ul li .goumai_con .right p em{ background:#000; color:#fff; padding:0 5px; margin-left:5px;}
        .tuijian ul li .goumai_con .right i{ display:block; width:10px; height:10px; float:left; margin-top:5px}
        .tuijian ul li .goumai_con .right i img{ width:100%; float:left;}
        .tuijian ul li > p{ color:#666; font-size:12px; line-height:1.8em;overflow:hidden;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical; height:42px;}
        .tuijian ul li .tuijian_listtop_con .tuijian_list_top p{}
        .tuijian ul li .tuijian_listtop_con  .goumai_con{width:100%;padding:0 10px;}
        .tuijian ul li .tuijian_listtop_con  .goumai_con .left{text-align: left; float:none;}
        .tuijian ul li .tuijian_listtop_con  .goumai_con .left p{font-size:12px;color: #f00;line-height: 0;margin-right: 0;display:  inline-block;float:none; font-weight:800}
        .tuijian ul li .tuijian_listtop_con  .goumai_con .left p span{ font-size:14px;}
        .tuijian ul li .tuijian_listtop_con  .goumai_con .left em{font-size:12px;color:#fff;margin-top: 7px;display:  inline-block; float:none;}
    </style>
</head>

<body>
<div class="main-container" style="background-color: #FFFFFF;">
    <div class="header_list" style="background-color: #f44435;">
        <h3 style="color: #fff;">
            积分专区
        </h3>
    </div>
    <div style="height:50px;"></div>
    <!--精品推荐开始-->
    <div class="tab_con">
        <div class="con" style="display:block;">
            <?php if($list != null): ?><div class="rexiao">
                    <div class="rexiao_con clearfixd">
                        <div class="tuijian">
                            <ul class="clearfixd">
                                <?php if(is_array($list)): foreach($list as $key=>$vo): ?><li class="fr_l" style="background-color: #f6f6f6;box-sizing: border-box;padding: 15px;border-radius: 10px;">
                                        <a href="<?php echo U('Mobile/Goods/goods2',array('id'=>$vo[goods_id]));?>">
                                            <div class="tuijian_top">
                                                <img src="<?php echo ($vo['original_img']); ?>" style="width: 100%;height: 133px;border: none;"/>
                                            </div>
                                            <div class="tuijian_bottom tuijian_listtop_con">
                                                <div class="tuijian_list_top clearfixd">
                                                    <h1>
                                                        <em>
                                                            热销产品
                                                        </em>
                                                        <?php echo ($vo['goods_name']); ?>
                                                    </h1>
                                                </div>
                                                <div class="goumai_con" style="width: 100%;margin-top: 10px;padding: 0;">
                                                    <div class="right" style="width: 100%;background-color: #f6f6f6;">
                                                        <p style="float: left;color: red;border-radius: 2px;line-height:15px;font-weight:bolder; width: 50%;text-align: center"><span><?php echo ($vo['shop_price']*100); ?></span>积分</p>
                                                        <p style="float: right;">&yen;<del><?php echo ($vo['market_price']); ?></del></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li><?php endforeach; endif; ?>
                            </ul>
                        </div>
                    </div>
                </div><?php endif; ?>
        </div>
    </div>
    <!--精品推荐结束-->
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
    <script type="text/javascript">
        $(document).ready(function(){
            $(".tab_nav li").click(function(){
                $(".tab_nav li").eq($(this).index()).addClass("on").siblings().removeClass('on');
                $(".tab_con .con").hide().eq($(this).index()).show();
            });
        });
    </script>
</div>
</body>
</html>