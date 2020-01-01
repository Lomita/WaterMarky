<?php
    require 'dataBaseConnection.php';
    session_start();
  
    //sleep(500);

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
		$stmt->bind_param('s', $username);		
		$stmt->execute();

        //logout destroy session
        $_SESSION = array();
        session_destroy();

        popMsg('Account delted!');
    } 
?>  

