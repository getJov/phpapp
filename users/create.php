<?php

declare(strict_types=1);

require __DIR__ . '/../includes/auth.php';
require_login();
require __DIR__ . '/../config/database.php';

$errors = [];
$name = '';
$email = '';

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

    $errors = array_merge($errors, validate_required_password_pair($password, $confirmPassword));

    if (!$errors) {
        try {
            $statement = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
            $statement->execute([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            set_flash('success', 'User created.');
            redirect('/dashboard.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'That email is already registered.';
            } else {
                $errors[] = 'Unable to create user right now.';
            }
        }
    }
}

$pageTitle = 'Add user';
require __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 mb-0">Add user</h1>
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
            <div class="mb-3">
                <label class="form-label" for="name">Name</label>
                <input class="form-control" id="name" name="name" value="<?= e($name) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" value="<?= e($email) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="new-password" data-password-input required>
                    <button class="btn btn-outline-secondary" type="button" data-toggle-password="password">Show</button>
                </div>
                <div class="password-meter mt-2" data-password-meter="password">
                    <div class="password-meter-bar"></div>
                </div>
                <div class="form-text" data-password-meter-label="password">Use lowercase, uppercase, digit, symbol, and at least 8 characters.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="confirm_password">Confirm password</label>
                <div class="input-group">
                    <input class="form-control" id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
                    <button class="btn btn-outline-secondary" type="button" data-toggle-password="confirm_password">Show</button>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Create user</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
