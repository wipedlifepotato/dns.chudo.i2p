<?php
	function generateRandomString($len = 10, $alphabet="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") {
            srand(time());
	    $ret="";
	    for($i = $len; $i > 0;$i--){
		$ret .= $alphabet[(rand() % strlen($alphabet))];
	    }
	    return $ret;
	}
	//require_once('base2n.php');
	function isset_all($arr, ...$values){
		foreach($values as $v){
			if( !isset($arr[$v]) ) return false;
		}
		return true;
	}
	class addressbook_service{

		protected function escapeString($t,$is_sqlite=false){
			if($is_sqlite) return $this->db->escapeString($t);
			return $this->db->real_escape_string($t);
		}//maybe delete it...
		public function __construct(){
			$this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DB);
			if ($this->db->connect_error) {
				die('MYSQLERROR (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
			}
			$this->createTablesIfNotExists();
		}
  		function __destruct() {
			$this->db->close();
			unset($this->db);
   		}

		protected function createTablesIfNotExists(){
			$this->db->query('CREATE TABLE IF NOT EXISTS domains (host varchar(255), description TEXT, b64 TEXT);');
			$this->db->query('CREATE TABLE IF NOT EXISTS tmp_files (host varchar(255), namefile varchar(255));');

		}
		
		protected function IsExistsDomainOrB64($domain,$b64){
			$domain = $this->escapeString($domain);
			$b64 = $this->escapeString($b64);
			$res=$this->db->query("SELECT * FROM domains WHERE host='$domain' or b64='$b64'; ");
			$row = $res->fetch_array();
			if ($row) 
			    return true;
			return false;
		}
		public function getDomain($domain){
			
			$domain = $this->escapeString($domain);
			$results = $this->db->query("SELECT * FROM domains where host LIKE '%$domain%'");
			$ret=array();
			while ($row = $results->fetch_array()) {
			    $ret[] = $row;
			}
			return $ret;
		}
		public function isSubDomain($domain){
			if( sizeof( explode('.',$domain) ) > 2 ) return true;
			return false;
		}
		protected function is_valid_domain_name($domain_name) { 
		   // https://www.tutorialspoint.com/how-to-validate-domain-name-in-php
		   $tmp= (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   );
		   $partsdomains=explode('.', $domain_name);
		   if( sizeof($partsdomains) > 5 ) return false;
		   if( array_reverse((explode('.', $domain_name)))[0] == "i2p" && $tmp ) return true;	
		}

		public function addDomain($domain, $b64, $description, $mxdesc=64){
			if ( !$this->checkIsB64($b64) ) throw new Exception("NOT CORRECT BASE64");
			if ( !$this->is_valid_domain_name($domain) ) throw new Exception("NOT CORRECT DOMAIN NAME ".$domain);
			if( $this->IsExistsDomainOrB64($domain,$b64) ) throw new Exception("DOMAIN OR B64 ADDED ALREADY");
			if ( strlen($description) > $mxdesc ) throw new Exception("MAX DESCRIPTION SIZE IS $mxdesc");
			$domain = $this->escapeString($domain);
			$b64 = $this->escapeString($b64);
			$description = $this->escapeString($description);
			$sql="INSERT INTO domains(host,description,b64) values('$domain','$description','$b64')";
			//echo $sql;
			$res = 
				$this->db->query(
					$sql
				);
			$this->addToNewHostsFile($domain,$b64);
			$url="http://$domain/?i2paddresshelper=$b64";
			$this->getFileThoughProxy($url);
			return $res;
		}
		/*public function b64to32($str){ // not works
			$tmp = base64_decode($str, false);
			$tmp = hash ( 'sha256' , $tmp , true ); 
			$base32 = new Base2n(5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', FALSE, FALSE, FALSE);
			$tmp = $base32->encode($tmp);
			return $tmp;
		}*/
		protected function checkIsB64($str){
			if (base64_decode($str, false)) return true;
			return false;
		}
		public function addFromHostFile($hosts){
			$f = fopen($hosts,"r");
			$full="";
			while($text = fread($f,1024)){
				$full.= $text;
			}
			fclose($f);
			$lines= explode("\n",$full);
			//echo $lines[0];
			foreach ($lines as $line){
				$tmp = explode("=", $line,2);
				//echo $tmp[0]." ... ".$tmp[1];
				try{
					$this->addDomain($tmp[0],$tmp[1],"");
				}catch(Exception $e){print ("Warning: ".$e."</br>\n");}
			}
		}
		protected function addToNewHostsFile($domain,$b64,$fn='new-hosts.txt'){
			$data = $domain.'='.$b64.PHP_EOL;
			$f = fopen($fn, 'a');
			fwrite($f, $data);
			fclose($f);
		}
		public function deleteDomain($domain){
			$domain = $this->escapeString($domain);
			$this->db->query("DELETE FROM domains where host='$domain';");
		}
		public function clearDB(){
			$this->db->query('DELETE FROM domains;');
			$this->deleteTmpFilesForDomains();
		}

		//subdomain support
		protected function getFileThoughProxy($url,$proxy='tcp://127.0.0.1:4444'){
			$aContext = array(
			    'http' => array(
				'proxy'           => $proxy,
				'request_fulluri' => true,
				'header'          => "Proxy-Authorization: Basic",
			    ),
			);
			$cxContext = stream_context_create($aContext);
			return @file_get_contents("$url", False, $cxContext);
		}//	
		protected function genTmpFileForDomain($host){
			$host = $this->escapeString($host);
			$tmpString=generateRandomString();
			$this->db->query("INSERT INTO tmp_files(host,namefile) VALUES('$host','$tmpString');");
			return $tmpString;
		}
		protected function searchTmpFileForDomain($host){
			$host = $this->escapeString($host);
			$res=$this->db->query("SELECT * FROM tmp_files WHERE host='$host'");
			return $res->fetch_array();
		}
		public function deleteTmpFilesForDomains(){
			$this->db->query('DELETE FROM tmp_files;');
		}
		protected function deleteTmpFilesForDomain($host){
			$host = $this->escapeString($host);
			$this->db->query("DELETE FROM tmp_files where host='$host';");
		}	
		public function regSubDomain($domain,$b64,$description){
			$d=array_reverse( explode('.', $domain) );
			$host=$d[1].'.'.$d[0];
			if( $this->IsExistsDomainOrB64($host,"") === false ) throw new Exception("$host not exists for subdomain");
			$r=$this->searchTmpFileForDomain($host);
			if( $r ){ // file exists already
				$namefile=$r['namefile'];
				$url="http://$host/$namefile";
				$file=$this->getFileThoughProxy("http://$host/$namefile");
				if($file === false){
					throw new Exception("You will create file on $url");
				}else{
					$this->addDomain($domain,$b64,$description);
					return true;
				}//reg subdomain domain
			}else{
				$tmpString = $this->genTmpFileForDomain($host);
				return $host.'/'.$tmpString;
			}//file not exists already
		}//mainFunc
	};
?>
