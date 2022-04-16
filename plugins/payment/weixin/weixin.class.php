
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
        $notify_url = SITE_URL.'/index.php/Mobile/Payment/notify_wx/pay_code/weixin'; // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $wechatAppPay = new \wechatAppPay( WxPayConfig::$appid, WxPayConfig::$mchid, $notify_url,WxPayConfig::$key);
        //网页支付
        $wxData = array(
            'body' => '商城订单',
            'out_trade_no' => $order['order_sn'],
            'total_fee' => intval($order['total_amount']*100),
            // 'total_fee' => intval(1),
            'trade_type' => 'MWEB',
            'scene_info' => '{"h5_info": {"type":"Wap","wap_url":"http://pt.hbzhuojing.com/","wap_name": "商城订单"}}',
        );
        $result = $wechatAppPay->unifiedOrder($wxData);
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] === 'SUCCESS') {
            $redirect_url = urlencode(SITE_URL.'/index.php/Mobile/Payment/wx_jsApi_return/pay_code/weixin/order_sn/'.$order['order_sn']);
            $url = $result['mweb_url'].'&redirect_url=' . $redirect_url;
            header('Location:' . $url);
        }
    }
    /**
     * 服务器点对点响应操作给支付接口方调用
     *
     */
    function response1()
    {
        require_once("example/notify.php");
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }
    /**
     * 服务器点对点响应操作给支付接口方调用
     *微信H5异步回调
     */
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
        $str="code:".$result_code."<br>订单号：".$out_trade_no;
//        file_put_contents('./notify_url.log',print_r($xml,true),FILE_APPEND);
        if(strlen($out_trade_no) > 18){
            $order_sn = substr($out_trade_no,0,18);
        }
        if($result_code=='SUCCESS'){
            update_pay_status($order_sn); // 修改订单支付状态
        }
    }
    /**
     * 页面跳转响应操作给支付接口方调用
     */
    function respond2()
    {
        //获取接口数据，如果$_REQUEST拿不到数据，则使用file_get_contents函数获取
        $data = file_get_contents("php://input");

        libxml_disable_entity_loader(true); //禁止引用外部xml实体
        $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);//XML转数组
        $post_data = (array)$xml;
        file_put_contents('show_url.log', $post_data, FILE_APPEND);
        $order_sn = isset($post_data['out_trade_no']) && !empty($post_data['out_trade_no']) ? $post_data['out_trade_no'] : 0;
        if(strlen($order_sn) > 18){
            $order_sn = substr($order_sn,0,18);
        }
        if($post_data['return_code'] == 'SUCCESS' && $post_data['result_code'] == 'SUCCESS'){
            return array('status'=>1,'order_sn'=>$order_sn);//跳转至成功页面
        }else{
            return array('status'=>0,'order_sn'=>$order_sn); //跳转至失败页面
        }
    }

    function getJSAPI($order){
        $go_url = SITE_URL.'/index.php/Mobile/Payment/wx_jsApi_return/pay_code/weixin/order_sn/'.$order['order_sn'];
        $back_url = SITE_URL."/index.php/Mobile/Cart/cart2";
        $notify_url = SITE_URL.'/index.php/Mobile/Payment/wx_jsApi/pay_code/weixin';
        //①、获取用户openid
        $tools = new JsApiPay();
        $openId = $_SESSION['openid'];
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("支付订单：".$order['order_sn']);
        $input->SetAttach("weixin");
        $input->SetOut_trade_no($order['order_sn'].time());
//        $input->SetTotal_fee($order['total_amount']*100);
        $input->SetTotal_fee(1);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("text_wx_pay");
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($_SESSION['openid']);
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