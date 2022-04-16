<?php
namespace Mobile\Controller;

use Think\Controller;

/**
 * 个人免签
 */
class CodeController extends MobileBaseController{

    const CLIENT_ID = "c7193191f1457bcf51fa5a19e54bd881";//"此处填写商户client-ID";
    const CLIENT_SECRET = "6f05a5ceea87a92dfae621d2fe21094770df46874d32f5c370ac3e5ed519d1b9";//"此处填写秘钥";
    const POST_URL = "https://mmmmmmmpay.022wjyxy.cn/api/create";//"此处填写秘钥";
    //会员升级
    public function updateLevel(){
        $user = session('user');
        $openType = I("open_type");
        $open_level = M("user_level")->where("level =".$openType)->find();
        if($user['level'] == $open_level['level']){
            $this->ajaxReturn(array('status'=>300,'msg'=>"已开通该等级会员!"));
        }
        if($user['level'] > $open_level['level']){
            $this->ajaxReturn(array('status'=>300,'msg'=>"已开通更高等级会员!"));
        }
        $upd_level = M('update_level')->where("level =".$openType." and user_id=".$user['user_id'])->find();
        if(!$upd_level){
            $pay_radio = $_REQUEST['pay_type'];
            if($pay_radio=='pay_code=weixin'){
                $data['pay_name'] = '微信支付';
                $data['pay_code'] = 'weixin';
            }else{
                $data['pay_name'] = '支付宝支付';
                $data['pay_code'] = 'alipayMobile';
            }
            $data['account'] = $open_level['money'];
            $data['show_account'] = $open_level['show_money'];
            $data['order_sn'] = 'LEL'.date('YmdHis').rand(1,9);
            $data['user_id'] = $user['user_id'];
            $data['nickname'] = $user['nickname'];
            $data['level'] = $open_level['level'];
            $data['add_time'] = time();
            $data['end_time'] = time()+$open_level['days']*86400;
            $order_id = M('update_level')->add($data);
        }else{
            $order_id = $upd_level['order_id'];
        }
        if($order_id){
            $order = M('update_level')->where("order_id = $order_id")->find();
            if($order['pay_status']==0){
                $res = $this->pay($order['order_sn']);
                echo "<pre>";
                var_dump($res);die;
                $this->ajaxReturn(array('status'=>200,'msg'=>json_encode($res)));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"订单已支付!"));
            }
        }else{
            $this->ajaxReturn(array('status'=>300,'msg'=>"提交失败,参数有误!"));
        }
    }
    public function pay($order_sn){
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
        $return_url = SITE_URL.'/index.php/Mobile/Payment/returnUrl/pay_code/weixin';
        $out_trade_no= $order['order_sn'];
        $total_fee = $order['total_amount'];
//        $price = 0.1;
        $sign = $this->sign(array('client_id'=>self::CLIENT_ID,'total_fee'=>$total_fee,'out_trade_no'=>$out_trade_no),self::CLIENT_SECRET);
        $data = array(
            'client_id'=>self::CLIENT_ID,
            'total_fee'=>$total_fee,
            'out_trade_no'=>$out_trade_no,
            'callback_url'=>$return_url,
            'notify_url'=>$notify_url,
            'sign'=>$sign,
        );
        return $data;
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
        $str .= "&key=".$key;
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
        //        echo "<pre>";
//        var_dump($data);die;
        $this->display();
    }
    /**
     * return_url接收页面
     */
    public function queryOrder($order_sn){
        $url = "https://mmmmmmmpay.022wjyxy.cn/api/query/$order_sn";
        $res = $this->send_post($url, $data='');
        return $res;
    }
    /**
     * notify_url接收页面
     */
    public function paysapi_notify(){
        $pay_status = $_POST["pay_status"];
        $total_fee = $_POST["total_fee"];
        $order_sn = $_POST["order_sn"];
        $sign = $_POST["sign"];
        //校验传入的参数是否格式正确，略
        if($pay_status==1){
            update_pay_status($order_sn);
        }
        return "SUCCESS";
    }
}