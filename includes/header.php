<?php

$pageTitle = $pageTitle ?? 'PHP User App';
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/styles.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/dashboard.php">PHP User App</a>
        <div class="navbar-nav ms-auto">
            <?php if (is_logged_in()): ?>
                <span class="navbar-text me-3"><?= e($_SESSION['user_name'] ?? 'User') ?></span>
                <a class="nav-link" href="/logout.php">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="/login.php">Login</a>
                <a class="nav-link" href="/signup.php">Sign up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
