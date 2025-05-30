<?php
session_start();
include 'db.php';

// Protect admin page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "rpl_umkm");

// Update stok produk
if (isset($_POST['update_stok'])) {
    $id = $_POST['id'];
    $stok = $_POST['stok'];

    // Validasi stok minimal 0
    if ($stok < 0) $stok = 0;

    $stmt = $conn->prepare("UPDATE produk SET stok = ? WHERE id = ?");
    $stmt->bind_param("ii", $stok, $id);
    $stmt->execute();
    $stmt->close();

    // Redirect supaya refresh tidak mengulang POST
    header("Location: admin.php");
    exit();
}

// Hapus produk
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM produk WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Redirect supaya refresh tidak mengulang POST
    header("Location: admin.php");
    exit();
}

// Ambil semua produk
$result = $conn->query("SELECT * FROM produk ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Guide to ordering food with online payment">
	<meta name="author" content="Juragan Tulang Rangu Karawang">
	<title>Admin - Juragan Tulang Rangu Karawang</title>

	<!-- Favicon -->
	<link href="../img/logo.svg" rel="shortcut icon">

  <!-- Font Awesome CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Admin Panel - Manage Products</h1>
    <div>
        <a href="../index.html" class="btn btn-success me-2">
            <i class="fas fa-home"></i> Main Page
        </a>
        <a href="../db/register.php" class="btn btn-info">
            <i class="fas fa-user-plus"></i> Register New User
        </a>
    </div>
</div>

<!-- Add new product -->
<div class="mb-4">
    <h3>Add New Product</h3>
    <form method="post" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="nama" placeholder="Product name" class="form-control" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="deskripsi" placeholder="Varian" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="number" name="harga" placeholder="Price" class="form-control" required>
        </div>
        <div class="col-md-2">
            <input type="text" name="gambar" placeholder="Image URL" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="text" name="kategori" placeholder="Category" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="text" name="ukuran" placeholder="Size" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="text" name="label" placeholder="Label (HOT, NEW, DSB)" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="text" name="label_class" placeholder="Label Class (hot, new, dsb)" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="number" name="stok" placeholder="Stock" class="form-control" min="0" value="0" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="add" class="btn btn-primary px-md-4">Add</button>
        </div>
    </form>
</div>

<?php
// Handle add product submit
if (isset($_POST['add'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar'];
    $kategori = $_POST['kategori'];
    $ukuran = $_POST['ukuran'];
    $label = $_POST['label'];
    $label_class = $_POST['label_class'];
    $stok = $_POST['stok'];

    $stmt = $conn->prepare("INSERT INTO produk (nama, deskripsi, harga, gambar, kategori, ukuran, label, label_class, stok) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisssssi", $nama, $deskripsi, $harga, $gambar, $kategori, $ukuran, $label, $label_class, $stok);
    $stmt->execute();
    $stmt->close();

    // Redirect supaya refresh tidak mengulang POST
    header("Location: admin.php");
    exit();
}
?>

<!-- Product list -->
<h3>Product List</h3>
<table class="table table-bordered align-middle">
    <thead class="table-dark">
    <tr>
        <th>Name</th>
        <th>Varian</th>
        <th>Price</th>
        <th>Image</th>
        <th>Category</th>
        <th>Size</th>
        <th>Label</th>
        <th>Stock</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
            <td>
                <?php if ($row['gambar']): ?>
                    <img src="<?= htmlspecialchars($row['gambar']) ?>" alt="Product Image" style="width: 50px; height: auto;">
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['kategori']) ?></td>
            <td><?= htmlspecialchars($row['ukuran']) ?></td>
            <td><span class="label bg-<?= htmlspecialchars($row['label_class']) ?>"><?= htmlspecialchars($row['label']) ?></span></td>
            <td>
                <!-- Form untuk update stok -->
                <form method="post" class="d-flex align-items-center gap-2 mb-0">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="number" name="stok" value="<?= $row['stok'] ?>" min="0" class="form-control form-control-sm" style="width: 80px;" required>
                    <button type="submit" name="update_stok" class="btn btn-sm btn-warning" title="Update Stock">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </form>
            </td>
            <td>
                <!-- Form hapus -->
                <form method="post" onsubmit="return confirm('Are you sure you want to delete this product?');" class="mb-0">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-danger btn-sm" title="Delete Product">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
