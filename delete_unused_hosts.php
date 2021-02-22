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
		require_once('classes/addressbook_class_mysql.php');
		$addressbook = new addressbook_service();
		$addressbook->deleteUnusedDomains();
?>
