<?php
namespace Mobile\Controller;

use Common\Model\PayModel;
use Think\Controller;

/**
 * Paysapi个人免签
 */
class PaysapiController extends Controller{

    const UID = "4e6b2131423f8c5b97f5c990";//"此处填写PaysApi的uid";
    const TOKEN = "140d1288bfc8c06e7713706bdece5d7e";//"此处填写PaysApi的Token";
    const POST_URL = "https://pay.bbbapi.com/";

    public function pay(){
        $order_sn = I('get.order_sn');
        $istype = I('get.payment_type')=='alipay' ? 1 :2;
        $order = array();
        if(stripos($order_sn,'LEL') !== false){
            $order = M('update_level')->where(array('order_sn'=>$order_sn))->find();
            $order['total_amount']=$order['account'];
            $goodsname = "充值VIP";

        }elseif(stripos($order_sn,'PPX') !== false){
            $order = M('cart')->where(array('order_sn'=>$order_sn))->find();
            $goodsname = "购买商品";
        }
        $notify_url = U('paysapi_notify','','',true);
        $return_url = U('paysapi_return','','',true);
        $orderid = $order['order_sn'];
        $orderuid = $order['user_id'];
//        $price = $order['total_amount'];
        $price = 0.1;
        $key = md5($goodsname. $istype . $notify_url . $orderid . $orderuid . $price . $return_url . self::TOKEN . self::UID);
        $data = array(
            'goodsname'=>$goodsname,
            'istype'=>$istype,
            'key'=>$key,
            'notify_url'=>$notify_url,
            'orderid'=>$orderid,
            'orderuid'=>$orderuid,
            'price'=>$price,
            'return_url'=>$return_url,
            'uid'=>self::UID
        );
        $this->assign('data',$data);
        $this->assign('post_url',self::POST_URL);
        $this->display();
    }


    /**
     * return_url接收页面
     */
    public function paysapi_return(){
        $this->display();
    }

    /**
     * notify_url接收页面
     */
    public function paysapi_notify(){
        $paysapi_id = $_POST["paysapi_id"];
        $orderid = $_POST["orderid"];
        $price = $_POST["price"];
        $realprice = $_POST["realprice"];
        $orderuid = $_POST["orderuid"];
        $key = $_POST["key"];
        //校验传入的参数是否格式正确，略

        $token = self::TOKEN;

        $temps = md5($orderid . $orderuid . $paysapi_id . $price . $realprice . $token);

        if ($temps != $key){
            return jsonError("key值不匹配");
        }else{
            update_pay_status($orderid);
        }
    }
}