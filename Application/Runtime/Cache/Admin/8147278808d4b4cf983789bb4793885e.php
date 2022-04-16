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
    <div class="container-fluid">
        <!--新订单列表 操作信息-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i>充值</h3>
            </div>
            <div class="panel-body">

            <form id="delivery-form" method="post" action="">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <div class="row">
                            <td class="text-right col-sm-2">余额：</td>
                            <td colspan="3">
                                <select name="money_act_type" id="money_act_type" class="input-sm">
                                    <option value="1">增加</option>
                                    <option value="0">减少</option>
                                </select>
                                <input type="text" name="user_money" class="input-sm" value="<?php echo ((isset($_REQUEST['user_money']) && ($_REQUEST['user_money'] !== ""))?($_REQUEST['user_money']):'0'); ?>">
                            </td>
                        </div>
                    </tr>
                    <tr>
                        <div class="row">
                            <td class="text-right col-sm-2">积分：</td>
                            <td colspan="3">
                                <select name="point_act_type" id="point_act_type" class="input-sm">
                                    <option value="1">增加</option>
                                    <option value="0">减少</option>
                                </select>
                                <input type="text" name="pay_points" value="0" class="input-sm">
                            </td>
                        </div>
                    </tr>
                    <!--<tr>-->
                        <!--<div class="row">-->
                            <!--<td class="text-right col-sm-2">冻结金额：</td>-->
                            <!--<td colspan="3">-->
                                <!--<select name="frozen_act_type" id="frozen_act_type" class="input-sm">-->
                                    <!--<option value="1">增加</option>-->
                                    <!--<option value="0">减少</option>-->
                                <!--</select>-->
                                <!--<input type="text" name="frozen_money" value="0" class="input-sm">-->
                            <!--</td>-->
                        <!--</div>-->
                    <!--</tr>-->

                    <!--<tr>-->
                        <!--<div class="row">-->
                            <!--<td class="text-right col-sm-2">操作备注：</td>-->
                            <!--<td colspan="3">-->
                                    <!--<textarea name="desc" placeholder="请输入操作备注" rows="3" class="form-control"><?php echo ($_REQUEST['desc']); ?></textarea>-->
                            <!--</td>-->
                        <!--</div>-->
                    <!--</tr>-->

                    <tr>
                        <div class="row">
                            <td colspan="4">
                                <div class="form-group text-center">
                                    <button class="btn btn-primary" type="submit" id="disabled">提交</button>
                                    <button onclick="history.go(-1)"  class="btn btn-primary" type="button">返回</button>
                                </div>
                            </td>
                        </div>
                    </tr>

                    </tbody>
                </table>
            </form>

            </div>
        </div>

    </div>    <!-- /.content -->
        </section>
</div>