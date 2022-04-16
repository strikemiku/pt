<?php if (!defined('THINK_PATH')) exit();?>
                    <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <td class="text-center">
                                        <a href="javascript:sort('user_id');">ID</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort('username');">昵称</a>
                                    </td>
                                    <!--<td class="text-center">-->
                                        <!--<a href="javascript:sort('mobile');">头像</a>-->
                                    <!--</td>-->
                                    <td class="text-center">
                                        <a href="javascript:sort('level');">账号</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort('level');">等级</a>
                                    </td>
                                    <!--<td class="text-center">-->
                                        <!--<a href="javascript:sort('level');">达标</a>-->
                                    <!--</td>-->
                                    <td class="text-center">
                                        <a href="javascript:sort('user_code');">邀请码</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort('mobile');">推广码</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort('user_money');">余额</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort('user_money');">积分</a>
                                    </td>
                                    <!--<td class="text-center">-->
                                        <!--<a href="javascript:sort('room_num');">推荐值</a>-->
                                    <!--</td>-->
                                    <!--<td class="text-center">-->
                                        <!--<a href="javascript:sort('is_red');">贡献值</a>-->
                                    <!--</td>-->
                                    <td class="text-center">
                                        <a href="javascript:sort('is_red');">活跃度</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort('is_lock');">封号</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort('reg_time');">注册时间</a>
                                    </td>
                                    <td class="text-center">操作</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($userList)): $i = 0; $__LIST__ = $userList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                                        <td class="text-center"><?php echo ($list["user_id"]); ?></td>
                                        <td class="text-center"><?php echo (getSubstr($list["nickname"],0,8)); ?></td>
                                        <td class="text-center"><?php echo ($list["mobile"]); ?></td>
                                        <!--<td class="text-center">-->
                                            <!--<img width="35" height="35" style="border-radius: 50%;" src="<?php echo ($list['head_pic']); ?>"/>-->
                                        <!--</td>-->
                                        <td class="text-center"><?php echo ($list["level_name"]); ?></td>
                                        <td class="text-center"><?php echo ($list["user_code"]); ?></td>
                                        <td class="text-center"><?php echo ((isset($list["pcode"]) && ($list["pcode"] !== ""))?($list["pcode"]):"---"); ?></td>
                                        <td class="text-center"><?php echo ($list["user_money"]); ?></td>
                                        <td class="text-center"><?php echo ($list["pay_points"]); ?></td>
                                        <!--<td class="text-center"><?php echo ($list["recom_num"]); ?></td>-->
                                        <!--<td class="text-center"><?php echo ($list["devote_num"]); ?></td>-->
                                        <td class="text-center"><?php echo ($list["active_num"]); ?></td>
                                        <!--<td class="text-center">-->
                                            <!--<?php if($list[is_red] == 1): ?>-->
                                                <!--是-->
                                                <!--<?php else: ?>-->
                                                <!--否-->
                                            <!--<?php endif; ?>-->
                                        <!--</td>-->
                                        <td class="text-center">
                                            <img width="20" height="20" src="/Public/images/<?php if($list[is_lock] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('users','user_id','<?php echo ($list["user_id"]); ?>','is_lock',this)"/>
                                        </td>
                                        <td class="text-center"><?php echo (date('Y-m-d',$list["reg_time"])); ?></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" onclick="login(<?php echo ($list['user_id']); ?>)"  data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情" >登录</a>
                                            <a href="<?php echo U('Admin/user/detail',array('id'=>$list['user_id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情">编辑</a>
                                            <!--<a href="<?php echo U('Admin/user/address',array('id'=>$list['user_id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="收货地址">收货地址</a>-->
                                            <a href="<?php echo U('Admin/user/account_edit',array('id'=>$list['user_id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="充值">充值</a>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-sm-3 text-left">
                        </div>
                        <div class="col-sm-6 text-right"><?php echo ($page); ?></div>
                    </div>
<script>
    // author :凌寒 2018年5月17日13:53:02 执行充值方法
    function jihuo(user_id,is_lock)
    {
        if(user_id == ''){
            layer.msg('会员不存在!',{icon:5,time:1500});
            return false;
        }
        if(is_lock == 1){
            layer.msg("会员已激活!",{icon:5,time:1500});
            return false;
        }

        layer.confirm('您确定要激活该用户吗?',{btn: ['激活', '取消'],title:"温馨提示"}, function(){
            $.ajax({
                type : 'post',
                url : '/index.php?m=Admin&c=User&a=jihuo_user&t='+Math.random(),
                data : {jh_user_id:user_id},
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
    //设置创业基金
    function yunying(user_id,is_yyzx)
    {
        if(user_id == ''){
            layer.msg('会员不存在!',{icon:5,time:1500});
            return false;
        }
        if(is_yyzx == 1){
            var desc = '设置可以申请创业基金吗?';
        }else{
            var desc = '设置取消申请创业基金吗?';
        }
        layer.confirm(desc,{btn: ['确定', '取消'],title:"温馨提示"}, function(){
            $.ajax({
                type : 'post',
                url : '/index.php?m=Admin&c=User&a=cyjj&t='+Math.random(),
                data : {jh_user_id:user_id,},
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
    $(".pagination  a").click(function(){
        var page = $(this).data('p');
        ajax_get_table('search-form2',page);
    });
    //后台登录前台账号
    function login(user_id)
    {
        var url = "<?php echo U('Mobile/User/do_login');?>";
        open_page(url,user_id);
//        $.ajax({
//            type : 'post',
//            url :"<?php echo U('Mobile/User/do_login');?>",
//            data : {type:'admin',user_id:user_id},
//            dataType : 'json',
//            success : function(res){
//                if(res.status == 1){
//
//                }else{
//                    layer.msg(res.msg,{icon:5,time:1500});
//                    return false;
//                }
//            },
//            error : function(XMLHttpRequest, textStatus, errorThrown) {
//                layer.msg('网络失败，请刷新页面后重试');
//            }
//        })
    }
    function open_page(url, user_id) {
        var form = '<form action="' + url + '"  target="_blank"  id="windowOpen" style="display:none">';
        form += '<input name="user_id" value="' +user_id + '"/>';
        form += '<input name="type" value="admin"/>';
        form += '</form>';
        $('body').append(form);
        $('#windowOpen').submit();
        $('#windowOpen').remove();
    }
</script>