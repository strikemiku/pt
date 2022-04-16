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
class CartController extends MobileBaseController {
    
    public $cartLogic; // 购物车逻辑操作类    
    public $user_id = 0;
    public $user = array();        
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();                
        $this->cartLogic = new \Home\Logic\CartLogic();                 
        if(session('?user'))
        {
        	$user = session('user');
                $user = M('users')->where("user_id = {$user['user_id']}")->find();
                session('user',$user);  //覆盖session 中的 user               			                
        	$this->user = $user;
        	$this->user_id = $user['user_id'];
        	$this->assign('user',$user); //存储用户信息
                // 给用户计算会员价 登录前后不一样
                if($user){
                    $user[discount] = (empty($user[discount])) ? 1 : $user[discount];
                    M('Cart')->execute("update `__PREFIX__cart` set member_goods_price = goods_price * {$user[discount]} where (user_id ={$user[user_id]} or session_id = '{$this->session_id}') and prom_type = 0");
                }                 
         }            
    }
    
    public function cart(){
        $this->display('cart');
    }
    /**
     * 将商品加入购物车
     */
    function addCart()
    {
        if($this->user_id == 0)
            $this->error('请先登陆',U('Mobile/User/login'));
        $goods_id = I("goods_id"); // 商品id
        $goods_num = I("goods_num");// 商品数量
        $goods_spec = I("goods_spec"); // 商品规格                
        $goods_spec = json_decode($goods_spec,true); //app 端 json 形式传输过来
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        $user_id = I("user_id",0); // 用户id        
        $result = $this->cartLogic->addCart($goods_id, $goods_num, $goods_spec,$unique_id,$user_id); // 将商品加入购物车
        exit(json_encode($result)); 
    }
    /**
     * ajax 将商品加入购物车
     */
    function ajaxAddCart()
    {
        if($this->user_id == 0){
            $this->ajaxReturn(array('status'=>300,'msg'=>'未登录，请先登录!'));
        }
        $goods_id = I("goods_id"); // 商品id
        $goods_num = I("goods_num");// 商品数量
        $type = I("type"); //1:快速拼团 2开团
        $home_code = I("home_code");
        if($home_code != '0'){
            $homeInfo = M("home")->where("home_code='{$home_code}'")->find();
            $groupNum = M("group_num")->where("num=".$homeInfo['pt_num'])->find();
            if(!$homeInfo){
                $this->ajaxReturn(array('status'=>300,'msg'=>'该团不存在!'));
            }
            $count = M("home_order")->where("home_id=".$homeInfo['id']." and user_id=".$this->user_id)->count();
            if($count>0){
                $this->ajaxReturn(array('status'=>300,'msg'=>'您已参加拼团!'));
            }
            if($homeInfo['status']==2){
                $this->ajaxReturn(array('status'=>300,'msg'=>'该团已结束!'));
            }
            if($homeInfo['status']==3){
                $this->ajaxReturn(array('status'=>300,'msg'=>'该团已解散!'));
            }
            if($homeInfo['sy_num']==0){
                $this->ajaxReturn(array('status'=>300,'msg'=>'该团人数已满!'));
            }
        }
        $result = $this->cartLogic->addCart($goods_id, $goods_num,$home_code,$this->session_id,$this->user_id,$type); // 将商品加入购物车
        exit(json_encode($result));
    }

    /*
     * 请求获取购物车列表
     */
    public function cartList()
    {
        $cart_form_data = $_POST["cart_form_data"]; // goods_num 购物车商品数量
        $cart_form_data = json_decode($cart_form_data,true); //app 端 json 形式传输过来

        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        $user_id = I("user_id"); // 用户id
        $where = " session_id = '$unique_id' "; // 默认按照 $unique_id 查询
        $user_id && $where = " user_id = ".$user_id; // 如果这个用户已经等了则按照用户id查询
        $cartList = M('Cart')->where($where)->getField("id,goods_num,selected");

        if($cart_form_data)
        {
            // 修改购物车数量 和勾选状态
            foreach($cart_form_data as $key => $val)
            {
                $data['goods_num'] = $val['goodsNum'];
                $data['selected'] = $val['selected'];
                $cartID = $val['cartID'];
                if(($cartList[$cartID]['goods_num'] != $data['goods_num']) || ($cartList[$cartID]['selected'] != $data['selected']))
                    M('Cart')->where("id = $cartID")->save($data);
            }
        }
        $result = $this->cartLogic->cartList($this->user, $unique_id,0);
        exit(json_encode($result));
    }

    /**
     * 购物车第二步确定页面
     */
    public function cart2()
    {
        if($this->user_id == 0){
            $this->error('请先登陆',U('Mobile/User/login'));
        }
        $address_id = I('address_id');
        if($address_id){
            $address = M('user_address')->where("address_id = $address_id")->find();
        }else{
            $address = M('user_address')->where("user_id = $this->user_id and is_default=1")->find();
        }
        if(empty($address)){
        	header("Location: ".U('Mobile/User/add_address',array('source'=>'cart2')));
        }else{
        	$this->assign('address',$address);
        }
        $result = $this->cartLogic->cartList($this->user, $this->session_id,1,1); // 获取购物车商品
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList',$payment);
        $this->assign('user',$this->user);
        $this->assign('cartList', $result['cartList']); // 购物车的商品
        $this->assign('total_price', $result['total_price']); // 总计
        $this->assign('cut_price',$result['cut_price']);
        if($_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $this->assign('is_wx',1);
        }else{
            $this->assign('is_wx',2);
        }
        $this->display();
    }
    // author :凌寒 2019年4月24日11:36:38 余额购买
    public function cart5(){
        $goods_id = I("id");
        if($this->user_id == 0) $this->error('请先登陆!!!',U('Mobile/User/login'));
        $user = M("users")->where("user_id=".$this->user_id)->find();

        $goods = M('Goods')->where("goods_id = $goods_id")->find();

        if(empty($goods)){
            $this->error('此商品不存在或者已下架');
        }
        $this->assign('user',$user);
        $this->assign('goods',$goods);
        $this->display();
    }
    public function tijiao(){

        if(IS_AJAX){
            $goods_id = I("goods_id");
            $user = M("users")->where("user_id=".$this->user_id)->find();
            $goods = M('Goods')->where("goods_id = $goods_id")->find();

            $config = getConfig();
            $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
            if($goods['market_price']>$user['user_money']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"余额不足!"));
            }

            $mairu_count = M("mairu")->where("add_time > '{$beginToday}' and user_id=".$user['user_id']." and pay_status = 1")->count('id');

            $mairu_num = $config['basic_mairu_num'];

            if(intval($mairu_num) <= intval($mairu_count)){
                $this->ajaxReturn(array('status'=>300,'msg'=>"每日只能买入".$mairu_num."单"));
            }
            $last_mairu =M("mairu")->where("user_id=".$user['user_id']." and pay_status = 1")->order("id desc")->find();
            //下单次数
            if($last_mairu){
                $data['num'] =$last_mairu['num']+1;
            }else{
                $data['num']=1;
            }
            $data['order_sn'] = "MR".date('YmdHis').rand(1000,9999); // 订单编号
            $data['user_id'] =$user['user_id'];
            $data['goods_id'] =$goods['goods_id'];
            $data['goods_price'] =$goods['market_price'];
            $data['goods_num'] =1;
            $data['mairu_money'] =$goods['market_price'];
            $data['maichu_money'] =1820;
            $data['order_status'] =0; //未匹配
            $data['pay_status'] =1; //已支付
            $data['pay_name'] ="余额支付";
            $data['add_time'] =time();
            $id = M('mairu')->add($data);
            //更新用户余额
            $res = M("users")->where("user_id=".$user['user_id'])->setDec("user_money",$data['mairu_money']);
            upd_money($user['user_id'],$data['mairu_money'],0,"会员买入产品",5);
            if(!$last_mairu){  //第一次下单更新有效会员
                M("users")->where("user_id=".$user['user_id'])->save(array('is_yx'=>1));
            }
            if($id && $res){
                $this->ajaxReturn(array('status'=>200,'msg'=>"支付成功"));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"支付失败"));
            }
        }
    }

    /**
     * ajax 将商品加入购物车
     */
    function ajaxAddCarts()
    {
        if($this->user_id == 0){
            $this->error('请先登陆',U('Mobile/User/login'));
        }
        $goods_id = I("goods_id"); // 商品id
        $goods_num = I("goods_num");// 商品数量
        $goods_spec = I("goods_spec"); // 商品规格
        $result = $this->cartLogic->add_cart($goods_id, $goods_num, $goods_spec,$this->session_id,$this->user_id); // 将商品加入购物车
        exit(json_encode($result));
    }
    public function cartOrder()
    {
        if($this->user_id == 0){
            $this->error('请先登陆',U('Mobile/User/login'));
        }
        $address_id = I('address_id');
        if($address_id){
            $address = M('user_address')->where("address_id = $address_id")->find();
        }else{
            $address = M('user_address')->where("user_id = $this->user_id and is_default=1")->find();
        }
        if(empty($address)){
            header("Location: ".U('Mobile/User/add_address',array('source'=>'cartOrder')));
        }else{
            $address['p_name'] = M("region")->where("id=".$address['province'])->getField("name");
            $address['c_name'] = M("region")->where("id=".$address['city'])->getField("name");
            $address['d_name'] = M("region")->where("id=".$address['district'])->getField("name");
            $this->assign('address',$address);
        }

        $result = $this->cartLogic->cartLists($this->user, $this->session_id,1,1); // 获取购物车商品
//        echo "<pre>";
//        var_dump($result);die;
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList',$payment);
        $this->assign('user',$this->user);
        $this->assign('cartList', $result['cartList']); // 购物车的商品
        $this->assign('total_price', $result['total_price']); // 总计
        $this->assign('cut_price',$result['cut_price']);
        $this->assign('is_show',$result['total_price']['is_show']);
        $this->display();
    }
    /**
     * ajax 获取订单商品价格 或者提交 订单
     */
    public function jisuan(){

        if($this->user_id == 0){
            exit(json_encode(array('status'=>-100,'msg'=>"登录超时请重新登录!",'result'=>null))); // 返回结果状态
        }
        $address_id = I("address_id"); //  收货地址id
        $shipping_code =  I("shipping_code"); //  物流编号
        $invoice_title = I('invoice_title'); // 发票
        $couponTypeSelect =  1; //  优惠券类型  1 下拉框选择优惠券 2 输入框输入优惠券代码
        $coupon_id =  I("q_id"); //  优惠券id
        $couponCode =  I("couponCode"); //  优惠券代码
        $pay_points =  I("pay_points",0); //  使用积分
        $user_money =  I("user_money",0); //  使用余额
        $user_money = $user_money ? $user_money : 0;

        $result = $this->cartLogic->cartLists($this->user, $this->session_id,1,1); // 获取购物车商品
        $quan = array();
        if($coupon_id>0){
            $quan = M("user_quan")->where("quan_id=".$_POST['q_id'])->order("id asc")->find();
            $res['couponFee'] = $quan['price'];
            $res['payables'] =$result['total_price']['total_fee']-$quan['price'];
            if( $res['payables']<0){
                $this->ajaxReturn(array('status'=>-1,'msg'=>'无法使用此购物券'));
            }
        }else{
            $res['couponFee'] = 0;
            $res['payables'] =$result['total_price']['total_fee'];
        }
        $this->ajaxReturn(array('status'=>1,'msg'=>'计算成功','result'=>$res));
    }
    //订单支付
    public function payOrder(){
        if(IS_AJAX){
            $config = getConfig();
            $user = M("users")->where("user_id=".$this->user_id)->find();
            $cart_id = I("cart_id");
            $cart= M("carts")->where("id=".$cart_id)->find();
            $goods = M("goods")->where("goods_id=".$cart['goods_id'])->find();
            $pay_type = I("pay_type");
            if($pay_type=='pay_code=cod'){
                if($cart['total_amount']>$user['user_money']){
                    $this->ajaxReturn(array('status'=>300,'msg'=>'余额不足!'));
                }
            }else{
                if($cart['total_amount']>$user['pay_points']){
                    $this->ajaxReturn(array('status'=>300,'msg'=>'积分不足!'));
                }
            }
            $model = M();
            $model->startTrans();  // 开启事务
            $address_id = I("address_id");
            $address = M('user_address')->where("address_id = $address_id")->find();
            //生成订单
            $order_id = $this->createOrder($address,$cart,$goods);
            $order = M("order")->where("order_id=".$order_id)->find();
            if($pay_type=='pay_code=cod'){
                //更新余额
                M("users")->where("user_id=".$order['user_id'])->setDec("user_money",$order['total_amount']);
                upd_money($order['user_id'],$order['total_amount'],0,"余额支付",5);
                $payInfo['pay_code'] = 'cod';
                $payInfo['pay_name'] = '余额支付';
                $payInfo['pay_time'] =time();
                $payInfo['pay_status'] =1;
            }else{
                //更新积分
                M("users")->where("user_id=".$order['user_id'])->setDec("pay_points",$order['total_amount']);
                upd_jifen($order['user_id'],$order['total_amount'],0,"积分支付",6);
                $payInfo['pay_code'] = 'jifen';
                $payInfo['pay_name'] = '积分支付';
                $payInfo['pay_time'] =time();
                $payInfo['pay_status'] =1;
            }
            //更新状态
            $payResoult=M("order")->where("order_id=".$order_id)->save($payInfo);
            if($order_id && $payResoult){
                // 减少对应商品的库存
                minus_stock($order_id);
                $model->commit();  //提交
                $this->ajaxReturn(array('status'=>200,'msg'=>'支付成功','order_id'=>$order['order_id']));
            }else{
                $model->rollback();  // 回滚
                $this->ajaxReturn(array('status'=>300,'msg'=>'支付失败!'));
            }
        }
    }
    //生成订单
    public function createOrder($address,$cart,$goods){

        $data = array(
            'order_sn'         => date('YmdHis').rand(1,9).rand(1,9).rand(1,9).rand(1,9), // 订单编号
            'user_id'          =>$cart['user_id'], // 用户id
            'goods_id'         =>$cart['goods_id'],
            'type'         =>1,
            'consignee'        =>$address['consignee'], // 收货人
            'province'         =>$address['province'],//'省份id',
            'city'             =>$address['city'],//'城市id',
            'district'         =>$address['district'],//'县',
            'twon'             =>$address['twon'],// '街道',
            'address'          =>$address['address'],//'详细地址',
            'mobile'           =>$address['mobile'],//'手机',
            'zipcode'          =>$address['zipcode'],//'邮编',
            'email'            =>$address['email'],//'邮箱',
            'coupon_price'     =>0,//'使用优惠券',
            'shipping_code'    =>'',//'物流编号',
            'shipping_name'    =>'', //'物流名称',
            'invoice_title'    =>'', //'发票抬头',
            'goods_price'      =>$cart['total_amount'],//'商品价格',
            'total_amount'     =>$cart['total_amount'],// 订单总额
            'order_amount'     =>$cart['total_amount'] ,//'应付款金额',
            'add_time'         =>time(), // 下单时间
        );
        $order_id = M("Order")->data($data)->add();
        // 订单操作日志
        logOrder($order_id,'订单提交，请等待系统确认','提交订单',$cart['user_id']);
        $data2['order_id']           = $order_id; // 订单id
        $data2['goods_id']           = $goods['goods_id']; // 商品id
        $data2['goods_name']         = $goods['goods_name']; // 商品名称
        $data2['goods_sn']           = $goods['goods_sn']; // 商品货号
        $data2['goods_num']          = $cart['goods_num']; // 购买数量
        $data2['market_price']       = $goods['market_price']; // 市场价
        $data2['goods_price']        = $cart['goods_price']; // 商品价
        $data2['spec_key']           = ''; // 商品规格
        $data2['spec_key_name']      = ''; // 商品规格名称
        $data2['sku']           	 = ''; // 商品sku
        $data2['member_goods_price'] = $cart['member_goods_price']; // 会员折扣价
        $data2['cost_price']         = $goods['cost_price']; // 成本价
        $orderGoodsId = M("OrderGoods")->data($data2)->add();
        return $order_id;
    }
    //分销商品支付成功跳转
    public function success(){
        $order_id = I("order_id");
        $order = M("order")->where("order_id=".$order_id)->find();
        $goods =  M("goods")->where("goods_id=".$order['goods_id'])->find();
        $this->assign('order',$order);
        $this->display();
    }
    /**
     * ajax 获取订单商品价格 或者提交 订单
     */
    public function cart3(){
                                
        if($this->user_id == 0){
            exit(json_encode(array('status'=>-100,'msg'=>"登录超时请重新登录!",'result'=>null))); // 返回结果状态
        }
        $address_id = I("address_id"); //  收货地址id
        $shipping_code =  I("shipping_code"); //  物流编号        
        $invoice_title = I('invoice_title'); // 发票
        $couponTypeSelect =  I("couponTypeSelect"); //  优惠券类型  1 下拉框选择优惠券 2 输入框输入优惠券代码
        $coupon_id =  I("coupon_id"); //  优惠券id
        $couponCode =  I("couponCode"); //  优惠券代码
        $pay_points =  I("pay_points",0); //  使用积分
        $user_money =  I("user_money",0); //  使用余额        
        $user_money = $user_money ? $user_money : 0;

        if($this->cartLogic->cart_count($this->user_id,1) == 0 ) exit(json_encode(array('status'=>300,'msg'=>'订单参数错误','result'=>null))); // 返回结果状态
        if(!$address_id) exit(json_encode(array('status'=>-3,'msg'=>'请先填写收货人信息','result'=>null))); // 返回结果状态
		$address = M('UserAddress')->where("address_id = $address_id")->find();
		$order_goods = M('cart')->where("user_id = {$this->user_id} and selected = 1")->select();

        $result = calculate_price($this->user_id,$order_goods,$shipping_code,0,$address[province],$address[city],$address[district],$pay_points,$user_money,$coupon_id,$couponCode);

		if($result['status'] < 0){
            exit(json_encode($result));
        }
        $car_price = array(
            'postFee'      => $result['result']['shipping_price'], // 物流费
            'couponFee'    => $result['result']['coupon_price'], // 优惠券            
            'balance'      => $result['result']['user_money'], // 使用用户余额
            'pointsFee'    => $result['result']['integral_money'], // 应付积分
            'payables'     => $result['result']['order_amount'], // 应付金额
            'goodsFee'     => $result['result']['goods_price'],// 商品价格
            'order_prom_id' => $result['result']['order_prom_id'], // 订单优惠活动id
            'order_prom_amount' => $result['result']['cut_fee'], // 优惠了多少钱
        );

        // 提交订单        
        if($_REQUEST['act'] == 'submit_order')
        {
            $cart_goods = M('cart')->where("user_id = {$this->user_id} and selected = 1")->find();
            $result = $this->cartLogic->addOrder($this->user_id,$address_id,$shipping_code,$invoice_title,$coupon_id,$car_price,$cart_goods); // 添加订单
            exit(json_encode($result));            
        }else{
            $return_arr = array('status'=>1,'msg'=>'计算成功','result'=>$car_price); // 返回结果状态
            exit(json_encode($return_arr));
        }
    }
    /*
    * 订单余额支付
    */
    public function cart_cod(){
        $user = M("users")->where("user_id=".$this->user_id)->find();
        $config = getConfig();
        if(IS_AJAX){
            $user = $this->user;
            $address_id = I("address_id");
            $address = M('user_address')->where("address_id = $address_id")->find();
            $order_id = I("order_id");
            $order= M("cart")->where("id=".$order_id)->find();
            if($order['total_amount']>$user['user_money']){
                $this->ajaxReturn(array('status'=>300,'msg'=>'余额不足!'));
            }
            $data['pay_name'] = '余额支付';
            $data['pay_code'] = 'cod';
            $data['pay_status'] = 1;
            $data['pay_time'] = time();
            $data['address_id'] = $address['address_id'];
            $res = M('cart')->where("id =".$order['id'])->save($data);
            if($res){
                //更新有效会员
                $is_yx = M("users")->where("user_id=".$order['user_id'])->getField("is_yx");
                if($is_yx==0){
                    M("users")->where("user_id=".$order['user_id'])->save(array('is_yx'=>1));
                }
                $info = M('cart')->where("id =".$order['id'])->find();
                //更新余额
                M("users")->where("user_id=".$order['user_id'])->setDec("user_money",$order['total_amount']);
                upd_money($order['user_id'],$order['total_amount'],0,"余额支付",5);
                //执行拼团操作
                pintuan($info);
                $this->ajaxReturn(array('status'=>200,'msg'=>'支付成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'支付失败!'));
            }
        }
    }
    /*
    * 订单积分支付
    */
    public function cart_jifen(){
        $user = M("users")->where("user_id=".$this->user_id)->find();
        $config = getConfig();
        if(IS_AJAX){
            $user = $this->user;
            $address_id = I("address_id");
            $address = M('user_address')->where("address_id = $address_id")->find();
            $order_id = I("order_id");
            $order= M("cart")->where("id=".$order_id)->find();
            if($order['total_amount']>$user['pay_points']){
                $this->ajaxReturn(array('status'=>300,'msg'=>'积分不足!'));
            }
            $data['pay_name'] = '积分支付';
            $data['pay_code'] = 'jifen';
            $data['pay_status'] = 1;
            $data['pay_time'] = time();
            $data['address_id'] = $address['address_id'];
            $res = M('cart')->where("id =".$order['id'])->save($data);
            if($res){
                //更新有效会员
                $is_yx = M("users")->where("user_id=".$order['user_id'])->getField("is_yx");
                if($is_yx==0){
                    M("users")->where("user_id=".$order['user_id'])->save(array('is_yx'=>1));
                }
                $info = M('cart')->where("id =".$order['id'])->find();
                //更新余额
                M("users")->where("user_id=".$order['user_id'])->setDec("pay_points",$order['total_amount']);
                upd_jifen($order['user_id'],$order['total_amount'],0,"积分支付",6);
                //执行拼团操作
                pintuan($info);
                $this->ajaxReturn(array('status'=>200,'msg'=>'支付成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'支付失败!'));
            }
        }
    }
    /*
    * 订单支付页面
    */
    public function cart4(){
        $order_id = I('order_id');
        $order = M('Order')->where("order_id = $order_id")->find();
        $user = M("users")->where("user_id=".$this->user_id)->find();
        $config = getConfig();
        if(IS_AJAX){
            $user = $this->user;
            $img = I("dakuan_img");
            if($img ==''){
                $this->ajaxReturn(array('status'=>300,'msg'=>'请上传打款截图'));
            }
            //安全密码
            if($user['twopassword']!=md5(I('twopwd'))){
                $this->ajaxReturn(array('status'=>300,'msg'=>'安全密码不正确'));
            }
            $pay_type = I("type");
            if($pay_type==1){
                $data['pay_code'] = "alipayMobile";
                $data['pay_name'] = "支付宝支付";
            }elseif ($pay_type==2){
                $data['pay_code'] = "weixin";
                $data['pay_name'] = "微信支付";
            }
            $data['pay_status'] = 1;
            $data['pay_time'] = time();
            $res = M('Order')->where("order_id = $order_id")->save($data);
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>'支付成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'支付失败!'));
            }
        }
        $order_goods = M("order_goods")->where("order_id = ".$order['order_id'])->field("goods_id")->select();
        $is_recommend = 0;
        foreach ($order_goods as $k=>$v){
            $goods = M("goods")->where("goods_id =".$v['goods_id'])->field("goods_id,is_recommend")->find();
            if($goods['is_recommend']==1){
                $is_recommend = 1;
            }
        }
        $this->assign('is_recommend',$is_recommend);
        $this->assign('order',$order);
        $this->assign('config',$config);
        $this->assign('user',$user);
        $this->display();
    }
    /*
     * 订单支付页面
     */
    public function cart4_copy(){

        $order_id = I('order_id');
        $order = M('Order')->where("order_id = $order_id")->find();

        // 如果已经支付过的订单直接到订单详情页面. 不再进入支付页面
        if($order['pay_status'] == 1){
            $order_detail_url = U("Mobile/User/order_detail",array('id'=>$order_id));
            header("Location: $order_detail_url");
        }

        $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and  scene in(0,1)")->select();        
//        //微信浏览器
//        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
//            $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and code in('weixin','cod')")->select();
//        }
        $paymentList = convert_arr_key($paymentList, 'code');
//        echo"<pre>";
//        var_dump($paymentList);die;
        foreach($paymentList as $key => $val)
        {
            $val['config_value'] = unserialize($val['config_value']);
            if($val['config_value']['is_bank'] == 2)
            {
                $bankCodeList[$val['code']] = unserialize($val['bank_code']);
            }
        }
        $user = M('users')->where(array('user_id'=>$_COOKIE['user_id']))->find();
        $this->assign('user',$user);

        $bank_img = include 'Application/Home/Conf/bank.php'; // 银行对应图片
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList',$paymentList);
        $this->assign('bank_img',$bank_img);
        $this->assign('order',$order);
        $this->assign('bankCodeList',$bankCodeList);
        $this->assign('pay_date',date('Y-m-d H:s', strtotime("+1 day")));
        $this->display("cart4_copy");
    }


    /*
    * ajax 请求获取购物车列表
    */
    public function ajaxCartList()
    {
        $post_goods_num = I("goods_num"); // goods_num 购物车商品数量
        $post_cart_select = I("cart_select"); // 购物车选中状态
        $where = " session_id = '$this->session_id' "; // 默认按照 session_id 查询
        $this->user_id && $where = " user_id = ".$this->user_id; // 如果这个用户已经等了则按照用户id查询

        $cartList = M('Cart')->where($where)->getField("id,goods_num,selected,prom_type,prom_id"); 

        if($post_goods_num)
        {
            // 修改购物车数量 和勾选状态
            foreach($post_goods_num as $key => $val)
            {                
                $data['goods_num'] = $val < 1 ? 1 : $val;
                if($cartList[$key]['prom_type'] == 1) //限时抢购 不能超过购买数量
                {
                    $flash_sale = M('flash_sale')->where("id = {$cartList[$key]['prom_id']}")->find();
                    $data['goods_num'] = $data['goods_num'] > $flash_sale['buy_limit'] ? $flash_sale['buy_limit'] : $data['goods_num'];
                }
                
                $data['selected'] = $post_cart_select[$key] ? 1 : 0 ;
                if(($cartList[$key]['goods_num'] != $data['goods_num']) || ($cartList[$key]['selected'] != $data['selected']))
                    M('Cart')->where("id = $key")->save($data);
            }
            $this->assign('select_all', $_POST['select_all']); // 全选框
        }

        $result = $this->cartLogic->cartList($this->user, $this->session_id,1,1);        
        if(empty($result['total_price']))
            $result['total_price'] = Array( 'total_fee' =>0, 'cut_fee' =>0, 'num' => 0, 'atotal_fee' =>0, 'acut_fee' =>0, 'anum' => 0);
        $this->assign('cartList', $result['cartList']); // 购物车的商品                
        $this->assign('total_price', $result['total_price']); // 总计       
        $this->display('ajax_cart_list');
    }

    /*
 * ajax 获取用户收货地址 用于购物车确认订单页面
 */
    public function ajaxAddress(){

        $regionList = M('Region')->getField('id,name');

        $address_list = M('UserAddress')->where("user_id = {$this->user_id}")->select();
        $c = M('UserAddress')->where("user_id = {$this->user_id} and is_default = 1")->count(); // 看看有没默认收货地址
        if((count($address_list) > 0) && ($c == 0)) // 如果没有设置默认收货地址, 则第一条设置为默认收货地址
            $address_list[0]['is_default'] = 1;

        $this->assign('regionList', $regionList);
        $this->assign('address_list', $address_list);
        $this->display('ajax_address');
    }

    /**
     * ajax 删除购物车的商品
     */
    public function ajaxDelCart()
    {
        $ids = I("ids"); // 商品 ids
        $result = M("Cart")->where(" id in ($ids)")->delete(); // 删除id为5的用户数据
        $return_arr = array('status'=>1,'msg'=>'删除成功','result'=>''); // 返回结果状态
        exit(json_encode($return_arr));
    }
    //申请团队--加购物车
    function add_cart()
    {
        if($this->user_id == 0)
            $this->error('请先登陆',U('Mobile/User/login'));
        $goods_id = I("goods_id"); // 商品id
        $goods_num = I("goods_num");// 商品数量
        $goods_spec = I("goods_spec"); // 商品规格
        $result = $this->cartLogic->add_cart($goods_id, $goods_num, $goods_spec,$this->session_id,$this->user_id); // 将商品加入购物车
        exit(json_encode($result));
    }
    //寄售区--加购物车
    function add_sell_cart()
    {
        if($this->user_id == 0){
            $this->error('请先登陆',U('Mobile/User/login'));
        }
        $goods_id = I("goods_id"); // 商品id
        $goods_num = I("goods_num");// 商品数量
        $goods_spec = I("goods_spec"); // 商品规格
        $result = $this->cartLogic->add_sell_cart($goods_id, $goods_num, $goods_spec,$this->session_id,$this->user_id); // 将商品加入购物车
        exit(json_encode($result));
    }
    /**
     * ajax 获取订单商品价格 或者提交 订单
     */
    public function cart_price(){

        if($this->user_id == 0)
            exit(json_encode(array('status'=>-100,'msg'=>"登录超时请重新登录!",'result'=>null))); // 返回结果状态

        $address_id = I("address_id"); //  收货地址id
        $shipping_code =  I("shipping_code"); //  物流编号
        $invoice_title = I('invoice_title'); // 发票
        $couponTypeSelect =  I("couponTypeSelect"); //  优惠券类型  1 下拉框选择优惠券 2 输入框输入优惠券代码
        $coupon_id =  I("coupon_id"); //  优惠券id
        $couponCode =  I("couponCode"); //  优惠券代码
        $pay_points =  I("pay_points",0); //  使用积分
        $user_money =  I("user_money",0); //  使用余额
        $user_money = $user_money ? $user_money : 0;

        if($this->cartLogic->cart_count($this->user_id,1) == 0 ) exit(json_encode(array('status'=>-2,'msg'=>'你的购物车没有选中商品','result'=>null))); // 返回结果状态
        if(!$address_id) exit(json_encode(array('status'=>-3,'msg'=>'请先填写收货人信息','result'=>null))); // 返回结果状态
        $address = M('UserAddress')->where("address_id = $address_id")->find();
        $order_goods = M('cart')->where("user_id = {$this->user_id} and selected = 1")->select();

        $result = calculate_price($this->user_id,$order_goods,$shipping_code,0,$address[province],$address[city],$address[district],$pay_points,$user_money,$coupon_id,$couponCode);

        if($result['status'] < 0)
            exit(json_encode($result));
        $car_price = array(
            'postFee'      => $result['result']['shipping_price'], // 物流费
            'couponFee'    => $result['result']['coupon_price'], // 优惠券
            'balance'      => $result['result']['user_money'], // 使用用户余额
            'pointsFee'    => $result['result']['integral_money'], // 应付积分
            'payables'     => $result['result']['order_amount'], // 应付金额
            'goodsFee'     => $result['result']['goods_price'],// 商品价格
            'order_prom_id' => $result['result']['order_prom_id'], // 订单优惠活动id
            'order_prom_amount' => $result['result']['cut_fee'], // 优惠了多少钱
        );

        // 提交订单
        if($_REQUEST['act'] == 'submit_order')
        {
            $result = $this->cartLogic->addLevelOrder($this->user_id,$address_id,$shipping_code,$invoice_title,$coupon_id,$car_price); // 添加订单
            exit(json_encode($result));
        }
        $return_arr = array('status'=>1,'msg'=>'计算成功','result'=>$car_price); // 返回结果状态
        exit(json_encode($return_arr));
    }
    /**
     * //申请团队--订单页面
     */
    public function cart6()
    {
        if($this->user_id == 0)
            $this->error('请先登陆',U('Mobile/User/login'));
        $address_id = I('address_id');
        if($address_id){
            $address = M('user_address')->where("address_id = $address_id")->find();
        }else{
            $address = M('user_address')->where("user_id = $this->user_id and is_default=1")->find();
        }
        if(empty($address)){
            header("Location: ".U('Mobile/User/add_address',array('source'=>'cart6')));
        }else{
            $this->assign('address',$address);
        }
        if($this->cartLogic->cart_count($this->user_id,1) == 0 )
            $this->error ('你的购物车没有选中商品','Cart/cart');

        $result = $this->cartLogic->cartList($this->user, $this->session_id,1,1); // 获取购物车商品

        $shippingList = M('Plugin')->where("`type` = 'shipping' and status = 1")->select();// 物流公司
        $this->assign('user',$this->user);
        $this->assign('shippingList', $shippingList); // 物流公司
        $this->assign('cartList', $result['cartList']); // 购物车的商品
        $this->assign('total_price', $result['total_price']); // 总计
        $this->assign('cut_price',$result['cut_price']);
        $this->display();
    }
    public function cart7(){

        $order_id = I('order_id');
        $order = M('Order')->where("order_id = $order_id")->find();
        // 如果已经支付过的订单直接到订单详情页面. 不再进入支付页面
        if($order['pay_status'] == 1){
            $order_detail_url = U("Mobile/User/order_detail",array('id'=>$order_id));
            header("Location: $order_detail_url");
        }

        $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and  scene in(0,1)")->select();

        //微信浏览器
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and code in('weixin','cod')")->select();
        }
        $paymentList = convert_arr_key($paymentList, 'code');

        foreach($paymentList as $key => $val)
        {
            $val['config_value'] = unserialize($val['config_value']);
            if($val['config_value']['is_bank'] == 2)
            {
                $bankCodeList[$val['code']] = unserialize($val['bank_code']);
            }
        }
//        echo "<pre>";
//        var_dump($paymentList);die;
        $bank_img = include 'Application/Home/Conf/bank.php'; // 银行对应图片
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList',$paymentList);
        $this->assign('bank_img',$bank_img);
        $this->assign('order',$order);
        $this->assign('bankCodeList',$bankCodeList);
        $this->assign('pay_date',date('Y-m-d', strtotime("+1 day")));
        $this->display();
    }
    /**
     * 商品寄售--订单页面
     */
    public function cart8()
    {
        if($this->user_id == 0){
            $this->error('请先登陆',U('Mobile/User/login'));
        }
        $maichu_id = I("id");
        $info = M("maichu")->where("id=".$maichu_id)->find();
        if(empty($info)){
            $this->error('此商品不存在或者已下架');
        }
        $address_id = I('address_id');
        if($address_id){
            $address = M('user_address')->where("address_id = $address_id")->find();
        }else{
            $address = M('user_address')->where("user_id = $this->user_id and is_default=1")->find();
        }
        if(empty($address)){
            header("Location: ".U('Mobile/User/add_address',array('source'=>'cart8','maichu_id'=>$maichu_id)));
        }else{
            $this->assign('address',$address);
        }
        $info['goods'] = M('Goods')->where("goods_id = ".$info['goods_id'])->find();
        $shippingList = M('Plugin')->where("`type` = 'shipping' and status = 1")->select();// 物流公司
        $this->assign('user',$this->user);
        $this->assign('shippingList', $shippingList); // 物流公司
        $this->assign('info', $info); // 购物车的商品
        $this->assign('total_price',$info['maichu_money']*$info['goods_num']); // 总计
        $this->assign('cut_price',$info['maichu_money']*$info['goods_num']);
        $this->display();
    }

    /**
     * ajax 获取订单商品价格 或者提交 订单
     */
    public function cart8_price(){

        if($this->user_id == 0){
            exit(json_encode(array('status'=>-100,'msg'=>"登录超时请重新登录!",'result'=>null))); // 返回结果状态
        }
        $address_id = I("address_id"); //  收货地址id
        if(!$address_id){
            exit(json_encode(array('status'=>-3,'msg'=>'请先填写收货人信息','result'=>null))); // 返回结果状态
        }
        $address = M('UserAddress')->where("address_id = $address_id")->find();
        $maichu_id = I("id");
        if(!$maichu_id){
            exit(json_encode(array('status'=>-100,'msg'=>"参数错误",'result'=>null))); // 返回结果状态
        }
        $info = M("maichu")->where("id=".$maichu_id)->find();
        $total_price = $info['maichu_money']*$info['goods_num'];

        $data = array(
            'order_sn'         => 'recharge'.date('YmdHis').rand(1000,9999), // 订单编号
            'user_id'          =>$this->user_id, // 用户id
            'type'             =>'2', //卖出订单
            'sell_id'          =>$info['id'], // 卖出id
            'sell_user_id'     =>$info['sell_user_id'], // 卖出用户id
            'consignee'        =>$address['consignee'], // 收货人
            'province'         =>$address['province'],//'省份id',
            'city'             =>$address['city'],//'城市id',
            'district'         =>$address['district'],//'县',
            'twon'             =>$address['twon'],// '街道',
            'address'          =>$address['address'],//'详细地址',
            'mobile'           =>$address['mobile'],//'手机',
            'zipcode'          =>$address['zipcode'],//'邮编',
            'email'            =>$address['email'],//'邮箱',
            'shipping_code'    =>'',//'物流编号',
            'shipping_name'    =>'', //'物流名称',
            'invoice_title'    =>'', //'发票抬头',
            'total_amount'     =>$total_price,// 订单总额
            'order_amount'     =>$total_price,//'应付款金额',
            'add_time'         =>time(), // 下单时间
        );
        $order_id = M("Order")->data($data)->add();
        if(!$order_id){
            return array('status'=>-8,'msg'=>'添加订单失败','result'=>NULL);
        }
        // 记录订单操作日志
        logOrder($order_id,'您提交了订单，请等待系统确认','提交订单',$this->user_id);

        $goods = M('goods')->where("goods_id = {$info['goods_id']} ")->find();
        $data2['order_id']           = $order_id; // 订单id
        $data2['goods_id']           = $goods['goods_id']; // 商品id
        $data2['goods_name']         = $goods['goods_name']; // 商品名称
        $data2['goods_sn']           = $goods['goods_sn']; // 商品货号
        $data2['goods_num']          = $info['goods_num']; // 购买数量
        $data2['market_price']       = $goods['shop_price']; // 市场价
        $data2['goods_price']        = $info['maichu_money']; // 商品价
        $data2['spec_key']           = ''; // 商品规格
        $data2['spec_key_name']      = ''; // 商品规格名称
        $data2['sku']           	 = ''; // 商品sku
        $data2['member_goods_price'] = ''; // 会员折扣价
        $data2['cost_price']         = ''; // 成本价
        M("OrderGoods")->data($data2)->add();
        //更改匹配状态
        M("maichu")->where("id=".$info['id'])->save(array('buy_user_id'=>$this->user_id,'order_status'=>2));
        $return_arr = array('status'=>1,'msg'=>'计算成功','order_id'=>$order_id); // 返回结果状态
        exit(json_encode($return_arr));
    }
    public function cart9(){

        $order_id = I('order_id');
        $order = M('Order')->where("order_id = $order_id")->find();
        // 如果已经支付过的订单直接到订单详情页面. 不再进入支付页面
        if($order['pay_status'] == 1){
            $order_detail_url = U("Mobile/User/order_detail",array('id'=>$order_id));
            header("Location: $order_detail_url");
        }

        $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and  scene in(0,1)")->select();

        //微信浏览器
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and code in('weixin','cod')")->select();
        }
        $paymentList = convert_arr_key($paymentList, 'code');

        foreach($paymentList as $key => $val)
        {
            $val['config_value'] = unserialize($val['config_value']);
            if($val['config_value']['is_bank'] == 2)
            {
                $bankCodeList[$val['code']] = unserialize($val['bank_code']);
            }
        }
//        echo "<pre>";
//        var_dump($paymentList);die;
        $bank_img = include 'Application/Home/Conf/bank.php'; // 银行对应图片
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList',$paymentList);
        $this->assign('bank_img',$bank_img);
        $this->assign('order',$order);
        $this->assign('bankCodeList',$bankCodeList);
        $this->assign('pay_date',date('Y-m-d', strtotime("+1 day")));
        $this->display();
    }
}
