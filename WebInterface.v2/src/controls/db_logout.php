<?php
header('Content-type: text/html; charset=utf-8');
ini_set('display_errors',1);
error_reporting(E_ALL);

include './config.php';

session_start();

//If info sent to end session, 
//then end the session and kick back to account/loginPage.html
if (isset($_GET['action']) && ($_GET['action'] === 'end')) 
{
    $_SESSION = array();
    session_destroy();
	$filePath = explode('/', $_SERVER['PHP_SELF'], -2);
    $filePath = implode('/', $filePath);
    $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
    header("Location:{$redirect}/login.html", true); 
    die();
}
?>

