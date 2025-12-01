<?php
/** @var string $title */
/** @var string $content */
/** @var array<int, array{type:string,message:string}> $flashMessages */
/** @var array<string, mixed>|null $currentUser */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap с CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Task4 App</a>
        <div class="d-flex">
            <?php if (!empty($currentUser)): ?>
                <span class="navbar-text me-3">
                    Signed in as <?= htmlspecialchars($currentUser['name'] ?? '', ENT_QUOTES) ?>
                </span>
                <a href="index.php?page=logout" class="btn btn-outline-light btn-sm">Logout</a>
            <?php else: ?>
                <a href="index.php?page=login" class="btn btn-outline-light btn-sm me-2">Login</a>
                <a href="index.php?page=register" class="btn btn-outline-light btn-sm">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="container">
    <?php foreach ($flashMessages as $flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES) ?> alert-dismissible fade show"
             role="alert">
            <?= htmlspecialchars($flash['message'], ENT_QUOTES) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endforeach; ?>

    <?= $content ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
