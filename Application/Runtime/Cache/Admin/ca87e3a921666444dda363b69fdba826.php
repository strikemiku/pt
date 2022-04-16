<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php echo ($tpshop_config['shop_info_store_name']); ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.3.0 -->
 	<link href="/Public/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 --
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/Public/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
    	folder instead of downloading all of them to reduce the load. -->
    <link href="/Public/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="/Public/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />   
    <!-- jQuery 2.1.4 -->
    <script src="/Public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
	<script src="/Public/js/global.js"></script>
    <script src="/Public/js/myFormValidate.js"></script>    
    <script src="/Public/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/Public/js/layer/layer-min.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
    <script src="/Public/js/myAjax.js"></script>
    <script type="text/javascript">
    function delfunc(obj){
    	layer.confirm('确认删除？', {
    		  btn: ['确定','取消'] //按钮
    		}, function(){
   				$.ajax({
   					type : 'post',
   					url : $(obj).attr('data-url'),
   					data : {act:'del',del_id:$(obj).attr('data-id')},
   					dataType : 'json',
   					success : function(data){
   						if(data==1){
   							layer.msg('操作成功', {icon: 1});
   							$(obj).parent().parent().remove();
   						}else{
   							layer.msg(data, {icon: 2,time: 2000});
   						}
   						layer.closeAll();
   					}
   				})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);
    }
    
    //全选
    function selectAll(name,obj){
    	$('input[name*='+name+']').prop('checked', $(obj).checked);
    }   
    
    function get_help(obj){
        layer.open({
            type: 2,
            title: '帮助手册',
            shadeClose: true,
            shade: 0.3,
            area: ['90%', '90%'],
            content: $(obj).attr('data-url'), 
        });
    }
    
    function delAll(obj,name){
    	var a = [];
    	$('input[name*='+name+']').each(function(i,o){
    		if($(o).is(':checked')){
    			a.push($(o).val());
    		}
    	})
    	if(a.length == 0){
    		layer.alert('请选择删除项', {icon: 2});
    		return;
    	}
    	layer.confirm('确认删除？', {btn: ['确定','取消'] }, function(){
    			$.ajax({
    				type : 'get',
    				url : $(obj).attr('data-url'),
    				data : {act:'del',del_id:a},
    				dataType : 'json',
    				success : function(data){
    					if(data == 1){
    						layer.msg('操作成功', {icon: 1});
    						$('input[name*='+name+']').each(function(i,o){
    							if($(o).is(':checked')){
    								$(o).parent().parent().remove();
    							}
    						})
    					}else{
    						layer.msg(data, {icon: 2,time: 2000});
    					}
    					layer.closeAll();
    				}
    			})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);	
    }
    </script>        
  </head>
  <body style="background-color:#ecf0f5;">
 


<link href="/Public/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
<script src="/Public/plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
<script src="/Public/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>

<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>
	</ol>
</div>

    <section class="content" style="padding:0px 15px;">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-body ">
                    <ul class="nav nav-tabs">
                        <?php if(is_array($group_list)): foreach($group_list as $k=>$vo): ?><li <?php if($k == 'shop_info'): ?>class="active"<?php endif; ?>><a href="javascript:void(0)" data-url="<?php echo U('System/index',array('inc_type'=>$k));?>" data-toggle="tab" onclick="goset(this)"><?php echo ($vo); ?></a></li><?php endforeach; endif; ?>
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="handlepost" action="<?php echo U('System/handle');?>">
                        <!--通用信息-->
                        <div class="tab-content" style="padding:20px 0px;">
                            <div class="tab-pane active" id="tab_tongyong">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td class="col-sm-2">网站标题：</td>
                                        <td class="col-sm-8">
                                            <input type="text" class="form-control" name="store_name" value="<?php echo ($config["store_name"]); ?>" >
                                            <span id="err_attr_name" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>首页轮播公告：</td>
                                        <td>
                                            <input type="text" class="form-control" name="index_desc" value="<?php echo ($config["index_desc"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2">网站LOGO: </td>
                                        <td >
                                            <img src='/Public/images/no_img.png'  style="width: 100px;height: 100px;float: left;border: 1px solid black;" onclick="GetUploadify(1,'','store_logo','call_back2');">
                                            <input type="hidden" name="store_logo" id="store_logo" value="<?php echo ($config["store_logo"]); ?>">
                                            <img width="100" height="100"  <?php if($config["store_logo"] != ''): ?>style="display: block;float: left"<?php else: ?>style="display: none;float: left"<?php endif; ?>  id="store_logos" src="<?php echo ($config["store_logo"]); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2">公众号二维码:</td>
                                        <td >
                                            <img src='/Public/images/no_img.png'  style="width: 100px;height: 100px;float: left;border: 1px solid black;" onclick="GetUploadify(1,'','gzh_img','call_back4');">
                                            <input type="hidden" name="gzh_img" id="gzh_img" value="<?php echo ($config["gzh_img"]); ?>">
                                            <img width="100" height="100"  <?php if($config["gzh_img"] != ''): ?>style="display: block;float: left"<?php else: ?>style="display: none;float: left"<?php endif; ?>  id="gzh_imgs" src="<?php echo ($config["gzh_img"]); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2">分享好友背景图:<br> (像素:宽365px * 高 635px)</td>
                                        <td >
                                            <img src='/Public/images/no_img.png'  style="width: 100px;height: 100px;float: left;border: 1px solid black;" onclick="GetUploadify(1,'','share_img','call_back3');">
                                            <input type="hidden" name="share_img" id="share_img" value="<?php echo ($config["share_img"]); ?>">
                                            <img width="100" height="100"  <?php if($config["share_img"] != ''): ?>style="display: block;float: left"<?php else: ?>style="display: none;float: left"<?php endif; ?>  id="share_imgs" src="<?php echo ($config["share_img"]); ?>">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="col-sm-2">支付宝二维码: </td>
                                        <td >
                                            <img src='/Public/images/no_img.png'  style="width: 100px;height: 100px;float: left;border: 1px solid black;" onclick="GetUploadify(1,'','zfb_img','call_back');">
                                            <input type="hidden" name="zfb_img" id="zfb_img" value="<?php echo ($config["zfb_img"]); ?>">
                                            <img width="100" height="100"  <?php if($config["zfb_img"] != ''): ?>style="display: block;float: left"<?php else: ?>style="display: none;float: left"<?php endif; ?>  id="zfb_imgs" src="<?php echo ($config["zfb_img"]); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2">微信二维码: </td>
                                        <td >
                                            <img src='/Public/images/no_img.png'  style="width: 100px;height: 100px;float: left;border: 1px solid black;" onclick="GetUploadify(1,'','wx_img','call_back1');">
                                            <input type="hidden" name="wx_img" id="wx_img" value="<?php echo ($config["wx_img"]); ?>">
                                            <img width="100" height="100"  <?php if($config["wx_img"] != ''): ?>style="display: block;float: left"<?php else: ?>style="display: none;float: left"<?php endif; ?>  id="wx_imgs" src="<?php echo ($config["wx_img"]); ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>开户行：</td>
                                        <td>
                                            <input type="text" class="form-control" name="com_bank_kai" value="<?php echo ($config["com_bank_kai"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>银行名称：</td>
                                        <td>
                                            <input type="text" class="form-control" name="com_bank_name" value="<?php echo ($config["com_bank_name"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>银行卡号：</td>
                                        <td>
                                            <input type="text" class="form-control" name="com_bank_card" value="<?php echo ($config["com_bank_card"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>银行卡姓名：</td>
                                        <td>
                                            <input type="text" class="form-control" name="com_bank_card_name" value="<?php echo ($config["com_bank_card_name"]); ?>" >
                                        </td>
                                    </tr>


                                        <!--<td>联系手机：</td>-->
                                        <!--<td>-->
                                        <!--<input type="text" class="form-control" name="mobile" value="<?php echo ($config["mobile"]); ?>" >-->
                                        <!--</td>-->
                                        <!--</tr> -->
                                        <!--<tr>-->
                                        <!--<td>联系地址：</td>-->
                                        <!--<td colspan="2">-->
                                        <!--<div class="col-xs-2">-->
                                        <!--<select onchange="get_city(this)" id="province" name="province" class="form-control" style="margin-left:-15px;">-->
                                        <!--<option  value="0">选择省份</option>-->
                                        <!--<?php if(is_array($province)): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
                                        <!--<option value="<?php echo ($vo["id"]); ?>" <?php if($config[province] == $vo[id]): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option>-->
                                        <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                        <!--</select>-->
                                        <!--</div>   -->
                                        <!--<div class="col-xs-2">                                        -->
                                        <!--<select onchange="get_area(this)" id="city" name="city" class="form-control">-->
                                        <!--<option value="0">选择城市</option>-->
                                        <!--<?php if(is_array($city)): $i = 0; $__LIST__ = $city;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
                                        <!--<option value="<?php echo ($vo["id"]); ?>" <?php if($config[city] == $vo[id]): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option>-->
                                        <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                        <!--</select>-->
                                        <!--</div>   -->
                                        <!--<div class="col-xs-2">                                        -->
                                        <!--<select id="district" name="district" class="form-control">-->
                                        <!--<option value="0">选择区域</option>-->
                                        <!--<?php if(is_array($area)): $i = 0; $__LIST__ = $area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
                                        <!--<option value="<?php echo ($vo["id"]); ?>" <?php if($config[district] == $vo[id]): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option>-->
                                        <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                        <!--</select>-->
                                        <!--</div> -->
                                        <!--<div class="col-xs-4">-->
                                        <!--<input type="text" placeholder="详细地址" class="form-control" name="address" value="<?php echo ($config["address"]); ?>">-->
                                        <!--</div>      -->
                                        <!--</td>-->
                                        <!--</tr>-->
                                    <tr>
                                        <td>咨询热线：</td>
                                        <td>
                                            <input type="text" class="form-control" name="kefu_mobile" value="<?php echo ($config["kefu_mobile"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>微信客服：</td>
                                        <td>
                                            <input type="text" class="form-control" name="kefu_weixin" value="<?php echo ($config["kefu_weixin"]); ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>QQ客服：</td>
                                        <td>
                                            <input type="text" class="form-control" name="kefu_qq" value="<?php echo ($config["kefu_qq"]); ?>" >
                                        </td>
                                    </tr>
                                    <!--<tr>-->
                                        <!--<td class="col-sm-2">网站验证开关：</td>-->
                                        <!--<td class="col-sm-6">-->
                                            <!--开:<input type="radio"  name="web_check" value="1" <?php if($config['web_check'] == 1): ?>checked="checked"<?php endif; ?> />-->
                                            <!--关:<input type="radio"  name="web_check" value="0" <?php if($config['web_check'] == 0): ?>checked="checked"<?php endif; ?> />-->
                                        <!--</td>-->
                                        <!--<td class="col-sm-7"></td>-->
                                    <!--</tr>-->

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td><input type="hidden" name="inc_type" value="<?php echo ($inc_type); ?>"></td>
                                        <td class="text-left"><input class="btn btn-primary" type="button" onclick="adsubmit()" value="保存"></td></tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </form><!--表单数据-->
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    function call_back(fileurl_tmp){
        $("#zfb_img").val(fileurl_tmp);
        $("#zfb_imgs").attr('src',fileurl_tmp);
        $("#zfb_imgs").css('display','block');
    }
    function call_back1(fileurl_tmp){
        $("#wx_img").val(fileurl_tmp);
        $("#wx_imgs").attr('src',fileurl_tmp);
        $("#wx_imgs").css('display','block');
    }
    function call_back2(fileurl_tmp){
        $("#store_logo").val(fileurl_tmp);
        $("#store_logos").attr('src',fileurl_tmp);
        $("#store_logos").css('display','block');
    }
    function call_back3(fileurl_tmp){
        $("#share_img").val(fileurl_tmp);
        $("#share_imgs").attr('src',fileurl_tmp);
        $("#share_imgs").css('display','block');
    }
    function call_back4(fileurl_tmp){
        $("#gzh_img").val(fileurl_tmp);
        $("#gzh_imgs").attr('src',fileurl_tmp);
        $("#gzh_imgs").css('display','block');
    }
    function adsubmit(){
        /*
         var site_url = $('input[name="site_url"]').val();
         var urlReg = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\w \.-]*)*$/;
         if(!urlReg.exec(site_url))
         {
         alert('网站域名格式必须是 http://www.xxx.com');
         return false;
         }
         */
        $('#handlepost').submit();
    }

    $(document).ready(function(){
        //get_province();
    });

    function goset(obj){
        window.location.href = $(obj).attr('data-url');
    }
</script>
</body>
</html>