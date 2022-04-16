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

/**
 * 管理员操作记录
 * @param $log_url 操作URL
 * @param $log_info 记录信息
 */
function adminLog($log_info){
    $add['log_time'] = time();
    $add['admin_id'] = session('admin_id');
    $add['log_info'] = $log_info;
    $add['log_ip'] = getIP();
    $add['log_url'] = __ACTION__;
    M('admin_log')->add($add);
}


function getAdminInfo($admin_id){
	return D('admin')->where("admin_id=$admin_id")->find();
}

function tpversion()
{     
    if(!empty($_SESSION['isset_push']))
        return false;    
    $_SESSION['isset_push'] = 1;    
    error_reporting(0);//关闭所有错误报告
    $app_path = dirname($_SERVER['SCRIPT_FILENAME']).'/';
    $version_txt_path = $app_path.'/Application/Admin/Conf/version.txt';
    $curent_version = file_get_contents($version_txt_path);
    
    $vaules = array(            
            'domain'=>$_SERVER['HTTP_HOST'], 
            'last_domain'=>$_SERVER['HTTP_HOST'], 
            'key_num'=>$curent_version, 
            'install_time'=>INSTALL_DATE, 
            'cpu'=>'0001',
            'mac'=>'0002',
            'serial_number'=>SERIALNUMBER,
            );     
     $url = "http://service.tp-shop.cn/index.php?m=Home&c=Index&a=user_push&".http_build_query($vaules);
     stream_context_set_default(array('http' => array('timeout' => 3)));
     file_get_contents($url);       
}
 
/**
 * 面包屑导航  用于后台管理
 * 根据当前的控制器名称 和 action 方法
 */
function navigate_admin()
{        
    $navigate = include APP_PATH.'Common/Conf/navigate.php';    
    $location = strtolower('Admin/'.CONTROLLER_NAME);
    $arr = array(
        '后台首页'=>'javascript:void();',
        $navigate[$location]['name']=>'javascript:void();',
        $navigate[$location]['action'][ACTION_NAME]=>'javascript:void();',
    );
    return $arr;
}

/**
 * 导出excel
 * @param $strTable	表格内容
 * @param $filename 文件名
 */
function downloadExcel($strTable,$filename)
{
	header("Content-type: application/vnd.ms-excel");
	header("Content-Type: application/force-download");
	header("Content-Disposition: attachment; filename=".$filename."_".date('Y-m-d').".xls");
	header('Expires:0');
	header('Pragma:public');
	echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$strTable.'</html>';
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 根据id获取地区名字
 * @param $regionId id
 */
function getRegionName($regionId){
    $data = M('region')->where(array('id'=>$regionId))->field('name')->find();
    return $data['name'];
}

function getMenuList($act_list){
	//根据角色权限过滤菜单
	$menu_list = getAllMenu();
	if($act_list != 'all'){
		$right = M('system_menu')->where("id in ($act_list)")->cache(true)->getField('right',true);
		foreach ($right as $val){
			$role_right .= $val.',';
		}
		$role_right = explode(',', $role_right);		
		foreach($menu_list as $k=>$mrr){
			foreach ($mrr['sub_menu'] as $j=>$v){
				if(!in_array($v['control'].'Controller@'.$v['act'], $role_right)){
					unset($menu_list[$k]['sub_menu'][$j]);//过滤菜单
				}
			}
		}
	}
	return $menu_list;
}

function getAllMenu(){
	return	array(
			'system' => array('name'=>'系统设置','icon'=>'fa-cog','sub_menu'=>array(
					array('name'=>'网站设置','act'=>'index','control'=>'System'),
                    array('name'=>'数据初始化','act'=>'initialize','control'=>'Tools'),
					array('name'=>'权限资源','act'=>'right_list','control'=>'System'),
			)),
			'access' => array('name' => '权限管理', 'icon'=>'fa-gears', 'sub_menu' => array(
					array('name' => '管理员列表', 'act'=>'index', 'control'=>'Admin'),
					array('name' => '角色管理', 'act'=>'role', 'control'=>'Admin'),
//					array('name' => '供应商管理', 'act'=>'supplier', 'control'=>'Admin'),
					array('name' => '管理员日志', 'act'=>'log', 'control'=>'Admin'),
			)),
			'member' => array('name'=>'会员管理','icon'=>'fa-user','sub_menu'=>array(
                    array('name'=>'会员列表','act'=>'index','control'=>'User'),
                    array('name'=>'会员等级','act'=>'levelList','control'=>'User'),
                    array('name'=>'推荐关系','act'=>'relation','control'=>'User'),
                    array('name'=>'开通会员','act'=>'level_log','control'=>'User'),
                    array('name'=>'余额充值','act'=>'recharge','control'=>'User'),
                    array('name' => '余额提现', 'act'=>'withdrawals', 'control'=>'User'),
//                    array('name' => '积分提现', 'act'=>'tx_log', 'control'=>'User'),
                    array('name' => '签到记录', 'act'=>'sign_log', 'control'=>'User'),
                    array('name'=>'互转记录','act'=>'hz_log','control'=>'User'),
//                    array('name'=>'退股记录','act'=>'loan','control'=>'User'),

			)),
            'Account' => array('name' => '账户管理', 'icon'=>'fa-book', 'sub_menu' => array(
                array('name'=>'余额记录','act'=>'money_log','control'=>'User'),
                array('name' => '积分记录', 'act'=>'jifen_log', 'control'=>'User'),
                array('name' => '活跃度记录', 'act'=>'active_log', 'control'=>'User'),
//                array('name' => '推荐值记录', 'act'=>'recom_log', 'control'=>'User'),
//                array('name' => '贡献值记录', 'act'=>'devote_log', 'control'=>'User'),
            )),
			'goods' => array('name' => '商品管理', 'icon'=>'fa-book', 'sub_menu' => array(
////					array('name' => '商品分类', 'act'=>'categoryList', 'control'=>'Goods'),
					array('name' => '拼团商品', 'act'=>'goodsList', 'control'=>'Goods'),
                    array('name' => '积分商城', 'act'=>'fxList', 'control'=>'Goods'),
			)),
			'promotion' => array('name' => '拼团管理', 'icon'=>'fa-bell', 'sub_menu' => array(
                    array('name' => '拼团等级', 'act'=>'priceList', 'control'=>'Promotion'),
                    array('name' => '拼团数量', 'act'=>'numList', 'control'=>'Promotion'),
                    array('name' => '房间列表', 'act'=>'home', 'control'=>'Promotion'),
                    array('name' => '参团列表', 'act'=>'homeOrder', 'control'=>'Promotion'),
			)),
            'count' => array('name' => '拼图统计', 'icon'=>'fa-signal', 'sub_menu' => array(
                array('name' => '商品统计', 'act'=>'saleTop', 'control'=>'Report'),
                array('name' => '会员统计', 'act'=>'userTop', 'control'=>'Report'),
            )),
            'order' => array('name' => '订单管理', 'icon'=>'fa-money', 'sub_menu' => array(
                array('name' => '订单列表', 'act'=>'index', 'control'=>'Order'),
                array('name' => '发货列表', 'act'=>'delivery_list', 'control'=>'Order'),
            )),
            'award' => array('name' => '抽奖管理', 'icon'=>'fa-plug', 'sub_menu' => array(
                    array('name'=>'奖品列表','act'=>'chouList','control'=>'Article'),
                    array('name'=>'中奖记录','act'=>'linkList','control'=>'Article'),

            )),
			'Ad' => array('name' => '轮播管理', 'icon'=>'fa-flag', 'sub_menu' => array(
					array('name' => '轮播列表', 'act'=>'adList', 'control'=>'Ad'),
//					array('name' => '轮播分类', 'act'=>'positionList', 'control'=>'Ad'),
			)),
			'content' => array('name' => '文章管理', 'icon'=>'fa-comments', 'sub_menu' => array(
					array('name' => '文章列表', 'act'=>'articleList', 'control'=>'Article'),
			)),
			'tools' => array('name' => '插件工具', 'icon'=>'fa-plug', 'sub_menu' => array(
					array('name' => '插件列表', 'act'=>'index', 'control'=>'Plugin'),
			)),
	);
}


function respose($res){
	exit(json_encode($res));
}