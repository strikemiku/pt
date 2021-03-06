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

    <section class="content ">
        <!-- Main content -->
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 添加价格</h3>
                </div>
                <div class="panel-body ">
                    <!--表单数据-->
                    <form method="post" id="handleposition" action="<?php echo U('Admin/Promotion/priceHandle');?>">
                        <!--通用信息-->
                        <div class="tab-content col-md-10">
                            <div class="tab-pane active" id="tab_tongyong">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td class="col-sm-2">名称：</td>
                                        <td class="col-sm-4">
                                            <input type="text" class="form-control" name="name" value="<?php echo ($info["name"]); ?>" >
                                            <span id="err_attr_name" style="color:#F00; display:none;"></span>
                                        </td>
                                        <td class="col-sm-4">请填写名称
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>价格：</td>
                                        <td>
                                            <input type="text" class="form-control" name="price" value="<?php echo ($info["price"]); ?>" >
                                        </td>
                                        <td class="col-sm-4">请填写价格</td>
                                    </tr>
                                    <!--<tr>-->
                                        <!--<td class="col-sm-2">拼团人数：</td>-->
                                        <!--<td class="col-xs-8">-->
                                            <!--<select id="group_num" name="group_num" class="form-control" style="witdh:150px;">-->
                                                <!--<?php if(is_array($group_num)): foreach($group_num as $key=>$v): ?>-->
                                                    <!--<option value="<?php echo ($v['num']); ?>" <?php if($v[group_num] == $v['num']): ?>selected<?php endif; ?>><?php echo ($v['num']); ?>人</option>-->
                                                <!--<?php endforeach; endif; ?>-->
                                            <!--</select>-->
                                        <!--</td>-->
                                    <!--</tr>-->
                                    <tr>
                                        <td>显示：</td>
                                        <td >
                                            <input type="radio" class="" name="is_show" value="1" <?php if($info[is_show] == 1): ?>checked="checked"<?php endif; ?>>开启
                                            <input type="radio" class="" name="is_show" value="0" <?php if($info[is_show] == 0): ?>checked="checked"<?php endif; ?>>关闭
                                        </td>
                                        <td class="col-sm-4">是否显示</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td><input type="hidden" name="act" value="<?php echo ($act); ?>">
                                            <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
                                        </td>
                                        <td class="col-sm-4"><input class="btn btn-primary" type="button" onclick="adsubmit()" value="保存"></td>
                                        <td class="text-left"></td>
                                    </tr>
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
    // 上传商品图片成功回调函数
    function call_back(fileurl_tmp){
        $("#link_logo").val(fileurl_tmp);
        $("#link_logos").attr('src',fileurl_tmp);
        $("#link_logos").css('display','block');
    }
    function adsubmit(){
        $('#handleposition').submit();
    }
</script>
</body>
</html>