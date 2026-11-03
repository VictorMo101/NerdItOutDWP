<html>
<head>
<meta charset="UTF-8" />
</head>
<body>
<h1>
    Welcome, <?php echo htmlspecialchars($username); ?>
</h1>
<form action="index.php?page=logout" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
    <button type="submit">Logout</button>
</form>
</body>
</html>
