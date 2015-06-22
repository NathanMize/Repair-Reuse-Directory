<?php
header('Content-type: text/html; charset=utf-8');
ini_set('display_errors',1);
error_reporting(E_ALL);

include './config.php';

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
    //check for valid data from user
    if (strlen(trim($_POST['username'])) === 0)
    {
        echo "{\"message\": \"error\", \"issue\": \"no username\"}";
        exit();
    }

    if (strlen(trim($_POST['password'])) === 0)
    {
        echo "{\"message\": \"error\", \"issue\": \"no password\"}";
        exit();
    }



    //Check data is clean using mysqli
    //Compare date to what is in database for username
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES);

    //Prepare a statement
    if ( ! ($stmt = $mysqli->prepare("SELECT COUNT(username) 
                                        FROM `users` 
                                        WHERE username=?"))) 
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") "
            . $mysqli->error;
    }             

    //Bind variables
    if ( ! ($stmt->bind_param("s", $username))) 
    {
        echo "<p>Binding parameters failed: (" . $stmt->errno . ") "
            . $stmt->error;
    }

    //Execute statement
    if ( ! ($stmt->execute())) 
    { 
        echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    //Bind results
    $usernameCount;

    if ( ! ($stmt->bind_result($usernameCount))) 
    {
        echo "<p>Binding output parameters failed: (" . $stmt->errno
            . ") " . $stmt->error;
    }

    //Fetch results
    if ( ! ($stmt->fetch())) 
    {
        echo "<p>Fetching results failed: (" . $stmt->errno . ") "
            . $stmt->error;
    }

    //Close statement
    if ( ! ($stmt->close())) 
    {
        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    if ($usernameCount !== 1) 
    {
        echo "{\"message\": \"error\", \"issue\": \"incorrect info\"}";
        exit();
    }


    $pass;

    if ($_POST['password'] === 'administrator')
    {
        $pass = 'administrator';
    } 
    else 
    {
        $pass = base64_encode(hash('sha256', $_POST['password']
        . "adc8904m2x0faoj9104qvv21530ujjj6b25vvewtwr324rs2rqjj"));
    }


    //Prepare a statement
    if ( ! ($stmt = $mysqli->prepare("SELECT COUNT(username) 
                                        FROM `users` 
                                        WHERE username=? 
                                        AND password=?"))) 
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") "
            . $mysqli->error;
    }             

    //Bind variables
    if ( ! ($stmt->bind_param("ss", $username, $pass))) 
    {
        echo "<p>Binding parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }

    //Execute statement
    if ( ! ($stmt->execute())) 
    { 
        echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    //Bind results
    if ( ! ($stmt->bind_result($usernameCount))) 
    {
        echo "<p>Binding output parameters failed: (" . $stmt->errno
            . ") " . $stmt->error;
    }

    //Fetch results
    if ( ! ($stmt->fetch())) 
    {
        echo "<p>Fetching results failed: (" . $stmt->errno . ") "
            . $stmt->error;
    }

    //Close statement
    if ( ! ($stmt->close())) 
    {
        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    if ($usernameCount !== 1)
    {
        echo "{\"message\": \"error\", \"issue\": \"incorrect info\"}";
        exit();
    } 
    else 
    {
        //check for currently active session.  If active, then end 
        //  session and start new session for new username.
        if (isset($_SESSION['active'])) 
        {
            $_SESSION = array();
            session_destroy();
            die();
        }

        //otherwise session active is not set... 
        //then no current session in place... 
        //proceed with creating new session with username    
        //start session with username
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['active'] = TRUE;


        //direct to new page
        //if login was first time (administrator, administrator), direct to 
        //account page to reset info
        if (($username === 'administrator') && ($pass === 'administrator'))
        {
            $_SESSION['default'] = TRUE;
            echo "{\"message\": \"account page\"}";
        }

        //else, direct to home page
        else 
        {
            $_SESSION['default'] = FALSE;
            echo "{\"message\": \"home page\"}";
        }
    }
}

if ( ! ($mysqli->close()))
{
    echo "<p>Close failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
?>