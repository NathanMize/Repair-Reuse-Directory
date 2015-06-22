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

  <body class="home" role="document">


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
        <h1> Businesses <a href="addBusiness.php" class="btn btn-primary">Add New</a></h1>
      </div>
      <div class="scroll-box">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Items</th>
              <th class="view-edit-del-col"></th>
            </tr>
          </thead>
          <tbody id="business-data">
<?php
            //Prepare a statement
            if(!($stmt = $mysqli->prepare("SELECT id, name FROM companies ORDER BY name"))){
                echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }           

            //Execute statement
            if(!($stmt->execute())){ 
                echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
            }
            
            $id;
            $name;
           
            if(!($stmt->bind_result($id, $name))){
                echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }


            $i = 1;
            //Fetch results        
            while($stmt->fetch()){              
?>    
              <tr>
                <td><?php echo $i++ ?></td>
                <td><?php echo $name ?></td>
                <td><?php 
    
                    
                    if(!($stmt2 = $mysqli2->prepare("SELECT cat.name, cont.name FROM contents cont INNER JOIN company_content cc ON cc.content_id = cont.id INNER JOIN categories cat ON cat.id = cont.cat_id WHERE cc.company_id=? ORDER BY cat.name, cont.name"))){
                        echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
                    }   

                    //Bind variables
                    if(!($stmt2->bind_param("i", $id))){
                        echo "<p>Binding parameters failed: (" . $stmt2->errno . ") " . $stmt2->error;
                    }

                    //Execute statement
                    if(!($stmt2->execute())){ 
                        echo "<p>Execute failed: (" . $stmt2->errno . ") " . $stmt2->error; 
                    }

                    $cat_name;
                    $item_name;
                    if(!($stmt2->bind_result($cat_name, $item_name))){
                        echo "<p>Binding output parameters failed: (" . $stmt2->errno . ") " . $stmt2->error;
                    }

                    $item_string = "";
                    $print_cat_name;

                    if($stmt2->fetch()){
                        $print_cat_name = $cat_name;
                        echo "<strong>" . $cat_name . "</strong><br>";
                        $item_string = $item_string . $item_name . ", ";
                    }

                    while($stmt2->fetch()){
                        if($print_cat_name == $cat_name){
                            $item_string = $item_string . $item_name . ", ";
                        }
                        else{
                            $item_string = rtrim($item_string, ", ");  //strip end comma
                            echo $item_string . "<br/><br/>";

                            $print_cat_name = $cat_name;
                            echo "<strong>" . $cat_name . "</strong><br>";
                            $item_string = "";
                            $item_string = $item_string . $item_name . ", ";
                        }
                    }
                    $item_string = rtrim($item_string, ", ");
                    echo $item_string . "<br/><br/>";

                    //Close statement
                    if(!($stmt2->close())){
                        echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
                    }

                ?></td>
                <td><a href= <?php echo "\"viewBusiness.php?id=" . $id . "\"" ?> class="btn btn-link">Business Info</a>
                    <a href= <?php echo "\"editBusiness.php?id=" . $id . "\"" ?> class="btn btn-link">Edit</a>
                    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#bus-delete-warning" data-name=<?php echo "\"" . $name . "\""?> data-id=<?php echo "\"" . $id . "\"" ?>>Delete</button></td>
              </tr>
<?php
            }

            //Close statement
            if(!($stmt->close())){
                echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
            }
?>
          </tbody>
        </table>
      </div>

      <div id="items" class="marker"></div>
      <div class="page-header">
        <h1 >Items <a href="addItem.php" class="btn btn-primary">Add New</a></h1>
      </div>
      <div class="scroll-box">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Category</th>
              <th class="edit-del-col"></th>
            </tr>
          </thead>
          <tbody id="item-data">
<?php
            //Prepare a statement
            if(!($stmt = $mysqli->prepare("SELECT c.id, c.name, cat.name FROM contents c INNER JOIN categories cat ON cat.id = c.cat_id ORDER BY c.name"))){
                echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }             

            //Execute statement
            if(!($stmt->execute())){ 
                echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
            }
            
            $id;
            $name;
            $category;

            if(!($stmt->bind_result($id, $name, $category))){
                echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            $i = 1;
            //Fetch results        
            while($stmt->fetch()){              
?>    
              <tr>
                <td><?php echo $i++ ?></td>
                <td><?php echo $name ?></td>
                <td><?php echo $category ?></td>
                <td><a href= <?php echo "\"editItem.php?id=" . $id . "\"" ?> class="btn btn-link">Edit</a>
                    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#item-delete-warning" data-name=<?php echo "\"" . $name . "\""?> data-id=<?php echo "\"" . $id . "\"" ?>>Delete</button></td>
              </tr>
<?php
            }

            //Close statement
            if(!($stmt->close())){
                echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
            }
?>
          </tbody>
        </table>
      </div>

      <div id="categories" class="marker"></div>
      <div class="page-header">
        <h1 >Categories <a href="addCategory.php" class="btn btn-primary">Add New</a></h1>
      </div>
      <div class="scroll-box">
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="col-sm-1">#</th>
              <th class="col-sm-4">Name</th>
              <th class="col-sm-5">Items</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="category-data">
<?php
            //Prepare a statement
            if(!($stmt = $mysqli->prepare("SELECT id, name FROM categories ORDER BY name"))){
                echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }             

            //Execute statement
            if(!($stmt->execute())){ 
                echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
            }
            
            $id;
            $name;

            if(!($stmt->bind_result($id, $name))){
                echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
            }

            $i = 1;
            //Fetch results        
            while($stmt->fetch()){              
?>    
              <tr>
                <td><?php echo $i++ ?></td>
                <td><?php echo $name ?></td>
                <td><?php 
    
                    $item_string = "";
                    $stmt2 = $mysqli2->query("SELECT c.name FROM contents c INNER JOIN categories cat ON c.cat_id = cat.id WHERE cat.id= $id");

                    while($row = $stmt2->fetch_assoc()) {
                        $item_string = $item_string . $row['name'] . ", ";
                    }
                    $item_string = rtrim($item_string, ", ");  //strip end comma

                    echo $item_string;
                ?></td>
                <td><a href= <?php echo "\"editCategory.php?id=" . $id . "\"" ?> class="btn btn-link" style="margin-left: 45px;">Edit</a>
                    <button type="button" class="btn btn-link" data-toggle="modal" data-target="#cat-delete-warning" data-name=<?php echo "\"" . $name . "\""?> data-id=<?php echo "\"" . $id . "\"" ?>>Delete</button></td>
              </tr>
<?php
            }

            //Close statement
            if(!($stmt->close())){
                echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
            }
?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal fade" id="bus-delete-warning">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"></h4>
          </div>
          <div class="modal-body">
            <p></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default col-sm-2" data-dismiss="modal">Cancel</button>
            <button type="button" id="bus-delete"></button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="cat-delete-warning">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"></h4>
          </div>
          <div class="modal-body">
            <p></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default col-sm-2" data-dismiss="modal">Cancel</button>
            <button type="button" id="cat-delete"></button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="item-delete-warning">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"></h4>
          </div>
          <div class="modal-body">
            <p></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default col-sm-2" data-dismiss="modal">Cancel</button>
            <button type="button" id="item-delete"></button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script src="./js/jquery-2.1.3.js"></script>

    <script src="../dist/js/bootstrap.js"></script>

    <script src="./js/main.js"></script>
  </body>
</html>


<?php

    if(!($mysqli->close())){
        echo "<p>Close failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if(!($mysqli2->close())){
        echo "<p>Close failed: (" . $mysqli2->errno . ") " . $mysqli2->error;
    }
?>