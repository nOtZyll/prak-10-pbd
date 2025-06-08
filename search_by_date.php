<?php
// FILE: search_by_date.php

// --- BAGIAN LOGIKA PHP ---

// 1. Koneksi Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "classicmodels";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // Sebaiknya tidak menampilkan error detail di produksi
    die("Connection failed. Please try again later.");
}

// 2. Inisialisasi variabel
$searchResult = null;
$searchedDate = '';
$errorMessage = '';

// 3. Proses form jika disubmit
if (isset($_GET['shippedDate'])) {
    $searchedDate = trim($_GET['shippedDate']);
    
    if (!empty($searchedDate)) {
        $sql = "SELECT c.customerNumber, c.customerName, c.contactLastName, c.contactFirstName, 
                c.phone, c.addressLine1, c.addressLine2, c.city, c.state, c.postalCode, c.country, 
                o.orderNumber, o.orderDate, o.requiredDate, o.shippedDate, o.status, o.comments
                FROM customers c
                JOIN orders o ON c.customerNumber = o.customerNumber
                WHERE o.shippedDate = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $searchedDate);
        $stmt->execute();
        $searchResult = $stmt->get_result();
        $stmt->close();
    } else {
        $errorMessage = "Please select a shipped date to start a search.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Search by Shipped Date</title>
    <style>
        /* CSS LENGKAP DARI CONTOH SEBELUMNYA */
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
            max-width: 90%;
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
            flex-wrap: wrap; /* Agar responsif di layar kecil */
        }

        label {
            font-weight: 500;
            color: #234567;
        }
        
        /* PERUBAHAN: Style diterapkan untuk input[type="date"] juga */
        input[type="text"], input[type="date"] {
            flex-grow: 1;
            padding: 10px 12px;
            border: 1px solid #b3d8fd;
            border-radius: 7px;
            font-size: 1em;
            font-family: 'Segoe UI', Arial, sans-serif; /* Pastikan font konsisten */
            transition: border 0.2s, background 0.2s;
            background: #f0f7ff;
            min-width: 200px; /* Lebar minimum untuk input */
        }

        input[type="text"]:focus, input[type="date"]:focus {
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
            min-width: 900px; /* Lebar minimum tabel */
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
             vertical-align: middle; /* Konten sejajar di tengah */
        }

        .text-right {
            text-align: right; /* Kelas untuk meratakan angka ke kanan */
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
    <h1>Customer Search by Shipped Date</h1>
    
    <div class="search-container">
        <form method="get" action="">
            <label for="shippedDate">Select Shipped Date:</label>
            <input type="date" id="shippedDate" name="shippedDate" value="<?php echo htmlspecialchars($searchedDate); ?>" required>
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
            <h2>Orders Shipped on <?php echo htmlspecialchars(date("F j, Y", strtotime($searchedDate))); ?></h2>
            <?php if ($searchResult->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th class="text-right">Customer #</th>
                            <th>Customer Name</th>
                            <th>Contact</th>
                            <th class="text-right">Order #</th>
                            <th>Order Date</th>
                            <th>Shipped Date</th>
                            <th>Status</th>
                            <th>Address</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $searchResult->fetch_assoc()): ?>
                            <?php
                                // PERBAIKAN: Gabungkan semua bagian alamat menjadi satu string yang rapi
                                $addressParts = [
                                    $row['addressLine1'],
                                    $row['addressLine2'],
                                    $row['city'],
                                    $row['state'],
                                    $row['postalCode'],
                                    $row['country']
                                ];
                                // Hapus bagian yang kosong dan gabungkan dengan koma
                                $fullAddress = implode(", ", array_filter(array_map('htmlspecialchars', $addressParts)));
                            ?>
                            <tr>
                                <td class="text-right"><?php echo htmlspecialchars($row['customerNumber']); ?></td>
                                <td><?php echo htmlspecialchars($row['customerName']); ?></td>
                                <td><?php echo htmlspecialchars($row['contactFirstName'] . " " . $row['contactLastName']); ?></td>
                                <td class="text-right"><?php echo htmlspecialchars($row['orderNumber']); ?></td>
                                <td><?php echo htmlspecialchars($row['orderDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['shippedDate']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo $fullAddress; ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-results">No orders found shipped on "<?php echo htmlspecialchars($searchedDate); ?>"</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</body>
</html>
<?php
$conn->close();
?>