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
    <link rel="stylesheet" href="home product.css" />
    <link rel="stylesheet" href="laptop.css" />
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
/* ===== Video Banner Section ===== */
.video-banner {
  position: relative;
  width: 100%;
  height: 80vh;
  overflow: hidden;
}

.video-banner video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Optional overlay text */
.video-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  color: white;
  text-shadow: 1px 1px 8px #000;
  z-index: 2;
}

.video-overlay h1 {
  font-size: 3rem;
  margin-bottom: 10px;
}

.video-overlay p {
  font-size: 1.5rem;
}

</style>
   
  
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


<section class="video-banner">
  <video autoplay muted loop playsinline>
    <source src="video/gaming-banner.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <div class="video-overlay">
    <h1>Unleash the Power of Gaming</h1>
    <p>Explore the latest gaming gear at unbeatable prices</p>
    <a href="gaming.php" class="btn">Shop Now</a>
  </div>
</section>




</div>
<div class="shop-categories-row">
  <div class="shop-category-card">
    <img src="image/laptops.png" alt="Laptops" />
    <a href="laptops.php" class="shop-now-btn">Shop Now</a>
  </div>
  <div class="shop-category-card">
    <img src="image/desktop.png" alt="Desktops" />
    <a href="desktops.php" class="shop-now-btn">Shop Now</a>
  </div>
  <div class="shop-category-card">
    <img src="image/gaming.png" alt="Gaming" />
    <a href="gaming.php" class="shop-now-btn">Shop Now</a>
  </div>
  <div class="shop-category-card">
    <img src="image/printer.png" alt="Printers" />
    <a href="printers.php" class="shop-now-btn">Shop Now</a>
  </div>
</div>



<!-- Latest Laptops Section -->
<section class="latest-laptops"> 
  <h2>Latest Laptops</h2> 
  <div class="laptop-carousel"> 
    <div class="laptop-track"> 

      <!-- Laptop 1 -->
      <div class="laptop-card"> 
        <a href="product1.php"><img src="image/1.png" alt="MSI Laptop"></a> 
        <h3>MSI</h3> 
        <p>Rs. 310,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="1"> 
            <input type="hidden" name="product_name" value="MSI">
            <input type="hidden" name="product_price" value="310000">
            <input type="hidden" name="product_image" value="image/1.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn" 
            data-product-id="1"
            data-product-name="MSI"
            data-product-price="310000"
            data-product-image="image/1.png"
          >❤</button> 
          <a href="buy-now.php?id=1" class="buy-now-btn">Buy Now</a> 
        </div>
      </div> 

      <!-- Laptop 2 --> 
      <div class="laptop-card"> 
        <a href="product2.php"><img src="image/2.png" alt="Asus Zenbook"></a> 
        <h3>Asus Zenbook</h3> 
        <p>Rs. 210,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="2"> 
            <input type="hidden" name="product_name" value="Asus Zenbook">
            <input type="hidden" name="product_price" value="210000">
            <input type="hidden" name="product_image" value="image/2.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn"
            data-product-id="2"
            data-product-name="Asus Zenbook"
            data-product-price="210000"
            data-product-image="image/2.png"
          >❤</button> 
          <a href="buy-now.php?id=2" class="buy-now-btn">Buy Now</a> 
        </div> 
      </div> 

      <!-- Laptop 3 --> 
      <div class="laptop-card"> 
        <a href="product-details.php?id=3"><img src="image/4.png" alt="HP 14"></a> 
        <h3>HP 14</h3> 
        <p>Rs. 175,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="3"> 
            <input type="hidden" name="product_name" value="HP 14">
            <input type="hidden" name="product_price" value="175000">
            <input type="hidden" name="product_image" value="image/4.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn"
            data-product-id="3"
            data-product-name="HP 14"
            data-product-price="175000"
            data-product-image="image/4.png"
          >❤</button> 
          <a href="buy-now.php?id=3" class="buy-now-btn">Buy Now</a> 
        </div> 
      </div> 

      <!-- Laptop 4 --> 
      <div class="laptop-card"> 
        <a href="product-details.php?id=4"><img src="image/5.png" alt="Dell Latitude"></a> 
        <h3>Dell Latitude</h3> 
        <p>Rs. 240,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="4"> 
            <input type="hidden" name="product_name" value="Dell Latitude">
            <input type="hidden" name="product_price" value="240000">
            <input type="hidden" name="product_image" value="image/5.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn"
            data-product-id="4"
            data-product-name="Dell Latitude"
            data-product-price="240000"
            data-product-image="image/5.png"
          >❤</button> 
          <a href="buy-now.php?id=4" class="buy-now-btn">Buy Now</a> 
        </div> 
      </div> 

      <!-- Laptop 5 --> 
      <div class="laptop-card"> 
        <a href="product-details.php?id=5"><img src="image/6.png" alt="ACER A515"></a> 
        <h3>ACER A515</h3> 
        <p>Rs. 150,000</p> 
        <div class="product-icons"> 
          <form action="add_to_cart.php" method="post"> 
            <input type="hidden" name="product_id" value="5"> 
            <input type="hidden" name="product_name" value="ACER A515">
            <input type="hidden" name="product_price" value="150000">
            <input type="hidden" name="product_image" value="image/6.png">
            <button type="submit" class="add-to-cart-btn">🛒</button> 
          </form> 
          <button class="add-to-wishlist-btn"
            data-product-id="5"
            data-product-name="ACER A515"
            data-product-price="150000"
            data-product-image="image/6.png"
          >❤</button> 
          <a href="buy-now.php?id=5" class="buy-now-btn">Buy Now</a> 
        </div> 
      </div> 

      <!-- Laptop 6 --> 
      <div class="laptop-card"> 
        <a href="product-details.php?id=6"><img src="image/7.png" alt="Asus X515JP Intel i5"></a> 
        <h3>Asus X515JP Intel i5</h3>
        <p>Rs. 200,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="6">
            <input type="hidden" name="product_name" value="Asus X515JP Intel i5">
            <input type="hidden" name="product_price" value="200000">
            <input type="hidden" name="product_image" value="image/7.png">
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button class="add-to-wishlist-btn"
            data-product-id="6"
            data-product-name="Asus X515JP Intel i5"
            data-product-price="200000"
            data-product-image="image/7.png"
          >❤</button>
          <a href="buy-now.php?id=6" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

    </div>
  </div>
</section>


 



<script>
let slideIndex = 1;
showSlides(slideIndex);
let slideTimer = setInterval(() => plusSlides(1), 4000);

function plusSlides(n) {
  clearInterval(slideTimer);
  showSlides(slideIndex += n);
  slideTimer = setInterval(() => plusSlides(1), 4000);
}

function currentSlide(n) {
  clearInterval(slideTimer);
  showSlides(slideIndex = n);
  slideTimer = setInterval(() => plusSlides(1), 4000);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) { slideIndex = 1 }
  if (n < 1) { slideIndex = slides.length }
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}
</script>

<!-- ========== Two Promotional Banners ========== -->
<div class="banner-section">
  <div class="banner-card">
    <img src="image/banner 3.png" alt="Gaming Sale">
    <div class="banner-content">
      <h2>Ultimate Laptop Setup</h2>
      <p>Save up to 20% on laptops</p>
      <a href="laptops.php" class="banner-btn">Shop Now</a>
    </div>
  </div>
  <div class="banner-card">
    <img src="image/banner 4.png" alt="Gaming Deals">
    <div class="banner-content">
      <h2>Ultimate Gaming Setup</h2>
      <p>Save up to 30% on gaming gear</p>
      <a href="gaming.php" class="banner-btn">Shop Now</a>
    </div>
  </div>
</div>


<div class="category-links">
  <a href="laptops.php" class="category-link-btn">Laptops</a>
  <a href="desktops.php" class="category-link-btn">Desktops</a>
  <a href="accessories.php" class="category-link-btn">Accessories</a>
</div>


<!-- Gaming Products Section -->
<section class="gaming-products">
  <h2>Gaming Products</h2>
  <div class="product-grid">

    <!-- Product 1 -->
    <div class="product-card">
      <div class="product-image-wrapper">
        <a href="product7.php">
          <img src="image/gaming.png" alt="DualSense Wireless Controller" />
        </a>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="301" />
            <input type="hidden" name="product_name" value="DualSense Wireless Controller" />
            <input type="hidden" name="product_price" value="9000" />
            <input type="hidden" name="product_image" value="image/gaming.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button 
            class="add-to-wishlist-btn" 
            data-product-id="301" 
            data-product-name="DualSense Wireless Controller" 
            data-product-price="9000" 
            data-product-image="image/gaming.png"
          >❤</button>
          <a href="buy-now.php?id=301" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
      <h3>DualSense Wireless Controller</h3>
      <p class="price">Rs.9000</p>
    </div>

    <!-- Product 2 -->
    <div class="product-card">
      <div class="product-image-wrapper">
        <a href="product-details.php?id=302">
          <img src="image/gaming 1.png" alt="Gaming Chair" />
        </a>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="302" />
            <input type="hidden" name="product_name" value="Gaming Chair" />
            <input type="hidden" name="product_price" value="18000" />
            <input type="hidden" name="product_image" value="image/gaming 1.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button 
            class="add-to-wishlist-btn" 
            data-product-id="302" 
            data-product-name="Gaming Chair" 
            data-product-price="18000" 
            data-product-image="image/gaming 1.png"
          >❤</button>
          <a href="buy-now.php?id=302" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
      <h3>Gaming Chair</h3>
      <p class="price">Rs.18000</p>
    </div>

    <!-- Product 3 -->
    <div class="product-card">
      <div class="product-image-wrapper">
        <a href="product-details.php?id=303">
          <img src="image/1.png" alt="MSI Laptop" />
        </a>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="303" />
            <input type="hidden" name="product_name" value="MSI Laptop" />
            <input type="hidden" name="product_price" value="29000" />
            <input type="hidden" name="product_image" value="image/1.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button 
            class="add-to-wishlist-btn" 
            data-product-id="303" 
            data-product-name="MSI Laptop" 
            data-product-price="29000" 
            data-product-image="image/1.png"
          >❤</button>
          <a href="buy-now.php?id=303" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
      <h3>MSI Laptop</h3>
      <p class="price">Rs.29000</p>
    </div>

    <!-- Product 4 -->
    <div class="product-card">
      <div class="product-image-wrapper">
        <a href="product-details.php?id=304">
          <img src="image/gaming 2.png" alt="USB Thunder Gaming Headset" />
        </a>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="304" />
            <input type="hidden" name="product_name" value="USB Thunder Gaming Headset" />
            <input type="hidden" name="product_price" value="5600" />
            <input type="hidden" name="product_image" value="image/gaming 2.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button 
            class="add-to-wishlist-btn" 
            data-product-id="304" 
            data-product-name="USB Thunder Gaming Headset" 
            data-product-price="5600" 
            data-product-image="image/gaming 2.png"
          >❤</button>
          <a href="buy-now.php?id=304" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
      <h3>USB Thunder Gaming Headset</h3>
      <p class="price">Rs.5600</p>
    </div>

    <!-- Product 5 -->
    <div class="product-card">
      <div class="product-image-wrapper">
        <a href="product-details.php?id=305">
          <img src="image/gaming 3.png" alt="Sony DualSense Edge Controller" />
        </a>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="305" />
            <input type="hidden" name="product_name" value="Sony DualSense Edge Controller" />
            <input type="hidden" name="product_price" value="4800" />
            <input type="hidden" name="product_image" value="image/gaming 3.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button 
            class="add-to-wishlist-btn" 
            data-product-id="305" 
            data-product-name="Sony DualSense Edge Controller" 
            data-product-price="4800" 
            data-product-image="image/gaming 3.png"
          >❤</button>
          <a href="buy-now.php?id=305" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
      <h3>Sony DualSense Edge Controller</h3>
      <p class="price">Rs.4800</p>
    </div>

    <!-- Product 6 -->
    <div class="product-card">
      <div class="product-image-wrapper">
        <a href="product-details.php?id=306">
          <img src="image/gaming 4.png" alt="DUALSHOCK 4 Wireless Controller" />
        </a>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="306" />
            <input type="hidden" name="product_name" value="DUALSHOCK 4 Wireless Controller" />
            <input type="hidden" name="product_price" value="7000" />
            <input type="hidden" name="product_image" value="image/gaming 4.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button 
            class="add-to-wishlist-btn" 
            data-product-id="306" 
            data-product-name="DUALSHOCK 4 Wireless Controller" 
            data-product-price="7000" 
            data-product-image="image/gaming 4.png"
          >❤</button>
          <a href="buy-now.php?id=306" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
      <h3>DUALSHOCK 4 Wireless Controller</h3>
      <p class="price">Rs.7000</p>
    </div>

    <!-- Product 7 -->
    <div class="product-card">
      <div class="product-image-wrapper">
        <a href="product-details.php?id=307">
          <img src="image/gaming 5.png" alt="Jedel GM1070 Gaming Mouse" />
        </a>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="307" />
            <input type="hidden" name="product_name" value="Jedel GM1070 Gaming Mouse" />
            <input type="hidden" name="product_price" value="2000" />
            <input type="hidden" name="product_image" value="image/gaming 5.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button 
            class="add-to-wishlist-btn" 
            data-product-id="307" 
            data-product-name="Jedel GM1070 Gaming Mouse" 
            data-product-price="2000" 
            data-product-image="image/gaming 5.png"
          >❤</button>
          <a href="buy-now.php?id=307" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
      <h3>Jedel GM1070 Gaming Mouse</h3>
      <p class="price">Rs.2000</p>
    </div>

    <!-- Product 8 -->
    <div class="product-card">
      <div class="product-image-wrapper">
        <a href="product-details.php?id=308">
          <img src="image/gaming 6.png" alt="PowerA MOGA XP5-A Bluetooth Controller" />
        </a>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="308" />
            <input type="hidden" name="product_name" value="PowerA MOGA XP5-A Bluetooth Controller" />
            <input type="hidden" name="product_price" value="12000" />
            <input type="hidden" name="product_image" value="image/gaming 6.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button 
            class="add-to-wishlist-btn" 
            data-product-id="308" 
            data-product-name="PowerA MOGA XP5-A Bluetooth Controller" 
            data-product-price="12000" 
            data-product-image="image/gaming 6.png"
          >❤</button>
          <a href="buy-now.php?id=308" class="buy-now-btn">Buy Now</a>
        </div>
      </div>
      <h3>PowerA MOGA XP5-A Bluetooth Controller</h3>
      <p class="price">Rs.12000</p>
    </div>
  <!-- Product 9 -->
<div class="product-card">
  <div class="product-image-wrapper">
    <a href="product-details.php?id=309">
      <img src="image/gaming 7.png" alt="Gaming Headset">
    </a>
    <div class="product-icons">
      <form action="add_to_cart.php" method="post">
        <input type="hidden" name="product_id" value="309">
        <input type="hidden" name="product_name" value="Gaming Headset">
        <input type="hidden" name="product_price" value="4800">
        <input type="hidden" name="product_image" value="image/gaming 7.png">
        <button type="submit" class="add-to-cart-btn">🛒</button>
      </form>
      <button 
        class="add-to-wishlist-btn" 
        data-product-id="309" 
        data-product-name="Gaming Headset"
        data-product-price="4800"
        data-product-image="image/gaming 7.png"
      >❤ </button>
      <a href="buy-now.php?id=309" class="buy-now-btn">Buy Now</a>
    </div>
  </div>
  <h3>Gaming Headset</h3>
  <p class="price">Rs.4800</p>
</div>

<!-- Product 10 -->
<div class="product-card">
  <div class="product-image-wrapper">
    <a href="product-details.php?id=310">
      <img src="image/gaming 10.png" alt="Gaming Keyboard">
    </a>
    <div class="product-icons">
      <form action="add_to_cart.php" method="post">
        <input type="hidden" name="product_id" value="310">
        <input type="hidden" name="product_name" value="Gaming Keyboard">
        <input type="hidden" name="product_price" value="3800">
        <input type="hidden" name="product_image" value="image/gaming 10.png">
        <button type="submit" class="add-to-cart-btn">🛒</button>
      </form>
      <button 
        class="add-to-wishlist-btn" 
        data-product-id="310"
        data-product-name="Gaming Keyboard"
        data-product-price="3800"
        data-product-image="image/gaming 10.png"
      >❤</button>
      <a href="buy-now.php?id=310" class="buy-now-btn">Buy Now</a>
    </div>
  </div>
  <h3>Gaming Keyboard</h3>
  <p class="price">Rs.3800</p>
</div>
<!-- Product 11 -->
<div class="product-card">
  <div class="product-image-wrapper">
    <a href="product-details.php?id=311">
      <img src="image/gaming 9.png" alt="Gaming Keyboard">
    </a>
    <div class="product-icons">
      <form action="add_to_cart.php" method="post">
        <input type="hidden" name="product_id" value="311">
        <input type="hidden" name="product_name" value="Gaming Mouse">
        <input type="hidden" name="product_price" value="1200">
        <input type="hidden" name="product_image" value="image/gaming 9.png">
        <button type="submit" class="add-to-cart-btn">🛒</button>
      </form>
      <button 
        class="add-to-wishlist-btn" 
        data-product-id="311"
        data-product-name="Gaming Keyboard"
        data-product-price="1200"
        data-product-image="image/gaming 9.png"
      >❤</button>
      <a href="buy-now.php?id=311" class="buy-now-btn">Buy Now</a>
    </div>
  </div>
  <h3>Gaming Keyboard</h3>
  <p class="price">Rs.1200</p>
</div>
<!-- Product 12 -->
<div class="product-card">
  <div class="product-image-wrapper">
    <a href="product-details.php?id=312">
      <img src="image/gaming 8.png" alt="Gaming Keyboard">
    </a>
    <div class="product-icons">
      <form action="add_to_cart.php" method="post">
        <input type="hidden" name="product_id" value="312">
        <input type="hidden" name="product_name" value="Gaming Keyboard">
        <input type="hidden" name="product_price" value="1000">
        <input type="hidden" name="product_image" value="image/gaming 8.png">
        <button type="submit" class="add-to-cart-btn">🛒</button>
      </form>
      <button 
        class="add-to-wishlist-btn" 
        data-product-id="312"
        data-product-name="Gaming Keyboard"
        data-product-price="1000"
        data-product-image="image/gaming 8.png"
      >❤</button>
      <a href="buy-now.php?id=312" class="buy-now-btn">Buy Now</a>
    </div>
  </div>
  <h3>Gaming Keyboard</h3>
  <p class="price">Rs.1000</p>
</div>
  </div>
</section>
<!-- ========== Video Banner Section ========== -->
<section class="video-banner">
  <video autoplay muted loop playsinline>
    <source src="vedio/new banner.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <div class="video-overlay">
    <h1>Welcome to Technoshop</h1>
    <p>Best Tech Deals in Sri Lanka</p>
  </div>
</section>



<!-- Latest Accessories Section -->
<section class="latest-laptops">
  <h2>Latest Accessories</h2>
  <div class="laptop-carousel">
    <div class="laptop-track">

      <!-- Accessory 1 -->
      <div class="laptop-card">
        <div class="product-image-wrapper">
          <a href="product-details.php?id=701">
            <img src="image/mouse1.png" alt="Toad Ergo 3 Mouse">
          </a>
          <div class="product-icons">
            <form action="add_to_cart.php" method="post">
              <input type="hidden" name="product_id" value="701">
              <input type="hidden" name="product_name" value="Toad Ergo 3">
              <input type="hidden" name="product_price" value="3800">
              <input type="hidden" name="product_image" value="image/mouse1.png">
              <button type="submit" class="add-to-cart-btn">🛒</button>
            </form>
            <button 
              class="add-to-wishlist-btn"
              data-product-id="201"
              data-product-name="Toad Ergo 3"
              data-product-price="3800"
              data-product-image="image/mouse1.png"
            >❤ </button>
            <a href="buy-now.php?id=701" class="buy-now-btn">Buy Now</a>
          </div>
        </div>
        <h3>Toad Ergo 3</h3>
        <p>Rs.3,800</p>
      </div>

      <!-- Accessory 2 -->
      <div class="laptop-card">
        <div class="product-image-wrapper">
          <a href="product-details.php?id=702">
            <img src="image/headset.png" alt="Wireless Headset">
          </a>
          <div class="product-icons">
            <form action="add_to_cart.php" method="post">
              <input type="hidden" name="product_id" value="202">
              <input type="hidden" name="product_name" value="Wireless Headset">
              <input type="hidden" name="product_price" value="7000">
              <input type="hidden" name="product_image" value="image/headset.png">
              <button type="submit" class="add-to-cart-btn">🛒</button>
            </form>
            <button 
              class="add-to-wishlist-btn"
              data-product-id="702"
              data-product-name="Wireless Headset"
              data-product-price="7000"
              data-product-image="image/headset.png"
            >❤</button>
            <a href="buy-now.php?id=702" class="buy-now-btn">Buy Now</a>
          </div>
        </div>
        <h3>Wireless Headset</h3>
        <p>Rs.7,000</p>
      </div>

      <!-- Accessory 3 -->
      <div class="laptop-card">
        <div class="product-image-wrapper">
          <a href="product-details.php?id=203">
            <img src="image/laptop stand.png" alt="Adjustable Laptop Stand">
          </a>
          <div class="product-icons">
            <form action="add_to_cart.php" method="post">
              <input type="hidden" name="product_id" value="703">
              <input type="hidden" name="product_name" value="Adjustable Laptop Stand">
              <input type="hidden" name="product_price" value="7500">
              <input type="hidden" name="product_image" value="image/laptop stand.png">
              <button type="submit" class="add-to-cart-btn">🛒</button>
            </form>
            <button 
              class="add-to-wishlist-btn"
              data-product-id="703"
              data-product-name="Adjustable Laptop Stand"
              data-product-price="7500"
              data-product-image="image/laptop stand.png"
            >❤</button>
            <a href="buy-now.php?id=703" class="buy-now-btn">Buy Now</a>
          </div>
        </div>
        <h3>Adjustable Laptop Stand</h3>
        <p>Rs.7,500</p>
      </div>

      <!-- Accessory 4 -->
      <div class="laptop-card">
        <div class="product-image-wrapper">
          <a href="product-details.php?id=704">
            <img src="image/keyboard1.png" alt="Wireless BANGLA Keyboard">
          </a>
          <div class="product-icons">
            <form action="add_to_cart.php" method="post">
              <input type="hidden" name="product_id" value="704">
              <input type="hidden" name="product_name" value="Wireless BANGLA Keyboard">
              <input type="hidden" name="product_price" value="2400">
              <input type="hidden" name="product_image" value="image/keyboard1.png">
              <button type="submit" class="add-to-cart-btn">🛒</button>
            </form>
            <button 
              class="add-to-wishlist-btn"
              data-product-id="704"
              data-product-name="Wireless BANGLA Keyboard"
              data-product-price="2400"
              data-product-image="image/keyboard1.png"
            >❤ </button>
            <a href="buy-now.php?id=704" class="buy-now-btn">Buy Now</a>
          </div>
        </div>
        <h3>Wireless BANGLA Keyboard</h3>
        <p>Rs.2,400</p>
      </div>

      <!-- Accessory 5 -->
      <div class="laptop-card">
        <div class="product-image-wrapper">
          <a href="product-details.php?id=705">
            <img src="image/Lenovo ThinkPad.png" alt="Lenovo ThinkPad">
          </a>
          <div class="product-icons">
            <form action="add_to_cart.php" method="post">
              <input type="hidden" name="product_id" value="705">
              <input type="hidden" name="product_name" value="Lenovo ThinkPad">
              <input type="hidden" name="product_price" value="9500">
              <input type="hidden" name="product_image" value="image/Lenovo ThinkPad.png">
              <button type="submit" class="add-to-cart-btn">🛒</button>
            </form>
            <button 
              class="add-to-wishlist-btn"
              data-product-id="705"
              data-product-name="Lenovo ThinkPad"
              data-product-price="9500"
              data-product-image="image/Lenovo ThinkPad.png"
            >❤</button>
            <a href="buy-now.php?id=205" class="buy-now-btn">Buy Now</a>
          </div>
        </div>
        <h3>Lenovo ThinkPad</h3>
        <p>Rs.9,500</p>
      </div>

      <!-- Accessory 6 -->
      <div class="laptop-card">
        <div class="product-image-wrapper">
          <a href="product-details.php?id=206">
            <img src="image/Poly Blackwire 3220 Stereo US.png" alt="Poly Blackwire 3220">
          </a>
          <div class="product-icons">
            <form action="add_to_cart.php" method="post">
              <input type="hidden" name="product_id" value="706">
              <input type="hidden" name="product_name" value="Poly Blackwire 3220">
              <input type="hidden" name="product_price" value="2900">
              <input type="hidden" name="product_image" value="image/Poly Blackwire 3220 Stereo US.png">
              <button type="submit" class="add-to-cart-btn">🛒</button>
            </form>
            <button 
              class="add-to-wishlist-btn"
              data-product-id="706"
              data-product-name="Poly Blackwire 3220"
              data-product-price="2900"
              data-product-image="image/Poly Blackwire 3220 Stereo US.png"
            >❤</button>
            <a href="buy-now.php?id=706" class="buy-now-btn">Buy Now</a>
          </div>
        </div>
        <h3>Poly Blackwire 3220</h3>
        <p>Rs.2,900</p>
      </div>

      <!-- Accessory 7 -->
      <div class="laptop-card">
        <div class="product-image-wrapper">
          <a href="product-details.php?id=707">
            <img src="image/gaming 9.png" alt="Gaming Mouse">
          </a>
          <div class="product-icons">
            <form action="add_to_cart.php" method="post">
              <input type="hidden" name="product_id" value="707">
              <input type="hidden" name="product_name" value="Gaming Mouse">
              <input type="hidden" name="product_price" value="1200">
              <input type="hidden" name="product_image" value="image/gaming 9.png">
              <button type="submit" class="add-to-cart-btn">🛒</button>
            </form>
            <button 
              class="add-to-wishlist-btn"
              data-product-id="707"
              data-product-name="Gaming Mouse"
              data-product-price="1200"
              data-product-image="image/gaming 9.png"
            >❤</button>
            <a href="buy-now.php?id=207" class="buy-now-btn">Buy Now</a>
          </div>
        </div>
        <h3>Gaming Mouse</h3>
        <p>Rs.1,200</p>
      </div>

    </div>
  </div>
</section>



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
