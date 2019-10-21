
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
        $fileFormat = $_POST['fileFormat'];
    
        $supported_format = array('jpg', 'png', 'bmp', 'svg', 'pdf'); 
        if (isset($fileFormat) && in_array($fileFormat, $supported_format))
        {
            // Open the image to draw a watermark
            $image = new Imagick();
            $image->readImage(getcwd(). "\\".$filepath);

            $image->setImageFormat($fileFormat);

            //sav converted pic
            $image->writeImage(getcwd(). "/enchanted_pics/download_pic." . $image->getImageFormat());
            $filepath = getcwd(). "/enchanted_pics/download_pic." . $image->getImageFormat();
        }

        $filepath="C:/xampp/htdocs/WaterMarky/enchanted_pics/download_pic.svg";
        $nam =basename($filepath);


        // Process download
        if(file_exists($filepath)) 
        {
            switch(strtolower(substr(strrchr($filepath,'.'),1)))
            {
                case 'pdf': $mime = 'application/pdf'; break;
                case 'svg': $mime = 'image/svg+xml'; break;
                case 'bmp': $mime = 'image/bmp'; break;
                case 'jpg': $mime = 'image/jpg'; break;
                case 'png': $mime = 'image/png'; break;
                default: $mime = 'application/force-download';
            }
            header('Pragma: public');   // required
            header('Expires: 0');       // no cache
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private',false);
            header('Content-Type: '.$mime);
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: '.filesize($filepath));    // provide file size
            //flush(); // Flush system output buffer
            readfile($filepath);
            return popMsg("Successfully enchanted picture");
        }
        return popMsg("Something went wrong :(");
	}
?>