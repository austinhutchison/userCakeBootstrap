<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
$userId = $_GET['id'];

//Check if selected user exists
if(!userIdExists($userId)){
	header("Location: admin_users.php"); die();
}

$userdetails = fetchUserDetails(NULL, NULL, $userId); //Fetch user details

//Forms posted
if(!empty($_POST))
{	
	//Delete selected account
	if(!empty($_POST['delete'])){
		$deletions = $_POST['delete'];
		if ($deletion_count = deleteUsers($deletions)) {
			$successes[] = lang("ACCOUNT_DELETIONS_SUCCESSFUL", array($deletion_count));
		}
		else {
			$errors[] = lang("SQL_ERROR");
		}
	}
	else
	{
		//Update display name
		if ($userdetails['display_name'] != $_POST['display']){
			$displayname = trim($_POST['display']);
			
			//Validate display name
			if(displayNameExists($displayname))
			{
				$errors[] = lang("ACCOUNT_DISPLAYNAME_IN_USE",array($displayname));
			}
			elseif(minMaxRange(5,25,$displayname))
			{
				$errors[] = lang("ACCOUNT_DISPLAY_CHAR_LIMIT",array(5,25));
			}
			elseif(!ctype_alnum($displayname)){
				$errors[] = lang("ACCOUNT_DISPLAY_INVALID_CHARACTERS");
			}
			else {
				if (updateDisplayName($userId, $displayname)){
					$successes[] = lang("ACCOUNT_DISPLAYNAME_UPDATED", array($displayname));
				}
				else {
					$errors[] = lang("SQL_ERROR");
				}
			}
			
		}
		else {
			$displayname = $userdetails['display_name'];
		}
		
		//Activate account
		if(isset($_POST['activate']) && $_POST['activate'] == "activate"){
			if (setUserActive($userdetails['activation_token'])){
				$successes[] = lang("ACCOUNT_MANUALLY_ACTIVATED", array($displayname));
			}
			else {
				$errors[] = lang("SQL_ERROR");
			}
		}
		
		//Update email
		if ($userdetails['email'] != $_POST['email']){
			$email = trim($_POST["email"]);
			
			//Validate email
			if(!isValidEmail($email))
			{
				$errors[] = lang("ACCOUNT_INVALID_EMAIL");
			}
			elseif(emailExists($email))
			{
				$errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));
			}
			else {
				if (updateEmail($userId, $email)){
					$successes[] = lang("ACCOUNT_EMAIL_UPDATED");
				}
				else {
					$errors[] = lang("SQL_ERROR");
				}
			}
		}
		
		//Update title
		if ($userdetails['title'] != $_POST['title']){
			$title = trim($_POST['title']);
			
			//Validate title
			if(minMaxRange(1,50,$title))
			{
				$errors[] = lang("ACCOUNT_TITLE_CHAR_LIMIT",array(1,50));
			}
			else {
				if (updateTitle($userId, $title)){
					$successes[] = lang("ACCOUNT_TITLE_UPDATED", array ($displayname, $title));
				}
				else {
					$errors[] = lang("SQL_ERROR");
				}
			}
		}
		
		//Remove permission level
		if(!empty($_POST['removePermission'])){
			$remove = $_POST['removePermission'];
			if ($deletion_count = removePermission($remove, $userId)){
				$successes[] = lang("ACCOUNT_PERMISSION_REMOVED", array ($deletion_count));
			}
			else {
				$errors[] = lang("SQL_ERROR");
			}
		}
		
		if(!empty($_POST['addPermission'])){
			$add = $_POST['addPermission'];
			if ($addition_count = addPermission($add, $userId)){
				$successes[] = lang("ACCOUNT_PERMISSION_ADDED", array ($addition_count));
			}
			else {
				$errors[] = lang("SQL_ERROR");
			}
		}
		
		$userdetails = fetchUserDetails(NULL, NULL, $userId);
	}
}

$userPermission = fetchUserPermissions($userId);
$permissionData = fetchAllPermissions();

require_once("models/header.php");

echo "
<h1>$websiteName</h1>
<h2>Admin User</h2>
<div class='row'>";

include("left-nav.php");

echo "
<div id='main' class='span9'>";

echo resultBlock($errors,$successes);

echo "
<div class='row'>
	<form name='adminUser' action='".$_SERVER['PHP_SELF']."?id=".$userId."' method='post'>
			<div class='span4'>
			<h3>Manage</h3>
			<p>
				<label>Display Name:</label>
				<input type='text' name='display' value='".$userdetails['display_name']."' />
			</p>
			<p>
				<label>Email:</label>
				<input type='text' name='email' value='".$userdetails['email']."' />
			</p>
			<p>
				<label>Title:</label>
				<input type='text' name='title' value='".$userdetails['title']."' />
			</p>
			<div class='well'>
				<p>Remove Permission:
				<div class='permissions' data-toggle='buttons-checkbox'>";

					//List of permission levels user is apart of
					foreach ($permissionData as $v1) {
						if(isset($userPermission[$v1['id']])){
							echo "
							
								<button type='button' class='btn' name='removePermission[" . $v1['id'] . "]' value='" . $v1['id'] . "'><i class='icon-remove'></i> " . $v1['name'] . "</button>
							";
						}
					}

					//List of permission levels user is not apart of
					echo "</div></p>
					<p>Add Permission:
					<div class='permissions' data-toggle='buttons-checkbox'>
					";
					foreach ($permissionData as $v1) {
						if(!isset($userPermission[$v1['id']])){
							echo "
								<button type='button' class='btn' name='addPermission[" . $v1['id'] . "]' value='" . $v1['id'] . "'><i class='icon-plus'></i> " . $v1['name'] . "</button>
							";
						}
					}

					echo"
					</div>
				</p>";
				?>
				<script>
				$(document).ready(function() {
					$('.permissions button').click(function() {
						if($(this).next('input').length) {
							$(this).next('input').remove();
						}
						else {
							var permission = $(this).attr('name');
							var value = parseInt($(this).attr('value'));
							$(this).after("<input type='hidden' name='" + permission + "' value='" + value + "'>");
						}
						
						
					});
				});
				</script>
<?php 
				echo "

			</div>
			<p>
				<label class='checkbox'>
				<input type='checkbox' name='delete[".$userdetails['id']."]' id='delete[".$userdetails['id']."]' value='".$userdetails['id']."'>
				Delete</label>
			</p>
			<p>
				<label>&nbsp;</label>
				<input type='submit' class='btn btn-primary' value='Update' class='submit' />
			</p>
		</div>

		<div class='span4'>
			<h3>Detail</h3>
			<table class='table'>
				<tr>
					<td>
						ID
					</td>
					<td>
						".$userdetails['id']."
					</td>
				</tr>
				<tr>
					<td>
						Username
					</td>
					<td>
						".$userdetails['user_name']."
					</td>
				</tr>
				<tr>
					<td>
						Active
					</td>";

					//Display activation link, if account inactive
					if ($userdetails['active'] == '1'){
						echo "<td>Yes</td>";	
					}
					else{
						echo "<td>No
						</p>
						<p>
						<label>Activate:</label>
						<input type='checkbox' name='activate' id='activate' value='activate'>
						</td>";
					}

					echo "
				</tr>
				<tr>
					<td>
						Sign Up
					</td>
					<td>
					".date("j M, Y", $userdetails['sign_up_stamp'])."
					</td>
				</tr>
				<tr>
					<td>
						Last Sign In
					</td>
					<td>";

					//Last sign in, interpretation
					if ($userdetails['last_sign_in_stamp'] == '0'){
						echo "Never";	
					}
					else {
						echo date("j M, Y", $userdetails['last_sign_in_stamp']);
					}

					echo "
					</td>
				</tr>
			</table>
		</div>
	</form>
</div><!--row-->
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
