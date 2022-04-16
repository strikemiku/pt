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
use Home\Logic\UsersLogic;
use Think\Controller;
class MobileBaseController extends Controller {
    public $user = array();
    public $user_id = 0;
    public $session_id;
    public $weixin_config;
    public $cateTrre = array();

    /*
     * 初始化操作
     */
    public function _initialize() {
        $this->session_id = session_id(); // 当前的 session_id
        define('SESSION_ID',$this->session_id); //将当前的session_id保存为常量，供其它方法调用
        if($_GET['pid'] !=''){
            $_SESSION['pid']=$_GET['pid'];
        }
        // 判断当前用户是否手机
        if(isMobile()){
            cookie('is_mobile','1',3600);
        }else{
            cookie('is_mobile','0',3600);
        }
        //获取微信配置
        $wechat_list = M('wx_user')->select();
//        $_SESSION['openid']='';
//        //微信浏览器
//        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') && $wechat_list[0]['wait_access']){
//            $this->weixin_config = $wechat_config=$wechat_list[0];
//            $this->assign('wechat_config', $wechat_config); // 微信配置
//            if($_SESSION['openid'] && $_SESSION['openid']!='0'){
//                $user = M("users")->where("oauth='weixin' and openid='{$_SESSION['openid']}'")->find();
//                if($user){
//                    session('user',$user);
//                    setcookie('user_id',session('user_id'),null,'/');
//                }else{
//                    $wxuser = $this->GetOpenid();
//                    $resout_user = $this->thirdReg($wxuser);
//                    session('user',$resout_user);
//                    setcookie('user_id',session('user_id'),null,'/');
//                }
//            }else{
//                //去授权获取openid
//                $wxuser = $this->GetOpenid();
//                $resout_user = $this->thirdReg($wxuser);
//                session('user',$resout_user);
//                setcookie('user_id',session('user_id'),null,'/');
//            }
//            // 微信Jssdk 操作类 用分享朋友圈 JS
//            $jssdk = new \Mobile\Logic\Jssdk($this->weixin_config['appid'], $this->weixin_config['appsecret']);
//            $signPackage = $jssdk->GetSignPackage();
//            $this->assign('signPackage', $signPackage);
//        }
        $this->public_assign();
    }
    // 网页授权登录获取 OpendId
    public function GetOpenid()
    {
//        if($_SESSION['openid'] && $_SESSION['openid']!='0'){
//            return $_SESSION['openid'];
//        }
        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            $baseUrl = urlencode($this->get_url());
            $url = $this->__CreateOauthUrlForCode($baseUrl); // 获取 code地址
            Header("Location: $url"); // 跳转到微信授权页面 需要用户确认登录的页面
            exit();
        } else {
            // 上面跳转, 这里跳了回来
            //获取code码，以获取openid
            $code = $_GET['code'];
            $data = $this->getOpenidFromMp($code);
            $data2 = $this->GetUserInfo($data['access_token'],$data['openid']);
            $data['nickname'] = $data2['nickname'];
            $data['headimgurl'] = $data2['headimgurl'];
            $data['subscribe'] = $data2['subscribe'];
            $_SESSION['subscribe'] = $data2['subscribe'];
            $_SESSION['openid'] = $data['openid'];
            return  $data;
        }
    }
    //注册
    public function thirdReg($data){
        $nickname = trim($data['nickname']) ? trim($data['nickname']) : '微信用户';
        $openid = $data['openid']; //第三方返回唯一标识
        //获取用户信息
        $user = M("users")->where("openid='{$openid}' and oauth='weixin'")->find();
        if(!$user){
            if($_SESSION['pid']){
                $parent_info = M("users")->where(array('user_id'=>$_SESSION['pid']))->find();
                $map['pid']=$parent_info['user_id'];  //推荐人id
                $map['pcode']=$parent_info['user_code'];  //推荐人编号
            }
            $map['openid'] = $openid;
            $map['user_code'] =userCode();
            $map['nickname']= $nickname;
            $map['mobile']= "";
            $map['password']=md5(123456);
            $map['twopassword']=md5(123456);
            $map['pt_num']=0;  //拼团次数
            $map['level']=1;   //注册会员
            $map['type']=0;
            $map['is_lock']=1;  //已激活
            $map['reg_time']=time();
            $map['oauth'] = "weixin";
            $map['subscribe']=$data['subscribe'];
            $map['head_pic'] = $data['headimgurl'];
            $map['token'] = md5(time().mt_rand(1,99999));
            $id=M('users')->add($map);
            $user = M('users')->where("user_id = $id")->find();
        }else{
            $user['token'] = md5(time().mt_rand(1,999999999));
            M('users')->where("user_id = '{$user['user_id']}'")->save(array('token'=>$user['token'],'last_login'=>time()));
        }
        return $user;
    }
    /**
     * 保存公告变量到 smarty中 比如 导航 
     */   
    public function public_assign()
    {
       $tpshop_config = array();
       $tp_config = M('config')->cache(true,TPSHOP_CACHE_TIME)->select();       
       foreach($tp_config as $k => $v)
       {
       	  if($v['name'] == 'hot_keywords'){
       	  	 $tpshop_config['hot_keywords'] = explode('|', $v['value']);
       	  }       	  
          $tpshop_config[$v['inc_type'].'_'.$v['name']] = $v['value'];
       }                        
       
       $goods_category_tree = get_goods_category_tree();    
       $this->cateTrre = $goods_category_tree;
//        $config =getConfig();
       $this->assign('goods_category_tree', $goods_category_tree);                     
       $brand_list = M('brand')->cache(true,TPSHOP_CACHE_TIME)->field('id,parent_cat_id,logo,is_hot')->where("parent_cat_id>0")->select();              
       $this->assign('brand_list', $brand_list);
        $this->assign("controller",CONTROLLER_NAME);
        $this->assign("action",ACTION_NAME);
       $this->assign('tpshop_config', $tpshop_config);
    }
    /**
     * 获取当前的url 地址
     * @return type
     */
    private function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }    
    
    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function GetOpenidFromMp($code)
    {
         //通过code换取网页授权access_token  和 openid
        $url = $this->__CreateOauthUrlForOpenid($code);       
        $ch = curl_init();//初始化curl        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);         
        $res = curl_exec($ch);//运行curl，结果以jason形式返回            
        $data = json_decode($res,true);//取出openid access_token                
        curl_close($ch);
                
        return $data;
    }
    
    
        /**
     *
     * 通过access_token openid 从工作平台获取UserInfo      
     * @return openid
     */
    public function GetUserInfo($access_token,$openid)
    {         
        // 获取用户 信息
        $url = $this->__CreateOauthUrlForUserinfo($access_token,$openid);
        $ch = curl_init();//初始化curl        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);         
        $res = curl_exec($ch);//运行curl，结果以jason形式返回            
        $data = json_decode($res,true);//取出openid access_token                
        curl_close($ch);
        // 获取看看用户是否关注了 你的微信公众号， 再来判断是否提示用户 关注
        $access_token2 = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token2&openid=$openid";
        $subscribe_info = httpRequest($url,'GET');
        $subscribe_info = json_decode($subscribe_info,true);
        $data['subscribe'] = $subscribe_info['subscribe'];
        return $data;
    }
    
    
    public function get_access_token(){
        //判断是否过了缓存期
        $wechat = M('wx_user')->find();
        $expire_time = $wechat['web_expires'];
        if($expire_time > time()){
           return $wechat['web_access_token'];
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$wechat[appid]}&secret={$wechat[appsecret]}";
        $return = httpRequest($url,'GET');
        $return = json_decode($return,1);
        $web_expires = time() + 7000; // 提前200秒过期
        M('wx_user')->where(array('id'=>$wechat['id']))->save(array('web_access_token'=>$return['access_token'],'web_expires'=>$web_expires));
        return $return['access_token'];
    }    

    /**
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = $this->weixin_config['appid'];
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
//        $urlObj["scope"] = "snsapi_base";
        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }

    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = $this->weixin_config['appid'];
        $urlObj["secret"] = $this->weixin_config['appsecret'];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     *
     * 构造获取拉取用户信息(需scope为 snsapi_userinfo)的url地址     
     * @return 请求的url
     */
    private function __CreateOauthUrlForUserinfo($access_token,$openid)
    {
        $urlObj["access_token"] = $access_token;
        $urlObj["openid"] = $openid;
        $urlObj["lang"] = 'zh_CN';        
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/userinfo?".$bizString;
    }    
    
    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }


    // author :凌寒 2018年9月18日15:17:55 发送
    public function message($phone){
        $code =  mt_rand(1000,9999);//验证码
        $sendUrl = 'http://sms.outeng.net/public/sendsms';
        $smsConf = array(
            'key'   => 'v7BlDXt4dVxijcO9U39oVB5WfVLwxgxU',
            'mobile'    => $phone,
            'tpl_id'    => 212,
            'tpl_value' =>'#code#='.$code
        );

        $this->otcurl($sendUrl,$smsConf,1);
        return $code;
    }
    // author :凌寒 2018年9月18日15:18:01 短信接口
    public function otcurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }

}