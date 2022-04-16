<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <link href="/Template/mobile/wuyang/Static/css/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/web.css"/>
    <link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/iconfont.css"/>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/jquery.min.js"></script>
    <script src="/Template/mobile/wuyang/Static/js/layer/layer.js"></script>
    <script src="/Template/mobile/wuyang/Static/js/swiper.jquery.min.js" type="text/javascript"></script>
    <script src="/Public/js/global.js"></script>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/web.js"></script>
    <title>首页</title>
</head>

<body>
<div class="main-container">
    <!--海报区域开始-->
    <div class="banner">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?php if(is_array($lun)): foreach($lun as $key=>$v): ?><div class="swiper-slide"><a href="<?php echo ($v["ad_link"]); ?>"><img src="<?php echo ($v["ad_code"]); ?>" alt="<?php echo ($v["ad_name"]); ?>" style="width:100%; !important;"/></a></div><?php endforeach; endif; ?>
            </div>
            <!-- 如果需要分页器 -->
            <div class="swiper-pagination"></div>

        </div>
        <script>
            var mySwiper = new Swiper ('.swiper-container', {
                direction: 'horizontal',
                loop: true,
                autoplay:5000,

                // 如果需要分页器
                pagination: '.swiper-pagination',

            })
        </script>

    </div>
    <!--海报区域结束-->
    <!--<div class="banner_btbg"><img src="/Template/mobile/wuyang/Static/images/fenge_img.png"/></div>-->
    <!--导航区域开始-->
    <nav class="erji_nav">
        <ul class="clearfixd">
            <li class="fr_l" style="width: 20%"><a href="<?php echo U('User/qrcode');?>"><img src="/Template/mobile/wuyang/Static/images/index_001.png" alt=""/><p>分享好友</p></a></li>
            <li class="fr_l" style="width: 20%"><a href="<?php echo U('Goods/list2');?>"><img src="/Template/mobile/wuyang/Static/images/index_008.png" alt=""/><p>积分商城</p></a></li>
            <li class="fr_l" style="width: 20%"><a href="<?php echo U('User/updLevel');?>"><img src="/Template/mobile/wuyang/Static/images/index_002.png" alt=""/><p>优享会员</p></a></li>
            <li class="fr_l" style="width: 20%"><a onclick="game()"><img src="/Template/mobile/wuyang/Static/images/index_003.png" alt=""/><p>抽奖活动</p></a></li>
            <li class="fr_l" style="width: 20%"><a href="<?php echo U('User/sign');?>"><img src="/Template/mobile/wuyang/Static/images/index_004.png" alt=""/><p>每日签到</p></a></li>
        </ul>
    </nav>
    <!--导航区域结束-->
    <!--注册公告区域开始-->
    <div class="zc_gg s_section clearfixd">
        <div class="zhuce_left fr_l">
            <img src="/Template/mobile/wuyang/Static/images/gonggao.png"/>
        </div>
        <div class="zhuce_right fr_l">
            <ul id="ticker-1" style=" font-size:12px; color:#666; line-height:30px; margin:0;">
                <li style="margin:0; float:left; white-space:nowrap;"><?php echo ($config['shop_info_index_desc']); ?></li>
                <li style="margin:0; float:left; white-space:nowrap;"><?php echo ($config['shop_info_index_desc']); ?></li>
            </ul>
        </div>
    </div>

    <!--注册公告区域结束-->
    <script>
        var mySwiper = new Swiper ('.swiper-container1', {
            direction: 'horizontal',
            loop: true,
            autoplay:5000,

            // 如果需要分页器
            pagination: '.swiper-pagination1',

        });
        function game() {
            var is_open = parseInt("<?php echo ($config['basic_chou_open']); ?>");
            if(is_open==0){
                layer.msg("抽奖活动暂未开放",{time:1500});
                return false;
            }
            window.location.href="<?php echo U('User/game');?>";
        }
        $(document).ready(function(){
            var price = "<?php echo ($groupList[0]['price']); ?>";
            $(".index_nav_ul li").eq(0).addClass("on").siblings().removeClass('on');
            ajaxList(price);
            $(".index_nav_ul li").click(function(){
                $(".index_nav_ul li").eq($(this).index()).addClass("on").siblings().removeClass('on');
                var nowPrice = $(".index_nav_ul li").eq($(this).index()).attr('title');
                ajaxList(nowPrice);
            });
        });
        function ajaxList(price) {
            $.ajax({
                type : "POST",
                url:"<?php echo U('Mobile/Index/ajaxList');?>",
                data : {price:price},
                success: function(data)
                {
                    $("#showList").html("");
                    $("#showList").append(data);
                }
            });
        }
    </script>
    <div class="index_title"><img src="/Template/mobile/wuyang/Static/images/pd_titleimg.jpg"></div>
    <div class="index_nav_ul">
        <ul>
            <?php if(is_array($groupList)): foreach($groupList as $k=>$v): ?><li style="width: 20%" title="<?php echo ($v[price]); ?>"><?php echo ($v['name']); ?></li><?php endforeach; endif; ?>
        </ul>
    </div>

    <!--精品推荐开始-->
    <div class="indexsp_con">
        <div class="rexiao">
            <div class="rexiao_con clearfixd">
                <div class="tuijian">
                    <ul class="clearfixd" id="showList">

                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="zhbd_con_bg" style="display: none"></div>
    <div class="zhbd_con" style="display: none">
        <div class="con">
            <i><img src="/Template/mobile/wuyang/Static/images/close_h.png"/></i>
            <p>恭喜您获得新人红包</p>
            <span><?php echo ($config['basic_red_money']); ?><em>元</em></span>
        </div>
        <div class="bottom">
            <a href="javascript:void(0)" onclick="send_red()"><img src="/Template/mobile/wuyang/Static/images/lingqu_btn.png"/></a>
        </div>
    </div>
    <div class="gz_gzhtanbg" style="display: none"></div>
    <div class="gz_gzhtancon" style="display: none">
    	<div class="title">
    		<img src="/Template/mobile/wuyang/Static/images/close.png">
    	</div>
    	<div class="con">
    		<img src="<?php echo ($config['shop_info_gzh_img']); ?>">
    		<span>长安二维码关注公众号</span>
    		<!--<button type="button">关注</button>-->
    		<p>为了让您有更好的购物体验，请先关注公众号 <em style="color: red">[上海谷亮]</em> 再进行拼团下单</p>
    	</div>
    </div>
    
    
    <!--<div class="xf_btn" style="width: 60px;height: 60px;font-size: 15px;font-weight: bolder;top:47%;">-->
    	<!--<p>关注<br>-->
        <!--公众号</p>-->
    <!--</div>-->
    <!--弹框-->
    <script>
        $(function(){
//            var subscribe = "<?php echo ($user['subscribe']); ?>";
//            if(subscribe==0 || !subscribe){
//                $(".gz_gzhtanbg,.gz_gzhtancon").fadeIn();
//            }
            $(".zhbd_con_bg,.zhbd_con .con i,.zhbd_con .bottom a,.zhbd_con .bottom button").click(function(){
                $(".zhbd_con_bg,.zhbd_con").fadeOut();
            });
			
			$(".xf_btn").click(function(){
                $(".gz_gzhtanbg,.gz_gzhtancon").fadeIn();
            });
			$(".gz_gzhtanbg,.gz_gzhtancon .title img").click(function(){
                $(".gz_gzhtanbg,.gz_gzhtancon").fadeOut();
            });
        });
        //领红包操作
        function send_red()
        {
            $.ajax({
                type : 'post',
                url :"<?php echo U('Mobile/User/ling_red');?>",
                data : {},
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
                    layer.msg('网络失败，请刷新页面后重试');
                }
            })
        }
    </script>



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
            var cart_cn = getCookie('cn');
            if(cart_cn == ''){
                $.ajax({
                    type : "GET",
                    url:"/index.php?m=Home&c=Cart&a=header_cart_list",//+tab,
                    success: function(data){
                        cart_cn = getCookie('cn');
                        $('#cart').html(cart_cn);
                    }
                });
            }
            $('#cart').html(cart_cn);
        });
    </script>

    <script src="/Template/mobile/wuyang/Static/js/jquery.carouFredSel-6.0.4-packed.js"></script>
    <script type="text/javascript">
        $(function() {
            var _scroll = {
                delay: 1000,
                easing: 'linear',
                items: 1,
                duration: 0.07,
                timeoutDuration: 0,
                pauseOnHover: 'immediate'
            };
            $('#ticker-1').carouFredSel({
                width: 1200,
                align: false,
                items: {
                    width: 'variable',
                    height: 30,
                    visible: 1
                },
                scroll: _scroll
            });


            //	set carousels to be 100% wide
            $('.caroufredsel_wrapper').css('width', '100%');
        });
    </script>
</div>
</body>
</html>