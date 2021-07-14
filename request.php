<?php include('templates/header.php');?>
<?php
$reserved_hosts = array("b32.i2p", "proxy.i2p",
"router.i2p","console.i2p");

function request(){
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);

		require_once("classes/base64_class.php");
		include("classes/sam.php");
		require_once('classes/addressbook_class_mysql.php');


		$test = new addressbook_service();
		//$test->addFromHostFile('hosts.txt');
		//$test->clearDB();

		//search domain
		if ( isset($_GET['d']) ){
			echo "<div id='domains'>";
			$alldomains=true;
			if( isset($_GET['all_domains']) && $_GET['all_domains'] == 'y') $alldomains=false;
			//print($alldomains?"true":'false');
			$domains= $alldomains ? $test->getDomain($_GET['d'],true) : $test->getDomain($_GET['d']);
			$co=false;
			if ( isset($_GET['check_online']) && $_GET['check_online'] == 'y') $co=true;
			$sam=null;
			foreach( $domains as $value){
				$host=$value['host'];
				$b64=$value['b64'];
				$desc=$value['description'];
				$b32 = (new b32_b64())->b32from64($b64) . ".b32.i2p";
				$last_online="";//last_online
				if($co){
					$cache=$test->existOnlineStatus($host);
					if( $cache === false){// if not exist online status in 'cache'
						if( $sam == null) $sam = new SAM(SAMHOST,SAMPORT);
						$online = $sam->check_online("$b64");
						$test->addOnlineStatus($host,$online);
						if($online)
							$last_online="now: ".date("F j, Y, g:i a");
						else $last_online="NaN";
					}else{// if exists in 'cache'
						if( $test->diffRequestAndOnlineStatus($host) ){ // '>5 minutes ago checked'
							if( $sam == null ) $sam = new SAM(SAMHOST,SAMPORT);
							//print("<!-- >5minutes -->");
							$online = $sam->check_online("$b64");
							$test->UpdateOnlineStatusIfNeed($host,$online);
							if($online)
								$last_online="now: ".date("F j, Y, g:i a");
							else 	$last_online= $cache['last_online'];
						}else//get from DB
							$last_online= $cache['last_online'];
					}//end of exist in cache

				}//end if check online
				
				if( strlen($desc) ) $desc = "-".$desc;
				$last_online = $co ? "(Last seen: $last_online)": "";
				echo "<a href='http://$host/?i2paddresshelper=$b64'>$host $last_online</a> $desc  <br/>($b32)<hr/>";
			}//foreach
			echo "<a href=index.php>back</a>";
			echo "</div>";
			return true;
		}

		//add domain
		if( isset_all($_GET, 'host','b64','desc') ){
			global $reserved_hosts;
			$host=$_GET['host'];
			for ( $cn = 0; isset($host[$cn]); $cn++){
				if($cn >=253) die("Is long name for domain");
			}
			$b64=$_GET['b64'];
			$desc=$_GET['desc'];
			foreach( $reserved_hosts as $rhost ){
				if( strstr($host,$rhost) != false ) die("Is reserverd ($rhost) host. you can not to register it");
			}
			echo "<center><a href=index.php>back</a><br/>";
			if(!$test->checkIsB64($b64))die("uncorrect b64");
			$sam = new SAM(SAMHOST,SAMPORT);
			$online = $sam->check_online("$b64");
			if(!$online)die("your service is down!");
		
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
					return true;
				}
			}
			echo "Added!<hr/></center>";
		}
}

request();
?>

<?php include('templates/footer.php');?>
