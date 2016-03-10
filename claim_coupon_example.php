<?php
require_once 'dada_rsa.php';
require_once 'dada_http.php';

$order_number = 'TP' . date('Ymd') . mt_rand(1000,9999);
example('13316563757',$order_number);

function example($mobile,$order_number) {
	//组装请求明文
	$req_arr = array(
			'company_code' => 'demo',
			'send_time' => time(),
			'coupon_type' => 525,
			'mobile' => $mobile,
			'order_number' => $order_number
	);
	
	$text = "company_code=" . $req_arr['company_code'];
	$text .= "&send_time=" . $req_arr['send_time'];
	$text .= "&coupon_type=" . $req_arr['coupon_type'];
	$text .= "&mobile=" . $req_arr['mobile'];
	$text .= "&order_number=" . $req_arr['order_number'];
	
	//设置明文
	DadaRsa::instance()->set_text($text);	
	//加载嗒嗒公钥
	if (!DadaRsa::instance()->load_public_key("rsa/dada_rsa_public_key.pem")) {
		echo "example,load_public_key fail" . PHP_EOL;
		return false;
	}
	
	//加载企业私钥}
	if (!DadaRsa::instance()->load_private_key("rsa/demo_company_rsa_private_key.pem")) {
		echo "example,load_private_key fail" . PHP_EOL;
		return false;
	}
	
	//生成密文
	$cipher = '';
	if (!DadaRsa::instance()->gen_cipher($cipher)) {
		echo "example,gen_cipher fail" . PHP_EOL;
		return false;
	}
	
	//生成签名
	$sign = '';
	if (!DadaRsa::instance()->gen_sign($sign)) {
		echo "example,gen_cipher fail" . PHP_EOL;
		return false;
	}
	
	//嗒嗒开放平台，测试环境地址
	$host = "dev.open.api.dadabus.com";
	$cipher = urlencode($cipher);
	$sign = urlencode($sign);
	$req_url = "http://" . $host . "/coupon/grant?ciphertext=$cipher" . "&signature=" . $sign;
	
	$rsp =  http_get($req_url);
	if (false === $rsp) {
		echo "example,http_get fail" . PHP_EOL;
		return false;
	}
	
	echo "success,rsp:" . $rsp . PHP_EOL;
	
	return true;
}
?>