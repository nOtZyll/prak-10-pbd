<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ClassicModels DB</title>
    <style>
        /* Menggunakan style dasar yang sama persis */
        body {
            background: linear-gradient(120deg, #89f7fe 0%, #66a6ff 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Kontainer utama untuk landing page */
        .main-container {
            background: #fff;
            max-width: 800px;
            width: 100%;
            margin: 30px auto;
            padding: 40px 50px;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            text-align: center;
        }

        h1 {
            color: #234567;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #56789a;
            font-size: 1.1em;
            margin-bottom: 40px;
        }

        /* Grid untuk kartu navigasi */
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            text-align: left;
        }

        /* Style untuk setiap kartu navigasi */
        .nav-card {
            background: #f8f9fa;
            border: 1px solid #e0f2f7;
            border-radius: 12px;
            padding: 25px;
            text-decoration: none;
            color: #234567;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }

        .nav-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(102, 166, 255, 0.2);
            border-color: #66a6ff;
        }

        .nav-card .icon {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: #66a6ff;
        }

        .nav-card h3 {
            margin: 0 0 8px 0;
            font-size: 1.25em;
        }

        .nav-card p {
            margin: 0;
            color: #56789a;
            font-size: 0.95em;
            line-height: 1.5;
            flex-grow: 1; /* Membuat deskripsi mengisi ruang */
        }

    </style>
</head>
<body>

    <div class="main-container">
        <h1>Dashboard ClassicModels</h1>
        <p class="subtitle">Selamat datang! Silakan pilih menu untuk mengelola atau melihat data.</p>

        <div class="nav-grid">

            <a href="search_by_city.php" class="nav-card">
                <div class="icon">🏙️</div>
                <h3>Search by City</h3>
                <p>Cari data pelanggan berdasarkan lokasi kota mereka.</p>
            </a>

            <a href="search_by_date.php" class="nav-card">
                <div class="icon">📅</div>
                <h3>Search by Shipped Date</h3>
                <p>Temukan pesanan dan pelanggan berdasarkan tanggal pengiriman.</p>
            </a>

            <a href="formProducts.php" class="nav-card">
                <div class="icon">📦</div>
                <h3>Product Management</h3>
                <p>Lihat, tambah, atau kelola data produk dalam database.</p>
            </a>
            
            <a href="formOrders.php" class="nav-card">
                <div class="icon">🛒</div>
                <h3>Order Management</h3>
                <p>Lihat, tambah, atau kelola data pesanan pelanggan.</p>
            </a>

        </div>
    </div>

</body>
</html>