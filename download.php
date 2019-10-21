<?php
    session_start();

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
            $image = new Imagick();
            $image->readImage(getcwd(). "\\".$filepath);

            $x_res = 200; 
            $y_res = 200;
            
            //custom resolution
            if(isset($_POST['horizontal_res']) ? $x_res = $_POST['horizontal_res'] : $x_res = $image->getImageHeight());
            if(isset($_POST['vertical_res']) ? $y_res = $_POST['vertical_res'] : $y_res = $image->getImageWidth());
        
            $image->resizeImage($x_res, $y_res, Imagick::FILTER_LANCZOS, 0.5);
    
            //custom format
            if(isset($_POST['fileFormat']) ? $fileFormat = $_POST['fileFormat'] : $fileFormat = $image->getImageFormat());

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

        return popMsg("Something went wrong :(");
	}
?>