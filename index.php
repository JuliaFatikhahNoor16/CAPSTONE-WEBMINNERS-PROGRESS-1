<?php
require_once '../config/koneksi.php';

$menu_query = "SELECT * FROM menu ORDER BY id DESC"; // Bisa dibatasi jika perlu
$menu_result = mysqli_query($connection, $menu_query);

$queryGaleri = "SELECT * FROM galeri ORDER BY id DESC";
$resultGaleri = mysqli_query($connection, $queryGaleri);
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Erthree Coffee Space</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="container header-flex">
        <div class="logo-title">
            <img src="../img/logo.png" alt="Logo Erthree" class="logo">
            <span class="brand">Erthree Coffee Space</span>
        </div>
        
        <!-- ICON NAVBAR -->
        <nav class="nav-icon">
            <div class="nav-item">
                <a href="index.php" title="Home">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" title="Keranjang">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Cart</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="login.php" title="Admin">
                    <i class="fas fa-user-cog"></i>
                    <span>Admin</span>
                </a>
            </div>
        </nav>
    </div>
</header>

<!-- PROFILE -->
<section class="profile" style="background-image: url('../img/bg-about.jpg');">
    <div class="container">
        <h2>Tentang Kami</h2>
        <p>
            Selamat datang di <strong>Erthree Coffee Space</strong> — Terletak di sudut tenang Loa Bakung, Samarinda, Erthree Coffee hadir sebagai hidden gem bagi para pencinta ketenangan dan kenikmatan rasa. Dengan konsep rumah yang hangat dan nyaman, kami ingin menciptakan ruang yang terasa akrab—tempat semua orang bisa merasa pulang.

            Kami menyajikan pilihan kopi, teh, dan berbagai minuman lainnya yang diracik dengan sepenuh hati. Dari seduhan klasik hingga kreasi khas, setiap cangkir membawa cerita dan kehangatan. Lebih dari sekadar tempat ngopi, Erthree Coffee adalah tempat untuk berbagi tawa, obrolan, dan momen berharga.

            Kami percaya bahwa pelayanan adalah bagian dari pengalaman. Itu sebabnya para barista kami selalu siap menyambut dengan senyum dan keramahan, menjadikan setiap kunjungan lebih dari sekadar menikmati minuman—tapi juga merasakan kedekatan.

            Selamat datang di Erthree Coffee. Rumah kecil penuh rasa.
        </p>
    </div>
</section>

<!-- MENU -->
<section class="menu" id="menu">
    <div class="container">
        <h2>Menu Kami</h2>
        <div class="menu-container">
            <?php while ($row = mysqli_fetch_assoc($menu_result)) : ?>
                <div class="menu-card">
                    <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama']); ?>">
                    <div class="menu-info">
                        <h3><?= htmlspecialchars($row['nama']); ?></h3>

                        <!-- Deskripsi ringkas + toggle -->
                        <div class="deskripsi" data-full="<?= htmlspecialchars($row['deskripsi']) ?>">
                            <span class="short"><?= mb_strimwidth($row['deskripsi'], 0, 100, '...') ?></span>
                            <a href="#" class="toggle-detail">Lihat Detail</a>
                        </div>

                        <span class="harga">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>


<!-- BUNDLING / PROMO -->
<section class="bundling" id="bundling">
    <div class="container">
        <h2>Bundling Menu</h2>
        <div class="menu-container">
            <?php
            require_once '../config/koneksi.php';
            $bundlingQuery = "SELECT * FROM bundling";
            $bundlingResult = mysqli_query($connection, $bundlingQuery);

            while ($row = mysqli_fetch_assoc($bundlingResult)) :
            ?>
                <div class="menu-card">
                    <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama']); ?>">
                    <div class="menu-info">
                        <h3><?= htmlspecialchars($row['nama']); ?></h3>
                        <span class="harga">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>


<!-- GALERI -->
<section class="galeri" id="galeri">
    <div class="container">
        <h2>Galeri Erthree</h2>
        <div class="galeri-container">
            <?php while ($galeri = mysqli_fetch_assoc($resultGaleri)) : ?>
                <div class="galeri-card">
                    <img src="../uploads/<?= htmlspecialchars($galeri['gambar']); ?>" alt="<?= htmlspecialchars($galeri['judul']); ?>">
                    <p><?= htmlspecialchars($galeri['deskripsi']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>&copy; <?= date("Y") ?> Erthree Coffee Space</p>
        <p>📍 Jl.Jakarta, Loa Bakung, Kec. Sungai Kunjang, Kota Samarinda, Kalimantan Timur, Blok AI No.27 | @Erthree.Coffee | 📧 erthree@coffee.id</p>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-detail').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const deskripsiBox = this.closest('.deskripsi');
                const shortSpan = deskripsiBox.querySelector('.short');
                const fullText = deskripsiBox.dataset.full;

                if (this.textContent === 'Lihat Detail') {
                    shortSpan.textContent = fullText;
                    this.textContent = 'Sembunyikan';
                } else {
                    shortSpan.textContent = fullText.substring(0, 100) + '...';
                    this.textContent = 'Lihat Detail';
                }
            });
        });
    });
</script>

</body>
</html>
