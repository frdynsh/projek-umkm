<?php
session_start();
include '../../db/db.php';

// Cek autentikasi admin atau karyawan
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'employee'])) {
    header("Location: ../../login.php");
    exit();
}

// Handle tambah varian produk
if (isset($_POST['add_variant'])) {
    $productId = $_POST['product_id'];
    $variant = trim($_POST['variant']);
    $category = $_POST['category']; // 'size' atau 'extra'
    $price = $_POST['price'];

    // Cegah duplikasi berdasarkan product_id + variant + category
    $stmt = $conn->prepare("SELECT COUNT(*) FROM product_variants WHERE product_id = ? AND LOWER(variant) = LOWER(?) AND category = ?");
    $stmt->bind_param("iss", $productId, $variant, $category);
    $stmt->execute();
    $stmt->bind_result($exists);
    $stmt->fetch();
    $stmt->close();

    if ($exists > 0) {
        header("Location: ../views/product_variants_manage.php?error=exists");
        exit();
    }

    // Simpan ke DB
    $stmt = $conn->prepare("INSERT INTO product_variants (product_id, variant, category, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $productId, $variant, $category, $price);
    $stmt->execute();
    $stmt->close();

    header("Location: ../views/product_variants_manage.php?success=1");
    exit();
}
?>
