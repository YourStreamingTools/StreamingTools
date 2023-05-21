<?php
// Initialize the session
session_start();

// Check if the user is already logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
} else {
    // Require database connection
    require_once "db_connect.php";

    // Get user information from the database
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT username, signup_date, last_login, api_key, profile_image FROM users WHERE id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $user_id);
        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows == 1){
                $stmt->bind_result($username, $signup_date, $last_login, $api_key, $twitch_profile_image_url);
                $stmt->fetch();
                $_SESSION['signup_date'] = $signup_date;
                $_SESSION['last_login'] = $last_login;
                $_SESSION['api_key'] = $api_key;
                $_SESSION['profile_image'] = $twitch_profile_image_url;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
                exit;
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>YourStreamingTools - Profile</title>
  <link rel="icon" href="https://cdn.yourstreaming.tools/img/logo.ico" type="image/png" />
  <link rel="apple-touch-icon" href="https://cdn.yourstreaming.tools/img/logo.ico">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/about.js"></script>
  <script src="js/profile.js"></script>
  <style type="text/css">
    body {
      font: 14px sans-serif;
    }
    .wrapper {
      width: 350px; padding: 20px;
    }
    a.popup-link {
      text-decoration: none;
      color: black;
      cursor: pointer;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php">YourStreamingTools</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="profile.php">View Profile</a></li>
			<li><a href="update_profile.php">Update Profile</a></li>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourStreamingTools. All rights reserved.</a></p>
    </div>
</nav>
<div class="col-md-6">
    <h1>Your Profile</h1>
    <img src="<?php echo $twitch_profile_image_url; ?>" width="150px" height="150px" alt="Twitch Profile Image for <?php echo $_SESSION['username']; ?>">
    <br><br>
    <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
    <p><strong>Joined:</strong> <?php echo date('F j, Y', strtotime($_SESSION['signup_date'])); ?> (AET)</p>
    <p><strong>Last Login:</strong> <?php echo date('F j, Y', strtotime($_SESSION['last_login'])); ?> at <?php echo date('g:i A', strtotime($last_login)); ?> (AET)</p>
    <p><strong>API Key:</strong> <span class="api-key-wrapper" style="display: none;"><?php echo $api_key; ?></span></p>
    <button type="button" class="btn btn-primary" id="show-api-key">Show API Key</button>
    <button type="button" class="btn btn-primary" id="hide-api-key" style="display:none;">Hide API Key</button>
    <br><br>
    <!--<a href="change_password.php" class="btn btn-primary">Change Password</a>-->
    <a href="logout.php" class="btn btn-primary">Logout</a>
</div>
</body>
</html>