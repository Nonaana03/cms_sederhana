<!DOCTYPE html>
<html>
<head>
    <title>Daftar Post</title>
</head>
<body>
    <h1>Daftar Post</h1>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <a href="/posts/show/<?php echo $post['id']; ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html> 