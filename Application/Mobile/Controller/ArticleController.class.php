<?php
namespace Mobile\Controller;
use Home\Logic\CartLogic;
use Think\Controller;
use Think\Page;
use Think\Verify;
class ArticleController extends MobileBaseController {
    public function index(){       
        $article_id = I('article_id',38);
    	$article = D('article')->where("article_id=$article_id")->find();
    	$this->assign('article',$article);
        $this->display();
    }
 

    /**
     * 文章内列表页
     */
    public function articleList(){        
        $list = M('Article')->where("cat_id IN(1,2,3,4,5,6,7)")->select();
        $this->assign('list',$list);
        $this->display();
    }    
    /**
     * 文章内容页
     */
    public function article(){
    	$article_id = I('article_id',1);
    	$article = D('article')->where("article_id=$article_id")->find();
    	$this->assign('article',$article);
        $this->display();
    }
    //系统公告
    public function gong(){
        //首页轮播
        $lun = M("ad")->where("pid=51322 and enabled =1")->field("ad_id,ad_name,ad_code,ad_link")->order("orderby desc")->select();
        $this->assign('lun',$lun);
        $list = M("article")->where('cat_id=6 and is_open=1')->order("article_id desc")->select();
        foreach ($list as $kk=>$vv){
            $list[$kk]['publish_time'] = date("Y-m-d ",$vv['publish_time']);
        }
        $this->assign('list',$list);
        $this->display();
    }
    //公告详情
    public function gongDetail(){
        $id = I("id");
        $detail = M("article")->where('article_id='.$id)->find();
        $detail['publish_time'] = date("Y-m-d H:i:s",$detail['publish_time']);
        $detail['content'] = htmlspecialchars_decode( $detail['content']);
        //上一篇
        $pre_info = M('article')->where(' article_id > '.$id .' and cat_id='.$detail['cat_id'])->order('article_id asc')->find();
        $this->assign('pre_info',$pre_info);
        //下一篇
        $next_info = M('Article')->where(' article_id < '.$id .' and cat_id='.$detail['cat_id'])->order('article_id desc')->find();
        $this->assign('next_info',$next_info);
        $this->assign('detail',$detail);
        $this->display();
    }
}