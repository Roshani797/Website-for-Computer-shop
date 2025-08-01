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
    /* ======== Computer Components Section Styling ======== */


  .accessories-section {
  padding: 40px 20px;
  background-color: #f9f9f9;
}

.section-title {
  text-align: center;
  font-size: 28px;
  margin-bottom: 30px;
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
      <span>Email: info@computershop.com</span> | 
      <span>Contact: +123-456-7890</span>
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
</div><br>

<!-- ===================== Computer Components Section ===================== -->
<section class="components-section">
  <h2 style="text-align:center;">Computer Components</h2><br>
  <div class="product-grid">

    <!-- Component 1 -->
    <div class="product-card">
      <a href="product20000.php"><img src="image/cpu.png" alt="Intel Core i7 CPU" /></a><br>
      <h3>Intel Core i7-12700K</h3>
      <p>Rs. 65,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20000" />
          <input type="hidden" name="product_name" value="Intel Core i7-12700K" />
          <input type="hidden" name="product_price" value="65000" />
          <input type="hidden" name="product_image" value="image/cpu.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20000"
          data-product-name="Intel Core i7-12700K"
          data-product-price="65000"
          data-product-image="image/cpu.png">❤</button>
        <a href="buy-now.php?id=20000" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Component 2 -->
    <div class="product-card">
      <a href="product20001.php"><img src="image/gpu.png" alt="NVIDIA RTX 3060" /></a>
      <h3>NVIDIA GeForce RTX 3060</h3>
      <p>Rs. 120,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20001" />
          <input type="hidden" name="product_name" value="NVIDIA GeForce RTX 3060" />
          <input type="hidden" name="product_price" value="120000" />
          <input type="hidden" name="product_image" value="image/gpu.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20001"
          data-product-name="NVIDIA GeForce RTX 3060"
          data-product-price="120000"
          data-product-image="image/gpu.png">❤</button>
        <a href="buy-now.php?id=20001" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Component 3 -->
    <div class="product-card">
      <a href="product20002.php"><img src="image/ram.png" alt="Corsair Vengeance 16GB RAM" /></a>
      <h3>Corsair Vengeance 16GB DDR4</h3>
      <p>Rs. 18,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20002" />
          <input type="hidden" name="product_name" value="Corsair Vengeance 16GB DDR4" />
          <input type="hidden" name="product_price" value="18000" />
          <input type="hidden" name="product_image" value="image/ram.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20002"
          data-product-name="Corsair Vengeance 16GB DDR4"
          data-product-price="18000"
          data-product-image="image/ram.png">❤</button>
        <a href="buy-now.php?id=20002" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Component 4 -->
    <div class="product-card">
      <a href="product20003.php"><img src="image/motherboard.png" alt="ASUS ROG Motherboard" /></a><br>
      <h3>ASUS ROG Strix Z690-F</h3>
      <p>Rs. 75,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20003" />
          <input type="hidden" name="product_name" value="ASUS ROG Strix Z690-F" />
          <input type="hidden" name="product_price" value="75000" />
          <input type="hidden" name="product_image" value="image/motherboard.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20003"
          data-product-name="ASUS ROG Strix Z690-F"
          data-product-price="75000"
          data-product-image="image/motherboard.png">❤</button>
        <a href="buy-now.php?id=20003" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Component 5 -->
    <div class="product-card">
      <a href="product20004.php"><img src="image/ssd.png" alt="Samsung 980 PRO 1TB SSD" /></a>
      <h3>Samsung 980 PRO 1TB SSD</h3>
      <p>Rs. 29,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20004" />
          <input type="hidden" name="product_name" value="Samsung 980 PRO 1TB SSD" />
          <input type="hidden" name="product_price" value="29000" />
          <input type="hidden" name="product_image" value="image/ssd.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20004"
          data-product-name="Samsung 980 PRO 1TB SSD"
          data-product-price="29000"
          data-product-image="image/ssd.png">❤</button>
        <a href="buy-now.php?id=20004" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Component 6 -->
    <div class="product-card">
      <a href="product20005.php"><img src="image/hdd.png" alt="WD 2TB Hard Drive" /></a><br>
      <h3>WD Blue 2TB HDD</h3>
      <p>Rs. 15,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20005" />
          <input type="hidden" name="product_name" value="WD Blue 2TB HDD" />
          <input type="hidden" name="product_price" value="15500" />
          <input type="hidden" name="product_image" value="image/hdd.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20005"
          data-product-name="WD Blue 2TB HDD"
          data-product-price="15500"
          data-product-image="image/hdd.png">❤</button>
        <a href="buy-now.php?id=20005" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Component 7 -->
    <div class="product-card">
      <a href="product20006.php"><img src="image/psu.png" alt="Corsair 750W PSU" /></a>
      <h3>Corsair RM750x 750W PSU</h3>
      <p>Rs. 29,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20006" />
          <input type="hidden" name="product_name" value="Corsair RM750x 750W PSU" />
          <input type="hidden" name="product_price" value="29500" />
          <input type="hidden" name="product_image" value="image/psu.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20006"
          data-product-name="Corsair RM750x 750W PSU"
          data-product-price="29500"
          data-product-image="image/psu.png">❤</button>
        <a href="buy-now.php?id=20006" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Component 8 -->
    <div class="product-card">
      <a href="product20007.php"><img src="image/case.png" alt="NZXT Mid Tower Case" /></a>
      <h3>NZXT H510 Mid Tower Case</h3>
      <p>Rs. 20,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20007" />
          <input type="hidden" name="product_name" value="NZXT H510 Mid Tower Case" />
          <input type="hidden" name="product_price" value="20000" />
          <input type="hidden" name="product_image" value="image/case.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20007"
          data-product-name="NZXT H510 Mid Tower Case"
          data-product-price="20000"
          data-product-image="image/case.png">❤</button>
        <a href="buy-now.php?id=20007" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Component 9 -->
    <div class="product-card">
      <a href="product20008.php"><img src="image/cooler.png" alt="Cooler Master CPU Cooler" /></a><br>
      <h3>Cooler Master Hyper 212</h3>
      <p>Rs. 10,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20008" />
          <input type="hidden" name="product_name" value="Cooler Master Hyper 212" />
          <input type="hidden" name="product_price" value="10500" />
          <input type="hidden" name="product_image" value="image/cooler.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20008"
          data-product-name="Cooler Master Hyper 212"
          data-product-price="10500"
          data-product-image="image/cooler.png">❤</button>
        <a href="buy-now.php?id=20008" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Component 10 -->
    <div class="product-card">
      <a href="product20009.php"><img src="image/soundcard.png" alt="ASUS Xonar Sound Card" /></a>
      <h3>ASUS Xonar AE Sound Card</h3>
      <p>Rs. 16,500</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="20009" />
          <input type="hidden" name="product_name" value="ASUS Xonar AE Sound Card" />
          <input type="hidden" name="product_price" value="16500" />
          <input type="hidden" name="product_image" value="image/soundcard.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="20009"
          data-product-name="ASUS Xonar AE Sound Card"
          data-product-price="16500"
          data-product-image="image/soundcard.png">❤</button>
        <a href="buy-now.php?id=20009" class="buy-now-btn">Buy Now</a>
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