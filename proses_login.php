<?php
session_start();
require_once '../config/koneksi.php';  // Pastikan ini mengarah ke file koneksi.php

$username = $_POST['username'];
$password = $_POST['password'];

// Query untuk cek user dari DB
$stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verifikasi password menggunakan password_verify
    if (password_verify($password, $user['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Password salah.";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Username tidak ditemukan.";
    header("Location: login.php");
    exit;
}
?>
