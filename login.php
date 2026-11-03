<?php
require_once("includes/connection.php");
require_once("includes/session.php");
require_once("includes/functions.php");

if (logged_in()) {
	redirect_to("index.php");
}


if (isset($_POST['submit'])) { 
	$username = trim($_POST['user']);
	$password = trim($_POST['pass']);
	
	try {
		$query = "SELECT userID, username, password FROM Users WHERE username = :username LIMIT 1";
		$stmt = $connection->prepare($query);
		
		$stmt->bindParam(':username', $username);
		$stmt->execute();
		
		$found_user = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if ($found_user) {
			if (password_verify($password, $found_user['password'])) {
				$_SESSION['user_id'] = $found_user['userID'];
				$_SESSION['user'] = $found_user['username'];
				redirect_to("index.php");
			} else {
				$message = "Username/password combination incorrect.<br />
				Please make sure your caps lock key is off and try again.";
			}
		} else {
			$message = "Username/password combination incorrect.<br />
			Please make sure your caps lock key is off and try again.";
		}
	} catch (PDOException $e) {
		die("Database query failed: " . $e->getMessage());
	}
} elseif (isset($_GET['logout']) && $_GET['logout'] == 1) {
	$message = "You are now logged out.";
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
</head>
<body>
<?php
if (!empty($message)) {
	echo "<p>" . $message . "</p>";
}
?>
<h2>Please login</h2>
<form action="" method="post">
Username:
<input type="text" name="user" maxlength="30" value="" />
Password:
<input type="password" name="pass" maxlength="30" value="" />
<input type="submit" name="submit" value="Login" />
</form>
<a href="register.php">register</a>


</body>
</html>
<?php
if (isset($connection)){$connection = null;}
?>