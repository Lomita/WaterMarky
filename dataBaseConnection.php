<?php
    $host = 'localhost';
    $username = 'dbConnect';
    $password = 'Gwy^*FrXUa6V8%54kG';
    $database = 'watermarky_db';

    // connect with database
    try
    {
        if (!$mysqli = new mysqli($host, $username, $password, $database))
        {
            throw new Exception('Unable to connect');
        }
            
    }
    catch(Exception $e)
    {
        error_log($e->getMessage());
    }
 ?>
