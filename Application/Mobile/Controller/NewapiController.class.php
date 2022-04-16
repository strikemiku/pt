<?php
namespace Mobile\Controller;

use Think\Controller;

/**
 * 个人免签
 */
class CodeapiController extends Controller{

    const CLIENT_ID = "c7193191f1457bcf51fa5a19e54bd881";//"此处填写商户client-ID";
    const CLIENT_SECRET = "6f05a5ceea87a92dfae621d2fe21094770df46874d32f5c370ac3e5ed519d1b9";//"此处填写秘钥";

    public function pay(){
        $order_sn = I('get.order_sn');
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
        $out_trade_no= $order['order_sn'];
        $total_fee = $order['total_amount'];
//        $price = 0.1;

        $key = $this->sign(array('client_id'=>self::CLIENT_ID,'total_fee'=>$total_fee,'out_trade_no'=>$out_trade_no,'out_trade_no'=>$order_sn),self::UUID);
        $data = array(
            'code'=>self::CODE,
            'uuid'=>self::UUID,
            'amount'=>$price,
            'notice_url'=>$notify_url,
            'out_trade_no'=>$order_sn,
            'sign'=>$key,
        );
        echo "<pre>";
        var_dump($data);die;
        $res = $this->send_post(self::POST_URL, $data);

    }
    /**
     * return_url接收页面
     */
    public function queryOrder($order_sn){
        $url = "http://103.60.110.55:12222/api/openapi/query/$order_sn";
        $res = $this->send_post($url, $data='');
        return $res;
    }
    /*
    * 签名
    * */
    public function sign($data,$key){
        // 空值不参与排序
        $data = array_filter($data);
        // 字典排序
        ksort($data);
        // 以 url 键值对格式拼接参数
        $str = urldecode(http_build_query($data));
        // 拼接商户密钥。直接拼接 key 就好了
        $str .= $key;
        return strtoupper(md5($str));
    }
    //curl post请求
    function send_post($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
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