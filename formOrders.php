<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            background: linear-gradient(120deg, #89f7fe 0%, #66a6ff 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        h2 {
            text-align: center;
            color: #234567;
            margin-top: 40px;
            letter-spacing: 2px;
        }
        form {
            background: #fff;
            max-width: 420px;
            margin: 40px auto;
            padding: 32px 36px 24px 36px;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }
        label {
            font-weight: 500;
            color: #234567;
            margin-bottom: 6px;
            display: block;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #b3d8fd;
            border-radius: 7px;
            font-size: 1em;
            margin-bottom: 18px;
            transition: border 0.2s;
            background: #f0f7ff;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #66a6ff;
            outline: none;
            background: #e6f3ff;
        }
        textarea {
            min-height: 60px;
            resize: vertical;
        }
        .error, .success {
            max-width: 420px;
            margin: 24px auto 0 auto;
            padding: 12px 18px;
            border-radius: 7px;
            font-size: 1em;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .error {
            background: #e3f0ff;
            color: #0d47a1;
            border: 1px solid #90caf9;
        }
        .success {
            background: #e0f7fa;
            color: #00796b;
            border: 1px solid #4dd0e1;
        }
        input[type="submit"] {
            background: linear-gradient(90deg, #89f7fe 0%, #66a6ff 100%);
            color: #fff;
            border: none;
            font-weight: bold;
            font-size: 1.1em;
            padding: 12px 0;
            border-radius: 7px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(102,166,255,0.15);
            transition: background 0.2s, transform 0.1s;
        }
        input[type="submit"]:hover {
            background: linear-gradient(90deg, #66a6ff 0%, #89f7fe 100%);
            transform: translateY(-2px) scale(1.03);
        }
    </style>
</head>
<body>
  <h2>Tambah Order</h2>
  
  <?php
  // Koneksi database
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "classicmodels";
  
  $conn = new mysqli($servername, $username, $password, $dbname);
  
  // Variabel untuk pesan error/success
  $error = "";
  $success = "";
  
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Ambil data dari form
      $orderNumber = $_POST["orderNumber"];
      $orderDate = $_POST["orderDate"];
      $requiredDate = $_POST["requiredDate"];
      $shippedDate = $_POST["shippedDate"] ?? null;
      $status = $_POST["status"];
      $comments = $_POST["comments"] ?? null;
      $customerNumber = $_POST["customerNumber"];
      
      // Validasi input
      if (!is_numeric($orderNumber)) {
          $error = "Order Number harus berupa angka";
      } elseif (!is_numeric($customerNumber)) {
          $error = "Customer Number harus berupa angka";
      } elseif (empty($orderDate) || empty($requiredDate)) {
          $error = "Order Date dan Required Date wajib diisi";
      } else {
          // Cek apakah customerNumber ada di database
          $checkCustomer = $conn->prepare("SELECT customerNumber FROM customers WHERE customerNumber = ?");
          $checkCustomer->bind_param("i", $customerNumber);
          $checkCustomer->execute();
          $checkCustomer->store_result();
          
          if ($checkCustomer->num_rows == 0) {
              $error = "Customer dengan nomor $customerNumber tidak ditemukan";
          } else {
              // Insert data ke database dengan prepared statement
              $stmt = $conn->prepare("INSERT INTO orders (orderNumber, orderDate, requiredDate, shippedDate, status, comments, customerNumber) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
              $stmt->bind_param("isssssi", $orderNumber, $orderDate, $requiredDate, $shippedDate, $status, $comments, $customerNumber);
              
              if ($stmt->execute()) {
                  $success = "Order berhasil ditambahkan!";
              } else {
                  // Tangani error database
                  if ($conn->errno == 1062) {
                      $error = "Order Number $orderNumber sudah ada";
                  } else {
                      $error = "Terjadi kesalahan: " . $conn->error;
                  }
              }
              $stmt->close();
          }
          $checkCustomer->close();
      }
  }
  ?>
  
  <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
  <?php endif; ?>
  
  <?php if ($success): ?>
      <div class="success"><?php echo $success; ?></div>
  <?php endif; ?>
  
  <form method="post" action="">
    <label>Order Number:</label>
    <input type="number" name="orderNumber" required>
    <label>Order Date:</label>
    <input type="date" name="orderDate" required>
    <label>Required Date:</label>
    <input type="date" name="requiredDate" required>
    <label>Shipped Date:</label>
    <input type="date" name="shippedDate">
    <label>Status:</label>
    <select name="status" required>
      <option value="In Process">In Process</option>
      <option value="Shipped">Shipped</option>
      <option value="Cancelled">Cancelled</option>
      <option value="On Hold">On Hold</option>
      <option value="Disputed">Disputed</option>
      <option value="Resolved">Resolved</option>
    </select>
    <label>Comments:</label>
    <textarea name="comments"></textarea>
    <label>Customer Number:</label>
    <input type="number" name="customerNumber" required>
    <input type="submit" value="Submit">
  </form>
</body>
</html>