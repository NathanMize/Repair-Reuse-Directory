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

$mysqli2 = new mysqli($host, 
                    $username, 
                    $password, 
                    $database
);

if ($mysqli2->connect_errno) 
{
    echo "<p>Failed to connect to MySQL: (" . $mysqli->connect_errno
        . ") " . $mysqli->connect_error;
}

else 
{ 
 
    if (isset($_POST['id']))
    {
        $id = $_POST['id'];
    } 
    else 
    {
        exit();
    }
    
    //Prepare a statement
    if ( ! ($stmt = $mysqli->prepare("DELETE FROM companies 
                                            WHERE id=?")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }             
    
    //Bind variables
    if ( ! ($stmt->bind_param("i", $id)))
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
    
    //insert into table fresh data
    //Prepare a statement
    if ( ! ($stmt = $mysqli->prepare("SELECT id, name, info, phone, website, 
                                            street, city, st, zip 
                                            FROM companies 
                                            ORDER BY name")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }           
    
    //Execute statement
    if ( ! ($stmt->execute()))
    { 
        echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
    }
    
    $id;
    $name;
    $info;
    $phone;
    $website;
    $street;
    $city;
    $st;
    $zip;
    
    if ( ! ($stmt->bind_result($id, 
                                $name, 
                                $info, 
                                $phone, 
                                $website, 
                                $street, 
                                $city, 
                                $st, 
                                $zip)))
    {
        echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }


    $i = 1;
    $json_string = "{\"data\": \"business table\", \"html\": \""; 

   //Fetch results        
    while($stmt->fetch()){
        if(!($city === "")){
            $city = $city . ", ";
        }
        if ($zip == null){
            $zip = "";
        }
      
        $json_string = $json_string . "<tr><td>" . $i++ . "</td><td>" . $name 
            . "</td><td>"; 

        
        if ( ! ($stmt2 = $mysqli2->prepare("SELECT cat.name, cont.name 
                                            FROM contents cont 
                                            INNER JOIN company_content cc 
                                            ON cc.content_id = cont.id 
                                            INNER JOIN categories cat 
                                            ON cat.id = cont.cat_id 
                                            WHERE cc.company_id=? 
                                            ORDER BY cat.name, cont.name")))
        {
            echo "<p>Prepare failed: (" . $mysqli->errno . ") " 
                . $mysqli->error;
        }   
        
        //Bind variables
        if ( ! ($stmt2->bind_param("i", $id)))
        {
            echo "<p>Binding parameters failed: (" . $stmt2->errno . ") " 
                . $stmt2->error;
        }
        
        //Execute statement
        if ( ! ($stmt2->execute()))
        { 
            echo "<p>Execute failed: (" . $stmt2->errno . ") " 
                . $stmt2->error; 
        }
        
        $cat_name;
        $item_name;
        
        if ( ! ($stmt2->bind_result($cat_name, $item_name)))
        {
            echo "<p>Binding output parameters failed: (" . $stmt2->errno 
                . ") " . $stmt2->error;
        }
        
        $item_string = "";
        $print_cat_name;
        
        if ($stmt2->fetch())
        {
            $print_cat_name = $cat_name;
            $json_string = $json_string . "<strong>" . $cat_name 
                . "</strong><br>";
            $item_string = $item_string . $item_name . ", ";
        }
        
        while ($stmt2->fetch()) 
        {
            if ($print_cat_name === $cat_name)
            {
                $item_string = $item_string . $item_name . ", ";
            }
            else
            {
                $item_string = rtrim($item_string, ", ");  //strip end comma
                $print_cat_name = $cat_name;
                $json_string = $json_string . $item_string 
                    . "<br/><br/><strong>" . $cat_name . "</strong><br>";
                $item_string = "";
                $item_string = $item_string . $item_name . ", ";
            }
        }
        $item_string = rtrim($item_string, ", ");
        $json_string = $json_string . $item_string . "<br/><br/>";
        
        //Close statement
        if ( ! ($stmt2->close()))
        {
            echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
        }


        $json_string = $json_string . "</td><td><a href=\\\"" 
            . "viewBusiness.php?id=" . $id . "\\\" class=\\\"btn btn-link"
            . "\\\">Business Info</a><a href=\\\"editBusiness.php?id=" . $id 
            . "\\\" class=\\\"btn btn-link\\\">Edit</a><button type=\\\""
            . "button\\\" class=\\\"btn btn-link\\\" data-toggle=\\\"modal\\\" "
            . "data-target=\\\"#bus-delete-warning\\\" data-name=\\\"" 
            . $name . "\\\" data-id=\\\"" . $id 
            . "\\\">Delete</button></td></tr>";
    }
    
    $json_string = $json_string . "\"}";

    echo $json_string;

    //Close statement
    if ( ! ($stmt->close()))
    {
        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
    }
}
?>