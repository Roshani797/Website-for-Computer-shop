<?php
session_start();
include 'db.php';
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];
$cartCountResult = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE session_id = '$session_id'");
$cartCountRow = $cartCountResult->fetch_assoc();
$cart_count = $cartCountRow['total'] ?? 0;

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

$cartItems = $conn->query("SELECT * FROM cart WHERE session_id = '$session_id'");
$total = 0;
$items = [];

while ($row = $cartItems->fetch_assoc()) {
    $row['subtotal'] = $row['product_price'] * $row['quantity'];
    $total += $row['subtotal'];
    $items[] = $row;
}

$delivery_fee = 0;
$convenience_fee = 3200;
$final_total = $total + $convenience_fee + $delivery_fee;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $shipping_method = $_POST['shipping'] ?? 'pickup';
    $payment_method = $_POST['payment_method'] ?? 'card';

    $delivery_fee = ($shipping_method === 'delivery') ? 500 : 0;
    $final_total = $total + $convenience_fee + $delivery_fee;

    $stmt = $conn->prepare("INSERT INTO orders (session_id, full_name, email, address, phone, shipping_method, payment_method, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed for orders: " . $conn->error);
    }

    $stmt->bind_param("sssssssd", $session_id, $full_name, $email, $address, $phone, $shipping_method, $payment_method, $final_total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)");

    if (!$stmt_item) {
        die("Prepare failed for order_items: " . $conn->error);
    }

    foreach ($items as $item) {
        $stmt_item->bind_param("iisdi", $order_id, $item['product_id'], $item['product_name'], $item['product_price'], $item['quantity']);
        $stmt_item->execute();
    }

    $conn->query("DELETE FROM cart WHERE session_id = '$session_id'");

    echo "<script>alert('Order placed successfully!'); window.location='index.php';</script>";
    exit;
}

// Handle search query
if (isset($_GET['q'])) {
    $searchQuery = trim($_GET['q']);
    if ($searchQuery !== '') {
        // Query the database to find product matching search
        // We'll look in the peripheral table here; adapt for other categories if needed

        $searchEscaped = $conn->real_escape_string($searchQuery);
        $sql = "SELECT id, name FROM peripheral WHERE name LIKE '%$searchEscaped%' LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc();
            // Redirect to product page
            header("Location: product" . $product['id'] . ".php");
            exit;
        } else {
            // No product found - you can show a message or redirect to "no results" page
            $no_results_message = "No product found matching: " . htmlspecialchars($searchQuery);
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Technoshop</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        h2 { text-align: center; margin-bottom: 10px; }
        .instruction { text-align: center; margin-bottom: 25px; font-size: 16px; color: #555; }
        .checkout-container { max-width: 1100px; margin: auto; background: #fff; border-radius: 10px; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .flex-row { display: flex; flex-wrap: wrap; gap: 40px; }
        .left, .right { flex: 1; min-width: 300px; }
        .section { margin-bottom: 25px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; }
        input[type="text"], input[type="email"], textarea {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border: 1px solid #ccc; border-radius: 6px;
        }
        textarea { resize: vertical; }
        .radio-group label { display: block; margin: 10px 0; }
        .fee-summary { font-size: 16px; margin-top: 15px; }
        .fee-summary div { margin-bottom: 5px; }
        .total { font-weight: bold; font-size: 18px; }
        .payment-box {
            border: 1px solid #ccc; padding: 15px;
            margin-bottom: 15px; border-radius: 6px;
            background: #fafafa;
        }
        .payment-box img { height: 22px; vertical-align: middle; margin-right: 5px; }
        .terms { margin: 25px 0 15px; }
        .terms label { font-weight: normal; }
        button {
            background: green; color: #fff;
            padding: 12px 25px; border: none;
            border-radius: 6px; cursor: pointer;
            font-size: 16px;
        }
        button:hover { background: darkgreen; }
        .btn-row { text-align: center; margin-top: 20px; }
        .btn-row a {
            margin-left: 15px; color: #007bff;
            text-decoration: none; font-weight: 500;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td {
            border: 1px solid #ddd; padding: 10px; text-align: center;
        }
        th { background: #f0f0f0; }
    </style>
      <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="home product.css" />
    <link rel="stylesheet" href="laptop.css" />
       <link rel="stylesheet" href="laptop.css" />
   
  
    <script src="script.js" defer></script>
    <script src="wishlist.js"></script>

     
  
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <link rel="stylesheet" href="footer.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Header -->
  <div class="top-header">
    <div class="contact-left">
       <span>Email: computer@technoshop.com</span> | 
      <span>Contact: 0771387684</span>
    </div>
    <div class="nav-right">
      <a href="index.php">Home</a>
      <a href="about.php">About Us</a>
      <a href="bank details.php">Bank Details</a>
      <a href="contact-us.php">Contact Us</a>
    </div>
  </div>

  <!-- Navigation Bar -->
  <div class="navbar">
    <div class="logo">
      <img src="logo.png"alt="Computer Shop" />
    </div>
<form id="searchForm" method="GET" action="#" class="search-bar" style="display:flex; gap:5px; align-items:center;">
  <select name="category" id="categorySelect" class="category-select" required>
    <option value="">All Categories</option>
    <optgroup label="Peripherals">
      <option value="peripherals.php">Headphones</option>
      <option value="peripherals.php">Powerbank</option>
      <option value="peripherals.php">Mouse</option>
      <option value="peripherals.php">Keyboard</option>
      <option value="peripherals.php">Mouse Mat</option>
      <option value="peripherals.php">UPS</option>
    </optgroup>
    <optgroup label="Laptops">
      <option value="hp_laptop.php">HP</option>
      <option value="laptops.php">Asus</option>
      <option value="laptops.php">MSI</option>
      <option value="laptops.php">Dell</option>
      <option value="laptops.php">Lenovo</option>
      <option value="laptops.php">Acer</option> 
    </optgroup>
    <optgroup label="computer_components.php" label="Computer Components">
      <option value="computer_components.php">Memory</option>
      <option value="computer_components.php">Storage</option>
      <option value="computer_components.php">Computer Cases</option>
      <option value="computer_components.php">Motherboards</option>
      <option value="computer_components.php">Monitors</option>
      <option value="processors.php">Processors</option>
    </optgroup>
    <optgroup label="gaming.php" label="Gaming">
      <option value="gaming.php">Mouse</option>
      <option value="gaming.php">Desktop</option>
      <option value="gaming.php">Laptop</option>
      <option value="gaming.php">Gaming Chair</option>
    </optgroup>
    <optgroup label="printers.php" label="Printers & Scanners">
      <option value="printers.php">Printers</option>
      <option value="printers.php">Scanners</option>
    </optgroup>
  </select>
  <input type="text" name="search" id="searchInput" placeholder="Search products..." />
  <button type="submit"><i class="fas fa-search"></i></button>
</form>
    <div class="icons">
<?php if (isset($_SESSION['username'])): ?>
    <span>Welcome, <a href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>!</span>
    <a href="logout.php">Logout</a>
<?php else: ?>
    <a href="login.html">Login</a> | <a href="register.html">Register</a>
<?php endif; ?>


        
<a href="wishlist_page.php" class="wishlist-icon">
  ❤ <span id="wishlist-count">0</span>
</a>

      
<a href="cart.php" style="position: relative; display: inline-block; text-decoration: none;">
    🛒
    <span style="
        background: red;
        color: white;
        font-weight: bold;
        font-size: 12px;
        border-radius: 50%;
        padding: 2px 7px;
        position: absolute;
        top: -10px;
        right: -15px;
        min-width: 20px;
        text-align: center;
        display: inline-block;
    ">
        <?= $cart_count ?>
    </span>
</a>



</form>   

    </div>
  </div>

<!-- Category Mega Menu with Hotline -->
<div class="category-mega-bar">
  <div class="mega-menu-wrapper">
    <ul class="mega-menu">
      <li>
        <a href="laptops.php">Laptops</a>
        <ul class="submenu">
          <li><a href="hp_laptop.php">HP</a></li>
          <li><a href="#">Asus</a></li>
          <li><a href="#">MSI</a></li>
          <li><a href="#">Dell</a></li>
          <li><a href="#">Lenovo</a></li>
          <li><a href="#">Acer</a></li>
        </ul>
      </li>
      <li><a href="desktops.php">Desktops & Servers</a></li>
      <li>
        <a href="computer_components.php">Computer Components</a>
        <ul class="submenu">
          <li><a href="#">Memory</a></li>
          <li><a href="#">Storage</a></li>
          <li><a href="#">Computer Cases</a></li>
          <li><a href="#">Motherboards</a></li>
          <li><a href="#">Monitors</a></li>

      <li><a href="processors.php">Processors</a></li>
        </ul>
      </li>
      <li>
        <a href="gaming.php">Gaming</a>
        <ul class="submenu">
          <li><a href="#">Mouse</a></li>
          <li><a href="#">Desktop</a></li>
          <li><a href="#">Laptop</a></li>
          <li><a href="#">Gaming Chair</a></li>
        </ul>
      </li>
      <li><a href="printers.php">Printers & Scanners</a></li>
      <li>
        <a href="peripherals.php">Peripherals</a>
        <ul class="submenu">
          <li><a href="#">Headphones</a></li>
          <li><a href="#">Powerbank</a></li>
          <li><a href="#">Mouse</a></li>
          <li><a href="#">Keyboard</a></li>
          <li><a href="#">Mouse Mat</a></li>
          <li><a href="#">UPS</a></li>
        </ul>
      </li>
    </ul>
<div class="hotline">
  <strong>Hotline:</strong> 
  <a href="tele:+771387684">
    <span class="phone-icon">📞</span> +771387684
  </a>
</div>

  </div>
</div>
<h2>Checkout</h2>

        <!-- Order Summary -->
        <div class="section">
            <h3>Order Summary</h3>
            <table>
                <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>Rs.<?= number_format($item['product_price'], 2) ?></td>
                        <td>Rs.<?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Convenience Fee</strong></td>
                    <td>Rs.<?= number_format($convenience_fee, 2) ?></td>
                </tr>
                <tr class="total">
                    <td colspan="3">Total</td>
                    <td>Rs.<?= number_format($final_total, 2) ?></td>
                </tr>
            </table>
        </div>

<div class="checkout-container">
    <form method="post">
        <div class="flex-row">
            <!-- Billing Details (Left) -->
            <div class="left section">
                <h3>Billing Details</h3>
                <label>Full Name</label>
                <input type="text" name="full_name" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Phone</label>
                <input type="text" name="phone" required>

                <label>Address</label>
                <textarea name="address" rows="4" required></textarea>
            </div>

            <!-- Shipping & Payment (Right) -->
            <div class="right">
                <div class="section">
                    <h3>Shipping</h3>
                    <div class="radio-group">
                        <label><input type="radio" name="shipping" value="pickup" checked> Store Pickup</label>
                        <label><input type="radio" name="shipping" value="delivery"> Delivery (Charges will be applicable)</label>
                    </div>

                    <div class="fee-summary">
                        <div>Convenience Fee: Rs. <?= number_format($convenience_fee, 2) ?></div>
                        <div class="total">Total: Rs. <?= number_format($final_total, 2) ?></div>
                    </div>
                </div>

                <div class="section">
                    <h3>Payment Method</h3>

                    <div class="payment-box">
                        <label>
                            <input type="radio" name="payment_method" value="card" checked>
                            <img src="https://img.icons8.com/color/48/000000/mastercard-logo.png"/>
                            <img src="https://img.icons8.com/color/48/000000/visa.png"/>
                            Online Pay Credit/Debit Card
                        </label>
                        <p>This is a secure purchase through Paycorp Payment Gateway.<br>
                        Standard Delivery 3 to 5 Days <strong>* Delivery charges will be applicable</strong></p>
                    </div>

                    <div class="payment-box">
                        <label>
                            <input type="radio" name="payment_method" value="koko">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Koko_Logo.png/320px-Koko_Logo.png" style="height:22px;">
                            Koko: Buy Now Pay Later
                        </label>
                    </div>
                </div>
            </div>
        </div>


        <!-- Terms -->
        <div class="terms">
            <label><input type="checkbox" required> I have read and agree to the website <a href="#">terms and conditions</a></label>
        </div>

        <!-- Buttons -->
        <div class="btn-row">
            <button type="submit">Place Order</button>
            <a href="cart.php">← Back to Cart</a>
        </div>
    </form>
</div>


  <?php include 'footer.php'; ?>


 


<script>
  document.querySelectorAll('.add-to-wishlist-btn').forEach(button => {
    button.addEventListener('click', function () {
      const product = {
        id: this.dataset.productId,
        name: this.dataset.productName,
        price: this.dataset.productPrice,
        image: this.dataset.productImage
      };

      // Send to server via fetch/AJAX or store in localStorage/session
      console.log("Wishlist Product:", product);

      alert(`${product.name} added to wishlist!`);
    });
  });
</script>




<script>
function updateCartCount() {
  fetch('get_cart_count.php')
    .then(response => response.text())
    .then(count => {
      document.getElementById('cart-count').innerText = count;
    });
}

// Load when page loads
updateCartCount();

// Optional: Refresh every 30 seconds
setInterval(updateCartCount, 30000);
</script>

<script>
  document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault(); // prevent normal form submit

    const categorySelect = document.getElementById('categorySelect');
    const searchInput = document.getElementById('searchInput');

    const categoryPage = categorySelect.value.trim();
    const searchQuery = searchInput.value.trim();

    if (categoryPage && !searchQuery) {
      // If category selected and no search query, redirect to category page
      window.location.href = categoryPage;
    } else if (searchQuery) {
      // If there's a search query, you can redirect to a search handler or perform search here.
      // For now, redirect to a search results page with query params
      // You can create 'search.php' to handle this later
      const url = new URL(window.location.origin + '/search.php');
      url.searchParams.append('q', searchQuery);
      if (categoryPage) url.searchParams.append('category', categoryPage);
      window.location.href = url.toString();
    } else {
      // Neither category nor search, do nothing or alert
      alert('Please select a category or enter a search term.');
    }
  });
</script>

</body>
</html>
