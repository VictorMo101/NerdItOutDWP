<html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="public/css/home.css">
<script src="public/js/home.js" defer></script>
</head>
<body>
<h1>
    Welcome, <?php echo htmlspecialchars($username); ?>
</h1>
<?php if ($message !== ""): ?>
    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>
<form action="index.php?page=create_post" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
    <label for="post-text">Create a post</label>
    <textarea id="post-text" name="text" maxlength="280" required></textarea>
    <label for="post-image">Add an image</label>
    <input id="post-image" type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" />
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
                <?php if (!empty($post['image'])): ?>
                    <button class="post-image-link" type="button" data-image="<?php echo htmlspecialchars($post['image'], ENT_QUOTES, 'UTF-8'); ?>">
                        <img class="post-image-preview" src="<?php echo htmlspecialchars($post['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="View post image" />
                    </button>
                <?php endif; ?>
                <small><?php echo htmlspecialchars($post['createdAt'], ENT_QUOTES, 'UTF-8'); ?></small>
                <?php if ($post['username'] === $_SESSION['user']): ?>
                    <form action="index.php?page=delete_post" method="post">
                        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['postID'], ENT_QUOTES, 'UTF-8'); ?>" />
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
                        <button type="submit">Delete</button>
                    </form>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<form action="index.php?page=logout" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
    <button type="submit">Logout</button>
</form>
<div class="image-modal" id="image-modal" role="dialog" aria-modal="true" aria-label="Full-size post image">
    <button class="image-modal-close" id="image-modal-close" type="button" aria-label="Close image">&times;</button>
    <img class="image-modal-content" id="image-modal-content" src="" alt="Full-size post image" />
</div>
</body>
</html>
