<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_bundling'])) {
    $nama = mysqli_real_escape_string($connection, $_POST['nama']);
    $harga = (int) $_POST['harga'];
    $deskripsi = mysqli_real_escape_string($connection, $_POST['deskripsi']);

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    $uploadDir = '../uploads/';
    $uploadPath = $uploadDir . basename($gambar);

    if (move_uploaded_file($tmp, $uploadPath)) {
        $queryInsert = "INSERT INTO bundling (nama, harga, deskripsi, gambar) VALUES ('$nama', $harga, '$deskripsi', '$gambar')";
        mysqli_query($connection, $queryInsert);
        header("Location: bundling.php");
        exit;
    } else {
        echo "<script>alert('Gagal upload gambar');</script>";
    }
}

$query = "SELECT * FROM bundling";
$result = mysqli_query($connection, $query);

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Bundling - Erthree Coffee</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="dashboard-page">

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<div class="dashboard-main-content">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="dashboard-header-info">
            <h1>Kelola Konten Bundling</h1>
            <p>Halo, <?= htmlspecialchars($username); ?>! Tambah atau ubah bundling di sini.</p>
        </div>
        <div class="dashboard-header-user">
            <img src="../img/etmin.png" alt="Admin Photo" class="dashboard-admin-avatar">
        </div>
    </header>

    <!-- Konten Bundling -->
    <section class="dashboard-menu-content"> <!-- Gunakan class yang sama seperti menu -->
        <h2>Tambah Bundling Baru</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="form-menu"> <!-- Sama seperti form-menu -->
            <input type="text" name="nama" placeholder="Nama Bundling" required>
            <input type="number" name="harga" placeholder="Harga (angka saja)" required>
            <textarea name="deskripsi" placeholder="Deskripsi bundling..." required></textarea>
            <input type="file" name="gambar" accept="image/*" required>
            <button type="submit" name="tambah_bundling" class="btn-primary"><i class="fa fa-save"></i> Simpan Bundling</button>
        </form>
        <hr>

        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" width="60"></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                        <td>
                            <a href="bundling-edit.php?id=<?= $row['id']; ?>" class="btn-warning"><i class="fa fa-edit"></i></a>
                            <a href="bundling-hapus.php?id=<?= $row['id']; ?>" class="btn-danger" onclick="return confirm('Hapus bundling ini?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>
