<?php
		if (php_sapi_name() !== 'cli')die("run though cli");

		include('config.php');
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
		$running = exec("ps aux|grep ". basename(__FILE__) ."|grep -v grep|wc -l");
		if($running > 1) {
  		 exit;
		}
		require_once("classes/base64_class.php");
		include("classes/sam.php");
		require_once('classes/addressbook_class_mysql.php');
		$addressbook = new addressbook_service();
		$domains=$addressbook->getDomain("");
		$hosts=array(
		//	"http://stats.i2p/cgi-bin/newhosts.txt",//
			"http://notbob.i2p/hosts.txt",
			"http://reg.i2p/hosts.txt"
		);

		foreach($hosts as $host){
			print("Download $host\n");
			$file = $addressbook->getFileThoughProxy("$host",I2PHTTPPROXY,false);
			$addressbook->addDomainsByText($file);
		}
		print("\r\n\r\nUpdate domains cache\r\n\r\n");
		$sam = null;
		foreach( $domains as $value){
				$host=$value['host'];
				$b64=$value['b64'];
				$cache=$addressbook->existOnlineStatus($host);
				if( $cache === false){// if not exist online status in 'cache'
					print("add to cache!\r\n");
					if( $sam == null) $sam = new SAM(SAMHOST,SAMPORT);
					$online = $sam->check_online("$b64");
					print("ping to resourse ". $sam->getLastPing() );
					$addressbook->addOnlineStatus($host,$online);
				}else{// if exists in 'cache'
					print("update cache!\r\n");
					if( $addressbook->diffRequestAndOnlineStatus($host) ){ // '>5 minutes ago checked'
						if( $sam == null ) $sam = new SAM(SAMHOST,SAMPORT);
						$online = $sam->check_online("$b64");
						print("ping to resourse ". $sam->getLastPing() );
						$addressbook->UpdateOnlineStatusIfNeed($host,$online);
					}else//get from DB
						$last_online= $cache['last_online'];
					}//end of exist in cache
	}//
?>
