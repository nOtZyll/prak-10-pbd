<?php
// FILE: search_customer.php

// --- LOGIKA PHP DI ATAS ---

// 1. Koneksi Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "classicmodels";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. SARAN FITUR BARU: Ambil daftar kota untuk datalist
$cities = [];
$cityResult = $conn->query("SELECT DISTINCT city FROM customers ORDER BY city ASC");
if ($cityResult->num_rows > 0) {
    while($row = $cityResult->fetch_assoc()) {
        $cities[] = $row['city'];
    }
}

// 3. Inisialisasi variabel untuk hasil pencarian
$searchResult = null;
$searchedCity = '';
$errorMessage = '';

if (isset($_GET['city'])) {
    $searchedCity = trim($_GET['city']);
    if (!empty($searchedCity)) {
        // Gunakan prepared statement untuk mencegah SQL injection
        $stmt = $conn->prepare("SELECT * FROM customers WHERE city = ?");
        $stmt->bind_param("s", $searchedCity);
        $stmt->execute();
        $searchResult = $stmt->get_result();
        $stmt->close();
    } else {
        $errorMessage = "Please enter a city name to start a search.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Search by City</title>
    <style>
        body {
            background: linear-gradient(120deg, #89f7fe 0%, #66a6ff 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1, h2 {
            text-align: center;
            color: #234567;
            margin-top: 40px;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }
        
        .search-container, .result-container {
            background: #fff;
            max-width: 90%; /* Dibuat lebih lebar untuk tabel */
            margin: 30px auto;
            padding: 32px 36px;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            overflow-x: auto; /* Agar tabel bisa di-scroll di layar kecil */
        }

        .search-container form {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        label {
            font-weight: 500;
            color: #234567;
        }
        
        input[type="text"] {
            flex-grow: 1;
            padding: 10px 12px;
            border: 1px solid #b3d8fd;
            border-radius: 7px;
            font-size: 1em;
            transition: border 0.2s, background 0.2s;
            background: #f0f7ff;
        }

        input[type="text"]:focus {
            border-color: #66a6ff;
            outline: none;
            background: #e6f3ff;
        }
        
        input[type="submit"] {
            background: linear-gradient(90deg, #89f7fe 0%, #66a6ff 100%);
            color: #fff;
            border: none;
            font-weight: bold;
            font-size: 1.1em;
            padding: 10px 20px;
            border-radius: 7px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(102,166,255,0.15);
            transition: all 0.2s;
        }

        input[type="submit"]:hover {
            background: linear-gradient(90deg, #66a6ff 0%, #89f7fe 100%);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 12px rgba(102,166,255,0.25);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            min-width: 800px; /* Lebar minimum tabel */
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0f2f7;
        }

        thead th {
            background-color: #e6f3ff;
            color: #234567;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tbody tr:hover {
            background-color: #f0f7ff;
        }
        
        tbody td {
             font-size: 0.95em;
             vertical-align: middle; /* PERBAIKAN: Konten sejajar di tengah */
        }

        .text-right {
            text-align: right; /* PERBAIKAN: Kelas untuk meratakan angka ke kanan */
        }

        .error, .no-results {
            text-align: center;
            padding: 12px 18px;
            border-radius: 7px;
            font-size: 1em;
            margin-top: 1em;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        .no-results {
            background: #e3f0ff;
            color: #0d47a1;
            border: 1px solid #90caf9;
        }
    </style>
</head>
<body>
    <h1>Customer Search by City</h1>
    
    <div class="search-container">
        <form method="get" action="">
            <label for="city">Enter City Name:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($searchedCity); ?>" list="city-list" required>
            <datalist id="city-list">
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo htmlspecialchars($city); ?>">
                <?php endforeach; ?>
            </datalist>
            <input type="submit" value="Search">
        </form>
    </div>

    <?php if ($errorMessage): ?>
        <div class="result-container">
            <p class="error"><?php echo $errorMessage; ?></p>
        </div>
    <?php endif; ?>

    <?php if ($searchResult): ?>
        <div class="result-container">
            <h2>Customers in <?php echo htmlspecialchars($searchedCity); ?></h2>
            <?php if ($searchResult->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th class="text-right">Customer #</th>
                            <th>Customer Name</th>
                            <th>Contact</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Postal Code</th>
                            <th>Country</th>
                            <th class="text-right">Sales Rep</th>
                            <th class="text-right">Credit Limit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $searchResult->fetch_assoc()): ?>
                            <?php
                                // PERBAIKAN: Gabungkan alamat dengan rapi
                                $fullAddress = htmlspecialchars($row['addressLine1']);
                                if (!empty($row['addressLine2'])) {
                                    $fullAddress .= ", " . htmlspecialchars($row['addressLine2']);
                                }
                            ?>
                            <tr>
                                <td class="text-right"><?php echo htmlspecialchars($row['customerNumber']); ?></td>
                                <td><?php echo htmlspecialchars($row['customerName']); ?></td>
                                <td><?php echo htmlspecialchars($row['contactFirstName'] . " " . $row['contactLastName']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo $fullAddress; ?></td>
                                <td><?php echo htmlspecialchars($row['city']); ?></td>
                                <td><?php echo htmlspecialchars($row['state']); ?></td>
                                <td><?php echo htmlspecialchars($row['postalCode']); ?></td>
                                <td><?php echo htmlspecialchars($row['country']); ?></td>
                                <td class="text-right"><?php echo $row['salesRepEmployeeNumber'] ? htmlspecialchars($row['salesRepEmployeeNumber']) : 'N/A'; ?></td>
                                <td class="text-right"><?php echo number_format($row['creditLimit'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-results">No customers found in "<?php echo htmlspecialchars($searchedCity); ?>"</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</body>
</html>
<?php
$conn->close();
?>