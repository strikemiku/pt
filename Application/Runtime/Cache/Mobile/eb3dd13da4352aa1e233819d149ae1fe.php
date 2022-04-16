<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no, email=no"/>
    <meta charset="UTF-8">
    <title>商品详情</title>
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/core.css">
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/icon.css">
    <link rel="stylesheet" type="text/css" media="all" href="/Template/mobile/wuyang/Static/css/countdown.css" />
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/home.css">
    <script src="/Template/mobile/wuyang/Static/new/js/jquery.min.js"></script>
    <script src="/Template/mobile/wuyang/Static/js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/countdown.js" ></script>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/new/layer/layer.js"></script>

    <style>
        #aa .colors{
            background-color:#FF5E53 ;
            color:#fff;
        }
        .item1 .ui-number .num {
            display: inline-block;
            border: 1px;
            width: 40px;
            height: 30px;
            float: right;
            text-align: center;
            font-size: 12px;
            line-height: 30px;
            color: #666;
            text-align: center;
        }
        .item1 .ui-number .increase {
            display: inline-block;
            background: none;
            border: 0;
            border-left: 1px solid #ddd9da;
            float: right;
            width: 30px;
            height: 30px;
            font-size: 24px;
            line-height: 30px;
            color: #F8849C;
            background: url(/Template/mobile/wuyang/Static/new/images/flow/shop-cart.png) no-repeat -23px -25px;
            background-size: 60px;
            text-indent: -9999px;
        }
        .item1 .ui-number .decrease {
            display: inline-block;
            background: none;
            font-size: 24px;
            line-height: 30px;
            border: 0;
            width: 30px;
            float: right;
            height: 30px;
            color: #F8849C;
            border-right: 1px solid #ddd9da;
            text-indent: -9999px;
            background: url(/Template/mobile/wuyang/Static/new/images/flow/shop-cart.png) no-repeat 6px -25px;
            background-size: 60px;
        }
        .aui-product-pages-img img {
            margin: 0px;
        }
        .pt_procon .con .one .text em {
            font-size: 14px;
            color: black;
        }
        .pd_tancon .one em {
            width: 100%;
            color: gray;
        }
        .payment-time em {
            color: black;
            background-color: white;
            padding: 0 3px;
            margin: 0 2px;
            -webkit-border-radius: 1px;
            -moz-border-radius: 1px;
            border-radius: 5px;
            font-style: normal;
        }
        .active-time .time_prefix{
            color: gray;
        }
        .layui-layer-btn .layui-layer-btn0 {
            border-color: red;
            background-color: red;
            color: #fff;
        }
    </style>
</head>
<body>
<header class="aui-header-default aui-header-fixed aui-header-bg"> <a href="javascript:history.go(-1);"  class="aui-header-item" style="position: relative;z-index:1000;"> <i class="aui-icon aui-icon-back-white"></i> </a>
    <div class="aui-header-center aui-header-center-clear">
        <div class="aui-header-center-logo">
            <div class="aui-car-white-Typeface">商品详情</div>
        </div>
    </div>
</header>
<div class="aui-banner-content aui-fixed-top" data-aui-slider>
    <div class="aui-banner-wrapper">
        <?php if(is_array($goods_images_list)): foreach($goods_images_list as $key=>$v): ?><div class="aui-banner-wrapper-item">
                <a href="javascript:void(0);">
                    <img src="<?php echo ($v['image_url']); ?>" style="height: 200px">
                </a>
            </div><?php endforeach; endif; ?>
    </div>
    <div class="aui-banner-pagination"></div>
</div>
<div class="aui-product-content">
    <div class="aui-real-price clearfix" style=" padding:15px;">
    	<div class="pt_con">
        	<p>团购价<span><?php echo ($goods['group_num']); ?>人团</span></p>
        </div>
        <span>
              <span id="good_price" style="font-size: 24px;">
                 ￥<?php echo ($goods['shop_price']); ?>
              </span>
            <span class="aui-list_it_price" style="color:#fff;font-size: 12px;margin-top: 12px;margin-left: 5px;text-decoration: line-through">
               ￥<?php echo ($goods['market_price']); ?>
            </span>
        </span>
    </div>
    <div class="aui-product-title" >
        <h2> <?php echo ($goods['goods_name']); ?> </h2>
        <!--<p> <?php echo ($goods['goods_remark']); ?> </p>-->
    </div>
    <div class="ptrs_con">
    	<div class="top_con">
            <p>本商品已累计拼中<?php echo ($goods['pz_num']); ?>次</p>
            <p>每团抽<?php echo ($goods['chou_num']); ?>人</p>
        </div>
    </div>
    <div class="ptwf_con">
    	<p>拼团玩法</p>
        <span>参加拼团>邀请好友>满员开团>拼团发货</span>
    </div>
    <?php if($ptList != null): ?><div class="aui-product-title pt_procon" style="margin-top:10px;">
            <h3><?php echo ($goods['total_pt_num']); ?>人正在拼团，快来一起拼</h3>
            <div class="con">
                <ul>
                    <?php if(is_array($ptList)): foreach($ptList as $key=>$v): ?><li>
                            <div class="one" style="padding: 5px 0px;">

                                <div class="tx_con">
                                    <?php if(is_array($v["orders"])): foreach($v["orders"] as $key=>$vv): if($vv['user_pic'] != null): ?><img src="<?php echo ($vv['user_pic']); ?>"/>
                                            <?php else: ?>
                                            <img src="<?php echo ($tpshop_config["shop_info_store_logo"]); ?>"/><?php endif; endforeach; endif; ?>
                                </div>
                                <a  onClick="AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,1,'<?php echo ($v["home_code"]); ?>');">房间-<em style="color: gold;font-size: 12px;"><?php echo ($v["home_code_show"]); ?></em> <br></a>
                                <div class="text">
                                    <span style="padding-left: 10px;">差<i><?php echo ($v["sy_num"]); ?></i>人成团</span>
                                </div>
                            </div>
                        </li><?php endforeach; endif; ?>
                </ul>
            </div>
        </div><?php endif; ?>
    <div class="aui-product-coupon">
        <div class="m-actionsheet" id="actionSheet">
            <form name="buy_goods_form" method="post" id="buy_goods_form" >
                <div style="position:relative">
                    <div class="guige_tan_con_con" style="height: 150px;">
                        <div class="guige_tancon">
                            <div class="guitan_top">
                                <a href="javascript:void;"><img id="picture" src="<?php echo ($goods['original_img']); ?>" /></a>
                                <div class="guitan_txt">
                                    <h3 >
                                        <em>￥</em>
                                        <span >
                                        <?php echo ($goods['shop_price']); ?>
                                        </span>
                                    </h3>
                                    <p><em>库存</em><span id="kucun"><?php echo ($goods['store_count']); ?></span></p>
                                    <p class="p" ></p>
                                </div>
                            </div>
                            <div class="guige_bottom">
                                <div class="guicenter_title">
                                    <p>购买数量 <em style="color: red">(限购一件)</em></p>
                                </div>
                                <div class="item1">
                                    <span class="ui-number">
                                          <button type="button" class="increase" onClick="goods_add();">+</button>
                                            <input type="text" class="num" id="number" name="goods_num" value="1" min="1" max="1000" readonly/>
                                            <input type="hidden" name="goods_id" id="goods_id" value="<?php echo ($goods["goods_id"]); ?>"/>
                                          <button type="button" class="decrease"  onClick="goods_cut();">-</button>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <a href="javascript:;" class="actionsheet-action" id="cancel"></a>
                    <div class="aui-product-function">
                        <a href="javascript:void(0);" class="yellow-color" id="pinone" style="border-radius: 40px;background: #313131;color: #fff;display: none;width: 70%;margin-left: 15%;"  onClick="AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,1,0);">快速拼团</a>
                        <a href="javascript:void(0);" class="red-color " id="pintwo" style="border-radius: 40px;background: #fde368;;color: black;display: none;width: 70%;margin-left: 15%;"  onClick="AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,2,0);">我要开团</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--弹框-->
    <div class="tk_bg"></div>
    <div class="pd_tancon">
        <h3>正在拼单</h3>
        <div class="close"><img src="/Template/mobile/wuyang/Static/images/close.png"/></div>
        <div class="con">
            <ul>
                <?php if(is_array($ptList)): foreach($ptList as $key=>$vv): ?><li>
                    <div class="one">
                        <?php if($vv['user_pic'] == null): ?><img src="<?php echo ($config["shop_info_store_logo"]); ?>" style="width: 40px;height: 40px;"/>
                            <?php else: ?>
                            <img src="<?php echo ($vv['user_pic']); ?>" style="width: 40px;height: 40px;"/><?php endif; ?>
                        <div class="text">
                            <p><?php echo ($vv['user_name']); ?><span style="padding-left: 15px;">还差<?php echo ($vv['ys_num']); ?>人</span></p>
                            <input type="hidden" name="countDowns" data-prefix="剩余" value="<?php echo ($vv['end_time']); ?>">
                            <span style="background-color: white;color: black"></span>
                        </div>
                        <?php if($vv[type] == 1): ?><a  onClick="AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,0);">快速拼团</a>
                            <?php else: ?>
                            <a onClick="AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,1);">房间拼团</a><?php endif; ?>
                    </div>
                    <script type="text/javascript">
                        $("input[name='countDowns']").each(function () {
                            var time_end=this.value;
                            var con=$(this).next("span");
                            var _=this.dataset;
                            countDown(con,{
                                title:_.title,//优先级最高,填充在prefix位置
                                prefix:_.prefix,//前缀部分
                                suffix:_.suffix,//后缀部分
                                time_end:time_end//要到达的时间
                            });
                        });
                    </script>
                </li><?php endforeach; endif; ?>
            </ul>
        </div>
    </div>
    <!--快速-->
    <div class="ks_pdcon">
        <p>选择房间拼团，收益更多哦！</p>
        <div class="bottom">
            <a href="javascript:void(0)" onClick="AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,0);" class="qr_pt">快速拼团</a>
            <a href="javascript:void(0)" class="ks_qx">取消</a>
        </div>
    </div>
    <!--房间号-->
    <div class="fj_pdcon">
        <p>团长房间号</p>
        <input type="text" name="home_code" id="home_code" placeholder="请输入团长房间号..." value=""/>
        <div class="bottom">
            <a onClick="AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,1);" class="qr_pt">确定进入</a>
            <a href="javascript:void(0)" class="ks_qx">取消</a>
        </div>
    </div>
    <!--参与-->
    <div class="cy_pdcon">
        <div class="close"><img src="/Template/mobile/wuyang/Static/images/close.png"/></div>
        <span>参与小熊饼干的拼团</span>
        <p>仅剩<em>1</em>个名额，20:06:00后结束</p>
        <div class="con">
            <div class="one">
                <span>团长</span>
                <img src="/Template/mobile/wuyang/Static/images/touxiang.jpeg"/>
            </div>
            <div class="one">
                <img src="/Template/mobile/wuyang/Static/images/dd_img.png"/>
            </div>
        </div>
        <a href="#" class="cy_pt">参与拼团</a>
    </div>

    <script>
        $(document).ready(function(){
            $(".ks_btn").click(function(){
                $(".tk_bg,.ks_pdcon").fadeIn();
            });

            $(".ck_btn").click(function(){

                $(".tk_bg,.pd_tancon").fadeIn();
            });

            $(".fj_btn").click(function(){

                $(".tk_bg,.fj_pdcon").fadeIn();
            });

            $(".qpt").click(function(){

                $(".tk_bg,.cy_pdcon").fadeIn();
            });

            $(".ks_qx,.pd_tancon,.pd_tancon .close,.cy_pdcon .close").click(function(){

                $(".tk_bg,.fj_pdcon,.ks_pdcon,.pd_tancon,.cy_pdcon").fadeOut()
            })
        });
    </script>
    <script>
        (function (win){
            var callboarTimer;
            var callboard = $('#callboard');
            var callboardUl = callboard.find('ul');
            var callboardLi = callboard.find('li');
            var liLen = callboard.find('li').length;
            var initHeight = callboardLi.first().outerHeight(true);

            win.autoAnimation = function (){
                if (liLen <= 1) return;
                var self = arguments.callee;
                var callboardLiFirst = callboard.find('li').first();
                callboardLiFirst.animate({
                    marginTop:-initHeight
                }, 500, function (){
                    clearTimeout(callboarTimer);
                    callboardLiFirst.appendTo(callboardUl).css({marginTop:0});
                    callboarTimer = setTimeout(self, 3000);
                });
            }

            callboard.mouseenter(
                function (){
                    clearTimeout(callboarTimer);
                }).mouseleave(function (){
                callboarTimer = setTimeout(win.autoAnimation, 3000);
            });
        }(window));
        setTimeout(window.autoAnimation, 3000);
    </script>
    <script type="text/javascript">




        function click_search (){
            var search_ka = document.getElementById("search_ka");
            if (search_ka.className == "s-buy open ui-section-box"){
                search_ka.className = "s-buy ui-section-box";
            }else {
                search_ka.className = "s-buy open ui-section-box";
            }
        }

        function changeAtt(t) {
            t.lastChild.checked='checked';
            for (var i = 0; i<t.parentNode.childNodes.length;i++) {
                if (t.parentNode.childNodes[i].className == 'hover') {
                    t.parentNode.childNodes[i].className = '';
                    t.childNodes[0].checked="checked";
                }
            }
            t.className = "hover";
            changePrice();
        }

        function changeAtt1(t) {
            t.lastChild.checked='checked';
            for (var i = 0; i<t.parentNode.childNodes.length;i++) {
                if (t.className == 'hover') {
                    t.className = '';
                    t.childNodes[0].checked = false;
                }
                else{
                    t.className="hover";
                    t.childNodes[0].checked = true;
                }
            }
            changePrice();
        }

        function goods_cut(){
            return true;
            var num_val=document.getElementById('number');
            var new_num=num_val.value;
            var Num = parseInt(new_num);
            if(Num>1)Num=Num-1;
            num_val.value=Num;
        }
        function goods_add(){
            return true;
            var num_val=document.getElementById('number');
            var new_num=num_val.value;
            var Num = parseInt(new_num);
            Num=Num+1;  num_val.value=Num;
        }
    </script>
    <script>
        $(document).ready(function(){

            var org_goods_price = <?php echo ($goods["shop_price"]); ?>; // 商品本店价格
            var store_count = <?php echo ($goods["cost_price"]); ?>; // 商品成本价格
            var user_gains = "<?php echo ($gains); ?>";
            if(user_gains){
                var gains = org_goods_price-store_count;
                if(gains >= 0){
                    gains = gains*user_gains;
                    $("#gains").html("赚&nbsp;"+gains.toFixed(2));
                }
            }else{
                $("#gains").html();
            }
            // 更新商品价格
            get_goods_price();
        });
        function switch_spec(spec) {

            if(spec){
                $("#ss_"+spec).addClass('colors').siblings().removeClass('colors');
                $("#ss_"+spec).siblings().children('input').prop('checked',false);
                $("#ss_"+spec).children('input').prop('checked',true);
            }
            //更新商品价格
            get_goods_price();
        }
        function get_goods_price() {

            var org_goods_price = <?php echo ($goods["shop_price"]); ?>; // 商品起始价
            var store_count = <?php echo ($goods["store_count"]); ?>; // 商品起始库存
            var spec_goods_price = <?php echo ($spec_goods_price); ?>;  // 规格 对应 价格 库存表   //alert(spec_goods_price['28_100']['price']);
            // 如果有属性选择项
            if(spec_goods_price != null)
            {

                goods_spec_arr = new Array();
                goods_spec_title = new Array();
                $("input[name^='goods_spec']:checked").each(function(){
                    goods_spec_title.push($(this).attr('title'));
                    goods_spec_arr.push($(this).val());
                });
                var spec_title = goods_spec_title.sort(sortNumber).join('_');
                var spec_key = goods_spec_arr.sort(sortNumber).join('_');  //排序后组合成 key
                goods_price = spec_goods_price[spec_key]['price']; // 找到对应规格的价格
                store_count = spec_goods_price[spec_key]['store_count']; // 找到对应规格的库存

            }
            var goods_num = parseInt($(".num").html());
            // 库存不足的情况
            if(goods_num > store_count)
            {
                goods_num = store_count;
                alert('库存仅剩 '+store_count+' 件');
                $(".num").val(goods_num);
            }
            //加载不同规格的图片
//            var a = goods_spec_arr.sort(sortNumber);
//            var goods_imgs = <?php echo ($specImageList); ?>;
//            for(var i=0;i<a.length;i++){
//                if(goods_imgs[a[i]]){
//                    $("#picture").attr("src",goods_imgs[a[i]]);
//                    break;
//                }
//            }
            var price = parseFloat(goods_price);
            $("#goods_price").html(price+'元'); // 变动价格显示
            $("#kucun").html(store_count+'件');
            if(spec_title != null){
                $(".p").css('display','block').html('已选择:'+spec_title);
            }
        }
        function sortNumber(a,b)
        {
            return a - b;
        }

    </script>

    <div class="aui-product-pages">
        <div class="aui-product-pages-title">
            <h3>商品详情</h3>
            <span class="fr_r zk_btn">展开<img src="/Template/mobile/wuyang/Static/new/images/xiala_img.png"></span>
        </div>
        <div class="aui-product-pages-img">
            <?php echo ($goods['goods_content']); ?>
        </div>
    </div>
    <?php if($tjList != null): ?><div class="tj_listcon">
            <h3>为你推荐</h3>
            <div class="con">
                <?php if(is_array($tjList)): foreach($tjList as $key=>$vo): ?><div class="one">
                        <a href="<?php echo U('Mobile/Goods/goodsInfo',array('id'=>$vo[goods_id]));?>">
                            <img src="<?php echo ($vo['original_img']); ?>">
                            <div class="txt_con">
                                <p><?php echo ($vo['goods_name']); ?></p>
                                <span><?php echo ($vo['group_num']); ?>人团</span>
                                <h2>￥<?php echo ($vo['shop_price']); ?><em>￥<?php echo ($vo['market_price']); ?></em></h2>
                            </div>
                            <i>立省<?php echo ($vo['sq_money']); ?>元</i>
                        </a>
                    </div><?php endforeach; endif; ?>
            </div>
        </div><?php endif; ?>
</div>

<div class="ptxf_con" style="top:87%;height: 40px;background-color: lightgray">
	<img src="/Template/mobile/wuyang/Static/new/images/hongbao_img.png">
    <p>未拼中全额退￥<?php echo ($goods['shop_price']); ?></p>
</div>



<footer class="aui-footer-product">
    <div class="aui-footer-product-fixed">
    	<div class="left">
        	<a href="<?php echo U('Index/index');?>">
            	<img src="/Template/mobile/wuyang/Static/new/images/shouye_img.png">
                <p>首页</p>
            </a>
        </div>
        <div class="aui-footer-product-action-list">
            <a href="javascript:void(0);" class="yellow-color" style="width: 50%"  onclick="openTeam(1)">一键拼团</a>
            <a href="javascript:void(0);" class="red-color" style="width: 50%" onclick="openTeam(2)" >我要开团</a>
        </div>
    </div>
</footer>

<!--<script src="/Template/mobile/wuyang/Static/new/js/scrolltopcontrol.js"></script>-->
<script src="/Template/mobile/wuyang/Static/new/js/aui.js"></script>
<script src="/Template/mobile/wuyang/Static/new/js/aui-down.js"></script>
<script src="/Template/mobile/wuyang/Static/new/js/aui-car.js"></script>

<script>
    //开团
    function openTeam(type) {
        if(type==1){
            $("#pinone").css('display','block');
            $("#pintwo").css('display','none');
        }else{
            $("#pinone").css('display','none');
            $("#pintwo").css('display','block');
        }

        $("#actionSheet").addClass("actionsheet-toggle");
    }
    $("#cancel").click(function(){
        $("#actionSheet").removeClass("actionsheet-toggle");
    });
    $("#goods_num").change(function(){
        layer.msg("会员套餐数量只能购买一件",{icon:5});
        $("#goods_num").val("1");
    });
    /**
     * 快速拼团
     */
    function AjaxAddCart(goods_id,num,type,home_code)
    {
        if($("#buy_goods_form").length > 0) {
            var user_id = "<?php echo ($user['user_id']); ?>";
            if(!user_id){
                layer.open({
                    title:'温馨提示',
                    content: '未登录->请先登录',
                    btn: ['登录', '再逛逛'],
                    shadeClose: false,
                    yes: function () {
                        location.href = "/index.php?m=Mobile&c=User&a=login";
                    },
                    no: function () {
                        layer.closeAll();
                    }
                });
                return false;
            }
            var pt_num = parseInt("<?php echo ($user['pt_num']); ?>");
            var level_pt_num = parseInt("<?php echo ($user['level_pt_num']); ?>");
            if(pt_num>=level_pt_num){
                layer.msg("今日拼团次数已达上限");
                return false;
            }
            $.ajax({
                type: "POST",
                url: "/index.php?m=Mobile&c=Cart&a=ajaxAddCart",
                data: {goods_id:goods_id,goods_num:num,type:type,home_code:home_code},
                dataType: 'json',
                success: function (data) {
                    if (data.status != 200) {
                        layer.msg(data.msg);
                        return false;
                    }
                    location.href = "/index.php?m=Mobile&c=Cart&a=cart2";
                }
            });
        }
    }
</script>
<script>
$(function(){	
	
	$(".zk_btn").click(function(){
		
		$(".aui-product-pages-img").toggleClass("on");
		
				
	})
})
</script>
</body>
</html>