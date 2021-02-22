<?php
		if (php_sapi_name() !== 'cli')die("run though cli");

		include('config.php');
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
		$running = exec("ps aux|grep ". basename(__FILE__) ."|grep -v grep|wc -l");
		if($running > 2) {
  		 exit;
		}
		require_once("classes/base64_class.php");
		include("classes/sam.php");
		require_once('classes/addressbook_class_mysql.php');
		$addressbook = new addressbook_service();
		$domains=$addressbook->getDomains(0,$addressbook->getCountDomains(),1);
		foreach($domains as $domain){
			$addressbook->addToNewHostsFile($domain['domain'],$domain['b64'],'alive-hosts-now.txt');
		}

		$domains=$addressbook->getDomain("",true);
		foreach($domains as $domain){
			$addressbook->addToNewHostsFile($domain['domain'],$domain['b64'],'alive-hosts.txt');
		}
		
		
?>
