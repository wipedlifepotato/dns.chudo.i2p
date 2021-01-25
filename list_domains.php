<?php include('templates/header.php');?>

<?php
function request(){
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
		require_once("classes/base64_class.php");
		include("classes/sam.php");
		require_once('classes/addressbook_class_mysql.php');
		$addressbook = new addressbook_service();
		$offset=0;
		if( isset($_GET['o']) ){
			$offset = intval($_GET['o']);
		}
		$domains=$addressbook->getDomains($offset, 5);
		echo "<div id='domains'>";
		foreach( $domains as $value){
		 $host=$value['host'];
		 $b64=$value['b64'];
		 $desc=$value['description'];
		 $cache_online=$addressbook->existOnlineStatus($host);
		 $last_online=$cache_online['last_online'];
		 $b32 = (new b32_b64())->b32from64($b64) . ".b32.i2p";
		 if( !strlen($desc) ) $desc = "no info";
		 echo "<a href='http://$host/?i2paddresshelper=$b64'>$host (Last seen: $last_online)</a> - $desc  <br/>($b32)<hr/>";
		}//foreach
		$np=$offset+5;
		$bp=$offset-5;
		if($offset) print( "<a href='?o=$bp'>&lt;</a>" );
		echo "<a href='?o=$np'>&gt;</a><hr/><a href=index.php>main page</a>";
		echo "</div>";
		return true;
	}
request();
?>

<?php include('templates/footer.php');?>
