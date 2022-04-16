<?php if (!defined('THINK_PATH')) exit();?><form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <td class="text-center">
                    <a href="javascript:sort('goods_id');">ID</a>
                </td>
                <td class="text-center">
                    <a href="javascript:sort('goods_name');">商品名称</a>
                </td>
                <!--<td class="text-center">-->
                    <!--<a href="javascript:sort('cat_id');">分类</a>-->
                <!--</td>-->
                <td class="text-center">
                    <a href="javascript:sort('group_num');">拼团人数</a>
                </td>
                <td class="text-center">
                    <a href="javascript:sort('shop_price');">出售价</a>
                </td>
                <!--<td class="text-center">-->
                    <!--<a href="javascript:sort('sales_sum');">已拼</a>-->
                <!--</td>-->
                <td class="text-center">
                    <a href="javascript:void(0);">库存</a>
                </td>                
                <td class="text-center">
                    <a href="javascript:sort('is_on_sale');">上架</a>
                </td>
                <!--<td class="text-center">-->
                    <!--<a href="javascript:sort('is_recommend');">推荐</a>-->
                <!--</td>-->
                <td class="text-center">
                    <a href="javascript:sort('sort');">排序</a>
                </td>                   
                <td class="text-center">操作</td>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($goodsList)): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                    <td class="text-center"><?php echo ($list["goods_id"]); ?></td>
                    <td class="text-center"><?php echo (getSubstr($list["goods_name"],0,15)); ?></td>
                    <!--<td class="text-center"><?php echo ($catList[$list[cat_id]][name]); ?></td>-->
                    <td class="text-center"><?php echo ($list["group_num"]); ?></td>
                    <td class="text-center"><?php echo ($list["shop_price"]); ?></td>
                    <!--<td class="text-center"><?php echo ($list["sales_sum"]); ?></td>-->
                    <td class="text-center">
                        <input type="text" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" onpaste="this.value=this.value.replace(/[^\d.]/g,'')"  onchange="ajaxUpdateField(this);" name="store_count" size="4" data-table="goods" data-id="<?php echo ($list["goods_id"]); ?>" value="<?php echo ($list["store_count"]); ?>"/>
                    </td>
                    <td class="text-center">                        
                        <img width="20" height="20" src="/Public/images/<?php if($list[is_on_sale] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('goods','goods_id','<?php echo ($list["goods_id"]); ?>','is_on_sale',this)"/>
                    </td>
                    <!--<td class="text-center">-->
                        <!--<img width="20" height="20" src="/Public/images/<?php if($list[is_recommend] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('goods','goods_id','<?php echo ($list["goods_id"]); ?>','is_recommend',this)"/>-->
                    <!--</td>-->
                    <td class="text-center">                         
                        <input type="text" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" onchange="updateSort('goods','goods_id','<?php echo ($list["goods_id"]); ?>','sort',this)" size="4" value="<?php echo ($list["sort"]); ?>" />
                    </td>                    
                    <td class="text-center">
                        <a  href="<?php echo U('Admin/Promotion/home',array('goods_id'=>$list['goods_id']));?>" class="btn btn-info" title="查看详情">拼团房间</a>
                        <a href="<?php echo U('Admin/Goods/addEditGoods',array('id'=>$list['goods_id']));?>" class="btn btn-primary" title="编辑">编辑</a>
                        <a href="javascript:void(0);" onclick="del('<?php echo ($list[goods_id]); ?>')" class="btn btn-danger" title="删除">删除</a>
                        <!--<a href="javascript:void(0);" onclick="ClearGoodsHtml('<?php echo ($list[goods_id]); ?>')" class="btn btn-default" title="清除静态缓存页面"><i class="fa fa-fw fa-refresh"></i></a>-->
                        <!--<a href="javascript:void(0);" onclick="ClearGoodsThumb('<?php echo ($list[goods_id]); ?>')" class="btn btn-default" title="清除缩略图缓存"><i class="glyphicon glyphicon-picture"></i></a>-->
                    </td>                        
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
</form>
<div class="row">
    <div class="col-sm-3 text-left"></div>
    <div class="col-sm-9 text-right"><?php echo ($page); ?></div>
</div>
<script>
    function goodsSell(goodsId) {
        var bl = "<?php echo ($config['basic_sell_bl']); ?>";
        layer.open({
            title:'温馨提示',
            content: '商品将以原价'+bl+"%卖出，确定要卖出吗",
            btn: ['卖出', '取消'],
            shadeClose: false,
            yes: function () {
                $.ajax({
                    type : 'post',
                    url :"<?php echo U('Admin/Goods/set_goods');?>",
                    data : {goods_id:goodsId},
                    async : false,
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
                        layer.msg('网络失败，请刷新页面后重试');
                    }
                })
            },
            no: function () {
                layer.closeAll();
            }
        });
    }
    function set(goods_id) {
        $.ajax({
            type : 'post',
            url :"<?php echo U('Admin/Goods/set');?>",
            data : {goods_id:goods_id},
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
                layer.msg('网络失败，请刷新页面后重试');
            }
        })
    }
    // 点击分页触发的事件
    $(".pagination  a").click(function(){
        cur_page = $(this).data('p');
        ajax_get_table('search-form2',cur_page);
    });
	
    /*
     * 清除静态页面缓存
     */
    function ClearGoodsHtml(goods_id)
    {
    	$.ajax({
				type:'GET',
				url:"<?php echo U('Admin/System/ClearGoodsHtml');?>",
				data:{goods_id:goods_id},
				dataType:'json',
				success:function(data){
					layer.alert(data.msg, {icon: 2});								 
				}
		});
    }
    /*
     * 清除商品缩列图缓存
     */
    function ClearGoodsThumb(goods_id)
    {
    	$.ajax({
				type:'GET',
				url:"<?php echo U('Admin/System/ClearGoodsThumb');?>",
				data:{goods_id:goods_id},
				dataType:'json',
				success:function(data){
					layer.alert(data.msg, {icon: 2});								 
				}
		});
    }		
</script>