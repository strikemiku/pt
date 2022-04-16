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
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> 用户信息</h3>
            </div>
            <div class="panel-body">
                <form action="" method="post" onsubmit="return checkUserUpdate(this);">
                    <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td class="col-sm-2">推广码:</td>
                        <td class="col-sm-2"><input type="text" class="form-control" name="user_code" value="<?php echo ($user["user_code"]); ?>" readonly="readonly"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-sm-2">姓名:</td>
                        <td ><input type="text" class="form-control" name="nickname" value="<?php echo ($user["nickname"]); ?>"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-sm-2">会员等级:</td>
                        <td>
                            <select name="level" id="level"  class="form-control" style="width:250px;" >
                                <?php if(is_array($user_level)): foreach($user_level as $k=>$v): ?><option value="<?php echo ($v['level']); ?>" <?php if($v['is_check'] == 1): ?>selected="selected"<?php endif; ?> >
                                    <?php echo ($v['level_name']); ?>
                                    </option><?php endforeach; endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>手机号:</td>
                        <td>
                            <input type="text" class="form-control" name="mobile" value="<?php echo ($user["mobile"]); ?>">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>余额:</td>
                        <td>
                            <input class="form-control" type="text" name="user_money" value="<?php echo ($user["user_money"]); ?>" readonly="readonly">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>积分:</td>
                        <td>
                            <input class="form-control" type="text" name="pay_points" value="<?php echo ($user["pay_points"]); ?>" readonly="readonly">
                        </td>
                        <td></td>
                    </tr>
                    <!--<tr>-->
                        <!--<td>推荐值:</td>-->
                        <!--<td>-->
                            <!--<input class="form-control" type="text" name="recom_num" value="<?php echo ($user["recom_num"]); ?>" readonly="readonly">-->
                        <!--</td>-->
                        <!--<td></td>-->
                    <!--</tr>-->
                    <!--<tr>-->
                        <!--<td>贡献值:</td>-->
                        <!--<td>-->
                            <!--<input class="form-control" type="text" name="devote_num" value="<?php echo ($user["devote_num"]); ?>" readonly="readonly">-->
                        <!--</td>-->
                        <!--<td></td>-->
                    <!--</tr>-->
                    <tr>
                        <td>活跃度:</td>
                        <td>
                            <input class="form-control" type="text" name="active_num" value="<?php echo ($user["active_num"]); ?>" readonly="readonly">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>拼团总数:</td>
                        <td>
                            <input class="form-control" type="text" name="total_pt_num" value="<?php echo ($user["total_pt_num"]); ?>" readonly="readonly">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>每日拼团数量:</td>
                        <td>
                            <input class="form-control" type="text" name="pt_num" value="<?php echo ($user["pt_num"]); ?>" readonly="readonly">
                        </td>
                        <td></td>
                    </tr>
                    <!--<tr>-->
                        <!--<td>每日拼团失败次数:</td>-->
                        <!--<td>-->
                            <!--<input class="form-control" type="text" name="false_num" value="<?php echo ($user["false_num"]); ?>">-->
                        <!--</td>-->
                        <!--<td></td>-->
                    <!--</tr>-->
                    <tr>
                        <td>登录密码:</td>
                        <td><input type="password" class="form-control" name="password"></td>
                        <td>留空表示不修改密码</td>
                    </tr>
                    <tr>
                        <td>交易密码:</td>
                        <td><input type="password" class="form-control" name="twopassword"></td>
                        <td>留空表示不修改密码</td>
                    </tr>
                    <tr>
                        <td>注册时间:</td>
                        <td>
                            <?php echo (date('Y-m-d H:i:s',$user["reg_time"])); ?>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button type="submit" class="btn btn-info">
                                <i class="ace-icon fa fa-check bigger-110"></i> 保存
                            </button>
                            <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default pull-right" data-original-title="返回"><i class="fa fa-reply"></i></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </form>

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
    // 上传商品图片成功回调函数
    function call_back(fileurl_tmp){
        $("#head_pic").val(fileurl_tmp);
        $("#head_pics").attr('src',fileurl_tmp);
        $("#head_pics").css('display','block');
    }
    function checkUserUpdate(){

        var mobile = $('input[name="mobile"]').val();
        var password = $('input[name="password"]').val();
        var password2 = $('input[name="password2"]').val();
        var twopassword = $('input[name="twopassword"]').val();
        var twopassword2 = $('input[name="twopassword2"]').val();
        var error ='';
//        if(password != password2){
//            error += "两次输入的密码不一样\n";
//        }
//        if(twopassword != twopassword2){
//            error += "两次输入的二级密码不一样\n";
//        }
//        if(!checkEmail(email)){
//            error += "邮箱地址有误\n";
//        }
//        if(!checkMobile(mobile)){
//            error += "手机号码填写有误\n";
//        }
        if(error){
            layer.alert(error, {icon: 2});  //alert(error);
            return false;
        }
        return true;

    }
</script>

</body>
</html>