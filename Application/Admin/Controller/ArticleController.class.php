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
use Admin\Logic\ArticleCatLogic;

class ArticleController extends BaseController {

    public function categoryList(){
        $ArticleCat = new ArticleCatLogic(); 
        $cat_list = $ArticleCat->article_cat_list(0, 0, false);
        $type_arr = array('系统默认','系统帮助','系统公告');
        $this->assign('type_arr',$type_arr);
        $this->assign('cat_list',$cat_list);
        $this->display('categoryList');
    }
    
    public function category(){
        $ArticleCat = new ArticleCatLogic();  
 		$act = I('GET.act','add');
        $this->assign('act',$act);
        $cat_id = I('GET.cat_id');
        $cat_info = array();
        if($cat_id){
            $cat_info = D('article_cat')->where('cat_id='.$cat_id)->find();
            $this->assign('cat_info',$cat_info);
        }
        $cats = $ArticleCat->article_cat_list(0,$cat_info['parent_id'],true);
        $this->assign('cat_select',$cats);
        $this->display();
    }
    
    public function articleList(){
        $Article =  M('Article'); 
        $res = $list = array();
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 20 : $_REQUEST['size'];
        
        $where = " 1 = 1 ";
        $keywords = trim(I('keywords'));
        $keywords && $where.=" and title like '%$keywords%' ";
        $cat_id = I('cat_id',0);
        $cat_id && $where.=" and cat_id = $cat_id ";
        $res = $Article->where($where)->order('article_id desc')->page("$p,$size")->select();
        $count = $Article->where($where)->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出

        $ArticleCat = new ArticleCatLogic();
        $cats = $ArticleCat->article_cat_list(0,0,false);
        if($res){
        	foreach ($res as $val){
        		$val['category'] = $cats[$val['cat_id']]['cat_name'];
        		$val['add_time'] = date('Y-m-d',$val['publish_time']);
        		$list[] = $val;
        	}
        }
        $this->assign('cats',$cats);
        $this->assign('cat_id',$cat_id);
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出
        $this->display('articleList');
    }
    
    public function article(){
        $ArticleCat = new ArticleCatLogic();
 		$act = I('GET.act','add');
        $info = array();
        $info['publish_time'] = time()+3600*24;
        if(I('GET.article_id')){
           $article_id = I('GET.article_id');
           $info = D('article')->where('article_id='.$article_id)->find();
        }
        $cats = $ArticleCat->article_cat_list(0,$info['cat_id']);
        $this->assign('cat_select',$cats);
        $this->assign('act',$act);
        $this->assign('info',$info);
        $this->initEditor();
        $this->display();
    }
    
    
    /**
     * 初始化编辑器链接
     * @param $post_id post_id
     */
    private function initEditor()
    {
        $this->assign("URL_upload", U('Admin/Ueditor/imageUp',array('savepath'=>'article')));
        $this->assign("URL_fileUp", U('Admin/Ueditor/fileUp',array('savepath'=>'article')));
        $this->assign("URL_scrawlUp", U('Admin/Ueditor/scrawlUp',array('savepath'=>'article')));
        $this->assign("URL_getRemoteImage", U('Admin/Ueditor/getRemoteImage',array('savepath'=>'article')));
        $this->assign("URL_imageManager", U('Admin/Ueditor/imageManager',array('savepath'=>'article')));
        $this->assign("URL_imageUp", U('Admin/Ueditor/imageUp',array('savepath'=>'article')));
        $this->assign("URL_getMovie", U('Admin/Ueditor/getMovie',array('savepath'=>'article')));
        $this->assign("URL_Home", "");
    }
    
    
    public function categoryHandle(){
    	$data = I('post.');   
        if($data['act'] == 'add'){           
            $d = D('article_cat')->add($data);
        }
        
        if($data['act'] == 'edit')
        {
        	if ($data['cat_id'] == $data['parent_id']) 
			{
        		$this->error("所选分类的上级分类不能是当前分类",U('Admin/Article/category',array('cat_id'=>$data['cat_id'])));
        	}
        	$ArticleCat = new ArticleCatLogic();
        	$children = array_keys($ArticleCat->article_cat_list($data['cat_id'], 0, false)); // 获得当前分类的所有下级分类
        	if (in_array($data['parent_id'], $children))
        	{
        		$this->error("所选分类的上级分类不能是当前分类的子分类",U('Admin/Article/category',array('cat_id'=>$data['cat_id'])));
        	}
        	$d = D('article_cat')->where("cat_id=$data[cat_id]")->save($data);
        }
        
        if($data['act'] == 'del'){      	
        	$res = D('article_cat')->where('parent_id ='.$data['cat_id'])->select(); 
        	if ($res)
        	{
        		exit(json_encode('还有子分类，不能删除'));
        	}
        	$res = D('article')->where('cat_id ='.$data['cat_id'])->select();       	      	
        	if ($res)
        	{
        		exit(json_encode('非空的分类不允许删除'));
        	}      	
        	$r = D('article_cat')->where('cat_id='.$data['cat_id'])->delete();
        	if($r) exit(json_encode(1));
        }
        if($d){
        	$this->success("操作成功",U('Admin/Article/categoryList'));
        }else{
        	$this->error("操作失败",U('Admin/Article/categoryList'));
        }
    }
    
    public function aticleHandle(){
        $data = I('post.');
        $data['publish_time'] = strtotime($data['publish_time']);
        //$data['content'] = htmlspecialchars(stripslashes($_POST['content']));
        if($data['act'] == 'add'){
                $data['click'] = mt_rand(1000,1300);
        	$data['add_time'] = time(); 
            $r = D('article')->add($data);
        }
        
        if($data['act'] == 'edit'){
            $r = D('article')->where('article_id='.$data['article_id'])->save($data);
        }
        
        if($data['act'] == 'del'){
        	$r = D('article')->where('article_id='.$data['article_id'])->delete();
        	if($r) exit(json_encode(1));       	
        }
        if($r){
            $this->success("操作成功",U('Admin/Article/articleList'));
        }else{
            $this->error("操作失败",U('Admin/Article/articleList'));
        }
    }
    
    
    public function link(){
    	$act = I('GET.act','add');
    	$this->assign('act',$act);
    	$link_id = I('GET.link_id');
    	$link_info = array();
    	if($link_id){
    		$link_info = D('game_log')->where('id='.$link_id)->find();
    		$this->assign('info',$link_info);
    	}
    	$list =M("users")->where("1=1")->order("user_id asc")->field("user_id,nickname")->select();
    	$this->assign('list',$list);
    	$this->display();
    }
    
    public function linkList(){
    	$Ad =  M('game_log');
    	$res = $Ad->where('1=1')->order('id desc')->page($_GET['p'].',10')->select();
    	if($res){
    		foreach ($res as $k=>$v){
                $res[$k]['nickname'] = M("users")->where("user_id=".$v['user_id'])->getField("nickname");
    		}
    	}
    	$this->assign('list',$res);// 赋值数据集
    	$count = $Ad->where('1=1')->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
    	$show = $Page->show();// 分页显示输出
    	$this->assign('page',$show);// 赋值分页输出
    	$this->display();
    }
    
    public function linkHandle(){
        $data = I('post.');
    	if($data['act'] == 'add'){
    		$data['add_time'] = time();
    		$r = D('game_log')->add($data);
    	}
    	if($data['act'] == 'edit'){
    		$r = D('game_log')->where('id='.$data['link_id'])->save($data);
    	}
    	
    	if($data['act'] == 'del'){
    		$r = D('game_log')->where('id='.$data['link_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	
    	if($r){
    		$this->success("操作成功",U('Admin/Article/linkList'));
    	}else{
    		$this->error("操作失败",U('Admin/Article/linkList'));
    	}
    }
    public function chou(){
        $act = I('GET.act','add');
        $this->assign('act',$act);
        $link_id = I('GET.id');
        $link_info = array();
        if($link_id){
            $link_info = D('game')->where('id='.$link_id)->find();
            $this->assign('info',$link_info);
        }
        $list =M("users")->where("1=1")->order("user_id asc")->field("user_id,nickname")->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function chouList(){
        $Ad =  M('game');
        $res = $Ad->where('1=1')->order('id desc')->page($_GET['p'].',10')->select();
        $this->assign('list',$res);// 赋值数据集
        $count = $Ad->where('1=1')->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }

    public function chouHandle(){
        $data = I('post.');
        if($data['act'] == 'add'){
            $data['add_time'] = time();
            $r = D('game')->add($data);
        }
        if($data['act'] == 'edit'){
            $r = D('game')->where('id='.$data['id'])->save($data);
        }

        if($data['act'] == 'del'){
            $r = D('game')->where('id='.$data['id'])->delete();
            if($r) exit(json_encode(1));
        }

        if($r){
            $this->success("操作成功",U('Admin/Article/chouList'));
        }else{
            $this->error("操作失败",U('Admin/Article/chouList'));
        }
    }
    public function help_list(){
    	
    }
    
    public function notice_list(){
    	
    }
}