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
class IndexController extends MobileBaseController {

    public function index(){
        $config = getConfig();
        //团购分类
        $groupList = M('group_price')->where("is_show=1")->order("id asc")->select();
        //首页轮播
        $lun = M("ad")->where("pid=1 and enabled =1")->field("ad_id,ad_name,ad_code,ad_link")->order("orderby desc")->select();
        $this->assign("groupList",$groupList);
        $this->assign('config',$config);
        $this->assign('lun',$lun);
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')===false){
//            $this->redirect("Index/webVerify");
        }
        $user = session('user');
        $this->assign('user',$user);
        $this->assign('config',$config);
        $this->display();
    }
    /**
     * 商品列表页
     */
    public function ajaxList(){
        $price = I("price");
        $list = M("goods")->where("is_on_sale=1 and type=1 and group_price=".$price)->order("sort asc")->field("goods_id,goods_name,original_img,keywords,shop_price,market_price,sales_sum,group_num")->select();
        $this->assign('list', $list);
        $this->display('ajaxList');
    }
// author :凌寒 2019年1月25日16:15:38 网站验证
    public function webVerify(){
//
        $this->display();
    }



    /**
     * 模板列表
     */
    public function mobanlist(){
        $arr = glob("D:/wamp/www/svn_tpshop/mobile--html/*.html");
        foreach($arr as $key => $val)
        {
            $html = end(explode('/', $val));
            echo "<a href='http://www.php.com/svn_tpshop/mobile--html/{$html}' target='_blank'>{$html}</a> <br/>";            
        }        
    }
    public function ceshi(){
        $user_id = 	140;
        $config = getConfig();
        $sy_team_num = sy_team_num($user_id);  //计算剩余团队的团队业绩
        echo "<pre>";
        var_dump($sy_team_num);
        var_dump($config['basic_up_dongshi_qy_num']);die;
    }

    /**
     * 商品列表页
     */
    public function goodsList(){
        $config = getConfig();
        $where = 'order_status != 3';
        $list = M("maichu")->where($where)->select();
        if($list){
            foreach ($list as $k => $v) {
                $list[$k]['goods'] = M("goods")->where("goods_id=".$v['goods_id'])->find();
                $list[$k]['sell_user'] = M("users")->where("user_id=".$v['sell_user_id'])->field("user_id,nickname,head_pic")->find();
            }
        }else{
            $list = array();
        }
        $this->assign('config',$config);
        $this->assign('lists',$list);
        $this->display();
    }
    //拼团专区
    public function lists(){
        $price = I("type");
        $info = M('group_price')->where("is_show=1 and price=".$price)->find();
        $lists = M("goods")->where("is_on_sale=1 and group_price=".$price)->order("sort asc")->field("goods_id,goods_name,original_img,keywords,shop_price,market_price,group_num,sales_sum")->select();
        $this->assign('info',$info);
        $this->assign('lists',$lists);
        $this->display('lists');
    }
    // author :凌寒 2019年4月24日11:15:55 商品详情
    public function goods_detail(){
        $goods_id = I("id");
        $goods = M('Goods')->where("goods_id = $goods_id")->find();
//        dump($goods);die;
        if(empty($goods)){
            $this->error('此商品不存在或者已下架');
        }
        $goods['goods_content'] = htmlspecialchars_decode($goods['goods_content']);
        $goods_images_list = M('GoodsImages')->where("goods_id = $goods_id")->select(); // 商品 图册

        $user = session('user');
        $this->assign('user',$user);
        $this->assign('goods_images_list',$goods_images_list);//商品缩略图
        $this->assign('goods',$goods);
        $this->display();
    }
    public function ajaxGetMore(){
    	$p = I('p',1);
    	$favourite_goods = M('goods')->where("is_recommend=1 and is_on_sale=1")->order('goods_id DESC')->page($p,10)->cache(true,TPSHOP_CACHE_TIME)->select();//首页推荐商品
    	$this->assign('favourite_goods',$favourite_goods);
    	$this->display();
    }
}