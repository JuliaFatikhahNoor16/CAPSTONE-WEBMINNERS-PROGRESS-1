<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Erthree Coffee</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="login-page">

    <div class="login-container">
        <!-- Header: Logo dan Sambutan -->
        <div class="header">
            <img src="../img/logo.png" alt="Erthree Coffee Logo">
            <h2>Login Admin</h2>
            <p>Silakan masuk untuk mengelola konten.</p>
        </div>

        <!-- Pesan Error -->
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <!-- Form Login -->
        <form action="proses_login.php" method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">Login</button>
            <button type="button" class="back-btn" onclick="window.location.href='index.php'">Kembali</button>
        </form>
    </div>

</body>
</html>
