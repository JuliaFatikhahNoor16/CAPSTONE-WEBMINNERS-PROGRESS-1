<?php

session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_galeri'])) {
    $judul = mysqli_real_escape_string($connection, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($connection, $_POST['deskripsi']);

    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    $uploadDir = '../uploads/';
    $uploadPath = $uploadDir . basename($gambar);

    if (move_uploaded_file($tmp, $uploadPath)) {
        $queryInsert = "INSERT INTO galeri (judul, deskripsi, gambar) VALUES ('$judul','$deskripsi','$gambar')";
        mysqli_query($connection, $queryInsert);
        header("Location: galeri.php");
        exit;
    } else {
        echo "<script>alert('Gagal upload gambar');</script>";
    }
}

// Ambil data dari tabel menu
$query = "SELECT * FROM galeri";
$result = mysqli_query($connection, $query);

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Galeri - Erthree Coffee</title>
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
            <h1>Kelola Konten Galeri</h1>
            <p>Halo, <?= htmlspecialchars($username); ?>! Tambah atau ubah galeri disini.</p>
        </div>
        <div class="dashboard-header-user">
            <img src="../img/etmin.png" alt="Admin Photo" class="dashboard-admin-avatar">
        </div>
    </header>

    <!-- Konten Menu -->
    <section class="dashboard-galeri-content">
        <h2>Tambah Konten Baru</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="form-galeri">
            <input type="text" name="judul" placeholder="Judul Konten" required>
            <textarea name="deskripsi" placeholder="Deskripsi Konten..." required></textarea>
            <input type="file" name="gambar" accept="image/*" required>
            <button type="submit" name="tambah_galeri" class="btn-primary"><i class="fa fa-save"></i> Simpan Konten</button>
        </form>
        <hr>

        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><img src="../uploads/<?= $row['gambar']; ?>" width="60"></td>
                        <td><?= htmlspecialchars($row['judul']); ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                        <td>
                            <a href="galeri-edit.php?id=<?= $row['id']; ?>" class="btn-warning"><i class="fa fa-edit"></i></a>
                            <a href="galeri-hapus.php?id=<?= $row['id']; ?>" class="btn-danger" onclick="return confirm('Hapus konten ini?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>
