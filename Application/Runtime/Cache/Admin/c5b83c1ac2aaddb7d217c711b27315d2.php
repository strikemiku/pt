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
 

<!--物流配置 css -start-->
<style>
    ul.group-list {
        width: 96%;min-width: 1000px; margin: auto 5px;list-style: disc outside none;
    }
    ul.group-list li {
        white-space: nowrap;float: left;
        width: 150px; height: 25px;
        padding: 3px 5px;list-style-type: none;
        list-style-position: outside;border: 0px;margin: 0px;
    }
</style>
<!--物流配置 css -end-->

<!--以下是在线编辑器 代码 -->
<script type="text/javascript">
    /*
     * 在线编辑器相 关配置 js
     *  参考 地址 http://fex.baidu.com/ueditor/
     */
    window.UEDITOR_Admin_URL = "/Public/plugins/Ueditor/";
    var URL_upload = "<?php echo ($URL_upload); ?>";
    var URL_fileUp = "<?php echo ($URL_fileUp); ?>";
    var URL_scrawlUp = "<?php echo ($URL_scrawlUp); ?>";
    var URL_getRemoteImage = "<?php echo ($URL_getRemoteImage); ?>";
    var URL_imageManager = "<?php echo ($URL_imageManager); ?>";
    var URL_imageUp = "<?php echo ($URL_imageUp); ?>";
    var URL_getMovie = "<?php echo ($URL_getMovie); ?>";
    var URL_home = "<?php echo ($URL_home); ?>";
</script>
<script type="text/javascript" charset="utf-8" src="/Public/plugins/Ueditor/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/Public/plugins/Ueditor/ueditor.all.min.js"> </script>
<script type="text/javascript" charset="utf-8" src="/Public/plugins/Ueditor/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript">

    var editor;
    $(function () {
        //具体参数配置在  editor_config.js  中
        var options = {
            zIndex: 999,
            initialFrameWidth: "95%", //初化宽度
            initialFrameHeight: 400, //初化高度
            focus: false, //初始化时，是否让编辑器获得焦点true或false
            maximumWords: 99999, removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign'
            , //允许的最大字符数 'fullscreen',
            pasteplain:false, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
            autoHeightEnabled: true
            /*   autotypeset: {
             mergeEmptyline: true,        //合并空行
             removeClass: true,           //去掉冗余的class
             removeEmptyline: false,      //去掉空行
             textAlign: "left",           //段落的排版方式，可以是 left,right,center,justify 去掉这个属性表示不执行排版
             imageBlockLine: 'center',    //图片的浮动方式，独占一行剧中,左右浮动，默认: center,left,right,none 去掉这个属性表示不执行排版
             pasteFilter: false,          //根据规则过滤没事粘贴进来的内容
             clearFontSize: false,        //去掉所有的内嵌字号，使用编辑器默认的字号
             clearFontFamily: false,      //去掉所有的内嵌字体，使用编辑器默认的字体
             removeEmptyNode: false,      //去掉空节点
             //可以去掉的标签
             removeTagNames: {"font": 1},
             indent: false,               // 行首缩进
             indentValue: '0em'           //行首缩进的大小
             }*/,
            toolbars: [
                ['fullscreen', 'source', '|', 'undo', 'redo',
                    '|', 'bold', 'italic', 'underline', 'fontborder',
                    'strikethrough', 'superscript', 'subscript',
                    'removeformat', 'formatmatch', 'autotypeset',
                    'blockquote', 'pasteplain', '|', 'forecolor',
                    'backcolor', 'insertorderedlist',
                    'insertunorderedlist', 'selectall', 'cleardoc', '|',
                    'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                    'customstyle', 'paragraph', 'fontfamily', 'fontsize',
                    '|', 'directionalityltr', 'directionalityrtl',
                    'indent', '|', 'justifyleft', 'justifycenter',
                    'justifyright', 'justifyjustify', '|', 'touppercase',
                    'tolowercase', '|', 'link', 'unlink', 'anchor', '|',
                    'imagenone', 'imageleft', 'imageright', 'imagecenter',
                    '|', 'insertimage', 'emotion', 'insertvideo',
                    'attachment', 'map', 'gmap', 'insertframe',
                    'insertcode', 'webapp', 'pagebreak', 'template',
                    'background', '|', 'horizontal', 'date', 'time',
                    'spechars', 'wordimage', '|',
                    'inserttable', 'deletetable',
                    'insertparagraphbeforetable', 'insertrow', 'deleterow',
                    'insertcol', 'deletecol', 'mergecells', 'mergeright',
                    'mergedown', 'splittocells', 'splittorows',
                    'splittocols', '|', 'print', 'preview', 'searchreplace']
            ]
        };
        editor = new UE.ui.Editor(options);
        editor.render("goods_content");  //  指定 textarea 的  id 为 goods_content

    });
</script>
<!--以上是在线编辑器 代码  end-->
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
        <div class="container-fluid">
            <div class="pull-right">
                <a href="javascript:history.go(-1)" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="返回"><i class="fa fa-reply"></i></a>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i>商品详情</h3>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_tongyong" data-toggle="tab">通用信息</a></li>
                        <li><a href="#tab_goods_images" data-toggle="tab">商品相册</a></li>
                        <!--<li><a href="#tab_goods_shipping" data-toggle="tab">商品物流</a></li>-->
                    </ul>
                    <!--表单数据-->
                    <form method="post" id="addEditGoodsForm">

                        <!--通用信息-->
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_tongyong">

                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>商品名称:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["goods_name"]); ?>" name="goods_name" class="form-control" style="width:550px;"/>
                                            <span id="err_goods_name" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>关键词:</td>
                                        <td>
                                            <input type="text" class="form-control" style="width:550px;" value="<?php echo ($goodsInfo["keywords"]); ?>" name="keywords" placeholder="用空格隔开"/>
                                            <span id="err_keywords" style="color:#F00;"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品简介:</td>
                                        <td>
                                            <textarea rows="3" cols="75" name="goods_remark"><?php echo ($goodsInfo["goods_remark"]); ?></textarea>
                                            <span id="err_goods_remark" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    <!--<tr>-->
                                        <!--<td>商品分类:</td>-->
                                        <!--<td>-->
                                            <!--<div class="col-xs-3">-->
                                                <!--<select name="cat_id" id="cat_id" onchange="get_category(this.value,'cat_id_2','0');" class="form-control" style="width:220px;margin-left:-15px;">-->
                                                    <!--<option value="0">请选择商品分类</option>-->
                                                    <!--<?php if(is_array($cat_list)): foreach($cat_list as $k=>$v): ?>-->
                                                        <!--<option value="<?php echo ($v['id']); ?>" <?php if($v['id'] == $level_cat['1']): ?>selected="selected"<?php endif; ?> >-->
                                                        <!--<?php echo ($v['name']); ?>-->
                                                        <!--</option>-->
                                                    <!--<?php endforeach; endif; ?>-->
                                                <!--</select>-->
                                            <!--</div>-->
                                            <!--<div class="col-xs-3">-->
                                                <!--<select name="cat_id_2" id="cat_id_2" onchange="get_category(this.value,'cat_id_3','0');" class="form-control" style="width:220px;margin-left:-15px;">-->
                                                    <!--<option value="0">请选择商品分类</option>-->
                                                <!--</select>-->
                                            <!--</div>-->
                                            <!--<span id="err_cat_id" style="color:#F00; display:none;"></span>-->
                                        <!--</td>-->
                                    <!--</tr>-->
                                    <tr>
                                        <td >拼团人数：</td>
                                        <td >
                                            <div class="col-xs-3">
                                                <select id="group_num" name="group_num" class="form-control" style="width:220px;margin-left:-15px;">
                                                    <?php if(is_array($group_num)): foreach($group_num as $key=>$v): ?><option value="<?php echo ($v['num']); ?>" <?php if($goodsInfo[group_num] == $v['num']): ?>selected<?php endif; ?>><?php echo ($v['num']); ?>人</option><?php endforeach; endif; ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <!--<tr>-->
                                        <!--<td>拼团件数:</td>-->
                                        <!--<td>-->
                                            <!--<input type="text" value="<?php echo ($goodsInfo["sales_sum"]); ?>" name="sales_sum" class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />-->
                                            <!--<span id="err_yunfei" style="color:#F00;">件</span>-->
                                        <!--</td>-->
                                    <!--</tr>-->
                                    <tr>
                                        <td >拼团等级：</td>
                                        <td >
                                            <div class="col-xs-3">
                                                <select id="group_price" name="group_price" onchange="change_price()" class="form-control" style="width:220px;margin-left:-15px;">
                                                    <option value="100" <?php if($goodsInfo[group_price] == 100): ?>selected<?php endif; ?>>普通商品</option>
                                                    <?php if(is_array($group_price)): foreach($group_price as $key=>$v): ?><option value="<?php echo ($v['price']); ?>" <?php if($v[price] == $goodsInfo['group_price']): ?>selected<?php endif; ?>><?php echo ($v['name']); ?></option><?php endforeach; endif; ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>出售价:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["shop_price"]); ?>" name="shop_price" id="shop_price"  class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_shop_price" style="color:#F00;">元</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>市场价:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["market_price"]); ?>" name="market_price"  class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_market_price" style="color:#F00;">元</span>
                                        </td>
                                    </tr>
                                    <!--<tr>-->
                                        <!--<td>邮费:</td>-->
                                        <!--<td>-->
                                            <!--<input type="text" value="<?php echo ($goodsInfo["yunfei"]); ?>" name="yunfei"  class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />-->
                                            <!--<span id="err_market_price" style="color:#F00;">元</span>-->
                                        <!--</td>-->
                                    <!--</tr>-->
                                    <!--<tr>-->
                                        <!--<td>适用范围：</td>-->
                                        <!--<td>-->
                                            <!--<input type="checkbox" <?php if(strripos($info['group'],$vo['level_id']) !== false): ?>checked<?php endif; ?> name="group[]" value="<?php echo ($vo["level_id"]); ?>">会员-->
                                            <!--<input type="checkbox" <?php if(strripos($info['group'],$vo['level_id']) !== false): ?>checked<?php endif; ?> name="group[]" value="<?php echo ($vo["level_id"]); ?>">达标会员A-->
                                            <!--<input type="checkbox" <?php if(strripos($info['group'],$vo['level_id']) !== false): ?>checked<?php endif; ?> name="group[]" value="<?php echo ($vo["level_id"]); ?>">达标会员B-->
                                            <!--<input type="checkbox" <?php if(strripos($info['group'],$vo['level_id']) !== false): ?>checked<?php endif; ?> name="group[]" value="<?php echo ($vo["level_id"]); ?>">股东A-->
                                            <!--<input type="checkbox" <?php if(strripos($info['group'],$vo['level_id']) !== false): ?>checked<?php endif; ?> name="group[]" value="<?php echo ($vo["level_id"]); ?>">股东B-->
                                        <!--</td>-->
                                    <!--</tr>-->
                                    <tr>
                                        <td >拼团倒计时：</td>
                                        <td >
                                            <input type="text" value="<?php echo ($goodsInfo["dao_time"]); ?>" name="dao_time" class="form-control" style="width:150px;" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_yunfei" style="color:#F00;">分钟</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>库存数量:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["store_count"]); ?>" class="form-control" style="width:150px;" name="store_count" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_store_count" style="color:#F00; ">件</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品排序:</td>
                                        <td>
                                            <input type="text" value="<?php echo ($goodsInfo["sort"]); ?>" class="form-control" style="width:150px;" name="sort" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')" />
                                            <span id="err_sort" style="color:#F00; "></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-sm-2">缩略图:(192px*155px)</td>
                                        <td >
                                            <img src='/Public/images/no_img.png'  style="width: 100px;height: 100px;float: left;border: 1px solid gray;" onclick="GetUploadify(1,'','original_img','call_back3');">
                                            <input type="hidden" name="original_img" id="original_img" value="<?php echo ($goodsInfo["original_img"]); ?>">
                                            <img width="100" height="100"   class="original_imgs" src="<?php echo ($goodsInfo["original_img"]); ?>"  <?php if($goodsInfo["original_img"] != ''): ?>style="display: block;float: left"<?php else: ?>style="display: none;float: left"<?php endif; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>商品详情:</td>
                                        <td width="85%">
                                            <textarea class="span12 ckeditor" id="goods_content" name="goods_content" title=""><?php echo ($goodsInfo["goods_content"]); ?></textarea>
                                            <span id="err_goods_content" style="color:#F00; display:none;"></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--其他信息-->

                            <!-- 商品相册-->
                            <div class="tab-pane" id="tab_goods_images">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <?php if(is_array($goodsImages)): foreach($goodsImages as $k=>$vo): ?><div style="width:100px; text-align:center; margin: 5px; display:inline-block;" class="goods_xc">
                                                    <input type="hidden" value="<?php echo ($vo['image_url']); ?>" name="goods_images[]">
                                                    <a onclick="" href="<?php echo ($vo['image_url']); ?>" target="_blank"><img width="100" height="100" src="<?php echo ($vo['image_url']); ?>"></a>
                                                    <br>
                                                    <a href="javascript:void(0)" onclick="ClearPicArr2(this,'<?php echo ($vo['image_url']); ?>')">删除</a>
                                                </div><?php endforeach; endif; ?>

                                            <div class="goods_xc" style="width:100px; text-align:center; margin: 5px; display:inline-block;">
                                                <input type="hidden" name="goods_images[]" value="" />
                                                <a href="javascript:void(0);" onclick="GetUploadify(10,'','goods','call_back2');"><img src="/Public/images/add-button.jpg" width="100" height="100" /></a>
                                                <br/>
                                                <a href="javascript:void(0)">请上传(470*200px)轮播图</a>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--商品相册-->
                            <!-- 商品物流-->
                            <div class="tab-pane" id="tab_goods_shipping">
                                <h4><b>物流配送：</b><input type="checkbox" onclick="choosebox(this)">全选</h4>
                                <table class="table table-bordered table-striped dataTable" id="goods_shipping_table">
                                    <?php if(is_array($plugin_shipping)): foreach($plugin_shipping as $kk=>$shipping): ?><tr>
                                            <td class="title left" style="padding-right:50px;">
                                                <b><?php echo ($shipping[name]); ?>：</b>
                                                <label class="right"><input type="checkbox" value="1" cka="mod-<?php echo ($kk); ?>">全选</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <ul class="group-list">
                                                    <?php if(is_array($shipping_area)): foreach($shipping_area as $key=>$vv): if($vv[shipping_code] == $shipping[code]): ?><li><label><input type="checkbox" name="shipping_area_ids[]" value="<?php echo ($vv["shipping_area_id"]); ?>" <?php if(in_array($vv['shipping_area_id'],$goods_shipping_area_ids)): ?>checked='checked='<?php endif; ?> ck="mod-<?php echo ($kk); ?>"><?php echo ($vv["shipping_area_name"]); ?></label></li><?php endif; endforeach; endif; ?>
                                                    <div class="clear-both"></div>
                                                </ul>
                                            </td>
                                        </tr><?php endforeach; endif; ?>
                                </table>
                            </div>
                            <!-- 商品物流-->
                        </div>
                        <div class="pull-left" style="margin-left: 17%;">
                            <input type="hidden" name="goods_id" value="<?php echo ($goodsInfo["goods_id"]); ?>">
                            <button class="btn btn-primary" onclick="ajax_submit_form('addEditGoodsForm','<?php echo U('Goods/addEditGoods?is_ajax=1');?>');" title="" data-toggle="tooltip" type="button" data-original-title="保存">保存</button>
                        </div>
                    </form><!--表单数据-->
                </div>
            </div>
        </div>    <!-- /.content -->
    </section>
</div>
<script>
    $(document).ready(function(){
        $(":checkbox[cka]").click(function(){
            var $cks = $(":checkbox[ck='"+$(this).attr("cka")+"']");
            if($(this).is(':checked')){
                $cks.each(function(){$(this).prop("checked",true);});
            }else{
                $cks.each(function(){$(this).removeAttr('checked');});
            }
        });
        $("#group_price").change(function(){
            var value = $(this).val();
            if(value==100){
                $("#shop_price").attr('readonly',false);
            }else{
                $("#shop_price").val(value);
                $("#shop_price").attr('readonly',true);
            }
        });
    });

    function choosebox(o){
        var vt = $(o).is(':checked');
        if(vt){
            $('input[type=checkbox]').prop('checked',vt);
        }else{
            $('input[type=checkbox]').removeAttr('checked');
        }
    }
    /*
     * 以下是图片上传方法
     */
    // 上传商品图片成功回调函数
    function call_back(fileurl_tmp){
        $("#original_img").val(fileurl_tmp);
        $("#original_img2").attr('href', fileurl_tmp);
    }

    function call_back3(fileurl_tmp){

        $("#original_img").val(fileurl_tmp);
        $(".original_imgs").attr('src',fileurl_tmp);
        $(".original_imgs").css('display','block');
    }

    // 上传商品相册回调函数
    function call_back2(paths){

        var  last_div = $(".goods_xc:last").prop("outerHTML");
        for (i=0;i<paths.length ;i++ )
        {
            $(".goods_xc:eq(0)").before(last_div);	// 插入一个 新图片
            $(".goods_xc:eq(0)").find('a:eq(0)').attr('href',paths[i]).attr('onclick','').attr('target', "_blank");// 修改他的链接地址
            $(".goods_xc:eq(0)").find('img').attr('src',paths[i]);// 修改他的图片路径
            $(".goods_xc:eq(0)").find('a:eq(1)').attr('onclick',"ClearPicArr2(this,'"+paths[i]+"')").text('删除');
            $(".goods_xc:eq(0)").find('input').val(paths[i]); // 设置隐藏域 要提交的值
        }
    }
    /*
     * 上传之后删除组图input     
     * @access   public
     * @val      string  删除的图片input
     */
    function ClearPicArr2(obj,path)
    {
        $.ajax({
            type:'GET',
            url:"<?php echo U('Admin/Uploadify/delupload');?>",
            data:{action:"del", filename:path},
            success:function(){
                $(obj).parent().remove(); // 删除完服务器的, 再删除 html上的图片
            }
        });
        // 删除数据库记录
        $.ajax({
            type:'GET',
            url:"<?php echo U('Admin/Goods/del_goods_images');?>",
            data:{filename:path},
            success:function(){
                //
            }
        });
    }



    /** 以下 商品属性相关 js*/
    $(document).ready(function(){

        // 商品类型切换时 ajax 调用  返回不同的属性输入框
        $("#goods_type").change(function(){
            var goods_id = $("input[name='goods_id']").val();
            var type_id = $(this).val();
            $.ajax({
                type:'GET',
                data:{goods_id:goods_id,type_id:type_id},
                url:"/index.php/admin/Goods/ajaxGetAttrInput",
                success:function(data){
                    $("#goods_attr_table tr:gt(0)").remove()
                    $("#goods_attr_table").append(data);
                }
            });
        });
        // 触发商品类型
        $("#goods_type").trigger('change');
        $("input[name='exchange_integral']").blur(function(){
            var shop_price = parseInt($("input[name='shop_price']").val());
            var exchange_integral = parseInt($(this).val());
            if (shop_price * 100 < exchange_integral) {

            }
        });
    });


    // 属性输入框的加减事件
    function addAttr(a)
    {
        var attr = $(a).parent().parent().prop("outerHTML");
        attr = attr.replace('addAttr','delAttr').replace('+','-');
        $(a).parent().parent().after(attr);
    }
    // 属性输入框的加减事件
    function delAttr(a)
    {
        $(a).parent().parent().remove();
    }


    /** 以下 商品规格相关 js*/
    $(document).ready(function(){

        // 商品类型切换时 ajax 调用  返回不同的属性输入框
        $("#spec_type").change(function(){
            var goods_id = '<?php echo ($goodsInfo["goods_id"]); ?>';
            var spec_type = $(this).val();
            $.ajax({
                type:'GET',
                data:{goods_id:goods_id,spec_type:spec_type},
                url:"<?php echo U('admin/Goods/ajaxGetSpecSelect');?>",
                success:function(data){
                    $("#ajax_spec_data").html('')
                    $("#ajax_spec_data").append(data);
                    //alert('132');
                    ajaxGetSpecInput();	// 触发完  马上处罚 规格输入框
                }
            });
        });
        // 触发商品规格
        $("#spec_type").trigger('change');
    });

    /** 以下是编辑时默认选中某个商品分类*/
    $(document).ready(function(){

        <?php if($level_cat['2'] > 0): ?>// 商品分类第二个下拉菜单
            get_category('<?php echo ($level_cat[1]); ?>','cat_id_2','<?php echo ($level_cat[2]); ?>');<?php endif; ?>
        <?php if($level_cat['3'] > 0): ?>// 商品分类第二个下拉菜单
            get_category('<?php echo ($level_cat[2]); ?>','cat_id_3','<?php echo ($level_cat[3]); ?>');<?php endif; ?>

        //  扩展分类
        <?php if($level_cat2['2'] > 0): ?>// 商品分类第二个下拉菜单
            get_category('<?php echo ($level_cat2[1]); ?>','extend_cat_id_2','<?php echo ($level_cat2[2]); ?>');<?php endif; ?>
        <?php if($level_cat2['3'] > 0): ?>// 商品分类第二个下拉菜单
            get_category('<?php echo ($level_cat2[2]); ?>','extend_cat_id_3','<?php echo ($level_cat2[3]); ?>');<?php endif; ?>

    });

</script>
</body>
</html>