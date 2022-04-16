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
 

<div class="wrapper">
    <!-- Content Header (Page header) -->
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>
	</ol>
</div>

    <section class="content">
        <!-- Main content -->
        <!--<div class="container-fluid">-->
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="pull-right">
                        <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default"
                           data-original-title="返回"><i class="fa fa-reply"></i></a>
                    </div>
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-list"></i> 添加会员</h3>
                    </div>
                    <div class="panel-body">
                        <!--<form action="" method="post" >-->
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="col-sm-2">推广码:</td>
                                    <td class="col-sm-2"><input type="text" class="form-control" name="pcode" value=""></td>
                                    <td style="color: red;line-height: 35px;">必填</td>
                                </tr>
                                <tr>
                                    <td class="col-sm-2">昵&nbsp;&nbsp;&nbsp;称:</td>
                                    <td><input type="text" class="form-control" name="nickname" value=""></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>手机号:</td>
                                    <td>
                                        <input type="text" class="form-control" name="mobile">
                                    </td>
                                    <td style="color: red;line-height: 35px;">必填</td>
                                </tr>
                                <tr>
                                    <td>登陆密码:</td>
                                    <td><input type="password" class="form-control" name="password"></td>
                                    <td style="color: red;line-height: 35px;">必填</td>
                                </tr>
                                <tr>
                                    <td>交易密码:</td>
                                    <td><input type="password" class="form-control" name="twopassword"></td>
                                    <td style="color: red;line-height: 35px;">必填</td>
                                </tr>
                                <!--<tr>-->
                                    <!--<td>身份证号:</td>-->
                                    <!--<td><input type="text" class="form-control" name="id_card"></td>-->
                                    <!--<td></td>-->
                                <!--</tr>-->
                                <!--<tr>-->
                                    <!--<td>省/市/区地址：</td>-->
                                    <!--<td colspan="2">-->
                                        <!--<div class="col-xs-2">-->
                                            <!--<select onchange="get_city(this)" id="province" name="province" class="form-control" style="margin-left:-15px;">-->
                                                <!--<option value="0">选择省份</option>-->
                                                <!--<?php if(is_array($province)): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
                                                    <!--<option value="<?php echo ($vo["id"]); ?>"<?php if($config[province] == $vo[id]): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option>-->
                                                <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                            <!--</select>-->
                                        <!--</div>-->
                                        <!--<div class="col-xs-2">-->
                                            <!--<select onchange="get_area(this)" id="city" name="city" class="form-control">-->
                                                <!--<option value="0">选择城市</option>-->

                                            <!--</select>-->
                                        <!--</div>-->
                                        <!--<div class="col-xs-2">-->
                                            <!--<select id="district" name="district" class="form-control">-->
                                                <!--<option value="0">选择区域</option>-->
                                            <!--</select>-->
                                        <!--</div>-->
                                    <!--</td>-->
                                <!--</tr>-->
                                <!--<tr>-->
                                    <!--<td>详细地址:</td>-->
                                    <!--<td><input type="text" class="form-control" name="address" value=""></td>-->
                                <!--</tr>-->
                                <tr>
                                    <td></td>
                                    <td>
                                        <button type="submit" class="btn btn-info" onClick="checkSubmit()">
                                            <i class="ace-icon fa fa-check bigger-110"></i> 保存
                                        </button>
                                        <input type="reset" class="btn btn-default pull-right" value="重置">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        <!--</form>-->

                    </div>
                </div>
            </div>
        </div>    <!-- /.content -->
    </section>
</div>
<script>
    /**
     * 获取城市
     * @param t  省份select对象
     */
    function get_city(t){
        var parent_id = $(t).val();
        if(!parent_id > 0){
            return;
        }
        $('#twon').empty().css('display','none');
        var url = '/index.php?m=Home&c=Api&a=getRegion&level=2&parent_id='+ parent_id;
        $.ajax({
            type : "GET",
            url  : url,
            error: function(request) {
                alert("服务器繁忙, 请联系管理员!");
                return;
            },
            success: function(v) {
                v = '<option value="0">选择城市</option>'+ v;
                $('#city').empty().html(v);
            }
        });
    }
    /**
     * 获取地区
     * @param t  城市select对象
     */
    function get_area(t){
        var parent_id = $(t).val();
        if(!parent_id > 0){
            return;
        }
        var url = '/index.php?m=Home&c=Api&a=getRegion&level=3&parent_id='+ parent_id;
        $.ajax({
            type : "GET",
            url  : url,
            error: function(request) {
                alert("服务器繁忙, 请联系管理员!");
                return;
            },
            success: function(v) {
                v = '<option value="0">选择区域</option>'+ v;
                $('#district').empty().html(v);
            }
        });
    }
    // 获取最后一级乡镇
    function get_twon(obj){
        var parent_id = $(obj).val();
        var url = '/index.php?m=Home&c=Api&a=getTwon&parent_id='+ parent_id;
        $.ajax({
            type : "GET",
            url  : url,
            success: function(res) {
                if(parseInt(res) == 0){
                    $('#twon').empty().css('display','none');
                }else{
                    $('#twon').css('display','block');
                    $('#twon').empty().html(res);
                }
            }
        });
    }
    function check_code(user_code) {

        var reg =/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,18}$/;

        if(reg.test(user_code)) {
            return true;
        }else{
            return false;
        };

    }
    function checkMobile(tel) {
        var reg = /(^1[3|4|5|7|8|9][0-9]{9}$)/;
        if (reg.test(tel)) {
            return true;
        }else{
            return false;
        };
    }
    function checkSubmit() {
        var pcode = $('input[name="pcode"]').val();
        var nickname = $('input[name="nickname"]').val();
        var mobile = $('input[name="mobile"]').val();
        var password = $('input[name="password"]').val();
        var twopassword = $('input[name="twopassword"]').val();
        if (pcode == '') {
            layer.msg("请输入推荐码", {icon: 5});
            return false;
        }
        if (nickname == '') {
            layer.msg("请输入昵称", {icon: 5});
            return false;
        }
        if (mobile == '') {
            layer.msg("请输入手机号", {icon: 5});
            return false;
        }
        if (!checkMobile(mobile)) {
            layer.msg("手机号格式不正确", {icon: 5});
            return false;
        }
        if (password == '') {
            layer.msg("登录密码必填", {icon: 5});
            return false;
        }
        if (twopassword == '') {
            layer.msg("交易密码必填", {icon: 5});
            return false;
        }
//        var province = $.trim($('#province option:selected').val());
//        var city = $.trim($('#city option:selected').val());
//        var district = $.trim($('#district option:selected').val());
//        var address = $('input[name="address"]').val();
//        if(province == '0') {
//            layer.msg('请选择省份',{icon:5,time:1500});
//            return false;
//        }
//        if(city == '0') {
//            layer.msg('请选择城市',{icon:5,time:1500});
//            return false;
//        }
//        if(district == '0') {
//            layer.msg('请选择区域',{icon:5,time:1500});
//            return false;
//        }
//        if(address == '') {
//            layer.msg('请输入详细地址',{icon:5,time:1500});
//            return false;
//        }
        $.ajax({
            type : 'post',
            url : '/index.php?m=Admin&c=User&a=add_user',
            data : {pcode:pcode,nickname:nickname,mobile:mobile,password:password,twopassword:twopassword},
            dataType : 'json',
            success : function(res){
                if(res.status == 200){
                    layer.msg(res.msg,{icon:6,time:1500},function () {
                        location.href="/Admin/User/index";
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