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
 

<script type="text/javascript" src="/Public/tuijian/js/proTree.js" ></script>
<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>
	</ol>
</div>

    <link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/font-awesome/4.6.0/css/font-awesome.min.css">
    <style type="text/css">
        * {
            padding: 0;
            margin: 0;
            font-family: "microsoft yahei";
        }

        ul li {
            list-style-type: none;
        }

        .box {
            width: 200px;
            /*border: 1px solid red;*/
        }

        ul {
            margin-left: 40px;
            /*border: 1px solid blue;*/
        }

        .menuUl li {
            margin: 10px 0;
        }

        .menuUl li span:hover {
            text-decoration: underline;
            cursor: pointer;
        }

        .menuUl li i { margin-right: 10px; top: 0px; cursor: pointer; color: #161616;font-size: 28px;}
        .menuUl li span{ color: #161616;font-size: 18px;}
    </style>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>推荐关系</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox float-e-margins">
                                <div class="ibox-content">
                                    <div class="innerUl"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<script type="text/javascript">

    //后台传入的 标题列表
    var arr = <?php echo ($list); ?>;
//    console.log(arr);
//     var arr = [
//         {
//             id: 1,
//             name: "一级标题",
//             pid: 0
//         },
//         {
//             id: 2,
//             name: "二级标题",
//             pid: 0
//         },
//         {
//             id: 3,
//             name: "2.1级标题",
//             pid: 2
//         },
//         {
//             id: 4,
//             name: "2.2级标题",
//             pid: 2
//         },
//         {
//             id: 5,
//             name: "1.1级标题",
//             pid: 1
//         },
//         {
//             id: 6,
//             name: "1.2级标题",
//             pid: 1
//         },
//         {
//             id: 7,
//             name: "1.21级标题",
//             pid: 6
//         }, {
//             id: 8,
//             name: "三级标题",
//             pid: 0
//         }, {
//             id: 9,
//             name: "1.22级标题",
//             pid: 6
//         }, {
//             id: 10,
//             name: "1.221级标题",
//             pid: 9
//         }, {
//             id: 11,
//             name: "1.2211级标题",
//             pid: 10
//         }, {
//             id: 12,
//             name: "1.2212级标题",
//             pid: 10
//         }
//     ];
    //标题的图标是集成bootstrap 的图标  更改 请参考bootstrap的字体图标替换自己想要的图标
    $(".innerUl").ProTree({
        arr: arr,
        simIcon: "fa fa-male", //单个标题字体图标 不传默认glyphicon-file
        mouIconOpen: "fa fa-user", //含多个标题的打开字体图标  不传默认glyphicon-folder-open
        mouIconClose:"fa fa-user-plus", //含多个标题的关闭的字体图标  不传默认glyphicon-folder-close
//          callback: function(id,name) {
//              alert("你选择的id是" + id + "，名字是" + name);
//          }

    })
</script>
<!-- /.content-wrapper -->
</body>
</html>