<?php
    require 'dataBaseConnection.php';
    session_start();

    $slastname = $sfirstname = $semail = $spassword ='';

    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['username']) && !empty($_SESSION['username']))
    {    
        $tmpUsername = htmlspecialchars(trim($_SESSION['username']));

        //get userdata by username
        $query = "SELECT * FROM users WHERE username = ?";
        
        $stmt = $mysqli->prepare($query);
        if($stmt == false)
        {
            error_log("MYSQLI ERROR: ".$mysqli->connect_error);
            $error = 'Something went wrong!';
        }
        
        if(empty($error))
        {
            $stmt->bind_param('s', $tmpUsername);		
            $stmt->execute();
            
            $result = $stmt->get_result();

            while($user = $result->fetch_assoc())
            {
                if($user['username'] === $_SESSION['username'])
                {
                    error_log("USER DATA SUCCESS: User: ".$_SESSION['username']);
                    $slastname = $user['lastname'];
                    $sfirstname = $user['firstname'];
                    $semail = $user['email'];
                }
                else
                {
                    error_log("USER DATA ERROR: ERROR: Data corresponding to user not found User: ".$_SESSION['username']);
                    $error = 'The data corresponding to your account could not be found, contact the system administrator!';
                }
                    
            }

            if($_SERVER['REQUEST_METHOD'] == "POST" && 
            isset($_POST['save_data']) && 
            $_POST['change_firstname'] != NULL && 
            $_POST['change_lastname'] != NULL && 
            $_POST['change_mail'] != NULL)
            {
                $error = $message =  '';
                $firstname = $lastname = $email = $username = '';  

                if(strcmp($_POST['change_firstname'], $sfirstname) == 0 && 
                strcmp($_POST['change_lastname'], $slastname) == 0 && 
                strcmp($_POST['change_mail'], $semail) == 0)
                {
                    error_log("USER DATA ERROR: ERROR: Nothing has changed User: ".$_SESSION['username']);
                    $error .= "Nothing has changed!";
                }
                    

                // vorname vorhanden, mindestens 1 Zeichen und maximal 30 Zeichen lang
                if(!empty(trim($_POST['change_firstname'])) && strlen(trim($_POST['change_firstname'])) <= 30)
                    $firstname = htmlspecialchars(trim($_POST['change_firstname']));
                else 
                    $error .= "Please enter a correct first name.<br />";

                // nachname vorhanden, mindestens 1 Zeichen und maximal 30 zeichen lang
                if(!empty(trim($_POST['change_lastname'])) && strlen(trim($_POST['change_lastname'])) <= 30)
                    $lastname = htmlspecialchars(trim($_POST['change_lastname']));
                else
                    $error .= "Please enter a correct lastname ein.<br />";

                // emailadresse vorhanden, mindestens 1 Zeichen und maximal 100 zeichen lang
                if(!empty(trim($_POST['change_mail'])) && strlen(trim($_POST['change_mail'])) <= 100)
                {
                    $email = htmlspecialchars(trim($_POST['change_mail']));
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
                        $error .= "Please enter a correct email address<br />";
                } 
                else 
                    $error .= "Please enter a correct email address.<br />";
                
                if(empty($error))
                {
                    $firstname = htmlspecialchars(trim($_POST['change_firstname']));
                    $lastname = htmlspecialchars(trim($_POST['change_lastname']));
                    $email = htmlspecialchars(trim($_POST['change_mail']));
                
            
                    $query = "UPDATE users SET email=?,lastname=?,firstname=? WHERE username = ?; ";
            
                    $stmt = $mysqli->prepare($query);
                    if($stmt == false)
                    {
                        error_log("MYSQLI ERROR: ".$mysqli->connect_error);
                        $error = 'Something went wrong!';
                    }
            
                    if(empty($error))
                    {

                        $stmt->bind_param('ssss', $email, $lastname, $firstname, $_SESSION['username']);		
                        $stmt->execute();
                
                        $result = $stmt->get_result();
                        $stmt->close();
                        
                        echo($result);
                
                        error_log("USER DATA CHANGED SUCCESS: User: ".$_SESSION['username']);
                        header("Location: user_info.php");
                    }
                    error_log("USER DATA CHANGED FAILED: User: ".$_SESSION['username']);
                }
            }
        }     
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" && 
            isset($_POST['delete_account']))
            header("Location: delete_account.php");
    else    
        header("Location: WaterMarky.php");
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; script-src-elem * 'unsafe-inline'; img-src *; style-src 'unsafe-inline'; style-src-elem *">
    <meta http-equiv="X-Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; script-src-elem * 'unsafe-inline'; img-src *; style-src 'unsafe-inline'; style-src-elem *">
    <meta http-equiv="X-WebKit-CSP" content="default-src 'self'; script-src 'self' 'unsafe-inline'; script-src-elem * 'unsafe-inline';  img-src *; style-src 'unsafe-inline'; style-src-elem *">
        
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>WaterMarky | Profile</title>
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

    <!-- NAV BAR -->
    <div class="container p-4 mb-2 bg-dark  text-light"> 
        <nav class="navbar navbar-expand-lg navbar-white bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto ">    
                    <?php
                        //if not logged in return to watermarky mainpage
                        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                            echo '<li class="nav-item active ">
                                <a class="nav-link align-middle text-success" href="WaterMarky.php" >'.htmlspecialchars(trim($_SESSION['username'])).' <span class="sr-only">(current)</span></a>
                            </li>   
                            <li class="nav-item dropdown">
                            <a class="nav-link align-right text-primary" href="WaterMarky.php" >Account Type: '.$_SESSION['role_id'].'</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link align-right text-danger" href="sign_out.php" >Sign out</a>
                            </li>';
                        else
                            header("Location: WaterMarky.php");
                    ?>
                </ul>    
            </div>
        </nav>
    </div> 
    
    <!-- USER DATA -->  
    <div class="container p-4 mb-2 bg-dark  text-light">
        <?php
            // output error message
            if(!empty($error))
                echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
            else if (!empty($message))
                echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        ?>
        <form method="post" id="sub" >
            <!-- Firstname -->    
            <a class="text-left font-weight-bold">Firstname</a>
            <input type="text" class="form-control" id="firstname" disabled=true name="change_firstname" 
                value="<?php echo htmlspecialchars(trim($sfirstname))?>" aria-label="Kontaktinformation" 
                aria-describedby="button-addon2" maxlength="30" required="true">
            <br/>
            
            <!-- Lastname -->
            <a class="text-left font-weight-bold">Lastname</a> 
            <input type="text" class="form-control"
                    id="lastname" disabled=true name="change_lastname" 
                    value="<?php echo htmlspecialchars(trim($slastname));?>" 
                    aria-label="Kontaktinformation" aria-describedby="button-addon2"
                    maxlength="30"
                    required="true">
            <br/>

            <!-- Mail -->
            <a class="text-left font-weight-bold">Mail</a>
            <input type="text" class="form-control" id="email" disabled=true name="change_mail" 
                value="<?php echo htmlspecialchars(trim($semail))?>" aria-label="Kontaktinformation" maxlength="100"
                required="true" aria-describedby="button-addon2">
            <br/>

            <input type="submit" class="btn btn-success" name="save_data" value="Save" visible=false id="button-change"></button>
            <button type="button" id="btn_change"class="btn btn-warning" name="edit" >Edit</button>
            <a class="btn btn-danger" href="delete_account.php" onclick="return  confirm('All information will be erased!')">Delete Account</a>
        </form>
        
    </div>
    
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script> 
                //enable last- and first name
                $("#btn_change").on("click",function()
                {
                    $("#lastname").prop('disabled', false);
                    $("#firstname").prop('disabled', false);
                    $("#email").prop('disabled', false);
                })
    </script> 
</body>
</html>