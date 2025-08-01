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
    /* Navigation Arrows */
.slideshow-container {
  position: relative;
}

.prev-slide,
.next-slide {
  position: absolute;
  top: 45%;
  transform: translateY(-50%);
  background-color: rgba(0, 0, 0, 0.5);
  border: none;
  color: white;
  font-size: 30px;
  padding: 10px 15px;
  cursor: pointer;
  z-index: 10;
  border-radius: 50%;
}

.prev-slide {
  left: 0;
}

.next-slide {
  right: 0;
}

.prev-slide:hover,
.next-slide:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

    /* Slideshow Container */
    .slideshow-container {
      width: 100%;
      overflow: hidden;
      position: relative;
      margin: 20px auto;
      max-width: 1400px;
    }

    /* Wrapper holding all slides horizontally */
    .slides-wrapper {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }

    /* Each slide card */
    .slide-card {
      flex: 0 0 20%; /* Show 5 cards at a time */
      box-sizing: border-box;
      padding: 10px;
      text-align: center;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin: 0 5px;
    }

    .slide-card img {
      max-width: 100%;
      height: auto;
      border-radius: 5px;
      margin-bottom: 10px;
    }

    .slide-card h3 {
      font-size: 1.1rem;
      margin: 5px 0;
    }

    .slide-card p {
      font-weight: bold;
      color: #444;
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


    /* Responsive */
    @media (max-width: 900px) {
      .slide-card {
        flex: 0 0 33.33%; /* 3 cards visible */
      }
    }

    @media (max-width: 600px) {
      .slide-card {
        flex: 0 0 50%; /* 2 cards visible */
      }
    }

    @media (max-width: 400px) {
      .slide-card {
        flex: 0 0 100%; /* 1 card visible */
      }
    }
    /* Banner Cards */
.banner-card-section {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 40px;
  padding: 0 20px;
  flex-wrap: wrap;
}
.banner-card {
  width: 100%;
  max-width: 900px;
}
.banner-card img {
  width: 100%;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.server-section {
    max-width: 1470px;
    margin: auto;
    padding: 40px 20px;
    font-family: sans-serif;
  }




  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
  }

  .product-card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    text-align: center;
    transition: transform 0.3s ease;
  }

  .product-card:hover {
    transform: translateY(-5px);
  }

  .product-card img {
    width: 100%;
    height: auto;
    margin-bottom: 10px;
  }

  .product-icons {
    margin-top: 10px;
  }


.desktop-section {
    max-width: 1500px;
    margin: 40px auto;
    padding: 0 20px;
    font-family: Arial, sans-serif;
  }
  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
    gap: 25px;
  }
  .product-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    padding: 15px;
    text-align: center;
    transition: transform 0.3s ease;
  }
  .product-card:hover {
    transform: translateY(-5px);
  }
  .product-card img {
    width: 100%;
    height: auto;
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
</div><br>
<section class="slideshow-section">
  <h2 style="text-align:center;">Desktop & Servers</h2><br>
  <div class="slideshow-container" id="desktopSlide">
     <button class="slide-arrow prev-slide" onclick="showPrevSlide()">&#10094;</button>
    <div class="slides-wrapper" id="slidesWrapper">

      <!-- Your 10 products here -->
      <!-- Product 1 -->
      <div class="slide-card">
        <a href="product1.php"><img src="image/ds1.png" alt="Dell Server" /></a><br><br><br><br>
        <h3>Dell PowerEdge R740</h3>
        <p>Rs. 700,00</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="200" />
            <input type="hidden" name="product_name" value="Dell PowerEdge R740" />
            <input type="hidden" name="product_price" value="70000" />
            <input type="hidden" name="product_image" value="image/ds1.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="200"
            data-product-name="Dell PowerEdge R740"
            data-product-price="70000"
            data-product-image="image/ds1.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=200" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 2 -->
      <div class="slide-card">
        <a href="product2.php"><img src="image/ds2.png" alt="HP Server" /></a><br><br><br>
        <h3>HP ProLiant DL380 Gen10</h3>
        <p>Rs. 735,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="201" />
            <input type="hidden" name="product_name" value="HP ProLiant DL380 Gen10" />
            <input type="hidden" name="product_price" value="735000" />
            <input type="hidden" name="product_image" value="image/ds2.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="201"
            data-product-name="HP ProLiant DL380 Gen10"
            data-product-price="735000"
            data-product-image="image/ds2.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=201" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 3 -->
      <div class="slide-card">
        <a href="product3.php"><img src="image/ds3.png" alt="iMac" /></a><br><br><br>
        <h3>Apple iMac 24” M3</h3>
        <p>Rs. 599,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="202" />
            <input type="hidden" name="product_name" value="Apple iMac 24” M3" />
            <input type="hidden" name="product_price" value="599000" />
            <input type="hidden" name="product_image" value="image/ds3.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="202"
            data-product-name="Apple iMac 24” M3"
            data-product-price="599000"
            data-product-image="image/ds3.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=202" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 4 -->
      <div class="slide-card">
        <a href="product4.php"><img src="image/ds4.png" alt="Lenovo Desktop" /></a><br><br><br><br>
        <h3>Lenovo ThinkCentre M920</h3>
        <p>Rs. 285,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="203" />
            <input type="hidden" name="product_name" value="Lenovo ThinkCentre M920" />
            <input type="hidden" name="product_price" value="285000" />
            <input type="hidden" name="product_image" value="image/ds4.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="203"
            data-product-name="Lenovo ThinkCentre M920"
            data-product-price="285000"
            data-product-image="image/ds4.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=203" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 5 -->
      <div class="slide-card">
        <a href="product5.php"><img src="image/ds5.png" alt="Mini Server" /></a><br><br><br>
        <h3>Mini Server Intel</h3>
        <p>Rs. 310,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="204" />
            <input type="hidden" name="product_name" value="Mini Server Intel" />
            <input type="hidden" name="product_price" value="310000" />
            <input type="hidden" name="product_image" value="image/ds5.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="204"
            data-product-name="Mini Server Intel"
            data-product-price="310000"
            data-product-image="image/ds5.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=204" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 6 -->
      <div class="slide-card">
        <a href="product6.php"><img src="image/ds6.png" alt="Acer Veriton" /></a><br><br><br>
        <h3>Acer Veriton X Series</h3>
        <p>Rs. 165,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="205" />
            <input type="hidden" name="product_name" value="Acer Veriton X Series" />
            <input type="hidden" name="product_price" value="165000" />
            <input type="hidden" name="product_image" value="image/ds6.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="205"
            data-product-name="Acer Veriton X Series"
            data-product-price="165000"
            data-product-image="image/ds6.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=205" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 7 -->
      <div class="slide-card">
        <a href="product7.php"><img src="image/ds7.png" alt="Dell OptiPlex" /></a><br><br>
        <h3>Dell OptiPlex 7090</h3>
        <p>Rs. 225,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="206" />
            <input type="hidden" name="product_name" value="Dell OptiPlex 7090" />
            <input type="hidden" name="product_price" value="225000" />
            <input type="hidden" name="product_image" value="image/ds7.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="206"
            data-product-name="Dell OptiPlex 7090"
            data-product-price="225000"
            data-product-image="image/ds7.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=206" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 8 -->
      <div class="slide-card">
        <a href="product8.php"><img src="image/ds8.png" alt="Intel Server" /></a>
        <h3>Intel Server R1304</h3>
        <p>Rs. 470,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="207" />
            <input type="hidden" name="product_name" value="Intel Server R1304" />
            <input type="hidden" name="product_price" value="470000" />
            <input type="hidden" name="product_image" value="image/ds8.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="207"
            data-product-name="Intel Server R1304"
            data-product-price="470000"
            data-product-image="image/ds8.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=207" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 9 -->
      <div class="slide-card">
        <a href="product9.php"><img src="image/ds9.png" alt="MINISFORUM" /></a><br><br><br>
        <h3>MINISFORUM Venus NPB5</h3>
        <p>Rs. 180,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="208" />
            <input type="hidden" name="product_name" value="MINISFORUM Venus NPB5" />
            <input type="hidden" name="product_price" value="180000" />
            <input type="hidden" name="product_image" value="image/ds9.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="208"
            data-product-name="MINISFORUM Venus NPB5"
            data-product-price="180000"
            data-product-image="image/ds9.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=208" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

      <!-- Product 10 -->
      <div class="slide-card">
        <a href="product10.php"><img src="image/ds10.png" alt="HP EliteDesk" /></a>
        <h3>HP EliteDesk 800 G6</h3>
        <p>Rs. 310,000</p>
        <div class="product-icons">
          <form action="add_to_cart.php" method="post">
            <input type="hidden" name="product_id" value="209" />
            <input type="hidden" name="product_name" value="HP EliteDesk 800 G6" />
            <input type="hidden" name="product_price" value="310000" />
            <input type="hidden" name="product_image" value="image/ds10.png" />
            <button type="submit" class="add-to-cart-btn">🛒</button>
          </form>
          <button
            class="add-to-wishlist-btn"
            data-product-id="209"
            data-product-name="HP EliteDesk 800 G6"
            data-product-price="310000"
            data-product-image="image/ds10.png"
          >
            ❤
          </button>
          <a href="buy-now.php?id=209" class="buy-now-btn">Buy Now</a>
        </div>
      </div>

    </div>
  
   <button class="slide-arrow next-slide" onclick="showNextSlide()">&#10095;</button>
</div>
</section>
<script>
  const slidesWrapper = document.getElementById('slidesWrapper');
  const totalSlides = slidesWrapper.children.length;
  const visibleSlides = window.innerWidth <= 600 ? 2 : window.innerWidth <= 900 ? 3 : 5;
  let currentIndex = 0;

  function updateSlidePosition() {
    slidesWrapper.style.transform = `translateX(-${currentIndex * (100 / visibleSlides)}%)`;
  }

  function showNextSlide() {
    if (currentIndex < totalSlides - visibleSlides) {
      currentIndex++;
    } else {
      currentIndex = 0;
    }
    updateSlidePosition();
  }

  function showPrevSlide() {
    if (currentIndex > 0) {
      currentIndex--;
    } else {
      currentIndex = totalSlides - visibleSlides;
    }
    updateSlidePosition();
  }

  // Auto slide
  setInterval(showNextSlide, 3000);
</script>

  <!-- Banner Cards Below -->
  <div class="banner-card-section">
    <div class="banner-card">
      <a href="banner1-link.php"><img src="image/banner1.png" alt="Server Sale" /></a>
    </div>
    <div class="banner-card">
      <a href="banner2-link.php"><img src="image/banner2.png" alt="comming soon" /></a>
    </div>
  </div><br>

<section class="server-section">
  <h2 style="text-align: center;">latest Products</h2><br>

  

  <!-- Server Products Grid -->
  <div class="product-grid">
    <!-- Product 1 -->
    <div class="product-card">
      <a href="product1.php"><img src="image/ds1.png" alt="Dell Server"></a>
      <h3>Dell PowerEdge R740</h3>
      <p>Rs. 70,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="600" />
          <input type="hidden" name="product_name" value="Dell PowerEdge R740" />
          <input type="hidden" name="product_price" value="70000" />
          <input type="hidden" name="product_image" value="image/ds1.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="600"
          data-product-name="Dell PowerEdge R740"
          data-product-price="70000"
          data-product-image="image/ds1.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=600" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 2 -->
    <div class="product-card">
      <a href="product2.php"><img src="image/ds2.png" alt="HP Server"></a>
      <h3>HP ProLiant DL380 Gen10</h3>
      <p>Rs. 735,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="601" />
          <input type="hidden" name="product_name" value="HP ProLiant DL380 Gen10" />
          <input type="hidden" name="product_price" value="735000" />
          <input type="hidden" name="product_image" value="image/ds2.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="601"
          data-product-name="HP ProLiant DL380 Gen10"
          data-product-price="735000"
          data-product-image="image/ds2.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=601" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 3 -->
    <div class="product-card">
      <a href="product3.php"><img src="image/ds3.png" alt="Apple iMac"></a>
      <h3>Apple iMac 24” M3</h3>
      <p>Rs. 599,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="602" />
          <input type="hidden" name="product_name" value="Apple iMac 24” M3" />
          <input type="hidden" name="product_price" value="599000" />
          <input type="hidden" name="product_image" value="image/ds3.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="602"
          data-product-name="Apple iMac 24” M3"
          data-product-price="599000"
          data-product-image="image/ds3.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=602" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 4 -->
    <div class="product-card">
      <a href="product4.php"><img src="image/ds4.png" alt="Lenovo Desktop"></a><br><br>
      <h3>Lenovo ThinkCentre M920</h3>
      <p>Rs. 285,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="603" />
          <input type="hidden" name="product_name" value="Lenovo ThinkCentre M920" />
          <input type="hidden" name="product_price" value="285000" />
          <input type="hidden" name="product_image" value="image/ds4.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="603"
          data-product-name="Lenovo ThinkCentre M920"
          data-product-price="285000"
          data-product-image="image/ds4.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=603" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 5 -->
    <div class="product-card">
      <a href="product5.php"><img src="image/ds5.png" alt="Mini Server"></a>
      <h3>Mini Server Intel</h3>
      <p>Rs. 310,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="604" />
          <input type="hidden" name="product_name" value="Mini Server Intel" />
          <input type="hidden" name="product_price" value="310000" />
          <input type="hidden" name="product_image" value="image/ds5.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="604"
          data-product-name="Mini Server Intel"
          data-product-price="310000"
          data-product-image="image/ds5.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=604" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 6 -->
    <div class="product-card">
      <a href="product6.php"><img src="image/ds6.png" alt="Acer Veriton"></a><br><br>
      <h3>Acer Veriton X Series</h3>
      <p>Rs. 165,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="605" />
          <input type="hidden" name="product_name" value="Acer Veriton X Series" />
          <input type="hidden" name="product_price" value="165000" />
          <input type="hidden" name="product_image" value="image/ds6.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="605"
          data-product-name="Acer Veriton X Series"
          data-product-price="165000"
          data-product-image="image/ds6.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=605" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 7 -->
    <div class="product-card">
      <a href="product7.php"><img src="image/ds7.png" alt="Dell OptiPlex"></a>
      <h3>Dell OptiPlex 7090</h3>
      <p>Rs. 225,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="606" />
          <input type="hidden" name="product_name" value="Dell OptiPlex 7090" />
          <input type="hidden" name="product_price" value="225000" />
          <input type="hidden" name="product_image" value="image/ds7.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="606"
          data-product-name="Dell OptiPlex 7090"
          data-product-price="225000"
          data-product-image="image/ds7.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=606" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 8 -->
    <div class="product-card">
      <a href="product8.php"><img src="image/ds8.png" alt="Intel Server"></a>
      <h3>Intel Server R1304</h3>
      <p>Rs. 470,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="607" />
          <input type="hidden" name="product_name" value="Intel Server R1304" />
          <input type="hidden" name="product_price" value="470000" />
          <input type="hidden" name="product_image" value="image/ds8.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="607"
          data-product-name="Intel Server R1304"
          data-product-price="470000"
          data-product-image="image/ds8.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=607" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 9 -->
    <div class="product-card">
      <a href="product9.php"><img src="image/ds9.png" alt="MINISFORUM"></a><br><br><br>
      <h3>MINISFORUM Venus NPB5</h3>
      <p>Rs. 180,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="608" />
          <input type="hidden" name="product_name" value="MINISFORUM Venus NPB5" />
          <input type="hidden" name="product_price" value="180000" />
          <input type="hidden" name="product_image" value="image/ds9.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="608"
          data-product-name="MINISFORUM Venus NPB5"
          data-product-price="180000"
          data-product-image="image/ds9.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=608" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Product 10 -->
    <div class="product-card">
      <a href="product10.php"><img src="image/ds10.png" alt="HP EliteDesk"></a>
      <h3>HP EliteDesk 800 G6</h3>
      <p>Rs. 310,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="609" />
          <input type="hidden" name="product_name" value="HP EliteDesk 800 G6" />
          <input type="hidden" name="product_price" value="310000" />
          <input type="hidden" name="product_image" value="image/ds10.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="609"
          data-product-name="HP EliteDesk 800 G6"
          data-product-price="310000"
          data-product-image="image/ds10.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=609" class="buy-now-btn">Buy Now</a>
      </div>
    </div>
  </div>
</section>




<section class="desktop-section">
  <h2 style="text-align:center;">Desktops</h2><br>

  <div class="product-grid">
    <!-- Desktop Product 1 -->
    <div class="product-card">
      <a href="desktop1.php"><img src="image/desktop1.png" alt="Dell OptiPlex 3080"></a><br><br>
      <h3>Dell OptiPlex 3080</h3>
      <p>Rs. 120,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="501" />
          <input type="hidden" name="product_name" value="Dell OptiPlex 3080" />
          <input type="hidden" name="product_price" value="120000" />
          <input type="hidden" name="product_image" value="image/desktop1.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="501" data-product-name="Dell OptiPlex 3080" data-product-price="120000" data-product-image="image/desktop1.png">❤</button>
        <a href="buy-now.php?id=501" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Desktop Product 2 -->
    <div class="product-card">
      <a href="desktop2.php"><img src="image/desktop2.png" alt="HP EliteDesk 800"></a>
      <h3>HP EliteDesk 800</h3>
      <p>Rs. 135,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="502" />
          <input type="hidden" name="product_name" value="HP EliteDesk 800" />
          <input type="hidden" name="product_price" value="135000" />
          <input type="hidden" name="product_image" value="image/desktop2.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="502" data-product-name="HP EliteDesk 800" data-product-price="135000" data-product-image="image/desktop2.png">❤</button>
        <a href="buy-now.php?id=502" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Desktop Product 3 -->
    <div class="product-card">
      <a href="desktop3.php"><img src="image/desktop3.png" alt="Lenovo ThinkCentre M75q"></a><br><br><br><br>
      <h3>Lenovo ThinkCentre M75q</h3>
      <p>Rs. 115,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="503" />
          <input type="hidden" name="product_name" value="Lenovo ThinkCentre M75q" />
          <input type="hidden" name="product_price" value="115000" />
          <input type="hidden" name="product_image" value="image/desktop3.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="503" data-product-name="Lenovo ThinkCentre M75q" data-product-price="115000" data-product-image="image/desktop3.png">❤</button>
        <a href="buy-now.php?id=503" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Desktop Product 4 -->
    <div class="product-card">
      <a href="desktop4.php"><img src="image/desktop4.png" alt="Acer Veriton X"></a><br><br><br><br>
      <h3>Acer Veriton X</h3>
      <p>Rs. 110,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="504" />
          <input type="hidden" name="product_name" value="Acer Veriton X" />
          <input type="hidden" name="product_price" value="110000" />
          <input type="hidden" name="product_image" value="image/desktop4.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="504" data-product-name="Acer Veriton X" data-product-price="110000" data-product-image="image/desktop4.png">❤</button>
        <a href="buy-now.php?id=504" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Desktop Product 5 -->
    <div class="product-card">
      <a href="desktop5.php"><img src="image/desktop5.png" alt="MSI Pro 24X"></a><br><br><br><br><br>
      <h3>MSI Pro 24X</h3>
      <p>Rs. 140,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="505" />
          <input type="hidden" name="product_name" value="MSI Pro 24X" />
          <input type="hidden" name="product_price" value="140000" />
          <input type="hidden" name="product_image" value="image/desktop5.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button class="add-to-wishlist-btn" data-product-id="505" data-product-name="MSI Pro 24X" data-product-price="140000" data-product-image="image/desktop5.png">❤</button>
        <a href="buy-now.php?id=505" class="buy-now-btn">Buy Now</a>
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