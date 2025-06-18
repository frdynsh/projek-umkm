<?php
session_start();
include '../../db/db.php';
include '../includes/auth_check.php';

// Ambil semua produk
$product_result = $conn->query("SELECT id, name FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Product Options</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            width: calc(100% - 250px);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content">
            <h2 class="mb-4">Add Product Option</h2>
            <form action="../controllers/product_option_controller.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Select Product</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Choose --</option>
                        <?php while ($product = $product_result->fetch_assoc()): ?>
                            <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Size</label>
                    <input type="text" name="size" class="form-control" placeholder="e.g. Small, Medium" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Extra (optional)</label>
                    <input type="text" name="extra" class="form-control" placeholder="e.g. Extra Spicy">
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (Rp)</label>
                    <input type="number" name="price" class="form-control" required>
                </div>

                <button type="submit" name="add_option" class="btn btn-primary">Save Option</button>
            </form>

            <hr class="my-5">

            <h4>Existing Options</h4>
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Extra</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $option_query = "
                        SELECT p.name AS product_name, o.size, o.extra, o.price
                        FROM product_options o
                        JOIN products p ON o.product_id = p.id
                    ";
                    $options = $conn->query($option_query);

                    while ($opt = $options->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($opt['product_name']) ?></td>
                        <td><?= htmlspecialchars($opt['size']) ?></td>
                        <td><?= htmlspecialchars($opt['extra']) ?: '-' ?></td>
                        <td>Rp <?= number_format($opt['price'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>