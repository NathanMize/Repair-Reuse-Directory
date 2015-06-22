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

else {

    if(isset($_GET['id'])){
        $id = $_GET['id'];

        //Prepare a statement
        if(!($stmt = $mysqli->prepare("SELECT name FROM categories WHERE id=?"))){
            echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }           

        //Bind variables
        if(!($stmt->bind_param("i", $id))){
            echo "<p>Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }


        //Execute statement
        if(!($stmt->execute())){ 
            echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
        }
        

        $name;
        if(!($stmt->bind_result($name))){
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

        <div class="container theme-showcase" role="main">
        <div id="businesses" class="marker"></div>
          <div class="page-header">
            <h1>Edit Category</h1>
          </div>
          <form class="form-horizontal" id="editCategory">
            <div class="form-group">
              <label for="cat-name" class="col-sm-2 control-label">Name</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="cat-name" placeholder="Category Name" value=<?php echo "\"" . $name . "\"" ?> required>
              </div>
            </div>
            <div class="form-group">
              <label for="cat-name" class="col-sm-2 control-label">Items</label>
              <div class="col-sm-10">
                <a href="./home.php#items" class="btn btn-default" style="width: 113px;">Go to Items</a>
              </div>
            </div>
            <div class="form-group col-sm-3"  style="margin-left: 40px;">
              <div class="col-sm-offset-6 col-sm-6">
                <input type="button" id="edit-category" class="btn btn-success" value="Edit Category" style="width: 113px;" onclick=<?php echo "\"editCategory(" . $id . ")\""?>>
              </div>
            </div>
            <div class="form-group col-sm-2">
              <div class="col-sm-12">
                <a href="home.php#categories" class="btn btn-default" style="width: 113px;">Back</a>
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
