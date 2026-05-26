<?php

session_start();

function config(): array
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }

    return $config;
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $cfg = config();
        $dsn = 'pgsql:host=' . $cfg['db_host'] . ';port=' . $cfg['db_port'] . ';dbname=' . $cfg['db_name'];
        $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    return $pdo;
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_email']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect_to('login.php');
    }
}

function current_user_name(): string
{
    return $_SESSION['user_name'] ?? 'User';
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_page(): string
{
    return basename($_SERVER['PHP_SELF']);
}

function fetch_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetch_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}
