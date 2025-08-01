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
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Computer Shop</title>
  <link rel="stylesheet" href="style.css" />
 <link rel="stylesheet" href="footer.css" />
  <script src="script.js" defer></script>
    <script src="wishlist.js" defer></script>
 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 20px;
}
.product-card {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 15px;
  text-align: center;
  transition: 0.3s;
}
.product-card:hover {
  transform: scale(1.03);
}
.product-card img {
  width: 100%;
  height: auto;
  margin-bottom: 10px;
}

    .product-icons {
  margin-top: 10px;
  display: flex;
  justify-content: center;
  gap: 10px;
}

.add-to-cart-btn,
.add-to-wishlist-btn{
  background:rgba(0, 0, 0, 0);
  color: black;
  border: none;
  padding: 6px 10px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  text-decoration: none;
}


.buy-now-btn {
  background:rgb(36, 102, 27);
  color: white;
  border: none;
  padding: 6px 10px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  text-decoration: none;
}

.add-to-cart-btn:hover,
.add-to-wishlist-btn:hover,
.buy-now-btn:hover {
  background:rgba(36, 162, 55, 0.83);
}
.video-banner-section {
  position: relative;
  width: 100%;
  height: 350px;
  overflow: hidden;
  margin: 40px 0;
}

.video-banner {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  color: white;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 20px;
}

.video-overlay h2 {
  font-size: 32px;
  margin-bottom: 10px;
}

.video-overlay p {
  font-size: 18px;
  margin-bottom: 20px;
}

.banner-btn {
  display: inline-block;
  padding: 10px 20px;
  background: #ffc107;
  color: black;
  border-radius: 5px;
  text-decoration: none;
  font-weight: bold;
}

</style>
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


<!-- ===================== Printers Section ===================== -->
<section class="printers-section">
  <h2 style="text-align:center;">Printers</h2>
  <div class="product-grid">

    <!-- Printer Product 1 -->
    <div class="product-card">
      <a href="product3000.php"><img src="image/printer1.2.png" alt="HP LaserJet Pro M404dn"></a><br><br>
      <h3>HP LaserJet Pro M404dn</h3>
      <p>Rs. 62,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3000">
          <input type="hidden" name="product_name" value="HP LaserJet Pro M404dn">
          <input type="hidden" name="product_price" value="62000">
          <input type="hidden" name="product_image" value="image/printer1.2.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3000"
          data-product-name="HP LaserJet Pro M404dn"
          data-product-price="62000"
          data-product-image="image/printer1.2.png">❤</button>
        <a href="buy-now.php?id=3000" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Printer Product 2 -->
    <div class="product-card">
      <a href="product3001.php"><img src="image/printer2.png" alt="Canon PIXMA G2020"></a><br><br><br>
      <h3>Canon PIXMA G2020</h3>
      <p>Rs. 45,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3001">
          <input type="hidden" name="product_name" value="Canon PIXMA G2020">
          <input type="hidden" name="product_price" value="45000">
          <input type="hidden" name="product_image" value="image/printer2.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3001"
          data-product-name="Canon PIXMA G2020"
          data-product-price="45000"
          data-product-image="image/printer2.png">❤</button>
        <a href="buy-now.php?id=3001" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Printer Product 3 -->
    <div class="product-card">
      <a href="product3002.php"><img src="image/printer3.png" alt="Epson EcoTank L3250"></a><br><br>
      <h3>Epson EcoTank L3250</h3>
      <p>Rs. 52,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3002">
          <input type="hidden" name="product_name" value="Epson EcoTank L3250">
          <input type="hidden" name="product_price" value="52500">
          <input type="hidden" name="product_image" value="image/printer3.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3002"
          data-product-name="Epson EcoTank L3250"
          data-product-price="52500"
          data-product-image="image/printer3.png">❤</button>
        <a href="buy-now.php?id=3002" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Printer Product 4 -->
    <div class="product-card">
      <a href="product3003.php"><img src="image/printer4.png" alt="Brother HL-L2350DW"></a><br><br><br><br>
      <h3>Brother HL-L2350DW</h3>
      <p>Rs. 38,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3003">
          <input type="hidden" name="product_name" value="Brother HL-L2350DW">
          <input type="hidden" name="product_price" value="38000">
          <input type="hidden" name="product_image" value="image/printer4.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3003"
          data-product-name="Brother HL-L2350DW"
          data-product-price="38000"
          data-product-image="image/printer4.png">❤</button>
        <a href="buy-now.php?id=3003" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Printer Product 5 -->
    <div class="product-card">
      <a href="product3004.php"><img src="image/printer5.png" alt="HP DeskJet Ink Advantage 2776"></a>
      <h3>HP DeskJet Ink Advantage 2776</h3>
      <p>Rs. 17,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3004">
          <input type="hidden" name="product_name" value="HP DeskJet Ink Advantage 2776">
          <input type="hidden" name="product_price" value="17000">
          <input type="hidden" name="product_image" value="image/printer5.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3004"
          data-product-name="HP DeskJet Ink Advantage 2776"
          data-product-price="17000"
          data-product-image="image/printer5.png">❤</button>
        <a href="buy-now.php?id=3004" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Printer Product 6 -->
    <div class="product-card">
      <a href="product3005.php"><img src="image/printer6.png" alt="Canon i-SENSYS LBP6030B"></a>
      <h3>Canon i-SENSYS LBP6030B</h3>
      <p>Rs. 30,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3005">
          <input type="hidden" name="product_name" value="Canon i-SENSYS LBP6030B">
          <input type="hidden" name="product_price" value="30000">
          <input type="hidden" name="product_image" value="image/printer6.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3005"
          data-product-name="Canon i-SENSYS LBP6030B"
          data-product-price="30000"
          data-product-image="image/printer6.png">❤</button>
        <a href="buy-now.php?id=3005" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Printer Product 7 -->
    <div class="product-card">
      <a href="product3006.php"><img src="image/printer7.png" alt="Epson L121 Inkjet"></a>
      <h3>Epson L121 Inkjet</h3>
      <p>Rs. 35,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3006">
          <input type="hidden" name="product_name" value="Epson L121 Inkjet">
          <input type="hidden" name="product_price" value="35000">
          <input type="hidden" name="product_image" value="image/printer7.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3006"
          data-product-name="Epson L121 Inkjet"
          data-product-price="35000"
          data-product-image="image/printer7.png">❤</button>
        <a href="buy-now.php?id=3006" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Printer Product 8 -->
    <div class="product-card">
      <a href="product3007.php"><img src="image/printer8.png" alt="Brother DCP-T420W"></a>
      <h3>Brother DCP-T420W</h3>
      <p>Rs. 44,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3007">
          <input type="hidden" name="product_name" value="Brother DCP-T420W">
          <input type="hidden" name="product_price" value="44500">
          <input type="hidden" name="product_image" value="image/printer8.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3007"
          data-product-name="Brother DCP-T420W"
          data-product-price="44500"
          data-product-image="image/printer8.png">❤</button>
        <a href="buy-now.php?id=3007" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Printer Product 9 -->
    <div class="product-card">
      <a href="product3008.php"><img src="image/printer9.png" alt="HP Smart Tank 515"></a><br><br>
      <h3>HP Smart Tank 515</h3>
      <p>Rs. 58,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3008">
          <input type="hidden" name="product_name" value="HP Smart Tank 515">
          <input type="hidden" name="product_price" value="58000">
          <input type="hidden" name="product_image" value="image/printer9.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3008"
          data-product-name="HP Smart Tank 515"
          data-product-price="58000"
          data-product-image="image/printer9.png">❤</button>
        <a href="buy-now.php?id=3008" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Printer Product 10 -->
    <div class="product-card">
      <a href="product3009.php"><img src="image/printer10.png" alt="Canon PIXMA TS207"></a><br><br>
      <h3>Canon PIXMA TS207</h3>
      <p>Rs. 14,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="3009">
          <input type="hidden" name="product_name" value="Canon PIXMA TS207">
          <input type="hidden" name="product_price" value="14000">
          <input type="hidden" name="product_image" value="image/printer10.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="3009"
          data-product-name="Canon PIXMA TS207"
          data-product-price="14000"
          data-product-image="image/printer10.png">❤</button>
        <a href="buy-now.php?id=3009" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

  </div>
</section>



<!-- ========== Video Banner Section ========== -->
<section class="video-banner-section">
  <video autoplay muted loop playsinline class="video-banner">
    <source src="video/tech-banner.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>

</section>

<!-- ===================== Scanners Section ===================== -->
<section class="scanners-section">
  <h2 style="text-align:center;">Scanners</h2>
  <div class="product-grid">
  
    <!-- Scanner 4000 -->
    <div class="product-card">
      <a href="product4000.php"><img src="image/scanner1.png" alt="Canon CanoScan LiDE 300" /></a><br /><br /><br /><br />
      <h3>Canon CanoScan LiDE 300</h3>
      <p>Rs. 19,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4000" />
          <input type="hidden" name="product_name" value="Canon CanoScan LiDE 300" />
          <input type="hidden" name="product_price" value="19500" />
          <input type="hidden" name="product_image" value="image/scanner1.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4000"
          data-product-name="Canon CanoScan LiDE 300"
          data-product-price="19500"
          data-product-image="image/scanner1.png">❤</button>
        <a href="buy-now.php?id=4000" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Scanner 4001 -->
    <div class="product-card">
      <a href="product4001.php"><img src="image/scanner2.png" alt="Epson Perfection V39" /></a>
      <h3>Epson Perfection V39</h3>
      <p>Rs. 24,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4001" />
          <input type="hidden" name="product_name" value="Epson Perfection V39" />
          <input type="hidden" name="product_price" value="24000" />
          <input type="hidden" name="product_image" value="image/scanner2.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4001"
          data-product-name="Epson Perfection V39"
          data-product-price="24000"
          data-product-image="image/scanner2.png">❤</button>
        <a href="buy-now.php?id=4001" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Scanner 4002 -->
    <div class="product-card">
      <a href="product4002.php"><img src="image/scanner3.png" alt="HP ScanJet Pro 2500 f1" /></a><br /><br /><br /><br /><br />
      <h3>HP ScanJet Pro 2500 f1</h3>
      <p>Rs. 47,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4002" />
          <input type="hidden" name="product_name" value="HP ScanJet Pro 2500 f1" />
          <input type="hidden" name="product_price" value="47000" />
          <input type="hidden" name="product_image" value="image/scanner3.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4002"
          data-product-name="HP ScanJet Pro 2500 f1"
          data-product-price="47000"
          data-product-image="image/scanner3.png">❤</button>
        <a href="buy-now.php?id=4002" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Scanner 4003 -->
    <div class="product-card">
      <a href="product4003.php"><img src="image/scanner4.png" alt="Plustek ePhoto Z300" /></a><br /><br /><br />
      <h3>Plustek ePhoto Z300</h3>
      <p>Rs. 32,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4003" />
          <input type="hidden" name="product_name" value="Plustek ePhoto Z300" />
          <input type="hidden" name="product_price" value="32500" />
          <input type="hidden" name="product_image" value="image/scanner4.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4003"
          data-product-name="Plustek ePhoto Z300"
          data-product-price="32500"
          data-product-image="image/scanner4.png">❤</button>
        <a href="buy-now.php?id=4003" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Scanner 4004 -->
    <div class="product-card">
      <a href="product4004.php"><img src="image/scanner5.png" alt="Brother ADS-2200" /></a><br /><br /><br /><br />
      <h3>Brother ADS-2200</h3>
      <p>Rs. 59,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4004" />
          <input type="hidden" name="product_name" value="Brother ADS-2200" />
          <input type="hidden" name="product_price" value="59000" />
          <input type="hidden" name="product_image" value="image/scanner5.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4004"
          data-product-name="Brother ADS-2200"
          data-product-price="59000"
          data-product-image="image/scanner5.png">❤</button>
        <a href="buy-now.php?id=4004" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Scanner 4005 -->
    <div class="product-card">
      <a href="product4005.php"><img src="image/scanner6.1.png" alt="Fujitsu ScanSnap iX1600" /></a><br /><br /><br />
      <h3>Fujitsu ScanSnap iX1600</h3>
      <p>Rs. 85,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4005" />
          <input type="hidden" name="product_name" value="Fujitsu ScanSnap iX1600" />
          <input type="hidden" name="product_price" value="85000" />
          <input type="hidden" name="product_image" value="image/scanner6.1.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4005"
          data-product-name="Fujitsu ScanSnap iX1600"
          data-product-price="85000"
          data-product-image="image/scanner6.1.png">❤</button>
        <a href="buy-now.php?id=4005" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Scanner 4006 -->
    <div class="product-card">
      <a href="product4006.php"><img src="image/scanner7.png" alt="Canon DR-F120" /></a>
      <h3>Canon DR-F120</h3>
      <p>Rs. 68,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4006" />
          <input type="hidden" name="product_name" value="Canon DR-F120" />
          <input type="hidden" name="product_price" value="68000" />
          <input type="hidden" name="product_image" value="image/scanner7.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4006"
          data-product-name="Canon DR-F120"
          data-product-price="68000"
          data-product-image="image/scanner7.png">❤</button>
        <a href="buy-now.php?id=4006" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Scanner 4007 -->
    <div class="product-card">
      <a href="product4007.php"><img src="image/scanner8.png" alt="Epson DS-1630" /></a><br /><br /><br /><br />
      <h3>Epson DS-1630</h3>
      <p>Rs. 49,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4007" />
          <input type="hidden" name="product_name" value="Epson DS-1630" />
          <input type="hidden" name="product_price" value="49000" />
          <input type="hidden" name="product_image" value="image/scanner8.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4007"
          data-product-name="Epson DS-1630"
          data-product-price="49000"
          data-product-image="image/scanner8.png">❤</button>
        <a href="buy-now.php?id=4007" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Scanner 4008 -->
    <div class="product-card">
      <a href="product4008.php"><img src="image/scanner9.png" alt="Brother DS-640 Mobile" /></a><br /><br /><br /><br /><br /><br />
      <h3>Brother DS-640 Mobile</h3>
      <p>Rs. 28,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4008" />
          <input type="hidden" name="product_name" value="Brother DS-640 Mobile" />
          <input type="hidden" name="product_price" value="28000" />
          <input type="hidden" name="product_image" value="image/scanner9.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4008"
          data-product-name="Brother DS-640 Mobile"
          data-product-price="28000"
          data-product-image="image/scanner9.png">❤</button>
        <a href="buy-now.php?id=4008" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Scanner 4009 -->
    <div class="product-card">
      <a href="product4009.php"><img src="image/scanner10.png" alt="Plustek OpticSlim 1180" /></a><br /><br /><br /><br />
      <h3>Plustek OpticSlim 1180</h3>
      <p>Rs. 65,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="4009" />
          <input type="hidden" name="product_name" value="Plustek OpticSlim 1180" />
          <input type="hidden" name="product_price" value="65000" />
          <input type="hidden" name="product_image" value="image/scanner10.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="4009"
          data-product-name="Plustek OpticSlim 1180"
          data-product-price="65000"
          data-product-image="image/scanner10.png">❤</button>
        <a href="buy-now.php?id=4009" class="buy-now-btn">Buy Now</a>
      </div>
    </div>
  </div>
</section>



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

<?php include 'footer.php'; ?>

</body>
</html>