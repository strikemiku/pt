<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: 当燃      
 * 专题管理
 * Date: 2016-03-09
 */

namespace Admin\Controller;
use Think\AjaxPage;
use Think\Page;
use Admin\Logic\GoodsLogic;

class PromotionController extends BaseController {

    public function index(){
        $this->display();
    }
    //房间管理
    public function home(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $home_code = I("home_code");
        $goods_name = I("goods_name");
        $status = I("status");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['add_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($goods_name){
            $goods_id=M("goods")->where("goods_name like '%$goods_name%'")->getField("goods_id");
            $map['goods_id'] = $goods_id;
        }
        if(I("goods_id")){
            $map['goods_id'] = I("goods_id");
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($home_code){
            $map['home_code'] = $home_code;
        }
        if($status){
            $map['status'] = $status;
        }
        $count = M('home')->where($map)->count();
        $page = new Page($count);
        $lists  = M('home')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $user = M("users")->where("user_id=".$vv['user_id'])->field("user_id,nickname")->find();
            $lists[$kk]['user_name'] = $user['nickname'];
            $goods = M("goods")->where("goods_id =".$vv['goods_id'])->field("goods_name,original_img")->find();
            $lists[$kk]['goods_name'] = $goods['goods_name'];
            $lists[$kk]['goods_img'] = $goods['original_img'];
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    //参团列表
    public function homeOrder(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $home_code = I("home_code");
        $status = I("status");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['add_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($home_code){
            $home_id = M("home")->where("home_code='{$home_code}'")->getField("id");
            $map['home_id'] = $home_id;
        }
        if($status){
            $map['status'] = $status;
        }
        $count = M('home_order')->where($map)->count();
        $page = new Page($count);
        $lists  = M('home_order')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['home_code'] = M("home")->where("id=".$vv['home_id'])->getField("home_code");
            $user = M("users")->where("user_id=".$vv['user_id'])->field("user_id,nickname,mobile")->find();
            $lists[$kk]['user_name'] = $user['nickname'];
            $lists[$kk]['mobile'] = $user['mobile'];
            $goods = M("goods")->where("goods_id =".$vv['goods_id'])->field("goods_name,original_img")->find();
            $lists[$kk]['goods_name'] = $goods['goods_name'];
            $lists[$kk]['goods_img'] = $goods['original_img'];
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    //设置指定中奖
    public function set_win(){
        $id= I("id");
        $is_false = I("is_false");
        $order = M("home_order")->where("id=".$id)->find();
        $info = M("home")->where("id=".$order['home_id'])->find();
        if($info['status'] == 2){
            $this->ajaxReturn(array('status'=>300,'msg'=>"该团已完成!"));
        }
        if($info['status'] == 3){
            $this->ajaxReturn(array('status'=>300,'msg'=>"该团已解散!"));
        }
        if($is_false==3){
            $win_num = $info['pt_num']*($info['award_bl']/100);
            $count = M("home_order")->where("is_false=3 and home_id= ".$order['home_id'])->count();
            if($count >= $win_num){
                $this->ajaxReturn(array('status'=>300,'msg'=>"该团中奖人数已满!"));
            }
        }
        $res = M("home_order")->where("id=".$id)->save(array('is_false'=>$is_false));
        $this->ajaxReturn(array('status'=>200,'msg'=>"设置成功!"));
    }
    // author :凌寒 2019年4月24日21:13:09 买入列表
    public function mairu(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $order_sn = I("order_sn");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['add_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($order_sn){
            $map['order_sn'] = $order_sn;
        }
//        echo "<pre>";
//        var_dump($map);die;
        $count = M('mairu')->where($map)->count();

        $page = new Page($count);
        $lists  = M('mairu')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $user = M("users")->where("user_id=".$vv['user_id'])->field("user_id,nickname")->find();
            $lists[$kk]['user_name'] = $user['nickname'];
            $goods = M("goods")->where("goods_id =".$vv['goods_id'])->field("goods_name,original_img")->find();
            $lists[$kk]['goods_name'] = $goods['goods_name'];
            $lists[$kk]['goods_img'] = $goods['original_img'];
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // author :凌寒 2019年4月24日21:13:09 卖出列表
    public function maichu(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $order_sn = I("order_sn");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['add_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($order_sn){
            $map['order_sn'] = $order_sn;
        }
//        echo "<pre>";
//        var_dump($map);die;
        $count = M('maichu')->where($map)->count();

        $page = new Page($count);
        $lists  = M('maichu')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $user = M("users")->where("user_id=".$vv['user_id'])->field("user_id,nickname")->find();
            $lists[$kk]['user_name'] = $user['nickname'];
            $goods = M("goods")->where("goods_id =".$vv['goods_id'])->field("goods_name,original_img")->find();
            $lists[$kk]['goods_name'] = $goods['goods_name'];
            $lists[$kk]['goods_img'] = $goods['original_img'];
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // author :凌寒 2019年4月24日21:13:09 匹配记录
    public function match(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $order_sn = I("order_sn");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['add_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($order_sn){
            $map['mc_sn'] = $order_sn;
        }
//        echo "<pre>";
//        var_dump($map);die;
        $count = M('match')->where($map)->count();

        $page = new Page($count);
        $lists  = M('match')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
//        foreach ($lists as $kk=>$vv){
//            $user = M("users")->where("user_id=".$vv['user_id'])->field("user_id,nickname")->find();
//            $lists[$kk]['user_name'] = $user['nickname'];
//        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // author :凌寒 2019年4月24日21:50:46 执行匹配
    public function do_match(){

        $list  = M('maichu')->where("order_status=0")->order('id asc')->select();
        foreach ($list as $kk=>$vv){
            $type = $this->do_match_two($vv['id']);
            if ($type == -1) {
                break;
            }
        }
        $this->ajaxReturn(array('status'=>200,'msg'=>"匹配完成"));
    }
    // author :凌寒 2019年4月24日22:22:39 执行匹配第二步
    public function do_match_two($maichu_id){
        $config = getConfig();
        $jing_jj_bibli = floatval($config['basic_jing_jj_bibli'])/100; //静态收益X%进奖金账户
        $jing_jf_bili = floatval($config['basic_jing_jf_bili'])/100; //静态收益X%进积分账户
        //根据ID查询出卖出订单
        $maichu_info = M('maichu')->where("id=".$maichu_id)->find();
        //查询买入订单
        $mairu_info = M('mairu')->where("order_status=0 and user_id !=".$maichu_info['user_id'])->order("id asc")->find();
        if (empty($mairu_info)) {      //如果买入为空，说明目前没有可以匹配的订单
            return -1;
        }

        //更改买入订单状态
        M('mairu')->where("id=".$mairu_info['id'])->save(array('order_status'=>1));
        //更改卖出订单状态
        M('maichu')->where("id=".$maichu_id)->save(array('order_status'=>1));

        $fy_bili = floatval($config['basic_maichu_fy'])/100;   //费用比例

        $lixi = $maichu_info['maichu_money']-$maichu_info['mairu_money'];  //利息

        $fy = $maichu_info['maichu_money']*$fy_bili;  //费用

        $sj_lixi = $lixi-$fy;  //实际利息

        $jiangjin = $sj_lixi*$jing_jj_bibli;  //奖金

        $jifen = $sj_lixi*$jing_jf_bili;   //积分

        //更新用户余额 和 冻结余额
//        M("users")->where("user_id=".$maichu_info['user_id'])->setInc('user_money',$maichu_info['mairu_money']);
        M("users")->where("user_id=".$maichu_info['user_id'])->setInc('frozen_money',$maichu_info['mairu_money']);
        upd_money($maichu_info['user_id'],$maichu_info['mairu_money'],1,"订单匹配",1);
        //更新用户奖金
        $a = M("users")->where("user_id=".$maichu_info['user_id'])->setInc("award_money",$jiangjin);
        upd_award($maichu_info['user_id'],$jiangjin,1,"订单匹配,利息80%入奖金账户",1);

        //更新用户积分
        $b = M("users")->where("user_id=".$maichu_info['user_id'])->setInc("pay_points",$jifen);
        upd_jifen($maichu_info['user_id'],$jifen,1,"订单匹配,利息20%入积分账户",1);


        //匹配完成，新增匹配记录
        $mc['mc_sn'] = "MC".date('YmdHis').rand(1000,9999);;
        $mc['mairu_id'] = $mairu_info['id'];
        $mc['mairu_user_id'] = $mairu_info['user_id'];
        $mc['mairu_user_name'] =  M("users")->where("user_id=".$mairu_info['user_id'])->getField("nickname");    //买入user_name
        $mc['maichu_id'] = $maichu_info['id'];
        $mc['maichu_user_id'] = $maichu_info['user_id'];
        $mc['maichu_user_name'] = M("users")->where("user_id=".$maichu_info['user_id'])->getField("nickname");    //买出user_name
        $mc['mairu_money'] = $mairu_info['mairu_money'];
        $mc['maichu_money'] = $maichu_info['maichu_money'];
        $mc['money'] = $maichu_info['maichu_money'];
        $mc['lixi'] = $lixi;
        $mc['feiyong'] = $fy;
        $mc['sj_lixi'] = $sj_lixi;
        $mc['jiangjin'] = $jiangjin;
        $mc['jifen'] = $jifen;
        $mc['status'] = 1;
        $mc['add_time'] = time();
        $c = M('match')->add($mc);
        return 0;
    }
    public function num(){
        $act = I('GET.act','add');
        $this->assign('act',$act);
        $id = I('GET.id');
        $link_info = array();
        if($id){
            $link_info = D('group_num')->where('id='.$id)->find();
            $this->assign('info',$link_info);
        }
        $this->display();
    }

    public function numList(){
        $Ad =  M('group_num');
        $res = $Ad->where('1=1')->order('id desc')->page($_GET['p'].',10')->select();

        $this->assign('list',$res);// 赋值数据集
        $count = $Ad->where('1=1')->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    public function numHandle(){
        $data = I('post.');
        if($data['act'] == 'add'){
            $r = D('group_num')->add($data);
        }
        if($data['act'] == 'edit'){
            $info = D('group_num')->where('id='.$data['id'])->find();
            if($info['num']!=$data['num']){
                M("goods")->where("num_id=".$info['id'])->save(array('group_num'=>$data['num']));
            }
            $r = D('group_num')->where('id='.$data['id'])->save($data);
        }

        if($data['act'] == 'del'){
            $r = D('group_num')->where('id='.$data['id'])->delete();
            if($r) exit(json_encode(1));
        }

        if($r){
            $this->success("操作成功",U('Admin/Promotion/numList'));
        }else{
            $this->error("操作失败",U('Admin/Promotion/numList'));
        }
    }
    public function price(){
        $act = I('GET.act','add');
        $this->assign('act',$act);
        $id = I('GET.id');
        $link_info = array();
        if($id){
            $link_info = D('group_price')->where('id='.$id)->find();
            $this->assign('info',$link_info);
        }
        $group_num =M('group_num')->where("is_show=1")->order("id asc")->select();
        $this->assign('group_num',$group_num);
        $this->display();
    }

    public function priceList(){
        $Ad =  M('group_price');
        $res = $Ad->where('1=1')->order('id desc')->page($_GET['p'].',10')->select();

        $this->assign('list',$res);// 赋值数据集
        $count = $Ad->where('1=1')->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    public function priceHandle(){
        $data = I('post.');
        if($data['act'] == 'add'){
            $r = D('group_price')->add($data);
        }
        if($data['act'] == 'edit'){
            $info = D('group_price')->where('id='.$data['id'])->find();
            if($info['price']!=$data['price']){
                M("goods")->where("price_id=".$info['id'])->save(array('group_price'=>$data['price']));
            }
            $r = D('group_price')->where('id='.$data['id'])->save($data);
        }

        if($data['act'] == 'del'){
            $r = D('group_price')->where('id='.$data['id'])->delete();
            if($r) exit(json_encode(1));
        }

        if($r){
            $this->success("操作成功",U('Admin/Promotion/priceList'));
        }else{
            $this->error("操作失败",U('Admin/Promotion/priceList'));
        }
    }
        /**
         * 商品活动列表
         */
	public function prom_goods_list()
	{
		$parse_type = array('0'=>'直接打折','1'=>'减价优惠','2'=>'固定金额出售','3'=>'买就赠优惠券');                               
		$level = M('user_level')->select();
		if($level){
			foreach ($level as $v){
				$lv[$v['level_id']] = $v['level_name'];
			}
		}
		$this->assign("parse_type",$parse_type);
                
                $count = M('prom_goods')->count();
                $Page  = new \Think\Page($count,10);    	 
                $show = $Page->show();                      
		$res = M('prom_goods')->limit($Page->firstRow.','.$Page->listRows)->select();    
		if($res){
			foreach ($res as $val){
				if(!empty($val['group']) && !empty($lv)){
					$val['group'] = explode(',', $val['group']);
					foreach ($val['group'] as $v){
						$val['group_name'] .= $lv[$v].',';
					}
				}
				$prom_list[] = $val;
			}
		}
                $this->assign('page',$show);// 赋值分页输出
		$this->assign('prom_list',$prom_list);
		$this->display();
	}
	
	public function prom_goods_info()
	{
		$level = M('user_level')->select();
		$this->assign('level',$level);
		$prom_id = I('id');
		$info['start_time'] = date('Y-m-d');
		$info['end_time'] = date('Y-m-d',time()+3600*60*24);
		if($prom_id>0){
			$info = M('prom_goods')->where("id=$prom_id")->find();
			$info['start_time'] = date('Y-m-d',$info['start_time']);
			$info['end_time'] = date('Y-m-d',$info['end_time']);
			$prom_goods = M('goods')->where("prom_id=$prom_id and prom_type=3")->select();
			$this->assign('prom_goods',$prom_goods);
		}
		$this->assign('info',$info);
		$this->assign('min_date',date('Y-m-d'));
		$this->initEditor();
		$this->display();
	}
	
	public function prom_goods_save()
	{
		$prom_id = I('id');
		$data = I('post.');
		$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);
		$data['group'] = implode(',', $data['group']);
		if($prom_id){
			M('prom_goods')->where("id=$prom_id")->save($data);
			$last_id = $prom_id;
			adminLog("管理员修改了商品促销 ".I('name'));
		}else{
			$last_id = M('prom_goods')->add($data);
			adminLog("管理员添加了商品促销 ".I('name'));
		}
		
		if(is_array($data['goods_id'])){
			$goods_id = implode(',', $data['goods_id']);
			if($prom_id>0){
				M("goods")->where("prom_id=$prom_id and prom_type=3")->save(array('prom_id'=>0,'prom_type'=>0));
			}
			M("goods")->where("goods_id in($goods_id)")->save(array('prom_id'=>$last_id,'prom_type'=>3));
		}
		$this->success('编辑促销活动成功',U('Promotion/prom_goods_list'));
	}
	
	public function prom_goods_del()
	{
		$prom_id = I('id');                
                $order_goods = M('order_goods')->where("prom_type = 3 and prom_id = $prom_id")->find();
                if(!empty($order_goods))
                {
                    $this->error("该活动有订单参与不能删除!");    
                }                
		M("goods")->where("prom_id=$prom_id and prom_type=3")->save(array('prom_id'=>0,'prom_type'=>0));
		M('prom_goods')->where("id=$prom_id")->delete();
		$this->success('删除活动成功',U('Promotion/prom_goods_list'));
	}
    

    
        /**
         * 活动列表
         */
	public function prom_order_list()
	{
		$parse_type = array('0'=>'满额打折','1'=>'满额优惠金额','2'=>'满额送积分','3'=>'满额送优惠券');		
		$level = M('user_level')->select();
		if($level){
			foreach ($level as $v){
				$lv[$v['level_id']] = $v['level_name'];
			}
		}
                $count = M('prom_order')->count();
                $Page  = new \Think\Page($count,10);    	 
                $show = $Page->show();               
		$res = M('prom_order')->limit($Page->firstRow.','.$Page->listRows)->select();
		if($res){
			foreach ($res as $val){
				if(!empty($val['group']) && !empty($lv)){
					$val['group'] = explode(',', $val['group']);
					foreach ($val['group'] as $v){
						$val['group_name'] .= $lv[$v].',';
					}
				}
				$prom_list[] = $val;
			}
		}
                $this->assign('page',$show);// 赋值分页输出                  
                $this->assign("parse_type",$parse_type);
		$this->assign('prom_list',$prom_list);
		$this->display();
	}
	
	public function prom_order_info(){
		$this->assign('min_date',date('Y-m-d'));
		$level = M('user_level')->select();
		$this->assign('level',$level);
		$prom_id = I('id');
		$info['start_time'] = date('Y-m-d');
		$info['end_time'] = date('Y-m-d',time()+3600*24*60);
		if($prom_id>0){
			$info = M('prom_order')->where("id=$prom_id")->find();
			$info['start_time'] = date('Y-m-d',$info['start_time']);
			$info['end_time'] = date('Y-m-d',$info['end_time']);
		}
		$this->assign('info',$info);
		$this->assign('min_date',date('Y-m-d'));
		$this->initEditor();
		$this->display();
	}
	
	public function prom_order_save(){
		$prom_id = I('id');
		$data = I('post.');
		$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);
		$data['group'] = implode(',', $data['group']);
		if($prom_id){
			M('prom_order')->where("id=$prom_id")->save($data);
			adminLog("管理员修改了商品促销 ".I('name'));
		}else{
			M('prom_order')->add($data);
			adminLog("管理员添加了商品促销 ".I('name'));
		}
		$this->success('编辑促销活动成功',U('Promotion/prom_order_list'));
	}
	
	public function prom_order_del()
	{
		$prom_id = I('id');                                
                $order = M('order')->where("order_prom_id = $prom_id")->find();
                if(!empty($order))
                {
                    $this->error("该活动有订单参与不能删除!");    
                }
                                
		M('prom_order')->where("id=$prom_id")->delete();
		$this->success('删除活动成功',U('Promotion/prom_order_list'));
	}
	
    public function group_buy_list(){
    	$Ad =  M('group_buy');
    	$count = $Ad->count();
    	$Page = new \Think\Page($count,10);        
    	$res = $Ad->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
    	if($res){
    		foreach ($res as $val){
    			$val['start_time'] = date('Y-m-d H:i',$val['start_time']);
    			$val['end_time'] = date('Y-m-d H:i',$val['end_time']);
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);
    	$show = $Page->show();
    	$this->assign('page',$show);
    	$this->display();
    }
    
    public function group_buy(){
    	$act = I('GET.act','add');
    	$groupbuy_id = I('get.id');
    	$group_info = array();
    	$group_info['start_time'] = date('Y-m-d');
    	$group_info['end_time'] = date('Y-m-d',time()+3600*365);
    	if($groupbuy_id){
    		$group_info = D('group_buy')->where('id='.$groupbuy_id)->find();
    		$group_info['start_time'] = date('Y-m-d H:i',$group_info['start_time']);
    		$group_info['end_time'] = date('Y-m-d H:i',$group_info['end_time']);
    		$act = 'edit';
    	}
    	$this->assign('min_date',date('Y-m-d'));
    	$this->assign('info',$group_info);
    	$this->assign('act',$act);
    	$this->display();
    }
    
    public function groupbuyHandle(){
    	$data = I('post.');
    	$data['groupbuy_intro'] = htmlspecialchars(stripslashes($_POST['groupbuy_intro']));
    	$data['start_time'] = strtotime($data['start_time']);
    	$data['end_time'] = strtotime($data['end_time']);
    	if($data['act'] == 'del'){
    		$r = D('group_buy')->where('id='.$data['id'])->delete();
    		M('goods')->where("prom_type=2 and prom_id=".$data['id'])->save(array('prom_id'=>0,'prom_type'=>0));
    		if($r) exit(json_encode(1));
    	}
    	if($data['act'] == 'add'){
    		$r = D('group_buy')->add($data);
    		M('goods')->where("goods_id=".$data['goods_id'])->save(array('prom_id'=>$r,'prom_type'=>2));
    	}
    	if($data['act'] == 'edit'){
    		$r = D('group_buy')->where('id='.$data['id'])->save($data);
    		M('goods')->where("prom_type=2 and prom_id=".$data['id'])->save(array('prom_id'=>0,'prom_type'=>0));
    		M('goods')->where("goods_id=".$data['goods_id'])->save(array('prom_id'=>$data['id'],'prom_type'=>2));
    	}
    	if($r){
    		$this->success("操作成功",U('Admin/Promotion/group_buy_list'));
    	}else{
    		$this->error("操作失败",U('Admin/Promotion/group_buy_list'));
    	}
    }
    
    public function get_goods(){
    	$prom_id = I('id');
    	$count = M('goods')->where("prom_id=$prom_id and prom_type=3")->count(); 
    	$Page  = new \Think\Page($count,10);
    	$goodsList = M('goods')->where("prom_id=$prom_id and prom_type=3")->order('goods_id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
    	$show = $Page->show();
    	$this->assign('page',$show);
    	$this->assign('goodsList',$goodsList);
    	$this->display(); 
    }   
    
    public function search_goods(){
    	$GoodsLogic = new \Admin\Logic\GoodsLogic;
    	$brandList = $GoodsLogic->getSortBrands();
    	$this->assign('brandList',$brandList);
    	$categoryList = $GoodsLogic->getSortCategory();
    	$this->assign('categoryList',$categoryList);
    	
    	$goods_id = I('goods_id');
    	$where = ' is_on_sale = 1 and prom_type=0 and store_count>0 ';//搜索条件
    	if(!empty($goods_id)){
    		$where .= " and goods_id not in ($goods_id) ";
    	}
    	I('intro')  && $where = "$where and ".I('intro')." = 1";
    	if(I('cat_id')){
    		$this->assign('cat_id',I('cat_id'));
    		$grandson_ids = getCatGrandson(I('cat_id'));
    		$where = " $where  and cat_id in(".  implode(',', $grandson_ids).") "; // 初始化搜索条件
    	}
    	if(I('brand_id')){
    		$this->assign('brand_id',I('brand_id'));
    		$where = "$where and brand_id = ".I('brand_id');
    	}
    	if(!empty($_REQUEST['keywords']))
    	{
    		$this->assign('keywords',I('keywords'));
    		$where = "$where and (goods_name like '%".I('keywords')."%' or keywords like '%".I('keywords')."%')" ;
    	}
    	$count = M('goods')->where($where)->count();
    	$Page  = new \Think\Page($count,10);
    	$goodsList = M('goods')->where($where)->order('goods_id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
    	$show = $Page->show();//分页显示输出
    	$this->assign('page',$show);//赋值分页输出
    	$this->assign('goodsList',$goodsList);
    	$tpl = I('get.tpl','search_goods');
    	$this->display($tpl);
    }
    
    //限时抢购
    public function flash_sale(){
    	$condition = array();
    	$model = M('flash_sale');
    	$count = $model->where($condition)->count();
    	$Page  = new \Think\Page($count,10);    	 
    	$show = $Page->show();
    	$prom_list = $model->where($condition)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign('prom_list',$prom_list);
    	$this->assign('page',$show);// 赋值分页输出
    	$this->display();
    }
    
    public function flash_sale_info(){
    	if(IS_POST){
    		$data = I('post.');
    		$data['start_time'] = strtotime($data['start_time']);
    		$data['end_time'] = strtotime($data['end_time']);
    		if(empty($data['id'])){
    			$r = M('flash_sale')->add($data);
    			M('goods')->where("goods_id=".$data['goods_id'])->save(array('prom_id'=>$r,'prom_type'=>1));
    			adminLog("管理员添加抢购活动 ".$data['name']);
    		}else{
    			$r = M('flash_sale')->where("id=".$data['id'])->save($data);
    			M('goods')->where("prom_type=1 and prom_id=".$data['id'])->save(array('prom_id'=>0,'prom_type'=>0));
    			M('goods')->where("goods_id=".$data['goods_id'])->save(array('prom_id'=>$data['id'],'prom_type'=>1));
    		}
    		if($r){
    			$this->success('编辑抢购活动成功',U('Promotion/flash_sale'));
    			exit;
    		}else{
    			$this->error('编辑抢购活动失败',U('Promotion/flash_sale'));
    		}
    	}
    	$id = I('id');
        $info['start_time'] = date('Y-m-d H:i:s');
    	$info['end_time'] = date('Y-m-d 23:59:59',time()+3600*24*60);
    	if($id>0){
    		$info = M('flash_sale')->where("id=$id")->find();
    		$info['start_time'] = date('Y-m-d H:i',$info['start_time']);
    		$info['end_time'] = date('Y-m-d H:i',$info['end_time']);
    	}
    	$this->assign('info',$info);
    	$this->assign('min_date',date('Y-m-d'));
    	$this->display();
    }
    
    public function flash_sale_del(){
    	$id = I('del_id');
    	if($id){
    		M('flash_sale')->where("id=$id")->delete();
    		M('goods')->where("prom_type=1 and prom_id=$id")->save(array('prom_id'=>0,'prom_type'=>0));
    		 exit(json_encode(1));
    	}else{
    		 exit(json_encode(0));
    	}
    }
    
    private function initEditor()
    {
    	$this->assign("URL_upload", U('Admin/Ueditor/imageUp',array('savepath'=>'promotion')));
    	$this->assign("URL_fileUp", U('Admin/Ueditor/fileUp',array('savepath'=>'promotion')));
    	$this->assign("URL_scrawlUp", U('Admin/Ueditor/scrawlUp',array('savepath'=>'promotion')));
    	$this->assign("URL_getRemoteImage", U('Admin/Ueditor/getRemoteImage',array('savepath'=>'promotion')));
    	$this->assign("URL_imageManager", U('Admin/Ueditor/imageManager',array('savepath'=>'promotion')));
    	$this->assign("URL_imageUp", U('Admin/Ueditor/imageUp',array('savepath'=>'promotion')));
    	$this->assign("URL_getMovie", U('Admin/Ueditor/getMovie',array('savepath'=>'promotion')));
    	$this->assign("URL_Home", "");
    }
    
}