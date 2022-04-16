<?php
/**
 * 微信支付帮助库
 * ====================================================
 * 【请求型接口】--Wxpay_client_
 * 		统一支付接口类--UnifiedOrder
 * =====================================================
 * 【常用工具】：
 * 		genRandomString()，产生随机字符串，不长于32位
 * 		toUrlParams(),格式化参数，签名过程需要用到
 * 		getSign(),生成签名
 * 		array_to_xml(),array转xml
 * 		xml_to_array(),xml转 array
 * 		postXmlCurl(),以post方式提交xml到对应的接口url
 * 		postXmlSSLCurl(),使用证书，以post方式提交xml到对应的接口url
 */
class wechatAppPay {
	//接口API URL前缀
	const API_URL_PREFIX = 'https://api.mch.weixin.qq.com';
	//下单地址URL
	const UNIFIEDORDER_URL = "/pay/unifiedorder";
	//查询订单URL
	const ORDERQUERY_URL = "/pay/orderquery";
	//关闭订单URL
	const CLOSEORDER_URL = "/pay/closeorder";
	//公众账号ID
	private $appid;
	//商户号
	private $mch_id;
	//随机字符串
	private $nonce_str;
	//商品描述
	private $body;
	//商户订单号
	private $out_trade_no;
	//支付总金额
	private $total_fee;
	//终端IP
	private $spbill_create_ip;
	//支付结果回调通知地址
	private $notify_url;
	//交易类型
	private $trade_type;
	//支付密钥
	private $key;
	//证书路径
	private $SSLCERT_PATH;
	private $SSLKEY_PATH;
	//所有参数
	private $params = array();
	/**
	 * H5支付  四个参数就可以了
	 *	APPID
	 *	MCHID
	 *	NOTIFY_URL
	 *	KEY
	 */
	//这个类的构造函数
	public function __construct($appid, $mch_id, $notify_url, $key) {
		$this->appid = $appid;
		$this->mch_id = $mch_id;
		$this->notify_url = $notify_url;
		$this->key = $key;
	}

	/**
	 * 下单方法
	 * 	  微信H5
	 *     需要传入的参数
	 *		$params = array(
	 *		'body' => '充值',
	 *		'out_trade_no' => $order,
	 *		'total_fee' => intval($money * 100),
	 *		'trade_type' => 'MWEB',
	 *		'scene_info' => '{"h5_info": {"type":"Wap","wap_url":' . '"' . $host . '"' . ',"wap_name": "小肥羊充值"}}',
	 *		);
	 *
	 * @param   $params 下单参数
	 */
	public function unifiedOrder($params) {
		//这里数调用这个方法传入的参数
		$this->body = $params['body'];
		$this->out_trade_no = $params['out_trade_no'];
		$this->total_fee = $params['total_fee'];
		$this->trade_type = $params['trade_type'];
		$this->scene_info = $params['scene_info'];
		//类内部产生的参数
		$this->nonce_str = $this->genRandomString();
		$this->spbill_create_ip = $_SERVER['REMOTE_ADDR'];
		//拼接接口需要的参数
		$this->params['appid'] = $this->appid;
		$this->params['mch_id'] = $this->mch_id;
		$this->params['nonce_str'] = $this->nonce_str;
		$this->params['body'] = $this->body;
		$this->params['out_trade_no'] = $this->out_trade_no;
		$this->params['total_fee'] = $this->total_fee;
		$this->params['spbill_create_ip'] = $this->spbill_create_ip;
		$this->params['notify_url'] = $this->notify_url;
		$this->params['trade_type'] = $this->trade_type;
		$this->params['scene_info'] = $this->scene_info;
		//获取签名数据
		$this->params['sign'] = $this->getSign($this->params);
		//格式化数据
		$xml = $this->array_to_xml($this->params);
		//发送数据
		$response = $this->postXmlCurl($xml, self::API_URL_PREFIX . self::UNIFIEDORDER_URL);
		if (!$response) {
			return false;
		}
		$result = $this->xml_to_array($response);
		if (!empty($result['result_code']) && !empty($result['err_code'])) {
			$result['err_msg'] = $this->error_code($result['err_code']);
		}
		return $result;
	}

	/**
	 * 以post方式提交xml到对应的接口url
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param int $second   url执行超时时间，默认30s
	 * @throws WxPayException
	 */
	public function postXmlCurl($xml, $url, $second = 30) {
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $second);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			return false;
		}
	}

	/**
	 * 	作用：使用证书，以post方式提交xml到对应的接口url
	 */
	function postXmlSSLCurl($xml, $url, $second = 30) {
		$ch = curl_init();
		//超时时间
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//设置证书
		//使用证书：cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
		curl_setopt($ch, CURLOPT_SSLCERT, $this->SSLCERT_PATH);
		//默认格式为PEM，可以注释
		curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
		curl_setopt($ch, CURLOPT_SSLKEY, $this->SSLKEY_PATH);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$data = curl_exec($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			return false;
		}
	}
	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	public function genRandomString($length = 32) {
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$chars = str_shuffle($chars);
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	/**
	 * 	作用：生成签名
	 * 		此处应该注意的是在回调的时候是需要字符密钥参与签名的
	 * 	@param   $params 传入数组
	 */
	public function getSign($params) {
		if (!isset($this->key)) {
			throw new Exception('请配置支付密钥');
		}

		//签名步骤一：按字典序排序参数
		ksort($params);
		$String = $this->toUrlParams($params, false);
		//签名步骤二：在string后加入KEY
		$String = $String . "&key=" . $this->key;
		//签名步骤三：MD5加密
		$String = md5($String);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($String);
		return $result;
	}

	/**
	 * 	作用：格式化参数，签名过程需要使用
	 */
	function toUrlParams($paraMap, $urlencode) {
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
			if ($urlencode) {
				$v = urlencode($v);
			}
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) {
			$reqPar = substr($buff, 0, strlen($buff) - 1);
		}
		return $reqPar;
	}

	/**
	 * 输出xml字符
	 * @param   $params     参数名称
	 * return   string      返回组装的xml
	 **/
	public function array_to_xml($params) {
		if (!is_array($params) || count($params) < 1) {
			throw new Exception('数组转化xml传入参数有误');
		}

		$xml = "<xml>";
		foreach ($params as $key => $val) {
			if (is_numeric($val)) {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			} else {
				$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
			}
		}
		$xml .= "</xml>";
		return $xml;
	}

	/**
	 * 将xml转为array
	 * @param string $xml
	 * return array
	 */
	public function xml_to_array($xml) {
		if (!$xml) {
			throw new Exception('xml转化数组时输入数据有误');
		}
		//将XML转为array
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $data;
	}

	/**
	 * 验证sign
	 * @return [array] [此处传入回调回来的xml数据处理成数组的格式]
	 */
	function checkSign($tmpData) {
		if (isset($tmpData['sign'])) {
			$WxSign = $tmpData['sign'];
			unset($tmpData['sign']);
		}
		$localSign = $this->getSign($tmpData); //本地重新验证的签名
		if ($WxSign == $localSign) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * 错误代码
	 * @param  $code       服务器输出的错误代码
	 * return string
	 */
	public function error_code($code) {
		$errList = array(
			'NOAUTH' => '商户未开通此接口权限',
			'NOTENOUGH' => '用户帐号余额不足',
			'ORDERNOTEXIST' => '订单号不存在',
			'ORDERPAID' => '商户订单已支付，无需重复操作',
			'ORDERCLOSED' => '当前订单已关闭，无法支付',
			'SYSTEMERROR' => '系统错误!系统超时',
			'APPID_NOT_EXIST' => '参数中缺少APPID',
			'MCHID_NOT_EXIST' => '参数中缺少MCHID',
			'APPID_MCHID_NOT_MATCH' => 'appid和mch_id不匹配',
			'LACK_PARAMS' => '缺少必要的请求参数',
			'OUT_TRADE_NO_USED' => '同一笔交易不能多次提交',
			'SIGNERROR' => '参数签名结果不正确',
			'XML_FORMAT_ERROR' => 'XML格式错误',
			'REQUIRE_POST_METHOD' => '未使用post传递参数 ',
			'POST_DATA_EMPTY' => 'post数据不能为空',
			'NOT_UTF8' => '未使用指定编码格式',
		);
		if (array_key_exists($code, $errList)) {
			return $errList[$code];
		}
	}
}