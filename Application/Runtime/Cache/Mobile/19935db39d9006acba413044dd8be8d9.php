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
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/home.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="iTunesArtwork@2x.png" sizes="114x114" rel="apple-touch-icon-precomposed">
    <script src="/Template/mobile/wuyang/Static/new/js/jquery.min.js"></script>
    <!--<script src="/Template/mobile/wuyang/Static/new/js/scrolltopcontrol.js"></script>-->
    <script src="/Template/mobile/wuyang/Static/new/js/aui.js"></script>
    <script src="/Template/mobile/wuyang/Static/new/js/aui-down.js"></script>
    <script src="/Template/mobile/wuyang/Static/new/js/aui-car.js"></script>
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
        }
        .item1 .ui-number .increase {
            display: inline-block;
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
        .goods_content{
            display:-webkit-box;
            -webkit-box-orient:vertical;
            -webkit-line-clamp:2;
            overflow: hidden;;
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
                <a href="javascript:void(0);"> <img src="<?php echo ($v['image_url']); ?>" style="height: 200px;"> </a>
            </div><?php endforeach; endif; ?>
    </div>
    <div class="aui-banner-pagination"></div>
</div>
<div class="aui-product-content" style="margin-top: 20px;">
    <div class="aui-real-price clearfix" style="background: none">
        <span>
          <span id="good_price">
             <p style="color: red">
                 ￥<?php echo ($goods['shop_price']); ?>
             </p>
          </span>
            <del class="aui-list_it_price" style="color: gray;font-size: 12px;margin-top: 6px;margin-left: 5px;">
                     ￥<?php echo ($goods['market_price']); ?>
            </del>
        </span>
        <span  style="margin-left: 5px;" >
        <i id="gains"></i>
        </span>
    </div>
    <div class="aui-product-title">
        <h2> <?php echo ($goods['goods_name']); ?> </h2>
        <p> <?php echo ($goods['goods_remark']); ?> </p>
    </div>
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
                                        <span id="goods_price">
                                           <?php echo ($goods['shop_price']); ?>
                                        </span>
                                    </h3>
                                    <p><em>库存</em><span id="kucun"><?php echo ($goods['store_count']); ?></span></p>
                                    <p class="p" ></p>
                                </div>
                            </div>
                            <div class="guige_bottom">
                                <div class="guicenter_title">
                                    <p>购买数量</p>
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
                    <a href="javascript:;" class="actionsheet-action" id="cancel">

                    </a>
                    <div class="aui-product-function">
                        <!--<a  class="yellow-color" href="javascript:void(0);" onClick="AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,0);">加入购物车</a>-->
                        <a href="javascript:void(0);" style="width: 100%;background-color: red;border-radius: 1px;color: white"  class="red-color" onClick="AjaxAddCart(<?php echo ($goods[goods_id]); ?>)">立即购买</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="aui-product-pages">
        <div class="aui-product-pages-title" style="background-color: red;border-radius: 5px;">
            <h3 style="text-align: center;color: white;width: 100%">商品详情</h3>
        </div>
        <div class="aui-product-pages-img" style="display: block">
            <?php echo ($goods['goods_content']); ?>
        </div>
    </div>
</div>
<footer class="aui-footer-product">
    <div class="aui-footer-product-fixed">
        <div class="aui-footer-product-action-list" style="width: 100%">
            <!--<a class="yellow-color" style="width: 50%" href="javascript:void(0);" onClick="AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,0);">加入购物车</a>-->
            <a href="javascript:void(0);" style="width: 100%;background-color: red;border-radius: 1px;color: white"  data-ydui-actionsheet="{target:'#actionSheet',closeElement:'#cancel'}" class="red-color">立即购买</a>
        </div>
    </div>
</footer>
<script type="text/javascript">
    //    function click_search (){
    //        var search_ka = document.getElementById("search_ka");
    //        if (search_ka.className == "s-buy open ui-section-box"){
    //            search_ka.className = "s-buy ui-section-box";
    //        }else {
    //            search_ka.className = "s-buy open ui-section-box";
    //        }
    //    }
    //    function changeAtt(t) {
    //        t.lastChild.checked='checked';
    //        for (var i = 0; i<t.parentNode.childNodes.length;i++) {
    //            if (t.parentNode.childNodes[i].className == 'hover') {
    //                t.parentNode.childNodes[i].className = '';
    //                t.childNodes[0].checked="checked";
    //            }
    //        }
    //        t.className = "hover";
    //        changePrice();
    //    }
    //
    //    function changeAtt1(t) {
    //        t.lastChild.checked='checked';
    //        for (var i = 0; i<t.parentNode.childNodes.length;i++) {
    //            if (t.className == 'hover') {
    //                t.className = '';
    //                t.childNodes[0].checked = false;
    //            }
    //            else{
    //                t.className="hover";
    //                t.childNodes[0].checked = true;
    //            }
    //        }
    //        changePrice();
    //    }

    function goods_cut(){
        var num_val=document.getElementById('number');
        var new_num=num_val.value;
        var Num = parseInt(new_num);
        if(Num>1)Num=Num-1;
        num_val.value=Num;
    }
    function goods_add(){
        var num_val=document.getElementById('number');
        var new_num=num_val.value;
        var Num = parseInt(new_num);
        Num=Num+1;  num_val.value=Num;
    }
    /**
     * addcart 将商品加入购物车
     * @goods_id  商品id
     * @num   商品数量
     * @form_id  商品详情页所在的 form表单
     * @to_catr 加入购物车后再跳转到 购物车页面 默认不跳转 1 为跳转
     * layer弹窗插件请参考http://layer.layui.com/mobile/
     */
    function AjaxAddCart(goods_id)
    {
        var goods_num = $("#number").val();
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
        if(goods_num > 0){
            $.ajax({
                type: "POST",
                url: "/index.php?m=Mobile&c=Cart&a=ajaxAddCarts",
                data: $('#buy_goods_form').serialize(),// 你的formid 搜索表单 序列化提交
                dataType: 'json',
                success: function (data) {
                    if(data.status==1){
                        location.href = "/Mobile/Cart/cartOrder";
                    }else{
                        layer.msg(data.msg);
                        return false;
                    }
                }
            });
        }
    }
</script>
</body>
</html>