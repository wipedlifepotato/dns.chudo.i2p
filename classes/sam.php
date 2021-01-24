<?php
class SAM{
		protected function swrite($s,$m){
			if($this->debug) print("Write:".$m);
			return socket_write($s, $m, strlen($m));
		}
		protected function sread($s,$bs=1024){
			$res="";
			$res = socket_read($s, $bs,PHP_NORMAL_READ);
			/*while(  ){
				print("Read:".$res);
				$res .= $out;
			}*/
			if($this->debug) print("Read:".$res);
			return $res;
		}
		protected function write_hello($socket){
			$r="HELLO VERSION\r\n";
			$this->swrite($socket, $r);
			$msg=$this->sread($socket);
			if( strstr($msg, "HELLO REPLY RESULT=OK") === FALSE ){
				socket_close($socket);
				throw new Exception("can't open SAM: ".$msg);
			}
			return true;
		}
		protected function conn_to_sam($socket){
			$r=socket_connect($socket, $this->host,$this->port);
			if($r == false) 
				throw new Exception(""
				.socket_strerror(socket_last_error($socket)));
			return $r;
		}
		public function __construct($host='127.0.0.1',$port=7656,$idname='checker',$debug=false){
			$this->debug=$debug;
			$this->idname=$idname;
			$this->host=$host;
			$this->port=$port;
			$socket=socket_create(AF_INET, SOCK_STREAM,SOL_TCP);
			if($socket ==false){
			throw new
			Exception("socket_create() failed: "
			.socket_strerror(socket_last_error()));
			}
			$this->conn_to_sam($socket);
			$this->write_hello($socket);
			$this->main_socket=$socket;
			$this->session_init();
		}
	
		public function __destruct(){
			socket_close($this->main_socket);
		}
		protected function session_init($style='STREAM',$id='SAMCHECK'){
			//print("init session\r\n");
			$this->session_socket
				=socket_create(AF_INET, SOCK_STREAM,SOL_TCP);
			$this->conn_to_sam($this->session_socket);
			$this->write_hello($this->session_socket);
			$this->swrite($this->session_socket,"SESSION CREATE STYLE=$style DESTINATION=TRANSIENT ID=".$this->idname."\r\n");
			$reply=$this->sread($this->session_socket);
			if( strstr($reply, "RESULT=OK") === false){
				if( strstr($reply, "RESULT=DUPLICATED_ID") !== false) return $reply;
				throw 
				new Exception("Err session create:".$reply);
			}
			return $reply;
		}
		public function stream_connect($destination){
			$tmp=socket_create(AF_INET, SOCK_STREAM,SOL_TCP);
			$this->conn_to_sam($tmp);
			$this->write_hello($tmp);
			$this->swrite($tmp,
				"STREAM CONNECT ID=".$this->idname." DESTINATION=$destination\r\n");
			$reply=$this->sread($tmp);
			socket_close($tmp);
			if( strstr($reply, "RESULT=OK") === FALSE)
				throw new Exception( $reply );
			return $reply;
		}
		public function check_online($destination){
			try{
				$this->stream_connect($destination);
				return true;
			}catch(Exception $e){
				return false;
			}
		}
	};
?>
