<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth.php';
require_login();
require __DIR__ . '/../config/database.php';

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('danger', 'Invalid user.');
    redirect('/dashboard.php');
}

$statement = $pdo->prepare('SELECT id, name, email FROM users WHERE id = :id LIMIT 1');
$statement->execute(['id' => $id]);
$user = $statement->fetch();

if (!$user) {
    set_flash('danger', 'User not found.');
    redirect('/dashboard.php');
}

$errors = [];
$name = $user['name'];
$email = $user['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }

    $errors = array_merge($errors, validate_optional_password_pair($password, $confirmPassword));

    if (!$errors) {
        try {
            if ($password !== '') {
                $update = $pdo->prepare('UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id');
                $update->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'id' => $id,
                ]);
            } else {
                $update = $pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
                $update->execute([
                    'name' => $name,
                    'email' => $email,
                    'id' => $id,
                ]);
            }

            if (current_user_id() === $id) {
                $_SESSION['user_name'] = $name;
            }

            set_flash('success', 'User updated.');
            redirect('/dashboard.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'That email is already registered.';
            } else {
                $errors[] = 'Unable to update user right now.';
            }
        }
    }
}

$pageTitle = 'Edit user';
require __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 mb-0">Edit user</h1>
            <a class="btn btn-outline-secondary" href="/dashboard.php">Back</a>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <div><?= e($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= e((string) $id) ?>">
            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" name="name" value="<?= e($name) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= e($email) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">New password</label>
                <div class="input-group">
                    <input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="new-password" data-password-input>
                    <button class="btn btn-outline-secondary" type="button" data-toggle-password="password">Show</button>
                </div>
                <div class="password-meter mt-2" data-password-meter="password">
                    <div class="password-meter-bar"></div>
                </div>
                <div class="form-text" data-password-meter-label="password">Leave both password fields blank to keep the current password.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="confirm_password">Confirm new password</label>
                <div class="input-group">
                    <input class="form-control" id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" data-toggle-password="confirm_password">Show</button>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Save changes</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
