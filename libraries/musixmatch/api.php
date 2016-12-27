<?php
namespace packages\ghafiye\musixmatch;
use \packages\base\db\dbObject;
use \packages\base\json;
class api extends dbObject{
	private $apiGateway = 'https://api.musixmatch.com/ws/1.1/';
	private $apicGateway = 'https://apic-community.musixmatch.com/ws/1.1/';
	private $apicTokenFile = __DIR__.'/../../storage/private/musixmatch/apic/token.key';
	private $signatureSecret = 'ca4bd3230335b5449b00ee252d5a4fda240e59f5';
	private $curl_Options = array(
		CURLOPT_HEADER => false,
		CURLINFO_HEADER_OUT => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_AUTOREFERER => true,
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_PROXYAUTH => CURLAUTH_BASIC,
		CURLOPT_PROXYUSERPWD => 'amir:123',
		CURLOPT_PROXYPORT => 808,
		CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
		CURLOPT_PROXY => 's35.jeyserver.com',
		CURLOPT_USERAGENT => 'Dalvik/1.6.0 (Linux; U; Android 4.1.1; Android SDK built for x86 Build/JRO03H)',
		CURLOPT_COOKIEJAR =>  __DIR__.'/../../storage/private/musixmatch/cookie',
       	CURLOPT_COOKIEFILE => __DIR__.'/../../storage/private/musixmatch/cookie'
	);
	protected $usingAPIC = false;
	protected $usingCrawler = false;
	protected $usingCache = true;
	public function useAPIC(){
		$this->usingAPIC = true;
		return $this;
	}
	public function disableAPIC(){
		$this->usingAPIC = false;
		return $this;
	}
	public function useCrawler(){
		$this->usingCrawler = true;
		return $this;
	}
	public function disableCrawler(){
		$this->usingCrawler = false;
		return $this;
	}
	public function useCache(){
		$this->usingCache = true;
		return $this;
	}
	public function disableCache(){
		$this->usingCache = false;
		return $this;
	}
	public function buildParameters($rules, $parameters){
		$return = array();
		foreach($rules as $key => $rule){

			if(isset($rule['require']) and $rule['require']){
				if(!isset($parameters[$key])){
					throw new InputRequired($key);
				}
			}
			if(isset($parameters[$key])){
				$return[isset($rule['remote_key']) ? $rule['remote_key'] : $key] = $parameters[$key];
			}
		}
		return $return;
	}
	public function sendRequest($path, $parameters = array()){
		$options = $this->curl_Options;
		if($this->usingAPIC){
			$parameters = array_merge($parameters,array(
				'app_id' => 'community-app-v1.0',
				'usertoken' => $this->getAPICToken(),
				'format' => 'json'
			));
			$uri = $this->apicGateway.$path.'?'.http_build_query($parameters);
			$parameters = array_merge($parameters, $this->sign($uri));
			$uri = $this->apicGateway.$path."?".http_build_query($parameters);

			$headers = isset($options[CURLOPT_HTTPHEADER]) ? $options[CURLOPT_HTTPHEADER] : array();
			$headers[] = 'x-mxm-endpoint: foreground';
			$options[CURLOPT_HTTPHEADER] = $headers;
		}else{

			$parameters['format'] = 'json';
			$parameters['apikey'] = 'c0f1452a922e366033db671828693d9c';
			$uri = $this->apiGateway.$path.'?'.http_build_query($parameters);
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		curl_close($ch);
		if($result = json\decode($result)){
			if(isset($result['message']['body'])){
				return $result['message']['body'];
			}elseif(isset($result['message']['header']['status_code'])){
				return $result['message']['header']['status_code'];
			}
		}
		return false;
	}
	public function sendWebRequest($url, $parameters = array()){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($parameters));
		curl_setopt_array($ch, $this->curl_Options);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	public function getAPICToken(){
		//return '16122302027fa7f6ed4207d5703ad70e07a611f66279666f4d9fa5';
		if(is_file($this->apicTokenFile)){
			if($file = file_get_contents($this->apicTokenFile)){
				if($data = json\decode($file)){
					if(is_array($data) and isset($data['user_token'])){
						return $data['user_token'];
					}
				}
			}
		}
		if($data = $this->generateAPICToken()){
			if(is_array($data) and isset($data['user_token'])){
				file_put_contents($this->apicTokenFile, json\encode($data));
				return $data['user_token'];
			}
		}
		return null;
	}
	public function generateAPICToken(){
		$parameters = array(
			'app_id' => 'community-app-v1.0',
			//'build_number' => '2016121731',
			//'guid' => '3664e26cffb93038',
			'lang' => 'en_US',
			//'model' => 'manufacturer/unknown+brand/generic_x86+model/Android+SDK+built+for+x86',
			//'root' => 1,
			//'sideloaded' => 1,
			'timestamp' => date('c'),
			'referral' => 'unknown',
			'format' => 'json'
		);
		$url = $this->apicGateway."token.get?".http_build_query($parameters);
		$parameters = array_merge($parameters, $this->sign($url));
		$url = $this->apicGateway."token.get?".http_build_query($parameters);
		echo $url."\n";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt_array($ch, $this->curl_Options);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('x-mxm-endpoint: foreground'));
		$result = curl_exec($ch);
		curl_close($ch);
		print_r($result);
		if($result = json\decode($result)){
			if(isset($result['message']['body'])){
				return $result['message']['body'];
			}elseif(isset($result['message']['header']['status_code'])){
				return $result['message']['header']['status_code'];
			}
		}
		return false;
	}
	public function eventPost($event){
		$this->sendRequest("event.post", array(
			'api_method' => $event,
			'cluser' => 'e6',
		));
	}
	private function sign($url){
		$year = gmdate('Y');
		$month = gmdate('m');
		$day = gmdate('d');
		$hash = base64_encode($this->hmac_sha1($this->signatureSecret, $url.$year.$month.$day, true));
		return array(
			'signature' => $hash,
			'signature_protocol' => 'sha1'
		);
	}
	function hmac_sha1($key, $data, $raw = false){
	    if (strlen($key) > 64) {
	        $key = str_pad(sha1($key, true), 64, chr(0));
	    }
	    if (strlen($key) < 64) {
	        $key = str_pad($key, 64, chr(0));
	    }

	    // Outter and Inner pad
	    $opad = str_repeat(chr(0x5C), 64);
	    $ipad = str_repeat(chr(0x36), 64);

	    // Xor key with opad & ipad
	    for ($i = 0; $i < strlen($key); $i++) {
	        $opad[$i] = $opad[$i] ^ $key[$i];
	        $ipad[$i] = $ipad[$i] ^ $key[$i];
	    }

	    return sha1($opad.sha1($ipad.$data, true), $raw);
	}

}
class InputRequired extends \Exception{
	private $input;
	public function __construct($input){
		$this->input = $input;
	}
	public function getInput(){
		return $this->input;
	}
}
class shareURLException extends \Exception{

}
