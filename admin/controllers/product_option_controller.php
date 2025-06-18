<?php
session_start();
include '../../db/db.php';

// Check role
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'employee'])) {
    header("Location: ../../login.php");
    exit();
}

if (isset($_POST['add_option'])) {
    $product_id = $_POST['product_id'];
    $size = $_POST['size'];
    $extra = $_POST['extra'] ?? null;
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO product_options (product_id, size, extra, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $product_id, $size, $extra, $price);
    $stmt->execute();
    $stmt->close();

    header("Location: ../views/product_option_manage.php");
    exit();
}
?>