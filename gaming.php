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
.video-banner {
  position: relative;
  height: 450px;
  overflow: hidden;
}

.video-banner video {
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
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: white;
  text-align: center;
  padding: 0 20px;
}

.video-overlay h1 {
  font-size: 40px;
  margin-bottom: 10px;
}

.video-overlay p {
  font-size: 18px;
  margin-bottom: 20px;
}

.video-overlay .btn {
  background-color: #ffcc00;
  color: #000;
  padding: 12px 28px;
  text-decoration: none;
  font-weight: bold;
  border-radius: 5px;
  transition: background 0.3s ease;
}

.video-overlay .btn:hover {
  background-color: #e6b800;
}
.gaming-section {
  max-width: 1500px;
  margin: 40px auto;
  padding: 20px;
  font-family: Arial, sans-serif;
}
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
/* Section Title */
.section-title {
  text-align: center;
  font-size: 28px;
  margin: 40px 0 20px;
}

/* Gaming Product Grid */
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 20px;
  padding: 0 20px;
}

.product-card {
  background: #fff;
  padding: 15px;
  text-align: center;
  border: 1px solid #eee;
  border-radius: 10px;
  transition: box-shadow 0.3s ease;
}

.product-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  margin-bottom: 10px;
}

.product-card h3 {
  font-size: 18px;
  margin: 10px 0 5px;
}

.product-card p {
  font-size: 16px;
  color: #333;
  margin-bottom: 10px;
}

.product-card .btn {
  background-color: #ffcc00;
  color: #000;
  padding: 8px 16px;
  text-decoration: none;
  border-radius: 5px;
  font-weight: bold;
}

.product-card .btn:hover {
  background-color: #e6b800;
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

<section class="video-banner">
  <video autoplay muted loop playsinline>
    <source src="video/banner.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <div class="video-overlay">
    <h1>Unleash the Power of Gaming</h1>
    <p>Explore the latest gaming gear at unbeatable prices</p>
    <a href="gaming-products.php" class="btn">Shop Now</a>
  </div>
</section>



<section class="gaming-section">
  <h2 style="text-align:center;">Gaming Products</h2><br>

  <div class="product-grid">
    <!-- Product 1 -->
    <div class="product-card">
      <a href="product2000.php"><img src="image/g1.png" alt="Asus ROG Strix G15"></a><br><br><br>
      <h3>Asus ROG Strix G15</h3>
      <p>Rs. 345,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2000" />
          <input type="hidden" name="product_name" value="Asus ROG Strix G15" />
          <input type="hidden" name="product_price" value="345000" />
          <input type="hidden" name="product_image" value="image/g1.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2000" data-product-name="Asus ROG Strix G15" data-product-price="345000" data-product-image="image/g1.png">❤</button>
        <a href="buy-now.php?id=2000" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 2 -->
    <div class="product-card">
      <a href="product2001.php"><img src="image/g2.png" alt="Alienware Aurora R15"></a><br><br><br>
      <h3>Alienware Aurora R15</h3>
      <p>Rs. 520,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2001" />
          <input type="hidden" name="product_name" value="Alienware Aurora R15" />
          <input type="hidden" name="product_price" value="520000" />
          <input type="hidden" name="product_image" value="image/g2.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2001" data-product-name="Alienware Aurora R15" data-product-price="520000" data-product-image="image/g2.png">❤</button>
        <a href="buy-now.php?id=2001" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 3 -->
    <div class="product-card">
      <a href="product2002.php"><img src="image/g3.png" alt="Razer Blade 16"></a><br><br><br>
      <h3>Razer Blade 16</h3>
      <p>Rs. 615,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2002" />
          <input type="hidden" name="product_name" value="Razer Blade 16" />
          <input type="hidden" name="product_price" value="615000" />
          <input type="hidden" name="product_image" value="image/g3.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2002" data-product-name="Razer Blade 16" data-product-price="615000" data-product-image="image/g3.png">❤</button>
        <a href="buy-now.php?id=2002" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 4 -->
    <div class="product-card">
      <a href="product2003.php"><img src="image/g4.png" alt="Logitech G502 HERO"></a><br><br><br>
      <h3>Logitech G502 HERO</h3>
      <p>Rs. 18,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2003" />
          <input type="hidden" name="product_name" value="Logitech G502 HERO" />
          <input type="hidden" name="product_price" value="18500" />
          <input type="hidden" name="product_image" value="image/g4.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2003" data-product-name="Logitech G502 HERO" data-product-price="18500" data-product-image="image/g4.png">❤</button>
        <a href="buy-now.php?id=2003" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 5 -->
    <div class="product-card">
      <a href="product2004.php"><img src="image/g5.png" alt="Redragon K552 Keyboard"></a><br>
      <h3>Redragon K552 RGB Keyboard</h3>
      <p>Rs. 16,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2004" />
          <input type="hidden" name="product_name" value="Redragon K552 RGB Keyboard" />
          <input type="hidden" name="product_price" value="16000" />
          <input type="hidden" name="product_image" value="image/g5.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2004" data-product-name="Redragon K552 RGB Keyboard" data-product-price="16000" data-product-image="image/g5.png">❤</button>
        <a href="buy-now.php?id=2004" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 6 -->
    <div class="product-card">
      <a href="product2005.php"><img src="image/g6.png" alt="MSI Curved Monitor 27&quot;"></a><br><br>
      <h3>MSI Curved Gaming Monitor 27"</h3>
      <p>Rs. 92,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2005" />
          <input type="hidden" name="product_name" value="MSI Curved Gaming Monitor" />
          <input type="hidden" name="product_price" value="92000" />
          <input type="hidden" name="product_image" value="image/g6.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2005" data-product-name="MSI Curved Gaming Monitor" data-product-price="92000" data-product-image="image/g6.png">❤</button>
        <a href="buy-now.php?id=2005" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 7 -->
    <div class="product-card">
      <a href="product2006.php"><img src="image/g7.png" alt="Razer Kraken V3"></a><br><br><br>
      <h3>Razer Kraken V3 Headset</h3>
      <p>Rs. 21,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2006" />
          <input type="hidden" name="product_name" value="Razer Kraken V3 Headset" />
          <input type="hidden" name="product_price" value="21000" />
          <input type="hidden" name="product_image" value="image/g7.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2006" data-product-name="Razer Kraken V3 Headset" data-product-price="21000" data-product-image="image/g7.png">❤</button>
        <a href="buy-now.php?id=2006" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 8 -->
    <div class="product-card">
      <a href="product2007.php"><img src="image/g8.png" alt="NVIDIA RTX 4070"></a><br><br><br>
      <h3>NVIDIA GeForce RTX 4070</h3>
      <p>Rs. 185,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2007" />
          <input type="hidden" name="product_name" value="NVIDIA GeForce RTX 4070" />
          <input type="hidden" name="product_price" value="185000" />
          <input type="hidden" name="product_image" value="image/g8.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2007" data-product-name="NVIDIA GeForce RTX 4070" data-product-price="185000" data-product-image="image/g8.png">❤</button>
        <a href="buy-now.php?id=2007" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 9 -->
    <div class="product-card"><br>
      <a href="product2008.php"><img src="image/g9.png" alt="HyperX Cloud II"></a><br><br>
      <h3>HyperX Cloud II Wireless</h3>
      <p>Rs. 24,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2008" />
          <input type="hidden" name="product_name" value="HyperX Cloud II Wireless" />
          <input type="hidden" name="product_price" value="24500" />
          <input type="hidden" name="product_image" value="image/g9.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2008" data-product-name="HyperX Cloud II Wireless" data-product-price="24500" data-product-image="image/g9.png">❤</button>
        <a href="buy-now.php?id=2008" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 10 -->
    <div class="product-card">
      <a href="product2009.php"><img src="image/g10.png" alt="Gaming Chair Cougar Armor"></a><br><br>
      <h3>Cougar Armor Gaming Chair</h3>
      <p>Rs. 48,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="2009" />
          <input type="hidden" name="product_name" value="Cougar Armor Gaming Chair" />
          <input type="hidden" name="product_price" value="48000" />
          <input type="hidden" name="product_image" value="image/g10.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="2009" data-product-name="Cougar Armor Gaming Chair" data-product-price="48000" data-product-image="image/g10.png">❤</button>
        <a href="buy-now.php?id=2009" class="buy-now-btn">Buy Now</a>
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