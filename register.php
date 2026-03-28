<?php
header('Content-Type: application/json');
require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);

$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

// Validate
if (strlen($username) < 2 || strlen($username) > 20) {
    echo json_encode(['success' => false, 'message' => 'Username must be 2-20 characters.']);
    exit;
}

if (strlen($password) < 4) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 4 characters.']);
    exit;
}

$pdo = getDB();

// Check if username exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Username already taken.']);
    exit;
}

// Create user
$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
$stmt->execute([$username, $hash]);

$userId = $pdo->lastInsertId();

echo json_encode([
    'success' => true,
    'user' => ['id' => (int)$userId, 'username' => $username]
]);
