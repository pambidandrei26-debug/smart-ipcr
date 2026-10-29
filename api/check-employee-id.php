<?php
/**
 * Check if Employee ID already exists
 * Used for real-time validation in forms
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
requireLogin();
requireRole('admin', 'dean', 'secretary');

header('Content-Type: application/json');

$employeeId = trim($_GET['employee_id'] ?? '');
$excludeUserId = (int)($_GET['exclude_user_id'] ?? 0);

if (empty($employeeId)) {
    echo json_encode(['exists' => false]);
    exit;
}

$db = getDB();

if ($excludeUserId > 0) {
    // Exclude current user when editing
    $stmt = $db->prepare("SELECT id FROM users WHERE employee_id = ? AND id != ?");
    $stmt->execute([$employeeId, $excludeUserId]);
} else {
    $stmt = $db->prepare("SELECT id FROM users WHERE employee_id = ?");
    $stmt->execute([$employeeId]);
}

$exists = $stmt->fetch();

echo json_encode([
    'exists' => (bool)$exists,
    'employee_id' => $employeeId
]);
