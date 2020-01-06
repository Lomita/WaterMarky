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

    if(!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true)
        header('Location: WaterMarky.php');

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

    if (isset($_POST['submit']))
    {
        //get uploaded file
        $file = $_FILES['file'];
        
        //get file extension
        $tmp = explode('.', $file['name']);
        $fileExt = strtolower(end($tmp));

        //check if file extension is allowed
        $allowed = array('jpg', 'png', 'bmp', 'svg');
        if(!in_array($fileExt, $allowed))
        {
            error_log("UPLOAD FAILED: ERROR: filetype is not allowed User:".$_SESSION['username']);
            return popMsg("Files must be of one of the following types .jpg/.png/.bmp/.svg You cannot upload files of type ".$fileExt); 
        }
            
        //check if an error occured
        if($file['error'] !== 0)
        {
            error_log("UPLOAD FAILED: ERROR: ".$file['error']." User:".$_SESSION['username']);
            return popMsg("There was an error uploading your file error"); 
        }

        //check if file is bigger than 1GB    
        if($file['size'] > 1000000000)
        {
            error_log("UPLOAD FAILED: ERROR: file is to large User:".$_SESSION['username']);
            return popMsg("The file is too big! Cannot upload files of size larger than 1GB"); 
        }

        //create uniqe filename
        $uniqFileName = explode('.', $file['name'])[0].".".uniqid('', true).".".$fileExt;
        
        //upload
        if(move_uploaded_file($file['tmp_name'], 'upload/'.$uniqFileName))
        {
            $_SESSION['newFile'] = true; 
            error_log("UPLOAD SUCCESS: FILE: ".$uniqFileName." User: ".$_SESSION['username']);          
            return popMsg("Successfully uploaded");
        }

        error_log("UPLOAD FAILED: ERROR: There was an error saving the file FILE: ".$uniqFileName." User:".$_SESSION['username']);
        return popMsg("There was an error uploading your file!");     
    }
?>