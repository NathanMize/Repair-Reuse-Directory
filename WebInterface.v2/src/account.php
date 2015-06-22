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
      <div class="account-group" style="padding-bottom: 50px;">
        <div class="page-header">
          <h1>Account Information</h1>
        </div>
        <div class="col-sm-2">
          <p style="float: right;"><strong>Username</strong></p>
        </div> 
        <div class="col-sm-10" id="result-username-update">
          <p><?php echo $_SESSION['username'];?></p>
        </div>
        <div class="col-sm-2">
          <p style="float: right;"><strong>Permission Level</strong></p>
        </div> 
        <div class="col-sm-10">
          <p><?php 

              //Prepare a statement
              if(!($stmt = $mysqli->prepare("SELECT permissions FROM users WHERE username = ?"))){
                  echo "<p>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
              }           

              //Bind variables
              if(!($stmt->bind_param("s", $_SESSION['username']))){
                  echo "<p>Binding parameters failed: (" . $stmt2->errno . ") " . $stmt2->error;
              }

              //Execute statement
              if(!($stmt->execute())){ 
                  echo "<p>Execute failed: (" . $stmt->errno . ") " . $stmt->error; 
              }
              
              $permission;

              if(!($stmt->bind_result($permission))){
                  echo "<p>Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
              }

              //Fetch results        
              if(!($stmt->fetch())){ 
                  echo "<p>Fetching results failed: (" . $stmt->errno . ") " . $stmt->error;
              }

              //Close statement
              if(!($stmt->close())){
                  echo "<p>Close failed: (" . $stmt->errno . ") " . $stmt->error;
              }

              if($permission === 0){
                  echo "Administrator";
              } else {
                  echo "Moderator";
              }
        ?></p>
        </div>
      </div>

      <div class="account-group">
        <div class="page-header">
          <h1>Change Username</h1>
        </div>
        <form class="form-horizontal" id="newUsername">
          <div class="form-group">
            <label for="username" class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="new-username" placeholder="New Username" required>
            </div>
          </div>
          <div class="button-grouping">
            <div class="col-sm-4" style="margin-left: -15px;">
              <div class="col-sm-offset-6 col-sm-6">
                <input type="button" id="change-username" class="btn btn-success" value="Change Username" onclick="changeUsername()" style="margin-left: 5px;">
              </div><br>
            </div>
            <div class="form-group col-sm-6" id="result-username">
              
            </div>
          </div>
        </form>
      </div><br/>

      <div class="account-group">
        <div class="page-header">
          <h1>Change Password</h1>
        </div>
        <form class="form-horizontal" id="newPassword">
          <div class="form-group">
            <label for="password1" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="password1" placeholder="New Password">
            </div>
          </div>
          <div class="form-group">
            <label for="password2" class="col-sm-2 control-label">Retype Password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="password2" placeholder="Retype New Password">
            </div>
          </div>
          <div class="button-grouping">
            <div class="col-sm-4" style="margin-left: -15px;">
              <div class="col-sm-offset-6 col-sm-6">
                <input type="button" id="change-password" class="btn btn-success" value="Change Password" onclick="changePassword()" style="margin-left: 5px;">
              </div>
            </div>
            <div class="form-group col-sm-6" id="result-password">
              
            </div>
          </div>
        </form>
      </div>

    </div>

    <div class="modal fade" id="default-login">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">First time logging in?</h4>
          </div>
          <div class="modal-body">
            <p>Make sure to change your username and password from the default 'administrator'.</p>
          </div>
          <div class="modal-footer">
            <button type="button" id="confirm" class="btn btn-default btn-warning col-sm-2" style="float: right;" data-dismiss="modal">Ok</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script src="./js/jquery-2.1.3.js"></script>

    <script src="../dist/js/bootstrap.js"></script>

    <script src="./js/main.js"></script>

    <?php  
    if(($_SESSION['default']) == true){
?>
    <script>
        $(window).load(function(){
            $('#default-login').modal('show');
        });
    </script>

<?php
    }
?>

    <script>
      //call appropriate function when pressing enter
      $('#new-username').keypress(function(e){
          if(e.which === 13){
              e.preventDefault();
              changeUsername();
          }
      });
      $('#password1').keypress(function(e){
          if(e.which === 13){
              changePassword();
          }
      });
      $('#password2').keypress(function(e){
          if(e.which === 13){
              changePassword();
          }
      });
    </script>
  </body>
</html>