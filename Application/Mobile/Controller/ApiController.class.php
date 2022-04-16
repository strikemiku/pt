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
 * $Author: 当燃 2016-01-09
 */ 
namespace Mobile\Controller;
use Think\Verify;
class ApiController extends MobileBaseController {
    /*
    * 漏签判断
    * 每天零点更新
    * */
    public function missSign(){
        $config = getConfig();
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $user = M("users")->where("1=1")->order("user_id asc")->field("user_id,active_num,type,recom_num")->select();
        if($user){
            foreach ($user as $kk=>$vv){
                if($day<=10){
                    $decAward = $config['basic_sign_10_dec_num'];
                }elseif($day>10 && $day <=20){
                    $whr['user_id'] = $vv['user_id'];
                    $whr['year'] = $year;
                    $whr['month'] = $month;
                    $whr['day'] = array('between',array(1,10));
                    $count = M("user_sign")->where($whr)->count();
                    if($count>=10){
                        $decAward = $config['basic_sign_20_dec_num'];
                    }else{
                        $decAward = $config['basic_sign_10_dec_num'];
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
                            $decAward = $config['basic_sign_30_dec_num'];
                        }else{
                            $decAward = $config['basic_sign_20_dec_num'];
                        }
                    }else{
                        $decAward = $config['basic_sign_10_dec_num'];
                    }
                }
                //漏签判断
                $count = M("user_sign")->where("user_id=".$vv['user_id']." and year='{$year}' and month='{$month}' and day='{$day}'")->count();
                if($count==0 && $vv['active']>$decAward){
                    M("users")->where("user_id=".$vv['user_id'])->setDec("active_num",$decAward);
                    upd_active($vv['user_id'],$decAward,0,"漏签扣除".$decAward."活跃度",2);
                }
            }
        }
        echo "执行完毕";exit;
    }
    /*
    * 股东到期判断
    * 每天零点更新
    * */
    public function gdSet(){
        $nowTime = time();
        $list = M("update_level")->where("status=1 and pay_status=1")->order("order_id asc")->select();
        if($list){
            foreach ($list as $kk=>$vv){
                $user = M("users")->where("user_id=".$vv['user_id'])->field("user_id,level")->find();
                if($nowTime>$vv['end_time'] && $user['level']>1){
                    if($user['level']==2){
                        M("users")->where("user_id=".$vv['user_id'])->save(array('level'=>1,'gda_num'=>0));
                    }
                    if($user['level']==3){
                        M("users")->where("user_id=".$vv['user_id'])->save(array('level'=>1,'gdb_num'=>0));
                    }
                }
            }
        }
        echo "执行完毕";exit;
    }
    /*
    * 每日拼团次数清零
    * 每天零点1分更新
    * */
    public function dbSet(){
        //每日拼团数量清零
        M("users")->where("1=1")->save(array('pt_num'=>0));
        echo "执行完毕";exit;
    }
    /*
   * 未成团，房间解散
   * 每天零点更新
   * */
    public function dissTeam(){
        $nowTime = time();
        $home = M("home")->where("status=1")->order("id asc")->select();
        if($home){
            foreach ($home as $kk=>$vv){
                if($nowTime>$vv['end_time']){
                    $orders = M("home_order")->where("home_id=".$vv['id'])->field("id,user_id,home_id")->select();
                    $bacnkMoney = $vv['pt_price'];
                    foreach ($orders as $k=>$v){
                        M("home_order")->where("id=".$v['id'])->save(array('status'=>3));
                        //本金退回
                        M("users")->where("user_id=".$v['user_id'])->setInc("user_money",$bacnkMoney);
                        upd_money($v['user_id'],$bacnkMoney,1,"未成团本金退回".$bacnkMoney."",3);
                    }
                    M("home")->where("id=".$vv['id'])->save(array('status'=>3));
                }
            }
        }
        echo "执行完毕";exit;
    }
}