<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html >
<html>
<head>
<meta name="Generator" content="TPSHOP v1.1" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="applicable-device" content="mobile">
<title><?php echo ($tpshop_config['shop_info_store_name']); ?></title>
<meta http-equiv="keywords" content="<?php echo ($tpshop_config['shop_info_store_keyword']); ?>" />
<meta name="description" content="<?php echo ($tpshop_config['shop_info_store_desc']); ?>" />
<link rel="stylesheet" href="/Template/mobile/wuyang/Static/old/css/public.css">
<link rel="stylesheet" href="/Template/mobile/wuyang/Static/old/css/user.css">
<script type="text/javascript" src="/Template/mobile/wuyang/Static/old/js/jquery.js"></script>
<script src="/Public/js/global.js"></script>
<script src="/Public/js/mobile_common.js"></script>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/old/js/modernizr.js"></script>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/old/js/layer.js" ></script>

</head>

<link rel="stylesheet" type="text/css" media="all" href="/Template/mobile/wuyang/Static/css/countdown.css" />
<link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/iconfont.css"/>
<script src="/Template/mobile/wuyang/Static/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/js/countdown.js" ></script>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/new/layer/layer.js"></script>
<!--<script src="/Template/mobile/wuyang/Static/js/layer/layer.js"></script>-->
<style>

    .footer{position:fixed;height:50px;width:100%;bottom:0;left:0;box-shadow:0 0 4px 1px rgba(0,0,0,0.1);z-index: 99;}

    .nav_list{ background:#fff; padding:0px;}

    .nav_list ul li{ float:left; width:25%; text-align:center;}

    .nav_list ul li i{ color:#5a5c60; font-size:24px;}

    .nav_list ul li .img{width: 55px;height: 55px;background:#fff;overflow:hidden;box-shadow:0 0 10px rgba(0,0,0,0.1);border-radius:50%;padding:8px;box-sizing:border-box;border: 1px solid #eee;}
    .nav_list ul li .img img{ width:100%; height:100%;}
    .nav_list ul li .icon{height: 24px;}
    .nav_list ul li.on i{color: #e02e24;}

    .nav_list ul li .nav_title{ font-size:14px; color:#666;}

    .nav_list ul li.on .nav_title{color: #e02e24;}

    .layui-layer-btn .layui-layer-btn0 {
        border-color: red;
        background-color: red;
        color: #fff;
    }
</style>
<body>
<header>
    <div class="tab_nav">
        <div class="header">
            <div class="h-left"><a class="sb-back" href="<?php echo U('User/index');?>" title="返回"></a></div>
            <div class="h-mid">我的订单</div>
        </div>
    </div>
</header>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/old/js/mobile.js" ></script>
<div class="goods_nav hid" id="menu">
      <div class="Triangle">
        <h2></h2>
      </div>
      <ul>
        <li><a href="<?php echo U('Index/index');?>"><span class="menu1"></span><i>首页</i></a></li>
        <li><a href="<?php echo U('Goods/categoryList');?>"><span class="menu2"></span><i>分类</i></a></li>
        <li><a href="<?php echo U('Cart/cart');?>"><span class="menu3"></span><i>购物车</i></a></li>
        <li style=" border:0;"><a href="<?php echo U('User/index');?>"><span class="menu4"></span><i>我的</i></a></li>
   </ul>
 </div> 

<div id="tbh5v0">
    <!--------筛选 form 表单 开始-------------->
    <form action="<?php echo U('Mobile/order_list/ajax_order_list');?>" name="filter_form" id="filter_form">
        <div class="Evaluation2">
            <ul>
                <li><a href="<?php echo U('/Mobile/User/order_list');?>" class="tab_head <?php if($_GET[type] == ''): ?>on<?php endif; ?>"  >全部</a></li>
                <li><a href="<?php echo U('/Mobile/User/order_list',array('type'=>'WAITPAY'));?>"      class="tab_head <?php if($_GET[type] == 'WAITPAY'): ?>on<?php endif; ?>">待付款</a></li>
                <li><a href="<?php echo U('/Mobile/User/order_list',array('type'=>'WAITSEND'));?>"     class="tab_head <?php if($_GET[type] == 'WAITSEND'): ?>on<?php endif; ?>">待发货</a></li>
                <li><a href="<?php echo U('/Mobile/User/order_list',array('type'=>'WAITRECEIVE'));?>"  class="tab_head <?php if($_GET[type] == 'WAITRECEIVE'): ?>on<?php endif; ?>">待收货</a></li>
                <li><a href="<?php echo U('/Mobile/User/order_list',array('type'=>'WAITCCOMMENT'));?>" class="tab_head <?php if($_GET[type] == 'WAITCCOMMENT'): ?>on<?php endif; ?>">已完成</a></li>
            </ul>
        </div>

        <div class="order ajax_return">
            <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><div class="order_list">
                    <h2>
                        <a href="<?php echo U('/Mobile/User/order_detail',array('id'=>$list['order_id']));?>">
                            <img src="/Template/mobile/wuyang/Static/new/images/dianpu.png"><span>订单号:<?php echo ($list["order_sn"]); ?></span>
                        </a>
                    </h2>
                    <a href="<?php echo U('/Mobile/User/order_detail',array('id'=>$list['order_id']));?>">
                        <?php if(is_array($list["goods_list"])): $i = 0; $__LIST__ = $list["goods_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$good): $mod = ($i % 2 );++$i;?><dl style="position: relative">
                                <dt><img src="<?php echo ($good["original_img"]); ?>"></dt>
                                <dd class="name"><strong><?php echo ($good["goods_name"]); ?></strong>
                                <dd class="pice">￥<?php echo ($good['goods_price']); ?>元<em>x<?php echo ($good['goods_num']); ?></em></dd>
                            </dl><?php endforeach; endif; else: echo "" ;endif; ?>
                    </a>
                    <div class="pic">
                        <span style="float: right">总额：<strong>￥<?php echo ($list['order_amount']); ?>元</strong></span>
                    </div>
                    <div class="anniu" style="width:95%">
                        <?php if($list["receive_btn"] == 1): ?><a href="<?php echo U('Mobile/User/order_confirm',array('id'=>$list['order_id']));?>" style="float: right;background-color: red;">收货确认</a><?php endif; ?>
                        <?php if($list["shipping_status"] == 0 and $list["order_status"] < 2 and $list["type"] == 3): ?><a onclick="exchange(<?php echo ($list['order_id']); ?>)" style="float: right;background-color: red;">兑换积分</a><?php endif; ?>
                        <?php if($list["shipping_btn"] == 1): ?><a href="https://www.kuaidi100.com/chaxun?com=<?php echo ($list["shipping_code"]); ?>&nu=<?php echo ($list["invoice_no"]); ?>" style="float: right;background-color: red;">查看物流</a><?php endif; ?>
                    </div>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <!--查询条件-->
        <input type="hidden" name="type" value="<?php echo $_GET['type'];?>" />
    </form>
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

<script language="javascript">
    var  page = 1;

    /*** ajax 提交表单 查询订单列表结果*/
    function ajax_sourch_submit()
    {
        page += 1;
        $.ajax({
            type : "GET",
            url:"/index.php?m=Mobile&c=User&a=order_list&type=<?php echo ($_GET['type']); ?>&is_ajax=1&p="+page,//+tab,
//			url:"<?php echo U('Mobile/User/order_list',array('type'=>$_GET['type']),'');?>/is_ajax/1/p/"+page,//+tab,			
            //data : $('#filter_form').serialize(),
            success: function(data)
            {
                if(data == '')
                    $('#getmore').hide();
                else
                {
                    $(".ajax_return").append(data);
                    $(".m_loading").hide();
                }
            }
        });
    }
    //取消订单
    function exchange(order_id){
        layer.open({
            title:'温馨提示',
            content: '您确定要兑换成积分吗？',
            btn: ['确认', '稍后'],
            shadeClose: false,
            yes: function () {
                $.ajax({
                    type : 'post',
                    url :"<?php echo U('Mobile/User/change_jifen');?>",
                    data : {order_id:order_id},
                    dataType : 'json',
                    success : function(res){
                        if(res.status == 200){
                            layer.msg(res.msg,function () {
                                window.location.reload();
                            });
                        }else{
                            layer.msg(res.msg);
                            return false;
                        }
                    },
                    error : function(XMLHttpRequest, textStatus, errorThrown) {
                        layer.msg('网络失败，请刷新页面后重试');
                    }
                })
            },
            no: function () {
                layer.closeAll();
            }
        });
    }
    //取消订单
    function cancel_order(id){
        layer.open({
            title:'温馨提示',
            content: '您确定要取消订单吗？',
            btn: ['确认', '稍后'],
            shadeClose: false,
            yes: function () {
                location.href = "/index.php?m=Mobile&c=User&a=cancel_order&id="+id;
            },
            no: function () {
                layer.closeAll();
            }
        });
    }

</script>
</body>
</html>