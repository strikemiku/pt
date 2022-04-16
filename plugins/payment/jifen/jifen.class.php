<?php
/**
 * tpshop 货到付款插件
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

//namespace plugins\payment\alipay;

use Think\Model\RelationModel;
/**
 * 支付 逻辑定义
 * Class AlipayPayment
 * @package Home\Payment
 */

class cod extends RelationModel
{    
    public $tableName = 'plugin'; // 插件表            
    
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();        
    }    
    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $config_value    支付方式信息
     */
    function get_code($order, $config_value)
    {

            $url = SITE_URL.U('Payment/returnUrl',array('order_sn'=>$order['order_sn'],'pay_code'=>'cod'));

            return "<script>location.href='".$url."';</script>";         
    }         
    
    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {
        $user_money = M('users')->where(array('user_id'=>$_COOKIE['user_id']))->getField('user_money');  //余额支付

        $total_amount = M('order')->where("order_sn = '{$_REQUEST['order_sn']}'")->getField('total_amount');

        if($user_money >= $total_amount){
            $user_money = $user_money - $total_amount;
        }

        $res = M('users')->where(array('user_id'=>$_COOKIE['user_id']))->save(array('user_money'=>$user_money));  //更新用户余额

        if($res){
            //余额记录
            upd_money($_COOKIE['user_id'],$total_amount,0,"商城购物",6);
            //修改订单状态
            update_pay_status($_REQUEST['order_sn']);
            return array('status'=>1,'order_sn'=>$_REQUEST['order_sn']);
        }
    }
}