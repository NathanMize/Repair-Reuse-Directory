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

    $name;

    if (   ($_POST['username'] !== "") 
        && (strlen(trim($_POST['username'])) !== 0) 
        && (strlen($_POST['username']) >= 6) 
        && (strlen($_POST['username']) <= 20)
    ) {
        if (  ( ! (stristr($_POST['username'], '"') === FALSE)) 
            OR ( ! (stristr($_POST['username'], "'") === FALSE)) 
            OR ( ! (stristr($_POST['username'], '<') === FALSE))
            OR ( ! (stristr($_POST['username'], '>') === FALSE))
            OR ( ! (stristr($_POST['username'], '&') === FALSE))
            OR ( ! (stristr($_POST['username'], " ") === FALSE))
        ) {
            echo "{\"message\": \"error\"," 
                . "\"issue\": \"incorrect characters\"}";
            exit();
        } 
        else 
        {
            $name = htmlspecialchars($_POST['username'], ENT_QUOTES);
        }
    } 
    else 
    {
        echo "{\"message\": \"error\", \"issue\": \"incorrect length\"}"; 
        exit();
    }


    //Prepare a statement
    if ( ! ($stmt = $mysqli->prepare("UPDATE users 
                                        SET username=? 
                                        WHERE username=?")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }             

    //Bind variables
    if ( ! ($stmt->bind_param("ss", $name, $_SESSION['username'])))
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

    //Update page and session username
    $_SESSION['username'] = $name;
    if ($_SESSION['username'] !== 'administrator')
    {
        $_SESSION['default'] = false;
    }
    else
    {
        $_SESSION['default'] = true;
    }

    echo "{\"message\": \"success\", \"username\": \"" . $_SESSION['username'] 
        . "\"}"; 
}

if ( ! ($mysqli->close()))
{
    echo "<p>Close failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
?>