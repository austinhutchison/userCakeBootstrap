<?php 
/*
UserCake Version: 2.0.1
http://usercake.com
*/
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Get token param
if(isset($_GET["token"]))
{	
	$token = $_GET["token"];	
	if(!isset($token))
	{
		$errors[] = lang("FORGOTPASS_INVALID_TOKEN");
	}
	else if(!validateActivationToken($token)) //Check for a valid token. Must exist and active must be = 0
	{
		$errors[] = lang("ACCOUNT_TOKEN_NOT_FOUND");
	}
	else
	{
		//Activate the users account
		if(!setUserActive($token))
		{
			$errors[] = lang("SQL_ERROR");
		}
	}
}
else
{
	$errors[] = lang("FORGOTPASS_INVALID_TOKEN");
}

if(count($errors) == 0) {
	$successes[] = lang("ACCOUNT_ACTIVATION_COMPLETE");
}

require_once("models/header.php");

echo "
<h1>$websiteName</h1>
<h2>Activate Account</h2>
<div class='row'>";

include("left-nav.php");

echo "
<div id='main' class='span9'>";

echo resultBlock($errors,$successes);

echo "
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>