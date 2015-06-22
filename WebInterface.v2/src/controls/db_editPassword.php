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

    $pass;

    if (   ($_POST['password1'] !== "") 
        && (strlen(trim($_POST['password1'])) !== 0) 
        && (strlen($_POST['password1']) >= 10) 
        && (strlen($_POST['password1']) <= 20)
    ) {
        if (   ($_POST['password2'] === "") 
            OR (strlen(trim($_POST['password2'])) === 0)  
            OR ($_POST['password1'] !== $_POST['password2'])
        ) {
            echo "{\"message\": \"error\"," 
                . "\"issue\": \"no match\"}";
            exit();
        } 
        else 
        {
            if ($_POST['password1'] === 'administrator')
            {
                $pass = 'administrator';
            } 
            else 
            {
                $pass = base64_encode(hash('sha256', $_POST['password1'] 
                    . "adc8904m2x0faoj9104qvv21530ujjj6b25vvewtwr324rs2rqjj"));
            }
        }
    } 
    else 
    {
        echo "{\"message\": \"error\"," 
                . "\"issue\": \"incorrect length\"}";
        exit();
    }


    //Prepare a statement
    if ( ! ($stmt = $mysqli->prepare("UPDATE users 
                                        SET password=? 
                                        WHERE username=?")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }             

    //Bind variables
    if ( ! ($stmt->bind_param("ss", $pass, $_SESSION['username'])))
    {
        echo "<p>Binding parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }

    //Execute statement
    if ( ! ($stmt->execute()))
    { 
        echo "<p>Executing statement failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }

    //Close statement
    if ( ! ($stmt->close()))
    {
        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
    }


    if ($pass !== 'administrator')
    {
        $_SESSION['default'] = false;
    }
    else
    {
        $_SESSION['default'] = true;
    }

    echo "{\"message\": \"success\"}";
}

if ( ! ($mysqli->close()))
{
    echo "<p>Close failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
?>