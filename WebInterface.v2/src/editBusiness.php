<?php
header('Content-type: text/html; charset=utf-8');
ini_set('display_errors',1);
error_reporting(E_ALL);

include './controls/config.php';

session_start();

//if session is not active, kick back to login.html
if(!isset($_SESSION['active'])){
    $filePath = explode('/', $_SERVER['PHP_SELF'], -1);
    $filePath = implode('/', $filePath);
    $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
    header("Location:{$redirect}/login.html", true); 
}

//Create connection with database.
$mysqli = new mysqli($host, 
                    $username, 
                    $password, 
                    $database
);

if ($mysqli->connect_errno) {
    echo "<p>Failed to connect to MySQL: (" . $mysqli->connect_errno
        . ") " . $mysqli->connect_error;
}


$mysqli2 = new mysqli($host, 
                    $username, 
                    $password, 
                    $database
);

if ($mysqli2->connect_errno) {
    echo "<p>Failed to connect to MySQL: (" . $mysqli->connect_errno
        . ") " . $mysqli->connect_error;
}

else {


    if(isset($_GET['id'])){
        $bus_id = $_GET['id'];

        //Prepare a statement
        if(!($stmt = $mysqli->prepare("SELECT name, info, phone, website, street, city, st, zip, hours FROM companies WHERE id=?"))){
            echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }           

        //Bind variables
        if(!($stmt->bind_param("i", $bus_id))){
            echo "<p>Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        //Execute statement
        if(!($stmt->execute())){ 
            echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
        }
        

        $name;
        $info;
        $phone;
        $website;
        $street;
        $city;
        $state;
        $zip;
        $hours;
        if(!($stmt->bind_result($name, $info, $phone, $website, $street, $city, $state, $zip, $hours))){
            echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        if(!($stmt->fetch())){
            echo "<p>Fetching output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }

        //Close statement
        if(!($stmt->close())){
            echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
        }
    } 
    else{
      exit();
    }


?>
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <!--<link rel="icon" href="../../favicon.ico">-->

        <title>Corvallis Reuse and Repair Directory Administration Interface</title>

        <link href="./css/main.css" rel="stylesheet">

        <!-- Bootstrap core CSS -->
        <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap theme -->
        <link href="../dist/css/bootstrap-theme.min.css" rel="stylesheet">

        <!-- Custom styles for this template 
        <link href="theme.css" rel="stylesheet">-->

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

      </head>

      <body role="document">

        <!-- Fixed navbar -->
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="home.php">Home</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <li><a href="home.php#businesses">Businesses</a></li>
                <li><a href="home.php#items">Items</a></li>
                <li><a href="home.php#categories">Categories</a></li>
                <!--<li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Action</a></li>
                    <li><a href="#">Another action</a></li>
                    <li><a href="#">Something else here</a></li>
                    <li class="divider"></li>
                    <li class="dropdown-header">Nav header</li>
                    <li><a href="#">Separated link</a></li>
                    <li><a href="#">One more separated link</a></li>
                  </ul>
                </li>-->
              </ul>
              <ul class="nav navbar-nav" style="float: right;">
                <li><a href="./account.php">Account</a></li>
                <li><a href="./controls/db_logout.php?action=end">Log out</a></li>
              </ul>
            </div><!--/.nav-collapse -->
          </div>
        </nav>

        <div class="heading">
          <div class="image-spacer"></div>
          <div class="image-heading">
            <a href="home.php"><img src="../img/sustainablefeet.jpg" class="img-thumbnail image-square" alt="Corvallis Sustainability Coalition Waste Prevention Action Team"></a>
          </div>
          <div class="container">
           <!-- <img src="../img/sustainablecorvallis.png" class="img-thumbnail rectangle" alt="Sustainable Corvallis">-->
            <h1>Corvallis Reuse and Repair Directory</h1>
            <p>Administration Interface</p>
          </div>
        </div>

        <div class="container theme-showcase main" role="main">
        <div id="businesses" class="marker"></div>
          <div class="page-header">
            <h1>Edit Business</h1>
          </div>
          <form class="form-horizontal" id="editBusiness">
            <div class="form-group">
              <label for="bus-name" class="col-sm-2 control-label">Name</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="bus-name" placeholder="Business Name" value=<?php echo "\"" . $name . "\"" ?> required>
              </div>
            </div>

            <div class="form-group">
              <label for="bus-street" class="col-sm-2 control-label">Street</label>
              <div class="col-sm-10">

      <?php     if($street !== "") {?>
                    <input type="text" class="form-control" id="bus-street" placeholder="Street Address" value=<?php echo "\"" . $street . "\""?>>
      <?php     }
                else {?>
                    <input type="text" class="form-control" id="bus-street" placeholder="Street Address">
      <?php     }?>

              </div>
            </div>

            <div class="form-group col-sm-6">
              <label for="bus-city" class="col-sm-4 control-label">City</label>
              <div class="col-sm-8">

      <?php     if($city !== "") {?>
                    <input type="text" class="form-control" id="bus-city" placeholder="City" value=<?php echo "\"" . $city . "\""?>>
      <?php     }
                else {?> 
                    <input type="text" class="form-control" id="bus-city" placeholder="City">
      <?php     }?>   
                             
              </div>
            </div>

            <div class="form-group col-sm-2" style="margin-left: 22px;">
              <label for="bus-state" class="col-sm-4 control-label">State</label>
              <div class="col-sm-8">
                <select id="bus-state" class="form-control" required>
   
<!-- THERE MUST BE A BETTER WAY....   HELP ROB!-->
    <?php
                if($state == "AL"){          
    ?>    
                    <option value="AL" selected>AL
    <?php
                }
                else{
?>    
                    <option value="AL">AL
    <?php       } 
                if($state == "AK"){          
    ?>    
                    <option value="AK" selected>AK
    <?php
                }
                else{
?>    
                    <option value="AK">AK
    <?php       }
                if($state == "AZ"){          
    ?>    
                    <option value="AZ" selected>AZ
    <?php
                }
                else{
?>    
                    <option value="AZ">AZ
    <?php       } 
                if($state == "AR"){          
    ?>    
                    <option value="AR" selected>AR
    <?php
                }
                else{
?>    
                    <option value="AR">AR
    <?php       }
                if($state == "CA"){          
    ?>    
                    <option value="CA" selected>CA
    <?php
                }
                else{
?>    
                    <option value="CA">CA
    <?php       } 
                if($state == "CO"){          
    ?>    
                    <option value="CO" selected>CO
    <?php
                }
                else{
?>    CO
                    <option value="CO">CO
    <?php       } 
                if($state == "CT"){          
    ?>    
                    <option value="CT" selected>CT
    <?php
                }
                else{
?>    
                    <option value="CT">CT
    <?php       } 
                if($state == "DE"){          
    ?>    
                    <option value="DE" selected>DE
    <?php
                }
                else{
?>    
                    <option value="DE">DE
    <?php       } 
                if($state == "FL"){          
    ?>    
                    <option value="FL" selected>FL
    <?php
                }
                else{
?>    
                    <option value="FL">FL
    <?php       } 
                if($state == "GA"){          
    ?>    
                    <option value="GA" selected>GA
    <?php
                }
                else{
?>    
                    <option value="GA">GA
    <?php       } 
                if($state == "HI"){          
    ?>    
                    <option value="HI" selected>HI
    <?php
                }
                else{
?>    
                    <option value="HI">HI
    <?php       } 
                if($state == "ID"){          
    ?>    
                    <option value="ID" selected>ID
    <?php
                }
                else{
?>    
                    <option value="ID">ID
    <?php       } 
                if($state == "IL"){          
    ?>    
                    <option value="IL" selected>IL
    <?php
                }
                else{
?>    
                    <option value="IL">IL
    <?php       } 
                if($state == "IN"){          
    ?>    
                    <option value="IN" selected>IN
    <?php
                }
                else{
?>    
                    <option value="IN">IN
    <?php       } 
                if($state == "IA"){          
    ?>    
                    <option value="IA" selected>IA
    <?php
                }
                else{
?>    
                    <option value="IA">IA
    <?php       } 
                if($state == "KS"){          
    ?>    
                    <option value="KS" selected>KS
    <?php
                }
                else{
?>    
                    <option value="KS">KS
    <?php       } 
                if($state == "KY"){          
    ?>    
                    <option value="KY" selected>KY
    <?php
                }
                else{
?>    
                    <option value="KY">KY
    <?php       } 
                if($state == "LA"){          
    ?>    
                    <option value="LA" selected>LA
    <?php
                }
                else{
?>    
                    <option value="LA">LA
    <?php       } 
                if($state == "ME"){          
    ?>    
                    <option value="ME" selected>ME
    <?php
                }
                else{
?>    
                    <option value="ME">ME
    <?php       } 
                if($state == "MD"){          
    ?>    
                    <option value="MD" selected>MD
    <?php
                }
                else{
?>    
                    <option value="MD">MD
    <?php       } 
                if($state == "MA"){          
    ?>    
                    <option value="MA" selected>MA
    <?php
                }
                else{
?>    
                    <option value="MA">MA
    <?php       } 
                if($state == "MI"){          
    ?>    
                    <option value="MI" selected>MI
    <?php
                }
                else{
?>    
                    <option value="MI">MI
    <?php       } 
                if($state == "MN"){          
    ?>    
                    <option value="MN" selected>MN
    <?php
                }
                else{
?>    
                    <option value="MN">MN
    <?php       } 
                if($state == "MS"){          
    ?>    
                    <option value="MS" selected>MS
    <?php
                }
                else{
?>    
                    <option value="MS">MS
    <?php       } 
                if($state == "MO"){          
    ?>    
                    <option value="MO" selected>MO
    <?php
                }
                else{
?>    
                    <option value="MO">MO
    <?php       } 
                if($state == "MT"){          
    ?>    
                    <option value="MT" selected>MT
    <?php
                }
                else{
?>    
                    <option value="MT">MT
    <?php       } 
                if($state == "NE"){          
    ?>    
                    <option value="NE" selected>NE
    <?php
                }
                else{
?>    
                    <option value="NE">NE
    <?php       } 
                if($state == "NV"){          
    ?>    
                    <option value="NV" selected>NV
    <?php
                }
                else{
?>    
                    <option value="NV">NV
    <?php       } 
                if($state == "NH"){          
    ?>    
                    <option value="NH" selected>NH
    <?php
                }
                else{
?>    
                    <option value="NH">NH
    <?php       } 
                if($state == "NJ"){          
    ?>    
                    <option value="NJ" selected>NJ
    <?php
                }
                else{
?>    
                    <option value="NJ">NJ
    <?php       } 
                if($state == "NM"){          
    ?>    
                    <option value="NM" selected>NM
    <?php
                }
                else{
?>    
                    <option value="NM">NM
    <?php       } 
                if($state == "NY"){          
    ?>    
                    <option value="NY" selected>NY
    <?php
                }
                else{
?>    
                    <option value="NY">NY
    <?php       } 
                if($state == "NC"){          
    ?>    
                    <option value="NC" selected>NC
    <?php
                }
                else{
?>    
                    <option value="NC">NC
    <?php       }
                if($state == "ND"){          
    ?>    
                    <option value="ND" selected>ND
    <?php
                }
                else{
?>    
                    <option value="ND">ND
    <?php       }
                if($state == "OH"){          
    ?>    
                    <option value="OH" selected>OH
    <?php
                }
                else{
?>    
                    <option value="OH">OH
    <?php       }
                if($state == "OK"){          
    ?>    
                    <option value="OK" selected>OK
    <?php
                }
                else{
?>    
                    <option value="OK">OK
    <?php       }
                if($state == "OR"){          
    ?>    
                    <option value="OR" selected>OR
    <?php
                }
                else{
?>    
                    <option value="OR">OR
    <?php       }
                if($state == "PA"){          
    ?>    
                    <option value="PA" selected>PA
    <?php
                }
                else{
?>    
                    <option value="PA">PA
    <?php       }
                if($state == "RI"){          
    ?>    
                    <option value="RI" selected>RI
    <?php
                }
                else{
?>    
                    <option value="RI">RI
    <?php       }
                if($state == "SC"){          
    ?>    
                    <option value="SC" selected>SC
    <?php
                }
                else{
?>    
                    <option value="SC">SC
    <?php       }
                if($state == "SD"){          
    ?>    
                    <option value="SD" selected>SD
    <?php
                }
                else{
?>    
                    <option value="SD">SD
    <?php       }
                if($state == "TN"){          
    ?>    
                    <option value="TN" selected>TN
    <?php
                }
                else{
?>    
                    <option value="TN">TN
    <?php       }
                if($state == "TX"){          
    ?>    
                    <option value="TX" selected>TX
    <?php
                }
                else{
?>    
                    <option value="TX">TX
    <?php       }
                if($state == "UT"){          
    ?>    
                    <option value="UT" selected>UT
    <?php
                }
                else{
?>    
                    <option value="UT">UT
    <?php       }
                if($state == "VT"){          
    ?>    
                    <option value="VT" selected>VT
    <?php
                }
                else{
?>    
                    <option value="VT">VT
    <?php       }
                if($state == "VA"){          
    ?>    
                    <option value="VA" selected>VA
    <?php
                }
                else{
?>    
                    <option value="VA">VA
    <?php       }
                if($state == "WA"){          
    ?>    
                    <option value="WA" selected>WA
    <?php
                }
                else{
?>    
                    <option value="WA">WA
    <?php       }
                if($state == "WV"){          
    ?>    
                    <option value="WV" selected>WV
    <?php
                }
                else{
?>    
                    <option value="WV">WV
    <?php       }
                if($state == "WI"){          
    ?>    
                    <option value="WI" selected>WI
    <?php
                }
                else{
?>    
                    <option value="WI">WI
    <?php       }
                if($state == "WY"){          
    ?>    
                    <option value="WY" selected>WY
    <?php
                }
                else{
?>    
                    <option value="WY">WY
    <?php       }
    ?>
                </select>
              </div>
            </div>

            <div class="form-group col-sm-4" style="margin-left: -23px;">
              <label for="bus-phone" class="col-sm-6 control-label">Zip-Code</label>
              <div class="col-sm-6">

      <?php     if($zip !== "") {?>
                    <input type="text" class="form-control" id="bus-zip" placeholder="Zip-Code" value=<?php echo "\"" . $zip . "\""?>>
      <?php     }
                else {?> 
                    <input type="text" class="form-control" id="bus-zip" placeholder="Zip-Code">
      <?php     }?>  

              </div>
            </div>      

            <div class="form-group col-sm-6">
              <label for="bus-phone" class="col-sm-4 control-label">Phone</label>
              <div class="col-sm-8">

      <?php     if($phone !== "") {?>
                    <input type="text" class="form-control" id="bus-phone" placeholder="Phone Number" value=<?php echo "\"" . $phone . "\""?>>
      <?php     }
                else {?> 
                    <input type="text" class="form-control" id="bus-phone" placeholder="Phone Number">
      <?php     }?>  

              </div>
            </div>

            <div class="form-group col-sm-6">
              <label for="bus-website" class="col-sm-2 control-label">Website</label>
              <div class="col-sm-10">

      <?php     if($website !== "") {?>
                    <input type="text" class="form-control" id="bus-website" placeholder="Website" value=<?php echo "\"" . $website . "\""?>>
      <?php     }
                else {?> 
                    <input type="text" class="form-control" id="bus-website" placeholder="Website">
      <?php     }?> 

              </div>
            </div>

            <div class="form-group">
              <label for="bus-name" class="col-sm-2 control-label">Hours</label>
              <div class="col-sm-10">

    <?php       if($hours !== "") {?>
                    <input type="text" class="form-control" id="bus-hours" placeholder="Hours Open" value=<?php echo "\"" . $hours . "\""?>>
    <?php        }
                else {?> 
                    <input type="text" class="form-control" id="bus-hours" placeholder="Hours Open">
      <?php     }?> 

                
              </div>
            </div>

            <div class="form-group">
              <label for="bus-info" class="col-sm-2 control-label">Info</label>
              <div class="col-sm-10">

      <?php     if($info !== "") {?>
                    <textarea class="form-control" id="bus-info" rows="4" placeholder="Any extra information..."><?php echo $info; ?></textarea>
      <?php     }
                else {?> 
                    <textarea class="form-control" id="bus-info" rows="4" placeholder="Any extra information..."></textarea>
      <?php     }?> 

              </div>
            </div>

            <div class="form-group">
              <label for="bus-cat" class="col-sm-2 control-label">Categories</label>
              <div class="col-sm-10 selection">
    <?php

                //Get the item ids for this business
                //Prepare a statement
                if(!($stmt = $mysqli->prepare("SELECT content_id, reuse, repair FROM company_content WHERE company_id=?"))){
                    echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
                }           

                //Bind variables
                if(!($stmt->bind_param("i", $bus_id))){
                    echo "<p>Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                }

                //Execute statement
                if(!($stmt->execute())){ 
                    echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
                }
                
                $items;
                $reuse;
                $repair;
                if(!($stmt->bind_result($items, $reuse, $repair))){
                    echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
                }

                //Fetch results and build array
                $selected_items = array();
                $item_specifics = array();

                while($stmt->fetch()){ 
                    array_push($item_specifics, $items);
                    array_push($item_specifics, $reuse);
                    array_push($item_specifics, $repair);
                    array_push($selected_items, $item_specifics);
                    $item_specifics = array();
                }

                //Close statement
                if(!($stmt->close())){
                    echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
                }


            //Prepare a statement
            if(!($stmt = $mysqli->prepare("SELECT id, name FROM categories ORDER BY name"))){
                echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }           

            //Execute statement
            if(!($stmt->execute())){ 
                echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
            }
            
            $cat_id;
            $cat_name;

            if(!($stmt->bind_result($cat_id, $cat_name))){
                echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            //Fetch results        
            while($stmt->fetch()){ 
  ?>          <div class="check-box-grouping">          
                <div class="checkbox">
                  <label><input type="checkbox" class="category-checkbox" value=<?php echo "\"" . $cat_id . "\""?>><strong><?php echo $cat_name?></strong></label>
                </div>
                <div class="items-grouping">
  <?php
                  //Prepare a statement
                  if(!($stmt2 = $mysqli2->prepare("SELECT id, name FROM contents WHERE cat_id = ? ORDER BY name"))){
                      echo "<p>Prepare failed: (" . $mysqli2->errno . ") " . $mysqli2->error;
                  }           

                  //Bind variables
                  if(!($stmt2->bind_param("i", $cat_id))){
                      echo "<p>Binding parameters failed: (" . $stmt2->errno . ") " . $stmt2->error;
                  }

                  //Execute statement
                  if(!($stmt2->execute())){ 
                      echo "<p>Execute failed: (" . $stmt2->errno . ") " . $stmt2->error; 
                  }
                  
                  $item_id;
                  $item_name;

                  if(!($stmt2->bind_result($item_id, $item_name))){
                      echo "<p>Binding output parameters failed: (" . $stmt2->errno . ") " . $stmt2->error;
                  }

                    //Fetch results  
                    $item;   
                    $count = 0;   
                    while($stmt2->fetch()){   
                        foreach($selected_items as $item) {
                                if($item_id == $item[0]) { ?>    
                                    <div class="checkbox inline-item">
                                      <label class="col-sm-5"><input type="checkbox" class="item-checkbox" value=<?php echo "\"" . $item_id . "\""?> checked><?php echo $item_name?></label>

            <?php                       if($item[1] == 1){?>
                                            <label class="col-sm-1"><input type="checkbox" class="item-resell-checkbox" value=<?php echo "\"" . $item_id . "\""?> checked>Reuse</label>
            <?php                       } else {?>
                                            <label class="col-sm-1"><input type="checkbox" class="item-resell-checkbox" value=<?php echo "\"" . $item_id . "\""?>>Reuse</label>
            <?php                       }

                                        if($item[2] == 1){?>
                                            <label class="col-sm-1"><input type="checkbox" class="item-repair-checkbox" value=<?php echo "\"" . $item_id . "\""?> checked>Repair</label>
            <?php                       } else {?>
                                            <label class="col-sm-1"><input type="checkbox" class="item-repair-checkbox" value=<?php echo "\"" . $item_id . "\""?>>Repair</label>
            <?php                       }
            ?>
                                    </div>
               <?php                $count++;
                                    //break;
                                }
                        }
                        if($count == 0){?>
                                <div class="checkbox inline-item">
                                  <label class="col-sm-5"><input type="checkbox" class="item-checkbox" value=<?php echo "\"" . $item_id . "\""?>><?php echo $item_name?></label>
                                  <label class="col-sm-1"><input type="checkbox" class="item-resell-checkbox" value=<?php echo "\"" . $item_id . "\""?>>Reuse</label>
                                  <label class="col-sm-1"><input type="checkbox" class="item-repair-checkbox" value=<?php echo "\"" . $item_id . "\""?>>Repair</label>
                                </div>
            <?php             
                        }
                        $count = 0;
                    }
                  
                  //Close statement
                  if(!($stmt2->close())){
                      echo "<p>Close failed: (" . $stmt2->errno . ") " . $stmt2->error;
                  }

?>              </div>
              </div>
              <br/>
<?php
            }

            //Close statement
            if(!($stmt->close())){
                echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
            }
?>



              </div>
            </div>
            <div class="form-group col-sm-3"  style="margin-left: 40px;">
              <div class="col-sm-offset-6 col-sm-6">
                <input type="button" id="edit-item" class="btn btn-success" value="Edit Business" style="width: 113px;" onclick=<?php echo "\"editBusiness(" . $bus_id . ")\""?>>
              </div>
            </div>
            <div class="form-group col-sm-2">
              <div class="col-sm-12">
                <a href="home.php#businesses" class="btn btn-default" style="width: 113px;">Back</a>
              </div>
            </div>
            <div class="form-group col-sm-6" id="result">
              
            </div>
          </form>
        </div>

        <script src="./js/jquery-2.1.3.js"></script>

        <script src="../dist/js/bootstrap.js"></script>

        <script src="./js/main.js"></script>
      </body>
    </html>


<?php
}

    if(!($mysqli->close())){
        echo "<p>Close failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
?>
