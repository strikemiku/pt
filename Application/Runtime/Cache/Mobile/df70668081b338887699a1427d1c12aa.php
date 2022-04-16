<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html >
<html>
<head>
    <meta name="Generator" content="tpshop" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>确认订单</title>
    <meta http-equiv="keywords" content="<?php echo ($tpshop_config['shop_info_store_keyword']); ?>" />
    <meta name="description" content="<?php echo ($tpshop_config['shop_info_store_desc']); ?>" />
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/public.css">
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/flow.css">
    <link rel="stylesheet" href="/Template/mobile/wuyang/Static/new/css/style_jm.css">
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/new/js/jquery.js"></script>
    <script src="/Public/js/global.js"></script>
    <script src="/Public/js/mobile_common.js"></script>
    <script src="/Template/mobile/wuyang/Static/new/js/common.js"></script>
    <script src="/Template/mobile/wuyang/Static/js/layer/layer.js"></script>
    
</head>
<body style="background:#f8f8f8;position:relative;">
<div class="tab_nav">
    <div class="header">
        <div class="h-left"> <a class="sb-back" href="javascript:history.back(-1)" title="返回"></a> </div>
        <div class="h-mid"> 确认订单 </div>
    </div>
</div>
<form name="cart2_form" id="cart2_form" method="post" action="<?php echo U('Mobile/Payment/getPay');?>">
    <div class="screen-wrap fullscreen login">
        <section class="content" >
            <div class="wrap">
                <div class="active">
                    <div class="content-buy">
                        <div class="wrap order-buy">
                            <a href="<?php echo U('User/address_list',array('source'=>'cart2'));?>">
                                <section class="address">
                                    <div class="address-info" >收货人:
                                        <p class="address-name"><?php echo ($address["consignee"]); ?></p>
                                        <p class="address-phone"><?php echo ($address["mobile"]); ?></p>
                                    </div>
                                    <div class="address-details">收货地址：<?php echo ($address["pcd"]); echo ($address["address"]); ?></div>
                                    <input type="hidden"  name="address_id" value="<?php echo ($address["address_id"]); ?>"/>
                                </section>
                            </a>
                            <section class="order-info" style=" margin-top:0px;">
                                <div class="order-list">
                                    <div class="goods-list-title"> 商品详情</div>
                                    <ul class="order-list-info">
                                        <?php if(is_array($cartList)): foreach($cartList as $key=>$v): ?><li class="item" >
                                                <div class="itemPay list-price-nums" id="itemPay17">
                                                    <p class="price" style="padding-top: 5px;">￥<?php echo ($v["goods_price"]); ?> <br><br><span style="color: black;">x&nbsp; <?php echo ($v["goods_num"]); ?></span>  </p>
                                                    <p class="nums"></p>
                                                </div>
                                                <div class="itemInfo list-info" id="itemInfo12">
                                                    <div class="list-img"><img src="<?php echo ($v["original_img"]); ?>" style="width: 60px;height: 60px;"></div>
                                                    <div class="list-cont">
                                                        <h5 class="goods-title"><?php echo ($v["goods_name"]); ?> </h5>
                                                        <p class="godds-specification"><?php echo ($v["keywords"]); ?></p>
                                                    </div>
                                                </div>
                                            </li><?php endforeach; endif; ?>
                                    </ul>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
        </section>
        <div class="xqjg_numcon">
            <div class="one">
                <span>拼团价格</span>
                <p>￥<?php echo ($total_price['total_fee']); ?></p>
            </div>
            <div class="one">
                <span>配送方式</span>
                <p>
                    包邮
                </p>
            </div>
            <div class="one">
                <span>订单备注</span>
                <input type="textarea" id="order_desc" placeholder="选填内容，请填写您的备注消息0/100">
            </div>
            <div class="two">
                <p>共<?php echo ($total_price['num']); ?>件，合计<em>￥<?php echo ($total_price['total_fee']); ?></em></p>
            </div>
        </div>
        <div class="xqpt_wfcon">
            <div class="title">
                <i></i>
                <p>拼团玩法</p>
            </div>
            <div class="con">
                <img src="/Template/mobile/wuyang/Static/new/images/wf_img.jpg">
            </div>
        </div>
        <div class="xqhj_numcon">
            <p>合计:<em>￥<?php echo ($total_price['total_fee']); ?></em></p>
            <a  class="zf_btn">立即支付</a>
        </div>
    </div>
    <div class="zf_tancon">
        <div class="con">
            <div class="title">
                <h3>请选择支付方式</h3>
                <img src="/Template/mobile/wuyang/Static/new/images/close.png">
            </div>
            <div class="con_con">
                <?php if(is_array($paymentList)): foreach($paymentList as $key=>$v): if($v['code'] == 'weixin'): ?><!--<div class="one">-->
                        <!--    <img src="/Template/mobile/wuyang/Static/new/images/zf_wximg.png"/>-->
                        <!--    <p>微信</p>-->
                        <!--    <div class="danxuan">-->
                        <!--        <input type="radio" name="pay_radio" checked class="radio1" value="pay_code=<?php echo ($v['code']); ?>">-->
                        <!--        <i></i>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <?php elseif($v['code'] == 'alipayMobile' and $is_wx == 2): ?>
                        <!--<div class="one">-->
                        <!--    <img src="/Template/mobile/wuyang/Static/new/images/zf_zfbimg.png"/>-->
                        <!--    <p>支付宝</p>-->
                        <!--    <div class="danxuan">-->
                        <!--        <input type="radio" name="pay_radio" class="radio1" value="pay_code=<?php echo ($v['code']); ?>">-->
                        <!--        <i></i>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <?php elseif($v['code'] == 'cod'): ?>
                        <div class="one">
                            <img src="/Template/mobile/wuyang/Static/new/images/zf_yeimg.png"/>
                            <p>余额<em>当前余额：￥<?php echo ($user['user_money']); ?></em></p>
                            <div class="danxuan">
                                <input type="radio" name="pay_radio"  class="radio1" value="pay_code=<?php echo ($v['code']); ?>">
                                <i></i>
                            </div>
                        </div>
                        <?php elseif($v['code'] == 'jifen'): ?>
                        <!--<div class="one">-->
                            <!--<img src="/Template/mobile/wuyang/Static/images/jf_img.png"/>-->
                            <!--<p>积分<em>当前积分：￥<?php echo ($user['pay_points']); ?></em></p>-->
                            <!--<div class="danxuan">-->
                                <!--<input type="radio" name="pay_radio"  class="radio1" value="pay_code=<?php echo ($v['code']); ?>">-->
                                <!--<i></i>-->
                            <!--</div>-->
                        <!--</div>--><?php endif; endforeach; endif; ?>
            </div>
            <div class="zf_btn">
                <input type="hidden" name="cart_id" value="<?php echo ($cartList[0]['id']); ?>">
                <a onclick="order_submit()"><button type="button" >立即支付</button></a>
            </div>
        </div>
    </div>
</form>

<section class="f_mask" style="display: none;"></section>
<script type="text/javascript">
    $(".zf_btn").click(function(){
        $(".zf_tancon").fadeIn();
    });
    //提交订单
    function order_submit(){
        var pay_type = $("input[name='pay_radio']:checked").val();
        if(pay_type=='pay_code=cod'){
            var user_money = parseFloat("<?php echo ($user['user_money']); ?>");
            var total_price = parseFloat("<?php echo ($total_price['total_fee']); ?>");
            if(user_money < total_price){
                layer.msg("余额不足");return false;
            }
            var address_id = "<?php echo ($address["address_id"]); ?>";
            var order_id = "<?php echo ($cartList[0]['id']); ?>";
            $.ajax({
                type: "POST",
                url: "/index.php?m=Mobile&c=Cart&a=cart_cod",
                data: {address_id:address_id,order_id:order_id},
                dataType: 'json',
                success: function (data) {
                    if(data.status!=200){
                        layer.msg(data.msg,{icon:5});
                        return false;
                    }
                    $(".zf_tancon").fadeOut();
                    layer.msg(data.msg,{icon:6},function () {
                        location.href = "/index.php?m=Mobile&c=User&a=pt_order";
                    });
                }
            });
        }else if(pay_type=='pay_code=jifen'){
            var pay_points = parseFloat("<?php echo ($user['pay_points']); ?>");
            var total_price = parseFloat("<?php echo ($total_price['total_fee']); ?>");
            if(pay_points < total_price){
                layer.msg("积分不足");return false;
            }
            var address_id = "<?php echo ($address["address_id"]); ?>";
            var order_id = "<?php echo ($cartList[0]['id']); ?>";
            $.ajax({
                type: "POST",
                url: "/index.php?m=Mobile&c=Cart&a=cart_jifen",
                data: {address_id:address_id,order_id:order_id},
                dataType: 'json',
                success: function (data) {
                    if(data.status!=200){
                        layer.msg(data.msg,{icon:5});
                        return false;
                    }
                    $(".zf_tancon").fadeOut();
                    layer.msg(data.msg,{icon:6},function () {
                        location.href = "/index.php?m=Mobile&c=User&a=pt_order";
                    });
                }
            });
        }else{
            $('#cart2_form').submit();
        }
    }
    // 获取订单价格
    function ajax_order_price()
    {
        $.ajax({
            type : "POST",
            url:'/index.php?m=Mobile&c=Cart&a=cart3&act=order_price&t='+Math.random(),
            data : $('#cart2_form').serialize(),
            dataType: "json",
            success: function(data){
                if(data.status != 1)
                {
                    if(data.status == -100){
                        location.href ="<?php echo U('Mobile/User/login');?>";
                    }else if(data.status == 300){
                        layer.msg(data.msg,{icon:5},function () {
                            location.href ="<?php echo U('Mobile/User/order_list');?>";
                        });
                    }else{
                        layer.msg(data.msg,{icon:5});
                    }
                    return false;
                }
                $("#postFee").text(data.result.postFee); // 物流费
                $("#couponFee").text(data.result.couponFee);// 优惠券
                $("#balance").text(data.result.balance);// 余额
                $("#pointsFee").text(data.result.pointsFee);// 积分支付
                $("#payables").text(data.result.payables);// 应付
                $("#order_prom_amount").text(data.result.order_prom_amount);// 订单 优惠活动
            }
        });
    }

    // 提交订单
    ajax_return_status = 1; // 标识ajax 请求是否已经回来 可以进行下一次请求
    function submit_order()
    {
        if(ajax_return_status == 0){
            return false;
        }
        ajax_return_status = 0;
        $.ajax({
            type : "POST",
            url:"<?php echo U('Mobile/Cart/cart3');?>",//+tab,
            data : $('#cart2_form').serialize()+"&act=submit_order",// 你的formid
            dataType: "json",
            success: function(data){
                if(data.status != '1')
                {
                    layer.msg(data.msg,{icon:5});
                    // 登录超时
                    if(data.status == -100)
                        location.href ="<?php echo U('Mobile/User/login');?>";

                    ajax_return_status = 1; // 上一次ajax 已经返回, 可以进行下一次 ajax请求

                    return false;
                }
                // console.log(data);
                $("#postFee").text(data.result.postFee); // 物流费
                $("#couponFee").text(data.result.couponFee);// 优惠券
                $("#balance").text(data.result.balance);// 余额
                $("#pointsFee").text(data.result.pointsFee);// 积分支付
                $("#payables").text(data.result.payables);// 应付
                $("#order_prom_amount").text(data.result.order_prom_amount);// 订单 优惠活动
                layer.msg('订单提交成功!',{icon:6});
                location.href = "/index.php?m=Mobile&c=Cart&a=cart9&order_id="+data.result;
            }
        });
    }
</script>
<script>
    $(function(){
        $(".zf_tancon .title img").click(function(){
            $(".zf_tancon").fadeOut()
        });
    })
</script>
</body>
</html>