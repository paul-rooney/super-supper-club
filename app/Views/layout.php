<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'App') ?></title>

    <?php /* The single integration point: dev server tags OR built assets. */ ?>
    <?= vite('src/main.ts') ?>
</head>
<body>
    <nav>
        <a href="/">Home</a>
        <a href="/about">About</a>
    </nav>

    <main>
        <?= $content /* already-rendered page body (trusted HTML from the view) */ ?>
    </main>
</body>
</html>
