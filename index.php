<?php include('templates/header.php');?>
	<div id='forms'>
			<form action=request.php method=GET> <!-- search -->
			  <fieldset>
    				<legend>Search Domain/Jump:</legend>
				<ul>
					<li><input type=textarea name=d placeholder="example.i2p"/></li>
					<li><input type=submit value='search' /></li></br>
					<li>Last seen[not 100%]: 
					<input type=checkbox name=check_online value='y' /><li/>
				</ul>
				<div style="float:right; font-size:0.5em" class=''>
					"last seen" would show a value for last 5 minutes, even if a new request came later
				</div>
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
