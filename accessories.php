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

    .accessories-section {
  padding: 40px 20px;
  background-color: #f9f9f9;
}

.section-title {
  text-align: center;
  font-size: 28px;
  margin-bottom: 30px;
}

.accessories-container {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
}

.accessory-products {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  flex: 2;
}

.product-card {
  background: #fff;
  padding: 8px;
  border-radius: 8px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.product-card img {
  max-width: 70%;
  height: auto;
}

.product-card h3 {
  margin: 10px 0 5px;
  font-size: 16px;
}

.product-card p {
  color: #333;
  font-weight: bold;
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

.accessory-banner {
  flex: 2;
  display: flex;
  align-items: center;
  justify-content: center;
}


.accessory-banner img {
  width: 100%;
  height: 700px; /* Set desired height */
  object-fit: cover; /* Keeps image nicely cropped */
  border-radius: 5px;
}

/* Accessories Section */
.accessories-section {
  padding: 40px 20px;
  background-color: #f9f9f9;
}

.section-title {
  text-align: center;
  font-size: 28px;
  color: #333;
  margin-bottom: 30px;
  font-weight: bold;
}

.accessories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  padding: 0 10px;
}

/* Product Card */
.product-card {
  background: #fff;
  border-radius: 8px;
  padding: 15px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}


.product-card h3 {
  font-size: 16px;
  margin: 10px 0 5px;
  color: #222;
}

.product-card p {
  font-weight: bold;
  color: #444;
  margin-bottom: 10px;
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

<!-- ======= Accessories Product Section ======= -->
<section class="accessories-section">
  <h2 class="section-title">Latest Accessories</h2>

  <div class="accessories-container">
    <!-- Left: Product Grid -->
    <div class="accessory-products">
      <!-- Product 1 -->
      <div class="product-card">
        <a href="product5001.php"><img src="image/acc1.png" alt="Wireless Mouse"></a>
        <h3>Wireless Mouse</h3>
        <p>Rs. 2,500</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="5001">
            <input type="hidden" name="product_name" value="Wireless Mouse">
            <input type="hidden" name="product_price" value="2500">
            <input type="hidden" name="product_image" value="image/acc1.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="5001"
            data-product-name="Wireless Mouse"
            data-product-price="2500"
            data-product-image="image/acc1.png">❤</button>
          <a href="buy-now.php?id=5001" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 2 -->
      <div class="product-card">
        <a href="product5002.php"><img src="image/acc2.png" alt="Laptop Cooling Pad"></a>
        <h3>Laptop Cooling Pad</h3>
        <p>Rs. 3,800</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="5002">
            <input type="hidden" name="product_name" value="Laptop Cooling Pad">
            <input type="hidden" name="product_price" value="3800">
            <input type="hidden" name="product_image" value="image/acc2.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="5002"
            data-product-name="Laptop Cooling Pad"
            data-product-price="3800"
            data-product-image="image/acc2.png">❤</button>
          <a href="buy-now.php?id=5002" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 3 -->
      <div class="product-card">
        <a href="product5003.php"><img src="image/acc3.png" alt="USB Hub 4-Port"></a><br><br><br><br>
        <h3>USB Hub 4-Port</h3>
        <p>Rs. 1,900</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="5003">
            <input type="hidden" name="product_name" value="USB Hub 4-Port">
            <input type="hidden" name="product_price" value="1900">
            <input type="hidden" name="product_image" value="image/acc3.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="5003"
            data-product-name="USB Hub 4-Port"
            data-product-price="1900"
            data-product-image="image/acc3.png">❤</button>
          <a href="buy-now.php?id=5003" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 4 -->
      <div class="product-card">
        <a href="product5004.php"><img src="image/acc4.1.png" alt="Gaming Headset"></a>
        <h3>Gaming Headset</h3>
        <p>Rs. 5,500</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="5004">
            <input type="hidden" name="product_name" value="Gaming Headset">
            <input type="hidden" name="product_price" value="5500">
            <input type="hidden" name="product_image" value="image/acc4.1.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="5004"
            data-product-name="Gaming Headset"
            data-product-price="5500"
            data-product-image="image/acc4.1.png">❤</button>
          <a href="buy-now.php?id=5004" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 5 -->
      <div class="product-card">
        <a href="product5005.php"><img src="image/acc5.png" alt="Bluetooth Speaker"></a>
        <h3>Bluetooth Speaker</h3>
        <p>Rs. 4,200</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="5005">
            <input type="hidden" name="product_name" value="Bluetooth Speaker">
            <input type="hidden" name="product_price" value="4200">
            <input type="hidden" name="product_image" value="image/acc5.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="5005"
            data-product-name="Bluetooth Speaker"
            data-product-price="4200"
            data-product-image="image/acc5.png">❤</button>
          <a href="buy-now.php?id=5005" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 6 -->
      <div class="product-card">
        <a href="product5006.php"><img src="image/acc6.png" alt="Webcam 1080p HD"></a><br><br>
        <h3>Webcam 1080p HD</h3>
        <p>Rs. 3,200</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="5006">
            <input type="hidden" name="product_name" value="Webcam 1080p HD">
            <input type="hidden" name="product_price" value="3200">
            <input type="hidden" name="product_image" value="image/acc6.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="5006"
            data-product-name="Webcam 1080p HD"
            data-product-price="3200"
            data-product-image="image/acc6.png">❤</button>
          <a href="buy-now.php?id=5006" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
    </div>

    <!-- Right: Banner -->
    <div class="accessory-banner">
      <img src="image/acc_banner.png" alt="Mobile Accessories Banner">
    </div>
  </div>
</section>

<!-- ========== More Accessories Product Section ========== -->
<section class="accessories-section">
  <h2 class="section-title">More Accessories</h2>
  <div class="accessories-grid">

    <!-- Product 7 -->
    <div class="product-card">
      <a href="product5007.php"><img src="image/acc7.png" alt="Portable SSD"></a>
      <h3>Portable SSD 1TB</h3>
      <p>Rs. 12,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="5007">
          <input type="hidden" name="product_name" value="Portable SSD 1TB">
          <input type="hidden" name="product_price" value="12000">
          <input type="hidden" name="product_image" value="image/acc7.png">
          <button class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="5007"
          data-product-name="Portable SSD 1TB"
          data-product-price="12000"
          data-product-image="image/acc7.png">❤</button>
        <a href="buy-now.php?id=5007" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 8 -->
    <div class="product-card">
      <a href="product5008.php"><img src="image/acc8.png" alt="Stylus Pen"></a>
      <h3>Smart Stylus Pen</h3>
      <p>Rs. 3,200</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="5008">
          <input type="hidden" name="product_name" value="Smart Stylus Pen">
          <input type="hidden" name="product_price" value="3200">
          <input type="hidden" name="product_image" value="image/acc8.png">
          <button class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="5008"
          data-product-name="Smart Stylus Pen"
          data-product-price="3200"
          data-product-image="image/acc8.png">❤</button>
        <a href="buy-now.php?id=5008" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 9 -->
    <div class="product-card">
      <a href="product5009.php"><img src="image/acc9.png" alt="Wireless Charger"></a>
      <h3>Wireless Charger</h3>
      <p>Rs. 2,750</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="5009">
          <input type="hidden" name="product_name" value="Wireless Charger">
          <input type="hidden" name="product_price" value="2750">
          <input type="hidden" name="product_image" value="image/acc9.png">
          <button class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="5009"
          data-product-name="Wireless Charger"
          data-product-price="2750"
          data-product-image="image/acc9.png">❤</button>
        <a href="buy-now.php?id=5009" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 10 -->
    <div class="product-card">
      <a href="product5010.php"><img src="image/acc10.png" alt="USB-C Hub"></a>
      <h3>USB-C Multiport Hub</h3>
      <p>Rs. 4,800</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="5010">
          <input type="hidden" name="product_name" value="USB-C Multiport Hub">
          <input type="hidden" name="product_price" value="4800">
          <input type="hidden" name="product_image" value="image/acc10.png">
          <button class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="5010"
          data-product-name="USB-C Multiport Hub"
          data-product-price="4800"
          data-product-image="image/acc10.png">❤</button>
        <a href="buy-now.php?id=5010" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 11 -->
    <div class="product-card">
      <a href="product5011.php"><img src="image/acc11.png" alt="Laptop Stand"></a>
      <h3>Adjustable Laptop Stand</h3>
      <p>Rs. 3,950</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="5011">
          <input type="hidden" name="product_name" value="Adjustable Laptop Stand">
          <input type="hidden" name="product_price" value="3950">
          <input type="hidden" name="product_image" value="image/acc11.png">
          <button class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="5011"
          data-product-name="Adjustable Laptop Stand"
          data-product-price="3950"
          data-product-image="image/acc11.png">❤</button>
        <a href="buy-now.php?id=5011" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 12 -->
    <div class="product-card">
      <a href="product5012.php"><img src="image/acc12.png" alt="External DVD Drive"></a>
      <h3>External DVD Drive</h3>
      <p>Rs. 5,200</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="5012">
          <input type="hidden" name="product_name" value="External DVD Drive">
          <input type="hidden" name="product_price" value="5200">
          <input type="hidden" name="product_image" value="image/acc12.png">
          <button class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="5012"
          data-product-name="External DVD Drive"
          data-product-price="5200"
          data-product-image="image/acc12.png">❤</button>
        <a href="buy-now.php?id=5012" class="buy-now-btn">Buy Now</a>
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