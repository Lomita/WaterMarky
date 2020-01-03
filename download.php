<?php
    session_start();
    //echo '<pre>';
    //print_r($_POST);
	//print_r($_SESSION);
    //echo '</pre>';

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

    if(isset($_SESSION['preview_pic']) && is_file($_SESSION['preview_pic']))
    {
        $filepath = $_SESSION['preview_pic'];

        if(isset($filepath) && file_exists($filepath))
        {
            if(class_exists('Imagick'))
                $image = new Imagick();
            //Library could not be loaded    
            else
            {
                popMsg('Error: Your image could not be downloaded, contact the system administrator!');
                return;
            }

            $image->readImage(getcwd(). "\\".$filepath);

            $x_res = 200; 
            $y_res = 200;
            
            //custom resolution
            if(isset($_POST['horizontal_res']) && !empty($_POST['horizontal_res']) && is_numeric($_POST['horizontal_res'])) 
                $x_res = htmlspecialchars($_POST['horizontal_res']);
            else
                $x_res = $image->getImageWidth();
            
            if(isset($_POST['vertical_res']) && !empty($_POST['vertical_res']) && is_numeric($_POST['vertical_res']))
                $y_res = htmlspecialchars($_POST['vertical_res']);
            else
                $y_res = $image->getImageHeight();

            $image->resizeImage($x_res, $y_res, Imagick::FILTER_LANCZOS, 0.5);

            //custom format
            if(!empty(isset($_POST['fileFormat'])) ? $fileFormat = $_POST['fileFormat'] : $fileFormat = $image->getImageFormat());

            $image->setImageFormat($fileFormat);
            
            //save picture
            $image->writeImage(getcwd(). "/enchanted_pics/download_pic." . $image->getImageFormat());
            $filepath = getcwd(). "/enchanted_pics/download_pic." . $image->getImageFormat();
    
            // Process download
            if(file_exists($filepath)) 
            {
                switch(strtolower(substr(strrchr($filepath,'.'),1)))
                {
                    case 'pdf': $mime = 'application/pdf'; break;
                    case 'svg': $mime = 'image/svg+xml'; break;
                    case 'bmp': $mime = 'image/bmp'; break;
                    case 'jpg': $mime = 'image/jpeg'; break;
                    case 'png': $mime = 'image/png'; break;
                    default: $mime = 'application/force-download';
                }
                
                header('Pragma: public');   // required
                header('Expires: 0');       // no cache
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private',false);
                header('Content-Type: '.$mime);
                header('Content-Disposition: attachment; filename="'.basename($filepath).'');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: '.filesize($filepath));    // provide file size
                flush(); // Flush system output buffer
                readfile($filepath);
                return popMsg("Successfully downloaded");
            }
        }
        return popMsg("Im sorrry! I just lost your Picture! :(");
    }
    return popMsg("You have to create your picture first before you can download it! \\nPress Do The Image Magick! ;)");
?>