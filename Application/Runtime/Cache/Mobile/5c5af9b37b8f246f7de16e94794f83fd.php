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
    <title>余额提现</title>
    <style>
        #type{
            height: 40px;
            color: #666;
            font-size: 14px;
            line-height: 40px;
            border-radius: 3px;
            padding: 0 10px;
            border: 1px solid #e6e6e6;
            width: 100%;
        }
        #tx_type{
            height: 40px;
            color: #666;
            font-size: 14px;
            line-height: 40px;
            border-radius: 3px;
            padding: 0 10px;
            border: 1px solid #e6e6e6;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="main-container">
    <div class="header_list">
        <a href="<?php echo U('User/index');?>" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
        <h3>余额提现</h3>
        <a href="<?php echo U('withdrawalsdetail');?>" style="color: gray" class="mingxi">提现记录</a>
    </div>

    <div style="height:45px;"></div>

    <div class="chongzhi_con">
        <div class="chongzhi_jiner clearfixd">
            <div class="jine_l fr_l">可用余额：</div>
            <div class="jine_r fr_r"><input type="text" value="<?php echo ($user['user_money']); ?>元" readonly/></div>
        </div>
        <div class="chongzhi_jiner clearfixd">
            <div class="jine_l fr_l">提现金额：</div>
            <div class="jine_r fr_r"><input type="text" id="tixian_money" name="tixian_money" placeholder="请输入提现金额"/></div>
        </div>
        <div class="chongzhi_jiner clearfixd">
            <div class="jine_l fr_l">收款方式：</div>
            <div class="jine_r fr_r">
                <select name="type" id="type">
                    <option value="0">请选择收款方式</option>
                    <option value="1">支付宝</option>
                    <option value="2">微信</option>
                    <option value="3">银行卡</option>
                </select>
            </div>
        </div>
        <div class="chongzhi_jiner clearfixd" id="zhifubao" style="display: none;">
            <div class="jine_l fr_l">支付宝号：</div>
            <div class="jine_r fr_r">
                <img src="<?php echo ($user['zhifubao']); ?>" alt="" style="width: 250px;height: 250px;">
                <input type="hidden" id="zfb" name="zfb" value="<?php echo ($user["zhifubao"]); ?>" />
            </div>
        </div>
        <div class="chongzhi_jiner clearfixd" id="wx" style="display: none;">
            <div class="jine_l fr_l">微信号：</div>
            <div class="jine_r fr_r">
                <img src="<?php echo ($user['weixin']); ?>" alt="" style="width: 250px;height: 250px;">
                <input type="hidden" id="weixin" name="weixin" value="<?php echo ($user["weixin"]); ?>" />
            </div>
        </div>

        <div style="display: none" id="bank">
            <div class="chongzhi_jiner clearfixd" >
                <div class="chongzhi_jiner clearfixd" >
                    <div class="jine_l fr_l">持卡人：</div>
                    <div class="jine_r fr_r"><input type="text" id="bank_name" name="bank_name" value="<?php echo ($user["bank_card_name"]); ?>" placeholder="请输入持卡人姓名"/></div>
                </div>
                <div class="jine_l fr_l">银行名称：</div>
                <div class="jine_r fr_r"><input type="text" id="bank_title" name="bank_title"  value="<?php echo ($user["bank_name"]); ?>" placeholder="请输入银行名称"/></div>
            </div>
            <div class="chongzhi_jiner clearfixd" >
                <div class="jine_l fr_l">银行卡号：</div>
                <div class="jine_r fr_r"><input type="text" id="bank_card" name="bank_card" value="<?php echo ($user["bank_card"]); ?>" placeholder="请输入银行卡号"/></div>
            </div>
            <div class="chongzhi_jiner clearfixd" >
                <div class="jine_l fr_l">开户行：</div>
                <div class="jine_r fr_r"><input type="text" id="bank_kai" name="bank_kai" value="<?php echo ($user["bank_kai"]); ?>" placeholder="请输入开户行"/></div>
            </div>
        </div>
        <div class="chongzhi_jiner clearfixd">
            <div class="jine_l fr_l">交易密码：</div>
            <div class="jine_r fr_r"><input type="password" id="twopwd" name="twopwd" placeholder="请输入交易密码"/></div>
        </div>
        <div class="chongzhi_jiner clearfixd" style=" font-size:14px; color:#999">
            提示:提现<span style="color:red;"><?php echo ($config['basic_money_min']); ?></span>起提，
            <?php if($config['basic_money_fee_bl'] > 0): ?>提现手续费<span style="color:red;"><?php echo ($config['basic_money_fee_bl']); ?>%</span><?php endif; ?> .

        </div>
        <div class="chongzhi_btn"><input type="submit"  onClick="checkSubmit()" value="立即申请"/></div>
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

    $("#type").change(function () {
        var type = $(this).val();
        if(type==1){
            var zfb = "<?php echo ($user['zhifubao']); ?>";
            if(zfb==''){
                layer.msg("请上传支付宝收款码",{icon:5,time:1500},function () {
                    window.location.href="<?php echo U('User/set');?>";
                });
                return false;
            }
            $("#zhifubao").css('display','block');
            $("#wx").css('display','none');
            $("#bank").css('display','none');
        }else if(type==2){
            var zfb = "<?php echo ($user['weixin']); ?>";
            if(zfb==''){
                layer.msg("请上传微信收款码",{icon:5,time:1500},function () {
                    window.location.href="<?php echo U('User/set');?>";
                });
                return false;
            }
            $("#zhifubao").css('display','none');
            $("#wx").css('display','block');
            $("#bank").css('display','none');
        }else{
            var zfb = "<?php echo ($user['bank_card']); ?>";
            if(zfb==''){
                layer.msg("请完善银行卡信息",{icon:5,time:1500},function () {
                    window.location.href="<?php echo U('User/set');?>";
                });
                return false;
            }
            $("#zhifubao").css('display','none');
            $("#wx").css('display','none');
            $("#bank").css('display','block');
        }
    });
    // author :凌寒 2018年5月17日13:53:02 执行提现方法
    function checkSubmit()
    {
        var tixian_money = $.trim($('#tixian_money').val());
        var type = $.trim($('#type').val());
        var zfb = $.trim($('#zfb').val());
        var weixin = $.trim($('#weixin').val());
        var bank_title = $.trim($('#bank_title').val());
        var bank_card = $.trim($('#bank_card').val());
        var bank_name = $.trim($('#bank_name').val());
        var bank_kai = $.trim($('#bank_kai').val());
        var twopwd = $.trim($('#twopwd').val());

        if(tixian_money == ''){
            layer.msg("请输入提现金额",{icon:5,time:1500});
            return false;
        }
        if(tixian_money <= 0){
            layer.msg("提现金额错误",{icon:5,time:1500});
            return false;
        }
        if(type==0){
            layer.msg("请选择收款方式",{icon:5,time:1500});
            return false;
        }else{
           if(type==1){
               if(zfb==''){
                   layer.msg("请完善支付宝收款码",{icon:5,time:1500});
                   return false;
               }
           }else if(type==2){
               if(weixin==''){
                   layer.msg("请完善微信收款码",{icon:5,time:1500});
                   return false;
               }
           }else{
               if(bank_name==''){
                   layer.msg("请填写持卡人姓名",{icon:5,time:1500});
                   return false;
               }
               if(bank_title==''){
                   layer.msg("请填写银行名称",{icon:5,time:1500});
                   return false;
               }
               if(bank_card==''){
                   layer.msg("请填写银行卡号",{icon:5,time:1500});
                   return false;
               }
               if(bank_kai==''){
                   layer.msg("请填写开户行",{icon:5,time:1500});
                   return false;
               }
           }
        }
        if(twopwd == ''){
            layer.msg('请输入交易密码!',{icon:5,time:1500});
            return false;
        }
        $.ajax({
            type : 'post',
            url : '/index.php?m=Mobile&c=User&a=withdrawals&t='+Math.random(),
            data : {money:tixian_money,type:type,zfb:zfb,weixin:weixin,bank_title:bank_title,bank_card:bank_card,bank_name:bank_name,bank_kai:bank_kai,twopwd:twopwd},
            dataType : 'json',
            success : function(res){
                if(res.status == 200){
                    layer.msg(res.msg,{icon:6,time:1500},function () {
                        location.href="/Mobile/User/withdrawalsdetail";
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
    }
</script>
</body>
</html>