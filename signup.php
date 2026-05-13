<?php

declare(strict_types=1);

require __DIR__ . '/includes/auth.php';
require __DIR__ . '/config/database.php';

if (is_logged_in()) {
    redirect('/dashboard.php');
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (!$errors) {
        try {
            $statement = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
            $statement->execute([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            login_user((int) $pdo->lastInsertId(), $name);
            redirect('/dashboard.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'That email is already registered.';
            } else {
                $errors[] = 'Unable to create account right now.';
            }
        }
    }
}

$pageTitle = 'Sign up';
require __DIR__ . '/includes/header.php';
?>
<div class="card auth-card">
    <div class="card-body">
        <h1 class="h4 mb-3">Sign up</h1>

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
                <input class="form-control" id="password" name="password" type="password" minlength="6" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Create account</button>
        </form>

        <p class="mt-3 mb-0 text-center">
            Already have an account? <a href="/login.php">Login</a>
        </p>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
