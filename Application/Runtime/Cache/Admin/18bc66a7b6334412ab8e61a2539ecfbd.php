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
    <!-- Content Header (Page header) -->
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>
	</ol>
</div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> 订单列表</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                            <form action="<?php echo U('Admin/order/export_order');?>" id="search-form2" class="navbar-form form-inline" method="post">
                                <!--<div class="form-group">-->
                                    <!--<label class="control-label" for="input-order-id">收货人</label>-->
                                    <!--<div class="input-group">-->
                                        <!--<input type="text" name="consignee" placeholder="收货人" id="input-member-id" class="input-sm" style="width:100px;">-->
                                    <!--</div>-->
                                <!--</div>-->
                                <div class="form-group">
                                    <label class="control-label" for="input-order-id">订单编号</label>
                                    <div class="input-group">
                                        <input type="text" name="order_sn" placeholder="订单编号" id="input-order-id" class="input-sm" style="width:100px;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="input-order-id">支付状态</label>
                                    <select name="pay_status" class="input-sm" style="width:100px;">
                                        <option value="">支付状态</option>
                                        <option value="0">未支付</option>
                                        <option value="1">已支付</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="input-order-id">发货状态</label>
                                    <select name="shipping_status" class="input-sm" style="width:100px;">
                                        <option value="">发货状态</option>
                                        <option value="0">未发货</option>
                                        <option value="1">已发货</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="input-date-added">下单日期</label>
                                    <div class="input-group">
                                        <input type="text" name="timegap" value="<?php echo ($timegap); ?>" placeholder="下单日期"  id="add_time" class="input-sm">
					                 </div>
                                </div>

                                <div class="form-group">
                                    <!--<select name="order_status" class="input-sm" style="width:100px;">-->
                                        <!--<option value="">订单状态</option>-->
                                        <!--<?php if(is_array($order_status)): $k = 0; $__LIST__ = $order_status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k;?>-->
                                            <!--<option value="<?php echo ($k-1); ?>"><?php echo ($v); ?></option>-->
                                        <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                                    <!--</select>-->
                                    <input type="hidden" name="order_by" value="order_id">
                                    <input type="hidden" name="sort" value="desc">
                                    <input type="hidden" name="user_id" value="<?php echo ($_GET[user_id]); ?>">
                                </div>
                                <div class="form-group">
                                	<a href="javascript:void(0)" onclick="ajax_get_table('search-form2',1)" id="button-filter search-order" class="btn btn-primary"><i class="fa fa-search"></i> 筛选</a>
                                </div>
                                <!--<div class="form-group">-->
                                	<!--<a href="/index.php?m=Admin&c=Order&a=add_order" class="btn btn-primary"><i class="fa fa-search"></i>添加订单</a>-->
                                <!--</div>                                -->
                                <!--<button type="submit" class="btn btn-default pull-right"><i class="fa fa-file-excel-o"></i>&nbsp;导出excel</button>                               -->
                            </form>
                    </div>
                    <div id="ajax_return">

                    </div>

                </div>
            </div>
        </div>        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    $(document).ready(function(){
        ajax_get_table('search-form2',1);
        
		$('#add_time').daterangepicker({
			format:"YYYY/MM/DD",
			singleDatePicker: false,
			showDropdowns: true,
			minDate:'2016/01/01',
			maxDate:'2030/01/01',
			startDate:'2016/01/01',
		    locale : {
	            applyLabel : '确定',
	            cancelLabel : '取消',
	            fromLabel : '起始时间',
	            toLabel : '结束时间',
	            customRangeLabel : '自定义',
	            daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
	            monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月','七月', '八月', '九月', '十月', '十一月', '十二月' ],
	            firstDay : 1
	        }
		});
    });
    
    // ajax 抓取页面
    function ajax_get_table(tab,page){
        cur_page = page; //当前页面 保存为全局变量
            $.ajax({
                type : "POST",
                url:"/index.php/Admin/order/ajaxindex/p/"+page,//+tab,
                data : $('#'+tab).serialize(),// 你的formid
                success: function(data){
                    $("#ajax_return").html('');
                    $("#ajax_return").append(data);
                }
            });
    }

    // 点击排序
    function sort(field)
    {
        $("input[name='order_by']").val(field);
        var v = $("input[name='sort']").val() == 'desc' ? 'asc' : 'desc';
        $("input[name='sort']").val(v);
        ajax_get_table('search-form2',cur_page);
    }
</script>
</body>
</html>