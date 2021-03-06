
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
?>

<!DOCTYPE html>
<html lang=CH>
	<head>
        <meta charset="UTF-8" />
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; script-src-elem *; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta http-equiv="X-Content-Security-Policy" content="default-src 'self'; script-src 'self'; script-src-elem *; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta http-equiv="X-WebKit-CSP" content="default-src 'self'; script-src 'self'; script-src-elem *; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>WaterMarky | Pre Page</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	</head>
	<body> 

    <!-- icon and title -->
    <div class="container bg-dark text-light"> 
        <div class="container bg-light  text-dark"> 
        <a class="navbar-brand align-middle text-dark" href="WaterMarky.php">
            <img src="rsc/wizard-nav-bar.svg" width="70" height="70" class="d-inline-block align-left">
            <h1 class="d-inline-block align-middle">WaterMarky v2.0</h1>
        </a>
        </div>
    </div>
    
    <!-- NAV BAR -->
    <div class="container p-4 mb-2 bg-dark  text-light"> 
        <nav class="navbar navbar-expand-lg navbar-white bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto ">
                        <li class="nav-item active">
                            <a class="nav-link align-right text-dark" href="sign_in.php"><h6>Sign in</h6> <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link align-right text-dark" href="sign_up.php"><h6>Sign up</h6> <span class="sr-only">(current)</span></a>
                        </li>
                    </ul>    
                </div>
            </nav>
        </div>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>
