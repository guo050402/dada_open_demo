<?php 
class DadaRsa {
	private static $__instance = null;          
	//明文请求
	private $__text;    

	//MD5哈希值
	private $__md5;
	
	//企业私钥
	private $__company_private;    
	
	//嗒嗒公钥
	private $__dada_public;  
	
	public static function instance() {
		if (null == self::$__instance) {
			self::$__instance = new DadaRsa();
		}
		return self::$__instance;
	}   
	
	public function __construct() {   
	
	}  
	
	public function __destruct() {   
	
	}  
	
	public function load_private_key($key_path) {   	
		if (!empty($key_path)) {
			$this->__company_private = file_get_contents($key_path);
			if (false === $this->__company_private) {
				echo "load_private_key,file_get_contents $key_path fail" . PHP_EOL;
				return false;
			}
			return true;
		}
		else {
			echo "load_private_key,invalid key_path is empty" . PHP_EOL;
			return false;
		}
	} 
	
	public function load_public_key($key_path) {
		if (!empty($key_path)) {
			$this->__dada_public = file_get_contents($key_path);
			if (false === $this->__dada_public) {
				echo "load_public_key,file_get_contents $key_path fail" .PHP_EOL;
				return false;
			}
			return true;
		}
		else {
			echo "load_public_key,invalid key_path is empty" .PHP_EOL;
			return false;
		}
	}  
	
	public function gen_sign(&$sign) {   
		//参数检查
		if (empty($this->__text) || empty($this->__company_private)) {
			echo "gen_sign,invalid parameters" . PHP_EOL;
			return false;
		}

		//生成明文的MD5哈希值
		$this->__md5 = md5($this->__text);
		
		//对MD5哈希值，用企业私钥加密
		if (false === $this->private_encrypt($sign)) {
			echo "gen_sign,private_encrypt fail" . PHP_EOL; 
			return false;
		}
		
		//base64编码
		$sign = base64_encode($sign);
		
		return true;
	}  
											  
	public function gen_cipher(&$cipher) {     
		//参数检查
		if (empty($this->__text) || empty($this->__dada_public)) {
			echo "gen_sign,invalid parameters" . PHP_EOL;
			return false;
		}
		
		//使用嗒嗒公钥对明文加密
		if (false === $this->public_encrypt($cipher)) {
			echo "gen_cipher,public_encrypt fail" . PHP_EOL;
			return false;
		}
		
		$cipher = base64_encode($cipher);
		
		return true;
	}
	
	public function set_text($text) {
		$this->__text = $text;
	}  
	
	/////////////////////私有函数///////////////////   
	
	private function public_encrypt(&$cipher) {
		if (empty($this->__dada_public)) {
			echo "public_encrypt,public is empty" .PHP_EOL;
			return false;
		}
		$resource_id = openssl_pkey_get_public($this->__dada_public);
		if (false === $resource_id) {
			echo "public_encrypt,openssl_pkey_get_public fail" . PHP_EOL;
			return false;
		}

		if (false === openssl_public_encrypt($this->__text,$cipher,$resource_id)) {
			echo "public_encrypt,openssl_public_encrypt fail" . PHP_EOL;
			return false;
		}

		return true;
	}

	private function private_encrypt(&$cipher) {  
		if (empty($this->__company_private)) {
			echo "private_encrypt,private is empty" .PHP_EOL;
			return false;
		}
		$resource_id = openssl_pkey_get_private($this->__company_private);
		if (false === $resource_id) {
			echo "private_encrypt,openssl_pkey_get_private fail" . PHP_EOL;
			return false;
		}
		
		if (empty($this->__md5)) {
			echo "private_encrypt md5 is empty" . PHP_EOL;
			return false;
		}

		if (false === openssl_private_encrypt($this->__md5,$cipher,$resource_id)) {
			echo "private_encrypt,openssl_private_encrypt fail" . PHP_EOL;
			return false;
		}
		
		return true;
	}
	
}
?>
