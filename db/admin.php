<?php
session_start();
include 'db.php';

// Protect admin page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "rpl_umkm");

// Tambah atau update stok produk
if (isset($_POST['add'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar'];
    $kategori = $_POST['kategori'];
    $ukuran = $_POST['ukuran'];
    $label = $_POST['label'];
    $label_class = $_POST['label_class'];
    $stok_baru = $_POST['stok'];

    // Cek apakah produk dengan nama tersebut sudah ada
    $cek = $conn->prepare("SELECT id, stok FROM produk WHERE nama=? LIMIT 1");
    $cek->bind_param("s", $nama);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows > 0) {
        // Produk sudah ada, update stok
        $data = $result->fetch_assoc();
        $stok_lama = $data['stok'];
        $id_produk = $data['id'];
        $stok_total = $stok_lama + $stok_baru;

        $update = $conn->prepare("UPDATE produk SET stok=? WHERE id=?");
        $update->bind_param("ii", $stok_total, $id_produk);
        $update->execute();
        $update->close();
    } else {
        // Produk belum ada, tambah baru
        $stmt = $conn->prepare("INSERT INTO produk (nama, deskripsi, harga, gambar, kategori, ukuran, label, label_class, stok) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisssssi", $nama, $deskripsi, $harga, $gambar, $kategori, $ukuran, $label, $label_class, $stok_baru);
        $stmt->execute();
        $stmt->close();
    }
    $cek->close();
}

// Update stok via tombol kuning
if (isset($_POST['update_stok'])) {
    $id = $_POST['id'];
    $stok = $_POST['stok'];
    $update = $conn->prepare("UPDATE produk SET stok=? WHERE id=?");
    $update->bind_param("ii", $stok, $id);
    $update->execute();
    $update->close();
}

// Hapus produk
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM produk WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM produk");
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
        <h1 class="mb-0">Admin Panel</h1>
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
        <h3>Add Product</h3>
        <form method="post" class="row g-2">
            <div class="col-md-3"><input type="text" name="nama" placeholder="Product name" class="form-control" required></div>
            <div class="col-md-3"><input type="text" name="deskripsi" placeholder="Description" class="form-control"></div>
            <div class="col-md-2"><input type="number" name="harga" placeholder="Price" class="form-control" required></div>
            <div class="col-md-2"><input type="text" name="gambar" placeholder="Image URL" class="form-control"></div>
            <div class="col-md-2"><input type="text" name="kategori" placeholder="Category" class="form-control"></div>
            <div class="col-md-2"><input type="text" name="ukuran" placeholder="Size" class="form-control"></div>
            <div class="col-md-2"><input type="text" name="label" placeholder="Label (e.g. HOT, NEW)" class="form-control"></div>
            <div class="col-md-2"><input type="text" name="label_class" placeholder="Label Class (e.g. hot, new)" class="form-control"></div>
            <div class="col-md-2"><input type="number" name="stok" placeholder="Stock" class="form-control" required></div>
            <div class="col-md-2">
                <button type="submit" name="add" class="btn btn-primary px-4">Add</button>
            </div>
        </form>
    </div>

    <!-- Product list -->
    <h3>Product List</h3>
    <table class="table table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>Name</th><th>Description</th><th>Price</th><th>Image</th><th>Category</th><th>Size</th><th>Label</th><th>Stock</th><th>Actions</th>
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
                        <img src="<?= htmlspecialchars($row['gambar']) ?>" alt="Product Image" style="width: 50px;">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['kategori']) ?></td>
                <td><?= htmlspecialchars($row['ukuran']) ?></td>
                <td><span class="label bg-<?= htmlspecialchars($row['label_class']) ?>"><?= htmlspecialchars($row['label']) ?></span></td>
                <td>
                    <form method="post" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="number" name="stok" value="<?= $row['stok'] ?>" class="form-control form-control-sm" style="width: 70px;">
                        <button type="submit" name="update_stok" class="btn btn-warning btn-sm" title="Update Stock">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </form>
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
