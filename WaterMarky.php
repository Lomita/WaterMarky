<?php
    session_start();

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > 1200) 
    {
        if(isset($_SESSION['username']))
            error_log("SESSION TIMEOUT: LAST_ACTIVITY: ".$_SESSION['LAST_ACTIVITY']." User: ".$_SESSION['username']);

        // last request was more than 20 minutes ago
        session_unset();     // unset $_SESSION variable for the run-time 
        session_destroy();   // destroy session data in storage
        header('Location: sign_in.php');
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
    {
        //phpinfo();
        if(!isset($_SESSION['dropDownItems']))
        $_SESSION['dropDownItems'] = array (NULL);

        //true to show already uploaded images ;)
        if(!isset($_SESSION['newFile']))
        $_SESSION['newFile'] = true;
    }
    else
        header('Location: PreWaterMarky.php');
?>

<!DOCTYPE html>
<html lang=CH>
	<head>
        <meta charset="UTF-8" />
        <!-- 
            https://wiki.selfhtml.org/wiki/Sicherheit/Content_Security_Policy

            CSP  Die Anweisung default-src 'self' bedeutet, dass er prinzipiell nur zusätzliche Inhalte laden darf, wenn sie vom selben Server stammen.
            Die Anweisung script-src 'self' im content-Attribut erklärt dem Browser, dass er nur JavaScript-Dateien laden darf, die vom selben Server stammen wie dieses Dokument.
            Diese Direktive hat eine zusätzliche Auswirkung: Der Browser darf keinen JavaScript-Code ausführen, der im HTML-Dokument selbst notiert ist. 
            Soll JavaScript ausgeführt werden, so muss es in einer separaten Datei notiert stehen.
            Damit auch ältere Versionen des Internet Explorer erreicht werden, benötigt es ein weiteres <meta>-Element mit "X-Content-Security-Policy". 
            Auch ältere Versionen von auf der Webkit-Engine basierenden Browsern (wie z.B. Chrome oder Safari) 
            unterstützen vielleicht noch nicht standardmäßig die CSP und benötigen stattdessen "X-Webkit-CSP" als Header(-Ersatz).
        
        -->
        <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; script-src-elem *; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta http-equiv="X-Content-Security-Policy" content="default-src 'self'; script-src 'self'; script-src-elem *; img-src *; style-src 'unsafe-inline'; style-src-elem *">
        <meta http-equiv="X-WebKit-CSP" content="default-src 'self'; script-src 'self'; script-src-elem *;  img-src *; style-src 'unsafe-inline'; style-src-elem *">
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>WaterMarky | Home</title>
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
                        <?php
                        
                        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                            echo '<li class="nav-item active ">
                                <a class="nav-link align-middle text-success" href="user_info.php" >'.htmlspecialchars(trim($_SESSION['username'])).' <span class="sr-only">(current)</span></a>
                            </li>   
                            <li class="nav-item dropdown">
                            <a class="nav-link align-right text-primary" href="user_info.php" >Account Type: '.$_SESSION['role_id'].'</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link align-right text-danger" href="sign_out.php" >Sign out</a>
                            </li>';
                        else
                            echo '<li class="nav-item active">
                                    <a class="nav-link align-right text-dark" align="right" href="sign_in.php"><h6>Sign in</h6> <span class="sr-only">(current)</span></a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link align-right text-dark" href="sign_up.php"><h6>Sign up</h6> <span class="sr-only">(current)</span></a>
                                </li>';
                        ?>
                    </ul>    
                </div>
            </nav>
        </div>
        
        <!-- UPLOAD -->
        <div class="container p-4 mb-2 bg-dark  text-light">
        <h4>Upload pictures</h4>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="input-group mb-3">
                    <input type="file" name="file" id="inputGroupFile03">
                    <div class="col"></div>
                    <button class="btn btn-primary" type="submit" name="submit" id="upload">Upload</button>
                </div>
            </form>
        </div>

        <!-- PICTURE AND WATERMARK -->
        <div class="container p-4 mb-2 bg-dark  text-light">
        <h4>Picture & Watermark</h4>
            <form action="enchant_pictures.php" method="post">
            <div class="form-group">
                <div class="form-row">
                    <div class="col">
                        <h6>Canvas</h6>
                        <select class="custom-select" id="picDropDown" name="picDropDown">
                            <?php               
                                if(isset($_SESSION['newFile']) && $_SESSION['newFile'] === true && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                {
                                    //refill combo box
                                    $_SESSION['dropDownItems'] = array(NULL);
                                    $all_files = glob("upload/*.*");
                                    for ($i=0; $i<count($all_files); $i++)
                                    {
                                        $image_name = $all_files[$i];
                                        $supported_format = array('jpg', 'png', 'bmp', 'svg');
                                        $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                                        if (in_array($ext, $supported_format))
                                        {
                                            //echo '<img src="'.$image_name .'" alt="'.$image_name.'" />'."<br /><br />";
                                            print_r('<option value="'.$i.'">'.htmlspecialchars(basename($image_name)).'</option>');
                                            $_SESSION['dropDownItems'][$i]['filePath'] = $image_name;
                                        }        
                                    }
                                }
                            ?>        
                        </select>
                    </div>
                    <div class="col">
                        <h6>Picmark</h6>
                        <select class="custom-select" id="waterMarkDropDown" name="waterMarkDropDown">
                            <?php               
                                if(isset($_SESSION['newFile']) && $_SESSION['newFile'] === true && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                {
                                    //refill combo box
                                    $_SESSION['dropDownItems'] = array(NULL);
                                    $all_files = glob("upload/*.*");
                                    for ($i=0; $i<count($all_files); $i++)
                                    {
                                        $image_name = $all_files[$i];
                                        $supported_format = array('jpg', 'png', 'bmp', 'svg');
                                        $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                                        if (in_array($ext, $supported_format))
                                        {
                                            //echo '<img src="'.$image_name .'" alt="'.$image_name.'" />'."<br /><br />";
                                            print_r('<option value="'.$i.'">'.htmlspecialchars(basename($image_name)).'</option>');
                                            $_SESSION['dropDownItems'][$i]['filePath'] = $image_name;
                                        }        
                                    }
                                    $_SESSION['newFile'] === false;
                                }
                            ?>        
                        </select>
                    </div> 
                </div>    
            </div>
            <div class="form-group">
                <div class="form-row">
                    <div class="col">
                    <h6>Textmark</h6>
                        <input name="text" type="text" class="form-control" placeholder="Enter text (optional)">
                    </div>
                    <?php
                    if(isset($_SESSION['role_id']) && strcmp($_SESSION['role_id'], "Magick User") == 0)
                            echo'<div class="col">
                                <h6>Shapemark</h6>
                                    <select name="inputShape" class="form-control">
                                        <option selected>Add shape (optional)</option>
                                        <option value="rectangle">rectangle</option>
                                        <option value="circle">circle</option>
                                    </select>
                                </div>'
                    ?>
                </div>
                <br>
                <h6>Mark Alignement</h6>
                <div class="form-row">
                    <div class="col">
                        <select name="vertical_pos" class="form-control">
                            <option value="top">Top</option>
                            <option value="middle">Middle</option>
                            <option value="bottom">Bottom</option>
                        </select>
                    </div>
                    <div class="col">
                        <select name="horizontal_pos" class="form-control">
                            <option value="right">Right</option>
                            <option value="middle">Middle</option>
                            <option value="left">Left</option>
                        </select>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary" type="submit" name="submit" id="magick">Do the Image Magick</button>
                    </div>
                </div>
            </div>
            </form>
        </div>

        <!-- DOWNLOAD -->
        <div class="container p-4 mb-2 bg-dark  text-light"> 
            <form action="download.php" method="post">
                <h4>Download</h4>
                <div class="form-group">
                    <div class="form-row">
                        <div class="col">
                        <h6>Horizontal Resolution</h6>
                            <input name="horizontal_res" type="number" class="form-control" placeholder="Enter horizontal resolution X. (optional)"
                                   min="0" max="4096">
                        </div>
                        
                        <div class="col">
                        <h6>Vertical Resolution</h6>
                            <input name="vertical_res" type="number" class="form-control" placeholder="Enter vertical resolution Y. (optional)"
                                   min="0" max="4096">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-row">
                    <?php
                    if(isset($_SESSION['role_id']) && strcmp($_SESSION['role_id'], "Magick User") == 0)
                    echo '
                        <div class="col">
                        <h6>File format</h6>
                            <select id="fileFormat" name="fileFormat" class="form-control">
                                <option>jpg</option>
                                <option>png</option>
                                <option>bmp</option>
                                <option>svg</option>
                                <option>pdf</option>
                            </select>
                        </div>
                        ' ?>
                        <div class="col">
                        <h6 style="visibility:hidden">Download</h6>
                            <button class="btn btn-primary" type="submit" name="submit" id="download">Download</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- PREVIEW -->
        <div class="container p-4 mb-2 bg-dark text-light"> 
        <h4>Preview</h4>
         <?php
                if(isset($_SESSION['preview_pic']) && is_file($_SESSION['preview_pic']))
                    echo '<a href="'.$_SESSION['preview_pic'].'"><img src="'.$_SESSION['preview_pic'].'" alt="preview" class="img-thumbnail"></a>';      
                else
                    echo '<img src="rsc/no_img.png" alt="preview" class="img-thumbnail">';     
            ?>
            </div>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
</html>
