<?php
header('Content-Type: application/json');
require_once 'db.php';

$input = json_decode(file_get_contents('php://input'), true);

$userId = (int)($input['user_id'] ?? 0);
$score  = (int)($input['score'] ?? 0);

if ($userId <= 0 || $score < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit;
}

$pdo = getDB();

// Verify user exists
$stmt = $pdo->prepare('SELECT id FROM users WHERE id = ?');
$stmt->execute([$userId]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

// Insert score
$stmt = $pdo->prepare('INSERT INTO scores (user_id, score) VALUES (?, ?)');
$stmt->execute([$userId, $score]);

echo json_encode(['success' => true]);
