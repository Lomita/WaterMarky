<?php
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

  if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
      header('Location: WaterMarky.php');

  require 'dataBaseConnection.php';

  // Init
    $error = $message =  '';
    $firstname = $lastname = $email = $username = '';

  // did "POST" carry Data?
  if($_SERVER['REQUEST_METHOD'] == "POST")
  {
      /* Output of entire $_POST Array
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
      */

      // firstname exists, miniimum 1 character and maximum 30 characters long
      if(isset($_POST['firstname']) && !empty(trim($_POST['firstname'])) && strlen(trim($_POST['firstname'])) <= 30)
        // escape special characters > stop script injection
        $firstname = htmlspecialchars(trim($_POST['firstname']));
  
      else 
        $error .= "Please enter a correct firstname.<br />";

      // lastname exists, miniimum 1 character and maximum 30 characters long
      if(isset($_POST['lastname']) && !empty(trim($_POST['lastname'])) && strlen(trim($_POST['lastname'])) <= 30)
        //escape special characters > stop script injection
        $lastname = htmlspecialchars(trim($_POST['lastname']));

      else 
        $error .= "Please enter a correct lastname.<br />";

      // mail exists, miniimum 1 character and maximum 100 characters long
      if(isset($_POST['email']) && !empty(trim($_POST['email'])) && strlen(trim($_POST['email'])) <= 100)
      {
        $email = htmlspecialchars(trim($_POST['email']));
        
        // mail correct?
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
          $error .= "Please enter a correct mailadress.<br />";
      } 
      else 
        // output error message
        $error .= "Please enter a valid mailadress.<br />";

      // username exists, miniimum 6 character and maximum 30 characters long
      if(isset($_POST['username']) && !empty(trim($_POST['username'])) && strlen(trim($_POST['username'])) <= 30)
      {
        $username = trim($_POST['username']);
        // does the username meet the requirements? (minimum 6 characters, upper- and lower-case letter)
        if(!preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{6,}/", $username))
          $error .= "The username is not in the correct format.<br />";
      } 
      else 
        // output error message
        $error .= "Please enter a correct username.<br />";

      // password exists, minimum 8 characters
      if(isset($_POST['password']) && !empty(trim($_POST['password']))){
        $password = trim($_POST['password']);
        //does the password meet the requirements? (minimum 8 characters, numbers, no breaks, minimum one upper- and one lower-case letter)
        if(!preg_match("/(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password))
          $error .= "The password does not meet the required format.<br />";

      } 
      else
        // output error message
        $error .= "Please enter a correct lastname.<br />";

      // wirte data into Database if there are no errors
      if(empty($error))
      {
        $username = htmlspecialchars(trim($_POST['username']));
        $password = htmlspecialchars(trim($_POST['password']));
        $firstname = htmlspecialchars(trim($_POST['firstname']));
        $lastname = htmlspecialchars(trim($_POST['lastname']));
        $username = htmlspecialchars(trim($_POST['username']));
        $email = htmlspecialchars(trim($_POST['email']));
        $role_id = 1; //standard user

        //check if username already exists
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
            if($user['username'] === $username)
            {
              $error = 'Username '.$username.' is already taken!';
              error_log("SIGN UP FAILED: ERROR: username already taken User: ".$username);
            }    
          }
        }              

        if(empty($error))
        {
          $salted = "iLiKeMy".$password."ButIlIkeCaKeMuChMoRe";

          $password = password_hash($salted, PASSWORD_DEFAULT);

          $query = "INSERT INTO users (role_id, email, firstname, lastname, password,username)
          VALUES (?,?,?,?,?,?); ";

          $stmt = $mysqli->prepare($query);
          
          if($stmt == false)
          {
            error_log("MYSQLI ERROR: ".$mysqli->connect_error);
            $error = 'Something went wrong!';
          }

          if(empty($error))
          {
            $stmt->bind_param('isssss', $role_id, $email, $firstname,$lastname,$password,$username);		
            $stmt->execute();
    
            $result = $stmt->get_result();
            $stmt->close();
    
            error_log("SIGN UP SUCCESS: User: ".$username);
            header("Location: sign_in.php");
          }
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
		<title>WaterMarky | Sign up</title>
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
      <h1>Sign up</h1>
      <p>
        Please sign up to use this service.
      </p>
      <?php
        // output error message
        if(!empty($error))
          echo "<div class=\"alert alert-danger\" role=\"alert\">" . htmlspecialchars($error) . "</div>";
        else if (!empty($message))
          echo "<div class=\"alert alert-success\" role=\"alert\">" . htmlspecialchars($message) . "</div>";
      ?>
      <form action="" method="post">
        <!-- vorname -->
        <div class="form-group">
          <label for="firstname">Firstname *</label>
          <input type="text" name="firstname" class="form-control" id="firstname"
                  value="<?php echo $firstname ?>"
                  placeholder="Enter you're firstname."
                  required="true">
        </div>
        <!-- nachname -->
        <div class="form-group">
          <label for="lastname">Lastname *</label>
          <input type="text" name="lastname" class="form-control" id="lastname"
                  value="<?php echo $lastname ?>"
                  placeholder="Enter you're lastname"
                  maxlength="30"
                  required="true">
        </div>
        <!-- email -->
        <div class="form-group">
          <label for="email">Mail *</label>
          <input type="email" name="email" class="form-control" id="email"
                  value="<?php echo $email ?>"
                  placeholder="Enter you're mailadress."
                  maxlength="100"
                  required="true">
        </div>
        <!-- benutzername -->
        <div class="form-group">
          <label for="username">Username *</label>
          <input type="text" name="username" class="form-control" id="username"
                  value="<?php echo $username ?>"
                  placeholder="Upper- and lower-case letters, min 6 characters."
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
        <button type="submit" name="button" value="submit" class="btn btn-primary">Sign up</button>
        <a class="btn btn-secondary" data-toggle="collapse.show" href="WaterMarky.php" role="button">Back</a>
      </form>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  </body>
</html>
