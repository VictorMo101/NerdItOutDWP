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
<h2>Create New User</h2>
<form action="index.php?page=register" method="post">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
Username:
<input type="text" name="user" maxlength="30" />
Email:
<input type="email" name="email" maxlength="80" />
Display name:
<input type="text" name="displayName" maxlength="80" />
Password:
<input type="password" name="pass" maxlength="72" />
<input type="submit" name="submit" value="Create" />
</form>
<a href="index.php?page=login">login</a>
</body>
</html>
