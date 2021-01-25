<?php
		if (php_sapi_name() !== 'cli')die("run though cli");

		include('config.php');
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);

		require_once("classes/base64_class.php");
		include("classes/sam.php");
		require_once('classes/addressbook_class_mysql.php');
		$addressbook = new addressbook_service();
		$domains=$addressbook->getDomain("");
		$sam = null;
		foreach( $domains as $value){
				$host=$value['host'];
				$b64=$value['b64'];
				$cache=$addressbook->existOnlineStatus($host);
				if( $cache === false){// if not exist online status in 'cache'
					if( $sam == null) $sam = new SAM(SAMHOST,SAMPORT);
					$online = $sam->check_online("$b64");
					$addressbook->addOnlineStatus($host,$online);
				}else{// if exists in 'cache'
					if( $addressbook->diffRequestAndOnlineStatus($host) ){ // '>5 minutes ago checked'
						if( $sam == null ) $sam = new SAM(SAMHOST,SAMPORT);
						$online = $sam->check_online("$b64");
						$addressbook->UpdateOnlineStatusIfNeed($host,$online);
					}else//get from DB
						$last_online= $cache['last_online'];
					}//end of exist in cache
	}//
?>
