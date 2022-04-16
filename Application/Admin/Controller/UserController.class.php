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
 * Date: 2015-09-09
 */

namespace Admin\Controller;

use Think\AjaxPage;
use Think\Page;
use Admin\Logic\UsersLogic;

class UserController extends BaseController {
    //会员列表
    public function index(){
        $user_level = M("user_level")->order("level asc")->select();
        $this->assign('user_level',$user_level);
        $this->display();
    }

    /**
     * 会员列表
     */
    public function ajaxindex(){
        // 搜索条件
        $condition = array();
        if(I('mobile')){
            $condition['mobile'] = I('mobile');
        }
        if(I('nickname')){
            $condition['nickname'] = I('nickname');
        }
        if(I('level')){
            $condition['level'] = I('level');
        }
        if(I('user_code')){
            $condition['user_code'] = I('user_code');
        }
        if(I('pcode')){
            $condition['user_code'] = I('pcode');
        }
        $sort_order = I('order_by','user_id').' '.I('sort','desc');
        $model = M('users');
        $count = $model->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }
        
        $userList = $model->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach($userList as $kk=>$vv){
                $userList[$kk]['level_name'] = M('user_level')->where(array('level'=>$vv['level']))->getField("level_name");
               if($vv['pid']){
                    $userList[$kk]['p_name'] = M("users")->where("user_id = ".$vv['pid'])->getField("nickname");
               }

        }
        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('level',M('user_level')->where("level < 3")->getField('level,level_name'));
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    public function is_daili($user_id){
        //代理商
        $relation= M("user_relation")->where("user_id=".$user_id)->order("depth asc")->select();
        $arr = array();
        if($relation){
            foreach ($relation as $k=>$v){
                $parent = M('users')->where("user_id = '{$v[parent_user_id]}'")->field("user_id,level,mobile,nickname")->find();
                if($parent['level']==3){
                    $arr['dl_mobile'] = $parent['mobile'];
                    $arr['dl_name'] = $parent['nickname'];

                }
            }
        }
        return $arr;
    }
    public function daili(){
        $user_level = M("user_level")->order("level asc")->select();
        $this->assign('user_level',$user_level);
        $this->display();
    }

    /**
     * 会员列表
     */
    public function ajaxdaili(){
        // 搜索条件
        $condition = array();
        if(I('mobile')){
            $condition['mobile'] = I('mobile');
        }
        if(I('nickname')){
            $condition['nickname'] = I('nickname');
        }
        if(I('user_code')){
            $condition['user_code'] = I('user_code');
        }
        $sort_order = I('order_by','user_id').' '.I('sort','desc');

        $condition['level'] = 3;//代理商
        $model = M('users');
        $count = $model->where($condition)->count();
        $Page  = new AjaxPage($count,10);
        //  搜索条件下 分页赋值
        foreach($condition as $key=>$val) {
            $Page->parameter[$key]   =   urlencode($val);
        }

        $userList = $model->where($condition)->order($sort_order)->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach($userList as $kk=>$vv){
            $userList[$kk]['level_name'] = M('user_level')->where(array('level'=>$vv['level']))->getField("level_name");
            $userList[$kk]['province'] = M('region')->where(array('id'=>$vv['province']))->getField("name");
            $userList[$kk]['city'] = M('region')->where(array('id'=>$vv['city']))->getField("name");
            $userList[$kk]['district'] = M('region')->where(array('id'=>$vv['district']))->getField("name");
            if($vv['pid']){
                $userList[$kk]['p_name'] = M("users")->where("user_id = ".$vv['pid'])->getField("nickname");
            }
            if($vv['node_id']){
                $userList[$kk]['node_name'] = M("users")->where("user_id = ".$vv['node_id'])->getField("nickname");
            }
        }
//        $this->daochu($condition);
        $show = $Page->show();
        $this->assign('userList',$userList);
        $this->assign('level',M('user_level')->getField('level,level_name'));
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }
    //添加代理
    public function add_daili(){
        if(IS_POST){
            $data = I('post.');
            if($data['nickname']==''){
                $this->ajaxReturn(array('status'=>300,'msg'=>"请输入会员姓名"));
            }
            if(M('users')->where("mobile='{$data[mobile]}'")->count() > 0){
                $this->ajaxReturn(array('status'=>300,'msg'=>"手机号已注册"));
            }
            $user['level']=3;   //注册代理
            $user['nickname'] = $data['nickname'];
            $user['mobile'] = $data['mobile'];
            $user['password'] = md5($data['password']);
            $user['twopassword'] = md5($data['twopassword']);
            $user['user_money']=0;  //余额
            $user['reg_time'] = time();
            $data['is_lock']=0;
            $id = M('users')->add($user);
            if($id){
                //生成收货地址
                $addr['user_id']=$id;
                $addr['consignee']=$data['nickname'];
                $addr['mobile']=$data['mobile'];
                $addr['province']=$data['province'];
                $addr['city']=$data['city'];
                $addr['district']=$data['district'];
                $addr['address']=$data['address'];
                $addr['is_default']=1;
                M('user_address')->add($addr);
                $this->ajaxReturn(array('status'=>200,'msg'=>"注册成功"));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"注册失败"));
            }
        }
        $province = M('region')->where(array('parent_id'=>0))->select();
        $this->assign('province',$province);
        $this->display();
    }
    // author :凌寒 2019年1月25日09:53:37 导出会员表
    public function daochu(){
        $condition = array();
        $mobile = I('mobile');
        $where = 'where 1=1';
        if($mobile){
            $where .= " AND mobile = ".$mobile;
        }
        $name = I('nickname');
        if($name){
            $where .=" AND nickname = like '%$name%'";
        }

        if(I('level')>0){
            $where .= " AND level = ".I('level');
        }

        $sql = "select * from __PREFIX__users $where order by user_id desc";

        $orderList = D()->query($sql);

        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">ID</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员姓名</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">手机号码</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">会员等级</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">推荐人</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">推荐人编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">接点人</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">接点人编号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">累计消费</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">现金币</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">购物币</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">注册日期</td>';
        $strTable .= '</tr>';
        if(is_array($orderList)){
            foreach($orderList as $k=>$val){
                $level_name = M("user_level")->where("level=".$val['level'])->getField("level_name");
                if($val['pid']>0){
                    $p_name = M("users")->where("user_id=".$val['pid'])->getField("nickname");
                }else{
                    $p_name  =  "---";
                }
                if($val['node_id']){
                    $n_name = M("users")->where("user_id=".$val['node_id'])->getField("nickname");
                }else{
                    $n_name = "---";
                }
                $r_time = date("Y-m-d",$val['reg_time']);
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['user_id'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['nickname'].' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['user_code'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['mobile'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$level_name.'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$p_name.'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pcode'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$n_name.'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['node_code'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['total_amount'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['award_money'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['shop_money'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$r_time.'</td>';
                $strTable .= '</tr>';
            }
        }
        $strTable .='</table>';
        unset($orderList);
        downloadExcel($strTable,'User');
        exit();
    }
    /**
     * 会员详细信息查看
     */
    public function detail(){
        $uid = I('get.id');
        $user = D('users')->where(array('user_id'=>$uid))->find();
        if(!$user)
            exit($this->error('会员不存在'));
        if(IS_POST){
//            //  会员信息编辑
//            $mobile = I("mobile");
//            if(M('users')->where("mobile='{$mobile}' and user_id != {$uid}")->count()>=3){
//                $this->error('手机号已注册',U('User/index'));
//            }

            $password = I('post.password');
//            $password2 = I('post.password2');
//            if($password != '' && $password != $password2){
//                exit($this->error('两次输入密码不同'));
//            }
//            if($password == '' && $password2 == ''){
//                unset($_POST['password']);
//            }else{
                $_POST['password'] = MD5($_POST['password']);
//            }

            $twopassword = I('post.twopassword');
//            $twopassword2= I('post.twopassword2');
//            if($twopassword != '' && $twopassword != $twopassword2){
//                exit($this->error('两次输入的二级密码不同'));
//            }
//            if($twopassword == '' && $twopassword2 == ''){
//                unset($_POST['twopassword']);
//            }else{
                $_POST['twopassword'] = MD5($_POST['twopassword']);
//            }

            $row = M('users')->where(array('user_id'=>$uid))->save($_POST);
            if($row)
                exit($this->success('修改成功'));
            exit($this->error('未作内容修改或修改失败'));
        }
        $user_level = M("user_level")->order("level asc")->select();
        foreach ($user_level as $kk=>$vv){
            if($user['level']==$vv['level']){
                $user_level[$kk]['is_check']=1;
            }
        }
        $user['level_name'] = M("user_level")->where("level=".$user['level'])->getField("level_name");
        $user['province_name'] = M('region')->where(array('id'=>$user['province']))->getField("name");
        $user['city_name'] =  M('region')->where(array('id'=>$user['city']))->getField("name");
        $user['district_name']  =  M('region')->where(array('id'=>$user['district']))->getField("name");
        //获取省份
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $c = M('region')->where(array('parent_id' => $user['province'], 'level' => 2))->select();
        $d = M('region')->where(array('parent_id' => $user['city'], 'level' => 3))->select();
        $this->assign('province', $p);
        $this->assign('city', $c);
        $this->assign('district', $d);
        $this->assign('user_level',$user_level);
        $this->assign('user',$user);
        $this->display();
    }
    //注册会员
    public function add_user(){
        $config = getConfig();
    	if(IS_POST){
    		$data = I('post.');
            if($data['pcode']){
                $p_info = M("users")->where(array('user_code'=>$data['pcode']))->find();
                if(!$p_info){
                    $this->ajaxReturn(array('status'=>300,'msg'=>"推荐码不存在"));
                }
                $user['pid'] = $p_info['user_id'];
                $user['pcode'] = $p_info['user_code'];
                $user['pmobile'] = $p_info['mobile'];
            }
            if($data['nickname']==''){
                $this->ajaxReturn(array('status'=>300,'msg'=>"请输入昵称"));
            }
            if(M('users')->where("mobile='{$data[mobile]}'")->count() > 0){
                $this->ajaxReturn(array('status'=>300,'msg'=>"手机号已注册"));
            }
            $user['user_code'] = userCode($data['mobile']);
            $user['level']=1;   //注册会员
            $user['nickname'] = $data['nickname'];
            $user['mobile'] = $data['mobile'];
            $user['password'] = md5($data['password']);
            $user['twopassword'] = md5($data['twopassword']);
            $user['user_money']=0;  //余额
            $user['pt_num']=0;  //拼团次数
            $user['reg_time'] = time();
            $user['is_lock']=1;  //已激活
            $id = M('users')->add($user);
			if($id){
                relation($id);     //记录推荐关系
//                //奖励推荐值
//                if($config['basic_recom_num']>0 && $user['pid']){
//                    M("users")->where("user_id=".$user['pid'])->setInc("recom_num",$config['basic_recom_num']);
//                    upd_recom($user['pid'],$config['basic_recom_num'],1,"推荐好友注册奖励",1,$id);
//                }
                $this->ajaxReturn(array('status'=>200,'msg'=>"注册成功"));
			}else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"注册失败"));
			}
    	}
        $province = M('region')->where(array('parent_id'=>0))->select();
        $this->assign('province',$province);
    	$this->display();
    }
    public function ceshi(){
        $res = update_number(4,5);
        echo "<pre>";var_dump($res);die;
    }
    //更新团队数量和直推数量,自己购买量 2 更新创业基金额度  3会员升级分销商
    public function update_buy_number($user_id){
        $config = getConfig();
        $user = M('users')->where("user_id = '{$user_id}'")->find();
        M('users')->where("user_id = '{$user_id}'")->setInc('buy_num',1); // 更新自己销售业绩
        M('users')->where("user_id = '{$user_id}'")->setInc('team_num',1); //更新自己团队销售业绩
        if($user['pid']){

            M('users')->where("user_id = ".$user['pid'])->setInc('zt_num',1);   //更新直推销售额
            M('users')->where("user_id = ".$user['pid'])->setInc('cyjj',$config['basic_per_zt_num']); // 更新创业基金额度
            $parent = M('users')->where("user_id = ".$user['pid'])->field("user_id,level,zt_num")->find();
            if($parent['zt_num']==$config['basic_zt_num'] && $parent['level']==1){      //会员升级分销商
                M('users')->where("user_id = ".$parent['user_id'])->save(array('level'=>2));
            }

        }
        //更新上级团队业绩
        $relation = M("user_relation")->where("user_id=".$user_id)->order("depth asc")->select();
        if($relation){
            foreach ($relation as $kk=>$vv){
                M('users')->where("user_id = ".$vv['parent_user_id'])->setInc('team_num',1); //更新团队销售业绩
            }
        }
        return true;
    }
    //添加订单
    public function addOrder($user_id,$address_id)
    {
        //套餐产品
        $goods = M("goods")->where("is_members=1")->find();
        // 0插入订单 order
        $address = M('UserAddress')->where("address_id = $address_id")->find();
        $data = array(
            'order_sn'         => date('YmdHis').rand(1000,9999), // 订单编号
            'user_id'          =>$user_id, // 用户id
            'consignee'        =>$address['consignee'], // 收货人
            'province'         =>$address['province'],//'省份id',
            'city'             =>$address['city'],//'城市id',
            'district'         =>$address['district'],//'县',
            'twon'             =>$address['twon'],// '街道',
            'address'          =>$address['address'],//'详细地址',
            'mobile'           =>$address['mobile'],//'手机',
            'goods_price'      =>$goods['shop_price'],//'商品价格',
            'total_amount'     =>$goods['shop_price'],// 订单总额
            'order_amount'     =>$goods['shop_price'],//'应付款金额',
            'add_time'         =>time(), // 下单时间
            'pay_time'         =>time(), //支付时间
            'pay_status'       =>1, //支付状态
            'is_members'       =>1, //套餐产品
        );
        $order_id = M("Order")->add($data);
        // 记录订单操作日志
        logOrder($order_id,'注册提交订单，请等待系统确认','提交订单',$user_id);

        // 1插入order_goods 表
        $data2['order_id']           = $order_id; // 订单id
        $data2['goods_id']           = $goods['goods_id']; // 商品id
        $data2['goods_name']         = $goods['goods_name']; // 商品名称
        $data2['goods_sn']           = $goods['goods_sn']; // 商品货号
        $data2['goods_num']          = 1; // 购买数量
        $data2['market_price']       = $goods['market_price']; // 市场价
        $data2['goods_price']        = $goods['shop_price']; // 商品价
        $data2['member_goods_price'] = $goods['shop_price']; // 会员折扣价
        $data2['cost_price']         = $goods['cost_price']; // 成本价
        $data2['prom_type']          = 0; // 0 普通订单
        M("OrderGoods")->add($data2);
        //减库存
        M('Goods')->where("goods_id = {$goods['goods_id']}")->setDec('store_count',1); // 直接扣除商品总数量
    }
    //见点奖
    public function jiandian($user_id){
        $config = getConfig();
        $user = M('users')->where("user_id = '{$user_id}'")->field("user_id,level,user_money,pid")->find();
        //直推奖励
        if($user['pid']){
            $far = M('users')->where("user_id = '{$user[pid]}'")->field("user_id,level,user_money")->find();
            $awards = $config['basic_jd_fx_money'];
            if($awards > 0){
                M("users")->where("user_id=".$far['user_id'])->setInc("user_money",$awards);
                upd_money($far['user_id'],$awards,1,"直推奖励".$awards."元",2,$user_id);
            }
        }
        //代理商奖励
        $res = M("user_relation")->where("user_id=".$user_id)->order("depth asc")->select();
        if($res){
            foreach ($res as $kk=>$vv){
                $parent = M('users')->where("user_id = '{$vv[parent_user_id]}'")->field("user_id,level,user_money")->find();
                if ($parent['level']==3){
                    $award = $config['basic_jd_dl_money'];
                }else{
                    $award = 0;
                }
                if($award > 0){
                    M("users")->where("user_id=".$parent['user_id'])->setInc("user_money",$award);
                    upd_money($parent['user_id'],$award,1,"代理商奖励".$award."元",1,$user_id);
                }
            }
        }
    }
    // author :凌寒 2018年12月11日16:32:18 生成唯一的编码
    public function userCode($phone,$randCode = '')
    {
        $code = substr($phone,6,5).$randCode;

        $is = M('users')->where('user_code='.$code)->find();

        if($is){
            return $this->userCode($code,mt_rand(1,999));
        }else{
            return $code;
        }
    }
    /**
     * 用户收货地址查看
     */
    public function address(){
        $uid = I('get.id');
        $lists = D('user_address')->where(array('user_id'=>$uid))->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['p_name'] = M('region')->where(array('id'=>$vv['province']))->getField("name");
            $lists[$kk]['c_name'] =  M('region')->where(array('id'=>$vv['city']))->getField("name");
            $lists[$kk]['d_name']  =  M('region')->where(array('id'=>$vv['district']))->getField("name");
        }
        $this->assign('lists',$lists);
        $this->display();
    }

    /**
     * 删除会员
     */
    public function delete(){
        $uid = I('get.id');
        $row = M('users')->where(array('user_id'=>$uid))->delete();
        if($row){
            $this->success('成功删除会员');
        }else{
            $this->error('操作失败');
        }
    }
    // author :凌寒 2018年12月10日21:50:11 推荐关系图
    public function relation(){

        $users= M('users')->field('nickname,mobile,user_id,pid,user_code,user_money,level')->select();
//        foreach ($users as $kk=>$vv){
//            $users[$kk]['level_name'] = M("user_level")->where("level=".$vv['level'])->getField("level_name");
//        }
        $res = $this->generateTree($users);
        foreach ($res as $kk=>$vv){
            if(!$vv['pid']){
                $res[$kk]['pid'] = 0;
            }
        }
        $this->assign('list',json_encode($res));
        $this->display();
    }
    function generateTree($list, $pk = 'user_id', $pid = 'pid', $child = 'children', $root = '')
    {
        $packData = array();
        foreach ($list as $kk=> $data) {
            $packData[$kk]['id'] = $data[$pk];
            $packData[$kk]['name'] = '[姓名:'.$data['nickname'].'][手机号:'.$data['mobile'].']';
            $packData[$kk]['pid'] = $data[$pid];
        }
        return $packData;
    }
    // author :凌寒 2018年12月28日10:30:23 会员结构图
    public function structure(){
        if(I('user_num')){
            $user_num = I('user_num');
        }else{
            $small = M('users')->order('user_id asc')->find();
            $user_num = $small['user_code'];
        }

        $map['user_code']= $user_num;

        $huiyuan=M('users')->where($map)->find();

        if($huiyuan){

            if(I('user_num')){
                $huiyuan=M('users')->where(array('user_code'=>$huiyuan['user_code']))->find();
            }else{
                $huiyuan=M('users')->where(array('user_code'=>$user_num))->find();

                $this->assign('huiyuan_not_found',1);
            }

        }else{
            $huiyuan=M('users')->where(array('user_code'=>$user_num))->find();

            $this->assign('huiyuan_not_found',1);
        }
        $this->assign('user_num',$user_num);
        $lr = $this->lr_num($huiyuan['user_id']);
        $huiyuan['l_num'] =$lr['l_num'];
        $huiyuan['r_num'] =$lr['r_num'];
        $this->assign('huiyuan',$huiyuan);
//        dump($huiyuan);
        if($huiyuan){

            $huiyuan1=$this->getzuo($huiyuan['user_code']);
            $huiyuan1_rl = $this->lr_num($huiyuan1['user_id']);
            $huiyuan1['l_num'] =$huiyuan1_rl['l_num'];
            $huiyuan1['r_num'] =$huiyuan1_rl['r_num'];
            $huiyuan2=$this->getyou($huiyuan['user_code']);
            $huiyuan2_rl = $this->lr_num($huiyuan2['user_id']);
            $huiyuan2['l_num'] =$huiyuan2_rl['l_num'];
            $huiyuan2['r_num'] =$huiyuan2_rl['r_num'];

            $this->assign('huiyuan1',$huiyuan1);
            $this->assign('huiyuan2',$huiyuan2);

        }

        if($huiyuan1){
            $huiyuan11=$this->getzuo($huiyuan1['user_code']);
            $huiyuan11_rl = $this->lr_num($huiyuan11['user_id']);
            $huiyuan11['l_num'] =$huiyuan11_rl['l_num'];
            $huiyuan11['r_num'] =$huiyuan11_rl['r_num'];
            $huiyuan12=$this->getyou($huiyuan1['user_code']);
            $huiyuan12_rl = $this->lr_num($huiyuan12['user_id']);
            $huiyuan12['l_num'] =$huiyuan12_rl['l_num'];
            $huiyuan12['r_num'] =$huiyuan12_rl['r_num'];

            $this->assign('huiyuan11',$huiyuan11);
            $this->assign('huiyuan12',$huiyuan12);

        }

        if($huiyuan2){
            $huiyuan21=$this->getzuo($huiyuan2['user_code']);
            $huiyuan21_rl = $this->lr_num($huiyuan21['user_id']);
            $huiyuan21['l_num'] =$huiyuan21_rl['l_num'];
            $huiyuan21['r_num'] =$huiyuan21_rl['r_num'];
            $huiyuan22=$this->getyou($huiyuan2['user_code']);
            $huiyuan22_rl = $this->lr_num($huiyuan22['user_id']);
            $huiyuan22['l_num'] =$huiyuan22_rl['l_num'];
            $huiyuan22['r_num'] =$huiyuan22_rl['r_num'];

            $this->assign('huiyuan21',$huiyuan21);
            $this->assign('huiyuan22',$huiyuan22);

        }

        if($huiyuan11){
            $huiyuan111=$this->getzuo($huiyuan11['user_code']);
            $huiyuan111_rl = $this->lr_num($huiyuan111['user_id']);
            $huiyuan111['l_num'] =$huiyuan111_rl['l_num'];
            $huiyuan111['r_num'] =$huiyuan111_rl['r_num'];
            $huiyuan112=$this->getyou($huiyuan11['user_code']);
            $huiyuan112_rl = $this->lr_num($huiyuan112['user_id']);
            $huiyuan112['l_num'] =$huiyuan112_rl['l_num'];
            $huiyuan112['r_num'] =$huiyuan112_rl['r_num'];

            $this->assign('huiyuan111',$huiyuan111);
            $this->assign('huiyuan112',$huiyuan112);

        }

        if($huiyuan21){
            $huiyuan211=$this->getzuo($huiyuan21['user_code']);
            $huiyuan211_rl = $this->lr_num($huiyuan211['user_id']);
            $huiyuan211['l_num'] =$huiyuan211_rl['l_num'];
            $huiyuan211['r_num'] =$huiyuan211_rl['r_num'];
            $huiyuan212=$this->getyou($huiyuan21['user_code']);
            $huiyuan212_rl = $this->lr_num($huiyuan212['user_id']);
            $huiyuan212['l_num'] =$huiyuan212_rl['l_num'];
            $huiyuan212['r_num'] =$huiyuan212_rl['r_num'];

            $this->assign('huiyuan211',$huiyuan211);
            $this->assign('huiyuan212',$huiyuan212);

        }

        if($huiyuan12){
            $huiyuan121=$this->getzuo($huiyuan12['user_code']);
            $huiyuan121_rl = $this->lr_num($huiyuan121['user_id']);
            $huiyuan121['l_num'] =$huiyuan121_rl['l_num'];
            $huiyuan121['r_num'] =$huiyuan121_rl['r_num'];
            $huiyuan122=$this->getyou($huiyuan12['user_code']);
            $huiyuan122_rl = $this->lr_num($huiyuan122['user_id']);
            $huiyuan122['l_num'] =$huiyuan122_rl['l_num'];
            $huiyuan122['r_num'] =$huiyuan122_rl['r_num'];

            $this->assign('huiyuan121',$huiyuan121);
            $this->assign('huiyuan122',$huiyuan122);

        }

        if($huiyuan22){
            $huiyuan221=$this->getzuo($huiyuan22['user_code']);
            $huiyuan221_rl = $this->lr_num($huiyuan221['user_id']);
            $huiyuan221['l_num'] =$huiyuan221_rl['l_num'];
            $huiyuan221['r_num'] =$huiyuan221_rl['r_num'];
            $huiyuan222=$this->getyou($huiyuan22['user_code']);
            $huiyuan222_rl = $this->lr_num($huiyuan222['user_id']);
            $huiyuan222['l_num'] =$huiyuan222_rl['l_num'];
            $huiyuan222['r_num'] =$huiyuan222_rl['r_num'];
            $this->assign('huiyuan221',$huiyuan221);
            $this->assign('huiyuan222',$huiyuan222);

        }

        $data = [
            $huiyuan,
            $huiyuan1,
            $huiyuan2,
        ];
        $this->display();
    }
    public function getzuo($user_num){

        $huiyuan=M('users')->where("node_code='".$user_num."' and market=0 and level >=2")->find();
        return $huiyuan;
    }

    public function getyou($user_num){
        $huiyuan=M('users')->where("node_code='".$user_num."' and market=1 and level >=2")->find();
        return $huiyuan;
    }
    public function lr_num($user_id){
        if($user_id){
            $l_num = M("user_node")->where("userid=".$user_id." and shichang = 0")->count();  //左区人数
            $r_num = M("user_node")->where("userid=".$user_id." and shichang = 1")->count();  //右区人数
            return array('l_num'=>$l_num,'r_num'=>$r_num);
        }
    }
    // author :凌寒 2018-12-30 14:15:22 奖金币转购物币互转记录
    public function jg_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['create_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        $count = M('huzhuan_zr_log')->where($map)->count();

        $page = new Page($count);
        $lists  = M('huzhuan_zr_log')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['user_name'] = M("users")->where("user_id=".$vv['user_id'])->getField("nickname");
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // 贡献值记录
    public function devote_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $type=I("type");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['create_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($type){
            $map['type'] = $type;
        }
        $count = M('user_devote')->where($map)->count();
        $page = new Page($count);
        $lists  = M('user_devote')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['user_name'] = M("users")->where("user_id=".$vv['user_id'])->getField("nickname");
            if($vv['from_user_id']){
                $lists[$kk]['from_user_name'] = M("users")->where("user_id=".$vv['from_user_id'])->getField("nickname");
            }
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // 活跃度记录
    public function active_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $type=I("type");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['create_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($type){
            $map['type'] = $type;
        }
        $count = M('user_active')->where($map)->count();
        $page = new Page($count);
        $lists  = M('user_active')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['user_name'] = M("users")->where("user_id=".$vv['user_id'])->getField("nickname");
            if($vv['from_user_id']){
                $lists[$kk]['from_user_name'] = M("users")->where("user_id=".$vv['from_user_id'])->getField("nickname");
            }
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // 推荐值记录
    public function recom_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $type=I("type");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['create_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($type){
            $map['type'] = $type;
        }

        $count = M('user_recom')->where($map)->count();

        $page = new Page($count);
        $lists  = M('user_recom')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['user_name'] = M("users")->where("user_id=".$vv['user_id'])->getField("nickname");
            if($vv['from_user_id']){
                $lists[$kk]['from_user_name'] = M("users")->where("user_id=".$vv['from_user_id'])->getField("nickname");
            }
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // author :凌寒 2018-12-30 14:15:22 消费积分记录
    public function jifen_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $type=I("type");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['create_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($type){
            $map['type'] = $type;
        }

        $count = M('user_jifen')->where($map)->count();

        $page = new Page($count);
        $lists  = M('user_jifen')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['user_name'] = M("users")->where("user_id=".$vv['user_id'])->getField("nickname");
            if($vv['from_user_id']){
                $lists[$kk]['from_user_name'] = M("users")->where("user_id=".$vv['from_user_id'])->getField("nickname");
            }
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // author :凌寒 2018-12-30 14:15:22 红包记录
    public function hongbao_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $is_ling=I("is_ling");
        $is_yx = I("is_yx");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['create_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($is_ling=='1'){
            $map['is_ling'] = 1;
        }
        if($is_ling=='2'){
            $map['is_ling'] = 0;
        }
        if($is_yx=='1'){
            $map['is_yx'] = 1;
        }
        if($is_yx=='2'){
            $map['is_yx'] = 0;
        }
        $count = M('hongbao')->where($map)->count();
        $page = new Page($count,15);
        $lists  = M('hongbao')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['user_name'] = M("users")->where("user_id=".$vv['user_id'])->getField("nickname");
        }
        $this->assign('show',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // author :凌寒 2018-12-30 14:15:22 余额记录
    public function money_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $type=I("type");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['create_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($type){
            $map['type'] = $type;
        }

        $count = M('user_money')->where($map)->count();

        $page = new Page($count);
        $lists  = M('user_money')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['user_name'] = M("users")->where("user_id=".$vv['user_id'])->getField("nickname");
            if($vv['from_user_id']){
                $lists[$kk]['from_user_name'] = M("users")->where("user_id=".$vv['from_user_id'])->getField("nickname");
            }
        }
//        echo "<pre>";
//        var_dump($page->show());die;
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // author :凌寒 2018-12-30 14:15:22 奖金记录
    public function award_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $type=I("type");
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['create_time'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        if($type){
            $map['type'] = $type;
        }

        $count = M('user_award')->where($map)->count();

        $page = new Page($count);
        $lists  = M('user_award')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['user_name'] = M("users")->where("user_id=".$vv['user_id'])->getField("nickname");
            if($vv['from_user_id']){
                $lists[$kk]['from_user_name'] = M("users")->where("user_id=".$vv['from_user_id'])->getField("nickname");
            }
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // author :凌寒 2018-12-30 14:15:22 奖金币和购物币互转记录
    public function hz_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $type=I("type");
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
        if($type){
            $map['type'] = $type;
        }
        $count = M('user_exchange')->where($map)->count();
        $page = new Page($count);
        $lists  = M('user_exchange')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $zc_user = M("users")->where("user_id=".$vv['user_id'])->field("user_id,nickname,mobile")->find();
            $lists[$kk]['user_name'] = $zc_user['nickname'];
            $lists[$kk]['mobile'] = $zc_user['mobile'];
            $zr_user = M("users")->where("user_id=".$vv['to_user_id'])->field("user_id,nickname,mobile")->find();
            $lists[$kk]['to_user_name'] = $zr_user['nickname'];
            $lists[$kk]['to_mobile'] = $zr_user['mobile'];
        }
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    // author :凌寒 2018-12-30 14:15:22 会员升级记录
    public function level_log(){
        $timegap = I('timegap');
        $nickname = I('nickname');
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['ctime'] = array('between',array(strtotime($begin),strtotime($end)));
        }
        if($nickname){
            $map['nickname'] = array('like',"%$nickname%");
        }
        $count = M('update_level')->where($map)->count();
        $page = new Page($count);
        $lists  = M('update_level')->where($map)->order('add_time desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            $lists[$kk]['level_name'] = M("user_level")->where("level=".$vv['level'])->getField("level_name");
        }
        $this->assign('page',$page->show());
        $this->assign('lists',$lists);
        $this->display();
    }
    /**
     * 账户资金记录
     */
    public function account_log(){
        $user_id = I('get.id');
        //获取类型
        $type = I('get.type');
        //获取记录总数
        $count = M('account_log')->where(array('user_id'=>$user_id))->count();
        $page = new Page($count);
        $lists  = M('account_log')->where(array('user_id'=>$user_id))->order('change_time desc')->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($lists as $kk=>$vv){
            if($vv['from_user_id']){
                $lists[$kk]['from_user_name'] = M("users")->where(array('user_id'=>$vv['from_user_id']))->getField("nickname");
            }
        }
        $this->assign('user_id',$user_id);
        $this->assign('page',$page->show());
        $this->assign('lists',$lists);
        $this->display();
    }

    /**
     * 账户资金调节
     */
    public function account_edit(){
        $user_id = I('get.id');
        if(!$user_id > 0)
            $this->error("参数有误");
        if(IS_POST){
            //获取操作类型
            $m_op_type = I('post.money_act_type');
            $user_money = I('post.user_money');
            $user_money =  $m_op_type ? $user_money : 0-$user_money;

            $p_op_type = I('post.point_act_type');
            $pay_points = I('post.pay_points');
            $pay_points =  $p_op_type ? $pay_points : 0-$pay_points;

            $f_op_type = I('post.frozen_act_type');
            $frozen_money = I('post.frozen_money');
            $frozen_money =  $f_op_type ? $frozen_money : 0-$frozen_money;

            $user_info = M("users")->where("user_id=".$user_id)->field('user_id,user_money,pay_points,level')->find();
            $shop_money = $user_info['user_money']+$user_money;
            $award_money = $user_info['pay_points']+$pay_points;

            if($res = M("users")->where("user_id=".$user_id)->save(array('user_money'=>$shop_money))){
                upd_money($user_id,$user_money,1,"平台充值余额",2);
            }

            if( $ress = M("users")->where("user_id=".$user_id)->save(array('pay_points'=>$award_money))){
                upd_jifen($user_id,$pay_points,1,"平台充值积分",2);
            }
            $this->success("操作成功",U("Admin/User/index"));
            exit;
        }
        $this->assign('user_id',$user_id);
        $this->display();
    }
    //股东退股审核
    public function loan(){
        $model = M("user_back");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);
        $status = I('status');
        $nickname= I('nickname');
        $where = array();
        if($status != '') $where['status'] = $status;
        if($nickname){
            $user = M("users")->where("nickname like '%$nickname%'")->find();
            if($user){
                $where['user_id'] = $user['user_id'];
            }

        }
        $create_time = I('create_time');
        $create_time = $create_time  ? $create_time  : date('Y/m/d',strtotime('-1 year')).'-'.date('Y/m/d',strtotime('+1 day'));
        $create_time2 = explode('-',$create_time);
        $where['create_time'] = array('between',array(strtotime($create_time2[0]),strtotime($create_time2[1])));
        $count = $model->where($where)->count();
        $Page  = new Page($count,10);
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $kk=>$vv){
            $list[$kk]['level_name'] = M("user_level")->where(array('level'=>$vv['level']))->getField("level_name");
            $list[$kk]['nickname'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField("nickname");
        }
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        $this->display();
    }
    //退股审核
    public function loan_shenhe(){
        $model = M("user_back");
        $id = I('id');
        if(!$id){
            $this->ajaxReturn(array('msg'=>'缺少参数','state'=>300));
        }
        $dk_info = $model->where(array('id'=>$id))->find();
        $info = array(
            'status' => I('status'),
            'identify_time' => time(),
        );
        $model->where(array('id'=>$id))->save($info);
        if(I('status')==1){
            M("users")->where("user_id=".$dk_info['user_id'])->save(array('level'=>1));
        }
        $this->ajaxReturn(array('msg'=>'审核成功','url'=>U('User/loan'),'state'=>200));
    }
    // author :凌寒 报单申请记录
    public function baodan(){
        $model = M("baodan");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);
        $status = I('status');
        $nickname= I('nickname');
        $where = array();
        if($status != '') $where['status'] = $status;
        if($nickname) $where['reg_user_name'] = $nickname;
        $create_time = I('create_time');
        $create_time = $create_time  ? $create_time  : date('Y/m/d',strtotime('-1 year')).'-'.date('Y/m/d',strtotime('+1 day'));
        $create_time2 = explode('-',$create_time);
        $where['create_time'] = array('between',array(strtotime($create_time2[0]),strtotime($create_time2[1])));
        $count = $model->where($where)->count();
        $Page  = new Page($count,10);
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $kk=>$vv){
            $list[$kk]['p_name'] = M('region')->where("id=".$vv['province'])->getField("name");
            $list[$kk]['c_name'] = M('region')->where("id=".$vv['city'])->getField("name");
            $list[$kk]['d_name'] = M('region')->where("id=".$vv['district'])->getField("name");
        }
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        $this->display();
    }
    //报单审核
    function add_baodan(){
        if(IS_POST){
            $data = I('post.');
            if($data['pmobile']){
                $p_info = M("users")->where(array('mobile'=>$data['pmobile']))->find();
                if(!$p_info){
                    $this->ajaxReturn(array('status'=>300,'msg'=>"推荐人手机号不存在"));
                }
                $user['pid'] = $p_info['user_id'];
                $user['pmobile'] = $p_info['mobile'];
            }
            if($data['nickname']==''){
                $this->ajaxReturn(array('status'=>300,'msg'=>"请输入会员姓名"));
            }
            if(M('users')->where("mobile='{$data[mobile]}'")->count() > 0){
                $this->ajaxReturn(array('status'=>300,'msg'=>"手机号已注册"));
            }
            if($data['remark']==''){
                $this->ajaxReturn(array('status'=>300,'msg'=>"请输入审核通过描述"));
            }
            $user['level']=1;   //注册会员
            $user['nickname'] = $data['nickname'];
            $user['mobile'] = $data['mobile'];
            $user['password'] = md5($data['password']);
            $user['twopassword'] = md5($data['twopassword']);
            $user['user_money']=0;  //余额
            $user['reg_time'] = time();
            $data['is_lock']=0;
            $id = M('users')->add($user);
            if($id){
                //更改审核状态
                $sh['status']=1;
                $sh['remark']=$data['remark'];
                $sh['identify_time']=time();
                M("baodan")->where("id=".$data['id'])->save($sh);
                //生成收货地址
                $addr['user_id']=$id;
                $addr['consignee']=$data['nickname'];
                $addr['mobile']=$data['mobile'];
                $addr['province']=$data['province'];
                $addr['city']=$data['city'];
                $addr['district']=$data['district'];
                $addr['address']=$data['address'];
                $addr['is_default']=1;
                $address_id = M('user_address')->add($addr);
                //生成发货订单
                $this->addOrder($id,$address_id);
                //记录推荐关系
                $this->relations($id);
                //更新直推/团队和创业基金额度 会员升级分销商
                $this->update_buy_number($id);
                //见点奖
                $this->jiandian($id);
                $this->ajaxReturn(array('status'=>200,'msg'=>"审核成功"));
            }else{
                $this->ajaxReturn(array('status'=>300,'msg'=>"审核失败"));
            }
        }
        $id = I('get.id');
        $info = D('baodan')->where(array('id'=>$id))->find();
        $info['p_name'] = M('region')->where(array('id'=>$info['province']))->getField("name");
        $info['c_name'] =  M('region')->where(array('id'=>$info['city']))->getField("name");
        $info['d_name']  =  M('region')->where(array('id'=>$info['district']))->getField("name");
        //获取省份
        $p = M('region')->where(array('parent_id' => 0, 'level' => 1))->select();
        $c = M('region')->where(array('parent_id' => $info['province'], 'level' => 2))->select();
        $d = M('region')->where(array('parent_id' => $info['city'], 'level' => 3))->select();
        $this->assign('province', $p);
        $this->assign('city', $c);
        $this->assign('district', $d);
        $this->assign('info',$info);
        $this->display();
    }
    //报单审核拒绝
    public function baodan_shenhe(){
        $model = M("baodan");
        $id = I('id');
        if(!$id){
            $this->ajaxReturn(array('msg'=>'缺少参数','state'=>300));
        }
        $info = array(
            'remark' => I('remark'),
            'status' => 2,
            'identify_time' => time(),
        );
        $model->where(array('id'=>$id))->save($info);
        $this->ajaxReturn(array('msg'=>'审核成功','url'=>U('User/baodan'),'state'=>200));
    }
    // author :凌寒 代理申请记录
    public function agent(){
        $model = M("agent");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);
        $status = I('status');
        $nickname= I('nickname');
        $where = array();
        if($status != '') $where['status'] = $status;
        if($nickname){
            $user = M("users")->where("nickname like '%$nickname%'")->find();
            if($user){
                $where['user_id'] = $user['user_id'];
            }

        }
        $create_time = I('create_time');
        $create_time = $create_time  ? $create_time  : date('Y/m/d',strtotime('-1 year')).'-'.date('Y/m/d',strtotime('+1 day'));
        $create_time2 = explode('-',$create_time);
        $where['create_time'] = array('between',array(strtotime($create_time2[0]),strtotime($create_time2[1])));
        $count = $model->where($where)->count();
        $Page  = new Page($count,10);
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $kk=>$vv){
            $list[$kk]['nickname'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField("nickname");
        }
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        $this->display();
    }
    // author :凌寒 代理审核
    public function agent_shenhe(){
        $model = M("agent");
        $id = I('id');
        if(!$id){
            $this->ajaxReturn(array('msg'=>'缺少参数','state'=>300));
        }
        $agent_info = $model->where(array('id'=>$id))->find();
        $info = array(
            'remark' => I('remark'),
            'status' => I('status'),
            'identify_time' => time(),
        );
        $model->where(array('id'=>$id))->save($info);
        if(I('status')==1){
            $user = M("users")->where("user_id=".$agent_info['user_id'])->field("user_id,pid,level,zt_num,team_num")->find();
            $parent = M("users")->where("user_id=".$user['pid'])->field("user_id,pid,level,zt_num,team_num")->find();
            //更改申请人的等级为代理商 删除推荐人
            M("users")->where("user_id=".$user['user_id'])->save(array('level'=>3,'pid'=>null,'pmobile'=>null));
            //更新推荐人的直推人数和团队人数
            M("users")->where("user_id=".$parent['user_id'])->setDec('zt_num',1);
            M("users")->where("user_id=".$parent['user_id'])->setDec('team_num',$user['team_num']);
            //删除会员关系
            $child = M("user_relation")->where("parent_user_id=".$user['user_id'])->order("depth desc")->field("user_id,depth,parent_user_id")->select();
            if($child){
                foreach ($child as $kk=>$vv){
                    M("user_relation")->where("user_id=".$vv['user_id']." and depth > ".$vv['depth'])->delete();
                }
            }
            M("user_relation")->where("user_id=".$agent_info['user_id']."")->delete();
        }
        $this->ajaxReturn(array('msg'=>'审核成功','url'=>U('User/agent'),'state'=>200));
    }
    //签到记录
    public function sign_log(){
        $timegap = I('create_time');
        $nickname = I('nickname');
        $map = array();
        if($timegap){
            $gap = explode(' - ', $timegap);
            $begin = $gap[0];
            $end = $gap[1];
            $map['add_time'] = array('between',array(strtotime($begin." 00:00:00"),strtotime($end." 23:59:59")));
        }
        if($nickname){
            $user_id=M("users")->where("nickname like '%$nickname%'")->getField("user_id");
            $map['user_id'] = $user_id;
        }
        $count = M('user_sign')->where($map)->count();
        $page = new Page($count);
        $lists  = M('user_sign')->where($map)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show());
        $this->assign('list',$lists);
        $this->display();
    }
    //升级记录
    public function recharge_log(){
        $model = M("recharge");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);
        $type = I("type");
        $status = I('status');
        $nickname= I('nickname');
        $where = array();
        if($type == 1){
            $where['pay_code'] = "alipayMobile";
        }elseif ($type == 2){
            $where['pay_code'] = "weixin";
        }
        if($status != '') $where['status'] = $status;
        if($nickname){
            $user = M("users")->where("nickname like '%$nickname%'")->find();
            if($user){
                $where['user_id'] = $user['user_id'];
            }

        }
        $count = $model->where($where)->count();
        $Page  = new Page($count,10);
        $list = $model->where($where)->order("`order_id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $kk=>$vv){
            $list[$kk]['user_name'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField("nickname");
        }
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        $this->display();
    }
    public function recharge(){
        $model = M("recharge");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);
        $type = I("type");
        $status = I('status');
        $nickname= I('nickname');
        $where = array();
        if($type > 0) $where['type'] = $type;
        if($status != '') $where['status'] = $status;
        if($nickname){
            $user = M("users")->where("nickname like '%$nickname%'")->find();
            if($user){
                $where['user_id'] = $user['user_id'];
            }

        }

//        $create_time = I('create_time');
//        $create_time = $create_time  ? $create_time  : date('Y/m/d',strtotime('-1 year')).'-'.date('Y/m/d',strtotime('+1 day'));
//        $create_time2 = explode('-',$create_time);
//        $where = " create_time >= '".strtotime($create_time2[0])."' and create_time <= '".strtotime($create_time2[1])."' ";




        $count = $model->where($where)->count();
        $Page  = new Page($count,10);
        $list = $model->where($where)->order("`order_id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $kk=>$vv){
            $list[$kk]['user_name'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField("nickname");
            $list[$kk]['mobile'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField("mobile");
        }
//        $this->assign('create_time',$create_time);
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        $this->display();
    }
    // author :凌寒 2018年9月21日15:11:25 充值审核
    public  function chongzhi_identify(){
        $id = I('id');
        if(!$id){
            $this->ajaxReturn(array('msg'=>'缺少参数','state'=>300));
        }

        $model = M("chongzhi");
        $cz_info = $model->where(array('id'=>$id))->find();
        $info = array(
            'desc' => I('remark'),
            'status' => I('status'),
            'identify_time' => time(),
        );
        $model->where(array('id'=>$id))->save($info);
        if(I('status')==1){
            M("users")->where("user_id=".$cz_info['user_id'])->setInc("user_money",$cz_info['money']);
            upd_money($cz_info['user_id'],$cz_info['money'],1,"会员充值余额",1);
        }
        $this->ajaxReturn(array('msg'=>'审核成功','url'=>U('User/recharge'),'state'=>200));
    }
    public function level(){
    	$act = I('GET.act','add');
    	$this->assign('act',$act);
    	$level_id = I('GET.level_id');
    	$level_info = array();
    	if($level_id){
    		$level_info = D('user_level')->where('level_id='.$level_id)->find();
    		$this->assign('info',$level_info);
    	}
    	$this->display();
    }
    
    public function levelList(){
    	$Ad =  M('user_level');
    	$res = $Ad->where('1=1')->order('level_id')->page($_GET['p'].',10')->select();
    	if($res){
    		foreach ($res as $val){
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);
    	$count = $Ad->where('1=1')->count();
    	$Page = new \Think\Page($count,10);
    	$show = $Page->show();
    	$this->assign('page',$show);
    	$this->display();
    }
    
    public function levelHandle(){
    	$data = I('post.');
    	if($data['act'] == 'add'){
    	    $count = M("user_level")->where("level=".$data['level'])->count('level_id');
    	    if($count > 0){
                $this->error("操作失败",U('Admin/User/level'));
            }
    		$r = D('user_level')->add($data);
    	}
    	if($data['act'] == 'edit'){

    		$r = D('user_level')->where('level_id='.$data['level_id'])->save($data);
    	}
    	 
    	if($data['act'] == 'del'){
    		$r = D('user_level')->where('level_id='.$data['level_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	 
    	if($r){
    		$this->success("操作成功",U('Admin/User/levelList'));
    	}else{
    		$this->error("操作失败",U('Admin/User/levelList'));
    	}
    }

    /**
     * 搜索用户名
     */
    public function search_user()
    {
        $search_key = trim(I('search_key'));        
        if(strstr($search_key,'@'))    
        {
            $list = M('users')->where(" email like '%$search_key%' ")->select();        
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['email']}</option>";
            }                        
        }
        else
        {
            $list = M('users')->where(" mobile like '%$search_key%' ")->select();        
            foreach($list as $key => $val)
            {
                echo "<option value='{$val['user_id']}'>{$val['mobile']}</option>";
            }            
        } 
        exit;
    }
    
    /**
     * 分销树状关系
     */
    public function ajax_distribut_tree()
    {
          $list = M('users')->where("first_leader = 1")->select();
          $this->display();
    }

    /**
     *
     * @time 2016/08/31
     * @author dyr
     * 发送站内信
     */
    public function sendMessage()
    {
        $user_id_array = I('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $users = M('users')->field('user_id,nickname')->where(array('user_id' => array('IN', $user_id_array)))->select();
        }
        $this->assign('users',$users);
        $this->display();
    }

    /**
     * 发送系统消息
     * @author dyr
     * @time  2016/09/01
     */
    public function doSendMessage()
    {
        $call_back = I('call_back');//回调方法
        $message = I('post.text');//内容
        $type = I('post.type', 0);//个体or全体
        $admin_id = session('admin_id');
        $users = I('post.user');//个体id
        $message = array(
            'admin_id' => $admin_id,
            'message' => $message,
            'category' => 0,
            'send_time' => time()
        );
        if ($type == 1) {
            //全体用户系统消息
            $message['type'] = 1;
            M('Message')->data($message)->add();
        } else {
            //个体消息
            $message['type'] = 0;
            if (!empty($users)) {
                $create_message_id = M('Message')->data($message)->add();
                foreach ($users as $key) {
                    M('user_message')->data(array('user_id' => $key, 'message_id' => $create_message_id, 'status' => 0, 'category' => 0))->add();
                }
            }
        }
        echo "<script>parent.{$call_back}(1);</script>";
        exit();
    }

    /**
     *
     * @time 2016/09/03
     * @author dyr
     * 发送邮件
     */
    public function sendMail()
    {
        $user_id_array = I('get.user_id_array');
        $users = array();
        if (!empty($user_id_array)) {
            $user_where = array(
                'user_id' => array('IN', $user_id_array),
                'email' => array('neq', '')
            );
            $users = M('users')->field('user_id,nickname,email')->where($user_where)->select();
        }
        $this->assign('smtp', tpCache('smtp'));
        $this->assign('users', $users);
        $this->display();
    }

    /**
     * 发送邮箱
     * @author dyr
     * @time  2016/09/03
     */
    public function doSendMail()
    {
        $call_back = I('call_back');//回调方法
        $message = I('post.text');//内容
        $title = I('post.title');//标题
        $users = I('post.user');
        if (!empty($users)) {
            $user_id_array = implode(',', $users);
            $users = M('users')->field('email')->where(array('user_id' => array('IN', $user_id_array)))->select();
            $to = array();
            foreach ($users as $user) {
                if (check_email($user['email'])) {
                    $to[] = $user['email'];
                }
            }
            $res = send_email($to, $title, $message);
            echo "<script>parent.{$call_back}({$res});</script>";
            exit();
        }
    }
    /**
     * 积分提现
     */
    public function tx_log()
    {
        $model = M("user_tx");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);
        $type = I("type");
        $status = I('status');
        $nickname= I('nickname');
        $where = array();
        if($type > 0) $where['type'] = $type;
        if($status) $where['status'] = $status;
        if($nickname){
            $user = M("users")->where("nickname like '%".$nickname."%'")->find();
            if($user){
                $where['user_id'] = $user['user_id'];
            }
            $this->assign('nickname', $user['nickname']);
        }

        $count = $model->where($where)->count();
        $Page  = new Page($count,10);
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $kk=>$vv){
            $list[$kk]['user_name'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField("nickname");
            $list[$kk]['mobile'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField("mobile");
        }
//        echo "<pre>";
//        var_dump($list);die;
        $this->assign('type',$type);
        $this->assign('status',$status);
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        $this->display();
    }
    //积分提现审核
    public  function tx_sh(){
        $id = I('id');
        if(!$id){
            $this->ajaxReturn(array('msg'=>'缺少参数','state'=>300));
        }
        $model = M("user_tx");
        $cz_info = $model->where(array('id'=>$id))->find();
        $user = M("users")->where("user_id=".$cz_info['user_id'])->field("user_money,user_id")->find();
        $info = array(
            'remark' => I('remark'),
            'status' => I('status'),
            'identify_time' => time(),
        );
        $model->where(array('id'=>$id))->save($info);
        if(I('status')==1){  //申请成功
            upd_jifen($cz_info['user_id'],$cz_info['money'],0,"会员积分提现",4);
        }else{
            M("users")->where("user_id=".$cz_info['user_id'])->setInc("pay_points",$cz_info['money']);
        }
        $this->ajaxReturn(array('msg'=>'审核成功','url'=>U('User/tx_log'),'state'=>200));
    }
    /**
     * 提现申请记录
     */
    public function withdrawals()
    {
        $model = M("withdrawals");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);
        $type = I("type");
        $status = I('status');
        $nickname= I('nickname');
        $where = array();
        if($type > 0) $where['type'] = $type;
        if($status) $where['status'] = $status;
        if($nickname){
            $user = M("users")->where("nickname like '%".$nickname."%'")->find();
            if($user){
                $where['user_id'] = $user['user_id'];
            }
            $this->assign('nickname', $user['nickname']);
        }

        $count = $model->where($where)->count();
        $Page  = new Page($count,10);
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $kk=>$vv){
            $list[$kk]['user_name'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField("nickname");
            $list[$kk]['mobile'] = M("users")->where(array('user_id'=>$vv['user_id']))->getField("mobile");
        }

        $this->assign('type',$type);
        $this->assign('status',$status);
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        $this->display();
    }
    //提现审核
    public  function tx_identify(){
        $id = I('id');
        if(!$id){
            $this->ajaxReturn(array('msg'=>'缺少参数','state'=>300));
        }
        $model = M("withdrawals");
        $cz_info = $model->where(array('id'=>$id))->find();
        $user = M("users")->where("user_id=".$cz_info['user_id'])->field("user_money,user_id")->find();
        $info = array(
            'remark' => I('remark'),
            'status' => I('status'),
            'identify_time' => time(),
        );
        $model->where(array('id'=>$id))->save($info);
        if(I('status')==1){  //申请成功
            upd_money($cz_info['user_id'],$cz_info['money'],0,"会员提现",4);
        }else{
            M("users")->where("user_id=".$cz_info['user_id'])->setInc("user_money",$cz_info['money']);
        }
        $this->ajaxReturn(array('msg'=>'审核成功','url'=>U('User/withdrawals'),'state'=>200));
    }
    // author :凌寒 2019年5月14日09:26:59 设置创业基金
    public function cyjj(){
        $jh_user_id = I("jh_user_id");
        $jh_user = M("users")->where("user_id=".$jh_user_id)->find();
        if($jh_user['is_yyzx']==1){
            $res = M("users")->where("user_id=".$jh_user_id)->save(array('is_yyzx'=>0));
        }else{
            $res = M("users")->where("user_id=".$jh_user_id)->save(array('is_yyzx'=>1));
        }
        if($res){
            $this->ajaxReturn(array('status'=>200,'msg'=>"设置成功!"));
        }else{
            $this->ajaxReturn(array('status'=>300,'msg'=>"设置失败!"));
        }
    }
    // author :凌寒 2019年5月14日09:26:59 激活会员
    public function jihuo_user(){
        $config = getConfig();
        $reg_jifen = $config['basic_reg_jifen'];

        $jh_user_id = I("jh_user_id");
        $jh_user = M("users")->where("user_id=".$jh_user_id)->find();

        $tj_user =  M("users")->where("user_id=".$jh_user['pid'])->find();

        if($jh_user['is_lock']==1){
            $this->ajaxReturn(array('status'=>300,'msg'=>"该会员已激活!"));
        }
        if($tj_user['pay_points'] < $reg_jifen){
            $this->ajaxReturn(array('status'=>300,'msg'=>"推荐人积分不足!"));
        }

        M("users")->where("user_id=".$jh_user_id)->save(array('is_lock'=>1));

        //消耗推荐人消费积分给注册人
        M("users")->where("user_id=".$tj_user['user_id'])->setDec("pay_points",$reg_jifen);
        upd_jifen($tj_user['user_id'],$reg_jifen,0,"激活会员".$jh_user['nickname']."消耗",1);
        M("users")->where("user_id=".$jh_user_id)->setInc("pay_points",$reg_jifen);
        upd_jifen($jh_user_id,$reg_jifen,1,"激活赠送积分",2);

        $this->ajaxReturn(array('status'=>200,'msg'=>"激活成功!"));
    }

    /**
     * 删除申请记录
     */
    public function delWithdrawals()
    {
        $model = M("withdrawals");
        $model->where('id ='.$_GET['id'])->delete();
        $return_arr = array('status' => 1,'msg' => '操作成功','data'  =>'',);   //$return_arr = array('status' => -1,'msg' => '删除失败','data'  =>'',);
        $this->ajaxReturn(json_encode($return_arr));
    }

    /**
     * 修改编辑 申请提现
     */
    public  function editWithdrawals(){
        $id = I('id');
        $model = M("withdrawals");
        $withdrawals = $model->find($id);
        $user = M('users')->where("user_id = {$withdrawals[user_id]}")->find();

        if(IS_POST)
        {

            $w_id = I('id');
            $withdrawal = M("withdrawals")->find($w_id);
            $user_id = I("user_id");
            $status = I("status");
            $remark = I("remark");
            $res = M("withdrawals")->where(array('id'=>$w_id))->save(array('status'=>$status,'remark'=>$remark));
            if($res){
                if($status==1){
                    M("users")->where(array('user_id'=>$user_id))->setDec('frozen_money',$withdrawal['last_money']);
                }else{
                    M("users")->where(array('user_id'=>$user_id))->setDec('frozen_money',$withdrawal['last_money']);
                    M("users")->where(array('user_id'=>$user_id))->setInc('award_money',$withdrawal['last_money']);
                }
                $this->ajaxReturn(array('status'=>200,'msg'=>'审核成功'));
            }
        }
        if($user['nickname'])
            $withdrawals['user_name'] = $user['nickname'];
        elseif($user['email'])
            $withdrawals['user_name'] = $user['email'];
        elseif($user['mobile'])
            $withdrawals['user_name'] = $user['mobile'];

        $this->assign('user',$user);
        $this->assign('data',$withdrawals);
        $this->display();
    }
    /**
     *  转账汇款记录
     */
    public function remittance(){
        $model = M("remittance");
        $_GET = array_merge($_GET,$_POST);
        unset($_GET['create_time']);

        $status = I('status');
        $user_id = I('user_id');
        $account_bank = I('account_bank');
        $account_name = I('account_name');

        $create_time = I('create_time');
        $create_time = $create_time  ? $create_time  : date('Y/m/d',strtotime('-1 year')).'-'.date('Y/m/d',strtotime('+1 day'));
        $create_time2 = explode('-',$create_time);
        $where = " create_time >= '".strtotime($create_time2[0])."' and create_time <= '".strtotime($create_time2[1])."' ";
        $user_id && $where .= " and user_id = $user_id ";
        $account_bank && $where .= " and account_bank like '%$account_bank%' ";
        $account_name && $where .= " and account_name like '%$account_name%' ";

        $count = $model->where($where)->count();
        $Page  = new Page($count,16);
        $list = $model->where($where)->order("`id` desc")->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('create_time',$create_time);
        $show  = $Page->show();
        $this->assign('show',$show);
        $this->assign('list',$list);
        C('TOKEN_ON',false);
        $this->display();
    }


}