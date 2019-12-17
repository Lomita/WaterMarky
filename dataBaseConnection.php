<?php
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'watermarky_db';

    // connect with database
    $mysqli = new mysqli($host, $username, $password, $database);

    // error msg connection error
    if ($mysqli->connect_error) 
    {
        die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
    }
 ?>
