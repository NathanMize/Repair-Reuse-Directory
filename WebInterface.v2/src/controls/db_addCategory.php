<?php
header('Content-type: text/html; charset=utf-8');
ini_set('display_errors',1);
error_reporting(E_ALL);

include './config.php';

session_start();

//if session is not active, kick back to login.html
if ( ! (isset($_SESSION['active'])))
{
    $filePath = explode('/', $_SERVER['PHP_SELF'], -2);
    $filePath = implode('/', $filePath);
    $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
    header("Location:{$redirect}/login.html", TRUE); 
}

//Create connection with database.
$mysqli = new mysqli($host, 
                    $username, 
                    $password, 
                    $database
);

if ($mysqli->connect_errno) 
{
    echo "<p>Failed to connect to MySQL: (" . $mysqli->connect_errno
        . ") " . $mysqli->connect_error;
} 
else 
{ 

    //check data is clean using mysqli before inputting into database.  
    //Then input into database.

    if (strlen(trim($_POST['name'])) !== 0)
    {
        $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
    } 
    else 
    {
        echo "{\"message\": \"error\", \"issue\": \"no name\"}";
        exit();
    }

    //Prepare a statement
    if ( ! ($stmt = $mysqli->prepare("INSERT INTO categories (name) 
                                        VALUES (?)")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }             

    //Bind variables
    if ( ! ($stmt->bind_param("s", $name)))
    {
        echo "<p>Binding parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }

    //Execute statement
    if ( ! ($stmt->execute()))
    { 
        if ($stmt->errno === 1062)
        {
            echo "{\"message\": \"error\", \"issue\": \"duplicate name\"}";
            exit();
        }
    }

    //Close statement
    if ( ! ($stmt->close()))
    {
        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    echo "{\"message\": \"success\"}";
}

if ( ! ($mysqli->close()))
{
    echo "<p>Close failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
?>