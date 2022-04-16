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
    <title>我的团队</title>
    <style>
        .team_nav{ width:100%; background:#fff; box-shadow:0 0 5px rgba(0,0,0,0.1); position:relative; z-index:9; margin-top:15px;}
        .team_nav ul::after{ content:""; display:block; clear:both;}
        .team_nav ul li{ width:50%; float:left; padding:15px 0; text-align:center; font-size:14px; color:#333;}
        .team_nav ul li.on{border-bottom: 2px solid #e02e24;color:#e02e24;box-sizing:border-box;}
        .team_con .con{ display:none;}
    </style>
</head>

<body>
<div class="main-container">
    <div class="header_list">
        <a href="javascript:history.back(-1);" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
        <h3>我的团队</h3>

    </div>
    <div style="height:45px;"></div>

    <!--<div class="team_nav">-->
        <!--<ul>-->
            <!--<li class="on">-->
                <!--直推会员-->
            <!--</li>-->
            <!--<li>-->
                <!--间推会员-->
            <!--</li>-->
        <!--</ul>-->
    <!--</div>-->
    <div class="team_con">
        <div class="con" style="display:block;">
            <div class="tuandui_container">
                <dl>
                    <!--<dt class="clearfixd">
                        <span style="width: 100%">团队人数:<?php echo ($user['team']); ?>人</span>

                    </dt>-->
                    <dt class="clearfixd">
                        <span style="width:33.33%;color: red;">会员姓名</span>
                        <span style="width: 33.33%;color: red;">手机号</span>
                        <span style="width: 33.33%;color: red;">注册时间</span>
                    </dt>
                    <?php if(is_array($list)): foreach($list as $key=>$v): ?><dd class="clearfixd">
                            <span style="width: 33.33%"><?php echo ($v['nickname']); ?></span>
                            <span style="width: 33.33%"><?php echo ($v['mobile']); ?></span>
                            <span style="width: 33.33%"><?php echo (date('Y-m-d',$v['reg_time'])); ?></span>
                        </dd><?php endforeach; endif; ?>
                </dl>
            </div>
        </div>

        <div class="con">
            <div class="tuandui_container">
                <dl>
                    <dt class="clearfixd">
                        <span style="width:33.33%">会员姓名</span>
                        <span style="width: 33.33%">手机号</span>
                        <span style="width: 33.33%">注册时间</span>
                    </dt>
                    <?php if(is_array($relation)): foreach($relation as $key=>$vv): ?><dd class="clearfixd">
                            <span style="width: 33.33%"><?php echo ($vv['nickname']); ?></span>
                            <span style="width: 33.33%"><?php echo ($vv['mobile']); ?></span>
                            <span style="width: 33.33%"><?php echo (date('Y-m-d',$vv['reg_time'])); ?></span>
                        </dd><?php endforeach; endif; ?>
                </dl>
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
</div>
<script>
    // author :凌寒 2018年5月17日13:53:02 执行充值方法
    function jihuo(user_id,is_lock)
    {
        if(user_id == ''){
            layer.msg('会员不存在!',{icon:5,time:1500});
            return false;
        }
        if(is_lock == 1){
            layer.msg("会员已激活!",{icon:5,time:1500});
            return false;
        }

        layer.confirm('您确定要激活该用户吗?',{btn: ['激活', '取消'],title:"温馨提示"}, function(){
            $.ajax({
                type : 'post',
                url : '/index.php?m=Mobile&c=User&a=team&t='+Math.random(),
                data : {jh_user_id:user_id},
                dataType : 'json',
                success : function(res){
                    if(res.status == 200){
                        layer.msg(res.msg,{icon:6,time:1500},function () {
                            window.location.reload();
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
        });
    }
</script>
<script>
    $(document).ready(function(){
        $(".team_nav li").click(function(){
            $(".team_nav li").eq($(this).index()).addClass("on").siblings().removeClass('on');
            $(".team_con .con").hide().eq($(this).index()).show();

        });
    });
</script>
</body>
</html>