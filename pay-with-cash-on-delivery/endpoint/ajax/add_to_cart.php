<?php
session_start();
require_once __DIR__ . '/../../../db/db.php';

// Validasi session login
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user']['id'];
$productId = $_POST['product_id'] ?? null;
$optionId = $_POST['option_id'] ?? null;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Validasi input
if (!$productId || !$optionId) {
    echo json_encode(['status' => 'error', 'message' => 'Missing product or variant ID']);
    exit;
}

// Cek apakah variant benar dan sesuai dengan product
$sql = "SELECT id FROM product_variants WHERE id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $optionId, $productId);
$stmt->execute();
$result = $stmt->get_result();
$variant = $result->fetch_assoc();

if (!$variant) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product variant']);
    exit;
}

// Cek apakah item sudah ada di cart
$sqlCheck = "SELECT id, quantity FROM carts WHERE user_id = ? AND option_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("si", $userId, $optionId);
$stmtCheck->execute();
$checkResult = $stmtCheck->get_result();
$existing = $checkResult->fetch_assoc();

if ($existing) {
    // Update quantity
    $newQty = $existing['quantity'] + $quantity;
    $updateStmt = $conn->prepare("UPDATE carts SET quantity = ? WHERE id = ?");
    $updateStmt->bind_param("ii", $newQty, $existing['id']);
    $updateStmt->execute();
} else {
    // Insert baru
    $insertStmt = $conn->prepare("INSERT INTO carts (user_id, product_id, option_id, quantity, created_at) VALUES (?, ?, ?, ?, NOW())");
    $insertStmt->bind_param("siii", $userId, $productId, $optionId, $quantity);
    $insertStmt->execute();
}

echo json_encode(['status' => 'success']);