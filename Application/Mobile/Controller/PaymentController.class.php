<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */ 
namespace Mobile\Controller;
class PaymentController extends MobileBaseController {
    public $payment; //  具体的支付类
    public $pay_code; //  具体的支付code
    
    public function _initialize()
    {
        parent::_initialize();
        if (session('?user')) {
            $user = session('user');
            $user = M('users')->where("user_id = {$user['user_id']}")->find();
            session('user', $user);  //覆盖session 中的 user
            $this->user = $user;
            $this->user_id = $user['user_id'];
            $this->assign('user', $user); //存储用户信息
        }
        $nologin = array(
            'notify_wx','callback','notifyUrl'
        );
        if (!$this->user_id && !in_array(ACTION_NAME, $nologin)) {
            header("location:" . U('Mobile/User/login'));
            exit;
        }

    }
    
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();
        $pay_radio = $_REQUEST['pay_radio'];
        if(!empty($pay_radio)) 
        {                         
            $pay_radio = parse_url_param($pay_radio);
            $this->pay_code = $pay_radio['pay_code']; // 支付 code
        }
        else // 第三方 支付商返回
        {            
            $_GET = I('get.');            
            //file_put_contents('./a.html',$_GET,FILE_APPEND);    
            $this->pay_code = I('get.pay_code');
            unset($_GET['pay_code']); // 用完之后删除, 以免进入签名判断里面去 导致错误
        }
        //获取通知的数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        // 导入具体的支付类文件
        include_once  "plugins/payment/".$this->pay_code."/".$this->pay_code.".class.php"; 
        // D:\wamp\www\svn_tpshop\www\plugins\payment\alipay\alipayPayment.class.php
        $code = "\\".$this->pay_code; // alipay
        if(!empty($this->pay_code)){
            $this->payment = new $code();
        }
    }
    public function getPay(){
        C('TOKEN_ON',false); // 关闭 TOKEN_ON
        header("Content-type:text/html;charset=utf-8");
        $address_id = I("address_id");
        $address = M('user_address')->where("address_id = $address_id")->find();
        $order_id = I("cart_id");
        $order= M("cart")->where("id=".$order_id)->find();
        $user = session('user');
        if($order){
            if($order['pay_status']==0){
                $pay_radio = $_REQUEST['pay_radio'];
                $config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
                $payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");
                M('cart')->where("id = $order_id")->save(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code],'address_id'=>$address_id));

                //微信JS支付这种
                if($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
                    $code_str = $this->payment->getJSAPI($order);
                    exit($code_str);
                }else{
                    $code_str = $this->payment->get_code($order,$config_value);
                    exit($code_str);
                }
            }else{
                $this->error('订单已完成支付!');
            }
        }else{
            $this->error('提交失败,参数有误!');
        }
        $this->assign('code_str', $code_str);
        $this->assign('order_id', $order_id);
        $this->display('recharge'); //分跳转 和不 跳转
    }
    
//    public function getPay(){
//        C('TOKEN_ON',false); // 关闭 TOKEN_ON
//        header("Content-type:text/html;charset=utf-8");
//        $address_id = I("address_id");
//        $address = M('user_address')->where("address_id = $address_id")->find();
//        $order_id = I("cart_id");
//        $order= M("cart")->where("id=".$order_id)->find();
//        $user = session('user');
//        if($order){
//            if($order['pay_status']==0){
//                $pay_radio = $_REQUEST['pay_radio'];
//                $config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
//                $payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");
//                M('cart')->where("id = $order_id")->save(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code],'address_id'=>$address_id));
//                //微信JS支付
//                // if($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
//                if($this->pay_code == 'weixin'){
//                    $xx = 'https://mmmmmmmpay.022wjyxy.cn/api/create';//网关地址
//                $data=[
//                    'client_id'=>'16ce52ccbbc1eb86b8c730a2dfb46d20',
//                    'out_trade_no'=>$order['order_sn'],
//                    'total_fee'=>$order['total_amount'],
//                    'callback_url'=>$_SERVER['HTTP_ORIGIN'].'/index.php?m=Mobile&c=Payment&a=callback',
//                    'notify_url'=>$_SERVER['HTTP_ORIGIN'].'/index.php?m=Mobile&c=Payment&a=notify',
//                ];
//                ksort($data);//数组排序
//                $str = '';
//                foreach ($data as $k => $v) {
//                    //生成键值字串
//                    $str = $str . $k . $v;
//                }
//                $str = $str .'&key=29e2125e60a2a4ddbae355c32102bbdf16caf6b421da6c87a5deb5c9cdee0a8a';
//                $data['sign']=strtoupper(md5($str));
//                $ch = curl_init();
//                $res=strstr($xx,'https');
//                if($res){
//                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//
//                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//                }
//
//                //post https
//                curl_setopt($ch, CURLOPT_URL, $xx);
//                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//                curl_setopt($ch, CURLOPT_POST, 1);
//                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
//                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
//                $result = curl_exec($ch);
//
//                curl_close($ch);
//                $result=json_decode($result);
//                if($result->code==0&&$result->msg=="success"){
//                    header('Location: '.$result->data->pay_url);
//                }else{
//                    $this->error('支付失败!');
//                }
//                    // $code_str = $this->payment->getJSAPI($order);
//                    // exit($code_str);
//                }else{
////                    $code_str = $this->payment->get_code($order,$config_value);
//                    //在线支付 个人免签
//                    if($this->pay_code=='alipayMobile'){
//                        $this->redirect('Mobile/Paysapi/pay',array('order_sn'=>$order['order_sn'],'payment_type'=>'alipay'));
//                    }else{
//                        $this->redirect('Mobile/Paysapi/pay',array('order_sn'=>$order['order_sn'],'payment_type'=>'weixin'));
//                    }
//                    exit($code_str);
//                }
//            }else{
//                $this->error('订单已完成支付!');
//            }
//        }else{
//            $this->error('提交失败,参数有误!');
//        }
//        $this->assign('code_str', $code_str);
//        $this->assign('order_id', $order_id);
//        $this->display('recharge'); //分跳转 和不 跳转
//    }
    
    
    //异步
    //商品支付回调
    public function notify(){
        $data=file_get_contents('php://input');
        $data='{"pay_status":1,"total_fee":"100.00","order_sn":"PPX202201131812423","sign":"7880AA579B6B0A033C49491D9CB630AC"}';
        $data = json_decode($data,true);
        // $data=json_decode($data,true);PPX202201131806315
        // file_put_contents('./pay.txt',print_r($data,true).PHP_EOL,FILE_APPEND);
        $status=$data['pay_status'];
        $total_fee=$data['total_fee'];
        $order_sn=$data['order_sn'];
        if(!empty($order_sn)&&$status==1){
            $cart['pay_status'] = 2;
            $cart['pay_time'] = time();
            $res = M('cart')->where(['order_sn'=>$order_sn])->save($cart);
            if($res){
                $order= M("cart")->where(['order_sn'=>$order_sn])->find();
                //更新有效会员
                $is_yx = M("users")->where(['user_id'=>$order['user_id']])->getField("is_yx");
                if($is_yx==0){
                    M("users")->where(['user_id'=>$order['user_id']])->save(array('is_yx'=>1));
                }
                //执行拼团操作
                pintuan($order);
            }
        }
        echo "SUCCESS";
    }
    

    //商品支付回调
    public function callback(){
        header('Location: '.$_SERVER['HTTP_ORIGIN'].'/Mobile/User/recharge_log');
        $data=file_get_contents('php://input');
        $data = json_decode($data,true);
        // parse_str($data,$res);
        // $data=json_decode($data,true);
        $status=$data['pay_status'];
        $total_fee=$data['total_fee'];
        $order_sn=$data['order_sn'];
        if(!empty($order_sn)&&$status==1){
           $data['pay_status'] = 2;
            $data['pay_time'] = time();
            $res = M('cart')->where("order_sn =".$order_sn)->save($data);
            if($res){
                $order= M("cart")->where("order_sn=".$order_sn)->find();
                //更新有效会员
                $is_yx = M("users")->where("user_id=".$order['user_id'])->getField("is_yx");
                if($is_yx==0){
                    M("users")->where("user_id=".$order['user_id'])->save(array('is_yx'=>1));
                }
                //执行拼团操作
                pintuan($order);
                echo "SUCCESS";
            }
        }
    }
    //账户充值
    public function getRecharge(){
        header("Content-type:text/html;charset=utf-8");
        $user = session('user');
        $account = I("account");
        $data['order_sn'] = 'user'.date('YmdHis').rand(1,9);
        $data['user_id'] = $user['user_id'];
        $data['nickname'] = $user['nickname'];
        $data['account'] = $account;
        $data['ctime'] = time();
        $order_id = M('recharge')->add($data);
        // 修改订单的支付方式
        $payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");
        M('recharge')->where("order_id = $order_id")->save(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));
        $order = M('recharge')->where("order_id = $order_id")->find();
        if($order['pay_status'] == 1){
            $this->error('此订单，已完成支付!');
        }
        $order['total_amount'] = $order['account'];
        //订单支付提交
        $pay_radio = $_REQUEST['pay_radio'];
        $config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
        //微信JS支付
        if($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $code_str = $this->payment->getJSAPI($order);
            exit($code_str);
        }else{
            $code_str = $this->payment->get_code($order,$config_value);
            exit($code_str);
        }
        $this->assign('code_str', $code_str);
        $this->assign('order_id', $order_id);
        $this->display('payment');  // 分跳转 和不 跳转
    }
    /**
     * 提交支付方式
     */
    public function getCode(){
            C('TOKEN_ON',false); // 关闭 TOKEN_ON
            header("Content-type:text/html;charset=utf-8");            
            $order_id = I('order_id'); // 订单id

            // 修改订单的支付方式
            $payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");

            M('order')->where("order_id = $order_id")->save(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));           
            $order = M('order')->where("order_id = $order_id")->find();

            if($order['pay_status'] == 1){
            	$this->error('此订单，已完成支付!');
            }
            //订单支付提交
            $pay_radio = $_REQUEST['pay_radio'];
            $config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
            //微信JS支付
           if($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
               $code_str = $this->payment->getJSAPI($order);
               exit($code_str);
           }else{
           		$code_str = $this->payment->get_code($order,$config_value);
                exit($code_str);
           }

            $this->assign('code_str', $code_str);
            $this->assign('order_id', $order_id);
            $this->display('payment');  // 分跳转 和不 跳转
    }
    //团长升级-支付
    public function get_level(){
        header("Content-type:text/html;charset=utf-8");

        $order_id = I('order_id'); // 订单id
        // 修改订单的支付方式
        $payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");
        M('order')->where("order_id = $order_id")->save(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));
        $order = M('order')->where("order_id = $order_id")->find();
        if($order['pay_status'] == 1){
            $this->error('此订单，已完成支付!');
        }
        //订单支付提交
        $pay_radio = $_REQUEST['pay_radio'];
        $config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
        //微信JS支付
        if($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $code_str = $this->payment->getJSAPI($order);
            exit($code_str);
        }else{
            $code_str = $this->payment->get_code($order,$config_value);
            exit($code_str);
        }
        $this->assign('code_str', $code_str);
        $this->assign('order_id', $order_id);
        $this->display('payment');  // 分跳转 和不 跳转
    }

    //会员升级
    public function updateLevel(){
        header("Content-type:text/html;charset=utf-8");
        $order_id = I('order_id');
        $user = session('user');
        $openType = I("openType");
        $open_level = M("user_level")->where("level =".$openType)->find();
        $data['account'] = $open_level['money'];
        $data['show_account'] = $open_level['show_money'];
        if($order_id>0){
            M('update_level')->where(array('order_id'=>$order_id,'user_id'=>$user['user_id']))->save($data);
        }else{
            $data['order_sn'] = 'LEL'.date('YmdHis').rand(1,9);
            $data['user_id'] = $user['user_id'];
            $data['nickname'] = $user['nickname'];
            $data['level'] = $open_level['level'];
            $data['add_time'] = time();
            $data['end_time'] = time()+$open_level['days']*86400;
            $order_id = M('update_level')->add($data);
        }
        if($order_id ){
            $order = M('update_level')->where("order_id = $order_id")->find();
            if(is_array($order) && $order['pay_status']==0){
                $order['order_amount'] = $order['account'];
                $pay_radio = $_REQUEST['pay_radio'];
                $config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
                $payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");
                M('update_level')->where("order_id = $order_id")->save(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));
                //微信JS支付
                if($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
                    $code_str = $this->payment->getJSAPI($order);
                    exit($code_str);
                }else{
                    //在线支付 个人免签
                    if($this->pay_code=='alipayMobile'){
                        $this->redirect('Mobile/Codeapi/pay',array('order_sn'=>$order['order_sn'],'payment_type'=>'alipay'));
                    }else{
                        $this->redirect('Mobile/Codeapi/pay',array('order_sn'=>$order['order_sn'],'payment_type'=>'weixin'));
                    }
                }
            }else{
                $this->error('此升级代理订单，已完成支付!');
            }
        }else{
            $this->error('提交失败,参数有误!');
        }
        $this->assign('code_str', $code_str);
        $this->assign('order_id', $order_id);
        $this->display('recharge'); //分跳转 和不 跳转
    }
        // 服务器点对点
        public function notifyUrl(){
            $this->payment->response();
            exit();
        }

        // 页面跳转
        public function returnUrl(){
            $order_sn = $out_trade_no = $_GET['out_trade_no']; //商户订单号
            $trade_no = $_GET['trade_no']; //支付宝交易号
            $trade_status = $_GET['trade_status']; //交易状态
            $result = $this->payment->respond2();
            if(stripos($result['order_sn'],'PPX') !== false)
            {
                $order = M('home_order')->where("order_sn = '{$result['order_sn']}'")->find();
                $this->assign('order', $order);
                if($result)
                    $this->display('ppx_success');
                else
                    $this->display('ppx_error');
                exit();
            }elseif (stripos($result['order_sn'],'LEL') !== false){
                $order = M('update_level')->where("order_sn = '{$result['order_sn']}'")->find();
                $order['level_name'] = M("user_level")->where("level=".$order['level'])->getField("level_name");
                $order['money'] = M("user_level")->where("level=".$order['level'])->getField("money");
                $this->assign('order', $order);
                if($result['status'] == 1)
                    $this->display('level_success');
                else
                    $this->display('level_error');
                exit();
            }elseif (stripos($result['order_sn'],'user') !== false){
                $order = M('recharge')->where("order_sn = '{$result['order_sn']}'")->find();
                $this->assign('order', $order);
                if($order)
                    $this->display('recharge_success');
                else
                    $this->display('recharge_error');
                exit();
            }else{
                $order = M('order')->where("order_sn = '{$result['order_sn']}'")->find();
                $this->assign('order', $order);
                if($result['status'] == 1)
                    $this->display('success');
                else
                    $this->display('error');
            }

        }
    // 服务器点对点
    public function notify_wx(){
        //获取接口数据，如果$_REQUEST拿不到数据，则使用file_get_contents函数获取
        $data = file_get_contents("php://input");
        libxml_disable_entity_loader(true); //禁止引用外部xml实体
        $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);//XML转数组
        $post_data = (array)$xml;
//        file_put_contents('./notify_url.log',print_r($data."zhangziran",true),FILE_APPEND);
        $order_sn = isset($post_data['out_trade_no']) && !empty($post_data['out_trade_no']) ? $post_data['out_trade_no'] : 0;
        if($post_data['return_code'] == 'SUCCESS' && $post_data['result_code'] == 'SUCCESS'){
//            if(stripos($order_sn,'user') !== false){  //充值订单
//                // 如果这笔订单已经处理过了
//                $count = M('recharge')->where("order_sn = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
//                if($count == 0){
//                    return false;
//                }
//                // 修改支付状态
//                M('recharge')->where("order_sn = '$order_sn'")->save(array('pay_status'=>1,'pay_time'=>time()));
//                //更新状态
//                $order = M('recharge')->where("order_sn = '$order_sn'")->find();
//                //更新余额
//                M("users")->where("user_id=".$order['user_id'])->setInc("user_money",$order['account']);
//                upd_money($order['user_id'],$order['account'],1,"会员充值".$order['account']."元",1);
//            }
            update_pay_status($order_sn);
        }
        $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        echo $str;
    }
    // 服务器点对点
    public function wx_jsApi(){
        //获取接口数据，如果$_REQUEST拿不到数据，则使用file_get_contents函数获取
        $data = file_get_contents("php://input");
        libxml_disable_entity_loader(true); //禁止引用外部xml实体
        $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);//XML转数组
        $post_data = (array)$xml;
        file_put_contents('./notify_url.log',print_r($data."zhangziran",true),FILE_APPEND);
        $order_sn = isset($post_data['out_trade_no']) && !empty($post_data['out_trade_no']) ? $post_data['out_trade_no'] : 0;

        if($post_data['return_code'] == 'SUCCESS' && $post_data['result_code'] == 'SUCCESS'){
            update_pay_status($order_sn);
        }
        $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        echo $str;
    }
    public function wx_jsApi_return(){
        //微信JS支付
        if($this->pay_code == 'weixin'){
            $order_sn  = I("order_sn");
            if(stripos($order_sn,'PPX') !== false){
                $order = M('home_order')->where("order_sn = '{$order_sn}'")->find();
                $order['total_amount'] = M("home")->where("id=".$order['home_id'])->getField("pt_price");
                $this->assign('order', $order);
                if($order){
                    $this->display('ppx_success');
                }else{
                    $this->display('ppx_error');
                }
                exit();
            }elseif (stripos($order_sn,'user') !== false){
                $order = M('recharge')->where("order_sn = '{$order_sn}'")->find();
                $this->assign('order', $order);
                if($order)
                    $this->display('recharge_success');
                else
                    $this->display('recharge_error');
                exit();
            }elseif (stripos($order_sn,'LEL') !== false){
                $order = M('update_level')->where("order_sn = '{$order_sn}'")->find();
                $order['level_name'] = M("user_level")->where("level=".$order['level'])->getField("level_name");
                $order['money'] = M("user_level")->where("level=".$order['level'])->getField("money");
                $this->assign('order', $order);
                if($order)
                    $this->display('level_success');
                else
                    $this->display('level_error');
                exit();
            }
        }
    }
}
