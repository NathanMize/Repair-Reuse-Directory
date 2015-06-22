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
	
    if (strlen(trim($_POST['street'])) !== 0)
    {
        $street = htmlspecialchars($_POST['street'], ENT_QUOTES);
    } 
    else 
    {
        $street = "";
    }
    
    if (strlen(trim($_POST['city'])) !== 0) 
    {
        $city = htmlspecialchars($_POST['city'], ENT_QUOTES);
    } 
    else 
    {
        $city = "";
    }
    
    $state = htmlspecialchars($_POST['state'], ENT_QUOTES);

	if (strlen(trim($_POST['zip'])) !== 0) 
    {
        if (   (strlen($_POST['zip']) !== 5) 
            OR ( ! (is_numeric($_POST['zip']))) 
            OR ( ! (stristr($_POST['zip'], '.') === FALSE))
            OR ( ! (stristr($_POST['zip'], ',') === FALSE))
        ) {
            echo "{\"message\": \"error\", \"issue\": \"incorrect zip\"}";
            exit();
        }
        else 
        {
            $zip = htmlspecialchars($_POST['zip'], ENT_QUOTES); 
        }  
    } 
    else 
    {
        $zip = null;
	}

	if (strlen(trim($_POST['phone'])) !== 0) 
    {
        $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES); 
    } 
    else 
    {
        $phone = "";
    }
	
    if (strlen(trim($_POST['website'])) !== 0) 
    {
   		$website = htmlspecialchars($_POST['website'], ENT_QUOTES); 
    } 
    else 
    {
        $website = "";
    }
    
    if (strlen(trim($_POST['info'])) !== 0) 
    {
   		$info = htmlspecialchars($_POST['info'], ENT_QUOTES); 
    } 
    else 
    {
        $info = "";
    }

    if (strlen(trim($_POST['hours'])) !== 0) 
    {
        $hours = htmlspecialchars($_POST['hours'], ENT_QUOTES); 
    } 
    else 
    {
        $hours = "";
    }
    
    if (isset($_POST['items'])) 
    {
    	$items = $_POST['items'];
    } 
    else 
    {
        $items = "";
    }
    
    if (isset($_POST['items-resell'])) 
    {
        $items_resell = $_POST['items-resell'];
    } 
    else 
    {
        $items_resell = "";
    }
    
    if (isset($_POST['items-repair'])) 
    {
        $items_repair = $_POST['items-repair'];
    } 
    else 
    {
        $items_repair = "";
    }
    
    $id;
    $value;


    

    //Prepare a statement
    if ( ! ($stmt = $mysqli->prepare("INSERT INTO companies 
                            (name, info, phone, website, street, city, st, zip, hours) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }             

    //Bind variables
    if ( ! ($stmt->bind_param("sssssssis", $name, $info, $phone, $website, 
            $street, $city, $state, $zip, $hours)))
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




    //get new id
	//Prepare a statement
    if ( ! ($stmt = $mysqli->prepare("SELECT id 
                                    FROM companies 
                                    WHERE name=?")))
    {
        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }          

    //Bind variables
    if (! ($stmt->bind_param("s", $name)))
    {
        echo "<p>Binding parameters failed: (" . $stmt->errno . ") " 
            . $stmt->error;
    }

    //Execute statement
    if ( ! ($stmt->execute()))
    { 
        echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
    }

    if ( ! ($stmt->bind_result($id)))
    {
        echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " 
        . $stmt->error;
    }

    if( ! ($stmt->fetch()))
    {
        echo "<p>Fetching output parameters failed: (" . $stmt->errno . ") " 
        . $stmt->error;
    }

    //Close statement
    if ( ! ($stmt->close()))
    {
        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
    }


    
    //As long as there is at least one item in the variable array
    if ( ! ($items === ""))
    {
        $count = 0;
        //add entries to company_content table
        //loop through array of category id's and add entry to com_cat table 
        //with business id
        foreach ($items as $value)
        {
            //Prepare a statement
            if ( ! ($stmt = $mysqli->prepare("INSERT INTO company_content 
                                        (content_id, company_id, reuse, repair) 
                                        VALUES (?, ?, 0, 0)")))
            {
                echo "<p>Prepare failed: (" . $mysqli->errno . ") " 
                    . $mysqli->error;
                exit();
            }          

            //Bind variables
            if ( ! ($stmt->bind_param("ii", $value, $id)))
            {
                echo "<p>Binding parameters failed: (" . $stmt->errno . ") " 
                    . $stmt->error;
                exit();
            }

            //Execute statement
            if ( ! ($stmt->execute()))
            { 
                echo "<p>Execute failed: (" . $stmt->errno . ") " 
                    . $stmt->error; 
                exit();
            }

            //Close statement
            if ( ! ($stmt->close()))
            {
                echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
                exit();
            }
            $count++;
        }

        //set reuse nd resell checkbox info
        if ( ! ($items_resell === ""))
        {
            foreach ($items_resell as $value_resell)
            {
                foreach ($items as $value)
                {
                    if ($value_resell === $value)
                    {
                        if ( ! ($stmt = $mysqli->prepare("UPDATE company_content 
                                                            SET reuse=1 
                                                            WHERE content_id=? 
                                                            AND company_id=?")))
                        {
                            echo "<p>Prepare failed: (" . $mysqli->errno . ") " 
                                . $mysqli->error;
                            exit();
                        }          

                        //Bind variables
                        if ( ! ($stmt->bind_param("ii", $value, $id)))
                        {
                            echo "<p>Binding parameters failed: (" 
                                . $stmt->errno . ") " . $stmt->error;
                            exit();
                        }

                        //Execute statement
                        if ( ! ($stmt->execute()))
                        { 
                            echo "<p>Execute failed: (" . $stmt->errno . ") " 
                                . $stmt->error; 
                            exit();
                        }

                        //Close statement
                        if ( ! ($stmt->close()))
                        {
                            echo "<p>Close failed: (" . $stmt->errno . ") " 
                                . $stmt->error;
                            exit();
                        }
                        break;
                    }
                }
            }
        }
            
        if ( ! ($items_repair === ""))
        {
            foreach ($items_repair as $value_repair)
            {
                foreach ($items as $value)
                {
                    if ($value_repair === $value)
                    {
                        if ( ! ($stmt = $mysqli->prepare("UPDATE company_content 
                                                            SET repair=1 
                                                            WHERE content_id=? 
                                                            AND company_id=?")))
                        {
                            echo "<p>Prepare failed: (" . $mysqli->errno . ") " 
                                . $mysqli->error;
                            exit();
                        }          

                        //Bind variables
                        if ( ! ($stmt->bind_param("ii", $value, $id)))
                        {
                            echo "<p>Binding parameters failed: (" 
                                . $stmt->errno . ") " . $stmt->error;
                            exit();
                        }

                        //Execute statement
                        if ( ! ($stmt->execute()))
                        { 
                            echo "<p>Execute failed: (" . $stmt->errno . ") " 
                                . $stmt->error; 
                            exit();
                        }

                        //Close statement
                        if ( ! ($stmt->close()))
                        {
                            echo "<p>Close failed: (" . $stmt->errno . ") " 
                                . $stmt->error;
                            exit();
                        }
                        break;
                    }
                }
            }
        }

        if ($count === count($items))
        {
            echo "{\"message\": \"success\"}";
        }
    }
    //no items in the item array
    else 
    {
        echo "{\"message\": \"success\"}";
    }  
}

if ( ! ($mysqli->close()))
{
    echo "<p>Close failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
?>