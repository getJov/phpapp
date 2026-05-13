<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth.php';
require_login();
require __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/dashboard.php');
}

verify_csrf();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('danger', 'Invalid user.');
    redirect('/dashboard.php');
}

if (current_user_id() === $id) {
    set_flash('danger', 'You cannot delete your own account while logged in.');
    redirect('/dashboard.php');
}

$statement = $pdo->prepare('DELETE FROM users WHERE id = :id');
$statement->execute(['id' => $id]);

set_flash('success', 'User deleted.');
redirect('/dashboard.php');
