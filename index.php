<?php 
require_once("includes/session.php"); 
require_once("includes/connection.php"); 
require_once("includes/functions.php"); 
confirm_logged_in(); 

$username = htmlspecialchars($_SESSION['user'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
</head>

<body>
<h1 align="center">
    Welcome, <?php echo $username; ?>
</h1>
<a href="logout.php">Logout</a>
</body>
</html>