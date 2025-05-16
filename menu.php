<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_menu'])) {
    $nama     = mysqli_real_escape_string($connection, $_POST['nama']);
    $harga    = (int) $_POST['harga'];
    $kategori = mysqli_real_escape_string($connection, $_POST['kategori']);
    $deskripsi = mysqli_real_escape_string($connection, $_POST['deskripsi']);

    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];

    $uploadDir = '../uploads/';
    $uploadPath = $uploadDir . basename($gambar);

    if (move_uploaded_file($tmp, $uploadPath)) {
        $queryInsert = "INSERT INTO daftar_menu (nama_menu, harga, kategori, deskripsi) 
                        VALUES ('$nama', $harga, '$kategori', '$deskripsi')";
        if (!mysqli_query($connection, $queryInsert)) {
            die("Insert Error: " . mysqli_error($connection));
        }
        header("Location: menu.php");
        exit;
    } else {
        echo "<script>alert('Gagal upload gambar');</script>";
    }
}

// Ambil data dari database, urutkan berdasarkan kategori
$query = "SELECT * FROM menu ORDER BY kategori ASC";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Menu - Erthree Coffee</title>
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
            <h1>Kelola Konten Menu</h1>
            <p>Halo, <?= htmlspecialchars($username); ?>! Tambah atau ubah menu di sini.</p>
        </div>
        <div class="dashboard-header-user">
            <img src="../img/etmin.png" alt="Admin Photo" class="dashboard-admin-avatar">
        </div>
    </header>

    <!-- Konten Menu -->
    <section class="dashboard-menu-content">
        <h2>Tambah Menu Baru</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="form-menu">
            <input type="text" name="nama" placeholder="Nama Menu" required>
            <input type="number" name="harga" placeholder="Harga (angka saja)" required>
            <select name="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="kopi">Kopi</option>
                <option value="non-kopi">Non-Kopi</option>
                <option value="makanan ringan">Makanan Ringan</option>
                <option value="makanan berat">Makanan Berat</option>
            </select>
            <textarea name="deskripsi" placeholder="Deskripsi menu..." required></textarea>
            <input type="file" name="gambar" accept="image/*" required>
            <button type="submit" name="tambah_menu" class="btn-primary">
                <i class="fa fa-save"></i> Simpan Menu
            </button>
        </form>
        <hr>

        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $kategoriSebelumnya = '';
                while ($row = mysqli_fetch_assoc($result)) :
                    if ($row['kategori'] !== $kategoriSebelumnya) {
                        echo "<tr><td colspan='5' style='background:#eee;font-weight:bold;'>Kategori: " . ucfirst($row['kategori']) . "</td></tr>";
                        $kategoriSebelumnya = $row['kategori'];
                    }
                ?>
                    <tr>
                        <td><img src="../uploads/<?= $row['gambar']; ?>" width="60"></td>
                        <td><?= htmlspecialchars($row['nama_menu']); ?></td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td><?= ucfirst($row['kategori']); ?></td>
                        <td>
                            <a href="menu-edit.php?id=<?= $row['id']; ?>" class="btn-warning"><i class="fa fa-edit"></i></a>
                            <a href="menu-hapus.php?id=<?= $row['id']; ?>" class="btn-danger" onclick="return confirm('Hapus menu ini?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>
