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

/**
 * tpshop检验登陆
 * @param
 * @return bool
 */
function is_login(){
    if(isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0){
        return $_SESSION['admin_id'];
    }else{
        return false;
    }
}

//生成唯一的的房间号
function userRoom()
{
    $code = "PPX".mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9);
    $is = M('home')->where(array('home_code'=>$code))->find();
    if($is){
        return userRoom();
    }else{
        return $code;
    }
}
//生成唯一的编码
function userCode()
{
    $code = "PPX".mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9).mt_rand(1,9);
    $is = M('users')->where(array('user_code'=>$code))->find();
    if($is){
        return userCode();
    }else{
        return $code;
    }
}
//记录注册用户和上级的推荐关系和代数关系
function relation($user_id){
    $now_user = M("users")->where(array('user_id'=>$user_id))->find();
    $depth = 1;
    if($now_user['pid']){
        $parent_user_id = $now_user['pid'];
        while ($father_info = M("users")->where(array('user_id'=>$parent_user_id))->find()) {
            if($parent_user_id == 0 ||$parent_user_id==''){
                break;
            }
            if($father_info){
                $data['user_id'] = $now_user['user_id'];
                $data['user_mobile'] = $now_user['mobile'];
                $data['user_code'] = $now_user['user_code'];
                $data['parent_user_id'] = $father_info['user_id'];
                $data['p_mobile'] = $father_info['mobile'];
                $data['p_code'] = $father_info['user_code'];
                $data['depth'] = $depth;
                $data['add_time'] = time();
                M("user_relation")->add($data);
                $parent_user_id = $father_info['pid'];
            }
            $depth++;
        }
    }
}
/**
 * 获取用户信息
 * @param $user_id_or_name  用户id 邮箱 手机 第三方id
 * @param int $type  类型 0 user_id查找 1 邮箱查找 2 手机查找 3 第三方唯一标识查找
 * @param string $oauth  第三方来源
 * @return mixed
 */
function get_user_info($user_id_or_name,$type = 0,$oauth=''){
    $map = array();
    if($type == 0)
        $map['user_id'] = $user_id_or_name;
    if($type == 1)
        $map['email'] = $user_id_or_name;
    if($type == 2)
        $map['mobile'] = $user_id_or_name;
    if($type == 3){
        $map['openid'] = $user_id_or_name;
        $map['oauth'] = $oauth;
    }
    $user = M('users')->where($map)->find();
    return $user;
}
// author :凌寒 2018年12月14日14:38:51 获取系统中的配置信息
function getConfig(){
    $tpshop_config = array();
    $tp_config = M('config')->select();
    foreach($tp_config as $k => $v)
    {
        if($v['name'] == 'hot_keywords'){
            $tpshop_config['hot_keywords'] = explode('|', $v['value']);
        }
        $tpshop_config[$v['inc_type'].'_'.$v['name']] = $v['value'];
    }
    return $tpshop_config;
}
/**
 * 更新会员等级,折扣，消费总额
 * @param $user_id  用户ID
 * @return boolean
 */
function update_user_level($user_id){


    $config =getConfig();
    $level_info = M('user_level')->order('level_id')->select();

    $user = M("users")->where(array('user_id'=>$user_id))->find();

    if($user['level']==1){  //如果是预备会员

        if(floatval($user['total_amount']) >= floatval($config['basic_update_one_money'])){
            $res = M("users")->where(array('user_id'=>$user_id))->setInc('level',1);  //升级会员
            if($res){
                //记录节点关系
                $node_relation=get_jiedian_arr($user['pid']);
                $node_info = M("users")->where("user_id=".$node_relation['nodal_person'])->field('user_id,user_code')->find();
                $node_relation['node_code'] = $node_info['user_code'];
                $data['node_id']=$node_relation['nodal_person'];
                $data['node_code']=$node_relation['node_code'];
                $data['market'] = $node_relation['market'];

                $ress = M("users")->where(array('user_id'=>$user_id))->save($data);
                if($ress){
                    $user_info = M("users")->where(array('user_id'=>$user_id))->find();
                    //将节点关系记录在节点关系表中
                    cengcalculate($user_info);
                }
            }
        }
    }
}
//ot_stone 2018年12月12日13:42:42 获取节点人以及左右区
function get_jiedian_arr($tuijian_id){


    $uid = $tuijian_id;//推荐人uid
    $max_cengshu = M('user_node')->where("userid=$uid")->order("cengshu desc")->getField("cengshu");//最大层数

    if(!$max_cengshu){
        //relation表中没有会员
        $nodal_person = $uid;
        $member_market = 0;
    }else{
        $cengcount = 1;
        $stop = 0;
        while ($cengcount <= $max_cengshu) {
            $all_count = pow(2,$cengcount);//本层最多人数
            $max_count = M('user_node')->where("userid=$uid and cengshu=$cengcount")->count();//本层已有人数
            if($all_count == $max_count && $cengcount == $max_cengshu){ //如果最大层数已满
                $node_info = M('user_node')->where("userid=$uid and cengshu=$cengcount")->order("id asc")->field("newuserid")->find();
                $nodal_person = $node_info['newuserid'];//最大层数
                $member_market = 0;//左区
            }else{
                if($max_count != $all_count){
                    if($cengcount == 1){
                        //如果为第一层
                        $nodal_person = $uid;
                        $member_market = 1;
                        break;
                    }else{

                        $cengshu = $cengcount-1;
                        //如果最大层数没有满层 则获取最大层数-1的会员列表
                        $m_list = M('user_node')->where("userid=$uid and cengshu=$cengshu")->order("id asc")->field("userid,newuserid")->select();
                        foreach ($m_list as $k => $v) {
                            //查看该会员左右区是否都有会员
                            $userid = $v['newuserid'];
                            $member_market = 0;

                            $check_left = M("users")->where("market = 0 and node_id=".$userid)->find();

                            if(!$check_left){
                                $stop = 1;
                                break;
                            }else{
                                $member_market = 1;
                                $check_right = M("users")->where("market = 1 and node_id = ".$userid)->find();
                                if(!$check_right){
                                    $stop = 1;
                                    break;
                                }
                            }
                        }
                        $nodal_person = $userid;
                    }
                }
            }
            if($stop == 1){
                break;
            }
            $cengcount++;
        }
    }
    return ['nodal_person'=>$nodal_person,'market'=>$member_market];
}
//ot_stone 2018年7月27日13:52:32 注册会员时写入节点关系表
function cengcalculate($user_info)
{

    $cengcount = 1;
    if ($user_info['node_id']) {
        $fujiuserid = $user_info['node_id']; //新注册会员的节点人编号
        $shichang = $user_info['market']; //新注册会员的市场（左右区）
        while ($fujihuiyuan = M("users")->where(array('user_id' => $fujiuserid))->find()) {

            //如果有节点人
            if ($fujihuiyuan) {
                $data['userid'] = $fujihuiyuan['user_id']; //节点人id
                $data['usernumber'] = $fujihuiyuan['user_code']; //节点人编号
                $data['status'] = 0; //未激活
                $data['create_time'] = time();
                $data['newuserid'] = $user_info['user_id'];
                $data['cengshu'] = $cengcount;
                $data['shichang'] = $shichang;
                $data['is_dui'] = 0;
                $data['is_ceng'] = 0;

                M("user_node")->add($data);
//                if($shichang==0){
//                    M("users")->where(array('user_id'=>$fujihuiyuan['user_id']))->setInc('l_num',1);
//                }else{
//                    M("users")->where(array('user_id'=>$fujihuiyuan['user_id']))->setInc('r_num',1);
//                }
                $fujiuserid = $fujihuiyuan['node_id'];
                $shichang = $fujihuiyuan['market'];
            }
            $cengcount++;
        }
    }
}
//更新推荐值
function upd_recom($user_id,$money,$action,$desc,$type,$from_user_id=null){
    $data['user_id'] = $user_id;
    $data['from_user_id'] = $from_user_id;
    $data['action'] = $action;
    $data['money'] = $money;
    $data['desc'] =$desc;
    $data['type'] =$type;
    $data['add_time'] =time();
    M("user_recom")->add($data);
    return 1;
}
//更新活跃度
function upd_active($user_id,$money,$action,$desc,$type,$from_user_id=null){
    $data['user_id'] = $user_id;
    $data['from_user_id'] = $from_user_id;
    $data['action'] = $action;
    $data['money'] = $money;
    $data['desc'] =$desc;
    $data['type'] =$type;
    $data['add_time'] =time();
    M("user_active")->add($data);
    return 1;
}
//更新贡献值
function upd_devote($user_id,$money,$action,$desc,$type,$from_user_id=null){
    $data['user_id'] = $user_id;
    $data['from_user_id'] = $from_user_id;
    $data['action'] = $action;
    $data['money'] = $money;
    $data['desc'] =$desc;
    $data['type'] =$type;
    $data['add_time'] =time();
    M("user_devote")->add($data);
    return 1;
}
// author :凌寒 更新用户余额
function upd_money($user_id,$money,$action,$desc,$type,$from_user_id=null){
    $data['user_id'] = $user_id;
    $data['from_user_id'] = $from_user_id;
    $data['action'] = $action;
    $data['money'] = $money;
    $data['desc'] =$desc;
    $data['type'] =$type;
    $data['add_time'] =time();
    M("user_money")->add($data);
    return 1;
}
// author :凌寒 更新用户积分
function upd_jifen($user_id,$jifen,$action,$desc,$type,$from_user_id=null){
    $data['user_id'] = $user_id;
    $data['from_user_id'] = $from_user_id;
    $data['action'] = $action;
    $data['money'] = $jifen;
    $data['desc'] =$desc;
    $data['type'] =$type;
    $data['add_time'] =time();
    M("user_jifen")->add($data);
    return 1;
}
// author :凌寒 更新用户奖金
function upd_award($user_id,$money,$action,$desc,$type,$from_user_id=null){
    $data['user_id'] = $user_id;
    $data['from_user_id'] = $from_user_id;
    $data['action'] = $action;
    $data['money'] = $money;
    $data['desc'] =$desc;
    $data['type'] =$type;
    $data['add_time'] =time();
    M("user_award")->add($data);
    return 1;
}
/**
 *  商品缩略图 给于标签调用 拿出商品表的 original_img 原始图来裁切出来的
 * @param type $goods_id  商品id
 * @param type $width     生成缩略图的宽度
 * @param type $height    生成缩略图的高度
 */
function goods_thum_images($goods_id,$width,$height){

    if(empty($goods_id))
        return '';
    //判断缩略图是否存在
    $path = "Public/upload/goods/thumb/$goods_id/";
    $goods_thumb_name ="goods_thumb_{$goods_id}_{$width}_{$height}";

    // 这个商品 已经生成过这个比例的图片就直接返回了
    if(file_exists($path.$goods_thumb_name.'.jpg'))  return '/'.$path.$goods_thumb_name.'.jpg';
    if(file_exists($path.$goods_thumb_name.'.jpeg')) return '/'.$path.$goods_thumb_name.'.jpeg';
    if(file_exists($path.$goods_thumb_name.'.gif'))  return '/'.$path.$goods_thumb_name.'.gif';
    if(file_exists($path.$goods_thumb_name.'.png'))  return '/'.$path.$goods_thumb_name.'.png';

    $original_img = M('Goods')->where("goods_id = $goods_id")->getField('original_img');
    if(empty($original_img)) return '';

    $original_img = '.'.$original_img; // 相对路径
    if(!file_exists($original_img)) return '';

    $image = new \Think\Image();
    $image->open($original_img);

    $goods_thumb_name = $goods_thumb_name. '.'.$image->type();
    //生成缩略图
    if(!is_dir($path))
        mkdir($path,0777,true);

    //参考文章 http://www.mb5u.com/biancheng/php/php_84533.html  改动参考 http://www.thinkphp.cn/topic/13542.html
    $image->thumb($width, $height,2)->save($path.$goods_thumb_name,NULL,100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存

    //图片水印处理
    $water = tpCache('water');
    if($water['is_mark']==1){
        $imgresource = './'.$path.$goods_thumb_name;
        if($width>$water['mark_width'] && $height>$water['mark_height']){
            if($water['mark_type'] == 'img'){
                $image->open($imgresource)->water(".".$water['mark_img'],$water['sel'],$water['mark_degree'])->save($imgresource);
            }else{
                //检查字体文件是否存在
                if(file_exists('./zhjt.ttf')){
                    $image->open($imgresource)->text($water['mark_txt'],'./zhjt.ttf',20,'#000000',$water['sel'])->save($imgresource);
                }
            }
        }
    }
    return '/'.$path.$goods_thumb_name;
}

/**
 * 商品相册缩略图
 */
function get_sub_images($sub_img,$goods_id,$width,$height){
    //判断缩略图是否存在
    $path = "Public/upload/goods/thumb/$goods_id/";
    $goods_thumb_name ="goods_sub_thumb_{$sub_img['img_id']}_{$width}_{$height}";
    //这个缩略图 已经生成过这个比例的图片就直接返回了
    if(file_exists($path.$goods_thumb_name.'.jpg'))  return '/'.$path.$goods_thumb_name.'.jpg';
    if(file_exists($path.$goods_thumb_name.'.jpeg')) return '/'.$path.$goods_thumb_name.'.jpeg';
    if(file_exists($path.$goods_thumb_name.'.gif'))  return '/'.$path.$goods_thumb_name.'.gif';
    if(file_exists($path.$goods_thumb_name.'.png'))  return '/'.$path.$goods_thumb_name.'.png';

    $original_img = '.'.$sub_img['image_url']; //相对路径
    if(!file_exists($original_img)) return '';

    $image = new \Think\Image();
    $image->open($original_img);

    $goods_thumb_name = $goods_thumb_name. '.'.$image->type();
    // 生成缩略图
    if(!is_dir($path))
        mkdir($path,777,true);
    $image->thumb($width, $height,2)->save($path.$goods_thumb_name,NULL,100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存
    return '/'.$path.$goods_thumb_name;
}

/**
 * 刷新商品库存, 如果商品有设置规格库存, 则商品总库存 等于 所有规格库存相加
 * @param type $goods_id  商品id
 */
function refresh_stock($goods_id){
    $count = M("SpecGoodsPrice")->where("goods_id = $goods_id")->count();
    if($count == 0) return false; // 没有使用规格方式 没必要更改总库存

    $store_count = M("SpecGoodsPrice")->where("goods_id = $goods_id")->sum('store_count');
    M("Goods")->where("goods_id = $goods_id")->save(array('store_count'=>$store_count)); // 更新商品的总库存
}

/**
 * 根据 order_goods 表扣除商品库存
 * @param type $order_id  订单id
 */
function minus_stock($order_id){
    $orderGoodsArr = M('OrderGoods')->where("order_id = $order_id")->select();
    foreach($orderGoodsArr as $key => $val)
    {
//        // 有选择规格的商品
//        if(!empty($val['spec_key']))
//        {   // 先到规格表里面扣除数量 再重新刷新一个 这件商品的总数量
//            M('SpecGoodsPrice')->where("goods_id = {$val['goods_id']} and `key` = '{$val['spec_key']}'")->setDec('store_count',$val['goods_num']);
//            refresh_stock($val['goods_id']);
//        }else{
//               M('Goods')->where("goods_id = {$val['goods_id']}")->setDec('store_count',$val['goods_num']); // 直接扣除商品总数量
//        }
        M('Goods')->where("goods_id = {$val['goods_id']}")->setDec('store_count',$val['goods_num']); // 直接扣除商品总数量
        M('Goods')->where("goods_id = {$val['goods_id']}")->setInc('sales_sum',$val['goods_num']); // 增加商品销售量
    }
}

/**
 * 邮件发送
 * @param $to    接收人
 * @param string $subject   邮件标题
 * @param string $content   邮件内容(html模板渲染后的内容)
 * @throws Exception
 * @throws phpmailerException
 */
function send_email($to,$subject='',$content=''){
    require_once THINK_PATH.'Library/Vendor/phpmailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $config = tpCache('smtp');
    $mail->CharSet  = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    //调试输出格式
    //$mail->Debugoutput = 'html';
    //smtp服务器
    $mail->Host = $config['smtp_server'];
    //端口 - likely to be 25, 465 or 587
    $mail->Port = $config['smtp_port'];

    if($mail->Port === 465) $mail->SMTPSecure = 'ssl';// 使用安全协议
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //用户名
    $mail->Username = $config['smtp_user'];
    //密码
    $mail->Password = $config['smtp_pwd'];
    //Set who the message is to be sent from
    $mail->setFrom($config['smtp_user']);
    //回复地址
    //$mail->addReplyTo('replyto@example.com', 'First Last');
    //接收邮件方
    if(is_array($to)){
        foreach ($to as $v){
            $mail->addAddress($v);
        }
    }else{
        $mail->addAddress($to);
    }

    $mail->isHTML(true);// send as HTML
    //标题
    $mail->Subject = $subject;
    //HTML内容转换
    $mail->msgHTML($content);
    //Replace the plain text body with one created manually
    //$mail->AltBody = 'This is a plain-text message body';
    //添加附件
    //$mail->addAttachment('images/phpmailer_mini.png');
    //send the message, check for errors
    return $mail->send();
}

/**
 * 发送短信
 * @param $mobile  手机号码
 * @param $content  内容
 * @return bool

function sendSMS($mobile,$content)
{
$config = F('sms','',TEMP_PATH);
$http = $config['sms_url'];			//短信接口
$uid = $config['sms_user'];			//用户账号
$pwd = $config['sms_pwd'];			//密码
$mobileids = $mobile;         		//号码发送状态接收唯一编号
$data = array
(
'uid'=>$uid,					//用户账号
'pwd'=>md5($pwd.$uid),			//MD5位32密码,密码和用户名拼接字符
'mobile'=>$mobile,				//号码，以英文逗号隔开
'content'=>$content,			//内容
'mobileids'=>$mobileids,
);
//即时发送
$res = httpRequest($http,'POST',$data);//POST方式提交
$stat = strpos($res,'stat=100');
if($stat){
return true;
}else{
return false;
}
}
 */
//    /**
//     * 发送短信
//     * @param $mobile  手机号码
//     * @param $code    验证码
//     * @return bool    短信发送成功返回true失败返回false
//     */
function sendSMS($mobile, $code)
{
    //时区设置：亚洲/上海
    date_default_timezone_set('Asia/Shanghai');
    //这个是你下面实例化的类
    vendor('Alidayu.TopClient');
    //这个是topClient 里面需要实例化一个类所以我们也要加载 不然会报错
    vendor('Alidayu.ResultSet');
    //这个是成功后返回的信息文件
    vendor('Alidayu.RequestCheckUtil');
    //这个是错误信息返回的一个php文件
    vendor('Alidayu.TopLogger');
    //这个也是你下面示例的类
    vendor('Alidayu.AlibabaAliqinFcSmsNumSendRequest');

    $c = new \TopClient;
    $config =  tpCache('sms');
    //短信内容：公司名/名牌名/产品名
    $product = $config['sms_product'];
    //App Key的值 这个在开发者控制台的应用管理点击你添加过的应用就有了
    $c->appkey = $config['sms_appkey'];
    //App Secret的值也是在哪里一起的 你点击查看就有了
    $c->secretKey = $config['sms_secretKey'];
    //这个是用户名记录那个用户操作
    $req = new \AlibabaAliqinFcSmsNumSendRequest;
    //代理人编号 可选
    $req->setExtend("123456");
    //短信类型 此处默认 不用修改
    $req->setSmsType("normal");
    //短信签名 必须
    $req->setSmsFreeSignName("注册验证");
    //短信模板 必须
    $req->setSmsParam("{\"code\":\"$code\",\"product\":\"$product\"}");
    //短信接收号码 支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，
    $req->setRecNum("$mobile");
    //短信模板ID，传入的模板必须是在阿里大鱼“管理中心-短信模板管理”中的可用模板。
    $req->setSmsTemplateCode($config['sms_templateCode']); // templateCode

    $c->format='json';
    //发送短信
    $resp = $c->execute($req);
    //短信发送成功返回True，失败返回false
    //if (!$resp)
    if ($resp && $resp->result)   // if($resp->result->success == true)
    {
        // 从数据库中查询是否有验证码
        $data = M('sms_log')->where("code = '$code' and add_time > ".(time() - 60*60))->find();
        // 没有就插入验证码,供验证用
        empty($data) && M('sms_log')->add(array('mobile' => $mobile, 'code' => $code, 'add_time' => time(), 'session_id' => SESSION_ID));
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * 查询快递
 * @param $postcom  快递公司编码
 * @param $getNu  快递单号
 * @return array  物流跟踪信息数组
 */
function queryExpress($postcom , $getNu) {
//    $url = "http://wap.kuaidi100.com/wap_result.jsp?rand=".time()."&id={$postcom}&fromWeb=null&postid={$getNu}";
//    //$resp = httpRequest($url,'GET');
//
//    $resp = file_get_contents($url);
//
//    if (empty($resp)) {
//        return array('status'=>0, 'message'=>'物流公司网络异常，请稍后查询');
//    }
//    preg_match_all('/\\<p\\>&middot;(.*)\\<\\/p\\>/U', $resp, $arr);
//
//    if (!isset($arr[1])) {
//        return array( 'status'=>0, 'message'=>'查询失败，参数有误' );
//    }else{
//        foreach ($arr[1] as $key => $value) {
//            $a = array();
//            $a = explode('<br /> ', $value);
//            $data[$key]['time'] = $a[0];
//            $data[$key]['context'] = $a[1];
//        }
//        return array( 'status'=>1, 'message'=>'ok','data'=> array_reverse($data));
//    }
    $url = "https://www.ickd.cn/".$postcom.".html#no=".$getNu;
//    $url = "https://m.kuaidi100.com/query?type=".$postcom."&postid=".$getNu."&id=1";
    $resp = httpRequest($url,"GET");
    return json_decode($resp,true);
}

/**
 * 获取某个商品分类的 儿子 孙子  重子重孙 的 id
 * @param type $cat_id
 */
function getCatGrandson ($cat_id)
{
    $GLOBALS['catGrandson'] = array();
    $GLOBALS['category_id_arr'] = array();
    // 先把自己的id 保存起来
    $GLOBALS['catGrandson'][] = $cat_id;
    // 把整张表找出来
    $GLOBALS['category_id_arr'] = M('GoodsCategory')->cache(true,TPSHOP_CACHE_TIME)->getField('id,parent_id');
    // 先把所有儿子找出来
    $son_id_arr = M('GoodsCategory')->where("parent_id = $cat_id")->cache(true,TPSHOP_CACHE_TIME)->getField('id',true);
    foreach($son_id_arr as $k => $v)
    {
        getCatGrandson2($v);
    }
    return $GLOBALS['catGrandson'];
}

/**
 * 获取某个文章分类的 儿子 孙子  重子重孙 的 id
 * @param type $cat_id
 */
function getArticleCatGrandson ($cat_id)
{
    $GLOBALS['ArticleCatGrandson'] = array();
    $GLOBALS['cat_id_arr'] = array();
    // 先把自己的id 保存起来
    $GLOBALS['ArticleCatGrandson'][] = $cat_id;
    // 把整张表找出来
    $GLOBALS['cat_id_arr'] = M('ArticleCat')->getField('cat_id,parent_id');
    // 先把所有儿子找出来
    $son_id_arr = M('ArticleCat')->where("parent_id = $cat_id")->getField('cat_id',true);
    foreach($son_id_arr as $k => $v)
    {
        getArticleCatGrandson2($v);
    }
    return $GLOBALS['ArticleCatGrandson'];
}

/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getCatGrandson2($cat_id)
{
    $GLOBALS['catGrandson'][] = $cat_id;
    foreach($GLOBALS['category_id_arr'] as $k => $v)
    {
        // 找到孙子
        if($v == $cat_id)
        {
            getCatGrandson2($k); // 继续找孙子
        }
    }
}


/**
 * 递归调用找到 重子重孙
 * @param type $cat_id
 */
function getArticleCatGrandson2($cat_id)
{
    $GLOBALS['ArticleCatGrandson'][] = $cat_id;
    foreach($GLOBALS['cat_id_arr'] as $k => $v)
    {
        // 找到孙子
        if($v == $cat_id)
        {
            getArticleCatGrandson2($k); // 继续找孙子
        }
    }
}

/**
 * 查看某个用户购物车中商品的数量
 * @param type $user_id
 * @param type $session_id
 * @return type 购买数量
 */
function cart_goods_num($user_id = 0,$session_id = '')
{
    $where = " session_id = '$session_id' ";
    $user_id && $where .= " or user_id = $user_id ";
    // 查找购物车数量
    $cart_count =  M('Cart')->where($where)->sum('goods_num');
    $cart_count = $cart_count ? $cart_count : 0;
    return $cart_count;
}

/**
 * 获取商品库存
 * @param type $goods_id 商品id
 * @param type $key  库存 key
 */
function getGoodNum($goods_id,$key)
{
    if(!empty($key))
        return  M("SpecGoodsPrice")->where("goods_id = $goods_id and `key` = '$key'")->getField('store_count');
    else
        return  M("Goods")->where("goods_id = $goods_id")->getField('store_count');
}

/**
 * 获取缓存或者更新缓存
 * @param string $config_key 缓存文件名称
 * @param array $data 缓存数据  array('k1'=>'v1','k2'=>'v3')
 * @return array or string or bool
 */
function tpCache($config_key,$data = array()){
    $param = explode('.', $config_key);
    if(empty($data)){
        //如$config_key=shop_info则获取网站信息数组
        //如$config_key=shop_info.logo则获取网站logo字符串
        $config = F($param[0],'',TEMP_PATH);//直接获取缓存文件
        if(empty($config)){
            //缓存文件不存在就读取数据库
            $res = D('config')->where("inc_type='$param[0]'")->select();
            if($res){
                foreach($res as $k=>$val){
                    $config[$val['name']] = $val['value'];
                }
                F($param[0],$config,TEMP_PATH);
            }
        }
        if(count($param)>1){
            return $config[$param[1]];
        }else{
            return $config;
        }
    }else{
        //更新缓存
        $result =  D('config')->where("inc_type='$param[0]'")->select();
        if($result){
            foreach($result as $val){
                $temp[$val['name']] = $val['value'];
            }
            foreach ($data as $k=>$v){
                $newArr = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
                if(!isset($temp[$k])){
                    M('config')->add($newArr);//新key数据插入数据库
                }else{
                    if($v!=$temp[$k])
                        M('config')->where("name='$k'")->save($newArr);//缓存key存在且值有变更新此项
                }
            }
            //更新后的数据库记录
            $newRes = D('config')->where("inc_type='$param[0]'")->select();
            foreach ($newRes as $rs){
                $newData[$rs['name']] = $rs['value'];
            }
        }else{
            foreach($data as $k=>$v){
                $newArr[] = array('name'=>$k,'value'=>trim($v),'inc_type'=>$param[0]);
            }
            M('config')->addAll($newArr);
            $newData = $data;
        }
        return F($param[0],$newData,TEMP_PATH);
    }
}

/**
 * 记录帐户变动
 * @param   int     $user_id        用户id
 * @param   float   $user_money     可用余额变动
 * @param   int     $pay_points     消费积分变动
 * @param   string  $desc    变动说明
 * @param   float   distribut_money 分佣金额
 * @return  bool
 */
function accountLog($user_id, $user_money = 0,$pay_points = 0, $desc = '',$distribut_money = 0){
    /* 插入帐户变动记录 */
    $account_log = array(
        'user_id'       => $user_id,
        'user_money'    => $user_money,
        'pay_points'    => $pay_points,
        'change_time'   => time(),
        'desc'   => $desc,
    );
    /* 更新用户信息 */
    $sql = "UPDATE __PREFIX__users SET user_money = user_money + $user_money," .
        " pay_points = pay_points + $pay_points, distribut_money = distribut_money + $distribut_money WHERE user_id = $user_id";
    if( D('users')->execute($sql)){
        M('account_log')->add($account_log);
        return true;
    }else{
        return false;
    }
}
/**
 * 记录帐户变动
 * @param   int     $user_id        用户id
 * @param   float   $user_money     可用余额变动
 * @param   int     $pay_points     消费积分变动
 * @param   string  $desc    变动说明
 * @param   float   distribut_money 分佣金额
 * @return  bool
 */
function rechargeLog($user_id, $user_money = 0,$pay_points = 0, $desc = '',$distribut_money = 0){
    /* 插入帐户变动记录 */
    $account_log = array(
        'user_id'       => $user_id,
        'user_money'    => $user_money,
        'change_time'   => time(),
        'type'=>3,
        'desc'   => $desc,
    );

    /* 更新用户信息 */
    $sql = "UPDATE __PREFIX__users SET shop_money = shop_money + $user_money WHERE user_id = $user_id";

    if( D('users')->execute($sql)){
        M('account_log')->add($account_log);
        return true;
    }else{
        return false;
    }
}

/**
 * 订单操作日志
 * 参数示例
 * @param type $order_id  订单id
 * @param type $action_note 操作备注
 * @param type $status_desc 操作状态  提交订单, 付款成功, 取消, 等待收货, 完成
 * @param type $user_id  用户id 默认为管理员
 * @return boolean
 */
function logOrder($order_id,$action_note,$status_desc,$user_id = 0)
{
    $status_desc_arr = array('提交订单', '付款成功', '取消', '等待收货', '完成','退货');
    // if(!in_array($status_desc, $status_desc_arr))
    // return false;

    $order = M('order')->where("order_id = $order_id")->find();
    $action_info = array(
        'order_id'        =>$order_id,
        'action_user'     =>$user_id,
        'order_status'    =>$order['order_status'],
        'shipping_status' =>$order['shipping_status'],
        'pay_status'      =>$order['pay_status'],
        'action_note'     => $action_note,
        'status_desc'     =>$status_desc, //''
        'log_time'        =>time(),
    );
    return M('order_action')->add($action_info);
}

/*
 * 获取地区列表
 */
function get_region_list(){
    //获取地址列表 缓存读取
    if(!S('region_list')){
        $region_list = M('region')->select();
        $region_list = convert_arr_key($region_list,'id');
        S('region_list',$region_list);
    }

    return $region_list ? $region_list : S('region_list');
}
/*
 * 获取用户地址列表
 */
function get_user_address_list($user_id){
    $lists = M('user_address')->where(array('user_id'=>$user_id))->select();
    return $lists;
}

/*
 * 获取指定地址信息
 */
function get_user_address_info($user_id,$address_id){
    $data = M('user_address')->where(array('user_id'=>$user_id,'address_id'=>$address_id))->find();
    return $data;
}
/*
 * 获取用户默认收货地址
 */
function get_user_default_address($user_id){
    $data = M('user_address')->where(array('user_id'=>$user_id,'is_default'=>1))->find();
    return $data;
}
/**
 * 获取订单状态的 中文描述名称
 * @param type $order_id  订单id
 * @param type $order     订单数组
 * @return string
 */
function orderStatusDesc($order_id = 0, $order = array())
{
    if(empty($order))
        $order = M('Order')->where("order_id = $order_id")->find();

    if($order['pay_status'] == 0 && $order['order_status'] == 0)
        return 'WAITPAY'; //'待支付',
    if($order['pay_status'] == 1 &&  in_array($order['order_status'],array(0,1)) && $order['shipping_status'] != 1)
        return 'WAITSEND'; //'待发货',
    if(($order['shipping_status'] == 1) && ($order['order_status'] == 1))
        return 'WAITRECEIVE'; //'待收货',
    if($order['order_status'] == 2)
        return 'WAITCCOMMENT'; //'待评价',
    if($order['order_status'] == 3)
        return 'CANCEL'; //'已取消',
    if($order['order_status'] == 4)
        return 'FINISH'; //'已完成',
    if($order['order_status'] == 5)
        return 'CANCELLED'; //'已作废',
    return 'OTHER';
}

/**
 * 获取订单状态的 显示按钮
 * @param type $order_id  订单id
 * @param type $order     订单数组
 * @return array()
 */
function orderBtn($order_id = 0, $order = array())
{
    if(empty($order))
        $order = M('Order')->where("order_id = $order_id")->find();
    /**
     *  订单用户端显示按钮
    去支付     AND pay_status=0 AND order_status=0 AND pay_code ! ="cod"
    取消按钮  AND pay_status=0 AND shipping_status=0 AND order_status=0
    确认收货  AND shipping_status=1 AND order_status=0
    评价      AND order_status=1
    查看物流  if(!empty(物流单号))
     */
    $btn_arr = array(
        'pay_btn' => 0, // 去支付按钮
        'cancel_btn' => 0, // 取消按钮
        'receive_btn' => 0, // 确认收货
        'comment_btn' => 0, // 评价按钮
        'shipping_btn' => 0, // 查看物流
        'return_btn' => 0, // 退货按钮 (联系客服)
    );
    if($order['pay_status'] == 0 && $order['order_status'] == 0) // 待支付
    {
        $btn_arr['pay_btn'] = 1; // 去支付按钮
        $btn_arr['cancel_btn'] = 1; // 取消按钮
    }
    if($order['pay_status'] == 1 && in_array($order['order_status'],array(0,1)) && $order['shipping_status'] == 0) // 待发货
    {
        $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
    }
    if($order['pay_status'] == 1 && $order['order_status'] == 1  && $order['shipping_status'] == 1) //待收货
    {
        $btn_arr['receive_btn'] = 1;  // 确认收货
        $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
    }
    if($order['order_status'] == 2)
    {
        $btn_arr['comment_btn'] = 1;  // 评价按钮
        $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
    }
    if($order['shipping_status'] != 0)
    {
        $btn_arr['shipping_btn'] = 1; // 查看物流
    }
    if($order['shipping_status'] == 2 && $order['order_status'] == 1) // 部分发货
    {
        $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
    }

    return $btn_arr;
}

/**
 * 给订单数组添加属性  包括按钮显示属性 和 订单状态显示属性
 * @param type $order
 */
function set_btn_order_status($order)
{
    $order_status_arr = C('ORDER_STATUS_DESC');
    $order['order_status_code'] = $order_status_code = orderStatusDesc(0, $order); // 订单状态显示给用户看的
    $order['order_status_desc'] = $order_status_arr[$order_status_code];
    $orderBtnArr = orderBtn(0, $order);
    return array_merge($order,$orderBtnArr); // 订单该显示的按钮
}


/**
 * 支付完成修改订单
 * $order_sn 订单号
 * $pay_status 默认1 为已支付
 */
function update_pay_status($order_sn,$pay_status = 1)
{
    $config = getConfig();
    if(stripos($order_sn,'LEL') !== false){  //购买匹配
        // 如果这笔订单已经处理过了
        $count = M('update_level')->where("order_sn = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if($count == 0){
            return false;
        }
        // 找出对应的订单
        $order = M('update_level')->where("order_sn = '$order_sn'")->find();
        // 修改支付状态  已支付
        M('update_level')->where("order_sn = '$order_sn'")->save(array('pay_status'=>1,'pay_time'=>time()));
        //更新等级
        M("users")->where("user_id=".$order['user_id'])->save(array('level'=>$order['level']));
    }elseif(stripos($order_sn,'user') !== false){  //充值订单
        // 如果这笔订单已经处理过了
        $count = M('recharge')->where("order_sn = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if($count == 0){
            return false;
        }
        // 修改支付状态
        M('recharge')->where("order_sn = '$order_sn'")->save(array('pay_status'=>1,'pay_time'=>time()));
        //更新状态
        $order = M('recharge')->where("order_sn = '$order_sn'")->find();
        //更新余额
        M("users")->where("user_id=".$order['user_id'])->setInc("user_money",$order['account']);
        upd_money($order['user_id'],$order['account'],1,"会员充值".$order['account']."元",1);
    }elseif(stripos($order_sn,'PPX') !== false){  //PPX202107062256509208
        // 如果这笔订单已经处理过了
        $count = M('cart')->where("order_sn = '$order_sn' and pay_status = 0")->count();   // 看看有没已经处理过这笔订单  支付宝返回不重复处理操作
        if($count == 0){
            return false;
        }
        $order = M('cart')->where("order_sn = '$order_sn'")->find();
        // 修改支付状态
        M('cart')->where("order_sn = '$order_sn'")->save(array('pay_status'=>1,'pay_time'=>time()));
        //有效会员
        $is_yx = M("users")->where("user_id=".$order['user_id'])->getField("is_yx");
        if($is_yx==0){
            M("users")->where("user_id=".$order['user_id'])->save(array('is_yx'=>1));
        }
        $info = M('cart')->where("order_sn = '$order_sn'")->find();
        //拼团操作
        pintuan($info);
    }
}

//拼团操作
function pintuan($order){
    $config = getConfig();
    //总拼团次数
    M("users")->where("user_id=".$order['user_id'])->setInc('total_pt_num',1);
    $goods = M("goods")->where("goods_id=".$order['goods_id'])->find();
    $group_num = M("group_num")->where("num=".$goods['group_num'])->find();
    $user = M("users")->where("user_id=".$order['user_id'])->field("user_id,level,type,dba_num,dbb_num,gda_num,gdb_num,false_num")->find();
    if($order['type']==1){ //快速拼团
        if($order['home_code']=='0'){
            $info_1 = array();
            $info_1 = M("home")->where("status=1 and goods_id=".$goods['goods_id']." and user_id !=".$user['user_id'])->order("id asc")->find();
            if($info_1){
                $order_count = M("home_order")->where("home_id=".$info_1['id']." and user_id=".$user['user_id'])->count();
                if($order_count==0){
                    $upd1['yp_num'] = $info_1['yp_num']+1;
                    $upd1['sy_num'] = $info_1['sy_num']-1;
                    $res1 = M("home")->where('id='.$info_1['id'])->save($upd1);
                    if($res1){
                        $data3['home_id'] = $info_1['id'];
                        $data3['user_id'] = $user['user_id'];
                        $data3['order_sn'] = $order['order_sn'];
                        $data3['goods_id'] = $info_1['goods_id'];
                        $data3['is_win'] = 0;
                        $data3['pay_code'] = $order['pay_code'];
                        $data3['pay_name']  = $order['pay_name'];
                        $data3['pay_time']  = $order['pay_time'];
                        $data3['address_id']  = $order['address_id'];
                        $data3['is_false']=setFalse($user);
                        $data3['status']  = 1;
                        $data3['add_time'] = time();
                        M("home_order")->add($data3);
                    }
                    $res2 = M("home")->where('id='.$info_1['id'])->find();
                    if($res2['yp_num']==$res2['pt_num']){  //团已满，执行发奖,更改状态
                        sendTeamAward($res2);
                        M("home")->where('id='.$info_1['id'])->save(array('status'=>2));
                    }
                }else{
                    $info_1 = getRoom($goods['goods_id'],$user['user_id'],$info_1['id']);
                    if($info_1){
                        $upd1['yp_num'] = $info_1['yp_num']+1;
                        $upd1['sy_num'] = $info_1['sy_num']-1;
                        $res1 = M("home")->where('id='.$info_1['id'])->save($upd1);
                        if($res1){
                            $data3['home_id'] = $info_1['id'];
                            $data3['user_id'] = $user['user_id'];
                            $data3['order_sn'] = $order['order_sn'];
                            $data3['goods_id'] = $info_1['goods_id'];
                            $data3['is_win'] = 0;
                            $data3['pay_code'] = $order['pay_code'];
                            $data3['pay_name']  = $order['pay_name'];
                            $data3['pay_time']  = $order['pay_time'];
                            $data3['address_id']  = $order['address_id'];
                            $data3['is_false']=setFalse($user);
                            $data3['status']  = 1;
                            $data3['add_time'] = time();
                            M("home_order")->add($data3);
                        }
                        $res2 = M("home")->where('id='.$info_1['id'])->find();
                        if($res2['yp_num']==$res2['pt_num']){  //团已满，执行发奖,更改状态
                            sendTeamAward($res2);
                            M("home")->where('id='.$info_1['id'])->save(array('status'=>2));
                        }
                    }else{
                        $data['home_code']  = userRoom();
                        $data['goods_id']  = $order['goods_id'];
                        $data['user_id']  = $order['user_id'];
                        $data['type']  = 1;
                        $data['hb_bl']  = $group_num['hb_bl'];
                        $data['award_bl']  = $group_num['award_bl'];
                        $data['pt_price']  = $order['goods_price'];
                        $data['pt_num']  = $goods['group_num'];
                        $data['yp_num']  = 1;
                        $data['sy_num']  = $goods['group_num']-1;
                        $data['add_time']  = time();
                        $data['end_time']  = time()+$goods['dao_time']*60;
                        $data['status']  = 1;
                        $id = M("home")->add($data);
                        if($id){
                            $data2['home_id'] = $id;
                            $data2['user_id'] = $order['user_id'];
                            $data2['order_sn'] = $order['order_sn'];
                            $data2['goods_id'] = $order['goods_id'];
                            $data2['status']  = 1;
                            $data2['is_false']=setFalse($user);
                            $data2['pay_code'] = $order['pay_code'];
                            $data2['pay_name']  = $order['pay_name'];
                            $data2['pay_time']  = $order['pay_time'];
                            $data2['address_id']  = $order['address_id'];
                            $data2['add_time'] = time();
                            M("home_order")->add($data2);
                        }
                    }
                }
            }else{
                $data['home_code']  = userRoom();
                $data['goods_id']  = $order['goods_id'];
                $data['user_id']  = $order['user_id'];
                $data['type']  = 1;
                $data['hb_bl']  = $group_num['hb_bl'];
                $data['award_bl']  = $group_num['award_bl'];
                $data['pt_price']  = $order['goods_price'];
                $data['pt_num']  = $goods['group_num'];
                $data['yp_num']  = 1;
                $data['sy_num']  = $goods['group_num']-1;
                $data['add_time']  = time();
                $data['end_time']  = time()+$goods['dao_time']*60;
                $data['status']  = 1;
                $id = M("home")->add($data);
                if($id){
                    $data2['home_id'] = $id;
                    $data2['user_id'] = $order['user_id'];
                    $data2['order_sn'] = $order['order_sn'];
                    $data2['goods_id'] = $order['goods_id'];
                    $data2['status']  = 1;
                    $data2['is_false']=setFalse($user);
                    $data2['pay_code'] = $order['pay_code'];
                    $data2['pay_name']  = $order['pay_name'];
                    $data2['pay_time']  = $order['pay_time'];
                    $data2['address_id']  = $order['address_id'];
                    $data2['add_time'] = time();
                    M("home_order")->add($data2);
                }
            }
        }else{  //房间拼团
            $homeInfo = M("home")->where("home_code='{$order[home_code]}'")->find();
            $upd2['yp_num'] = $homeInfo['yp_num']+1;
            $upd2['sy_num'] = $homeInfo['sy_num']-1;
            $res1 = M("home")->where('id='.$homeInfo['id'])->save($upd2);
            if($res1){
                $data3['home_id'] = $homeInfo['id'];
                $data3['user_id'] = $user['user_id'];
                $data3['order_sn'] = $order['order_sn'];
                $data3['goods_id'] = $homeInfo['goods_id'];
                $data3['pay_code'] = $order['pay_code'];
                $data3['pay_name']  = $order['pay_name'];
                $data3['pay_time']  = $order['pay_time'];
                $data3['address_id']  = $order['address_id'];
                $data3['is_false']=setFalse($user);
                $data3['status']  = 1;
                $data3['add_time'] = time();
                M("home_order")->add($data3);
            }
            $res3 = M("home")->where('id='.$homeInfo['id'])->find();
            if($res3['yp_num']==$res3['pt_num']){  //团已满，执行发奖,更改状态
                sendTeamAward($res3);
                M("home")->where('id='.$res3['id'])->save(array('status'=>2));
            }
        }
    }elseif ($order['type']==2){ //团长开团
        $data['home_code']  = userRoom();
        $data['goods_id']  = $order['goods_id'];
        $data['user_id']  = $order['user_id'];
        $data['type']  = 2;
        $data['award_bl']  = $group_num['award_bl'];
        $data['hb_bl']  = $group_num['hb_bl'];
        $data['award_bl']  = $group_num['award_bl'];
        $data['pt_price']  = $order['goods_price'];
        $data['pt_num']  = $goods['group_num'];
        $data['yp_num']  = 1;
        $data['sy_num']  = $goods['group_num']-1;
        $data['add_time']  = time();
        $data['end_time']  = time()+$goods['dao_time']*60;
        $data['status']  = 1; //拼团中
        $id = M("home")->add($data);
        if($id){
            $data2['home_id'] = $id;
            $data2['user_id'] = $order['user_id'];
            $data2['order_sn'] = $order['order_sn'];
            $data2['goods_id'] = $order['goods_id'];
            $data2['pay_code'] = $order['pay_code'];
            $data2['pay_name']  = $order['pay_name'];
            $data2['pay_time']  = $order['pay_time'];
            $data2['address_id']  = $order['address_id'];
            $data2['is_false']=setFalse($user);
            $data2['status']  = 1;  //拼团中
            $data2['add_time'] = time();
            M("home_order")->add($data2);
        }
    }
}
function getRoom($goods_id,$user_id,$home_id)
{
    $info_1 = M("home")->where("status=1 and goods_id=".$goods_id." and user_id !=".$user_id." and id >".$home_id)->order("id asc")->find();
    if($info_1){
        $order_count = M("home_order")->where("home_id=".$info_1['id']." and user_id=".$user_id)->count();
        if($order_count>0){
            return getRoom($goods_id,$user_id,$info_1['id']);
        }else{
            return $info_1;
        }
    }else{
        return $info_1;
    }
}
//判断是否设置拼团失败
function setFalse($user){
    $config =getConfig();
    $is_false = 1;
    return $is_false;
}
//执行发奖
function sendTeamAward($home){
    $win_num = $home['award_bl']; //中奖人数
    $setList = M("home_order")->where("is_false =3 and home_id=".$home['id'])->select();
    $setCount = count($setList);
    $arr = array();
    if($setCount==0){
        $list = M("home_order")->where("home_id=".$home['id'])->select();
        shuffle($list); //打乱数组先后顺序
        $sendArr = array();
        for($i=0;$i<$win_num;$i++){
            $sendArr[] = $list[$i];
        }
        foreach($sendArr as $key=>$val){
            //生成订单
            createOrder($home,$val);
            //更改状态
            M("home_order")->where("id=".$val['id'])->save(array('status'=>2));
            $arr[]=$val['user_id'];
        }
    }elseif($setCount==$win_num){
        foreach($setList as $kk=>$vv){
            //生成订单
            createOrder($home,$vv);
            //更改状态
            M("home_order")->where("id=".$vv['id'])->save(array('status'=>2));
            $arr[]=$vv['user_id'];
        }
    }elseif($setCount<$win_num){
        $notSet = array();
        foreach($setList as $kk=>$vv){
            $notSet[]=$vv['user_id'];
        }
        $whr['user_id'] = array('not in',$notSet);
        $whr['home_id']=$home['id'];
        $list= M("home_order")->where($whr)->select();
        shuffle($list); //打乱数组先后顺序
        $last_num = $win_num-$setCount;
        $sendArr = array();
        for($i=0;$i<$last_num;$i++){
            $sendArr[] = $list[$i];
        }
        $sendArr = array_merge($setList,$sendArr);
        foreach($sendArr as $key=> $val){
            //生成订单
            createOrder($home,$val);
            //更改状态
            M("home_order")->where("id=".$val['id'])->save(array('status'=>2));
            $arr[]=$val['user_id'];
        }
    }
    $map['user_id'] = array('not in',$arr);
    $map['home_id']=$home['id'];
    $failArr = M("home_order")->where($map)->select();
    foreach($failArr as $k=>$v){
        //本金退回
        M("users")->where("user_id=".$v['user_id'])->setInc("user_money",$home['pt_price']);
        upd_money($v['user_id'],$home['pt_price'],1,"未中奖本金退回",3);
        //奖励红包积分
        $hbAward = $home['pt_price']*($home['hb_bl']/100);
        if($hbAward>0){
            M("users")->where("user_id=".$v['user_id'])->setInc("user_money",$hbAward);
            upd_money($v['user_id'],$hbAward,1,"未中奖红包奖励",8);
            //分销奖励
            fxAward($v['user_id'],$hbAward);
        }
        //更改状态
        M("home_order")->where("id=".$v['id'])->save(array('status'=>4));
    }
    return true;
}
/*三级分销*/
function fxAward($user_id,$money){
    $config = getConfig();
    if($res = M("user_relation")->where("depth < 6 and user_id=".$user_id)->order("depth asc")->select()){
        foreach ($res as $kk=>$vv){
            $p_user = M("users")->where("user_id=".$vv['parent_user_id'])->field("user_id,level")->find();
            if($vv['depth']==1){
                $award_bl = $config['basic_fx_one_bl']/100;
            }elseif ($vv['depth']==2){
                $award_bl = $config['basic_fx_two_bl']/100;
            }elseif ($vv['depth']==3){
                $award_bl = $config['basic_fx_thr_bl']/100;
            }elseif ($vv['depth']==4){
                $award_bl = $config['basic_fx_four_bl']/100;
            }elseif ($vv['depth']==5){
                $award_bl = $config['basic_fx_five_bl']/100;
            }else{
                $award_bl = 0;
            }
            $award = $money*$award_bl;
            if($award>0){
                M("users")->where("user_id=".$p_user['user_id'])->setInc("user_money",$award);
                if($vv['depth']==1){
                    upd_money($p_user['user_id'],$award,1,"分销".$vv['depth']."代奖励",9);
                }else{
                    upd_money($p_user['user_id'],$award,1,"分销".$vv['depth']."代奖励",10);
                }
            }
        }
    }
    return true;
}
//创建订单
function createOrder($home,$homeOrder){
    $address = M('UserAddress')->where("address_id = ".$homeOrder['address_id'])->find();
    $data = array(
        'order_sn'         => $homeOrder['order_sn'], // 订单编号
        'user_id'          =>$homeOrder['user_id'], // 用户id
        'goods_id'         =>$homeOrder['goods_id'],
        'type'             =>'3', //拼团订单
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
        'shipping_price'   =>0,//'物流价格',
        'total_amount'     =>$home['pt_price'],// 订单总额
        'order_amount'     =>$home['pt_price'],//'应付款金额',
        'add_time'         =>time(), // 下单时间
        'pay_code'         =>$homeOrder['pay_code'],
        'pay_name'         =>$homeOrder['pay_name'],
        'pay_time'         =>$homeOrder['pay_time'],
        'pay_status'        =>1,
    );
    $order_id = M("Order")->data($data)->add();
    //order_goods 表
    $goods = M('goods')->where("goods_id = {$homeOrder['goods_id']} ")->find();
    $data2['order_id']           = $order_id; // 订单id
    $data2['goods_id']           = $goods['goods_id']; // 商品id
    $data2['goods_name']         = $goods['goods_name']; // 商品名称
    $data2['goods_sn']           = $goods['goods_sn']; // 商品货号
    $data2['goods_num']          = 1; // 购买数量
    $data2['market_price']       = $goods['market_price']; // 市场价
    $data2['goods_price']        = $goods['shop_price']; // 商品价
    $data2['spec_key']           = ''; // 商品规格
    $data2['spec_key_name']      = ''; // 商品规格名称
    $data2['sku']           	 = ''; // 商品sku
    $data2['cost_price']         = $goods['cost_price']; // 成本价
    $data2['give_integral']      = $goods['give_integral']; // 购买商品赠送积分
    M("OrderGoods")->data($data2)->add();
    // 减少对应商品的库存
    minus_stock($order_id);
    return true;
}
//新极差奖
function jicha_award_new($user_id,$money){
    $config = getConfig();
    $user = M('users')->where("user_id = '{$user_id}'")->find();

    //购物反自己积分
    $user_jifen = $money*$config['basic_buy_bei'];
    M("users")->where("user_id=".$user['user_id'])->setInc("pay_points",$user_jifen);
    upd_jifen($user['user_id'],$user_jifen,1,"购物返积分",4,$user_id);

    $res = M("user_relation")->where("user_id=".$user_id)->order("depth asc")->select();
    if($res){
        $depth = 1;
        $now_user_level = $user['level'];
        $once = 0;
        $pre_award_money = 0;
        foreach ($res as $kk=>$vv){
            if($depth >= 1){
                $p_user = M("users")->where("user_id=".$vv['parent_user_id'])->field("user_id,nickname,level")->find();
                $p_level = M("user_level")->where("level=".$p_user['level'])->find();
                if($now_user_level <= $p_user['level']){
                    if($once == 0){
                        $award = $p_level['jc_award'];
                        $award_money = $award*$money;
                        $pre_award_money = $award_money;
                        $once=1;
                        $now_user_level = $p_user['level'];
                    }else{
                        $awards = $p_level['jc_award'];
                        $awards_money = $awards*$money;
                        $award_money = $awards_money-$pre_award_money;
                        $pre_award_money = $awards_money;
                        $now_user_level = $p_user['level'];
                    }
                    if($award_money>0){
                        //更新积分
                        M("users")->where("user_id=".$p_user['user_id'])->setInc("pay_points",$award_money);
                        upd_jifen($p_user['user_id'],$award_money,1,$vv['depth']."代会员'".$p_user['nickname']."'购物返积分",4,$user_id);
                    }

                }
            }
            $depth++;
        }
    }
    return true;
}
//极差奖
function jicha_award($user_id,$buy_num){
    $config = getConfig();
    $user = M('users')->where("user_id = '{$user_id}'")->find();
    $res = M("user_relation")->where("user_id=".$user_id)->order("depth asc")->select();
    if($res){
        $depth = 1;
        $now_user_level = $user['level'];
        $once = 0;
        $pre_award_money = 0;
        foreach ($res as $kk=>$vv){
            $p_user = M("users")->where("user_id=".$vv['parent_user_id'])->field("user_id,level")->find();
            if($now_user_level < $p_user['level']){
                if($once == 0){
                    if($p_user['level']==2){ //代理
                        $award = $config['basic_jc_daili_rate'];
                        $level_award = $config['basic_jc_daili_rate'];
                    }elseif($p_user['level']==3){ //区代
                        if($now_user_level==2){
                            $award = $config['basic_jc_qd_rate']-$config['basic_jc_daili_rate'];
                        }else{
                            $award = $config['basic_jc_qd_rate'];
                        }
                        $level_award = $config['basic_jc_qd_rate'];
                    }elseif($p_user['level']==4){ //总代
                        if($now_user_level==2){
                            $award = $config['basic_jc_zd_rate']-$config['basic_jc_daili_rate'];

                        }elseif ($now_user_level==3){
                            $award = $config['basic_jc_zd_rate']-$config['basic_jc_qd_rate'];

                        }else{
                            $award = $config['basic_jc_zd_rate'];

                        }
                        $level_award = $config['basic_jc_zd_rate'];
                    }elseif($p_user['level']==5){ //大使
                        if($now_user_level==2){
                            $award = $config['basic_jc_ds_rate']-$config['basic_jc_daili_rate'];
                        }elseif ($now_user_level==3){
                            $award = $config['basic_jc_ds_rate']-$config['basic_jc_qd_rate'];

                        }elseif ($now_user_level==4){
                            $award = $config['basic_jc_ds_rate']-$config['basic_jc_zd_rate'];

                        }else{
                            $award = $config['basic_jc_ds_rate'];

                        }
                        $level_award = $config['basic_jc_ds_rate'];
                    }elseif($p_user['level']==6){ //总监
                        if($now_user_level==2){
                            $award = $config['basic_jc_zj_rate']-$config['basic_jc_daili_rate'];

                        }elseif ($now_user_level==3){
                            $award = $config['basic_jc_zj_rate']-$config['basic_jc_qd_rate'];

                        }elseif ($now_user_level==4){
                            $award = $config['basic_jc_zj_rate']-$config['basic_jc_zd_rate'];

                        }elseif ($now_user_level==5){
                            $award = $config['basic_jc_zj_rate']-$config['basic_jc_ds_rate'];

                        }else{
                            $award = $config['basic_jc_zj_rate'];

                        }
                        $level_award = $config['basic_jc_zj_rate'];
                    }elseif($p_user['level']==7){   //董事
                        if($now_user_level==2){
                            $award = $config['basic_jc_dongshi_rate']-$config['basic_jc_daili_rate'];

                        }elseif ($now_user_level==3){
                            $award = $config['basic_jc_dongshi_rate']-$config['basic_jc_qd_rate'];
                        }elseif ($now_user_level==4){
                            $award = $config['basic_jc_dongshi_rate']-$config['basic_jc_zd_rate'];
                        }elseif ($now_user_level==5){
                            $award = $config['basic_jc_dongshi_rate']-$config['basic_jc_ds_rate'];
                        }elseif ($now_user_level==6){
                            $award = $config['basic_jc_dongshi_rate']-$config['basic_jc_zj_rate'];
                        }else{
                            $award = $config['basic_jc_dongshi_rate'];
                        }
                        $level_award = $config['basic_jc_dongshi_rate'];
                    }
                    $award_money = $award*$buy_num;
                    $pre_award_money = $level_award*$buy_num;
                    $once=1;
                    $now_user_level = $p_user['level'];
                }else{
                    if($p_user['level']==2){
                        $awards = $config['basic_jc_daili_rate'];
                    }elseif($p_user['level']==3){
                        $awards = $config['basic_jc_qd_rate'];
                    }elseif($p_user['level']==4){
                        $awards = $config['basic_jc_zd_rate'];
                    }elseif($p_user['level']==5){
                        $awards = $config['basic_jc_ds_rate'];
                    }elseif($p_user['level']==6){
                        $awards = $config['basic_jc_zj_rate'];
                    }elseif($p_user['level']==7){
                        $awards = $config['basic_jc_dongshi_rate'];
                    }else{
                        $awards = 0;
                    }
                    $award_money = $awards*$buy_num-$pre_award_money;
                    $pre_award_money = $awards*$buy_num;
                    $now_user_level = $p_user['level'];
                }
                if($award_money>0){
                    $ye_bili = $config['basic_jj_ye_bili']/100;  //奖金70%进入余额
                    $jf_bili = $config['basic_jj_jf_bili']/100;  //奖金20%进入积分
                    $ax_bili = $config['basic_jj_ax_bili']/100;  //奖金10%进入爱心
                    $ye_award = $award_money * $ye_bili;
                    $jf_award = $award_money * $jf_bili;
                    $ax_award = $award_money * $ax_bili;
                    //更新余额
                    M("users")->where("user_id=".$p_user['user_id'])->setInc("user_money",$ye_award);
                    upd_money($p_user['user_id'],$ye_award,1,"奖金".$config['basic_jj_ye_bili']."%进余额",8,$user_id);
                    //更新积分
                    M("users")->where("user_id=".$p_user['user_id'])->setInc("pay_points",$jf_award);
                    upd_jifen($p_user['user_id'],$jf_award,1,"奖金".$config['basic_jj_jf_bili']."%进积分",9,$user_id);
                    //更新爱心
                    M("users")->where("user_id=".$p_user['user_id'])->setInc("aixin_jifen",$ax_award);
                    upd_aixin($p_user['user_id'],$ax_award,1,"奖金".$config['basic_jj_ax_bili']."%进爱心",3,$user_id);
                }
            }

            $depth++;
            $award_money = $award=$level_award=0;
        }
    }
    return true;
}
//(1)更新用户有效会员/直推人数和团队人数(2)会员升级
function update_number($order_id,$user_id){
    $config = getConfig();
    $user = M('users')->where("user_id = '{$user_id}'")->field("user_id,is_yx")->find();
    $orderGoodsArr = M('OrderGoods')->where("order_id = '{$order_id}'")->field("goods_id,goods_name")->select();
    foreach($orderGoodsArr as $key => $val)
    {
        $goods = M("goods")->where("goods_id=".$val['goods_id'])->field("goods_id,is_recommend")->find();
        if($user['is_yx']==0 && $goods['is_recommend']==1){
            M('users')->where("user_id = '{$user_id}'")->save(array('is_yx'=>1));  //更新有效会员
            $relation = M("user_relation")->where("user_id=".$user_id)->order("depth asc")->select();
            if($relation){
                foreach ($relation as $kk=>$vv){
                    if($vv['depth']==1){
                        M('users')->where("user_id = ".$vv['parent_user_id'])->setInc('zt_num',1); // 更新直推有效人数
                    }
                    M('users')->where("user_id = ".$vv['parent_user_id'])->setInc('team_num',1);  //团队有效会员人数
                    $parent_info = M('users')->where("user_id = ".$vv['parent_user_id'])->field("user_id,level,zt_num,team_num,is_yx")->find();
                    //统计金牌服务商和银牌服务商
                    $yin_num = M('users')->where("pid = ".$vv['parent_user_id']." and level >= 2")->count();
                    $jin_num = M('users')->where("pid = ".$vv['parent_user_id']." and level >= 3")->count();
                    //团队升级
                    if($parent_info['level']==1 && $parent_info['zt_num'] >= $config['basic_zt_num'] && $parent_info['is_yx']==1){
                        M('users')->where("user_id = ".$parent_info['user_id'])->save(array('level'=>2));
                    }elseif ($parent_info['level']==2 && $parent_info['team_num'] >= $config['basic_jin_num'] && $yin_num >= $config['basic_jin_zt_num'] && $parent_info['is_yx']==1){
                        M('users')->where("user_id = ".$parent_info['user_id'])->save(array('level'=>3));
                    }elseif ($parent_info['level']==3 && $parent_info['team_num'] >= $config['basic_zuan_num'] && $jin_num >= $config['basic_zuan_zt_num'] && $parent_info['is_yx']==1){
                        M('users')->where("user_id = ".$parent_info['user_id'])->save(array('level'=>4));
                    }
                }
            }
        }
    }
    return true;
}

// author :凌寒 2019年5月11日14:40:50 用户升级
function update_level($user_id){
   $config = getConfig();

    $user = M('users')->where("user_id = '{$user_id}'")->find();


    if($user['buy_num'] >= $config['basic_up_daili_num'] && $user['buy_num'] < $config['basic_up_qudai_num'] && $user['level']==1){
        //升级代理
        M('users')->where("user_id = '{$user_id}'")->save(array('level'=>2));
    }elseif($user['level'] < 3 && $user['team_num'] >= $config['basic_up_qudai_num']){
        //升级区代
        M('users')->where("user_id = '{$user_id}'")->save(array('level'=>3));
    }else{
        $sy_team_num = sy_team_num($user_id);  //计算剩余团队的团队业绩
        if($user['level'] < 4 && $user['team_num'] >= $config['basic_up_zongdai_num'] && $user['team_num'] < $config['basic_up_dashi_num'] && $sy_team_num >= $config['basic_up_zongdai_qy_num']){
            //升级总代
            M('users')->where("user_id = '{$user_id}'")->save(array('level'=>4));
        }elseif ($user['level'] < 5 && $user['team_num'] >= $config['basic_up_dashi_num'] && $user['team_num'] < $config['basic_up_zongjian_num'] && $sy_team_num >= $config['basic_up_dashi_qy_num']){
            //升级大使
            M('users')->where("user_id = '{$user_id}'")->save(array('level'=>5));
        }elseif ($user['level'] < 6 && $user['team_num'] >= $config['basic_up_zongjian_num'] && $user['team_num'] < $config['basic_up_dongshi_num'] && $sy_team_num >= $config['basic_up_zongjian_qy_num']){
            //升级总监
            M('users')->where("user_id = '{$user_id}'")->save(array('level'=>6));
        }elseif ($user['level'] < 7 && $user['team_num'] >= $config['basic_up_dongshi_num'] && $sy_team_num >= $config['basic_up_dongshi_qy_num']){
            //升级董事
            M('users')->where("user_id = '{$user_id}'")->save(array('level'=>7));
        }
    }
    //上级升级
    if($relation =M("user_relation")->where("user_id=".$user_id)->order("depth asc")->select()){
        foreach ($relation as $kk=>$vv){
            $p_user = M('users')->where("user_id = ".$vv['parent_user_id'])->field("user_id,level,team_num")->find();
            $p_sy_team_num = sy_team_num($p_user['user_id']);  //计算剩余团队的团队业绩
            if($p_user['team_num'] >= $config['basic_up_qudai_num'] && $p_user['level'] < 3){
                //升级区代
                M('users')->where("user_id = ".$p_user['user_id'])->save(array('level'=>3));
            }elseif($p_user['level'] < 4 && $p_user['team_num'] >= $config['basic_up_zongdai_num'] && $p_user['team_num'] < $config['basic_up_dashi_num'] && $p_sy_team_num >= $config['basic_up_zongdai_qy_num']){
                //升级总代
                M('users')->where("user_id = ".$p_user['user_id'])->save(array('level'=>4));
            }elseif ($p_user['level'] < 5 && $p_user['team_num'] >= $config['basic_up_dashi_num'] && $p_user['team_num'] < $config['basic_up_zongjian_num'] && $p_sy_team_num >= $config['basic_up_dashi_qy_num']){
                //升级大使
                M('users')->where("user_id = ".$p_user['user_id'])->save(array('level'=>5));
            }elseif ($p_user['level'] < 6 && $p_user['team_num'] >= $config['basic_up_zongjian_num'] && $p_user['team_num'] < $config['basic_up_dongshi_num'] && $p_sy_team_num >= $config['basic_up_zongjian_qy_num']){
                //升级总监
                M('users')->where("user_id = ".$p_user['user_id'])->save(array('level'=>6));

            }elseif ($p_user['level'] < 7 && $p_user['team_num'] >= $config['basic_up_dongshi_num'] && $p_sy_team_num >= $config['basic_up_dongshi_qy_num']){
                //升级董事
                M('users')->where("user_id = ".$p_user['user_id'])->save(array('level'=>7));
            }
        }
    }
}

// author :凌寒 2019年5月11日15:40:58  计算剩余团队的团队业绩
function sy_team_num($user_id){
    $max_team = M('users')->where("pid = '{$user_id}'")->field("user_id,buy_num,team_num")->order("team_num desc")->find();
    $sy_team_num = 0;
    if($max_team){
        $sy_team = M('users')->where("pid = '{$user_id}' and user_id != '{$max_team[user_id]}'")->field("user_id,buy_num,team_num")->select();
        if($sy_team){
            foreach ($sy_team as $kk=>$vv){
                $sy_team_num +=$vv['team_num'];
            }
        }
    }
    return $sy_team_num;
}
// author :凌寒 2019年5月11日16:33:33 运营中心奖励
function yunying_award($user_id,$buy_num){
    $config = getConfig();
    $bili = $config['basic_yunying_bili'];  //运营中心奖励比例
    $ye_bili = $config['basic_jj_ye_bili']/100;  //奖金70%进入余额
    $jf_bili = $config['basic_jj_jf_bili']/100;  //奖金20%进入积分
    $ax_bili = $config['basic_jj_ax_bili']/100;  //奖金10%进入爱心
    $award = $bili*$buy_num;
    if($award > 0){
        $ye_award = $award * $ye_bili;
        $jf_award = $award * $jf_bili;
        $ax_award = $award * $ax_bili;
        $user = M('users')->where("user_id = '{$user_id}'")->find();
        if($user['city']){
            $yy_users = M('users')->where("city = '{$user[city]}' and is_yyzx = 1")->field("user_id")->select();
            if($yy_users){
                foreach ($yy_users as $kk=>$vv){
                    //更新余额
                    M("users")->where("user_id=".$vv['user_id'])->setInc("user_money",$ye_award);
                    upd_money($vv['user_id'],$ye_award,1,"奖金".$config['basic_jj_ye_bili']."%进余额",7,$user_id);
                    //更新积分
                    M("users")->where("user_id=".$vv['user_id'])->setInc("pay_points",$jf_award);
                    upd_jifen($vv['user_id'],$jf_award,1,"奖金".$config['basic_jj_jf_bili']."%进消费积分",8,$user_id);
                    //更新爱心
                    M("users")->where("user_id=".$vv['user_id'])->setInc("aixin_jifen",$ax_award);
                    upd_aixin($vv['user_id'],$ax_award,1,"奖金".$config['basic_jj_ax_bili']."%进爱心积分",2,$user_id);
                }
            }
        }
    }
    return true;
}
// author :凌寒 2019年5月11日16:33:33 运营中心奖励(复制)
function yunying_awards($user_id,$money){
    $config = getConfig();
    $bili = $config['basic_yunying_bili']/100;  //运营中心奖励比例
    $ye_bili = $config['basic_jj_ye_bili']/100;  //奖金70%进入余额
    $jf_bili = $config['basic_jj_jf_bili']/100;  //奖金20%进入积分
    $ax_bili = $config['basic_jj_ax_bili']/100;  //奖金10%进入爱心
    $award = $bili*$money;
    if($award > 0){
        $ye_award = $award * $ye_bili;
        $jf_award = $award * $jf_bili;
        $ax_award = $award * $ax_bili;
        $user = M('users')->where("user_id = '{$user_id}'")->find();
        if($user['city']){
            $yy_users = M('users')->where("city = '{$user[city]}' and is_yyzx = 1")->field("user_id")->select();
            if($yy_users){
                foreach ($yy_users as $kk=>$vv){
                    //更新余额
                    M("users")->where("user_id=".$vv['user_id'])->setInc("user_money",$ye_award);
                    upd_money($vv['user_id'],$ye_award,1,"奖金".$config['basic_jj_ye_bili']."%进余额",7,$user_id);
                    //更新积分
                    M("users")->where("user_id=".$vv['user_id'])->setInc("pay_points",$jf_award);
                    upd_jifen($vv['user_id'],$jf_award,1,"奖金".$config['basic_jj_jf_bili']."%进消费积分",8,$user_id);
                    //更新爱心
                    M("users")->where("user_id=".$vv['user_id'])->setInc("aixin_jifen",$ax_award);
                    upd_aixin($vv['user_id'],$ax_award,1,"奖金".$config['basic_jj_ax_bili']."%进爱心积分",2,$user_id);
                }
            }
        }
    }
    return true;
}
// author :凌寒 2019年3月7日15:35:54 返奖记录(走太阳线)
function update_award($order){
    $config = getConfig();
    $orderGoodsArr = M('OrderGoods')->where("order_id = ".$order['order_id'])->select();
    $total_money =0;
    foreach ($orderGoodsArr as $kk=>$vv){
        $orderGoodsArr[$kk]['is_tejia'] = M("goods")->where("goods_id=".$vv['goods_id'])->getField("is_tejia");
        if($vv['is_tejia']==0){
            //更新用户累计消费金额(只增不减)
            M('users')->where(array('user_id'=>$order['user_id']))->setInc('total_amount',$vv['member_goods_price']);
            $total_money +=$vv['member_goods_price'];
        }
    }
    $order['total_money'] = $total_money;
    $user_relation = M("user_relation")->where(array('user_id'=>$order['user_id']))->order('depth asc')->select();
    if($user_relation){
        foreach ($user_relation as $kk=>$vv){
            $p_info = M("users")->where("user_id=".$vv['parent_user_id'])->field('user_id,level')->find();
            if($p_info['level']==2){  //兼职营业员
                if($vv['depth']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['depth']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['depth']>=3 && $vv['depth']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }
            }elseif ($p_info['level']==3){ //县级代理
                if($vv['depth']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['depth']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['depth']>=3 && $vv['depth']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }elseif ($vv['depth']>=6 && $vv['depth']<=8){
                    $bili = $config['basic_s_e_rate']/100;
                }
            }elseif ($p_info['level']==4){  //市级代理
                if($vv['depth']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['depth']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['depth']>=3 && $vv['depth']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }elseif ($vv['depth']>=6 && $vv['depth']<=8){
                    $bili = $config['basic_s_e_rate']/100;
                }elseif ($vv['depth']>=9 && $vv['depth']<=11){
                    $bili = $config['basic_n_e_rate']/100;
                }
            }elseif ($p_info['level']==5){  //省级代理
                if($vv['depth']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['depth']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['depth']>=3 && $vv['depth']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }elseif ($vv['depth']>=6 && $vv['depth']<=8){
                    $bili = $config['basic_s_e_rate']/100;
                }elseif ($vv['depth']>=9 && $vv['depth']<=11){
                    $bili = $config['basic_n_e_rate']/100;
                }elseif ($vv['depth']>=12 && $vv['depth']<=14){
                    $bili = $config['basic_t_four_rate']/100;
                }elseif ($vv['depth']>=15 && $vv['depth']<=17){
                    $bili = $config['basic_f_s_rate']/100;
                }
            }elseif ($p_info['level']==6){  //区域代理
                if($vv['depth']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['depth']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['depth']>=3 && $vv['depth']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }elseif ($vv['depth']>=6 && $vv['depth']<=8){
                    $bili = $config['basic_s_e_rate']/100;
                }elseif ($vv['depth']>=9 && $vv['depth']<=11){
                    $bili = $config['basic_n_e_rate']/100;
                }elseif ($vv['depth']>=12 && $vv['depth']<=14){
                    $bili = $config['basic_t_four_rate']/100;
                }elseif ($vv['depth']>=15 && $vv['depth']<=17){
                    $bili = $config['basic_f_s_rate']/100;
                }
            }else{
                $bili=0;
            }
            if($bili>0){
                $money = $order['total_money']*$bili;
                //添加购物钱包记录
                $data['user_id'] = $p_info['user_id'];
                $data['user_money'] = $money/2;
                $data['change_time'] = time();
                $data['order_id'] = $order['order_id'];
                $data['desc'] = "购物币+".$data['user_money'];
                $data['from_user_id'] = $order['user_id'];
                $data['type'] = 1;
                $id = M("account_log")->add($data);
                if($id){
                    M('users')->where(array('user_id'=>$p_info['user_id']))->setInc('shop_money',$money/2);
                }
                //添加购物钱包记录
                $datas['user_id'] = $p_info['user_id'];
                $datas['user_money'] = $money/2;
                $datas['change_time'] = time();
                $datas['order_id'] = $order['order_id'];
                $datas['desc'] = "奖金币+".$data['user_money'];
                $datas['from_user_id'] = $order['user_id'];
                $datas['type'] = 2;
                $ids= M("account_log")->add($datas);
                if($ids){
                    M('users')->where(array('user_id'=>$p_info['user_id']))->setInc('award_money',$money/2);
                }
            }
        }
    }
}
// author :凌寒 2019年3月7日15:35:54 返奖记录(走节点关系)
function update_award_two($order){
    $config = getConfig();
    $orderGoodsArr = M('OrderGoods')->where("order_id = ".$order['order_id'])->select();
    $total_money =0;
    foreach ($orderGoodsArr as $kk=>$vv){
        $orderGoodsArr[$kk]['is_tejia'] = M("goods")->where("goods_id=".$vv['goods_id'])->getField("is_tejia");
        if($vv['is_tejia']==0){
            $total_money +=$vv['member_goods_price'];
        }
    }
    $order['total_money'] = $total_money;
    $user_relation = M("user_node")->where(array('newuserid'=>$order['user_id']))->order('cengshu asc')->select();
    if($user_relation){
        foreach ($user_relation as $kk=>$vv){
            $p_info = M("users")->where("user_id=".$vv['userid'])->field('user_id,level')->find();
            if($p_info['level']==2){  //兼职营业员
                if($vv['cengshu']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['cengshu']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['cengshu']>=3 && $vv['cengshu']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }
            }elseif ($p_info['level']==3){ //县级代理
                if($vv['cengshu']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['cengshu']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['cengshu']>=3 && $vv['cengshu']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }elseif ($vv['cengshu']>=6 && $vv['cengshu']<=8){
                    $bili = $config['basic_s_e_rate']/100;
                }
            }elseif ($p_info['level']==4){  //市级代理
                if($vv['cengshu']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['cengshu']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['cengshu']>=3 && $vv['cengshu']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }elseif ($vv['cengshu']>=6 && $vv['cengshu']<=8){
                    $bili = $config['basic_s_e_rate']/100;
                }elseif ($vv['cengshu']>=9 && $vv['cengshu']<=11){
                    $bili = $config['basic_n_e_rate']/100;
                }
            }elseif ($p_info['level']==5){  //省级代理
                if($vv['cengshu']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['cengshu']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['cengshu']>=3 && $vv['cengshu']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }elseif ($vv['cengshu']>=6 && $vv['cengshu']<=8){
                    $bili = $config['basic_s_e_rate']/100;
                }elseif ($vv['cengshu']>=9 && $vv['cengshu']<=11){
                    $bili = $config['basic_n_e_rate']/100;
                }elseif ($vv['cengshu']>=12 && $vv['cengshu']<=14){
                    $bili = $config['basic_t_four_rate']/100;
                }elseif ($vv['cengshu']>=15 && $vv['cengshu']<=17){
                    $bili = $config['basic_f_s_rate']/100;
                }
            }elseif ($p_info['level']==6){  //区域代理
                if($vv['cengshu']==1){
                    $bili = $config['basic_one_rate']/100;
                }elseif ($vv['cengshu']==2){
                    $bili = $config['basic_two_rate']/100;
                }elseif ($vv['cengshu']>=3 && $vv['cengshu']<=5){
                    $bili = $config['basic_t_f_rate']/100;
                }elseif ($vv['cengshu']>=6 && $vv['cengshu']<=8){
                    $bili = $config['basic_s_e_rate']/100;
                }elseif ($vv['cengshu']>=9 && $vv['cengshu']<=11){
                    $bili = $config['basic_n_e_rate']/100;
                }elseif ($vv['cengshu']>=12 && $vv['cengshu']<=14){
                    $bili = $config['basic_t_four_rate']/100;
                }elseif ($vv['cengshu']>=15 && $vv['cengshu']<=17){
                    $bili = $config['basic_f_s_rate']/100;
                }
            }else{
                $bili=0;
            }
            if($bili>0){
                $money = $order['total_money']*$bili;
                //添加购物钱包记录
                $data['user_id'] = $p_info['user_id'];
                $data['user_money'] = $money/2;
                $data['change_time'] = time();
                $data['order_id'] = $order['order_id'];
                $data['desc'] = "见点奖(购物币+".$data['user_money'].")";
                $data['from_user_id'] = $order['user_id'];
                $data['type'] = 1;
                $id = M("account_log")->add($data);
                if($id){
                    M('users')->where(array('user_id'=>$p_info['user_id']))->setInc('shop_money',$money/2);
                }
                //添加购物钱包记录
                $datas['user_id'] = $p_info['user_id'];
                $datas['user_money'] = $money/2;
                $datas['change_time'] = time();
                $datas['order_id'] = $order['order_id'];
                $datas['desc'] = "见点奖(现金币+".$data['user_money'].")";
                $datas['from_user_id'] = $order['user_id'];
                $datas['type'] = 2;
                $ids= M("account_log")->add($datas);
                if($ids){
                    M('users')->where(array('user_id'=>$p_info['user_id']))->setInc('award_money',$money/2);
                }
            }
        }
    }
}
/**
 * 订单确认收货
 * @param $id   订单id
 */
function confirm_order($id,$user_id = 0){

    $where = "order_id = $id";
    $user_id && $where .= " and user_id = $user_id ";

    $order = M('order')->where($where)->find();
    if($order['order_status'] != 1)
        return array('status'=>-1,'msg'=>'该订单不能收货确认');

    $data['order_status'] = 2; // 已收货
    $data['pay_status'] = 1; // 已付款
    $data['confirm_time'] = time(); // 收货确认时间
    if($order['pay_code'] == 'cod'){
        $data['pay_time'] = time();
    }
    $row = M('order')->where(array('order_id'=>$id))->save($data);
    if(!$row)
        return array('status'=>-3,'msg'=>'操作失败');

//    //更新用户累计消费金额
//    $orderGoodsArr = M('OrderGoods')->where("order_id = ".$order['order_id'])->select();
//    foreach ($orderGoodsArr as $kk=>$vv){
//        $orderGoodsArr[$kk]['is_tejia'] = M("goods")->where("goods_id=".$vv['goods_id'])->getField("is_tejia");
//        if($vv['is_tejia']==0){
//            //更新用户累计消费金额(只增不减)
//            M('users')->where(array('user_id'=>$order['user_id']))->setInc('total_amount',$vv['member_goods_price']);
//        }
//    }

//    // 给他升级, 根据用户消费的累计额度达到级别入规
//    update_user_level($order['user_id']);

//    //返奖记录(走节点关系)
//    update_award_two($order);
//        //分销设置
//        M('rebate_log')->where("order_id = $id")->save(array('status'=>2,'confirm'=>time()));

    return array('status'=>1,'msg'=>'操作成功');
}

/**
 * 给订单送券送积分 送东西
 */
function order_give($order)
{
    $order_goods = M('order_goods')->where("order_id=".$order['order_id'])->cache(true)->select();
    //查找购买商品送优惠券活动
    foreach ($order_goods as $val)
    {
        if($val['prom_type'] == 3)
        {
            $prom = M('prom_goods')->where('type=3 and id='.$val['prom_id'])->find();
            if($prom){
                $coupon = M('coupon')->where("id=".$prom['expression'])->find();//查找优惠券模板
                if($coupon && $coupon['createnum']>0){
                    $remain = $coupon['createnum'] - $coupon['send_num'];//剩余派发量
                    if($remain > 0)
                    {
                        $data = array('cid'=>$coupon['id'],'type'=>$coupon['type'],'uid'=>$order['user_id'],'send_time'=>time());
                        M('coupon_list')->add($data);
                        M('Coupon')->where("id = {$coupon['id']}")->setInc('send_num'); // 优惠券领取数量加一
                    }
                }
            }
        }
    }

    //查找订单满额送优惠券活动
    $pay_time = $order['pay_time'];
    $prom = M('prom_order')->where("type>1 and end_time>$pay_time and start_time<$pay_time and money<=".$order['order_amount'])->order('money desc')->find();
    if($prom){
        if($prom['type']==3){
            $coupon = M('coupon')->where("id=".$prom['expression'])->find();//查找优惠券模板
            if($coupon){
                if($coupon['createnum']>0){
                    $remain = $coupon['createnum'] - $coupon['send_num'];//剩余派发量
                    if($remain > 0)
                    {
                        $data = array('cid'=>$coupon['id'],'type'=>$coupon['type'],'uid'=>$order['user_id'],'send_time'=>time());
                        M('coupon_list')->add($data);
                        M('Coupon')->where("id = {$coupon['id']}")->setInc('send_num'); // 优惠券领取数量加一
                    }
                }
            }
        }else if($prom['type']==2){
            accountLog($order['user_id'], 0 , $prom['expression'] ,"订单活动赠送积分");
        }
    }
    $points = M('order_goods')->where("order_id = {$order[order_id]}")->sum("give_integral * goods_num");
    $points && accountLog($order['user_id'], 0,$points,"下单赠送积分");
}


/**
 * 查看商品是否有活动
 * @param goods_id 商品ID
 */

function get_goods_promotion($goods_id,$user_id=0){
    $now = time();
    $goods = M('goods')->where("goods_id=$goods_id")->find();
    $where = "end_time>$now and start_time<$now and id=".$goods['prom_id'];

    $prom['price'] = $goods['shop_price'];
    $prom['prom_type'] = $goods['prom_type'];
    $prom['prom_id'] = $goods['prom_id'];
    $prom['is_end'] = 0;

    if($goods['prom_type'] == 1){//抢购
        $prominfo = M('flash_sale')->where($where)->find();
        if(!empty($prominfo)){
            if($prominfo['goods_num'] == $prominfo['buy_num']){
                $prom['is_end'] = 2;//已售馨
            }else{
                //核查用户购买数量
                $where = "user_id = $user_id and order_status!=3 and  add_time>".$prominfo['start_time']." and add_time<".$prominfo['end_time'];
                $order_id_arr = M('order')->where($where)->getField('order_id',true);
                if($order_id_arr){
                    $goods_num = M('order_goods')->where("prom_id={$goods['prom_id']} and prom_type={$goods['prom_type']} and order_id in (".implode(',', $order_id_arr).")")->sum('goods_num');
                    if($goods_num < $prominfo['buy_limit']){
                        $prom['price'] = $prominfo['price'];
                    }
                }else{
                    $prom['price'] = $prominfo['price'];
                }
            }
        }
    }

    if($goods['prom_type']==2){//团购
        $prominfo = M('group_buy')->where($where)->find();
        if(!empty($prominfo)){
            if($prominfo['goods_num'] == $prominfo['buy_num']){
                $prom['is_end'] = 2;//已售馨
            }else{
                $prom['price'] = $prominfo['price'];
            }
        }
    }
    if($goods['prom_type'] == 3){//优惠促销
        $parse_type = array('0'=>'直接打折','1'=>'减价优惠','2'=>'固定金额出售','3'=>'买就赠优惠券','4'=>'买M件送N件');
        $prominfo = M('prom_goods')->where($where)->find();
        if(!empty($prominfo)){
            if($prominfo['type'] == 0){
                $prom['price'] = $goods['shop_price']*$prominfo['expression']/100;//打折优惠
            }elseif($prominfo['type'] == 1){
                $prom['price'] = $goods['shop_price']-$prominfo['expression'];//减价优惠
            }elseif($prominfo['type']==2){
                $prom['price'] = $prominfo['expression'];//固定金额优惠
            }
        }
    }

    if(!empty($prominfo)){
        $prom['start_time'] = $prominfo['start_time'];
        $prom['end_time'] = $prominfo['end_time'];
    }else{
        $prom['prom_type'] = $prom['prom_id'] = 0 ;//活动已过期
        $prom['is_end'] = 1;//已结束
    }

    if($prom['prom_id'] == 0){
        M('goods')->where("goods_id=$goods_id")->save($prom);
    }
    return $prom;
}

/**
 * 查看订单是否满足条件参加活动
 * @param order_amount 订单应付金额
 */
function get_order_promotion($order_amount){
    $parse_type = array('0'=>'满额打折','1'=>'满额优惠金额','2'=>'满额送倍数积分','3'=>'满额送优惠券','4'=>'满额免运费');
    $now = time();
    $prom = M('prom_order')->where("type<2 and end_time>$now and start_time<$now and money<=$order_amount")->order('money desc')->find();
    $res = array('order_amount'=>$order_amount,'order_prom_id'=>0,'order_prom_amount'=>0);
    if($prom){
        if($prom['type'] == 0){
            $res['order_amount']  = round($order_amount*$prom['expression']/100,2);//满额打折
            $res['order_prom_amount'] = $order_amount - $res['order_amount'] ;
            $res['order_prom_id'] = $prom['id'];
        }elseif($prom['type'] == 1){
            $res['order_amount'] = $order_amount- $prom['expression'];//满额优惠金额
            $res['order_prom_amount'] = $prom['expression'];
            $res['order_prom_id'] = $prom['id'];
        }
    }
    return $res;
}

/**
 * 计算订单金额
 * @param type $user_id  用户id
 * @param type $order_goods  购买的商品
 * @param type $shipping  物流code
 * @param type $shipping_price 物流费用, 如果传递了物流费用 就不在计算物流费
 * @param type $province  省份
 * @param type $city 城市
 * @param type $district 县
 * @param type $pay_points 积分
 * @param type $user_money 余额
 * @param type $coupon_id  优惠券
 * @param type $couponCode  优惠码
 */

function calculate_price($user_id=0,$order_goods,$shipping_code='',$shipping_price=0,$province=0,$city=0,$district=0,$pay_points=0,$user_money=0,$coupon_id=0,$couponCode='')
{
    $cartLogic = new \Home\Logic\CartLogic();
    $user = M('users')->where("user_id = $user_id")->find();// 找出这个用户
    $amount = M("user_level")->where(array('level'=>$user['level']))->getField('amount');   //复购金额


    if(empty($order_goods))
        return array('status'=>-9,'msg'=>'商品列表不能为空','result'=>'');

    $goods_id_arr = get_arr_column($order_goods,'goods_id');
    $goods_arr = M('goods')->where("goods_id in(".  implode(',',$goods_id_arr).")")->getField('goods_id,weight,market_price,is_free_shipping'); // 商品id 和重量对应的键值对
    $goods_price = 0;
    $cut_fee = 0;
    $anum = 0;
    $total_jifen = 0;
    foreach($order_goods as $key => $val)
    {
        $goods_jifen = M("goods")->where("goods_id=".$val['goods_id'])->getField("jifen_price");
        $yunfei = M("goods")->where("goods_id=".$val['goods_id'])->getField("yunfei");
        $per_yunfei = M("goods")->where("goods_id=".$val['goods_id'])->getField("per_yunfei");
        $order_goods[$key]['member_goods_price'] = $val['member_goods_price'] = $amount;

        $order_goods[$key]['goods_fee'] = $val['goods_num'] * $val['goods_price'];    // 小计
        $order_goods[$key]['store_count']  = getGoodNum($val['goods_id'],$val['spec_key']); // 最多可购买的库存数量
        if($order_goods[$key]['store_count'] <= 0)
            return array('status'=>-10,'msg'=>$order_goods[$key]['goods_name']."库存不足,请重新下单",'result'=>'');
        $shipping_price += $yunfei*1+($val['goods_num']-1)*$per_yunfei;
        $goods_price += $order_goods[$key]['goods_price']*$val['goods_num']; // 商品总价
        $cut_fee     += $val['goods_num'] * $val['goods_price'] - $val['goods_num'] * $val['goods_price']; // 共节约
        $anum        += $val['goods_num']; // 购买数量
        $total_jifen = $val['goods_num']*$goods_jifen;
    }

    $order_amount = $goods_price+ $shipping_price - $cut_fee; // 应付金额 = 商品价格 + 物流费 - 优惠券

    $total_amount = $goods_price;
    //订单总价  应付金额  物流费  商品总价 节约金额 共多少件商品 积分  余额  优惠券
    $result = array(
        'total_amount'      => $total_amount, // 商品总价
        'order_amount'      => $order_amount, // 应付金额
        'shipping_price'    => $shipping_price, // 物流费
        'goods_price'       => $goods_price, // 商品总价
        'cut_fee'           => $cut_fee, // 共节约多少钱
        'anum'              => $anum, // 商品总共数量
        'integral_money'    => $total_jifen,  // 积分抵消金额
        'user_money'        => $user_money, // 使用余额
        'coupon_price'      => 0,// 优惠券抵消金额
        'order_goods'       => $order_goods, // 商品列表 多加几个字段原样返回
    );

    return array('status'=>1,'msg'=>"计算价钱成功",'result'=>$result); // 返回结果状态
}

/**
 * 获取商品一二三级分类
 * @return type
 */
function get_goods_category_tree(){
    $result = array();
    $cat_list = M('goods_category')->where("is_show = 1")->order('sort_order')->select();//所有分类

    foreach ($cat_list as $val){
        if($val['level'] == 2){
            $arr[$val['parent_id']][] = $val;
        }
        if($val['level'] == 3){
            $crr[$val['parent_id']][] = $val;
        }
        if($val['level'] == 1){
            $tree[] = $val;
        }
    }

    foreach ($arr as $k=>$v){
        foreach ($v as $kk=>$vv){
            $arr[$k][$kk]['sub_menu'] = empty($crr[$vv['id']]) ? array() : $crr[$vv['id']];
        }
    }

    foreach ($tree as $val){
        $val['tmenu'] = empty($arr[$val['id']]) ? array() : $arr[$val['id']];
        $result[$val['id']] = $val;
    }
    return $result;
}
// author :凌寒 2018年6月19日16:18:27 上传文件调用的方法
function upload_file($files,$type){

    $name = array_keys($files);

    $name = $name[0];

    $ext = extend($files[$name]['name']);

    if($type == 'image'){
        $extArr = array("jpg", "png", "gif","jpeg",'heic');
        if(in_array($ext,$extArr)){
            $img_url = "./uploads/image/".date('Y-m-d',time()).'/';
        }else{
            return 'type_error';
        }
    }elseif($type == 'video'){
        $extArr = array("wma", "mp4", "rmvb","wav",'mid','wav','avi','mov','wmv','vcd','svd','asf','rmvb','rm','mpg');
        if(in_array($ext,$extArr)){
            $img_url = "./uploads/video/".date('Y-m-d',time()).'/';
        }else{
            return 'type_error';
        }
    } elseif($type == 'txt'){
        if($ext == 'txt'){
            $img_url = "./Uploads/txt/".date('Y-m-d',time()).'/';
        }else{
            return 'type_error';
        }
    }elseif($type == 'ppt'){
        if($ext == 'pptx'){
            $img_url = "./Uploads/ppt/".date('Y-m-d',time()).'/';
        }elseif($ext == 'ppt'){
            $img_url = "./Uploads/ppt/".date('Y-m-d',time()).'/';
        }else{
            return 'type_error';
        }
    } else{
        return 'type_error';
    }


    if (!file_exists($img_url)) {
        mkdir($img_url,0777,true);
    }
    $image_name = time().rand(100,999).".".$ext;//图片名字
    $tmp = $files[$name]['tmp_name'];//临时名字

    if(move_uploaded_file($tmp,$img_url.$image_name)){
        if($ext == 'txt' || $ext == 'ppt'){
            $body = file_get_contents('http://'.$_SERVER['SERVER_NAME'].$img_url.$image_name);
            $encode = mb_detect_encoding($body, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));

            if($encode != 'UTF-8'){
                $body=iconv("gb2312", "utf-8//IGNORE",$body);
                $encode = mb_detect_encoding($body, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
                file_put_contents($img_url.$image_name, $body);
            }
        }
        if($ext == 'lrc'){
            $body = file_get_contents('http://'.$_SERVER['SERVER_NAME'].$img_url.$image_name);
            $encode = mb_detect_encoding($body, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
            if($encode != 'UTF-8'){
                $body=iconv("gb2312", "utf-8//IGNORE",$body);
                $encode = mb_detect_encoding($body, array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
                file_put_contents($img_url.$image_name, $body);
            }
        }
        /**
         * 不知道什么意思的代码
         * 也不知道有什么作用
         * 先注释掉了再说
         * 日后如果你有幸接触到了这串代码
         * 请把它延续下去
         */
//        if($ext == 'mp3' || $ext == 'wav'){
//            $filename = ltrim($img_url.$image_name,'.');
//            $filenames = '/www/wwwroot/www.chaihuor.com'.$filename;
//            $name_arr = explode('.',$filename);
//            //高品质mp3 320kbps
//            exec('ffmpeg -i '.$filenames.' -ab 320k /www/wwwroot/www.chaihuor.com'.$name_arr[0].'-high.mp3');
//            //普通品质MP3  128kbps
//            exec('ffmpeg -i '.$filenames.' -ab 128k /www/wwwroot/www.chaihuor.com'.$name_arr[0].'-general.mp3');
//            //shell脚本(暂时不能使用 估计权限问题)
//            // system("/compress.sh ". $filename, $status);
//        }
        return ltrim($img_url.$image_name,'.');
    }else{
        return 'error';
    }


}

//获取文件类型后缀
function extend($file_name){
    $extend = pathinfo($file_name);//获取文件扩展名
    $extend = strtolower($extend["extension"]);
    return $extend;
}

