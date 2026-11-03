<html>
<head>
<meta charset="UTF-8" />
</head>
<body>
<?php
if (!empty($message)) {
    echo "<p>" . htmlspecialchars($message) . "</p>";
}
?>
<h2>Please login</h2>
<form action="index.php?page=login" method="post">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
Username:
<input type="text" name="user" maxlength="30" />
Password:
<input type="password" name="pass" />
<input type="submit" name="submit" value="Login" />
</form>
<a href="index.php?page=register">register</a>
</body>
</html>
