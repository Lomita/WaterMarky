<?php

require 'dataBaseConnection.php';
session_start();
    
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > 1200) 
{
	if(isset($_SESSION['username']))
		error_log("SESSION TIMEOUT: LAST_ACTIVITY: ".$_SESSION['LAST_ACTIVITY']." User: ".$_SESSION['username']);

	// last request was more than 20 minutes ago
	session_unset();     // unset $_SESSION variable for the run-time 
	session_destroy();   // destroy session data in storage
}
else
	$_SESSION['LAST_ACTIVITY'] = time();

	
if (!isset($_SESSION['CREATED']))
	$_SESSION['CREATED'] = time();
else if(isset($_SESSION['CREATED']) && (time() - $_SESSION['CREATED']) > 1200)
{
	if(isset($_SESSION['username']))
		error_log("SESSION REGENERATE ID: CREATED: ".$_SESSION['CREATED']." User: ".$_SESSION['username']);
	
	// session started more than 20 minutes ago
	session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
	$_SESSION['CREATED'] = time();  // update creation time
} 

$error = '';
$message = '';

// form was sent and visitor is not logged in.
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error))
{
	// username
	if(!empty(trim($_POST['username'])))
	{
		$username = trim($_POST['username']);
		
		// checking username
		if(!preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}/", $username) || strlen($username) > 30)
			$error .= "The username does not meet the required format.<br />";
	} 
	else 
		$error .= "Please enter you're username.<br />";
	
	// password
	if(!empty(trim($_POST['password'])))
	{
		$password = trim($_POST['password']);
		// password valid?
		if(!preg_match("/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password))
			$error .= "The password does not meet the required format.<br />";
	} 
	else 
		$error .= "Please enter you're password.<br />";
	
	// no errors
	if(empty($error))
	{
		$username = htmlspecialchars(trim($_POST['username']));
		$password = htmlspecialchars(trim($_POST['password']));
		$saltedPw = "iLiKeMy".$password."ButIlIkeCaKeMuChMoRe";

		$query = "SELECT * FROM users WHERE username = ?";
		
		$stmt = $mysqli->prepare($query);
		if($stmt == false)
		{
			error_log("MYSQLI ERROR: ".$mysqli->connect_error);
			$error = 'Something went wrong!';
		}

		if(empty($error))
		{
			$stmt->bind_param('s', $username);		
			$stmt->execute();
			
			$result = $stmt->get_result();

			while($user = $result->fetch_assoc())
			{
				if(password_verify($saltedPw, $user['password']) && $user['username'] === $username)
				{
					$message = 'You have been logged in successfully!';

					session_regenerate_id(true);
					
					$_SESSION = array();
					$_SESSION['username'] = htmlspecialchars(trim($username));
					$_SESSION['loggedin'] = true;

					$role = 'User';
					if($user['role_id'] == 2)
						$role = 'Magick User';

					$_SESSION['role_id'] = $role;

					//Login Successfull
					error_log("LOGIN SUCCESS: User: ".$_SESSION['username']);

					return header('Location: WaterMarky.php');		
				}
			}

			//Log login attempts
			$ur = htmlspecialchars(trim($username));
			$pw = htmlspecialchars(trim($password));
			error_log("LOGIN FAILED: User: ".$ur." | PW: ".$pw);
			
			$error = 'Username or password are wrong!';
		}
	}
}

?>
<!DOCTYPE html>
<html lang=CH>
	<head>
        <meta charset="UTF-8" />
		<meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta http-equiv="X-Content-Security-Policy" content="default-src 'self'; script-src 'self'; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta http-equiv="X-WebKit-CSP" content="default-src 'self'; script-src 'self'; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>WaterMarky | Sign in</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	</head>
    <body>
		<!-- icon and titel -->
		<div class="container bg-dark text-light"> 
			<div class="container bg-light  text-dark"> 
				<a class="navbar-brand align-middle text-dark" href="WaterMarky.php">
					<img src="rsc/wizard-nav-bar.svg" width="70" height="70" class="d-inline-block align-left">
					<h1 class="d-inline-block align-middle">WaterMarky v2.0</h1>
				</a>
			</div>
    	</div>
    
	<div class="container p-4 mb-2 bg-dark  text-light">  
	<h1>Sign in</h1>
	<p>
		Please Sign in using you're username and password.
	</p>
		<?php
			// output error message
			if(!empty($error))
			echo "<div class=\"alert alert-danger\" role=\"alert\">" . htmlspecialchars($error) . "</div>";
			else if (!empty($message))
			echo "<div class=\"alert alert-success\" role=\"alert\">" . htmlspecialchars($message) . "</div>";
		?>
		<form action="" method="post">
			
			<!-- username -->
			<div class="form-group">
				<label for="username">Username *</label>
				<input type="text" name="username" class="form-control" id="username"
						value=""
						placeholder="Upper- and lower-case, min 6 characters."
						maxlength="30" required="true"
						pattern="(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}"
						title="Upper- and lower-case, min 6 characters.">
			</div>

			<!-- password -->
			<div class="form-group">
				<label for="password">Password *</label>
				<input type="password" name="password" class="form-control" id="password"
						placeholder="Upper- and lower-case letters, numbers, specialcharacters, min. 8 characters"
						pattern="(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$"
						title="minimum one Upper-, one lower-case letter, one number and one specialcharacter, minimum 8 characters long."
						required="true">
			</div>
			
			<button type="submit" name="button" value="submit" class="btn btn-primary" >Log in</button>
			<a class="btn btn-secondary" data-toggle="collapse.show" href="WaterMarky.php" role="button">Back</a>
		
		</form>
	</div>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>