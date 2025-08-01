<?php
session_start();
include 'db.php';

// Initialize variables to avoid undefined variable warnings
$error = '';
$success = '';

// Session ID for cart count
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}
$session_id = $_SESSION['session_id'];

// Get cart count from database
$cartCountResult = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE session_id = '$session_id'");
$cartCountRow = $cartCountResult->fetch_assoc();
$cart_count = $cartCountRow['total'] ?? 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correctEmail = "roshaninisansala098@gmail.com"; // Replace with your official email

    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif ($email !== $correctEmail) {
        $error = "Message could not be sent. (Correct mail is website login email.)";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (first_name, last_name, email, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $firstName, $lastName, $email, $message);

        if ($stmt->execute()) {
            $success = "Message sent successfully!";
            // Clear form data after success to prevent resubmission
            $_POST = [];
        } else {
            $error = "Something went wrong. Please try again later.";
        }

        $stmt->close();
    }
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
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us - Computer Shop</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="contact.css" />
  <link rel="stylesheet" href="footer.css" />
  <script src="script.js" defer></script>
  <script src="wishlist.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
      <img src="logo.png" alt="Computer Shop" />
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
          <span>Welcome, <a href="profile.php"><?= htmlspecialchars($_SESSION['username']) ?></a>!</span>
          <a href="logout.php">Logout</a>
      <?php else: ?>
          <a href="login.html">Login</a> | <a href="register.html">Register</a>
      <?php endif; ?>

      <a href="wishlist_page.php" class="wishlist-icon">❤ <span id="wishlist-count">0</span></a>

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
          display: inline-block;">
          <?= $cart_count ?>
        </span>
      </a>
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

  <div class="contact-container">
    <h1>Contact Us</h1>
    <p>You can contact our customer service for all questions and inquiries on any of the channels listed below during office hours.</p>

    <?php if (!empty($error)): ?>
      <script>alert("<?= htmlspecialchars($error) ?>");</script>
    <?php elseif (!empty($success)): ?>
      <script>alert("<?= htmlspecialchars($success) ?>");</script>
    <?php endif; ?>

    <form method="post" class="contact-form" onsubmit="return validateForm()">
      <div class="form-group">
        <input type="text" name="first_name" placeholder="First Name" required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" />
        <input type="text" name="last_name" placeholder="Last Name" required value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" />
      </div>
      <div class="form-group">
        <input type="email" name="email" id="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
      </div>
      <div class="form-group">
        <textarea name="message" rows="5" placeholder="Your Message" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
      </div>
      <div class="form-group checkbox">
        <input type="checkbox" id="not_robot" required />
        <label for="not_robot">I'm not a robot</label>
      </div>
      <button type="submit">Send Message</button>
    </form>

    <div class="info-boxes">
      <a href="tel:+94 76 408 1994" class="info-box">📞 Contact Number</a>
      <a href="https://wa.me/1234567890" class="info-box">💬 WhatsApp</a>
      <a href="mailto:computer@technoshop.com" class="info-box">📧 Email</a>
      <a href="index.php" class="info-box">🏬 Our Shop</a>
    </div>

    <div class="contact-banner">
      <img src="image/contact.png" alt="Contact Us Banner" />
      <div class="banner-text">
        <h2>We're Here to Help</h2>
      </div>
    </div>
  </div>

  <script>
    // Example validateForm function - you can customize it further
    function validateForm() {
      const checkbox = document.getElementById('not_robot');
      if (!checkbox.checked) {
        alert("Please confirm you are not a robot.");
        return false;
      }
      return true;
    }
  </script>

  <script>
    // Wishlist button listener example (optional)
    document.querySelectorAll('.add-to-wishlist-btn').forEach(button => {
      button.addEventListener('click', function () {
        const product = {
          id: this.dataset.productId,
          name: this.dataset.productName,
          price: this.dataset.productPrice,
          image: this.dataset.productImage
        };
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
    updateCartCount();
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
