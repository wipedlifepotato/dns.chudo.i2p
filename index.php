<?php include('templates/header.php');?>
	<div id='forms'>
			<form action=request.php method=GET> <!-- search -->
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
			<form action=request.php method=GET> <!-- add domain -->
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
<?php include('templates/footer.php');?>
