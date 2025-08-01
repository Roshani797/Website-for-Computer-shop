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
  /* General Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', sans-serif;
  background: #f2f2f2;
  color: #222;
  line-height: 1.6;
}

.payment-info {
  max-width: 1400px;
  margin: 40px auto;
  background: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Titles */
.payment-info h2 {
  font-size: 28px;
  color: #222;
  margin-bottom: 10px;
  text-align: center;
}

.payment-info h3 {
  font-size: 18px;
  color: #555;
  text-align: center;
  margin-bottom: 30px;
}

/* Notice List Styling */
.notice-list {
  list-style: none;
  padding: 0;
  margin-bottom: 40px;
}

.notice-list li {
  background: #fffce9;
  padding: 18px 20px;
  margin-bottom: 15px;
  border-left: 5px solid #ffcc00;
  border-radius: 6px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  font-size: 16px;
}

.notice-list strong {
  color: #000;
}

/* Bank Section */
.bank-details {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: space-between;
}

.bank-card {
  background: #f9f9f9;
  padding: 25px;
  flex: 1 1 calc(33.333% - 20px);
  border-left: 5px solid #0073e6;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  transition: transform 0.3s;
}

.bank-card:hover {
  transform: translateY(-5px);
}

.bank-card h3 {
  margin-bottom: 10px;
  color: #0073e6;
  font-size: 20px;
}

.bank-card p {
  margin: 5px 0;
  font-size: 15px;
  color: #333;
}

/* Responsive Design */
@media (max-width: 768px) {
  .bank-card {
    flex: 1 1 100%;
  }

  .payment-info h2 {
    font-size: 24px;
  }

  .payment-info h3 {
    font-size: 16px;
  }

  .notice-list li {
    font-size: 15px;
  }
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

  <section class="payment-info">
    <h2>💳 Payment එකක් කිරීමට පෙර සැලකිලිමත් විය යුතු කරුණු</h2>
    <h3>Consider below details before making a payment</h3>

    <ul class="notice-list">
      <li>1.ඔබ payment එකක් සිදු කිරීමට පෙර අනිවාර්යයෙන් ම ඔබට අවශ්‍ය භාණ්ඩය shop එකෙහි stock පවතී ද යන්න අප අමතා ස්ථීර කරගත යුතුය.<br>
         <strong>Before you make a payment</strong>, make sure the item is in stock by contacting us.</li>

      <li>2.Cash on delivery <strong>නොමැත</strong>.<br>
         <strong>No cash on delivery.</strong></li>

      <li>3.භාණ්ඩය courier කිරීමට පෙර සියලුම මුදල් ගෙවා තිබිය යුතුය.<br>
         <strong>All payments must be made prior to couriering.</strong></li>

      <li>4.ඔබ භාණ්ඩයක් වෙන් කරගනු ලබන්නේ නම් (pre-order), භාණ්ඩයේ වටිනාකමින් <strong>50%</strong> ක් ගෙවිය යුතුය.<br>
         <strong>Pre-orders require 50% advance payment.</strong></li>

      <li>5.ඔබ payment එක කිරීමෙන් පසු cash deposit machine එක හෝ online transfer මඟින් සිදුකරන්නේ නම් එහි slip එකේ screenshot එක WhatsApp මාර්ගයෙන් අපට එවිය යුතුය.<br>
         <strong>Send payment slip screenshot via WhatsApp:</strong>0771387684</li>

      <li>6.ඒ සමඟම ඔබගේ නම, ලිපිනය, දුරකතන අංක 2ක් යොමු කළ යුතුය.<br>
          Include your <strong>name, address, and 2 phone numbers</strong>. If your address is outside a major city, mention the nearest major city or district.</li>

      <li>7.Card payment සිදු කළ හැක්කේ <strong>shop එකට පැමිණීමෙන් පමණි</strong>.<br>
         <strong>Card payments can only be made in-store.</strong></li>

      <li>8.Amex Card සඳහා <strong>4%</strong>, Visa/Master සඳහා <strong>3.5%</strong> කටයුතු මතම වැය වේ.<br>
          Card fees: 4% (Amex), 3.5% (Visa/Master)</li>
    </ul>

    

<h2>🏦 Bank Account Details</h2>

<div class="bank-details">
<div class="bank-card">
<h3>People's Bank</h3>
<p><strong>Branch:</strong>Ratnapura</p>
<p><strong>Account No:</strong>1234567890</p> 
<p><strong>Name:</strong>N.A.R.N.Ranjith</p> 
</div> 

<div class="bank-card"> 
<h3>Commercial Bank</h3> 
<p><strong>Branch:</strong>Ratnapura</p> 
<p><strong>Account No:</strong>123456789</p> 
<p><strong>Name:</strong> TECHNO SHOP</p> 
</div> 

<div class="bank-card"> 
<h3>Ceylon Bank</h3> 
<p><strong>Branch:</strong>Ratnapura</p> 
<p><strong>Account No:</strong> 02801320878340</p> 
<p><strong>Name:</strong>N.A.R.N.Ranjith</p> 
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