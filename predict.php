<?php
// Mulai session dan cek login
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

$query = "SELECT * FROM menu";
$result = mysqli_query($connection, $query);

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Menu - Erthree Coffee</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Add jQuery library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body class="dashboard-page">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="dashboard-main-content">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="dashboard-header-info">
                <h1>Prediksi Kategori Penjualan</h1>
                <p>Halo, <?= htmlspecialchars($username); ?>! Tambah atau ubah menu disini.</p>
            </div>
            <div class="dashboard-header-user">
                <img src="../img/etmin.png" alt="Admin Photo" class="dashboard-admin-avatar">
            </div>
        </header>

        <div class="dashboard-content">
            <h1><i class="fa fa-chart-bar"></i> Prediksi Kategori Penjualan</h1>

            <div class="prediction-card">
                <div class="card-header">
                    <h2>Parameter Prediksi</h2>
                </div>
                <div class="card-body">
                    <form id="predictionForm">
                        <div class="form-group">
                            <label for="productSelect">Produk:</label>
                            <select id="productSelect" class="form-control">
                                <?php
                                // Ambil daftar produk unik dari database
                                $query = "SELECT DISTINCT Product_Name FROM dashboard";
                                $result = mysqli_query($connection, $query);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . htmlspecialchars($row['Product_Name']) . '">'
                                        . htmlspecialchars($row['Product_Name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <button type="button" id="predictBtn" class="btn btn-primary">
                            <i class="fa fa-calculator"></i> Prediksi Kategori
                        </button>
                    </form>
                </div>
            </div>

            <div class="prediction-result mt-4">
                <div class="card">
                    <div class="card-header">
                        <h2>Hasil Prediksi</h2>
                    </div>
                    <div class="card-body">
                        <div id="predictionResult" class="alert alert-info">
                            Silakan pilih produk dan klik "Prediksi Kategori"
                        </div>
                        <div class="chart-container" style="position: relative; height:400px; width:100%">
                            <canvas id="predictionChart"></canvas>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            $('#predictBtn').click(async function() {
                // Ambil data produk yang dipilih dari dropdown
                const productName = $('#productSelect').val();
                console.log(productName);
                // Ambil data produk terpilih dari database untuk prediksi
                try {
                    // Tampilkan loading
                    $('#predictionResult').html('<div class="alert alert-warning">Memproses prediksi...</div>');

                    // 1. Ambil data historis produk dari database
                    const productData = await getProductData(productName);
                    console.log(productData);

                    // 2. Panggil API FastAPI untuk prediksi
                    const response = await fetch('http://127.0.0.1:8000/predict/', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer <?php echo $_SESSION['api_token'] ?? ''; ?>'
                        },
                        body: JSON.stringify({
                            Product_Name: productData.Product_Name,
                            Product_Price: productData.Product_Price,
                            Quantity: productData.Quantity,
                            Total: productData.Total,
                            Month: new Date().getMonth() + 1, // Bulan saat ini
                            Quantity_Monthly: productData.Quantity_Monthly,
                            Day: new Date().getDate(), // Hari saat ini
                            Year: new Date().getFullYear() // Tahun saat ini
                        })
                    });

                    const predictionResult = await response.json();

                    if (!response.ok) throw new Error(predictionResult.detail || 'Gagal melakukan prediksi');

                    // Tampilkan hasil prediksi
                    displayPredictionResult(productName, predictionResult);

                } catch (error) {
                    console.error('Error:', error);
                    $('#predictionResult').html(
                        '<div class="alert alert-danger">Gagal melakukan prediksi: ' + error.message + '</div>'
                    );
                }
            });

            // Fungsi untuk mengambil data produk dari database
            async function getProductData(productName) {
                const response = await fetch(`get-product-data.php?product=${encodeURIComponent(productName)}`);
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal mengambil data produk');
                return data;
            }

            // Fungsi untuk menampilkan hasil prediksi
            function displayPredictionResult(productName, result) {
                const categoryMap = {
                    "Sedikit": "warning",
                    "Sedang": "info",
                    "Banyak": "success"
                };

                const alertClass = categoryMap[result.predicted_category] || 'secondary';

                $('#predictionResult').html(`
                    <div class="alert alert-${alertClass}">
                        <h4>Hasil Prediksi Kategori</h4>
                        <p><strong>Produk:</strong> ${productName}</p>
                        <p><strong>Kategori Prediksi:</strong> 
                            <span class="badge bg-${alertClass}">${result.predicted_category}</span>
                        </p>
                        <p><strong>Model:</strong> ${result.model_used}</p>
                        ${result.confidence ? `<p><strong>Confidence:</strong> ${result.confidence}%</p>` : ''}
                    </div>
                `);

                // Jika ada data historis, render grafik
                if (result.historical_data && result.predicted_data) {
                    renderPredictionChart(result.historical_data, result.predicted_data);
                }
            }
        });

        function renderPredictionChart(historical, predicted) {
            const ctx = document.getElementById('predictionChart').getContext('2d');

            // Hapus chart sebelumnya jika ada
            if (window.predictionChart) {
                window.predictionChart.destroy();
            }

            window.predictionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [...historical.labels, ...predicted.labels],
                    datasets: [{
                            label: 'Data Historis',
                            data: historical.values,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            fill: true,
                            tension: 0.1
                        },
                        {
                            label: 'Prediksi',
                            data: [...Array(historical.values.length).fill(null), ...predicted.values],
                            borderColor: '#e74a3b',
                            backgroundColor: 'rgba(231, 74, 59, 0.05)',
                            borderDash: [5, 5],
                            fill: true,
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Jumlah Penjualan'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>