'<!DOCTYPE html>
<html lang=CH>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta http-equiv="X-Content-Security-Policy" content="default-src 'self'; script-src 'self'; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta http-equiv="X-WebKit-CSP" content="default-src 'self'; script-src 'self'; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>WaterMarky | Sign out</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    </head>
    <body> 
        <?php
            session_start();
    
            if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1200)) 
            {
                if(isset($_SESSION['LAST_ACTIVITY']))
                    error_log("SESSION TIMEOUT: LAST_ACTIVITY: ".$_SESSION['LAST_ACTIVITY']." User: ".$_SESSION['username']);

                // last request was more than 20 minutes ago
                session_unset();     // unset $_SESSION variable for the run-time 
                session_destroy();   // destroy session data in storage
          
                header('Location: sign_in.php');
            }
            else
                $_SESSION['LAST_ACTIVITY'] = time();
          
            if (isset($_SESSION['CREATED'])) 
                $_SESSION['CREATED'] = time();
            else if (time() - $_SESSION['CREATED'] > 1200) 
            {
                // session started more than 20 minutes ago
                session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
                $_SESSION['CREATED'] = time();  // update creation time
            }

            //logout destroy session
            if(isset($_SESSION))
            {
                error_log("LOGOUT: User: ".$_SESSION['username']);
                $_SESSION = array();
                session_destroy();

                echo  '<div class="container p-4 mb-2 bg-dark text-light" style="text-align:center"> 
                        <h1 >See ya soon :P</h1>
                        <img src="rsc/cat.jpg" alt="smug_cat" mb-auto class="img-thumbnail img-responsive">
                        <a class="nav-link align-middle btn btn-primary" data-toggle="collapse" href="WaterMarky.php" role="button" aria-expanded="false" >Back to Home</a>  
                      </div>';
            }
            else    
                header("Location: WaterMarky.php"); 
        ?>    
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>
</html>