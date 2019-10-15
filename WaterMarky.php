<!DOCTYPE html>
<html>
    <?php
        //phpinfo();
        session_start();
        if(!isset($_SESSION['dropDownItems']))
            $_SESSION['dropDownItems'] = array (NULL);

        //true to show already uploaded images ;)
        if(!isset($_SESSION['newFile']))
            $_SESSION['newFile'] = true;
        
        if(!isset($_SESSION['picSelection']))
            $_SESSION['newFile'] = true;
    ?>
	<head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>WaterMarky</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	</head>
	<body>
		<div class="container">
			<header>
				<h1 class="display-4">WaterMarky v1.0</h1>
			</header>
        </div>
        
        <!-- UPLOAD -->
        <div class="container" name="upload">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="input-group mb-3">
                    <input type="file" name="file" id="inputGroupFile03">
                    <div class="col"></div>
                    <button class="btn btn-primary" type="submit" name="submit" id="inputGroupFileAddon03">Upload</button>
                </div>
            </form>
        </div>

        <!-- FILESELECTION -->
        <div class="container" name="upload">
            <div>
                <h6>Choose picture</h6>
                <select class="custom-select" id="picDropDown" name="picDropDown">
                    <?php               
                        if($_SESSION['newFile'] === true)
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
                                    print_r('<option value="'.$i.'">'.$image_name.'</option>');
                                    $_SESSION['dropDownItems'][$i]['filePath'] = $image_name;
                                }        
                            }
                            $_SESSION['newFile'] === false;
                        }
                    ?>        
                </select>
            </div>      
        </div>
            <?php
                
                //DEBUG :3
                //echo '<pre>';
                //print_r($_POST['picDropDown']);
                //echo '</pre>';
                        
                if(isset($_SESSION['dropDownItems'][0]['filePath']))
                {
                    /* Create Imagick object */
                    $Imagick = new Imagick();

                    /* Create a drawing object and set the font size */
                    $ImagickDraw = new ImagickDraw();
                    $ImagickDraw->setFontSize( 50 );

                    /* Read image into object*/
                    $Imagick->readImage(/*'../htdocs/WaterMarky/'.$_SESSION['dropDownItems'][0]['filePath']*/ 'C:\xampp\htdocs\WaterMarky\upload\Download.5da589e02e4782.73848217.jpg');

                    /*
                    /* Seek the place for the text */
                    $ImagickDraw->setGravity( Imagick::GRAVITY_CENTER );

                    /* Write the text on the image */
                    $Imagick->annotateImage( $ImagickDraw, 4, 20, 0, "Test Watermark" );

                    /* Set format to png */
                    $Imagick->setImageFormat( 'jpg' );

                    /* Output */
                    header( "Content-Type: image/{$Imagick->getImageFormat()}" );
                    echo $Imagick->getImageBlob();
                }
                
                /* Array containing sample image file names
                $images = array("kites.jpg", "balloons.jpg");
                
                 Loop through array to create image gallery
                foreach($images as $image){
                    echo '<div class="img-box">';
                        echo '<img src="images/' . $image . '" width="200" alt="' .  pathinfo($image, PATHINFO_FILENAME) .'">';
                        echo '<p><a href="download.php?file=' . urlencode($image) . '">Download</a></p>';
                    echo '</div>';
                }*/
            ?>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>
