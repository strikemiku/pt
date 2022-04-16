<?php
/**
 * tpshop 微信支付插件
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

use Think\Model\RelationModel;
/**
 * 支付 逻辑定义
 * Class 
 * @package Home\Payment
 */

class weixin extends RelationModel
{    
    public $tableName = 'plugin'; // 插件表        
    public $alipay_config = array();// 支付宝支付配置参数
    
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();
                
        require_once("lib/WxPay.Api.php"); // 微信扫码支付demo 中的文件
        require_once("example/wechatH5Pay.php");    //微信h5支付文件
        require_once("example/WxPay.NativePay.php");
        require_once("example/WxPay.JsApiPay.php");

        $paymentPlugin = M('Plugin')->where("code='weixin' and  type = 'payment' ")->find(); // 找到微信支付插件的配置
        $config_value = unserialize($paymentPlugin['config_value']); // 配置反序列化        
        WxPayConfig::$appid = $config_value['appid']; // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        WxPayConfig::$mchid = $config_value['mchid']; // * MCHID：商户号（必须配置，开户邮件中可查看）
        WxPayConfig::$key = $config_value['key']; // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        WxPayConfig::$appsecret = $config_value['appsecret']; // 公众帐号secert（仅JSAPI支付的时候需要配置)，                                      
    }
    function get_code($order, $config_value)
    {

        $notify_url = SITE_URL.'/index.php/Mobile/Payment/notifyUrl/pay_code/weixin'; // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $wechatAppPay = new \wechatAppPay( WxPayConfig::$appid, WxPayConfig::$mchid, $notify_url,WxPayConfig::$key);
        //网页支付
        $wxData = array(
            'body' => '太羲堂订单',
            'out_trade_no' => $order['order_sn'],
//            'total_fee' => intval($order['order_amount']*100),
            'total_fee' => intval(1),
            'trade_type' => 'MWEB',
            'scene_info' => '{"h5_info": {"type":"Wap","wap_url":"http://txt.lczhufei.top","wap_name": "太羲堂订单"}}',
        );
        $result = $wechatAppPay->unifiedOrder($wxData);
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
            $redirect_url = urlencode(SITE_URL."/Mobile/User/order_list/type/WAITSEND");
            $url = $result['mweb_url'].'&redirect_url=' . $redirect_url;
            echo "<script>window.location.href='".$url."'</script>";
        }
    }
    /**
     * 服务器点对点响应操作给支付接口方调用
     * 
     */
    function response_h5()
    {

        require_once("example/WxPayPubHelper.php");
        //使用通用通知接口
        $notify = new \Notify_pub();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        $result_code= $notify->data['result_code'];
        // 订单号
        $out_trade_no= $notify->data['out_trade_no'];
        if($result_code=='SUCCESS'){
            update_pay_status($out_trade_no); // 修改订单支付状态
        }

//        //以log文件形式记录回调信息
//        $log_name = "./notify_url.log";//log文件路径
//        file_put_contents($log_name, "【接收到的notify通知】:\n".$xml."\n");
    }
    function response()
    {
        require_once("example/WxPayPubHelper.php");
        //使用通用通知接口
        $notify = new \Notify_pub();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        $result_code= $notify->data['result_code'];
        // 订单号
        $out_trade_no= $notify->data['out_trade_no'];
        if($result_code=='SUCCESS'){
            update_pay_status($out_trade_no); // 修改订单支付状态
        }
    }
    
    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {
        // 微信扫码支付这里没有页面返回
    }

    function getJSAPI($order){
        $go_url = U('Mobile/User/order_detail',array('id'=>$order['order_id']));
        $back_url = U('Mobile/Cart/cart4',array('order_id'=>$order['order_id']));
        //①、获取用户openid
        $tools = new JsApiPay();
//        $openId = $tools->GetOpenid();
        $open_id_info = $_SESSION['openid'];
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("太羲堂订单：".$order['order_sn']);
        $input->SetAttach("weixin");
        $input->SetOut_trade_no($order['order_sn'].time());
        $input->SetTotal_fee($order['order_amount']*100);
//        $input->SetTotal_fee(1*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("tp_wx_pay");
        $input->SetNotify_url(SITE_URL.'/index.php/Mobile/Payment/notifyUrl/pay_code/weixin');
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($open_id_info['openid']);
//        echo "<pre>";
//        var_dump($input);die;
        $order2 = WxPayApi::unifiedOrder($input);

        $jsApiParameters = $tools->GetJsApiParameters($order2);
        $html = <<<EOF
	<script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',$jsApiParameters,
			function(res){
				//WeixinJSBridge.log(res.err_msg);
				 if(res.err_msg == "get_brand_wcpay_request:ok") {
				    location.href='$go_url';
				 }else{
				 	alert(res.err_code+res.err_desc+res.err_msg);
				    location.href='$back_url';
				 }
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	callpay();
	</script>
EOF;
        
    return $html;

    }

}