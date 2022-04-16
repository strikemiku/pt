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
                    <h3 class="panel-title"><i class="fa fa-list"></i>充值申请</h3>
                </div>
                <div class="panel-body">
                    <div class="navbar navbar-default">
                        <form id="search-form2" class="navbar-form form-inline"  method="post" action="<?php echo U('/Admin/User/recharge');?>">
                            <div class="form-group">
                                <label for="input-order-id" class="control-label">充值账户:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="mobile" placeholder="充值用户" value="<?php echo ($_REQUEST['mobile']); ?>" name="nickname" />
                                </div>
                                <label for="input-order-id" class="control-label">充值状态:</label>
                                <div class="form-group">
                                    <select class="form-control" id="pay_status" name="pay_status">
                                        <option value="">-选择充值状态-</option>
                                        <option value="0">未支付</option>
                                        <option value="1">已成功</option>
                                    </select>
                                </div>
                                <label for="input-order-id" class="control-label">充值类型:</label>
                                <div class="form-group">
                                    <select class="form-control" id="type" name="type">
                                        <option value="">-选择充值类型-</option>
                                        <option value="1"<?php if($_REQUEST['type'] == 1): ?>selected<?php endif; ?>>支付宝</option>
                                        <option value="2"<?php if($_REQUEST['type'] == 2): ?>selected<?php endif; ?>>微信</option>
                                        <option value="3"<?php if($_REQUEST['type'] == 3): ?>selected<?php endif; ?>>银行卡</option>
                                    </select>
                                </div>


                                <!--<label for="input-order-id" class="control-label">收款账号:</label>-->
                                <!--<div class="input-group">-->
                                <!--<input type="text" class="form-control" id="input-order-id" placeholder="收款账号" value="<?php echo ($_REQUEST['account_bank']); ?>" name="account_bank" />-->
                                <!--</div>-->
                                <!--<div class="input-group margin">-->
                                <!--<div class="input-group-addon">-->
                                <!--申请时间<i class="fa fa-calendar"></i>-->
                                <!--</div>-->
                                <!--<input type="text" id="start_time" value="<?php echo ($create_time); ?>" name="create_time" class="form-control pull-right">-->
                                <!--</div>-->
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
                                    <th class="sorting text-center">充值编号</th>
                                    <th class="sorting text-center">充值会员</th>
                                    <th class="sorting text-center">充值账户</th>
                                    <th class="sorting text-center">充值金额</th>
                                    <th class="sorting text-center">充值方式</th>
                                    <th class="sorting text-center">状态</th>
                                    <th class="sorting text-center">支付时间</th>
                                    <th class="sorting text-center">申请时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                                        <td class="text-center"><?php echo ($v["order_id"]); ?></td>
                                        <td class="text-center"><?php echo ($v["order_sn"]); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo U('Admin/user/detail',array('id'=>$v[user_id]));?>">
                                                <?php echo ($v["user_name"]); ?>
                                            </a>
                                        </td>
                                        <td class="text-center"><?php echo ($v["mobile"]); ?></td>
                                        <td class="text-center"><?php echo ($v["account"]); ?></td>
                                        <td class="text-center">
                                            <?php echo ($v['pay_name']); ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if($v[pay_status] == 0): ?><span style="color: orange">未支付</span><?php endif; ?>
                                            <?php if($v[pay_status] == 1): ?><span style="color: green">已成功</span><?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($v[pay_time] != null): echo (date("Y-m-d H:m:i",$v["pay_time"])); ?>
                                                <?php else: ?>
                                                ----<?php endif; ?>

                                        </td>
                                        <td class="text-center">
                                            <?php if($v[ctime] != null): echo (date("Y-m-d H:m:i",$v["ctime"])); ?>
                                                <?php else: ?>
                                                ----<?php endif; ?>

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
                                    <td>审核状态:</td>
                                    <td>
                                        <input type="radio" name="status" value="1" <?php if($data["status"] == 1): ?>checked<?php endif; ?>>通过
                                        <input type="radio" name="status" value="2" <?php if($data["status"] == 2): ?>checked<?php endif; ?>>拒绝
                                    </td>
                                </tr>
                                <tr style="height: 50px;">
                                    <td>审核描述:</td>
                                    <td>
                                        <textarea rows="4" cols="30" id="remark" name="remark"><?php echo ($data['remark']); ?></textarea>
                                        <span id="err_remark" style="color:#F00; display:none;"></span>
                                    </td>
                                </tr>
                                <tr style="height: 50px;">
                                    <td></td>
                                    <td>
                                        <input type="hidden" id="car_id" value="">
                                        <input type="button" name="tijao" onclick="tijiao()" value="审核">
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
    // author :凌寒 2018年5月31日10:16:06 审核页面弹出
    function shen(id,status){
        if(status == '0'){
            $("#table").css('display','block');
            layer.open({
                type: 1,
                title: '充值审核',
                skin: 'layui-layer-rim', //加上边框
                area: ['400px', '250px'], //宽高
                content: $("#editForm")
            });
            $("#car_id").val(id);
        }else{
            layer.msg("已审核,请勿重复审核");
            return false;
        }
    }
    // author :凌寒 2018年5月31日10:16:23 审核提交
    function tijiao() {

        var id=$("#car_id").val();

        var status = $("input[name='status']:checked").val();

        var remark= $.trim($('#remark').val());

        if(!status){
            layer.msg("请选择审核状态");
            return false;
        }
        if(remark.length == 0)
        {
            layer.msg("请填写审核备注");
            return false;
        }
        $.ajax({
            type : 'post',
            url : '/index.php?m=Admin&c=User&a=chongzhi_identify&t='+Math.random(),
            data : {id:id,status:status,remark:remark},
            dataType : 'json',
            success : function(res){
                if(res.state == 200){
                    layer.msg(res.msg,{icon:6,time:1500},function () {
                        location.href=res.url;
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
</script>
</body>
</html>