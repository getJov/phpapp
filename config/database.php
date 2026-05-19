<?php

declare(strict_types=1);

function load_env_file(string $path): array
{
    if (!is_file($path) || !is_readable($path)) {
        return [];
    }

    $values = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        return [];
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value);

        if ($key === '') {
            continue;
        }

        if (
            strlen($value) >= 2
            && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        $values[$key] = $value;
    }

    return $values;
}

function env_value(array $env, string $key, ?string $default = null): string
{
    $value = $env[$key] ?? $default;

    if ($value === null || $value === '') {
        throw new RuntimeException("Missing required environment value: {$key}");
    }

    return $value;
}

try {
    $env = load_env_file(dirname(__DIR__) . '/.env');

    $dbHost = env_value($env, 'DB_HOST');
    $dbName = env_value($env, 'DB_NAME');
    $dbUser = env_value($env, 'DB_USER');
    $dbPass = env_value($env, 'DB_PASS');
    $dbCharset = env_value($env, 'DB_CHARSET', 'utf8mb4');

    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

    $pdoOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $dbUser, $dbPass, $pdoOptions);
} catch (Throwable $exception) {
    http_response_code(500);
    exit('Database connection failed. Check your database configuration.');
}
