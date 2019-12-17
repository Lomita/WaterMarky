<?php
    require 'dataBaseConnection.php';
    session_start();
?>

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <title>Adressbuch Pro | Konto Übersicht</title>
        </head>
    <body>
        <div class="container">  
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand align-middle" href="adressbuch.php">
                <img src="../img/nav-bar-icon.svg" width="35" height="35" class="d-inline-block align-middle" alt=""> Adressbuch Pro</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto ">
                    
                        <?php
                        
                        if(empty($error))
                        {

                            $query = "SELECT * FROM users WHERE username = ?";
                            
                            $stmt = $mysqli->prepare($query);
                            $stmt->bind_param('s', $_SESSION['username']);		
                            $stmt->execute();
                            
                            $result = $stmt->get_result();
                    
                            while($user = $result->fetch_assoc())
                            {
                                if($user['username'] === $_SESSION['username'])
                                {
                                    $slastname = $user['lastname'];
                                    $sfirstname = $user['firstname'];
                                    $semail = $user['email'];
                                    $spassword = $user['password'];
                                		
                                }
                                else
                                {
                                    $error = '  username or password wrong!';
                                }
                            }
                        }
                    
                    
                    
                            //handle navigation items for user and guests
                            if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                echo '<li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Account<span class="sr-only">(current)</span></a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="adressbuch.php">Home</a>
                                    <a class="dropdown-item" href="logout.php">Log out</a>
                                </div>
                                </li>
                                <li class="nav-item active ">
                                    <a class="nav-link align-middle" >Logged in as '.$_SESSION['username'].' <span class="sr-only">(current)</span></a>
                                </li>';
                            else{
                                echo '<li class="nav-item active">
                                        <a class="nav-link" align="right" href="login.php">Login <span class="sr-only">(current)</span></a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="register.php">Sign up <span class="sr-only">(current)</span></a>
                                    </li>';
                            }

                                    $error = $message =  '';
                                    $firstname = $lastname = $email = $username = '';


                                    if($_SERVER['REQUEST_METHOD'] == "POST"){

                                       /* echo "<pre>";
                                        print_r($_POST);
                                        echo "</pre>";*/
                                        
                                        
                                       // firstname exists, at least 1 character and maximum 30 characters long
                                    if(!empty(trim($_POST['change_firstname'])) && strlen(trim($_POST['change_firstname'])) <= 30){
                                    $firstname = htmlspecialchars(trim($_POST['change_firstname']));
                                    }else {
                                    $error .= "Please enter you're firstname.<br />";}

                                // lastname exists, at least 1 character and maximum 30 characters long
                                if(!empty(trim($_POST['change_lastname'])) && strlen(trim($_POST['change_lastname'])) <= 30){
                                    $lastname = htmlspecialchars(trim($_POST['change_lastname']));
                                 } else {
                                    $error .= "Please enter you're lastname.<br />";}

                                // mailadress exists, at least 1 character and maximum 100 characters long
                                /*if(!empty(trim($_POST['search_mail'])) && strlen(trim($_POST['search_mail'])) <= 100)
                                {
                                    $email = htmlspecialchars(trim($_POST['search_mail']));
                                    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false){
                                        $error .= "Please enter you're mailadress.<br />";
                                } 
                                else {
                                    $error .= "Please enter a valid mailadress.<br />";
                                }}*/// password exists, at least 8 characters
                                if(isset($_POST['password']) && !empty(trim($_POST['password'])))
                                {
                                    $password = trim($_POST['password']);
                                    //does the password comply with the requirement? (minimum 8 characters, numbers, no breaks, minimum one upper and one lower case letter)
                                    if(!preg_match("/(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password))
                                    {
                                        $error .= "The password does not meet the requirements.<br />";
                                    } 
                                    else{ 
                                        $error .= "Please enter a correct lastname.<br />";}
                                }
                                
                                echo $error;
                                    if(empty($error))
                                    {
                                        
                                        //$password = htmlspecialchars(trim($_POST['password']));
                                        $firstname = htmlspecialchars(trim($_POST['change_firstname']));
                                        $lastname = htmlspecialchars(trim($_POST['change_lastname']));
                                        //$email = htmlspecialchars(trim($_POST['search_mail']));
                                        
                                        $password = password_hash($password, PASSWORD_DEFAULT);
                                
                                        $query = "UPDATE users SET lastname=?,firstname=? WHERE username = ?; ";
                                
                                        $stmt = $mysqli->prepare($query);
                                        $stmt->bind_param('sss', /*$email, $firstname,*/$lastname,$firstname,$_SESSION['username']);		
                                        $stmt->execute();
                                
                                        $result = $stmt->get_result();
                                        $stmt->close();
                                        
                                        echo($result);
                                
                                        header("Location: user_info.php");
                                    }
                                }
                            
                        ?>
                    </ul>    
                </div>
            </nav>
            </div>
            <br>
            <br>
            <div class="container">
                <form method="post" id="sub" >
                    

                        <a>Nachname</a> 
                        <input type="text" class="form-control"
                                id="lastname" disabled=true name="change_lastname" 
                                value="<?php echo $slastname;?>" 
                                aria-label="Contact info" aria-describedby="button-addon2"
                                maxlength="30"
                  required="true"><br />
                        <a>Vorname</a>
                        <input type="text" class="form-control" id="firstname" disabled=true name="change_firstname" value="<?php echo $sfirstname?>" aria-label="Contact info" aria-describedby="button-addon2" maxlength="30"
                  required="true"><br />
                        <a>e-Mail</a>
                        <input type="text" class="form-control" id="mail" disabled=true name="search_mail" value="<?php echo $semail?>" aria-label="Conact info"maxlength="100"
                  required="true" aria-describedby="button-addon2"><br />
                        <a>Passwort</a>
                        <input type="password" class="form-control" id="password" disabled=true name="search_text" value="<?php echo $spassword?>" aria-label="Contact info" aria-describedby="button-addon2" pattern="(?=^.{8,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$"
                  title="at least one upper-, one lower-case letter, one number and a specialcharacter, minimum 8 characters long."
                  required="true"><br />

                        <input type="submit" class="btn btn-dark" name="Search" visible=false value="Save" id="button-change"></button>

                        <!--<div class="input-group-append">-->
                            <!--<input type="submit" class="btn btn-dark" name="Search" value="Edit" id="button-change"></button>-->
                            <button type="button" id="btn_change"class="btn btn-dark" >Edit</button>
                        <!--</div>-->                    
                </form>
                
                <br>
                <br>
                <?php
                
                    
                ?>
            </div>
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script>
                    $("form").submit(function(event){ 
                        

                    })
                    
                    $("#btn_change").on("click",function(){
                        $("#lastname").prop('disabled', false);
                        $("#firstname").prop('disabled', false);
                        //$("#email").prop('disabled', false);
                        //$("#password").prop('disabled', false);
                    })
        </script> 
    </body>
</html>

