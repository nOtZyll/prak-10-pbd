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
  <div class="form-container">
    <h2>Tambah Produk Baru</h2>
  
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
        $productCode = trim($_POST["productCode"]);
        $productName = trim($_POST["productName"]);
        $productLine = trim($_POST["productLine"]);
        $productScale = trim($_POST["productScale"]);
        $productVendor = trim($_POST["productVendor"]);
        $productDescription = trim($_POST["productDescription"]);
        $quantityInStock = trim($_POST["quantityInStock"]);
        $buyPrice = trim($_POST["buyPrice"]);
        $MSRP = trim($_POST["MSRP"]);
        
        // Validasi input
        $errors = [];
        
        if (strlen($productCode) > 15) {
            $errors[] = "Product Code maksimal 15 karakter";
        }
        
        if (strlen($productName) > 70) {
            $errors[] = "Product Name maksimal 70 karakter";
        }
        
        if (strlen($productLine) > 50) {
            $errors[] = "Product Line maksimal 50 karakter";
        }
        
        if (strlen($productScale) > 10) {
            $errors[] = "Product Scale maksimal 10 karakter";
        }
        
        if (strlen($productVendor) > 50) {
            $errors[] = "Product Vendor maksimal 50 karakter";
        }
        
        if (!is_numeric($quantityInStock) || $quantityInStock < 0) {
            $errors[] = "Quantity in Stock harus angka positif";
        }
        
        if (!is_numeric($buyPrice) || $buyPrice <= 0) {
            $errors[] = "Buy Price harus angka positif";
        }
        
        if (!is_numeric($MSRP) || $MSRP <= 0) {
            $errors[] = "MSRP harus angka positif";
        }
        
        // Cek apakah productLine valid
        $checkProductLine = $conn->prepare("SELECT productLine FROM productlines WHERE productLine = ?");
        $checkProductLine->bind_param("s", $productLine);
        $checkProductLine->execute();
        $checkProductLine->store_result();
        
        if ($checkProductLine->num_rows == 0) {
            $errors[] = "Product Line '$productLine' tidak valid";
        }
        $checkProductLine->close();
        
        // Jika tidak ada error, proses insert
        if (empty($errors)) {
            $stmt = $conn->prepare("INSERT INTO products (productCode, productName, productLine, productScale, productVendor, productDescription, quantityInStock, buyPrice, MSRP) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssidd", $productCode, $productName, $productLine, $productScale, $productVendor, $productDescription, $quantityInStock, $buyPrice, $MSRP);
            
            if ($stmt->execute()) {
                $success = "Produk berhasil ditambahkan!";
            } else {
                if ($conn->errno == 1062) {
                    $error = "Product Code '$productCode' sudah ada";
                } else {
                    $error = "Terjadi kesalahan: " . $conn->error;
                }
            }
            $stmt->close();
        } else {
            $error = implode("<br>", $errors);
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
      <label>Product Code (max 15 char):</label>
      <input type="text" name="productCode" maxlength="15" required>
      
      <label>Product Name (max 70 char):</label>
      <input type="text" name="productName" maxlength="70" required>
      
      <label>Product Line (max 50 char):</label>
      <select name="productLine" required>
        <?php
        // Ambil daftar productLine yang valid dari database
        $productLines = $conn->query("SELECT productLine FROM productlines ORDER BY productLine");
        while ($row = $productLines->fetch_assoc()) {
            echo "<option value='".htmlspecialchars($row['productLine'])."'>".htmlspecialchars($row['productLine'])."</option>";
        }
        ?>
      </select>
      
      <label>Product Scale (max 10 char):</label>
      <input type="text" name="productScale" maxlength="10" required>
      
      <label>Product Vendor (max 50 char):</label>
      <input type="text" name="productVendor" maxlength="50" required>
      
      <label>Product Description:</label>
      <textarea name="productDescription" required></textarea>
      
      <label>Quantity in Stock:</label>
      <input type="number" name="quantityInStock" min="0" required>
      
      <label>Buy Price:</label>
      <input type="number" name="buyPrice" min="0.01" step="0.01" required>
      
      <label>MSRP:</label>
      <input type="number" name="MSRP" min="0.01" step="0.01" required>
      
      <input type="submit" value="Submit">
    </form>
  </div>
</body>
</html>