<?php    
    session_start();                    
    //debug
    //echo '<pre>';
    //print_r($_POST);
    //echo '</pre>';

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

    if(isset($_SESSION['dropDownItems'][$_POST['picDropDown']]['filePath']) && 
             $_SESSION['dropDownItems'][$_POST['waterMarkDropDown']]['filePath'])
    {
        // Open the image to draw a watermark
        $image = new Imagick();
        $image->readImage(getcwd(). "\\".$_SESSION['dropDownItems'][$_POST['picDropDown']]['filePath']);

        // Open the watermark image
        // Important: the image should be obviously transparent with .png format
        $watermark = new Imagick();
        $watermark->readImage(getcwd(). "\\".$_SESSION['dropDownItems'][$_POST['waterMarkDropDown']]['filePath']);

        // Retrieve size of the Images to verify how to print the watermark on the image
        $img_Width = $image->getImageWidth();
        $img_Height = $image->getImageHeight();
        $watermark_Width = $watermark->getImageWidth();
        $watermark_Height = $watermark->getImageHeight();

        // Check if the dimensions of the image are less than the dimensions of the watermark
        // In case it is, then proceed to 
        if ($img_Height < $watermark_Height || $img_Width < $watermark_Width) {
            // Resize the watermark to be of the same size of the image
            $watermark->scaleImage($img_Width, $img_Height);

            // Update size of the watermark
            $watermark_Width = $watermark->getImageWidth();
            $watermark_Height = $watermark->getImageHeight();
        }

        // Calculate the position
        $x = ($img_Width - $watermark_Width) / 2;
        $y = ($img_Height - $watermark_Height) / 2;

        // Draw the watermark on your image
        $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);

        // save the image 
        $image->writeImage(getcwd(). "/enchanted_pics/pic_watermark." . $image->getImageFormat()); 
        $_SESSION['preview_pic'] = "enchanted_pics/pic_watermark." . $image->getImageFormat();

        return popMsg("Successfully enchanted picture");
    }
?>