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
		
		// prüfung benutzername
		if(!preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}/", $username) || strlen($username) > 30){
			$error .= "Der Benutzername entspricht nicht dem geforderten Format.<br />";
		}
	} else {
		$error .= "Geben Sie bitte den Benutzername an.<br />";
	}
	// password
	if(!empty(trim($_POST['password']))){
		$password = trim($_POST['password']);
		// passwort gültig?
		if(!preg_match("/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)){
			$error .= "Das Passwort entspricht nicht dem geforderten Format.<br />";
		}
	} else {
		$error .= "Geben Sie bitte das Passwort an.<br />";
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
				$message = 'Sie wurden erfolgreich angemeldet!';

				session_regenerate_id();
				
				$_SESSION = array();
				$_SESSION['username'] = $username;
				$_SESSION['loggedin'] = true;

				header('Location: WaterMarky.php');		
			}
			else
			{
				$error = 'Benutzername oder Password falsch!';
			}
		}
	}
}

?>
<!DOCTYPE html>
<html lang=CH>
	<head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>WaterMarky | Login</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	</head>
	<body> 
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
		<h1>Login</h1>
		<p>
			Bitte melden Sie sich mit Benutzernamen und Passwort an.
		</p>
		<?php
			// fehlermeldung oder nachricht ausgeben
			if(!empty($message))
				echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
			else if(!empty($error))
				echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";

		?>
		<form action="" method="POST">
			<div class="form-group">
			<label for="username">Benutzername *</label>
			<input type="text" name="username" class="form-control" id="username"
					value=""
					placeholder="Gross- und Keinbuchstaben, min 6 Zeichen."
					maxlength="30" required="true"
					pattern="(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}"
					title="Gross- und Keinbuchstaben, min 6 Zeichen.">
			</div>
			<!-- password -->
			<div class="form-group">
				<label for="password">Password *</label>
				<input type="password" name="password" class="form-control" id="password"
						placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, keine Umlaute"
						pattern="(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$"
						title="mindestens einen Gross-, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen, mindestens 8 Zeichen lang,keine Umlaute."
						required="true">
			</div>
			<button type="submit" name="button" value="submit" class="btn btn-info">Anmelden</button>
			<a class="btn btn-warning" data-toggle="collapse.show" href="WaterMarky.php" role="button" aria-expanded="false" aria-controls="collapseExample">Zurück</a>
		</form>
	</div>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>