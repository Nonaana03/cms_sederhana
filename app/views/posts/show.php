<!DOCTYPE html>
<html>
<head>
    <title>Detail Post</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
    <a href="/posts/index">Kembali ke daftar</a>
</body>
</html> 