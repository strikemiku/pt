<?php if (!defined('THINK_PATH')) exit(); if(is_array($list)): foreach($list as $key=>$vo): ?><li class="fr_l" style="width: 100%;">
        <a href="<?php echo U('Mobile/Goods/goodsInfo',array('id'=>$vo[goods_id]));?>">
            <div class="tuijian_top" style="width: 130px;float: left">
                <img src="<?php echo ($vo['original_img']); ?>" style="width: 90%;height: 135px;"/>
            </div>
            <div class="tuijian_bottom tuijian_listtop_con" style="width:calc(100% - 130px);float: left">
                <div class="tuijian_list_top clearfixd">
                    <span style="float: left;width: 100%;margin: 0 auto 5px;"><i style="float: none"><?php echo ($vo['group_num']); ?>人团</i><?php echo ($vo['goods_name']); ?></span>
                    <div class="jgzj_nummcon">
                        <div class="zj_left">
                            ￥<?php echo ($vo['market_price']); ?>
                        </div>
                        <div class="zj_center">
                            <p>直降<em style="width:100%; text-align:center; margin-top:-5px;">￥<?php echo ($vo[market_price]-$vo[shop_price]); ?></em></p>
                        </div>
                        <div class="zj_right">
                            ￥<?php echo ($vo['market_price']); ?>
                        </div>
                    </div>
                </div>
                <div class="goumai_con" style="width: 100%">
                    <div class="left">
                        <p style="color: black; width:100%; text-align:center; font-weight: bolder">&yen;<?php echo ($vo['shop_price']); ?></p>
                    </div>
                </div>
            </div>
        </a>
    </li><?php endforeach; endif; ?>