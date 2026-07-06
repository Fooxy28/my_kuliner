<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function baseUrl(string $path = ''): string
{
    $root = '/my_kuliner';
    return $root . ($path !== '' ? '/' . ltrim($path, '/') : '');
}

function redirect(string $path): void
{
    header('Location: ' . baseUrl($path));
    exit;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $message;
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-');
}

function uploadImage(string $inputName, string $targetDir, array $allowed = ['jpg', 'jpeg', 'png', 'webp']): ?string
{
    if (!isset($_FILES[$inputName]) || ($_FILES[$inputName]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$inputName];
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $allowedMime, true)) {
        return null;
    }

    $filename = uniqid('img_', true) . '.' . $ext;
    $targetPath = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return null;
    }

    return $filename;
}

function restaurantImageUrl(?string $filename): string
{
    $cleanName = trim((string)$filename);
    if ($cleanName !== '') {
        $fullPath = __DIR__ . '/../uploads/restaurants/' . $cleanName;
        if (is_file($fullPath)) {
            return baseUrl('uploads/restaurants/' . $cleanName);
        }
    }

    return 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1000&q=80';
}
