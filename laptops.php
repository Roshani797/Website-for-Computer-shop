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
  <link rel="stylesheet" href="laptop.css" />
  

  <script src="script.js" defer></script>
    <script src="wishlist.js" defer></script>
 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>

.latest-laptops {
  padding: 30px;
  background-color: #f9f9f9;
}

.latest-laptops h2 {
  text-align: center;
  margin-bottom: 20px;
  font-size: 28px;
  color: #222;
}

.slideshow-container {
  position: relative;
  overflow: hidden;
  width: 100%;
  max-width: 1400px;
  margin: auto;
}

.slides-wrapper {
  display: flex;
  overflow-x: auto;
  scroll-behavior: smooth;
  gap: 20px;
  padding: 10px;
}

.slide-card {
  flex: 0 0 auto;
  width: 220px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  text-align: center;
  padding: 15px;
}

.slide-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  border-radius: 6px;
}

.slide-card h4 {
  margin: 10px 0 5px;
  font-size: 16px;
  color: #333;
}

.slide-card p {
  color:rgb(8, 8, 8);
  font-weight: bold;
  margin-bottom: 0;
}

.slide-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(0,0,0,0.6);
  color: white;
  border: none;
  padding: 10px 15px;
  cursor: pointer;
  font-size: 22px;
  border-radius: 50%;
  z-index: 1;
}

.slide-btn:hover {
  background-color: rgba(0,0,0,0.8);
}

.slide-btn.prev {
  left: 0;
}

.slide-btn.next {
  right: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .slide-card {
    width: 160px;
  }
}

.more-products {
  padding: 30px;
  background-color: #f3f3f3;
}

.more-products h2 {
  text-align: center;
  font-size: 28px;
  color: #111;
  margin-bottom: 20px;
}

.product-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
}

.laptop-card {
  width: 220px;
  background: white;
  padding: 15px;
  border-radius: 8px;
  box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.laptop-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.laptop-card h3 {
  font-size: 16px;
  margin: 10px 0 5px;
  color: #333;
}

.laptop-card p {
  color:rgb(0, 0, 0);
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

<section class="latest-laptops">
  <h2>Latest Laptops</h2>
  <div class="slideshow-container">
    <button class="slide-btn prev">&#10094;</button>
    <div class="slides-wrapper" id="laptop-slides">
      <!-- Laptop Cards -->
      <div class="slide-card">
              <a href="product1.php"><img src="image/1.png" alt="MSI Laptop"></a> 
        <h3>MSI</h3> 
        <p>Rs. 310,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="80"> 
            <input type="hidden" name="product_name" value="MSI">
            <input type="hidden" name="product_price" value="310000">
            <input type="hidden" name="product_image" value="image/1.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn" 
            data-product-id="80"
            data-product-name="MSI"
            data-product-price="310000"
            data-product-image="image/1.png"
          >❤</button> 
          <a href="buy-now.php?id=80" class="buy-now-btn">Buy Now</a> 
        </div>
      </div> 


      <div class="slide-card">
           <a href="product2.php"><img src="image/2.png" alt="Asus Zenbook"></a> 
        <h3>Asus Zenbook</h3> 
        <p>Rs. 210,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="81"> 
            <input type="hidden" name="product_name" value="Asus Zenbook">
            <input type="hidden" name="product_price" value="210000">
            <input type="hidden" name="product_image" value="image/2.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn"
            data-product-id="81"
            data-product-name="Asus Zenbook"
            data-product-price="210000"
            data-product-image="image/2.png"
          >❤</button> 
          <a href="buy-now.php?id=81" class="buy-now-btn">Buy Now</a> 
        </div> 
      </div> 


      <div class="slide-card">
          <a href="product-details.php?id=82"><img src="image/4.png" alt="HP 14"></a> 
        <h3>HP 14</h3> 
        <p>Rs. 175,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="82"> 
            <input type="hidden" name="product_name" value="HP 14">
            <input type="hidden" name="product_price" value="175000">
            <input type="hidden" name="product_image" value="image/4.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn"
            data-product-id="82"
            data-product-name="HP 14"
            data-product-price="175000"
            data-product-image="image/4.png"
          >❤</button> 
          <a href="buy-now.php?id=82" class="buy-now-btn">Buy Now</a> 
        </div> 
      </div> 
      <div class="slide-card">
          <a href="product-details.php?id=83"><img src="image/5.png" alt="Dell Latitude"></a> 
        <h3>Dell Latitude</h3> 
        <p>Rs. 240,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="83"> 
            <input type="hidden" name="product_name" value="Dell Latitude">
            <input type="hidden" name="product_price" value="240000">
            <input type="hidden" name="product_image" value="image/5.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn"
            data-product-id="83"
            data-product-name="Dell Latitude"
            data-product-price="240000"
            data-product-image="image/5.png"
          >❤</button> 
          <a href="buy-now.php?id=83" class="buy-now-btn">Buy Now</a> 
        </div> 
      </div> 

 <div class="slide-card">
            <a href="product-details.php?id=84"><img src="image/6.png" alt="ACER A515"></a> 
        <h3>ACER A515</h3> 
        <p>Rs. 150,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="84"> 
            <input type="hidden" name="product_name" value="ACER A515">
            <input type="hidden" name="product_price" value="150000">
            <input type="hidden" name="product_image" value="image/6.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn"
            data-product-id="84"
            data-product-name="ACER A515"
            data-product-price="150000"
            data-product-image="image/6.png"
          >❤</button> 
          <a href="buy-now.php?id=84" class="buy-now-btn">Buy Now</a> 
        </div> 
      </div> 

      <div class="slide-card">
            <a href="product-details.php?id=85"><img src="image/1.2.png" alt="Asus X515JP Intel i5"></a> 
        <h3>Samsung Galaxy Book3 Ultra</h3>
        <p>Rs. 300,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="85">
            <input type="hidden" name="product_name" value="Samsung Galaxy Book3 Ultra">
            <input type="hidden" name="product_price" value="300000">
            <input type="hidden" name="product_image" value="image/1.2.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="85"
            data-product-name="Samsung Galaxy Book3 Ultra"
            data-product-price="300000"
            data-product-image="image/1.2.png"
          >❤</button>
          <a href="buy-now.php?id=85" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
         <div class="slide-card">
            <a href="product-details.php?id=86"><img src="image/1.3.png" alt="Asus X515JP Intel i5"></a> 
        <h3>HP ZBook Power 15.6inch</h3>
        <p>Rs. 200,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="86">
            <input type="hidden" name="product_name" value="HP ZBook Power 15.6inch">
            <input type="hidden" name="product_price" value="200000">
            <input type="hidden" name="product_image" value="image/1.3.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="86"
            data-product-name="HP ZBook Power 15.6inch"
            data-product-price="200000"
            data-product-image="image/1.3.png"
          >❤</button>
          <a href="buy-now.php?id=86" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
          <div class="slide-card">
            <a href="product-details.php?id=87"><img src="image/1.1.png" alt="Asus X515JP Intel i5"></a> 
        <h3>MacBook Pro 17</h3>
        <p>Rs. 500,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="87">
            <input type="hidden" name="product_name" value="MacBook Pro 17">
            <input type="hidden" name="product_price" value="500000">
            <input type="hidden" name="product_image" value="image/1.1.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="87"
            data-product-name="MacBook Pro 17"
            data-product-price="500000"
            data-product-image="image/1.1.png"
          >❤</button>
          <a href="buy-now.php?id=87" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
          <div class="slide-card">
            <a href="product-details.php?id=88"><img src="image/1.4.png" alt="Asus X515JP Intel i5"></a> 
        <h3>DELL Portátil VOSTRO 3400 CORE I3</h3>
        <p>Rs. 400,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="88">
            <input type="hidden" name="product_name" value="DELL Portátil VOSTRO 3400 CORE I3">
            <input type="hidden" name="product_price" value="400000">
            <input type="hidden" name="product_image" value="image/1.4.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="88"
            data-product-name="Asus X515JP Intel i5"
            data-product-price="400000"
            data-product-image="image/1.4.png"
          >❤</button>
          <a href="buy-now.php?id=88" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
          <div class="slide-card">
            <a href="product-details.php?id=89"><img src="image/k.png" alt="Asus X515JP Intel i5"></a> 
        <h3>Asus X515JP Intel i5</h3>
        <p>Rs. 200,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="89">
            <input type="hidden" name="product_name" value="Asus X515JP Intel i5">
            <input type="hidden" name="product_price" value="200000">
            <input type="hidden" name="product_image" value="image/k.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="89"
            data-product-name="Asus X515JP Intel i5"
            data-product-price="200000"
            data-product-image="image/k.png"
          >❤</button>
          <a href="buy-now.php?id=89" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
    <button class="slide-btn next">&#10095;</button>
  </div>
</section>

<section class="more-products">
  <h2>More Products</h2>
  <div class="product-grid">

    <!-- Laptop 6 -->
    <div class="laptop-card">
      <a href="product-details.php?id=90"><img src="image/j.png" alt="Asus X515JP Intel i5"></a>
      <h3>Asus X515JP Intel i5</h3>
      <p>Rs. 200,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="90">
          <input type="hidden" name="product_name" value="Asus X515JP Intel i5">
          <input type="hidden" name="product_price" value="200000">
          <input type="hidden" name="product_image" value="image/j.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="90"
          data-product-name="Asus X515JP Intel i5"
          data-product-price="200000"
          data-product-image="image/j.png"
        >❤</button>
        <a href="buy-now.php?id=90" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Laptop 7 -->
    <div class="laptop-card">
      <a href="product-details.php?id=91"><img src="image/i3.png" alt="HP Pavilion 15 Ryzen 5"></a>
      <h3>HP Pavilion 15 Ryzen 5</h3>
      <p>Rs. 185,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="91">
          <input type="hidden" name="product_name" value="HP Pavilion 15 Ryzen 5">
          <input type="hidden" name="product_price" value="185000">
          <input type="hidden" name="product_image" value="image/i3.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="91"
          data-product-name="HP Pavilion 15 Ryzen 5"
          data-product-price="185000"
          data-product-image="image/i3.png"
        >❤</button>
        <a href="buy-now.php?id=91" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Laptop 8 -->
    <div class="laptop-card">
      <a href="product-details.php?id=92"><img src="image/h2.png"alt="Dell Inspiron 14 i7"></a>
      <h3>Dell Inspiron 14 i7</h3>
      <p>Rs. 210,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="92">
          <input type="hidden" name="product_name" value="Dell Inspiron 14 i7">
          <input type="hidden" name="product_price" value="210000">
          <input type="hidden" name="product_image" value="image/h2.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="92"
          data-product-name="Dell Inspiron 14 i7"
          data-product-price="210000"
          data-product-image="image/h2.png"
        >❤</button>
        <a href="buy-now.php?id=92" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Laptop 9 -->
    <div class="laptop-card">
      <a href="product-details.php?id=93"><img src="image/k.png"alt="Lenovo IdeaPad 3 i3"></a>
      <h3>Lenovo IdeaPad 3 i3</h3>
      <p>Rs. 135,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="93">
          <input type="hidden" name="product_name" value="Lenovo IdeaPad 3 i3">
          <input type="hidden" name="product_price" value="135000">
          <input type="hidden" name="product_image" value="image/k.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="93"
          data-product-name="Lenovo IdeaPad 3 i3"
          data-product-price="135000"
          data-product-image="image/k.png"
        >❤</button>
        <a href="buy-now.php?id=93" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Laptop 10 -->
    <div class="laptop-card">
      <a href="product-details.php?id=94"><img src="image/f.png"alt="acerAspire 5 Intel i7"></a>
      <h3>Acer Aspire 5 Intel i7</h3>
      <p>Rs. 195,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="94">
          <input type="hidden" name="product_name" value="Acer Aspire 5 Intel i7">
          <input type="hidden" name="product_price" value="195000">
          <input type="hidden" name="product_image" value="image/f.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="94"
          data-product-name="Acer Aspire 5 Intel i7"
          data-product-price="195000"
          data-product-image="image/f.png"
        >❤</button>
        <a href="buy-now.php?id=94" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Laptop 11 -->
    <div class="laptop-card">
      <a href="product-details.php?id=95"><img src="image/e1.png" alt="GF63 Gaming"></a>
      <h3>MSI GF63 Gaming</h3>
      <p>Rs. 425,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="95">
          <input type="hidden" name="product_name" value="MSI GF63 Gaming">
          <input type="hidden" name="product_price" value="425000">
          <input type="hidden" name="product_image" value="image/e1.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="95"
          data-product-name="MSI GF63 Gaming"
          data-product-price="425000"
          data-product-image="image/e1.png"
        >❤</button>
        <a href="buy-now.php?id=95" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Laptop 12 -->
    <div class="laptop-card">
      <a href="product-details.php?id=96"><img src="image/d1.png"alt="apple mac book"></a>
      <h3>Apple MacBook Pro M2</h3>
      <p>Rs. 450,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="96">
          <input type="hidden" name="product_name" value="Apple MacBook Pro M2">
          <input type="hidden" name="product_price" value="450000">
          <input type="hidden" name="product_image" value="image/d1.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="96"
          data-product-name="Apple MacBook Pro M2"
          data-product-price="450000"
          data-product-image="image/d1.png"
        >❤</button>
        <a href="buy-now.php?id=96" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Laptop 13 -->
    <div class="laptop-card">
      <a href="product-details.php?id=97"><img src="image/a.png" alt="Samsung Galaxy Book3"></a>
      <h3>Samsung Galaxy Book3</h3>
      <p>Rs. 235,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="97">
          <input type="hidden" name="product_name" value="Samsung Galaxy Book3">
          <input type="hidden" name="product_price" value="235000">
          <input type="hidden" name="product_image" value="image/a.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="97"
          data-product-name="Samsung Galaxy Book3"
          data-product-price="235000"
          data-product-image="image/a.png"
        >❤</button>
        <a href="buy-now.php?id=97" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Laptop 14 -->
    <div class="laptop-card">
      <a href="product-details.php?id=98"><img src="image/b.png" alt="ASUS TUF A15 Gaming"></a>
      <h3>ASUS TUF A15 Gaming</h3>
      <p>Rs. 240,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="98">
          <input type="hidden" name="product_name" value="ASUS TUF A15 Gaming">
          <input type="hidden" name="product_price" value="240000">
          <input type="hidden" name="product_image" value="image/b.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="98"
          data-product-name="ASUS TUF A15 Gaming"
          data-product-price="240000"
          data-product-image="image/b.png"
        >❤</button>
        <a href="buy-now.php?id=98" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Laptop 14 -->
    <div class="laptop-card">
      <a href="product-details.php?id=99"><img src="image/c1.png" alt="ASUS TUF A15 Gaming"></a>
      <h3>ASUS TUF GAMING A15 AMD Ryzen</h3>
      <p>Rs. 270,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="99">
          <input type="hidden" name="product_name" value="ASUS TUF A15 Gaming">
          <input type="hidden" name="product_price" value="270000">
          <input type="hidden" name="product_image" value="image/c1.png">
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn"
          data-product-id="99"
          data-product-name="ASUS TUF A15 Gaming"
          data-product-price="270000"
          data-product-image="image/c1.png"
        >❤</button>
        <a href="buy-now.php?id=99" class="buy-now-btn">Buy Now</a>
      </div>
    </div>
  </div>
</section>

<script>
  
  const slideWrapper = document.getElementById('laptop-slides');
  const btnNext = document.querySelector('.slide-btn.next');
  const btnPrev = document.querySelector('.slide-btn.prev');

  btnNext.addEventListener('click', () => {
    slideWrapper.scrollBy({ left: 250, behavior: 'smooth' });
  });

  btnPrev.addEventListener('click', () => {
    slideWrapper.scrollBy({ left: -250, behavior: 'smooth' });
  });
</script>
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

      alert(${product.name} added to wishlist!);
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
