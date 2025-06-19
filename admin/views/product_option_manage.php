<?php
session_start();
include '../../db/db.php';
include '../includes/auth_check.php';

// Ambil semua produk
$product_result = $conn->query("SELECT id, name FROM products");

// Ambil semua varian untuk validasi duplicate
$existing_variants = [];
$variant_check = $conn->query("SELECT product_id, variant, category FROM product_variants");
while ($row = $variant_check->fetch_assoc()) {
    $existing_variants[] = $row['product_id'] . '|' . strtolower($row['variant']) . '|' . $row['category'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Product Variants</title>
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
            <h2 class="mb-4">Add Product Variants</h2>
            <form action="../controllers/product_variant_controller.php" method="POST" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label class="form-label">Select Product</label>
                    <select id="product_id" name="product_id" class="form-select" required>
                        <option value="">-- Choose --</option>
                        <?php while ($product = $product_result->fetch_assoc()): ?>
                            <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Variants Name</label>
                    <input type="text" id="variant" name="variant" class="form-control" placeholder="e.g. Medium, Extra Spicy" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" id="category" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <option value="size">Size</option>
                        <option value="extra">Extra</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (Rp)</label>
                    <input type="number" name="price" class="form-control" required>
                </div>

                <button type="submit" name="add_variant" class="btn btn-primary">Save</button>
            </form>

            <hr class="my-5">

            <h4>Varian yang Ada</h4>
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Varian</th>
                        <th>Category</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $variant_query = "
                        SELECT p.name AS product_name, v.variant, v.category, v.price
                        FROM product_variants v
                        JOIN products p ON v.product_id = p.id
                        ORDER BY v.product_id, v.category, v.id
                    ";
                    $variants = $conn->query($variant_query);

                    while ($v = $variants->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($v['product_name']) ?></td>
                        <td><?= htmlspecialchars($v['variant']) ?></td>
                        <td><?= htmlspecialchars($v['category']) ?></td>
                        <td>Rp <?= number_format($v['price'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const existingVariants = <?= json_encode($existing_variants) ?>;

        function validateForm() {
            const productId = document.getElementById('product_id').value;
            const variant = document.getElementById('variant').value.trim().toLowerCase();
            const category = document.getElementById('category').value;
            const key = productId + '|' + variant + '|' + category;

            if (existingVariants.includes(key)) {
                alert('Varian tersebut sudah tersedia untuk produk ini.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>