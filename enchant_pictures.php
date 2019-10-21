<?php    
    session_start();                    
    //debug
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

    if(isset($_POST['picDropDown']) && isset($_SESSION['dropDownItems'][$_POST['picDropDown']]['filePath']) && 
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
        
        //echo "You have selected :" .$_POST['inputLocation'];  // Displaying Selected Value

        // Check if a location was selected
        // Check which location was chosen
        // Calculate the position
        
        //default top left
        $x = 0;
        $y = 0;
        
        if (isset($_POST['vertical_pos']) && isset($_POST['horizontal_pos'])) 
        {
            switch ($_POST['vertical_pos']) 
            {
                case 'top':
                    $x = ($img_Width - $watermark_Width) / 2;
                    $y = 0;
                break;

                case 'middle':
                    $x = ($img_Width - $watermark_Width) / 2;
                    $y = ($img_Height - $watermark_Height) / 2;
                break;

                case 'bottom':
                    $x = ($img_Width - $watermark_Width) / 2;
                    $y = ($img_Height - $watermark_Height);
                break;
            }

            switch ($_POST['horizontal_pos']) 
            {
                case 'left':
                    $x -= ($img_Width - $watermark_Width) / 2;
                break;

                case 'right':
                    $x += ($img_Width - $watermark_Width) / 2;
                break;
            }

            if($x < 0 ? $x = 0: $x = $x); 
            if($y < 0 ? $y = 0: $y = $y);
        } 

        //create new draw object
        $draw = new ImagickDraw();

        //echo "You have written :" .$_POST['text'];  // Displaying Selected Value
        
        // Check if a text was added
        // Draw text
        if (isset($_POST['text'])) {
            // Font properties
            $draw->setFont('Arial');
            $draw->setFillColor('white');
            $draw->setFontSize( 40 );
            $draw->setStrokeColor('black');
            $draw->setStrokeWidth(1);

            $yText = $img_Height - 20;
            $image->annotateImage($draw, 0, $yText, 0, $_POST['text']);
        }


        //echo "You have chosen:" .$_POST['inputShape'];  // Displaying Selected Value

        // Check if a shape was added
        // Draw shape
        if (isset($_POST['inputShape'])) {
            switch ($_POST['inputShape']) {
                
                case 'rectangle':
                    // rectangle properties
                    $draw->setFillColor('yellow');
                    $draw->setStrokeColor( new ImagickPixel( 'red' ) );
                    $x1Rect = ($watermark_Width * 0.25) + $x;
                    $y1Rect = ($watermark_Height * 0.015) + $y;
                    // Draw the rectangle
                    $draw->rectangle( $x1Rect,  $y1Rect,  $x1Rect + 100, $y1Rect + 100);
                    $image->drawImage($draw);
                break;

                case 'circle':
                    // circle properties
                    $draw->setFillColor('yellow');
                    $draw->setStrokeColor( new ImagickPixel( 'red' ) );
                    $xCircle = ($watermark_Width / 2) + $x;
                    $yCircle = ($watermark_Height / 2) + $y;
                    $rCircle = 100;
                    // Draw the circle
                    $draw->circle ($xCircle, $yCircle, $xCircle + $rCircle, $yCircle);
                    $image->drawImage($draw);
                break;
            }
        }

        // Draw the watermark on your image
        $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);

        // save the image 
        $image->writeImage(getcwd(). "/enchanted_pics/pic_watermark." . $image->getImageFormat()); 
        $_SESSION['preview_pic'] = "enchanted_pics/pic_watermark." . $image->getImageFormat();

        return popMsg("Successfully enchanted picture");
    }

    return popMsg("You have to upload some Pictures first by clicking Choose file and Upload :(");
?>