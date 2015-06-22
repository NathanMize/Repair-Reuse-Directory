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

if (isset($_GET['id']) && (count($_GET) == 1)) 
{
    if ( ! (is_numeric($_GET['id'])) 
            OR ( ! (stristr($_GET['id'], '.') === FALSE))
            OR ( ! (stristr($_GET['id'], ',') === FALSE))
        ) {

            exit();
        }
  
    //GET ITEM.
    $id;
    $name;
    if ( ! ($stmt = $mysqli->prepare("SELECT id, name 
                                        FROM contents 
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




    //GET BUSINESSES OF ITEM
    $bus_id;
    $bus_name;
    $info;
    $phone;
    $website;
    $street;
    $city;
    $state;
    $zip;
    $hours;
    $reuse;
    $repair;
    $json_string = "{\"item-id\": \"" . $id ."\", \"item-name\": \"" . $name 
        . "\", \"businesses\": [";

    if ( ! ($stmt = $mysqli->prepare("SELECT com.id, 
                                            com.name, 
                                            com.info, 
                                            com.phone, 
                                            com.website, 
                                            com.street, 
                                            com.city, 
                                            com.st, 
                                            com.zip, 
                                            com.hours, 
                                            cc.reuse, 
                                            cc.repair 
                                    FROM companies com 
                                    INNER JOIN company_content cc 
                                    ON cc.company_id = com.id
                                    WHERE cc.content_id=?"))) 
    {
      echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }           

    if ( ! ($stmt->bind_param("i", $id)))
    {
        echo "<p>Binding parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }

    if ( ! ($stmt->execute()))
    { 
        echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
    }

    if ( ! ($stmt->bind_result($bus_id, 
                                $bus_name, 
                                $info, 
                                $phone, 
                                $website, 
                                $street, 
                                $city, 
                                $state, 
                                $zip, 
                                $hours, 
                                $reuse, 
                                $repair))) 
    {
        echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }

    while ($stmt->fetch()) 
    {    
        //only include business that have a marked reuse or repair or both as 1.
        if (($reuse == 1) OR ($repair == 1))
        {
            if ( ! ($city === ""))
            {
                $city = $city . ", ";
            }
            if ($zip == null)
            {
                $zip = "";
            }
            

            $json_string = $json_string . "{\"id\": \"" . $bus_id 
                ."\", \"name\": \"" . $bus_name . "\", \"address\": \"" 
                . $street . " " . $city . $state . " " . $zip 
                . "\", \"hours\": \"" . $hours . "\", \"phone\": \"" . $phone 
                . "\", \"website\": \"" . $website . "\", \"info\": \"" . $info 
                . "\", \"application\": {\"reuse\": \"" . $reuse 
                . "\", \"repair\": \"" . $repair . "\"}}, ";   
        }
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