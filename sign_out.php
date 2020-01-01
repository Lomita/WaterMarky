'<!DOCTYPE html>
<html lang=CH>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>WaterMarky | Sign out</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    </head>
    <body> 
        <?php
            session_start();
            
            //logout destroy session
            if(isset($_SESSION))
            {
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