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
        <h1>Add New Business</h1>
      </div>
      <form class="form-horizontal" id="addBusiness">
        <div class="form-group">
          <label for="bus-name" class="col-sm-2 control-label">Name</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="bus-name" placeholder="Business Name" required>
          </div>
        </div>
        <div class="form-group">
          <label for="bus-street" class="col-sm-2 control-label">Street</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="bus-street" placeholder="Street Address">
          </div>
        </div>

        <div class="form-group col-sm-6">
          <label for="bus-city" class="col-sm-4 control-label">City</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="bus-city" placeholder="City">
          </div>
        </div>
        <div class="form-group col-sm-2" style="margin-left: 22px;">
          <label for="bus-state" class="col-sm-4 control-label">State</label>
          <div class="col-sm-8">
            <select id="bus-state" class="form-control" required>
              <option value="AL">AL
              <option value="AK">AK
              <option value="AZ">AZ
              <option value="AR">AR
              <option value="CA">CA
              <option value="CO">CO
              <option value="CT">CT
              <option value="DE">DE
              <option value="FL">FL
              <option value="GA">GA
              <option value="HI">HI
              <option value="ID">ID
              <option value="IL">IL
              <option value="IN">IN
              <option value="IA">IA
              <option value="KS">KS
              <option value="KY">KY
              <option value="LA">LA
              <option value="ME">ME
              <option value="MD">MD
              <option value="MA">MA
              <option value="MI">MI
              <option value="MN">MN
              <option value="MS">MS
              <option value="MO">MO
              <option value="MT">MT
              <option value="NE">NE
              <option value="NV">NV
              <option value="NH">NH
              <option value="NJ">NJ
              <option value="NM">NM
              <option value="NY">NY
              <option value="NC">NC
              <option value="ND">ND
              <option value="OH">OH
              <option value="OK">OK
              <option selected value="OR">OR
              <option value="PA">PA
              <option value="RI">RI
              <option value="SC">SC
              <option value="SD">SD
              <option value="TN">TN
              <option value="TX">TX
              <option value="UT">UT
              <option value="VT">VT
              <option value="VA">VA
              <option value="WA">WA
              <option value="WV">WV
              <option value="WI">WI
              <option value="WY">WY
            </select>
          </div>
        </div>
        <div class="form-group col-sm-4" style="margin-left: -23px;">
          <label for="bus-phone" class="col-sm-6 control-label">Zip-Code</label>
          <div class="col-sm-6">
            <input type="text" class="form-control" id="bus-zip" placeholder="Zip-Code">
          </div>
        </div>      

        <div class="form-group col-sm-6">
          <label for="bus-phone" class="col-sm-4 control-label">Phone</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" id="bus-phone" placeholder="Phone Number">
          </div>
        </div>
        <div class="form-group col-sm-6">
          <label for="bus-website" class="col-sm-2 control-label">Website</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="bus-website" placeholder="Website">
          </div>
        </div>
        <div class="form-group">
          <label for="bus-name" class="col-sm-2 control-label">Hours</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="bus-hours" placeholder="Hours Open">
          </div>
        </div>
        <div class="form-group">
          <label for="bus-info" class="col-sm-2 control-label">Info</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="bus-info" rows="4"placeholder="Any extra information..."></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="bus-cat" class="col-sm-2 control-label">Categories / Items</label>
          <div class="col-sm-10 selection">

<?php
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
                  while($stmt2->fetch()){           
  ?>                <div class="checkbox inline-item">
                      <label class="col-sm-5"><input type="checkbox" class="item-checkbox" value=<?php echo "\"" . $item_id . "\""?>><?php echo $item_name?></label>
                      <label class="col-sm-1"><input type="checkbox" class="item-resell-checkbox" value=<?php echo "\"" . $item_id . "\""?>>Reuse</label>
                      <label class="col-sm-1"><input type="checkbox" class="item-repair-checkbox" value=<?php echo "\"" . $item_id . "\""?>>Repair</label>
                    </div>
  <?php
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
        <div class="button-grouping">
          <div class="form-group col-sm-3"  style="margin-left: 40px;">
            <div class="col-sm-offset-6 col-sm-6">
              <input type="button" id="add-business" class="btn btn-success" value="Add Business" style="width: 113px;" onclick="addBusiness()">
            </div>
          </div>
          <div class="form-group col-sm-2">
            <div class="col-sm-12">
              <a href="home.php#businesses" class="btn btn-default" style="width: 113px;">Back</a>
            </div>
          </div>
          <div class="form-group col-sm-6" id="result">
            
          </div>
        </div>
      </form>
    </div>

    <script src="./js/jquery-2.1.3.js"></script>

    <script src="../dist/js/bootstrap.js"></script>

    <script src="./js/main.js"></script>
  </body>
</html>

<?php

    if(!($mysqli->close())){
        echo "<p>Close failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
?>
