<?php
namespace packages\ghafiye;
use \packages\base\json;
class jsonrpc{
	const version = '2.0';
	private $socket;
	function __construct(){
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	}
	public function connect($ip, $port){
		if(socket_connect($this->socket, $ip, $port)){
			return true;
		}else{

		}
	}
	public function close(){
		return socket_close($this->socket);
	}
	public function notification($method,$params = array()){
		$data = array(
			'jsonrpc' => self::version,
			'method' => 'notification',
			'payload' => $params
		);
		return $this->send($data);
	}
	public function request($method, $params = array()){
		$data = array(
			'jsonrpc' => self::version,
			'id' => time(),
			'payload' => $params,
			'type' => 'request',
			'method' => $method,
			"params" => $params
		);
		if($this->send($data)){
			$response = $this->read();
			return isset($response['result']) ? $response['result'] : false;
		}
	}
	public function send($data){
		$in = json\encode($data);
		$in = "$".strlen($in)."\r\n".$in."\r\n";
		return socket_write($this->socket, $in, strlen($in)) == strlen($in);
	}
	public function read(){
		$header = socket_read($this->socket, 1);
		if($command = substr($header, 0, 1)){
			if($command == "+"){
				return json\decode($this->readLine(), true);
			}elseif($command == "$"){
				$fline = $this->readLine();
				$len = intval(trim($fline));
				$data = "";
				while(strlen($data) < $len){
					$data .= socket_read($this->socket, $len - strlen($data));
				}
				return json\decode($data, true);
			}
		}
		return null;
	}
	public function readLine(){
		$data = "";
		while(substr($data, -2) != "\r\n"){
			$chr = socket_read($this->socket, 1);
			if($chr === false){
				break;
			}
			$data .= $chr;
		}
		return substr($data,0, strlen($data) - 2);
	}
}
