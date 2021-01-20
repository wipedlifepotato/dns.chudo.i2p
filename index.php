<center>
<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
	include('config.php');
	if(!USEMYSQL)
		require_once('addressbook_class.php');
	else
		require_once('addressbook_class_mysql.php');


	$test = new addressbook_service();
	//$test->addFromHostFile('hosts.txt');
	//$test->clearDB();

	//search domain
	if ( isset($_GET['d']) ){
		$domains=$test->getDomain($_GET['d']);
		foreach( $domains as $value){
			$host=$value['host'];
			$b64=$value['b64'];
			$desc=$value['description'];
			if( !strlen($desc) ) $desc = "no info";
			echo "<a href='http://$host/?i2paddresshelper=$b64'>$host</a> - $desc <hr/>";
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

?>
<form action=index.php method=GET> <!-- search -->
	jump: <input type=textarea name=d placeholder="domain for jump"/>
	<input type=submit value='search' />
</form><br/><hr/>
Add Domain(http://127.0.0.1:7070/?page=i2p_tunnels):
<form action=index.php method=GET> <!-- search -->
	host(example.i2p): <input type=textarea name=host placeholder="domain"/><br/>
	b64: <input type=textarea name=b64 placeholder="base64"/></br>
	description: <input type=textarea name=desc placeholder="description"/></br>
	<input type=submit value="add domain" />
</form>
</center>
