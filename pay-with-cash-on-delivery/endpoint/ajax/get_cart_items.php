<?php
session_start();
require_once __DIR__ . '/../../../db/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['user']['id'];

$sql = "
SELECT 
    c.id as cart_id,
    p.name, p.image_path,
    pv.variant,
    pv.price,
    c.quantity
FROM carts c
JOIN products p ON p.id = c.product_id
JOIN product_variants pv ON pv.id = c.option_id
WHERE c.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];

while ($row = $result->fetch_assoc()) {
    $items[] = [
        'cart_id' => $row['cart_id'],
        'name' => $row['name'],
        'image' => '/projek-umkm/uploads/' . str_replace('uploads/', '', $row['image_path']),
        'variant' => $row['variant'],
        'price' => (int)$row['price'],
        'quantity' => (int)$row['quantity'],
    ];
}

echo json_encode(['status' => 'ok', 'data' => $items]);