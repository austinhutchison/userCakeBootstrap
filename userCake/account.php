<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/
$page_name = "Account";
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

echo "
<h1>$websiteName</h1>
<h2>Account</h2>
<div class='row'>";

include("left-nav.php");

echo "
<div id='main' class='span9'>
Hey, $loggedInUser->displayname. This is an example secure page designed to demonstrate some of the basic features of UserCake. Just so you know, your title at the moment is $loggedInUser->title, and that can be changed in the admin panel. You registered this account on " . date("M d, Y", $loggedInUser->signupTimeStamp()) . ".
</div>
</div><!--row-->
<div id='bottom'></div>
</div><!--container-->
</body>
</html>";

?>
