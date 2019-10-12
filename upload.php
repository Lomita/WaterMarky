<?php
    session_start();
    /** sendsa notification message and redirects to WaterMarky.php
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
            return popMsg("You cannot upload files of type ".$fileExt); 

        //checkk if an error occured
        if($file['error'] !== 0)
            return popMsg("There was an error uploading your file error code: ".$file['error']); 
        
        //check if file is bigger then 1GB    
        if($file['size'] > 1000000)
            return popMsg("The file is too big!"); 

        //create uniq filename
        $uniqFileName = explode('.', $file['name'])[0].".".uniqid('', true).".".$fileExt;
        
        //upload
        if(move_uploaded_file($file['tmp_name'], 'upload/'.$uniqFileName))
        {
            $_SESSION['newFile'] = true;            
            return popMsg("Successfully uploaded");
        }
        return popMsg("There was an error uploading your file!");     
    }
?>