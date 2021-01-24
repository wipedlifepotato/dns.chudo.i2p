<?php include('config.php'); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?php echo BRANDNAME; ?></title>
		<meta charset=utf-8>
		<link rel="stylesheet" href="css/css.css">
	</head>
<body>
	<header>
		<div id='dns'><?php echo BRANDNAME; ?></div>
		<div id='uroboros'></div>
		<div id='radiation'></div>
	</header>
	<div id='forms'>
			<?php
				include('request.php');
				request();
			?>
			<form action=index.php method=GET> <!-- search -->
			  <fieldset>
    				<legend>Search Domain/Jump:</legend>
				<ul>
					<li>jump: <input type=textarea name=d placeholder="domain for jump"/></li>
					<li>Check online (not 100% [in last 5 minutes]): 
					<input type=checkbox name=check_online value='y' /><li/>
					<li><input type=submit value='search' /></li>
				</ul>
			  </fieldset>
			</form>
<!-- -->
			<form action=index.php method=GET> <!-- add domain -->
			  <fieldset>
    				<legend>Add Domain:</legend>
				<ul>
					<li>host:<input type=textarea name=host placeholder="example.i2p"/></li>
					<li>b64:<input type=textarea name=b64 placeholder="base64"/></li>
					<li>desc:<input type=textarea name=desc placeholder="description"/></li>
					<li><input type=submit value="add domain" /></li>
				</ul>
			  </fieldset>
			</form>
	</div>
	<footer>
		<p>Source code on <a href='https://github.com/wipedlifepotato/dns.chudo.i2p'>github</a></p>
		<p><a href='hosts.txt'>hosts</a>|<a href='new-hosts.txt'>new-hosts</a></p>
	</footer>
</body>
</html>
