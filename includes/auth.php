<?php
function ensureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function validateAccountCreation(string $password, string $confirm): ?string
{
    if ($password === '') {
        return 'A password is required to create an account.';
    }
    if (strlen($password) < 6) {
        return 'Password must be at least 6 characters long.';
    }
    if ($password !== $confirm) {
        return 'Passwords do not match.';
    }
    return null;
}

function createUser(PDO $pdo, string $email, string $name, string $password, string $phone = '', string $address = ''): int
{
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new RuntimeException('This email is already registered. Please log in, or continue as a guest.');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $hash, $phone, $address]);
    return (int) $pdo->lastInsertId();
}
