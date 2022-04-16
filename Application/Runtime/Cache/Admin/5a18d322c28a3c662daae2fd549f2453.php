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
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>参团列表</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                        <form id="search-form2" class="navbar-form form-inline"  method="post" action="<?php echo U('/Admin/Promotion/homeOrder');?>">
                            <div class="form-group">
                                <label  class="control-label">会员名称:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="nickname" name="nickname" placeholder="会员名称" value="<?php echo ($_REQUEST['nickname']); ?>"  />
                                </div>
                                <label  class="control-label">房间编号:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="home_code" name="home_code" placeholder="请输入编号" value="<?php echo ($_REQUEST['home_code']); ?>" />
                                </div>

                                <label  class="control-label">拼团状态:</label>
                                <div class="input-group">
                                    <select name="status" class="form-control" style="width:120px;">
                                        <option value="">请选择状态</option>
                                        <option value="1">拼团中</option>
                                        <option value="2">拼团成功</option>
                                        <option value="3">未成团</option>
                                        <option value="4">拼团失败</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" id="button-filter search-order" type="submit"><i class="fa fa-search"></i> 筛选</button>
                            </div>
                        </form>
                    </div>

                    <div id="ajax_return">

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="sorting text-center">ID</th>
                                    <th class="sorting text-center">会员名称</th>
                                    <th class="sorting text-center">房间编号</th>
                                    <th class="sorting text-center">支付方式</th>
                                    <th class="sorting text-center">中奖状态</th>
                                    <th class="sorting text-center">拼团状态</th>
                                    <th class="sorting text-center">支付时间</th>
                                    <th class="sorting text-center">拼团时间</th>
                                    <th class="sorting text-center">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                                        <td class="text-center"><?php echo ($v["id"]); ?></td>
                                        <td class="text-center"><?php echo ($v["user_name"]); ?></td>
                                        <td class="text-center"><?php echo ($v["home_code"]); ?></td>
                                        <td class="text-center"><?php echo ($v["pay_name"]); ?></td>
                                        <td class="text-center">
                                            <?php if($v["is_false"] == 1): ?>随机
                                                <?php elseif($v["is_false"] == 2): ?>
                                                不中奖
                                                <?php elseif($v["is_false"] == 3): ?>
                                                中奖<?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($v["status"] == 1): ?><span style="color:orange">拼团中</span>
                                                <?php elseif($v["status"] == 2): ?>
                                                <span style="color: green">拼团成功</span>
                                                <?php elseif($v["status"] == 3): ?>
                                                <span style="color: red">未成团</span>
                                                <?php elseif($v["status"] == 4): ?>
                                                <span style="color: red">拼团失败</span><?php endif; ?>
                                        </td>
                                        <td class="text-center"><?php echo (date("Y-m-d H:i",$v["pay_time"])); ?></td>
                                        <td class="text-center"><?php echo (date("Y-m-d H:i",$v["add_time"])); ?></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" onclick="shen(<?php echo ($v['id']); ?>,<?php echo ($v['is_false']); ?>)"  data-toggle="tooltip" title="" class="btn btn-info"  >中奖设置</a>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 text-left"></div>
                            <div class="col-sm-6 text-right"><?php echo ($show); ?></div>
                        </div>
                        <form method="post" id="editForm" >
                            <table class="table-bordered" style="display: none" id="table">
                                <tr style="height: 50px;">
                                    <td>中奖状态:</td>
                                    <td>
                                        <input type="radio" name="is_false" value="1" checked>随机
                                        <input type="radio" name="is_false" value="2" >不中奖
                                        <input type="radio" name="is_false" value="3" >中奖
                                    </td>
                                </tr>
                                <tr style="height: 50px;">
                                    <td></td>
                                    <td>
                                        <input type="hidden" id="id" value="">
                                        <input type="button" name="tijao" onclick="tijiao()" value="设置">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    // author :凌寒 2018年5月31日10:16:06 审核页面弹出
    function shen(id,is_false){
        $("#table").css('display','block');
        layer.open({
            type: 1,
            title: '拼团中奖设置',
            skin: 'layui-layer-rim', //加上边框
            area: ['250px', '150px'], //宽高
            content: $("#editForm")
        });
        $("#id").val(id);
    }
    //审核提交
    function tijiao() {
        var id=$("#id").val();
        var is_false = $("input[name='is_false']:checked").val();
        if(!is_false){
            layer.msg("请选择中奖状态");
            return false;
        }
        $.ajax({
            type : 'post',
            url : '/index.php?m=Admin&c=Promotion&a=set_win&t='+Math.random(),
            data : {id:id,is_false:is_false},
            dataType : 'json',
            success : function(res){
                if(res.status == 200){
                    layer.msg(res.msg,{icon:6,time:1500},function () {
                        window.location.reload();
                    });
                }else{
                    layer.msg(res.msg,{icon:5,time:1500});
                    return false;
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                alert('网络失败，请刷新页面后重试');
            }
        })
    }
    //设置中奖
    function set_win(id,is_zhong)
    {
        if(is_zhong == 0){
            var desc = '设置为中奖？';
        }else{
            var desc = '设置中奖?';
        }
        layer.confirm(desc,{btn: ['确定', '取消'],title:"温馨提示"}, function(){
            $.ajax({
                type : 'post',
                url : '/index.php?m=Admin&c=Promotion&a=set_win&t='+Math.random(),
                data : {id:id},
                dataType : 'json',
                success : function(res){
                    if(res.status == 200){
                        layer.msg(res.msg,{icon:6,time:1500},function () {
                            window.location.reload();
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
        });
    }
    // 删除操作
    function del(id)
    {
        if(!confirm('确定要删除吗?'))
            return false;
        $.ajax({
            url:"/index.php?m=Admin&c=User&a=delWithdrawals&id="+id,
            success: function(v){
                var v =  eval('('+v+')');
                if(v.hasOwnProperty('status') && (v.status == 1))
                    location.href='<?php echo U('Admin/User/withdrawals');?>';
                else
                layer.msg(v.msg, {icon: 2,time: 1000}); //alert(v.msg);
            }
        });
        return false;
    }

    $(document).ready(function() {
        $('#start_time').daterangepicker({
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
</script>
</body>
</html>