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
 * 2015-11-21
 */
namespace Mobile\Controller;

use Home\Logic\UsersLogic;
use Think\Page;
use Think\Verify;

class UserController extends MobileBaseController
{

    public $user_id = 0;
    public $user = array();

    /*
    * 初始化操作
    */
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
            'login', 'pop_login', 'do_login', 'logout', 'verify','webVerify', 'set_pwd', 'finished','send_message','check_phone','check_phones','gong',
            'verifyHandle','reg','send_sms_reg_code', 'find_pwd', 'check_validate_code',
            'forget_pwd', 'check_captcha', 'check_username', 'send_validate_code', 'express','xyDetail','notify','callback'
        );
        if (!$this->user_id && !in_array(ACTION_NAME, $nologin)) {
            header("location:" . U('Mobile/User/login'));
            exit;
        }

        $order_status_coment = array(
            'WAITPAY' => '待付款 ', //订单查询状态 待支付
            'WAITSEND' => '待发货', //订单查询状态 待发货
            'WAITRECEIVE' => '待收货', //订单查询状态 待收货
            'WAITCCOMMENT' => '待评价', //订单查询状态 待评价
        );

//        session_unset();
//        session_destroy();
//        setcookie('cn', '', time() - 3600, '/');
//        setcookie('user_id', '', time() - 3600, '/');
//        header("Location:" . U('Mobile/Index/index'));
        $this->assign('order_status_coment', $order_status_coment);
    }

    /*
     * 用户中心首页
     */
    public function index()
    {
        $user = $this->user;
        if($user['pid']){
            $user['tj_mobile'] = M("users")->where(array('user_id'=>$user['pid']))->getField('mobile');
        }
        $user['doPin'] = M("home_order")->where("user_id=".$user['user_id']." and status=1")->count();
        $user['winPin'] = M("home_order")->where("user_id=".$user['user_id']." and status=2")->count();
        $user['falsePin'] = M("home_order")->where("user_id=".$user['user_id']." and status=4")->count();
        $level_name = M('user_level')->where("level = ".$this->user['level'])->getField('level_name');
        $this->assign('user',$user);
        $this->assign('level_name', $level_name);
        $this->display();
    }
    //拼团
    public function pt_order(){
        $user = $this->user;
        $status = I("status")?I("status"):1;
        $where['user_id'] = $user['user_id'];
        $where['status'] = $status;
        $list = M("home_order")->where($where)->order("id desc")->select();
        if($list){
            foreach ($list as $kk=>$vv){
                $list[$kk]['market_price'] =  M("goods")->where("goods_id=".$vv['goods_id'])->getField("market_price");
                $list[$kk]['goods_name'] =  M("goods")->where("goods_id=".$vv['goods_id'])->getField("goods_name");
                $list[$kk]['img'] =  M("goods")->where("goods_id=".$vv['goods_id'])->getField("original_img");
                $list[$kk]['pt_price'] =  M("home")->where("id=".$vv['home_id'])->getField("pt_price");
                $list[$kk]['home_code'] = M("home")->where("id=".$vv['home_id'])->getField("home_code");
            }
        }
        $this->assign('status',$status);
        $this->assign('list',$list);
        $this->display();
    }
    //签到
    public function sign(){
        $config = getConfig();
        $user= $this->user;
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $days = date("t");
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));
        if(IS_POST){
            $info = M("user_sign")->where("user_id=".$user['user_id']." and year='{$year}' and month='{$month}' and day='{$day}'")->find();
            if($info){
                $this->ajaxReturn(array('status'=>300,'msg'=>'今日已签到 !'));
            }
            $data['user_id'] = $user['user_id'];
            $data['nickname'] = $user['nickname'];
            $data['mobile'] = $user['mobile'];
            $data['year'] = $year;
            $data['month'] = $month;
            $data['day'] = $day;
            $data['type'] = 1;  //1签到
            $data['add_time'] = time();
            $id = M("user_sign")->add($data);
            if($id){
                if($day<=10){
                    $award = $config['basic_sign_10_inc_num'];
                }elseif($day>10 && $day <=20){
                    $whr['user_id'] = $user['user_id'];
                    $whr['year'] = $year;
                    $whr['month'] = $month;
                    $whr['day'] = array('between',array(1,10));
                    $count = M("user_sign")->where($whr)->count();
                    if($count>=10){
                        $award = $config['basic_sign_20_inc_num'];
                    }else{
                        $award = $config['basic_sign_10_inc_num'];
                    }
                }elseif($day>20 && $day <=31){
                    $whr['user_id'] = $user['user_id'];
                    $whr['year'] = $year;
                    $whr['month'] = $month;
                    $whr['day'] = array('between',array(1,10));
                    $count = M("user_sign")->where($whr)->count();
                    if($count>=10){
                        $map['user_id'] = $user['user_id'];
                        $map['year'] = $year;
                        $map['month'] = $month;
                        $map['day'] = array('between',array(10,20));
                        $ten_count = M("user_sign")->where($map)->count();
                        if($ten_count>=10){
                            $award = $config['basic_sign_30_inc_num'];
                        }else{
                            $award = $config['basic_sign_20_inc_num'];
                        }
                    }else{
                        $award = $config['basic_sign_10_inc_num'];
                    }
                }
                if($award>0){
                    M("user_sign")->where("id=".$id)->save(array('active_num'=>$award));
                    M("users")->where("user_id=".$user['user_id'])->setInc("active_num",$award);
                    upd_active($user['user_id'],$award,1,"签到奖励".$award."活跃度",1);
                }
                $this->ajaxReturn(array('status'=>200,'msg'=>"签到成功，奖励".$award."活跃度"));
            }
        }
        $arr = array();
        for($i=1;$i<=$days;$i++){
            $arr[$i]['day'] = $i;
            $arr[$i]['desc'] = $i;
            $signInfo = M("user_sign")->where("user_id=".$user['user_id']." and year='{$year}' and month='{$month}' and day='{$i}'")->find();
            if($signInfo){
                $arr[$i]['is_sign'] = 1;
            }else{
                $arr[$i]['is_sign'] = 0;
            }
        }
        $article = M("article")->where("cat_id=8 and is_open=1")->find();
        $article['date'] = $year."年".$month."月";
        $article['days'] = $arr;
        $article['active_num']=M("user_active")->where("type=1 and user_id=".$user['user_id'])->sum("money");
        if(!$article['active_num']){
            $article['active_num']='0.00';
        }
        $article['is_sign'] = M("user_sign")->where("user_id=".$user['user_id']." and year='{$year}' and month='{$month}' and day='{$day}'")->count();
        $article['content']=html_entity_decode($article['content']);
        $this->assign('info',$article);
        $this->assign('config',$config);
        $this->display();
    }
    //抽奖活动
    public function game(){
        $user = $this->user;
        $config = getConfig();
        $user['chou_num'] = $config['basic_chou_num']-$user['chou_num'];
        //中奖名单
        $list = M("game_log")->where("1=1")->order("id desc")->select();
        if($list){
            foreach ($list as $kk=>$vv){
                $list[$kk]['nickname'] = M("users")->where("user_id=".$vv['user_id'])->getField("nickname");
                if($vv['user_id']){
                    $list[$kk]['head_pic'] = M("users")->where("user_id=".$vv['user_id'])->getField("head_pic");
                }else{
                    $list[$kk]['head_pic'] = "";
                }
            }
        }
        //转盘列表
        $pan = M("game")->where("1=1")->order("price desc")->select();
        if(IS_AJAX){
            $active_num = $config['basic_active_num'];
            $luck_id = I("luck_id");
            if($user['active_num']<$active_num){
                $this->ajaxReturn(array('status'=>300,'msg'=>'活跃度不足'));
            }
            $index = $luck_id-1;
            $award = $pan[$index]['price'];
            if($award>0){
                $data['user_id'] = $user['user_id'];
                $data['type'] =1;
                $data['award_type'] =1;
                $data['award'] =$award;
                $data['add_time'] = time();
                $id = M('game_log')->add($data);
                if($id) {
                    M("users")->where("user_id=".$this->user_id)->setInc('chou_num',1);
                    //扣除活跃度
                    M("users")->where("user_id=".$user['user_id'])->setDec("active_num",$active_num);
                    upd_active($user['user_id'],$active_num,0,"抽奖扣除".$active_num."活跃度",3);
                    M("users")->where("user_id=".$user['user_id'])->setInc("user_money",$award);
                    upd_money($user['user_id'],$award,1,"抽奖奖励".$award."红包",7);
                    $this->ajaxReturn(array('status'=>200));
                } else {
                    $this->ajaxReturn(array('status'=>300,'msg'=>'错误了,请重试'));
                }
            }else{
                $this->ajaxReturn(array('status'=>200));
            }
        }
        //抽奖规则
        $detail = M("article")->where('is_open=1 and cat_id=10')->find();
        $detail['publish_time'] = date("Y-m-d ",$detail['publish_time']);
        $detail['content'] = htmlspecialchars_decode( $detail['content']);
        $this->assign('panList',$pan);
        $this->assign('chouList',$list);
        $this->assign('detail',$detail);
        $this->assign('config',$config);
        $this->assign('user',$user);
        $this->display();
    }
    //抽奖记录
    public function game_log(){
        $user = $this->user;
        $list = M("game_log")->where("type=1 and user_id=".$user['user_id'])->order("id desc")->select();
        if($list){
            foreach ($list as $kk=>$vv){
                if($vv['user_id']){
                    $list[$kk]['head_pic'] = M("users")->where("user_id=".$vv['user_id'])->getField("head_pic");
                }else{
                    $list[$kk]['head_pic'] = "";
                }
            }
        }
        $this->assign('list',$list);
        $this->display();
    }
    /*
     * 检测今日幸运大转盘次数
     * */
    public function check_luck_num()
    {
        if(IS_AJAX){
            $config = getConfig();
            $user = M("users")->where("user_id=".$this->user_id)->find();
            $user['chou_num'] = $config['basic_chou_num']-$user['chou_num'];
            $this->ajaxReturn(array('status'=>200,'chou_num'=>$user['chou_num']));
        }
    }
    //优享会员
    public function updLevel(){
        $user = $this->user;
        $user['level_name'] = M("user_level")->where("level=".$user['level'])->getField("level_name");
        $config = getConfig();
        if(IS_POST){
            $open_type = I("open_type");
            $open_level = M("user_level")->where("level =".$open_type)->find();
            if($user['level'] == $open_level['level']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"已开通该等级会员!"));
            }
            if($user['level'] > $open_level['level']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"已开通更高等级会员!"));
            }
            $pay_type = I("pay_type");
            if($pay_type=='pay_code=cod'){
                if($user['user_money']<$open_level['money']){
                    $this->ajaxReturn(array('status'=>300,'msg'=>"余额不足!"));
                }
                $data['pay_name'] = '余额支付';
                $data['pay_code'] = 'cod';
            }else{
                if($user['pay_points']<$open_level['money']){
                    $this->ajaxReturn(array('status'=>300,'msg'=>"积分不足!"));
                }
                $data['pay_name'] = '积分支付';
                $data['pay_code'] = 'jifen';
            }
            $data['order_sn'] = 'LEL'.date('YmdHis').rand(1,9);
            $data['user_id'] = $user['user_id'];
            $data['nickname'] = $user['nickname'];
            $data['level'] = $open_level['level'];
            $data['show_account'] = $open_level['show_money'];
            $data['account'] = $open_level['money'];
            $data['add_time'] = time();
            $data['end_time'] = time()+$open_level['days']*86400;
            $data['pay_time'] = time();
            $data['pay_status'] = 1;
            $order_id = M('update_level')->add($data);
            if($order_id){
                //更新数量
                if($pay_type=='pay_code=cod'){
                    M("users")->where("user_id=".$user['user_id'])->setDec("user_money",$open_level['money']);
                    upd_money($user['user_id'],$open_level['money'],0,"开通".$open_level['level_name'],6);
                }else{
                    M("users")->where("user_id=".$user['user_id'])->setDec("pay_points",$open_level['money']);
                    upd_jifen($user['user_id'],$open_level['money'],0,"开通".$open_level['level_name'],7);
                }
                //更新等级
                M("users")->where("user_id=".$user['user_id'])->save(array('level'=>$open_level['level']));
                $this->ajaxReturn(array('status'=>200,'msg'=>"开通成功!"));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"开通失败!"));
            }
        }
        $level = M("user_level")->where("level >1")->order("level asc")->select();
        foreach ($level as $kk=>$vv){
            $level[$kk]['per'] = round($vv['money']/90,2);
        }
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList',$payment);
        $this->assign('level',$level);
        $this->assign('config',$config);
        $this->assign('user',$user);
        if($_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $this->assign('is_wx',1);
        }else{
            $this->assign('is_wx',2);
        }
        $this->display();
    }
    //开通会员记录
    public function level_log(){
        $user = $this->user;
        $winList = M('update_level')->where("user_id=".$user['user_id']." and pay_status=1")->order("order_id desc")->select();
        foreach ($winList as $kk=>$vv){
            $winList[$kk]['level_name'] = M("user_level")->where("level=".$vv['level'])->getField("level_name");
        }
        $falseList = M('update_level')->where("user_id=".$user['user_id']." and pay_status=0")->order("order_id desc")->select();
        foreach ($falseList as $kk=>$vv){
            $falseList[$kk]['level_name'] = M("user_level")->where("level=".$vv['level'])->getField("level_name");
        }
        $this->assign('user',$user);
        $this->assign('winList',$winList);
        $this->assign('falseList',$falseList);
        $this->display();
    }
    // author :凌寒 2019年1月28日13:16:32 客服热线
    public function kefu(){
        $config = getConfig();
        $this->assign('config',$config);
        $this->display();
    }
    //矿机
    public function kuangji(){
        //运行中
        $do_list = M("miner")->where("status = 1 and user_id=".$this->user_id)->order('id desc')->select();
        $this->assign('doList',$do_list);
        //已完成
        $com_list = M("miner")->where("status = 2 and user_id=".$this->user_id)->order('id desc')->select();
        $this->assign('comList',$com_list);
        $this->display();
    }
    public function logout()
    {
        session_unset();
        session_destroy();
        setcookie('cn', '', time() - 3600, '/');
        setcookie('user_id', '', time() - 3600, '/');
        header("Location:" . U('Mobile/Index/index'));
    }
    //获取团长房间号
    public function get_home_code(){
        $code = I("home_code");
        $info = M("users")->where("room_num=".$code)->find();
        if(!$info){
            $this->ajaxReturn(array('status'=>300,'msg'=>"房间号不存在"));
        }else{
            if($info['user_id']==$this->user_id){
                $this->ajaxReturn(array('status'=>300,'msg'=>"房间号错误"));
            }
            $this->ajaxReturn(array('status'=>200));
        }
    }
    // author :凌寒 2019年4月24日16:33:15 交易列表
    public function jiaoyi(){
        $user = $this->user;
        $config = getConfig();
        $type=I("type");
        $time = time();
        if($type==1){ //买入明细
            $mairu_list = M("mairu")->where("user_id=".$user['user_id'])->order("id desc")->select();

            foreach ($mairu_list as $kk=>$vv){
                $goods = M("goods")->where("goods_id =".$vv['goods_id'])->field("goods_name,original_img")->find();

                $mairu_list[$kk]['goods_name'] = $goods['goods_name'];
                $mairu_list[$kk]['goods_img'] = $goods['original_img'];
                $mairu_list[$kk]['user_name'] = $user['nickname'];

                $last_time = $vv['add_time']+$config['basic_maichu_day']*24*3600;

                if($time > $last_time){
                    $mairu_list[$kk]['ke_mai'] = 1;
                }else{
                    $mairu_list[$kk]['ke_mai'] = 0;
                }
            }
//            echo "<pre>";
//            var_dump($mairu_list);die;
            $this->assign('maichu_fy',$config['basic_maichu_fy']);
            $this->assign('mairu_list',$mairu_list);
        }else if($type==2){  //卖出
            $maichu_list = M("maichu")->where("user_id=".$user['user_id'])->order("id desc")->select();
            foreach ($maichu_list as $kk=>$vv){
                $goods = M("goods")->where("goods_id =".$vv['goods_id'])->field("goods_name,original_img")->find();
                $maichu_list[$kk]['goods_name'] = $goods['goods_name'];
                $maichu_list[$kk]['goods_img'] = $goods['original_img'];
                $maichu_list[$kk]['user_name'] = $user['nickname'];
            }
            $this->assign('maichu_list',$maichu_list);
        }else if($type==3){  //交易明细
//            $maichu_list = M("match")->where("user_id=".$user['user_id'])->order("id desc")->select();
//            foreach ($maichu_list as $kk=>$vv){
//                $goods = M("goods")->where("goods_id =".$vv['goods_id'])->field("goods_name,original_img")->find();
//                $maichu_list[$kk]['goods_name'] = $goods['goods_name'];
//                $maichu_list[$kk]['goods_img'] = $goods['original_img'];
//                $maichu_list[$kk]['user_name'] = $user['nickname'];
//            }
//            $this->assign('maichu_list',$maichu_list);
        }
        $this->assign('type',$type);
        $this->display();
    }
    // author :凌寒 2019年4月25日14:08:37 匹配详情
    public function match(){
        $type = I("type");
        $id = I("id");
        if($type==1){  //买入匹配
            $match = M("match")->where("mairu_id=".$id)->find();
        }else{
            $match = M("match")->where("maichu_id=".$id)->find();
        }
        $this->assign('info',$match);
        $this->assign('type',$type);
        $this->display();
    }
    // author :凌寒 2019年4月24日17:22:00 卖出订单
    public function do_maichu(){
        $id = I("id");
        if(!$id){
            $this->ajaxReturn(array('status'=>300,'msg'=>"参数错误!"));
        }

        $info = M("mairu")->where("id=".$id)->find();

        $data['order_sn'] = "MC".date('YmdHis').rand(1000,9999); // 订单编号
        $data['user_id'] =$info['user_id'];
        $data['goods_id'] =$info['goods_id'];
        $data['goods_price'] =$info['goods_price'];
        $data['goods_num'] =1;
        $data['mairu_money'] =$info['mairu_money'];
        $data['maichu_money'] =1820;
        $data['order_status'] =0; //未匹配
        $data['pay_status'] =1; //已支付
        $data['pay_name'] ="余额支付";
        $data['add_time'] =time();
        $id = M("maichu")->add($data);
        //更新买入状态
        $is_maichu = M("mairu")->where("id=".$info['id'])->save(array('is_maichu'=>1));
        if($id && $is_maichu){
            $this->ajaxReturn(array('status'=>200,'msg'=>"卖出成功!"));
        }else{
            $this->ajaxReturn(array('status'=>300,'msg'=>"卖出失败!"));
        }
    }
    // author :凌寒 2019年4月25日14:39:38 申请提货
    public function tihuo(){
        $goods = M('goods')->where("is_tejia=1 and is_on_sale=1")->order('sort desc')->select();
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $this->assign('list',$goods);
        $this->assign('province', $p);
        $this->display();
    }
    // author :凌寒  执行提货申请
    public function do_tihuo(){
        $post = $_POST;
        $user = $this->user;
        //统计买入订单的数量
        $mairu_count = M("mairu")->where("user_id=".$user['user_id'])->order("id desc")->getField("num");
        if($mairu_count < 12){
            $this->ajaxReturn(array('status'=>300,'msg'=>"买入满12单才能申请提配!"));
        }elseif($mairu_count==12){
            $goods = M('goods')->where("goods_id=".$post['goods_id'])->find();
            $data['order_sn'] = date('YmdHis').rand(1000,9999);
            $data['user_id'] = $user['user_id'];
            $data['consignee'] = $post['consignee'];
            $data['province'] = $post['province'];
            $data['city'] = $post['city'];
            $data['district'] = $post['district'];
            $data['address'] = $post['address'];
            $data['mobile'] = $post['mobile'];
            $data['shipping_status'] = 0;
            $data['shipping_code'] = '';
            $data['shipping_name'] = '';
            $data['goods_price'] = $goods['market_price'];
            $data['pay_status'] = 1;  //支付完成
            $data['pay_code'] = 'cod';
            $data['pay_name'] = '余额支付';
            $data['pay_time'] = time();
            $data['order_amount'] = $goods['market_price'];
            $data['total_amount'] = $goods['market_price'];
            $data['add_time'] = time();
            $order_id = M("Order")->add($data);

            $data2['order_id']           = $order_id; // 订单id
            $data2['goods_id']           = $goods['goods_id']; // 商品id
            $data2['goods_name']         = $goods['goods_name']; // 商品名称
            $data2['goods_sn']           = $goods['goods_sn']; // 商品货号
            $data2['goods_num']          = 1; // 购买数量
            $data2['market_price']       = $goods['market_price']; // 市场价
            $data2['goods_price']        = $goods['market_price']; // 商品价
            $data2['cost_price']         = $goods['market_price']; // 成本价
            $data2['prom_type']          = 0; // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
            $order_goods_id = M("OrderGoods")->add($data2);
            if($order_id && $order_goods_id){
                $this->ajaxReturn(array('status'=>200,'msg'=>"申请成功!"));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"申请失败!"));
            }
        }else{  //大于12
            if($mairu_count%2 != 0){
                $this->ajaxReturn(array('status'=>300,'msg'=>"偶数买入订单才能提配!"));
            }
            $goods = M('goods')->where("goods_id=".$post['goods_id'])->find();
            $data['order_sn'] = date('YmdHis').rand(1000,9999);
            $data['user_id'] = $user['user_id'];
            $data['consignee'] = $post['consignee'];
            $data['province'] = $post['province'];
            $data['city'] = $post['city'];
            $data['district'] = $post['district'];
            $data['address'] = $post['address'];
            $data['mobile'] = $post['mobile'];
            $data['shipping_status'] = 0;
            $data['shipping_code'] = '';
            $data['shipping_name'] = '';
            $data['goods_price'] = $goods['market_price'];
            $data['pay_status'] = 1;  //支付完成
            $data['pay_code'] = 'cod';
            $data['pay_name'] = '余额支付';
            $data['pay_time'] = time();
            $data['order_amount'] = $goods['market_price'];
            $data['total_amount'] = $goods['market_price'];
            $data['add_time'] = time();
            $order_id = M("Order")->add($data);

            $data2['order_id']           = $order_id; // 订单id
            $data2['goods_id']           = $goods['goods_id']; // 商品id
            $data2['goods_name']         = $goods['goods_name']; // 商品名称
            $data2['goods_sn']           = $goods['goods_sn']; // 商品货号
            $data2['goods_num']          = 1; // 购买数量
            $data2['market_price']       = $goods['market_price']; // 市场价
            $data2['goods_price']        = $goods['market_price']; // 商品价
            $data2['cost_price']         = $goods['market_price']; // 成本价
            $data2['prom_type']          = 0; // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
            $order_goods_id = M("OrderGoods")->add($data2);
            if($order_id && $order_goods_id){
                $this->ajaxReturn(array('status'=>200,'msg'=>"申请成功!"));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"申请失败!"));
            }
        }
    }
    /*
     * 账户资金
     */
    public function account()
    {
        $user = session('user');
        //获取账户资金记录
        $logic = new UsersLogic();
        $data = $logic->get_account_log($this->user_id, I('get.type'));
        $account_log = $data['result'];

        $this->assign('user', $user);
        $this->assign('account_log', $account_log);
        $this->assign('page', $data['show']);

        if ($_GET['is_ajax']) {
            $this->display('ajax_account_list');
            exit;
        }
        $this->display();
    }

    public function coupon()
    {
        //
        $logic = new UsersLogic();
        $data = $logic->get_cocouupon($this->user_id, $_REQUEST['type']);
        $coupon_list = $data['result'];
        $this->assign('coupon_list', $coupon_list);
        $this->assign('page', $data['show']);
        if ($_GET['is_ajax']) {
            $this->display('ajax_coupon_list');
            exit;
        }
        $this->display();
    }

    /**
     *  登录
     */
    public function login()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('Mobile/User/index'));
        }
        $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U("Mobile/User/index");
        $this->assign('referurl', $referurl);
        $this->display();
    }

    //执行登录操作
    public function do_login()
    {
        if(I("type")=='admin'){
            $user_id = I("user_id");
            $user = M('users')->where("user_id=".$user_id)->find();
            if($user){
                session('user',$user);
                setcookie('user_id',$user['user_id'],null,'/');
                $this->redirect("/Mobile/User/index");
            }
        }else{
            $user_code= trim(I('post.mobile'));
            $password = trim(I('post.password'));
            if(!$user_code || !$password)
                $result= array('status'=>0,'msg'=>'请输入手机号或密码');
            $user = M('users')->where("mobile = '{$user_code}'")->find();
            if(!$user){
                $result = array('status'=>-1,'msg'=>'账号不存在!');
            }elseif($user['is_lock']==0){
                $result = array('status'=>-2,'msg'=>'账号被封禁!');
            }elseif(md5($password) != $user['password']){
                $result = array('status'=>-2,'msg'=>'密码错误!');
            }else{
                $result = array('status'=>1,'msg'=>'登陆成功','result'=>$user);
            }
            if($result['status'] == 1){
                session('user',$result['result']);
                setcookie('user_id',$result['result']['user_id'],null,'/');
            }
            exit(json_encode($result));
        }
    }
    /**
     *  注册
     */
    public function reg()
    {
        $config = getConfig();
        if (IS_POST) {
            $nickname =I('nickname');
            if($nickname==''){
                $this->ajaxReturn(array('status'=>300,'msg'=>'请输入会员姓名!'));
            }
            $mobile = I("mobile");
            if(M("users")->where("mobile = '{$mobile}'")->count()>0){
                $this->ajaxReturn(array('status'=>300,'msg'=>'手机号已注册!'));
            }
            // //检测验证码
            // if($_POST['code']!=$_SESSION['code']){
            //     $this->ajaxReturn(array('status'=>300,'msg'=>'验证码错误'));
            // }
            //推荐人编码
            $parent_info = M("users")->where(array('user_code'=>I("pcode")))->find();
            if(!$parent_info){
                $this->ajaxReturn(array('status'=>300,'msg'=>'邀请码不存在!'));
            }
            $data['user_code'] = userCode($mobile);
            $data['nickname']= I("nickname");
            $data['mobile']= $mobile;
            $data['pid']=$parent_info['user_id'];  //推荐人id
            $data['pcode']=$parent_info['user_code'];  //推荐人编号
            $data['password']=md5(I('pwd'));
            $data['twopassword']=md5(123456);
            $data['pt_num']=0;  //拼团次数
            $data['level']=1;   //注册会员
            $data['type']=0;
            $data['is_lock']=1;  //已激活
            $data['reg_time']=time();
            $id=M('users')->add($data);
            if($id){
                unset($_SESSION["code"]);
                relation($id); //记录推荐关系
//                //奖励推荐值
//                if($config['basic_recom_num']>0 && $parent_info['user_id']){
//                    M("users")->where("user_id=".$parent_info['user_id'])->setInc("recom_num",$config['basic_recom_num']);
//                    upd_recom($parent_info['user_id'],$config['basic_recom_num'],1,"推荐好友注册奖励",1,$id);
//                }
                $this->ajaxReturn(array('status'=>200,'msg'=>'注册成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'注册失败'));
            }
        }
        //注册协议
        $xy = M("article")->where('cat_id=7 and is_open=1')->find();
        $this->assign('xy',$xy);
        if($pcode = I('pcode')){
            $p_info = M("users")->where(array('user_code'=>$pcode))->field("user_id,user_code")->find();
            $this->assign('pInfo',$p_info);
        }
        $this->display();
    }
    //注册协议
    public function xyDetail(){
        $cat_id= I("cat_id");
        $detail = M("article")->where('is_open=1 and cat_id='.$cat_id)->find();
        $detail['publish_time'] = date("Y-m-d ",$detail['publish_time']);
        $detail['content'] = htmlspecialchars_decode( $detail['content']);
        $this->assign('detail',$detail);
        $this->display();
    }
    //会员开通协议
    public function openDetail(){
        $id = I("id");
        $detail = M("article")->where('article_id='.$id)->find();
        $detail['publish_time'] = date("Y-m-d ",$detail['publish_time']);
        $detail['content'] = htmlspecialchars_decode( $detail['content']);
        $this->assign('detail',$detail);
        $this->display();
    }
    //积分兑换
    public function do_duihuan()
    {
        if(IS_AJAX){
            $config = getConfig();
            $user= $this->user;
            $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            if($user['pay_points'] <= 0){
                $this->ajaxReturn(array('status'=>-1,'msg'=>'积分不足,无法兑换!'));
            }
            $map['type'] = 1;
            $map['user_id'] = $user['user_id'];
            $map['add_time'] = array('between',array($beginToday,$endToday));
            $today_dui = M("user_jifen")->where($map)->find();

            if($today_dui){
                $this->ajaxReturn(array('status'=>-1,'msg'=>'今日已领取奖励!'));
            }
            $pay_points = $user['pay_points'];
            if($user['level']==1){
                $bili = $config['basic_yh_bl']/100;
            }else{
                $bili = $config['basic_fws_bl']/100;
            }
            $award_jifen = $pay_points*$bili;
            if($award_jifen > 0){
                M("users")->where("user_id=".$user['user_id'])->setDec("pay_points",$award_jifen);
                upd_jifen($user['user_id'],$award_jifen,0,"积分转换余额",1,$user['user_id']);
                $award_money = $award_jifen/$config['basic_money_jifen'];
                M("users")->where("user_id=".$user['user_id'])->setInc("user_money",$award_money);
                upd_money($user['user_id'],$award_money,1,"积分转换余额",1,$user['user_id']);
            }
            $this->ajaxReturn(array('status'=>1,'msg'=>'积分兑换成功','result'=>$user));
        }
    }
    // author :凌寒 2018年12月11日21:39:20 友情链接
    public function link(){
        $list = M("friend_link")->where('is_show=1')->order("orderby desc")->select();
        $this->assign('list',$list);
        $this->display();
    }
    // author :凌寒 2018年12月11日21:39:20 系统公告
    public function gong(){

        $lun = M("ad")->where("pid=2 and enabled =1")->field("ad_id,ad_name,ad_code,ad_link")->order("orderby desc")->select();
        $this->assign('lun',$lun);
        $list = M("article")->where('cat_id=6 and is_open=1')->order("article_id desc")->select();
        foreach ($list as $kk=>$vv){
            $list[$kk]['publish_time'] = date("Y-m-d ",$vv['publish_time']);
        }
        $this->assign('list',$list);
        $this->display();
    }
    // author :凌寒 2018年12月11日21:47:04 公告详情
    public function gongDetail(){
        $id = I("id");
        $detail = M("article")->where('article_id='.$id)->find();
        $detail['publish_time'] = date("Y-m-d ",$detail['publish_time']);
        $detail['content'] = htmlspecialchars_decode( $detail['content']);
        //上一篇
        $pre_info = M('article')->where('cat_id='.$detail['cat_id'].' and article_id > '.$id )->order('article_id desc')->find();

        $this->assign('pre_info',$pre_info);
        //下一篇
        $next_info = M('Article')->where('cat_id='.$detail['cat_id'].' and article_id < '.$id )->order('article_id asc')->find();
        $this->assign('next_info',$next_info);

        $this->assign('detail',$detail);
        $this->display();
    }
    //会员报单
    public function baodan()
    {
        $user = $this->user;
        if (IS_POST) {
            $config = getConfig();
            //检测手机号
            $mobile = I("mobile");
            $mobile_count = M("users")->where("mobile = '{$mobile}'")->count();
            if($mobile_count > 0){
                $this->ajaxReturn(array('status'=>300,'msg'=>'该手机号已注册'));
            }
            if($user['twopassword'] != md5(I("twopwd"))){
                $this->ajaxReturn(array('status'=>300,'msg'=>'安全密码错误!'));
            }
            $data['user_id'] = $user['user_id'];
            $data['user_name']= $user['nickname'];
            $data['mobile']= $user['mobile'];
            $data['reg_user_name']=I("nickname");
            $data['reg_mobile']=I("mobile");
            $data['province']= I("province");
            $data['city']= I("city");
            $data['district']= I("district");
            $data['address']= I("address");
            $data['status']= 3;
            $data['create_time']= time();
//            echo "<pre>";
//            var_dump($data);die;
            $id=M('baodan')->add($data);
            if($id){
                $this->ajaxReturn(array('status'=>200,'msg'=>'报单申请成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'报单申请失败'));
            }
        }
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $this->assign('province', $p);
        $this->display();
    }
    // author :凌寒 报单记录
    public function baodan_log(){
        $user = $this->user;
        $log = M('baodan')->where("user_id=".$user['user_id'].'')->order("id desc")->select();
        $this->assign('log',$log);
        $this->display();
    }
    // author :凌寒 2018年11月23日08:54:17 推广海报
    public function qrcode(){
        $user= $this->user;
        $url = "http://" . $_SERVER['SERVER_NAME'] . "/index.php/Mobile/Index/index/pid/".$user['user_id'];
//        //微信浏览器
//        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') && $user['oauth']=='weixin'){
//            $url = "http://" . $_SERVER['SERVER_NAME'] . "/index.php/Mobile/Index/index/pid/".$user['user_id'];
//        }else{
//            $url = "http://" . $_SERVER['SERVER_NAME'] . "/index.php/Mobile/User/reg/pid/".$user['user_id'];
//        }
        Vendor('phpqrcode.phpqrcode');
        // 纠错级别：L、M、Q、H
        $level = 'L';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 4;
        // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
        $path = "Public/upload/qrcode/";
        // 生成的文件名
        $fileName = $path.$user['user_id'].'.png';
        //生成二维码图片
        $object = new \QRcode();
        $object->png($url, $fileName, $level, $size);
        //分享背景图
        $config = getConfig();
        $this->assign('config',$config);
        $this->assign('url',$url);
        $this->assign('fileName',$fileName);
        $this->display();
    }
    public function qrcode_home(){
        $user= $this->user;
        $url = "http://" . $_SERVER['SERVER_NAME'] . "/index.php/Mobile/User/reg/pcode/".$user['user_code'];
//        //微信浏览器
//        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') && $user['oauth']=='weixin'){
//            $url = "http://" . $_SERVER['SERVER_NAME'] . "/index.php/Mobile/Index/index/pid/".$user['user_id'];
//        }else{
//            $url = "http://" . $_SERVER['SERVER_NAME'] . "/index.php/Mobile/User/reg/pid/".$user['user_id'];
//        }
        Vendor('phpqrcode.phpqrcode');
        // 纠错级别：L、M、Q、H
        $level = 'L';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 4;
        // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
        $path = "Public/upload/qrcode/";
        // 生成的文件名
        $fileName = $path.$user['user_id'].'.png';
        //生成二维码图片
        $object = new \QRcode();
        $object->png($url, $fileName, $level, $size);
        //分享背景图
        $config = getConfig();
        $this->assign('config',$config);
        $this->assign('url',$url);
        $this->assign('fileName',$fileName);
        $this->display();
    }
    public function change_jifen(){
        if(IS_AJAX){
            $user_id = $this->user_id;
            $user_info = M('users')->where(array('user_id'=>$user_id))->find();
            $order_id = I("order_id");
            $order = M("order")->where("order_id=".$order_id)->find();
            if($order['shipping_status']==1){
                $this->ajaxReturn(array('status'=>300,'msg'=>'订单已发货'));
            }
            //更新订单状态
            $res = M("order")->where("order_id=".$order_id)->save(array("order_status"=>2));
            if($res){
                //兑换积分
                M("users")->where("user_id=".$order['user_id'])->setInc("pay_points",$order['total_amount']);
                upd_jifen($order['user_id'],$order['total_amount'],1,"商品兑换".$order['total_amount']."积分",4);
                $this->ajaxReturn(array('status'=>200,'msg'=>'兑换成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'兑换失败'));
            }
        }
        $user = $this->user;
        $this->assign('user',$user);
        $this->display();
    }
    // author :凌寒 2018年12月12日20:29:54 个人信息设置
    public function set(){
        $user = $this->user;
        $this->assign('user',$user);
        $this->display();
    }
    // author :凌寒 2018年11月21日14:42:10 修改头像
    public function change_img(){
        $user_id = $this->user_id;
        if(IS_AJAX){
            $user_info = M('users')->where(array('user_id'=>$user_id))->find();
            $head_pic = I('head_pic');
            if($user_info['head_pic']==$head_pic){
                $this->ajaxReturn(array('status'=>300,'msg'=>'没有任何修改'));
            }
            $res = M('users')->where("user_id=".$user_id)->save(array('head_pic'=>$head_pic));
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>'修改成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'修改失败'));
            }
        }
        $user = $this->user;
        $this->assign('user',$user);
        $this->display();
    }
    /*
     * 地址编辑
     */
    public function edit_address()
    {
        $id = I('id');
        $address = M('user_address')->where(array('address_id' => $id, 'user_id' => $this->user_id))->find();
        if (IS_POST) {
            $logic = new UsersLogic();
            $data = $logic->add_address($this->user_id, $id, I('post.'));
            if ($_POST['source'] == 'cart2') {
                header('Location:' . U('/Mobile/Cart/cart2', array('address_id' => $id)));
                exit;
            }elseif (I('source') == 'cart6') {
                header('Location:' . U('/Mobile/Cart/cart6', array('address_id' => $data['result'])));
                exit;
            }elseif (I('source') == 'cart8') {
                header('Location:' . U('/Mobile/Cart/cart8', array('address_id' => $data['result'],'id'=>I('maichu_id'))));
                exit;
            }else
                $this->success($data['msg'], U('/Mobile/User/address_list'));
            exit();
        }
        //获取省份
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $c = M('region')->where(array('parent_id' => $address['province'], 'level' => 2))->select();
        $d = M('region')->where(array('parent_id' => $address['city'], 'level' => 3))->select();
        if ($address['twon']) {
            $e = M('region')->where(array('parent_id' => $address['district'], 'level' => 4))->select();
            $this->assign('twon', $e);
        }

        $this->assign('province', $p);
        $this->assign('city', $c);
        $this->assign('district', $d);
        $this->assign('address', $address);
        $this->display();
    }
    /*
     * 地址编辑
     */
    public function change_address()
    {
        $user_id = $this->user_id;
        $user_info = M('users')->where(array('user_id'=>$user_id))->find();

        if (IS_POST) {
            $data['province'] = I("province");
            $data['city'] = I("city");
            $data['district'] = I("district");
            $res = M('users')->where("user_id=".$user_id)->save($data);
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>'修改成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'修改失败'));
            }
        }
        //获取省份
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $c = M('region')->where(array('parent_id' => $user_info['province'], 'level' => 2))->select();
        $d = M('region')->where(array('parent_id' => $user_info['city'], 'level' => 3))->select();
        $this->assign('province', $p);
        $this->assign('city', $c);
        $this->assign('district', $d);
        $this->assign('user', $user_info);
        $this->display();
    }
    // author :凌寒 2018年11月21日14:42:10 修改头像
    public function change_wx(){
        $user_id = $this->user_id;
        if(IS_AJAX){
            $user_info = M('users')->where(array('user_id'=>$user_id))->find();
            $weixin = I('weixin');
            if($user_info['weixin']==$weixin){
                $this->ajaxReturn(array('status'=>300,'msg'=>'没有任何修改'));
            }
            $res = M('users')->where("user_id=".$user_id)->save(array('weixin'=>$weixin));
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>'修改成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'修改失败'));
            }
        }
        $user = $this->user;
        $this->assign('user',$user);
        $this->display();
    }
    // author :凌寒 2018年11月21日14:42:10 修改支付宝
    public function change_zfb(){
        $user_id = $this->user_id;
        if(IS_AJAX){
            $user_info = M('users')->where(array('user_id'=>$user_id))->find();
            $zhifubao = I('zhifubao');
            if($user_info['zhifubao']==$zhifubao){
                $this->ajaxReturn(array('status'=>300,'msg'=>'没有任何修改'));
            }
            $res = M('users')->where("user_id=".$user_id)->save(array('zhifubao'=>$zhifubao));
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>'修改成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'修改失败'));
            }
        }
        $user = $this->user;
        $this->assign('user',$user);
        $this->display();
    }
    // author :凌寒 2018年10月26日13:31:36 修改用户名
    public function change_name(){
        if(IS_AJAX){
            $user_id = $this->user_id;
            $user_info = M('users')->where(array('user_id'=>$user_id))->find();
            $user_name = I('nickname');
            if($user_info['name']==$user_name){
                $this->ajaxReturn(array('status'=>300,'msg'=>'没有任何修改'));
            }
            $res = M('users')->where("user_id=".$user_id)->save(array('nickname'=>$user_name));
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>'修改成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'修改失败'));
            }
        }
        $user = $this->user;
        $this->assign('user',$user);
        $this->display();
    }
    // author :凌寒 2018年10月26日13:31:36 修改身份证号
    public function change_card(){
        if(IS_AJAX){
            $user_id = $this->user_id;
            $user_info = M('users')->where(array('user_id'=>$user_id))->find();
            $id_card = I('id_card');
            if($user_info['id_card']==$id_card){
                $this->ajaxReturn(array('status'=>300,'msg'=>'没有任何修改'));
            }
            $res = M('users')->where("user_id=".$user_id)->save(array('id_card'=>$id_card));
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>'修改成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'修改失败'));
            }
        }
        $user = $this->user;
        $this->assign('user',$user);
        $this->display();
    }
    // author :凌寒 2018年10月26日13:31:36 修改手机号
    public function change_mobile(){
        if(IS_AJAX){
            $user_id = $this->user_id;
            $code = I('code');
            $twopwd = I('pwd');
            $new_phone = I('new_mobile');
            if($code!=$_SESSION['code']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"验证码错误!"));
            }
            $user = M('users')->where("user_id=".$user_id)->find();
            if(md5($twopwd)!=$user["twopassword"]){
                $this->ajaxReturn(array('status'=>300,'msg'=>"安全密码错误!"));
            }
            if($user['mobile']==$new_phone){
                $this->ajaxReturn(array('status'=>300,'msg'=>"没有任何修改!"));
            }
            $check_mobile = M("users")->where("mobile='{$new_phone}'and user_id != {$user_id}")->count();
            if($check_mobile>0){
                $this->ajaxReturn(array('status'=>300,'msg'=>"手机号已注册!"));
            }

            $res = M('users')->where("user_id=".$user_id)->save(array('mobile'=>$new_phone));
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>'修改成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'修改失败'));
            }
        }
        $this->display();
    }
    // author :凌寒 2018年5月17日17:48:23 修改密码
    public function change_pwd(){
        if(IS_AJAX){
            $user_id = $this->user_id;
            $user_info = M('users')->where(array('user_id'=>$user_id))->find();
            $new_pwd = I('pwd')?I('pwd'):'';
            $re_pwd = I('repwd')?I('repwd'):'';
            if(md5($new_pwd)==$user_info['password']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"新旧密码一致"));
            }
            if($new_pwd!=$re_pwd){
                $this->ajaxReturn(array('status'=>300,'msg'=>"密码不一致"));
            }
            $res = M('users')->where(array('user_id'=>$user_id))->save(array('password'=>md5($new_pwd)));
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>"操作成功"));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"操作失败"));
            }
        }
        $this->display();
    }
    // author :凌寒 2018年5月17日17:48:23 修改二级密码
    public function change_twopwd(){
        if(IS_AJAX){
            $user_id = $this->user_id;
            $user_info = M('users')->where(array('user_id'=>$user_id))->find();
            $oldpwd = I('oldpwd')?I('oldpwd'):'';
            $new_pwd = I('pwd')?I('pwd'):'';
            $re_pwd = I('repwd')?I('repwd'):'';
            if(md5($oldpwd)!=$user_info['twopassword']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"旧安全密码错误!"));
            }
            if(md5($new_pwd)==$user_info['twopassword']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"旧密码和新密码不能一致"));
            }
            if($new_pwd!=$re_pwd){
                $this->ajaxReturn(array('status'=>300,'msg'=>"新密码和确认密码不一致"));
            }
            $res = M('users')->where(array('user_id'=>$user_id))->save(array('twopassword'=>md5($new_pwd)));
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>"修改成功"));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"修改失败"));
            }
        }
        $this->display();
    }
    // author :凌寒 2018年5月17日17:48:23 修改银行卡信息
    public function change_bank(){
        $user_id = $this->user_id;
        $user_info = M('users')->where(array('user_id'=>$user_id))->find();
        if(IS_AJAX){
            $data['bank_card_name'] = I('bank_card_name')?I('bank_card_name'):'';
            $data['bank_kai'] = I('bank_kai')?I('bank_kai'):'';
            $data['bank_name'] = I('bank_name')?I('bank_name'):'';
            $data['bank_card']= I('bank_card')?I('bank_card'):'';
            $res = M('users')->where(array('user_id'=>$user_info['user_id']))->save($data);
            if($res){
                $this->ajaxReturn(array('status'=>200,'msg'=>"修改成功"));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"修改失败"));
            }
        }
        $this->assign('user',$user_info);
        $this->display();
    }
    // author :凌寒 2018年10月29日15:27:46 上传图片
    public function up_img(){
        if($_FILES['file']['error']>0){
            return ['code'=>400,'msg'=>'上传异常'];
        }

        $up_file = upload_file($_FILES,'image');
        if($up_file == 'error'){
            $res = ['code'=>400,'msg'=>'上传失败'];
            echo json_encode($res);
            exit;
        }
        if($up_file == 'type_error'){
            $res = ['code'=>400,'msg'=>'上传文件格式错误'];
            echo json_encode($res);
            exit;
        }
        $res = ['code'=>200,'msg'=>$up_file];
        echo json_encode($res);
        exit;
    }
    // author :凌寒  联系我们
    public function contact(){
        $config = getConfig();
        $this->assign('config',$config);
        $this->display();
    }
    // author :凌寒 2019年5月13日09:29:10 红包领取
    public function hongbao(){
        $user_id = $this->user_id;
        $list = M("hongbao")->where("user_id=".$user_id)->order("id desc")->select();
        $this->assign('list',$list);
        $this->display();
    }
    // author :凌寒 2019年5月13日10:00:33 红包领取
    public function total_ling_hongbao(){
        $config = getConfig();
        $user_id = $this->user_id;
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));

        $info = M("hongbao")->where("user_id= {$user_id} and add_time > '{$beginToday}' and is_ling=0")->select();
        if(!$info){
            $this->ajaxReturn(array('status'=>300,'msg'=>'今日红包已领取'));
        }else{
            foreach ($info as $kk=>$vv){
                if($vv['is_yx']==1){
                    //更改红包状态
                    M("hongbao")->where("id=".$vv['id'])->save(array('is_ling'=>1,'ling_time'=>time()));
                    //更新用户账户
                    $award_money = $vv['total_money'];
                    $ye_bili = $config['basic_jj_ye_bili']/100;  //奖金70%进入余额
                    $jf_bili = $config['basic_jj_jf_bili']/100;  //奖金20%进入积分
                    $ax_bili = $config['basic_jj_ax_bili']/100;  //奖金10%进入爱心
                    $ye_award = $award_money * $ye_bili;
                    $jf_award = $award_money * $jf_bili;
                    $ax_award = $award_money * $ax_bili;
                    //更新余额
                    M("users")->where("user_id=".$vv['user_id'])->setInc("user_money",$ye_award);
                    upd_money($vv['user_id'],$ye_award,1,"奖金".$config['basic_jj_ye_bili']."%进余额",9);
                    //更新积分
                    M("users")->where("user_id=".$vv['user_id'])->setInc("pay_points",$jf_award);
                    upd_jifen($vv['user_id'],$jf_award,1,"奖金".$config['basic_jj_jf_bili']."%进消费积分",10);
                    //更新爱心
                    M("users")->where("user_id=".$vv['user_id'])->setInc("aixin_jifen",$ax_award);
                    upd_aixin($vv['user_id'],$ax_award,1,"奖金".$config['basic_jj_ax_bili']."%进爱心积分",4);
                }
                $award_money = $ye_award=$jf_award =$ax_award=0;
            }
            $this->ajaxReturn(array('status'=>200,'msg'=>'领取成功'));
        }
    }
    //红包领取
    public function ling_red(){
        $config = getConfig();
        $user_id = $this->user_id;
        $user_info = M('users')->where(array('user_id'=>$user_id))->find();
        //用户账户
        $award_money = $config['basic_red_money'];
        if($award_money> 0){
            M("users")->where("user_id=".$user_id)->setInc("user_money",$award_money);
            M("users")->where("user_id=".$user_id)->save(array('is_red'=>1));
            upd_money($user_id,$award_money,1,"领取红包奖励",5);
            $this->ajaxReturn(array('status'=>200,'msg'=>'红包领取成功'));
        }else{
            $this->ajaxReturn(array('status'=>300,'msg'=>'红包金额错误'));
        }
    }
    // author :凌寒 2019年5月13日10:46:09 爱心捐赠
    public function send_aixin(){
        $user_id = $this->user_id;
        $user_info = M('users')->where(array('user_id'=>$user_id))->find();
        if(IS_AJAX){
            $numbers = I("numbers");
            $twopwd = I('twopwd');

            if($numbers > $user_info['aixin_jifen']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"爱心值不足!"));
            }
            if(md5($twopwd)!=$user_info['twopassword']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"安全密码错误!"));
            }

            //更新爱心
            M("users")->where("user_id=".$user_info['user_id'])->setDec("aixin_jifen",$numbers);
            upd_aixin($user_info['user_id'],$numbers,0,"用户爱心捐赠",5);

            $this->ajaxReturn(array('status'=>200,'msg'=>"捐赠成功!"));
        }
        $this->assign('user',$user_info);
        $this->display();
    }
    // author :凌寒 2018年12月25日09:33:36 我的团队
    public function team(){

        $user_id = $this->user_id;
        $user =M("users")->where(array('user_id'=>$user_id))->find();
        //直推成员
        $child = M("users")->where(array('pid'=>$user_id))->field('user_id,nickname,reg_time,mobile')->order("reg_time desc")->select();
        foreach ($child as $kk=>$vv){
            $child[$kk]['mobile'] = substr_replace($vv['mobile'], '****', 3, 4);
        }
        $this->assign('list',$child);
//        //间推成员
//        $relation = M("user_relation")->where("parent_user_id='{$user_id}' and depth > 1")->order('id desc')->select();
//        foreach ($relation as $kk=>$vv){
//            $relation[$kk]['mobile'] = substr_replace($vv['user_mobile'], '****', 3, 4);
//            $relation[$kk]['nickname'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField('nickname');
//            $relation[$kk]['reg_time'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField('reg_time');
//        }
//        $this->assign('relation',$relation);
        $this->assign('user',$user);
        $this->display();
    }
    // author :凌寒 2019年5月18日16:51:04 计算团队人数
    public function team_num($user_id){
        $arr['team'] = 0;
        $arr['leiji_money'] = 0; //累计收入
//        $user = M("users")->where("user_id=".$user_id)->field("user_id,")
        $relation = M("user_relation")->where("parent_user_id=".$user_id)->select();
        $team_num = 0;
        foreach ($relation as $kk=>$vv){
            $team_num+=1;
        }
        return $team_num;
    }

    //ot_stone 2018年12月12日13:42:42 获取节点人以及左右区
    public function get_jiedian_arr($tuijian_id){

        $uid = $tuijian_id;//推荐人uid

        $max_cengshu = M('user_node')->where("userid=$uid")->order("cengshu desc")->getField("cengshu");//最大层数

        if(!$max_cengshu){
            //relation表中没有会员
            $nodal_person = $uid;
            $member_market = 0;
        }else{
            $cengcount = 1;
            while ($cengcount <= $max_cengshu) {
                $all_count = pow(2,$cengcount);//本层最多人数
                $max_count = M('user_node')->where("userid=$uid and cengshu=$cengcount")->count();//本层已有人数
                if($all_count == $max_count && $cengcount == $max_cengshu){ //如果最大层数已满
                    $nodal_person = M('user_node')->where("userid=$uid and cengshu=$cengcount")->order("id asc")->getFiled("newuserid");//最大层数
                    $member_market = 0;//左区
                }else{
                    if($max_count != $all_count){
                        if($cengcount == 1){
                            //如果为第一层
                            $nodal_person = $uid;
                            $member_market = 1;
                        }else{
                            $cengshu = $cengcount-1;
                            //如果最大层数没有满层 则获取最大层数-1的会员列表
                            $m_list = M('user_node')->where("userid=$uid and cengshu=$cengshu")->order("id asc")->field("userid,newuserid")->select();
                            foreach ($m_list as $k => $v) {
                                //查看该会员左右区是否都有会员
                                $userid = $v['newuserid'];
                                $member_market = 0;
                                $check_left = M("users")->where("nodal_person=$userid and member_market=0")->find();
                                if(!$check_left){
                                    break;
                                }else{
                                    $member_market = 1;
                                    $check_right = M("users")->where("nodal_person=$userid and member_market=1")->find();
                                    if(!$check_right){
                                        break;
                                    }
                                }
                            }
                            $nodal_person = $userid;
                        }
                    }
                }
                $cengcount++;
            }
        }

        return ['nodal_person'=>$nodal_person,'market'=>$member_market];
    }

    /*
     * 订单列表
     */
    public function order_list()
    {
        $where = ' user_id=' . $this->user_id;
        //条件搜索
        if(I('type')){
            $where .= C(strtoupper(I('type')));
        }else{
            $where .= " AND order_status in (0,1,2)";
        }
        $count = M('order')->where($where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $order_str = "order_id DESC";
        $order_list = M('order')->order($order_str)->where($where)->limit($Page->firstRow . ',' . $Page->listRows)->select();
//        echo "<pre>";
//        var_dump($order_list);die;
        //获取订单商品
        $model = new UsersLogic();
        foreach ($order_list as $k => $v) {
            $order_list[$k] = set_btn_order_status($v);  // 添加属性  包括按钮显示属性 和 订单状态显示属性
            //$order_list[$k]['total_fee'] = $v['goods_amount'] + $v['shipping_fee'] - $v['integral_money'] -$v['bonus'] - $v['discount']; //订单总额
            $data = $model->get_order_goods($v['order_id']);
            $order_list[$k]['goods_list'] = $data['result'];
            $delivery = M('delivery_doc')->where("order_id={$v['order_id']}")->limit(1)->find();
            $order_list[$k]['shipping_code'] = $delivery['shipping_code'];
            $order_list[$k]['invoice_no'] = $delivery['invoice_no'];
        }
        $this->assign('order_status', C('ORDER_STATUS'));
        $this->assign('shipping_status', C('SHIPPING_STATUS'));
        $this->assign('pay_status', C('PAY_STATUS'));
        $this->assign('page', $show);
        $this->assign('lists', $order_list);
        $this->assign('active', 'order_list');
        $this->assign('active_status', I('get.type'));
        if ($_GET['is_ajax']) {
            $this->display('ajax_order_list');
            exit;
        }
        $this->display();
    }
    public function wuliu(){
        $this->assign('invoice_no',I('invoice_no'));
        $this->assign('shipping_code',I('shipping_code'));
        $this->display();
    }
    /*
    * 卖出商品列表
    * */
    public function goodsList()
    {
        $config = getConfig();
        $where = 'sell_user_id=' . $this->user_id;
        $list = array();
        $list = M("maichu")->where($where)->select();
        if($list){
            foreach ($list as $k => $v) {
                $list[$k]['goods'] = M("goods")->where("goods_id=".$v['goods_id'])->find();
                $list[$k]['sell_user'] = M("users")->where("user_id=".$v['sell_user_id'])->field("user_id,nickname,head_pic")->find();
            }
        }
        $this->assign('config',$config);
        $this->assign('lists',$list);
        $this->display();
    }
    function ceshi(){
        $list = M("home_order")->where("home_id=172")->select();
        $no = array_rand($list);
        $order_id = $list[$no]['order_id'];
        echo "<pre>";
        var_dump($list[$no]['order_id']);die;
    }
    /*
     * 订单卖出
     * */
    public function order_sell()
    {
        $config = getConfig();
        $order_id = I("order_id");
        $order = M("order")->where("order_id=".$order_id)->find();
        if(!$order){
            $this->ajaxReturn(array('status'=>300,'msg'=>"订单不存在!"));
        }
        if($order['sell_status']==2){
            $this->ajaxReturn(array('status'=>300,'msg'=>"订单已卖出!"));
        }
        //更新卖出状态
        $res = M("order")->where("order_id=".$order_id)->save(array('sell_status'=>2));
        if($res){
            //更新抢单信息
            $home_order = M('home_order')->where("order_id=".$order['order_id'])->find();
            if($home_order){
                M('home')->where("id=".$home_order['home_id'])->setDec('num',1);
                M('home_order')->where("order_id=".$order['order_id'])->delete();
            }
            //卖出订单
            $goods =M("order_goods")->where("order_id=".$order['order_id'])->find();
            $data['order_id'] = $order['order_id'];
            $data['sell_user_id'] = $order['user_id'];
            $data['goods_id'] =$goods['goods_id'];
            $data['goods_price'] =$goods['goods_price'];
            $data['goods_num'] =$goods['goods_num'];
            $data['maichu_money'] =$goods['goods_price']*($config['basic_sell_bl']/100);
            $data['order_status'] =1;
            $data['add_time'] =time();
            M("maichu")->add($data);
            $this->ajaxReturn(array('status'=>200,'msg'=>"卖出成功!"));
        }else{
            $this->ajaxReturn(array('status'=>300,'msg'=>"卖出成功!"));
        }
    }
    /*
     * 订单列表
     */
    public function ajax_order_list()
    {

    }

    /*
     * 订单详情
     */
    public function order_detail()
    {
        $id = I('get.id');
        $map['order_id'] = $id;
        $map['user_id'] = $this->user_id;
        $order_info = M('order')->where($map)->find();
        $order_info = set_btn_order_status($order_info);  // 添加属性  包括按钮显示属性 和 订单状态显示属性
        if (!$order_info) {
            $this->error('没有获取到订单信息');
            exit;
        }
        //获取订单商品
        $model = new UsersLogic();
        $data = $model->get_order_goods($order_info['order_id']);
        $order_info['goods_list'] = $data['result'];
        //$order_info['total_fee'] = $order_info['goods_price'] + $order_info['shipping_price'] - $order_info['integral_money'] -$order_info['coupon_price'] - $order_info['discount'];

//        $region_list = get_region_list();
        $invoice_no = M('DeliveryDoc')->where("order_id = $id")->getField('invoice_no', true);
        $order_info[invoice_no] = implode(' , ', $invoice_no);

        $order_info['p_name'] = M('region')->where('id='.$order_info['province'])->getField('name');
        $order_info['c_name'] = M('region')->where('id='.$order_info['city'])->getField('name');
        $order_info['d_name'] = M('region')->where('id='.$order_info['district'])->getField('name');
        if($order_info['pay_btn']==0){
            if(stripos($order_info['order_sn'],'recharge') !== false){
                $order_info['order_jump'] = 'cart4';
            }elseif (stripos($order_info['order_sn'],'level') !== false){
                $order_info['order_jump'] = 'cart7';
            }else{
                $order_info['order_jump'] = 'cart4';
            }
        }

        //获取订单操作记录
        $order_action = M('order_action')->where(array('order_id' => $id))->select();
        $this->assign('order_status', C('ORDER_STATUS'));
        $this->assign('shipping_status', C('SHIPPING_STATUS'));
        $this->assign('pay_status', C('PAY_STATUS'));
//        $this->assign('region_list', $region_list);

        $this->assign('order_info', $order_info);
        $this->assign('order_action', $order_action);
        $this->display();
    }

    public function express()
    {
        $order_id = I('get.order_id');
        $result = $order_goods = $delivery = array();
        $order_goods = M('order_goods')->where("order_id=$order_id")->select();
        $delivery = M('delivery_doc')->where("order_id=$order_id")->limit(1)->find();

        if ($delivery['shipping_code'] && $delivery['invoice_no']) {

            $result = queryExpress($delivery['shipping_code'], $delivery['invoice_no']);
//                        echo "<pre>";
//            var_dump($result['data']);die;
            $this->assign('result', $result);
            $this->assign('order_goods', $order_goods);
            $this->assign('delivery', $delivery);
        }
        $this->display();
    }

    /*
     * 取消订单
     */
    public function cancel_order()
    {
        $id = I('get.id');
        //检查是否有积分，余额支付
        $logic = new UsersLogic();
        $data = $logic->cancel_order($this->user_id, $id);
        if ($data['status'] < 0)
            $this->error($data['msg']);
        $this->success($data['msg']);
    }

    /*
     * 用户地址列表
     */
    public function address_list()
    {
        $address_lists = M('user_address')->where(array('user_id'=>$this->user_id))->select();

        foreach ($address_lists as $kk=>$vv){
            $address_lists[$kk]['p_name'] = M('region')->where('id='.$vv['province'])->getField('name');
            $address_lists[$kk]['c_name'] = M('region')->where('id='.$vv['city'])->getField('name');
            $address_lists[$kk]['d_name'] = M('region')->where('id='.$vv['district'])->getField('name');
        }
        $this->assign('lists', $address_lists);
        $this->display();
    }

    /*
     * 添加地址
     */
    public function add_address()
    {
        if (IS_POST) {
            $logic = new UsersLogic();
            $data = $logic->add_address($this->user_id, 0, I('post.'));
            if ($data['status'] != 1){
                $this->error($data['msg']);
            }elseif (I('source') == 'cart2') {
                header('Location:' . U('/Mobile/Cart/cart2', array('address_id' => $data['result'])));
                exit;
            }elseif (I('source') == 'cart6') {
                header('Location:' . U('/Mobile/Cart/cart6', array('address_id' => $data['result'])));
                exit;
            }elseif (I('source') == 'cart8') {
                header('Location:' . U('/Mobile/Cart/cart8', array('address_id' => $data['result'],'id'=>I('maichu_id'))));
                exit;
            }
            $this->success($data['msg'], U('/Mobile/User/address_list'));
            exit();
        }
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $this->assign('province', $p);
        $this->display();

    }



    /*
     * 设置默认收货地址
     */
    public function set_default()
    {
        $id = I('get.id');
        $source = I('get.source');
        M('user_address')->where(array('user_id' => $this->user_id))->save(array('is_default' => 0));
        $row = M('user_address')->where(array('user_id' => $this->user_id, 'address_id' => $id))->save(array('is_default' => 1));
        if ($source == 'cart2') {
            header("Location:" . U('Mobile/Cart/cart2'));
            exit;
        }elseif (I('source') == 'cart6') {
            header('Location:' . U('/Mobile/Cart/cart6', array('address_id' => $data['result'])));
            exit;
        }elseif (I('source') == 'cart8') {
            header('Location:' . U('/Mobile/Cart/cart8', array('address_id' => $data['result'],'id'=>I('maichu_id'))));
            exit;
        }else {
            header("Location:" . U('Mobile/User/address_list'));
        }
    }

    /*
     * 地址删除
     */
    public function del_address()
    {
        $id = I('get.id');

        $address = M('user_address')->where("address_id = $id")->find();
        $row = M('user_address')->where(array('user_id' => $this->user_id, 'address_id' => $id))->delete();
        // 如果删除的是默认收货地址 则要把第一个地址设置为默认收货地址
        if ($address['is_default'] == 1) {
            $address2 = M('user_address')->where("user_id = {$this->user_id}")->find();
            $address2 && M('user_address')->where("address_id = {$address2['address_id']}")->save(array('is_default' => 1));
        }
        if (!$row)
            $this->error('操作失败', U('User/address_list'));
        else
            $this->success("操作成功", U('User/address_list'));
    }

    /*
     * 评论晒单
     */
    public function comment()
    {
        $user_id = $this->user_id;
        $status = I('get.status');
        $logic = new UsersLogic();
        $result = $logic->get_comment($user_id, $status); //获取评论列表
        $this->assign('comment_list', $result['result']);
        if ($_GET['is_ajax']) {
            $this->display('ajax_comment_list');
            exit;
        }
        $this->display();
    }

//    /*
//     *添加评论
//     */
//    public function add_comment()
//    {
//        if (IS_POST) {
//            // 晒图片
//            if ($_FILES[comment_img_file][tmp_name][0]) {
//                $upload = new \Think\Upload();// 实例化上传类
//                $upload->maxSize = $map['author'] = (1024 * 1024 * 3);// 设置附件上传大小 管理员10M  否则 3M
//                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
//                $upload->rootPath = './Public/upload/comment/'; // 设置附件上传根目录
//                $upload->replace = true; // 存在同名文件是否是覆盖，默认为false
//                //$upload->saveName  =  'file_'.$id; // 存在同名文件是否是覆盖，默认为false
//                // 上传文件
//                $upinfo = $upload->upload();
//                if (!$upinfo) {// 上传错误提示错误信息
//                    $this->error($upload->getError());
//                } else {
//                    foreach ($upinfo as $key => $val) {
//                        $comment_img[] = '/Public/upload/comment/' . $val['savepath'] . $val['savename'];
//                    }
//                    $add['img'] = serialize($comment_img); // 上传的图片文件
//                }
//            }
//
//            $user_info = session('user');
//            $logic = new UsersLogic();
//            $add['goods_id'] = I('goods_id');
//            $add['email'] = $user_info['email'];
//            $hide_username = I('hide_username');
//            if (empty($hide_username)) {
//                $add['username'] = $user_info['nickname'];
//            }
//            $add['order_id'] = I('order_id');
//            $add['service_rank'] = I('service_rank');
//            $add['deliver_rank'] = I('deliver_rank');
//            $add['goods_rank'] = I('goods_rank');
//            //$add['content'] = htmlspecialchars(I('post.content'));
//            $add['content'] = I('content');
//            $add['add_time'] = time();
//            $add['ip_address'] = getIP();
//            $add['user_id'] = $this->user_id;
//
//            //添加评论
//            $row = $logic->add_comment($add);
//            if ($row[status] == 1) {
//                $this->success('评论成功', U('/Mobile/Goods/goodsInfo', array('id' => $add['goods_id'])));
//                exit();
//            } else {
//                $this->error($row[msg]);
//            }
//        }
//        $rec_id = I('rec_id');
//        $order_goods = M('order_goods')->where("rec_id = $rec_id")->find();
//        $this->assign('order_goods', $order_goods);
//        $this->display();
//    }

    /*
     * 个人信息
     */
    public function userinfo()
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($this->user_id); // 获取用户信息
        $user_info = $user_info['result'];
        if (IS_POST) {
            I('post.nickname') ? $post['nickname'] = I('post.nickname') : false; //昵称
            I('post.qq') ? $post['qq'] = I('post.qq') : false;  //QQ号码
            I('post.head_pic') ? $post['head_pic'] = I('post.head_pic') : false; //头像地址
            I('post.sex') ? $post['sex'] = I('post.sex') : false;  // 性别
            I('post.birthday') ? $post['birthday'] = strtotime(I('post.birthday')) : false;  // 生日
            I('post.province') ? $post['province'] = I('post.province') : false;  //省份
            I('post.city') ? $post['city'] = I('post.city') : false;  // 城市
            I('post.district') ? $post['district'] = I('post.district') : false;  //地区
            I('post.email') ? $post['email'] = I('post.email') : false; //邮箱
            I('post.mobile') ? $post['mobile'] = I('post.mobile') : false; //手机

            $email = I('post.email');
            $mobile = I('post.mobile');
            $code = I('post.mobile_code', '');

            if (!empty($email)) {
                $c = M('users')->where("email = '{$post['email']}' and user_id != {$this->user_id}")->count();
                $c && $this->error("邮箱已被使用");
            }
            if (!empty($mobile)) {
                $c = M('users')->where("mobile = '{$post['mobile']}' and user_id != {$this->user_id}")->count();
                $c && $this->error("手机已被使用");
                if (!$code)
                    $this->error('请输入验证码');
                $check_code = $userLogic->sms_code_verify($mobile, $code, $this->session_id);
                if ($check_code['status'] != 1)
                    $this->error($check_code['msg']);
            }

            if (!$userLogic->update_info($this->user_id, $post))
                $this->error("保存失败");
            $this->success("操作成功");
            exit;
        }
        //  获取省份
        $province = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        //  获取订单城市
        $city = M('region')->where(array('parent_id' => $user_info['province'], 'level' => 2))->select();
        //  获取订单地区
        $area = M('region')->where(array('parent_id' => $user_info['city'], 'level' => 3))->select();
        $this->assign('province', $province);
        $this->assign('city', $city);
        $this->assign('area', $area);
        $this->assign('user', $user_info);
        $this->assign('sex', C('SEX'));
        $this->display();
    }

    /*
     * 邮箱验证
     */
    public function email_validate()
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($this->user_id); // 获取用户信息
        $user_info = $user_info['result'];
        $step = I('get.step', 1);
        //验证是否未绑定过
        if ($user_info['email_validated'] == 0)
            $step = 2;
        //原邮箱验证是否通过
        if ($user_info['email_validated'] == 1 && session('email_step1') == 1)
            $step = 2;
        if ($user_info['email_validated'] == 1 && session('email_step1') != 1)
            $step = 1;
        if (IS_POST) {
            $email = I('post.email');
            $code = I('post.code');
            $info = session('email_code');
            if (!$info)
                $this->error('非法操作');
            if ($info['email'] == $email || $info['code'] == $code) {
                if ($user_info['email_validated'] == 0 || session('email_step1') == 1) {
                    session('email_code', null);
                    session('email_step1', null);
                    if (!$userLogic->update_email_mobile($email, $this->user_id))
                        $this->error('邮箱已存在');
                    $this->success('绑定成功', U('Home/User/index'));
                } else {
                    session('email_code', null);
                    session('email_step1', 1);
                    redirect(U('Home/User/email_validate', array('step' => 2)));
                }
                exit;
            }
            $this->error('验证码邮箱不匹配');
        }
        $this->assign('step', $step);
        $this->display();
    }

    /*
    * 手机验证
    */
    public function mobile_validate()
    {
        $userLogic = new UsersLogic();
        $user_info = $userLogic->get_info($this->user_id); // 获取用户信息
        $user_info = $user_info['result'];
        $step = I('get.step', 1);
        //验证是否未绑定过
        if ($user_info['mobile_validated'] == 0)
            $step = 2;
        //原手机验证是否通过
        if ($user_info['mobile_validated'] == 1 && session('mobile_step1') == 1)
            $step = 2;
        if ($user_info['mobile_validated'] == 1 && session('mobile_step1') != 1)
            $step = 1;
        if (IS_POST) {
            $mobile = I('post.mobile');
            $code = I('post.code');
            $info = session('mobile_code');
            if (!$info)
                $this->error('非法操作');
            if ($info['email'] == $mobile || $info['code'] == $code) {
                if ($user_info['email_validated'] == 0 || session('email_step1') == 1) {
                    session('mobile_code', null);
                    session('mobile_step1', null);
                    if (!$userLogic->update_email_mobile($mobile, $this->user_id, 2))
                        $this->error('手机已存在');
                    $this->success('绑定成功', U('Home/User/index'));
                } else {
                    session('mobile_code', null);
                    session('email_step1', 1);
                    redirect(U('Home/User/mobile_validate', array('step' => 2)));
                }
                exit;
            }
            $this->error('验证码手机不匹配');
        }
        $this->assign('step', $step);
        $this->display();
    }

    public function collect_list()
    {
        $userLogic = new UsersLogic();
        $data = $userLogic->get_goods_collect($this->user_id);
        $this->assign('page', $data['show']);// 赋值分页输出
        $this->assign('goods_list', $data['result']);
        if ($_GET['is_ajax']) {
            $this->display('ajax_collect_list');
            exit;
        }
        $this->display();
    }

    /*
     *取消收藏
     */
    public function cancel_collect()
    {
        $collect_id = I('collect_id');
        $user_id = $this->user_id;
        if (M('goods_collect')->where("collect_id = $collect_id and user_id = $user_id")->delete()) {
            $this->success("取消收藏成功", U('User/collect_list'));
        } else {
            $this->error("取消收藏失败", U('User/collect_list'));
        }
    }

    public function message_list()
    {
        C('TOKEN_ON', true);
        if (IS_POST) {
            $this->verifyHandle('message');

            $data = I('post.');
            $data['user_id'] = $this->user_id;
            $user = session('user');
            $data['user_name'] = $user['nickname'];
            $data['msg_time'] = time();
            if (M('feedback')->add($data)) {
                $this->success("留言成功", U('User/message_list'));
                exit;
            } else {
                $this->error('留言失败', U('User/message_list'));
                exit;
            }
        }
        $msg_type = array(0 => '留言', 1 => '投诉', 2 => '询问', 3 => '售后', 4 => '求购');
        $count = M('feedback')->where("user_id=" . $this->user_id)->count();
        $Page = new Page($count, 100);
        $Page->rollPage = 2;
        $message = M('feedback')->where("user_id=" . $this->user_id)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $showpage = $Page->show();
        header("Content-type:text/html;charset=utf-8");
        $this->assign('page', $showpage);
        $this->assign('message', $message);
        $this->assign('msg_type', $msg_type);
        $this->display();
    }

    public function points()
    {
    	$type = I('type','all');
    	$this->assign('type',$type);
    	if($type == 'recharge'){
    		$count = M('recharge')->where("user_id=" . $this->user_id)->count();
    		$Page = new Page($count, 16);
    		$account_log = M('recharge')->where("user_id=" . $this->user_id)->order('order_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    	}else if($type == 'points'){
    		$count = M('account_log')->where("user_id=" . $this->user_id ." and pay_points!=0 ")->count();
    		$Page = new Page($count, 16);
    		$account_log = M('account_log')->where("user_id=" . $this->user_id." and pay_points!=0 ")->order('log_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    	}else{
    		$count = M('account_log')->where("user_id=" . $this->user_id)->count();
    		$Page = new Page($count, 16);
    		$account_log = M('account_log')->where("user_id=" . $this->user_id)->order('log_id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    	}
		$showpage = $Page->show();
        $this->assign('account_log', $account_log);
        $this->assign('page', $showpage);
        if ($_GET['is_ajax']) {
            $this->display('ajax_points');
            exit;
        }
        $this->display();
    }

    /*
     * 密码修改
     */
    public function password()
    {
        //检查是否第三方登录用户
        $logic = new UsersLogic();
        $data = $logic->get_info($this->user_id);
        $user = $data['result'];
        if ($user['mobile'] == '' && $user['email'] == '')
            $this->error('请先到电脑端绑定手机', U('/Mobile/User/index'));
        if (IS_POST) {
            $userLogic = new UsersLogic();
            $data = $userLogic->password($this->user_id, I('post.old_password'), I('post.new_password'), I('post.confirm_password')); // 获取用户信息
            if ($data['status'] == -1)
                $this->error($data['msg']);
            $this->success($data['msg']);
            exit;
        }
        $this->display();
    }
    // 找回密码
    function forget_pwd()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('Mobile/User/Index'));
        }
        if(IS_POST){
            $mobile = I('post.mobile');
            $code = I('post.code');
            $new_password = I('post.pwd');
            $confirm_password = I('post.repwd');
            //检查是否手机找回
            if(!$user = M("users")->where("mobile='{$mobile}'")->find()){
                $this->ajaxReturn(array('status'=>300,'msg'=>"手机号不存在!"));
            }else{
                if($code!=$_SESSION['code']){
                    $this->ajaxReturn(array('status'=>300,'msg'=>"验证码错误!"));
                }
                if($new_password != $confirm_password){
                    $this->ajaxReturn(array('status'=>300,'msg'=>"新密码和确认密码不一致!"));
                }
                if(md5($new_password) == $user['password']){
                    $this->ajaxReturn(array('status'=>300,'msg'=>"新密码和旧密码一致!"));
                }
                $res = M("users")->where("mobile='{$mobile}'")->save(array('password'=>md5($new_password)));
                if($res){
                    unset($_SESSION['code']);
                    $this->ajaxReturn(array('status'=>200,'msg'=>"密码找回成功"));
                }else{
                    unset($_SESSION['code']);
                    $this->ajaxReturn(array('status'=>300,'msg'=>"操作失败"));
                }
            }
        }
        $this->display();
    }
    // author :凌寒 2018年11月21日13:42:11 检测手机号(找回密码)
    public function check_phone(){
        $phone = I("phone");
        $list=M('users')->where("mobile='{$phone}'")->find();
        if($list){
            echo '2';
        }else{
            echo '1';
        }
    }
  // author :凌寒 2018年11月21日13:53:00 发送验证码
    public function send_message(){
        $phone = trim(I("phone"));
        $code = send_sms_mobile($phone);
        $_SESSION['code']=$code;
        echo 1;
    }
    function find_pwd()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('User/Index'));
        }
        $user = session('find_password');
        if (empty($user)) {
            $this->error("请先验证用户名", U('User/forget_pwd'));
        }
        $this->assign('user', $user);
        $this->display();
    }


    public function set_pwd()
    {
        if ($this->user_id > 0) {
            header("Location: " . U('User/Index'));
        }
        $check = session('validate_code');
        if (empty($check)) {
            header("Location:" . U('User/forget_pwd'));
        } elseif ($check['is_check'] == 0) {
            $this->error('验证码还未验证通过', U('User/forget_pwd'));
        }
        if (IS_POST) {
            $password = I('post.password');
            $password2 = I('post.password2');
            if ($password2 != $password) {
                $this->error('两次密码不一致', U('User/forget_pwd'));
            }
            if ($check['is_check'] == 1) {
                //$user = get_user_info($check['sender'],1);
                $user = M('users')->where("mobile = '{$check['sender']}' or email = '{$check['sender']}'")->find();
                M('users')->where("user_id=" . $user['user_id'])->save(array('password' => encrypt($password)));
                session('validate_code', null);
                //header("Location:".U('User/set_pwd',array('is_set'=>1)));
                $this->success('新密码已设置行牢记新密码', U('User/index'));
                exit;
            } else {
                $this->error('验证码还未验证通过', U('User/forget_pwd'));
            }
        }
        $is_set = I('is_set', 0);
        $this->assign('is_set', $is_set);
        $this->display();
    }

    //发送验证码
    public function send_validate_code()
    {
        $type = I('type');
        $send = I('send');
        $logic = new UsersLogic();
        $logic->send_validate_code($send, $type);
    }

    public function check_validate_code()
    {
        $code = I('post.code');
        $send = I('send');
        $logic = new UsersLogic();
        $logic->check_validate_code($code, $send);
    }
    /**
     * 验证码验证
     * $id 验证码标示
     */
    private function verifyHandle($id)
    {
        $verify = new Verify();
        if (!$verify->check(I('post.verify_code'), $id ? $id : 'user_login')) {
            $this->error("验证码错误");
        }
    }

    /**
     * 验证码获取
     */
    public function verify()
    {
        //验证码类型
        $type = I('get.type') ? I('get.type') : 'user_login';
        $config = array(
            'fontSize' => 40,
            'length' => 4,
            'useCurve' => true,
            'useNoise' => false,
        );
        $Verify = new Verify($config);
        $Verify->entry($type);
    }

    // author :凌寒 2019年4月25日13:25:19 爱心记录
    public function aixin_log()
    {
        $user_id = $this->user_id;
        $account_log = M("user_aixin")->where(array('user_id'=>$user_id))->order('id desc')->select();
        foreach ($account_log as $kk=>$vv){
            if($vv['from_user_id']){
                $account_log[$kk]['from_user_name'] = M("users")->where(array('user_id'=>$vv['from_user_id']))->getField('nickname');
            }
             $account_log[$kk]['add_time'] = date("Y-m-d",$vv['add_time']);
        }
        $this->assign('list',$account_log);
        $this->display();
    }
    //推荐值记录
    public function recom_log()
    {
        $user_id = $this->user_id;
        $account_log = M("user_recom")->where(array('user_id'=>$user_id))->order('id desc')->select();
        foreach ($account_log as $kk=>$vv){
            if($vv['from_user_id']){
                $account_log[$kk]['from_user_name'] = M("users")->where(array('user_id'=>$vv['from_user_id']))->getField('nickname');
            }
            $account_log[$kk]['add_time'] = date("Y-m-d",$vv['add_time']);
        }
        $this->assign('list',$account_log);
        $this->display();
    }
    //贡献值记录
    public function devote_log()
    {
        $user_id = $this->user_id;
        $account_log = M("user_devote")->where(array('user_id'=>$user_id))->order('id desc')->select();
        foreach ($account_log as $kk=>$vv){
            if($vv['from_user_id']){
                $account_log[$kk]['from_user_name'] = M("users")->where(array('user_id'=>$vv['from_user_id']))->getField('nickname');
            }
            $account_log[$kk]['add_time'] = date("Y-m-d",$vv['add_time']);
        }
        $this->assign('list',$account_log);
        $this->display();
    }
    //活跃度
    public function active_log()
    {
        $user_id = $this->user_id;
        $account_log = M("user_active")->where(array('user_id'=>$user_id))->order('id desc')->select();
        foreach ($account_log as $kk=>$vv){
            if($vv['from_user_id']){
                $account_log[$kk]['from_user_name'] = M("users")->where(array('user_id'=>$vv['from_user_id']))->getField('nickname');
            }
            $account_log[$kk]['add_time'] = date("Y-m-d",$vv['add_time']);
        }
        $this->assign('list',$account_log);
        $this->display();
    }
// author :凌寒 2019年4月25日13:25:19 余额记录
    public function money_log()
    {
        $user_id = $this->user_id;
        $account_log = M("user_money")->where(array('user_id'=>$user_id))->order('id desc')->select();
        foreach ($account_log as $kk=>$vv){
            if($vv['from_user_id']){
                $account_log[$kk]['from_user_name'] = M("users")->where(array('user_id'=>$vv['from_user_id']))->getField('nickname');
            }
            $account_log[$kk]['add_time'] = date("Y-m-d",$vv['add_time']);
        }
        $this->assign('list',$account_log);
        $this->display();
    }
    // author :凌寒 2019年4月25日13:25:19 积分记录
    public function jifen_log()
    {
        $user_id = $this->user_id;
        $account_log = M("user_jifen")->where(array('user_id'=>$user_id))->order('id desc')->select();
        foreach ($account_log as $kk=>$vv){
            if($vv['from_user_id']){
                $account_log[$kk]['from_user_name'] = M("users")->where(array('user_id'=>$vv['from_user_id']))->getField('nickname');
            }
            $account_log[$kk]['add_time'] = date("Y-m-d",$vv['add_time']);
        }
        $this->assign('list',$account_log);
        $this->display();
    }

    public function order_confirm()
    {
        $id = I('get.id', 0);
        $data = confirm_order($id, $this->user_id);
        if (!$data['status'])
            $this->error($data['msg']);
        else
            $this->success($data['msg']);
    }

    /**
     * 申请退货
     */
    public function return_goods()
    {
        $order_id = I('order_id', 0);
        $order_sn = I('order_sn', 0);
        $goods_id = I('goods_id', 0);
        $spec_key = I('spec_key');

        $c = M('order')->where("order_id = $order_id and user_id = {$this->user_id}")->count();
        if ($c == 0) {
            $this->error('非法操作');
            exit;
        }

        $return_goods = M('return_goods')->where("order_id = $order_id and goods_id = $goods_id and spec_key = '$spec_key'")->find();
        if (!empty($return_goods)) {
            $this->success('已经提交过退货申请!', U('Mobile/User/return_goods_info', array('id' => $return_goods['id'])));
            exit;
        }
        if (IS_POST) {

            // 晒图片
            if ($_FILES[return_imgs][tmp_name][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = $map['author'] = (1024 * 1024 * 3);// 设置附件上传大小 管理员10M  否则 3M
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->rootPath = './Public/upload/return_goods/'; // 设置附件上传根目录
                $upload->replace = true; // 存在同名文件是否是覆盖，默认为false
                //$upload->saveName  =  'file_'.$id; // 存在同名文件是否是覆盖，默认为false
                // 上传文件
                $upinfo = $upload->upload();
                if (!$upinfo) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {
                    foreach ($upinfo as $key => $val) {
                        $return_imgs[] = '/Public/upload/return_goods/' . $val['savepath'] . $val['savename'];
                    }
                    $data['imgs'] = implode(',', $return_imgs);// 上传的图片文件
                }
            }

            $data['order_id'] = $order_id;
            $data['order_sn'] = $order_sn;
            $data['goods_id'] = $goods_id;
            $data['addtime'] = time();
            $data['user_id'] = $this->user_id;
            $data['type'] = I('type'); // 服务类型  退货 或者 换货
            $data['reason'] = I('reason'); // 问题描述     
            $data['spec_key'] = I('spec_key'); // 商品规格						       
            M('return_goods')->add($data);
            $this->success('申请成功,客服第一时间会帮你处理', U('Mobile/User/order_list'));
            exit;
        }

        $goods = M('goods')->where("goods_id = $goods_id")->find();
        $this->assign('goods', $goods);
        $this->assign('order_id', $order_id);
        $this->assign('order_sn', $order_sn);
        $this->assign('goods_id', $goods_id);
        $this->display();
    }

    /**
     * 退换货列表
     */
    public function return_goods_list()
    {
        $count = M('return_goods')->where("user_id = {$this->user_id}")->count();
        $page = new Page($count, 4);
        $list = M('return_goods')->where("user_id = {$this->user_id}")->order("id desc")->limit("{$page->firstRow},{$page->listRows}")->select();
        $goods_id_arr = get_arr_column($list, 'goods_id');
        if (!empty($goods_id_arr))
            $goodsList = M('goods')->where("goods_id in (" . implode(',', $goods_id_arr) . ")")->getField('goods_id,goods_name');
        $this->assign('goodsList', $goodsList);
        $this->assign('list', $list);
        $this->assign('page', $page->show());// 赋值分页输出                    	    	
        if ($_GET['is_ajax']) {
            $this->display('return_ajax_goods_list');
            exit;
        }
        $this->display();
    }

    /**
     *  退货详情
     */
    public function return_goods_info()
    {
        $id = I('id', 0);
        $return_goods = M('return_goods')->where("id = $id")->find();
        if ($return_goods['imgs'])
            $return_goods['imgs'] = explode(',', $return_goods['imgs']);
        $goods = M('goods')->where("goods_id = {$return_goods['goods_id']} ")->find();
        $this->assign('goods', $goods);
        $this->assign('return_goods', $return_goods);
        $this->display();
    }
    public function goodsDetail(){
        $config = getConfig();
        $maichu_id = I('id');
        $info = M("maichu")->where("id=".$maichu_id)->find();
        if(empty($info)){
            $this->tp404('此商品不存在或者已下架');
        }
        $goods = M('Goods')->where("goods_id = ".$info['goods_id'])->find();
        $goods['goods_content'] = htmlspecialchars_decode($goods['goods_content']);
        if(empty($goods)){
            $this->tp404('此商品不存在或者已下架');
        }
        $goods_images_list = M('GoodsImages')->where("goods_id = ".$info['goods_id'])->select(); // 商品 图册
        $goods_attr_list = M('GoodsAttr')->where("goods_id = ".$info['goods_id'])->select(); // 查询商品属性表
        $spec_goods_price  = M('spec_goods_price')->where("goods_id = ".$info['goods_id'])->getField("key,price,store_count"); // 规格 对应 价格 库存表
        $this->assign('spec_goods_price', json_encode($spec_goods_price,true)); // 规格 对应 价格 库存表
        $goods['sale_num'] = M('order_goods')->where("goods_id={$info['goods_id']} and is_send=1")->count();
        $user = session('user');
        $this->assign("user",$user);
        $this->assign('goods_attr_list',$goods_attr_list);//属性列表
        $this->assign('goods_images_list',$goods_images_list);//商品缩略图
        $this->assign('goods',$goods);
        $this->assign('info',$info);
        $this->display();
    }
    //申请团长-商品详情
    public function leadGoods(){
        $config = getConfig();
        $goods_id = I("goods_id");
        $goods = M('Goods')->where("goods_id = $goods_id")->find();

        $goods['goods_content'] = htmlspecialchars_decode($goods['goods_content']);
        if(empty($goods)){
            $this->tp404('此商品不存在或者已下架');
        }
        $user = session('user');
        $this->assign("user",$user);
        $goods_images_list = M('GoodsImages')->where("goods_id = $goods_id")->select(); // 商品 图册
        $goods_attribute = M('GoodsAttribute')->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M('GoodsAttr')->where("goods_id = $goods_id")->select(); // 查询商品属性表
        $spec_goods_price  = M('spec_goods_price')->where("goods_id = $goods_id")->getField("key,price,store_count"); // 规格 对应 价格 库存表
        $this->assign('spec_goods_price', json_encode($spec_goods_price,true)); // 规格 对应 价格 库存表
        $goods['sale_num'] = M('order_goods')->where("goods_id=$goods_id and is_send=1")->count();
        $this->assign('goods_attribute',$goods_attribute);//属性值
        $this->assign('goods_attr_list',$goods_attr_list);//属性列表
        $this->assign('goods_images_list',$goods_images_list);//商品缩略图
        $this->assign('goods',$goods);
        $this->display();
    }
    public function recharge_one(){
        $config = getConfig();
        $order_id = I('order_id');
        $paymentList = M('Plugin')->where("`type`='payment' and code!='cod' and status = 1 and  scene in(0,1)")->select();
        //微信浏览器
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and code='weixin'")->select();
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
        $bank_img = include 'Application/Home/Conf/bank.php'; // 银行对应图片
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList',$paymentList);
        $this->assign('bank_img',$bank_img);
        $this->assign('bankCodeList',$bankCodeList);

        if($order_id>0){
            $order = M('recharge')->where("order_id = $order_id")->find();
            $this->assign('order',$order);
        }
        $user = $this->user;
        $this->assign('user',$user);
        $this->assign('config',$config);
        $this->display('recharge_one');
    }
    //充值
   public  function recharge(){
       $config = getConfig();
       if(IS_AJAX){
           $user = $this->user;
           $data['user_id'] = $user['user_id'];
           //安全密码
        //   if($user['twopassword']!=md5(I('twopwd'))){
        //       $this->ajaxReturn(array('status'=>300,'msg'=>'交易密码不正确'));
        //   }
           $money = I('money');
           $data['money'] = $money;
           $data['dakuan_img'] = I("dakuan_img");
           $data['bank_name'] = I("bank_title");
           $data['account_bank'] = I("bank_card");
           $data['account_name'] = I("bank_name");
           $data['bank_card_name'] = I("bank_card_name");
           $data['type'] = I("type");
           $data['create_time'] = time();
           $data['status'] = 0;
           $data['remark'] = "前台充值";
           $id = M('chongzhi')->add($data);
           if($id){
               $xx = 'https://mmmmmmmpay.022wjyxy.cn/api/create';//网关地址
                $data=[
                    'client_id'=>'16ce52ccbbc1eb86b8c730a2dfb46d20',
                    'out_trade_no'=>$data['create_time'].$id,
                    'total_fee'=>$money,
                    'callback_url'=>$_SERVER['HTTP_ORIGIN'].'/index.php?m=Mobile&c=User&a=callback',
                    'notify_url'=>$_SERVER['HTTP_ORIGIN'].'/index.php?m=Mobile&c=User&a=notify',
                ];
                ksort($data);//数组排序
                $str = '';
                foreach ($data as $k => $v) {
                    //生成键值字串
                    $str = $str . $k . $v;
                }
                $str = $str .'&key=29e2125e60a2a4ddbae355c32102bbdf16caf6b421da6c87a5deb5c9cdee0a8a';
                $data['sign']=strtoupper(md5($str));
                $ch = curl_init();
                $res=strstr($xx,'https');
                if($res){
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                }

                //post https
                curl_setopt($ch, CURLOPT_URL, $xx);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
                $result = curl_exec($ch);

                curl_close($ch);
                $result=json_decode($result);
                // if(!empty($result->pay_url)){
                    // M('chongzhi')->update($data);
                //     M('chongzhi')->where("id = ".explode('@',$parent['user_id']))->save(array('level'=>2));
                // }
               $this->ajaxReturn(array('status'=>200,'msg'=>'申请成功','data'=>$result));
           }else{
               $this->ajaxReturn(array('status'=>300,'msg'=>'提交失败!'));
           }
       }
       $order_id = I('order_id');
       $paymentList = M('Plugin')->where("`type`='payment' and code!='cod' and status = 1 and  scene in(0,1)")->select();
       //微信浏览器
       if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
           $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and code='weixin'")->select();
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
       $bank_img = include 'Application/Home/Conf/bank.php'; // 银行对应图片
       $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
       $this->assign('paymentList',$paymentList);
       $this->assign('bank_img',$bank_img);
       $this->assign('bankCodeList',$bankCodeList);

       if($order_id>0){
           $order = M('recharge')->where("order_id = $order_id")->find();
           $this->assign('order',$order);
       }
    //   var_dump($config);exit();
       $this->assign('config',$config);
       $this->display();
    }
    
    //异步
    //余额充值支付回调
    public function notify(){
        $data=file_get_contents('php://input');
        // $data='{"pay_status":1,"total_fee":"100.00","order_sn":"58@96","sign":"7880AA579B6B0A033C49491D9CB630AC"}';
        $data = json_decode($data,true);
        // $data=json_decode($data,true);
        // file_put_contents('./pay.txt',print_r($data,true).PHP_EOL,FILE_APPEND);
        $status=$data['pay_status'];
        $total_fee=$data['total_fee'];
        $order_sn=$data['order_sn'];
        $orderid=substr($data['order_sn'],10);
        if(!empty($orderid)){
            if($status==0){
                $status=2;
            }
            $chongzhi=M('chongzhi')->where(array('id'=>$orderid))->save(array('status'=>$status));
            if($chongzhi){
                if ($status==1) {
                    $cz_info = M("chongzhi")->where(array('id'=>$orderid))->find();
                    M("users")->where("user_id=".$cz_info['user_id'])->setInc("user_money",$cz_info['money']);
                    upd_money($cz_info['user_id'],$cz_info['money'],1,"会员充值余额",1);
                }
            }
        }
        echo "SUCCESS";exit();
    }
    
    //同步
    //余额充值支付回调
    public function callback(){
        $data=file_get_contents('php://input');
        // $data='{"pay_status":1,"total_fee":"100.00","order_sn":"58@96","sign":"7880AA579B6B0A033C49491D9CB630AC"}';
        $data = json_decode($data,true);
        parse_str($data,$res);
        // $data=json_decode($data,true);
        file_put_contents('./pay.txt',$data);
        $status=$data['pay_status'];
        $total_fee=$data['total_fee'];
        $order_sn=$data['order_sn'];
        $orderid=substr($data['order_sn'],10);
        if(!empty($orderid)){
            if($status==0){
                $status=2;
            }
            $chongzhi=M('chongzhi')->where(array('id'=>$orderid))->save(array('status'=>$status));
            if($chongzhi){
                if ($status==1) {
                    $cz_info = M("chongzhi")->where(array('id'=>$orderid))->find();
                    M("users")->where("user_id=".$cz_info['user_id'])->setInc("user_money",$cz_info['money']);
                    upd_money($cz_info['user_id'],$cz_info['money'],1,"会员充值余额",1);
                }
                echo "SUCCESS";
            }
        }
        header('Location: '.$_SERVER['HTTP_ORIGIN'].'/Mobile/User/recharge_log');
        exit();
    }
    
    // author :凌寒 2018年12月29日09:08:03充值记录
    public function recharge_log(){
       $user_id = $this->user_id;
       $recharge_log = M("chongzhi")->where("user_id = ".$user_id)->order("id desc")->select();
       foreach ($recharge_log as $kk=>$vv){
           $recharge_log[$kk]['create_time'] = date("Y-m-d",$vv['create_time']);
       }
//       echo "<pre>";
//       var_dump($recharge_log);die;
       $this->assign('log',$recharge_log);
       $this->display();
    }
    /**
     * 积分提现
     */
    public function jf_tx(){
        $config = getConfig();
        if(IS_AJAX){
            $user = $this->user;
            $time = time();
            $jg_time = $config['basic_tx_days']*86400;
            $info = M('user_tx')->where("user_id=".$user['user_id'])->order("create_time desc")->find();
            if($info){
                $last_time = $jg_time+$info['create_time'];
                if($time < $last_time){
                    $this->ajaxReturn(array('status'=>300,'msg'=>$config['basic_tx_days'].'内只能提现一次'));
                }
            }
            //安全密码
            if($user['twopassword']!=md5(I('twopwd'))){
                $this->ajaxReturn(array('status'=>300,'msg'=>'交易密码不正确'));
            }
            $money = floatval(I('money'));
            $feiyong = $config['basic_points_fee_bl']/100;
            $fy_money = $money*$feiyong;
            $last_money = $money+$fy_money;
            if($money < floatval($config['basic_points_min'])){
                $this->ajaxReturn(array('status'=>300,'msg'=>"提现最低额度为".$config['basic_min']));
            }
            if($money > $user['pay_points']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"账户余额不足!!!"));
            }
            if($last_money > $user['pay_points']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"手续费不足!!!"));
            }
            $type = I("type");
            if($type==1){
                $data['account_bank'] = I("zfb");
            }elseif ($type==2){
                $data['account_bank']= I("weixin");
            }else{
                $data['bank_kai'] = I("bank_kai");
                $data['bank_name'] = I("bank_title");
                $data['account_bank'] = I("bank_card");
                $data['account_name'] = I("bank_name");
            }
            $data['user_id'] = $user['user_id'];
            $data['money'] = $money;
            $data['feiyong'] = $fy_money;
            $data['last_money'] = $last_money;
            $data['type'] = $type;
            $data['create_time'] = time();
            $data['status'] = 0;
            $id = M('user_tx')->add($data);
            if($id){
                M("users")->where("user_id=".$user['user_id'])->setDec("pay_points",$last_money);
                $this->ajaxReturn(array('status'=>200,'msg'=>'申请成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'提交失败,请联系客服!'));
            }
        }
        $user = $this->user;
        $this->assign('user',$user);
        $this->assign('config',$config);
        $this->display();
    }
    public function jf_tx_log(){
        $user = $this->user;
        $list = M('user_tx')->where(array('user_id'=>$user['user_id']))->order("id desc")->select();
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * 申请提现记录
     */
    public function withdrawals(){
        $config = getConfig();

        if(IS_AJAX){
            $time = time();
            $jg_time = $config['basic_tx_days']*86400;
            $user = $this->user;
            $info = M('withdrawals')->where("user_id=".$user['user_id'])->order("create_time desc")->find();
            if($info){
                $last_time = $jg_time+$info['create_time'];
                if($time < $last_time){
                    $this->ajaxReturn(array('status'=>300,'msg'=>$config['basic_tx_days'].'内只能提现一次'));
                }
            }
            if($user['twopassword']!=md5(I('twopwd'))){
                $this->ajaxReturn(array('status'=>300,'msg'=>'安全密码不正确'));
            }
            $money = floatval(I('money'));
            $feiyong = $config['basic_money_fee_bl']/100;
            $fy_money = $money*$feiyong;
            $last_money = $money+$fy_money;
            if($money < floatval($config['basic_min'])){
                $this->ajaxReturn(array('status'=>300,'msg'=>"提现最低额度为".$config['basic_money_min']));
            }
            if($money > $user['user_money']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"可用余额不足!!!"));
            }
            if($last_money > $user['user_money']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"手续费不足!!!"));
            }
            $type = I("type");
            if($type==1){
                $data['account_bank'] = I("zfb");
            }elseif ($type==2){
                $data['account_bank']= I("weixin");
            }else{
                $data['bank_kai'] = I("bank_kai");
                $data['bank_name'] = I("bank_title");
                $data['account_bank'] = I("bank_card");
                $data['account_name'] = I("bank_name");
            }
            $data['user_id'] = $user['user_id'];
            $data['money'] = $money;
            $data['feiyong'] = $fy_money;
            $data['last_money'] = $last_money;
            $data['type'] = $type;
            $data['create_time'] = time();
            $data['status'] = 0;
            $id = M('withdrawals')->add($data);
            if($id){
                M("users")->where("user_id=".$user['user_id'])->setDec("user_money",$last_money);
                $this->ajaxReturn(array('status'=>200,'msg'=>'申请成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'提交失败,请联系客服!'));
            }
        }
        $user = $this->user;
        $this->assign('user',$user);
        $this->assign('config',$config);
        $this->display();
    }
    // author :凌寒 2019年1月25日13:46:35 现金转消费积分或者爱心积分
    public function jg_hz(){
        $config = getConfig();
        $user = $this->user;
        if(IS_AJAX){
            $config = getConfig();
            $user = $this->user;
            //安全密码
            if($user['twopassword']!=md5(I('twopwd'))){
                $this->ajaxReturn(array('status'=>300,'msg'=>'安全密码不正确'));
            }
            $money = I('money');
            if(floatval($money) > $user['user_money']){
                $this->ajaxReturn(array('status'=>300,'msg'=>"余额不足!!!"));
            }
            $type = I("type");

            if($type==3){
                //更新用户的现金积分和消费积分
                M("users")->where("user_id=".$user['user_id'])->setDec('user_money',$money);
                upd_money($user['user_id'],$money,0,"余额转积分",4);
                M("users")->where("user_id=".$user['user_id'])->setInc('pay_points',$money);
                upd_jifen($user['user_id'],$money,1,"余额转换",3);
            }else{
                //更新用户的现金积分和爱心积分
                M("users")->where("user_id=".$user['user_id'])->setDec('user_money',$money);
                upd_money($user['user_id'],$money,0,"余额转爱心",5);
                M("users")->where("user_id=".$user['user_id'])->setInc('aixin_jifen',$money);
                upd_aixin($user['user_id'],$money,1,"余额转换",1);
            }
            $this->ajaxReturn(array('status'=>200,'msg'=>'转换成功'));
        }
        $this->assign('user',$user);
        $this->assign('config',$config);
        $this->display();
    }
    // author :凌寒 2019年1月25日11:48:05 奖金币转购物币记录
    public function jg_log(){
        $user = $this->user;
        $log = M('user_money')->where("user_id=".$user['user_id']." and type in (4,5)")->select();
        $this->assign('log',$log);
        $this->display();
    }
    // 股东退股
    public function loan(){
        $config = getConfig();
        $user = $this->user;
        $level = M('user_level')->where("level = ".$user['level'])->find();
        if(IS_AJAX){
            $user = $this->user;
            $desc = I("desc");
            if($desc==''){
                $this->ajaxReturn(array('status'=>300,'msg'=>'请输入退股原因'));
            }
            $type = I("type");
            if($type==1){
                $data['account_bank'] = I("zfb");
            }elseif ($type==2){
                $data['account_bank']= I("weixin");
            }else{
                $data['bank_kai'] = I("bank_kai");
                $data['bank_name'] = I("bank_title");
                $data['account_bank'] = I("bank_card");
                $data['account_name'] = I("bank_name");
            }
            $fy = $level['money']*($level['fy_bl']/100);
            $data['user_id'] = $user['user_id'];
            $data['user_name'] = $user['nickname'];
            $data['mobile'] = $user['mobile'];
            $data['money'] = $level['money'];
            $data['feiyong'] = $fy;
            $data['back_money'] = $level['money']-$fy;
            $data['level'] = $level['level'];
            $data['desc'] = $desc;
            $data['type'] = $type;
            $data['create_time'] = time();
            $data['status']=3;
            $a = M("user_back")->add($data);
            if($a){
                $this->ajaxReturn(array('status'=>200,'msg'=>'申请成功,平台会尽快与您取得联系'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'申请失败!'));
            }
        }
        $this->assign('level',$level);
        $this->assign('user',$user);
        $this->assign('config',$config);
        $this->display();
    }
    //退款记录
    public function loan_log(){
        $user = $this->user;
        $log = M('user_back')->where("user_id=".$user['user_id'].'')->order("id desc")->select();
        $this->assign('log',$log);
        $this->display();
    }
    // author :凌寒 2019年5月11日10:41:37 代理申请
    public function agent(){
        $config = getConfig();
        $user = $this->user;
        if(IS_AJAX){
            $user = $this->user;
            //安全密码
            if($user['twopassword']!=md5(I('twopwd'))){
                $this->ajaxReturn(array('status'=>300,'msg'=>'密码不正确!'));
            }
            if(I("mobile")!=$user['mobile']){
                $this->ajaxReturn(array('status'=>300,'msg'=>'请输入注册手机号'));
            }
            $data['user_id'] = $user['user_id'];
            $data['user_name'] = I("nickname");
            $data['mobile'] = I("mobile");
            $data['create_time'] = time();
            $data['status']=3;
            $a = M("agent")->add($data);
            if($a){
                $this->ajaxReturn(array('status'=>200,'msg'=>'申请成功,平台会尽快与您取得联系'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'申请失败!'));
            }
        }
        $this->assign('user',$user);
        $this->assign('config',$config);
        $this->display();
    }
    // author :凌寒 代理申请记录
    public function agent_log(){
        $user = $this->user;
        $log = M('agent')->where("user_id=".$user['user_id'].'')->order("id desc")->select();
        $this->assign('log',$log);
        $this->display();
    }
   //积分互转
    public function jj_hz(){
        $config = getConfig();
        $user = $this->user;
        if(IS_AJAX){
            $user = $this->user;
            if($user['twopassword']!=md5(I('twopwd'))){
                $this->ajaxReturn(array('status'=>300,'msg'=>'密码不正确!'));
            }
            $to_code= I("to_mobile");
            $to_user = M("users")->where("user_code='{$to_code}'")->find();
            if(!$to_user){
                $this->ajaxReturn(array('status'=>300,'msg'=>'转出账号不存在!'));
            }
            if($to_code==$user['user_code']){
                $this->ajaxReturn(array('status'=>300,'msg'=>'转出账号不能为自己!'));
            }
            $money = I('money');
            if($money > $user['pay_points']){
                $this->ajaxReturn(array('status'=>300,'msg'=>'积分不足!'));
            }
            $fee_bl = $config['basic_hz_fee_bl']/100;
            $fee_num =$money*$fee_bl;
            $last_num = $money+$fee_num;
            if($last_num > $user['pay_points']){
                $this->ajaxReturn(array('status'=>300,'msg'=>'手续费不足!'));
            }
            $data['to_user_id'] = $to_user['user_id'];
            $data['user_id'] = $user['user_id'];
            $data['fee_num'] = $fee_num;
            $data['money'] = $money;
            $data['last_num'] = $last_num;
            $data['type']=1;
            $data['add_time'] = time();
            $id = M("user_exchange")->add($data);
            if($id){
                $a = M("users")->where("user_id=".$user['user_id'])->setDec('pay_points',$last_num);
                upd_jifen($user['user_id'],$last_num,0,"转出".$money."(手续费".$fee_num.")",1,$to_user['user_id']);
                $b = M("users")->where("user_id=".$to_user['user_id'])->setInc('pay_points',$money);
                upd_jifen($to_user['user_id'],$money,1,"转入".$money,3,$user['user_id']);
            }
            if($a && $b){
                $this->ajaxReturn(array('status'=>200,'msg'=>'转出成功'));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>'转出失败!'));
            }
        }
        $this->assign('user',$user);
        $this->assign('config',$config);
        $this->display();
    }
    //积分互转记录
    public function hz_log(){
        $user = $this->user;
        $log = M('user_jifen')->where("user_id=".$user['user_id'].' and type in (1,3)')->select();
        foreach ($log as $k=>$v){
            if($v['from_user_id']){
                $log[$k]['from_user_name'] =M("users")->where("user_id=".$v['from_user_id'])->getField("nickname");
            }else{
                $log[$k]['from_user_name'] ='';
            }

        }
//        dump($log);die;
        $this->assign('log',$log);
        $this->display();
    }
    // author :凌寒 2018-12-14 15:23:25 提现明细
    public function withdrawalsdetail(){
        $user = $this->user;
        $list = M('withdrawals')->where(array('user_id'=>$user['user_id']))->order("id desc")->select();
        $this->assign('list',$list);
        $this->display();
    }
    // author :凌寒 2018年12月28日08:37:59 会员升级
    public function shengji(){
        $order_id = I('order_id');
        $user = $this->user;
        $user['level_name'] = M("user_level")->where("level=".$user['level'])->getField("level_name");
        $tpshop_config = array();
        $tp_config = M('config')->cache(true,TPSHOP_CACHE_TIME)->select();
        foreach($tp_config as $k => $v)
        {
            if($v['name'] == 'hot_keywords'){
                $tpshop_config['hot_keywords'] = explode('|', $v['value']);
            }
            $tpshop_config[$v['inc_type'].'_'.$v['name']] = $v['value'];
        }
        $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and  scene in(0,1)")->select();
        //微信浏览器
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $paymentList = M('Plugin')->where("`type`='payment' and status = 1 and code='weixin'")->select();
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
        $levelList = M("user_level")->select();
//        dump($levelList);die;
        $this->assign("levelList",$levelList);
        $bank_img = include 'Application/Home/Conf/bank.php'; // 银行对应图片
        $payment = M('Plugin')->where("`type`='payment' and status = 1")->select();
        $this->assign('paymentList',$paymentList);
        $this->assign('bank_img',$bank_img);
        $this->assign('bankCodeList',$bankCodeList);
        $this->assign('user',$user);
        if($order_id>0){
            $order = M('recharge')->where("order_id = $order_id")->find();
            $this->assign('order',$order);
        }
        $this->display();
    }
    // author :凌寒 2018年12月30日14:39:58 升级记录
    public function shengji_log(){
        $user = $this->user;
        $log = M("update_level")->where("user_id=".$user['user_id'])->order("order_id desc")->select();
        foreach($log as $kk=>$vv){
            $log[$kk]['ctime'] = date("Y-m-d",$vv['ctime']);
            $log[$kk]['level_name'] = M("user_level")->where("level=".$vv['level'])->getField("level_name");
        }
        $this->assign('log',$log);
        $this->display();
    }
}