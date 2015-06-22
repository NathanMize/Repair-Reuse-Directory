<?php
header('Content-type: text/html; charset=utf-8');
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

//url to be used for in returned json
$filePath = explode('/', $_SERVER['PHP_SELF'], -1);
$filePath = implode('/', $filePath);
$url = "http://" . $_SERVER['HTTP_HOST'] . $filePath;

if (isset($_GET['id']) && (count($_GET) == 1))
{
    if ( ! (is_numeric($_GET['id'])) 
            OR ( ! (stristr($_GET['id'], '.') === FALSE))
            OR ( ! (stristr($_GET['id'], ',') === FALSE))
        ) {

            exit();
        }

    //GET CATEGORY.
    $id;
    $name;
    if ( ! ($stmt = $mysqli->prepare("SELECT id, name 
                                        FROM categories 
                                        WHERE id=?")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }             

    if ( ! ($stmt->bind_param("i", $_GET['id'])))
    {
      echo "<p>Binding parameters failed: (" . $stmt->errno . ") " 
        . $stmt->error;
    }

    if ( ! ($stmt->execute()))
    { 
      echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
    }

    if ( ! ($stmt->bind_result($id, $name)))
    {
        echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }
       
    if ( ! ($stmt->fetch())) 
    { 
        exit();             
    }

    if ( ! ($stmt->close()))
    {
        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
    }







    //GET ITEMS OF CATEGORY
    $item_id;
    $item_name;
    $json_string = "{\"category-id\": \"" . $id ."\", \"category-name\": \"" 
        . $name . "\", \"items\": [";

    if ( ! ($stmt = $mysqli->prepare("SELECT c.id, c.name 
                                        FROM contents c 
                                        INNER JOIN categories cat 
                                        ON c.cat_id = cat.id 
                                        WHERE cat.id=? 
                                        ORDER BY c.name")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }           

    if ( ! ($stmt->bind_param("i", $_GET['id'])))
    {
        echo "<p>Binding parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }

    if ( ! ($stmt->execute()))
    { 
        echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
    }

    if ( ! ($stmt->bind_result($item_id, $item_name)))
    {
        echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }

    while ($stmt->fetch())
    {              
        $json_string = $json_string . "{\"id\": \"" . $item_id 
            . "\", \"name\": \"" . $item_name . "\", \"item_url\": \"" 
            . $url . "/items.php?id=" . $item_id . "\"}, ";
    }

    $json_string = rtrim($json_string, ", ");  //strip end comma
    $json_string = $json_string . "]}";

    echo $json_string;

    if ( ! ($stmt->close()))
    {
        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
    }
}
else
{
    //ALL CATEGORIES.
    $id;
    $name;
    $json_string = "{\"categories\": [";

    if ( ! ($stmt = $mysqli->prepare("SELECT id, name 
                                        FROM categories 
                                        ORDER BY name")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }             

    if ( ! ($stmt->execute()))
    { 
        echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
    }

    if ( ! ($stmt->bind_result($id, $name)))
    {
        echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }
       
    while ($stmt->fetch())
    {              
        $json_string = $json_string . "{\"id\": \"" . $id ."\", \"name\": \"" 
            . $name . "\", \"category_url\": \"" . $url . "/categories.php?id=" 
            . $id . "\"}, ";
    }

    $json_string = rtrim($json_string, ", ");  //strip end comma
    $json_string = $json_string . "]}";

    echo $json_string;

    if ( ! ($stmt->close()))
    {
        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
    }
}

//Close connection
if ( ! ($mysqli->close()))
{
    echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
}
?>

