<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/
$location = basename($_SERVER['PHP_SELF']);
echo "
<div id='left-nav' class='span3'>
<div class='well'>";
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Links for logged in user
if(isUserLoggedIn()) {
	echo "
	<ul class='nav nav-list'>
	<li " . (($location=='account.php')?"class='active'" : "") . "><a href='account.php'>Account Home</a></li>
	<li " . (($location=='user_settings.php')?"class='active'" : "") . "><a href='user_settings.php'>User Settings</a></li>
	<li " . (($location=='logout.php')?"class='active'" : "") . "><a href='logout.php'>Logout</a></li>
	</ul>";
	
	//Links for permission level 2 (default admin)
	if ($loggedInUser->checkPermission(array(2))){
	echo "
	<ul class='nav nav-list'>
	<li class='nav-header'>Admin</li>
	<li " . (($location=='admin_configuration.php')?"class='active'" : "") . "><a href='admin_configuration.php'>Configuration</a></li>
	<li " . (($location=='admin_users.php')?"class='active'" : "") . "><a href='admin_users.php'>Users</a></li>
	<li " . (($location=='admin_permissions.php')?"class='active'" : "") . "><a href='admin_permissions.php'>Permissions</a></li>
	<li " . (($location=='admin_pages.php')?"class='active'" : "") . "><a href='admin_pages.php'>Pages</a></li>
	</ul>";
	}
} 
//Links for users not logged in
else {
	echo "
	<ul class='nav nav-list'>
	<li " . (($location=='index.php')?"class='active'" : "") . "><a href='index.php'>Home</a></li>
	<li " . (($location=='login.php')?"class='active'" : "") . "><a href='login.php'>Login</a></li>
	<li " . (($location=='register.php')?"class='active'" : "") . "><a href='register.php'>Register</a></li>
	<li " . (($location=='forgot-password.php')?"class='active'" : "") . "><a href='forgot-password.php'>Forgot Password</a></li>";
	if ($emailActivation)
	{
	echo "<li " . (($location=='account.php')?"class='active'" : "") . "><a href='resend-activation.php'>Resend Activation Email</a></li>";
	}
	echo "</ul>";
}
echo "</div>
</div>";
?>
