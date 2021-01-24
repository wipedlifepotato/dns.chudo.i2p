<?php
function request(){
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);

		require_once("base64_class.php");
		include("sam.php");
		require_once('addressbook_class_mysql.php');


		$test = new addressbook_service();
		//$test->addFromHostFile('hosts.txt');
		//$test->clearDB();

		//search domain
		if ( isset($_GET['d']) ){
			$domains=$test->getDomain($_GET['d']);
			$co=false;
			if ( isset($_GET['check_online']) && $_GET['check_online'] == 'y') $co=true;
			$sam=null;
			foreach( $domains as $value){
				$host=$value['host'];
				$b64=$value['b64'];
				$desc=$value['description'];
				$b32 = (new b32_b64())->b32from64($b64) . ".b32.i2p";
				$online="";
				if($co){
					$cache=$test->existOnlineStatus($host);
					if( $cache === false){// if not exist online status in 'cache'
						if( $sam == null) $sam = new SAM();
						$online = $sam->check_online("$b32");
						$test->addOnlineStatus($host,$online);
					}else{// if exists in 'cache'
						if( $test->diffRequestAndOnlineStatus($host) ){ // '>5 minutes ago checked'
							if( $sam == null) $sam = new SAM();
							print("<!-- >5minutes -->");
							$online = $sam->check_online("$b32");
							$test->UpdateOnlineStatusIfNeed($host,$online);
						}else//get from DB
							$online= $cache['status']>0?true:false;
					}//end of exist in cache
					$online = $online? "(*Is up*)" : "(*Is down*)";
				}//end if check online
				
				if( !strlen($desc) ) $desc = "no info";
				echo "<a href='http://$host/?i2paddresshelper=$b64'>$host $online</a> - $desc  <br/>($b32)<hr/>";
			}//foreach
			echo "<a href=?>back</a>";
			exit;
		}

		//add domain
		if( isset_all($_GET, 'host','b64','desc') ){
			echo "<a href=?>back</a><br/>";
			$host=$_GET['host'];
			$b64=$_GET['b64'];
			$desc=$_GET['desc'];
			if( $test->isSubDomain($host) ){
				try{
					$res=$test->regSubDomain($host,$b64,$desc,I2PHTTPPROXY);
					if($res !== TRUE){
						die("Create file on url->".$res);
					}
				}catch(Exception $e){
					die($e->getMessage());
				}
				//die("is subdomain of exists domain. support must be added later");
			}else{
				try{
					$test->addDomain($host,$b64,$desc);
				}catch (Exception $e){
					echo "".$e->getMessage();
					exit;
				}
			}
			echo "Added!<hr/>";
		}
}
?>
