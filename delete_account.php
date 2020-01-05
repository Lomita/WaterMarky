<?php
    require 'dataBaseConnection.php';
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

   /** sends a notification message and redirects to WaterMarky.php
     * @param msg message to confirm 
     */
    function popMsg($msg)
    {
        print_r('<script type="text/javascript" language="Javascript"> 
                confirm("'.$msg.'"); 
                window.location.href = "WaterMarky.php";
                </script>');
    }
    
    if(isset($_SESSION) && 
       isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && 
       isset($_SESSION['username']) && !empty($_SESSION['username']))
    {           
        $username = htmlspecialchars(trim($_SESSION['username']));

		$query = "DELETE FROM users WHERE username = ?";
        
        $stmt = $mysqli->prepare($query);
        if($stmt != false)
        {
            $stmt->bind_param('s', $username);		
            $stmt->execute();

            error_log("ACCOUNT DELTED: User: ".$_SESSION['username']);

            //logout destroy session
            $_SESSION = array();
            session_destroy();

            popMsg('Account delted!');
        }
        error_log("MYSQLI ERROR: ".$mysqli->connect_error);
        popMsg('Something went wrong!');
    } 
?>  

