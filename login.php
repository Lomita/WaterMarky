<?php

require 'dataBaseConnection.php';
session_start();

$error = '';
$message = '';

// form was sent and visitor is not logged in.
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)){
	// username
	if(!empty(trim($_POST['username']))){

		$username = trim($_POST['username']);
		
		// checking username
		if(!preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}/", $username) || strlen($username) > 30){
			$error .= "The username does not meet the required format.<br />";
		}
	} else {
		$error .= "Please enter you're username.<br />";
	}
	// password
	if(!empty(trim($_POST['password']))){
		$password = trim($_POST['password']);
		// passwort gültig?
		if(!preg_match("/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)){
			$error .= "The password does not meet the required format.<br />";
		}
	} else {
		$error .= "Please enter you're password.<br />";
	}
	
	// kein fehler
	if(empty($error))
	{
		$username = htmlspecialchars(trim($_POST['username']));
		$password = htmlspecialchars(trim($_POST['password']));

		$query = "SELECT * FROM users WHERE username = ?";
		
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param('s', $username);		
		$stmt->execute();
		
		$result = $stmt->get_result();

		while($user = $result->fetch_assoc())
		{
			if(password_verify($password, $user['password']) && $user['username'] === $username)
			{
				$message = 'You have been logged in successfully!';

				session_regenerate_id();
				
				$_SESSION = array();
				$_SESSION['username'] = $username;
				$_SESSION['loggedin'] = true;

				header('Location: WaterMarky.php');		
			}
			else
			{
				$error = 'Username or password are wrong!';
			}
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  </head>
  <body>
		<div class="container">
			<h1>Login</h1>
			<p>
				Please log in using you're username and password.
			</p>
			<?php
				// output error or success message
				if(!empty($message)){
					echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
				} else if(!empty($error)){
					echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
				}
			?>
			<form action="" method="POST">
				<div class="form-group">
				<label for="username">Username *</label>
				<input type="text" name="username" class="form-control" id="username"
						value=""
						placeholder="Upper- and lower-case letter, min 6 characters."
						maxlength="30" required="true"
						pattern="(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}"
						title="Upper- and lower-case letters, min 6 characters.">
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
		  		<button type="submit" name="button" value="submit" class="btn btn-info">Log in</button>
                <button type="reset" name="button" value="reset" class="btn btn-warning">Delete</button>
                <a class="btn btn-warning" data-toggle="collapse.show" href="WaterMarky.php" role="button" aria-expanded="false" aria-controls="collapseExample">Back</a>
			</form>
		</div>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	</body>
</html>