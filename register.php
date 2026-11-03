<?php
require_once("includes/session.php");
require_once("includes/connection.php");
require_once("includes/functions.php");
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
</head>

<?php
if (isset($_POST['submit'])) { 

	$username = trim($_POST['user']);
	$email = trim($_POST['email']);
	$display_name = trim($_POST['displayName']);
	$password = trim($_POST['pass']);
	
    $iterations = ['cost' => 15];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, $iterations);

    try {
        $query = "INSERT INTO Users (username, email, password, displayName)
                  VALUES (:username, :email, :hashed_password, :display_name)";
        $stmt = $connection->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':hashed_password', $hashed_password);
        $stmt->bindParam(':display_name', $display_name);

        $result = $stmt->execute();

        if ($result) {
            $message = "User Created.";
        } else {
            $message = "User could not be created.";
        }
    } catch (PDOException $e) {
        $errorInfo = $e->errorInfo;

        if ($e->getCode() === '23000' && isset($errorInfo[1]) && (int) $errorInfo[1] === 1062) {
            $message = "That username is already in use. Please choose another one.";
        } else {
            $message = "User could not be created. Please try again.";
        }
    }
}

if (!empty($message)) {
    echo "<p>" . htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</p>";
}
?>
<h2>Create New User</h2>

<form action="" method="post">
Username:
<input type="text" name="user" maxlength="30" value="" />
Email:
<input type="email" name="email" maxlength="80" value="" />
Display name:
<input type="text" name="displayName" maxlength="80" value="" />
Password:
<input type="password" name="pass" maxlength="30" value="" />
<input type="submit" name="submit" value="Create" />
</form>

<a href="login.php">login</a>

</body>
</html>
<?php
if (isset($connection)){$connection = null;}
?>