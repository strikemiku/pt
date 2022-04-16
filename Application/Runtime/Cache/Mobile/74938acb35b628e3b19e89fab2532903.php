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
    <title>余额明细</title>
</head>

<body>
<div class="main-container">
    <div class="header_list">
        <a href="javascript:history.back(-1);" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
        <h3>余额明细</h3>
    </div>

    <div style="height:45px;"></div>
    <div class="jiangjin_con">
        <dl>
            <dt class="clearfixd">
                <span>类型</span>
                <span>数额</span>
                <span>时间</span>
                <span>来源</span>
            </dt>
            <?php if(is_array($list)): foreach($list as $key=>$v): ?><dd class="clearfixd">
                    <span>
                       <?php if($v["type"] == 1): ?>会员充值
                            <?php elseif($v["type"] == 2): ?>
                            系统充值
                            <?php elseif($v["type"] == 3): ?>
                            拼团失败
                            <?php elseif($v["type"] == 4): ?>
                            会员提现
                            <?php elseif($v["type"] == 5): ?>
                            余额支付
                            <?php elseif($v["type"] == 6): ?>
                            开通会员
                            <?php elseif($v["type"] == 7): ?>
                            抽奖活动
                            <?php elseif($v["type"] == 8): ?>
                            红包奖励
                            <?php elseif($v["type"] == 9): ?>
                            直推奖励
                            <?php elseif($v["type"] == 10): ?>
                            间推奖励
                            <?php else: ?>
                            其他方式<?php endif; ?>
                    </span>
                    <span>
                        <?php if($v["action"] == 1): ?>+
                            <?php else: ?>
                            -<?php endif; ?>
                        <?php echo ($v['money']); ?>
                    </span>
                    <span><?php echo ($v['add_time']); ?></span>
                    <span><?php echo ((isset($v['from_user_name']) && ($v['from_user_name'] !== ""))?($v['from_user_name']):"---"); ?></span>
                </dd><?php endforeach; endif; ?>
        </dl>
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
</div>
</body>
</html>