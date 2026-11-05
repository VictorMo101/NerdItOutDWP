<html>
<head>
<meta charset="UTF-8" />
</head>
<body>
<h1>
    Welcome, <?php echo htmlspecialchars($username); ?>
</h1>
<?php if ($message !== ""): ?>
    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>
<form action="index.php?page=create_post" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
    <label for="post-text">Create a post</label>
    <textarea id="post-text" name="text" maxlength="280" required></textarea>
    <button type="submit">Post</button>
</form>
<section>
    <h2>Posts</h2>
    <?php if (empty($posts)): ?>
        <p>No posts yet.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <article>
                <h3>
                    <?php echo htmlspecialchars($post['displayName'] ?: $post['username'], ENT_QUOTES, 'UTF-8'); ?>
                </h3>
                <p><?php echo nl2br(htmlspecialchars($post['text'], ENT_QUOTES, 'UTF-8')); ?></p>
                <small><?php echo htmlspecialchars($post['createdAt'], ENT_QUOTES, 'UTF-8'); ?></small>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<form action="index.php?page=logout" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
    <button type="submit">Logout</button>
</form>
</body>
</html>
