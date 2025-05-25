<?php
session_start();
include 'db.php';

// Protect admin page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
  }

$conn = new mysqli("localhost", "root", "", "rpl_umkm");

if (isset($_POST['add'])) {
  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $image = $_POST['image'];

  $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssdsi", $name, $description, $price, $stock, $image);
  $stmt->execute();
  $stmt->close();

}

if (isset($_POST['update_stock'])) {
  $id = $_POST['id'];
  $stock = $_POST['stock'];
  
  $stmt = $conn->prepare("UPDATE products SET stock=? WHERE id=?");
  $stmt->bind_param("ii", $stock, $id);
  $stmt->execute();
  $stmt->close();
}

if (isset($_POST['delete'])) {
  $id = $_POST['id'];
  
  $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
}

$result = $conn->query("SELECT * FROM products");
?>


<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="mb-0">Admin Panel</h1>
  <div>
    <a href="/RPL/projek-umkm-main/" class="btn btn-success me-2">
      <i class="fas fa-home"></i> Main Page
    </a>
    <a href="/RPL/projek-umkm-main/db/register.php" class="btn btn-info">
      <i class="fas fa-user-plus"></i> Register New User
    </a>
  </div>
  </div>



  <!-- Add new product -->
  <div class="mb-4">
    <h3>Add New Product</h3>
    <form method="post" class="row g-2">
      <div class="col-md-4">
        <input type="text" name="name" placeholder="Product name" class="form-control" required>
      </div>
      <div class="col-md-4">
        <input type="text" name="description" placeholder="Description" class="form-control">
      </div>
      <div class="col-md-2">
        <input type="number" step="0.01" name="price" placeholder="Price" class="form-control" required>
      </div>
      <div class="col-md-1">
        <input type="number" name="stock" placeholder="Stock" class="form-control" required>
      </div>
      <div class="col-md-1">
        <input type="text" name="image" placeholder="Image URL" class="form-control">
      </div>
      <div class="col-12">
        <button type="submit" name="add" class="btn btn-primary mt-2">Add</button>
      </div>
    </form>
  </div>

  <!-- Product list -->
  <h3>Product List</h3>
  <table class="table table-bordered">
    <thead class="table-dark"><tr><th>Name</th><th>Description</th><th>Price</th><th>Stock</th><th>Image</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td><?= htmlspecialchars($row['price']) ?></td>
        <td>
          <form method="post" class="d-flex align-items-center gap-2">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="number" name="stock" value="<?= $row['stock'] ?>" style="width: 70px;" class="form-control form-control-sm">
            <button type="submit" name="update_stock" class="btn btn-warning btn-sm">Update</button>
          </form>
        </td>
        <td>
          <?php if ($row['image']): ?>
            <img src="<?= htmlspecialchars($row['image']) ?>" alt="Product Image" style="width: 50px;">
          <?php endif; ?>
        </td>
        <td>
          <form method="post">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Delete product?');">Delete</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>