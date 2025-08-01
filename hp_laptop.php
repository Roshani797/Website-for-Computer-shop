<?php
session_start();


include 'db.php';  // include your DB connection

// Ensure session_id is set like in cart.php
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

// Query cart count using session_id like cart.php
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
 /* Container for the product grid */
.hp-laptops-section {
  padding: 40px 20px;
  background-color: #f9f9f9;
}

.hp-laptops-section h2 {
  font-size: 2.5rem;
  margin-bottom: 30px;
  color: #222;
  font-weight: 700;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  text-transform: uppercase;
}

/* Grid layout for product cards */
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 28px;
  max-width: 1500px;
  margin: 0 auto;
}

/* Individual product card */
.product-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgb(0 0 0 / 0.1);
  padding: 20px;
  text-align: center;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 30px rgb(0 0 0 / 0.15);
}

/* Product image */
.product-card img {
  max-width: 100%;
  height: 160px;
  object-fit: contain;
  margin-bottom: 18px;
  border-radius: 8px;
  user-select: none;
}

/* Product title */
.product-card h3 {
  font-size: 1.25rem;
  margin: 0 0 10px;
  color: #111;
  font-weight: 600;
}

/* Price styling */
.product-card p {
  font-size: 1.1rem;
  color:rgb(0, 0, 0); /* A nice warm yellow */
  font-weight: 700;
  margin-bottom: 15px;
}


    .product-icons {
      margin-top: 8px;
      display: flex;
      justify-content: center;
      gap: 8px;
    }

    .add-to-cart-btn,
    .add-to-wishlist-btn{
    
      cursor: pointer;
      border: none;
      background:rgba(240, 240, 240, 0);
      padding: 6px 10px;
      border-radius: 4px;
      font-size: 18px;
      transition: background-color 0.3s ease;
      color: #333;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

 
.buy-now-btn {
   cursor: pointer;
      border: none;
  background:rgb(36, 102, 27);
       padding: 6px 10px;
      border-radius: 4px;
      font-size: 18px;
      transition: background-color 0.3s ease;
      color: white;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
}

.add-to-cart-btn:hover,
.add-to-wishlist-btn:hover,
.buy-now-btn:hover {
  background:rgba(36, 162, 55, 0.83);
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


<section class="hp-laptops-section">
  <h2 style="text-align:center;">HP Laptops</h2>
  <div class="product-grid">

    <!-- HP Laptop 1 -->
    <div class="product-card">
      <a href="product1500.php"><img src="image/hp1.png" alt="HP Pavilion 15" /></a>
      <h3>HP Pavilion 15</h3>
      <p>Rs. 85,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1500" />
          <input type="hidden" name="product_name" value="HP Pavilion 15" />
          <input type="hidden" name="product_price" value="85000" />
          <input type="hidden" name="product_image" value="image/hp1.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1500"
          data-product-name="HP Pavilion 15"
          data-product-price="85000"
          data-product-image="image/hp1.png">❤</button>
        <a href="buy-now.php?id=1500" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- HP Laptop 2 -->
    <div class="product-card">
      <a href="product1501.php"><img src="image/hp2.png" alt="HP Spectre x360" /></a>
      <h3>HP Spectre x360</h3>
      <p>Rs. 150,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1501" />
          <input type="hidden" name="product_name" value="HP Spectre x360" />
          <input type="hidden" name="product_price" value="150000" />
          <input type="hidden" name="product_image" value="image/hp2.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1501"
          data-product-name="HP Spectre x360"
          data-product-price="150000"
          data-product-image="image/hp2.png">❤</button>
        <a href="buy-now.php?id=1501" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- HP Laptop 3 -->
    <div class="product-card">
      <a href="product1502.php"><img src="image/hp3.png" alt="HP Envy 13" /></a>
      <h3>HP Envy 13</h3>
      <p>Rs. 120,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1502" />
          <input type="hidden" name="product_name" value="HP Envy 13" />
          <input type="hidden" name="product_price" value="120000" />
          <input type="hidden" name="product_image" value="image/hp3.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1502"
          data-product-name="HP Envy 13"
          data-product-price="120000"
          data-product-image="image/hp3.png">❤</button>
        <a href="buy-now.php?id=1502" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- HP Laptop 4 -->
    <div class="product-card">
      <a href="product1503.php"><img src="image/hp4.png" alt="HP Omen 15" /></a>
      <h3>HP Omen 15</h3>
      <p>Rs. 140,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1503" />
          <input type="hidden" name="product_name" value="HP Omen 15" />
          <input type="hidden" name="product_price" value="140000" />
          <input type="hidden" name="product_image" value="image/hp4.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1503"
          data-product-name="HP Omen 15"
          data-product-price="140000"
          data-product-image="image/hp4.png">❤</button>
        <a href="buy-now.php?id=1503" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- HP Laptop 5 -->
    <div class="product-card">
      <a href="product1504.php"><img src="image/hp5.png" alt="HP Elite Dragonfly" /></a>
      <h3>HP Elite Dragonfly</h3>
      <p>Rs. 190,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1504" />
          <input type="hidden" name="product_name" value="HP Elite Dragonfly" />
          <input type="hidden" name="product_price" value="190000" />
          <input type="hidden" name="product_image" value="image/hp5.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1504"
          data-product-name="HP Elite Dragonfly"
          data-product-price="190000"
          data-product-image="image/hp5.png">❤</button>
        <a href="buy-now.php?id=1504" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- HP Laptop 6 -->
    <div class="product-card">
      <a href="product1505.php"><img src="image/hp6.png" alt="HP Chromebook x360" /></a>
      <h3>HP Chromebook x360</h3>
      <p>Rs. 60,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1505" />
          <input type="hidden" name="product_name" value="HP Chromebook x360" />
          <input type="hidden" name="product_price" value="60000" />
          <input type="hidden" name="product_image" value="image/hp6.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1505"
          data-product-name="HP Chromebook x360"
          data-product-price="60000"
          data-product-image="image/hp6.png">❤</button>
        <a href="buy-now.php?id=1505" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- HP Laptop 7 -->
    <div class="product-card">
      <a href="product1506.php"><img src="image/hp7.png" alt="HP ProBook 450 G8" /></a>
      <h3>HP ProBook 450 G8</h3>
      <p>Rs. 95,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1506" />
          <input type="hidden" name="product_name" value="HP ProBook 450 G8" />
          <input type="hidden" name="product_price" value="95000" />
          <input type="hidden" name="product_image" value="image/hp7.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1506"
          data-product-name="HP ProBook 450 G8"
          data-product-price="95000"
          data-product-image="image/hp7.png">❤</button>
        <a href="buy-now.php?id=1506" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- HP Laptop 8 -->
    <div class="product-card">
      <a href="product1507.php"><img src="image/hp8.png" alt="HP EliteBook x360" /></a>
      <h3>HP EliteBook x360</h3>
      <p>Rs. 180,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1507" />
          <input type="hidden" name="product_name" value="HP EliteBook x360" />
          <input type="hidden" name="product_price" value="180000" />
          <input type="hidden" name="product_image" value="image/hp8.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1507"
          data-product-name="HP EliteBook x360"
          data-product-price="180000"
          data-product-image="image/hp8.png">❤</button>
        <a href="buy-now.php?id=1507" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- HP Laptop 9 -->
    <div class="product-card">
      <a href="product1508.php"><img src="image/hp9.png" alt="HP ZBook Studio G8" /></a>
      <h3>HP ZBook Studio G8</h3>
      <p>Rs. 250,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1508" />
          <input type="hidden" name="product_name" value="HP ZBook Studio G8" />
          <input type="hidden" name="product_price" value="250000" />
          <input type="hidden" name="product_image" value="image/hp9.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1508"
          data-product-name="HP ZBook Studio G8"
          data-product-price="250000"
          data-product-image="image/hp9.png">❤</button>
        <a href="buy-now.php?id=1508" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- HP Laptop 10 -->
    <div class="product-card">
      <a href="product1509.php"><img src="image/hp10.png" alt="HP Envy x360" /></a>
      <h3>HP Envy x360</h3>
      <p>Rs. 130,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1509" />
          <input type="hidden" name="product_name" value="HP Envy x360" />
          <input type="hidden" name="product_price" value="130000" />
          <input type="hidden" name="product_image" value="image/hp10.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="1509"
          data-product-name="HP Envy x360"
          data-product-price="130000"
          data-product-image="image/hp10.png">❤</button>
        <a href="buy-now.php?id=1509" class="buy-now-btn">Buy Now</a>
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