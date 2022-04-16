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

	<section class="content">
       <div class="row">
       		<div class="col-xs-12">
	       		<div class="box">
	             <div class="box-header">
	             <div class="row">
  					<div class="col-md-10">
  						<form action="" method="post">
							<div class="col-xs-2">
								<div class="form-group">
									<div class="input-group">
										<input type="text" name="goods_name" value="<?php echo ($_REQUEST['goods_name']); ?>" placeholder="请输入商品名称" id="input-mobile" class="form-control">
										<!--<span class="input-group-addon" id="basic-addon2"><i class="fa fa-search"></i></span>-->
									</div>
								</div>
							</div>
							<div class="col-xs-2">
								<div class="form-group">
									<div class="input-group">
										<select class="form-control" id="price" name="price">
											<option value="">请选择价格</option>
											<?php if(is_array($price)): foreach($price as $key=>$v): ?><option value="<?php echo ($v['price']); ?>" <?php if($_REQUEST['price'] == v['price']): ?>selected<?php endif; ?>><?php echo ($v['price']); ?></option><?php endforeach; endif; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<button class="btn btn-primary" id="button-filter search-order" type="submit"><i class="fa fa-search"></i> 筛选</button>
							</div>
                  		 </form>
                 	</div>
		  		 </div>
	             </div><!-- /.box-header -->
	             <div class="box-body">
	           	 <div class="row">
	            	<div class="col-sm-12">
		              <table id="list-table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
		                 <thead>
		                   <tr role="row">
							   <td class="text-center">
								   <a href="javascript:sort('goods_id');">ID</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('goods_name');">商品名称</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('cat_id');">价格</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('sales_sum');">房间数</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('shop_price');">拼团总数</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('shop_price');">拼团进行中</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('shop_price');">拼团成功</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('shop_price');">拼团失败</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('shop_price');">销售额</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('shop_price');">本金退回</a>
							   </td>
							   <td class="text-center">
								   <a href="javascript:sort('shop_price');">红包积分</a>
							   </td>
		                   </tr>
		                 </thead>
						<tbody>
                         <?php if(is_array($list)): foreach($list as $k=>$vo): ?><tr >
                             <td class="text-center"><?php echo ($k+1); ?></td>
                             <td class="text-center"><?php echo (getSubstr($vo["goods_name"],0,15)); ?></td>
		                     <td class="text-center"><?php echo ($vo["shop_price"]); ?></td>
		                     <td class="text-center"><?php echo ($vo["home_num"]); ?></td>
		                     <td class="text-center"><?php echo ($vo["total_pt_num"]); ?></td>
							  <td class="text-center"><?php echo ($vo["total_do_pt_num"]); ?></td>
		                     	<td class="text-center"><?php echo ($vo["total_win_pt_num"]); ?></td>
							  <td class="text-center"><?php echo ($vo["total_false_pt_num"]); ?></td>
							  <td class="text-center"><?php echo ($vo["total_sale_num"]); ?></td>
							  <td class="text-center"><?php echo ($vo["total_back_num"]); ?></td>
							  <td class="text-center"><?php echo ($vo["total_back_jifen"]); ?></td>
		                   </tr><?php endforeach; endif; ?>
		                   </tbody>
		                 <tfoot>
		                 </tfoot>
		               </table>
	               </div>
	          </div>
              <div class="row">
              	    <div class="col-sm-6 text-left"></div>
                    <div class="col-sm-6 text-right"><?php echo ($page); ?></div>
              </div>
	          </div>
	        </div>
       	</div>
       </div>
   </section>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#start_time').daterangepicker({
		format:"YYYY-MM-DD",
		singleDatePicker: false,
		showDropdowns: true,
		minDate:'2016-01-01',
		maxDate:'2030-01-01',
		startDate:'2016-01-01',
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
           '今天': [moment(), moment()],
           '昨天': [moment().subtract('days', 1), moment().subtract('days', 1)],
           '最近7天': [moment().subtract('days', 6), moment()],
           '最近30天': [moment().subtract('days', 29), moment()],
           '上一个月': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
        },
        opens: 'right',
        buttonClasses: ['btn btn-default'],
        applyClass: 'btn-small btn-primary',
        cancelClass: 'btn-small',
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
</script>
</body>
</html>