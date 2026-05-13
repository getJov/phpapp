<?php

declare(strict_types=1);

require __DIR__ . '/includes/auth.php';
require_login();
require __DIR__ . '/config/database.php';

$statement = $pdo->query('SELECT id, name, email, created_at, updated_at FROM users ORDER BY id DESC');
$users = $statement->fetchAll();

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 class="h3 mb-1">Users</h1>
        <p class="text-muted mb-0">Manage registered users.</p>
    </div>
    <a class="btn btn-primary" href="/users/create.php">Add user</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$users): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">No users found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e((string) $user['id']) ?></td>
                        <td><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['created_at']) ?></td>
                        <td>
                            <div class="dashboard-actions">
                                <a class="btn btn-sm btn-outline-secondary" href="/users/edit.php?id=<?= e((string) $user['id']) ?>">Edit</a>
                                <form method="post" action="/users/delete.php" data-confirm="Delete this user?">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e((string) $user['id']) ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit" <?= current_user_id() === (int) $user['id'] ? 'disabled' : '' ?>>Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
