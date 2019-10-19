<!DOCTYPE html>
<html>
    <?php
        session_start();
        //phpinfo();
        if(!isset($_SESSION['dropDownItems']))
            $_SESSION['dropDownItems'] = array (NULL);

        //true to show already uploaded images ;)
        if(!isset($_SESSION['newFile']))
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
        <h6>Upload pictures</h6>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="input-group mb-3">
                    <input type="file" name="file" id="inputGroupFile03">
                    <div class="col"></div>
                    <button class="btn btn-primary" type="submit" name="submit" id="inputGroupFileAddon03">Upload</button>
                </div>
            </form>
        </div>

        <!-- FILESELECTION -->
        <div class="container" name="FileSelection">
            <form action="enchant_pictures.php" method="post">
                <div class="form-group">
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
                            }
                        ?>        
                    </select>
                </div>
                <div class="form-group">
                    <h6>Choose watermark</h6>
                    <select class="custom-select" id="waterMarkDropDown" name="waterMarkDropDown">
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
                
                <form>
                <div class="form-row">
                    <div class="col">
                        <select id="inputLocation" name="inputLocation" class="form-control">
                            <option selected>Choose location...</option>
                            <option value="middle">middle</option>
                            <option value="top-left">top left</option>
                            <option value="bottom-right">bottom right</option>
                        </select>
                    </div>
                    <div class="col">
                    <button class="btn btn-primary" type="submit" name="submit" id="inputGroupFileAddon03">Do The Image Magic</button>
                        <?php
                            if(isset($_POST['submit'])){
                                $_SESSION['location'] = $_POST['inputLocation'];
                            }
                        ?>
                    </div>
                </div>
            </form>   
        </div>

        <div class="container" id="download" name="download"> 
            <form>
                <div class="form-row">
                    <div class="col">
                        <select id="inputState" class="form-control">
                            <option selected>Choose format...</option>
                            <option>jpg</option>
                            <option>png</option>
                            <option>bmp</option>
                            <option>svg</option>
                            <option>pdf</option>
                        </select>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary" type="submit" name="submit" id="inputGroupFileAddon03">Do The Image Magic</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="container" id="preview" name="preview"> 
        <h6>Preview</h6>
         <?php
                if(isset($_SESSION['preview_pic']) && is_file($_SESSION['preview_pic']))
                    echo '<a href="'.$_SESSION['preview_pic'].'""><img src="'.$_SESSION['preview_pic'].'" alt="" class="img-thumbnail">';      
                else
                    echo '<img src="rsc/no_img.png" alt="" class="img-thumbnail">';     
        
                //DEBUG :3
                //echo '<pre>';
                //print_r($_SESSION['preview_pic']);
                //echo '</pre>';

                //https://www.tutorialrepublic.com/php-tutorial/php-file-download.php
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
        </div>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>
