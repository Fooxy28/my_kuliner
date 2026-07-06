<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function currentUser(): ?array
{
    return isLoggedIn() ? $_SESSION['user'] : null;
}

function hasRole(string $role): bool
{
    $user = currentUser();
    return $user !== null && ($user['role'] ?? '') === $role;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /my_kuliner/auth/login.php');
        exit;
    }
}

function requireRole(string $role): void
{
    requireLogin();

    if (!hasRole($role)) {
        http_response_code(403);
        die('Akses ditolak.');
    }
}

function requireRoles(array $roles): void
{
    requireLogin();

    $user = currentUser();
    if (!in_array($user['role'] ?? '', $roles, true)) {
        http_response_code(403);
        die('Akses ditolak.');
    }
}
