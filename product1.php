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


$product_id = 1; // current product id

// Handle review form submission
$review_error = '';
$review_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    // Check if user is logged in to allow review submission
    if (!isset($_SESSION['username'])) {
        $review_error = "You must be logged in to submit a review.";
    } else {
        $username = $_SESSION['username'];
        $rating = intval($_POST['rating']);
        $review_text = trim($_POST['review_text']);

        // Basic validation
        if ($rating < 1 || $rating > 5) {
            $review_error = "Please select a rating between 1 and 5.";
        } elseif (empty($review_text)) {
            $review_error = "Review text cannot be empty.";
        } else {
            // Insert review into DB
            $stmt = $conn->prepare("INSERT INTO product_reviews (product_id, username, rating, review_text) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $product_id, $username, $rating, $review_text);
            if ($stmt->execute()) {
                $review_success = "Thank you! Your review has been submitted.";
            } else {
                $review_error = "Failed to submit review. Please try again later.";
            }
            $stmt->close();
        }
    }
}

// Fetch all reviews for this product
$reviews = [];
$stmt = $conn->prepare("SELECT username, rating, review_text, created_at FROM product_reviews WHERE product_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MSI Laptop - Product Details</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="footer.css" />
  <link rel="stylesheet" href="laptop.css" />
  <link rel="stylesheet" href="product_details.css" />
  <script src="script.js" defer></script>
  <script src="wishlist.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    
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

    /* Existing styles... */
    .review-form {
      margin-top: 2rem;
      border: 1px solid #ddd;
      padding: 1rem;
      max-width: 600px;
    }
    .review-form textarea {
      width: 100%;
      resize: vertical;
      padding: 0.5rem;
      font-size: 1rem;
    }
    .review-form select, .review-form button {
      margin-top: 0.5rem;
      padding: 0.5rem;
      font-size: 1rem;
    }
    .review-message {
      margin: 1rem 0;
      padding: 0.5rem;
      border-radius: 5px;
    }
    .review-message.success {
      background-color: #d4edda;
      color: #155724;
    }
    .review-message.error {
      background-color: #f8d7da;
      color: #721c24;
    }
    .review-list {
      max-width: 600px;
      margin-top: 2rem;
    }
    .review-list ul {
      list-style: none;
      padding-left: 0;
    }
    .review-list li {
      border-bottom: 1px solid #ddd;
      padding: 1rem 0;
    }
    .review-list .rating {
      color: gold;
      font-size: 1.2rem;
    }
    .review-list .username {
      font-weight: bold;
    }
    .review-list .date {
      color: #555;
      font-size: 0.9rem;
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
     
    </div>
  </div>
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
  <!-- Your existing header, nav, etc. -->

  <div class="product-detail-container">
    <h1>MSI</h1>
    <div class="product-card">
      <a href="product1.php"><img src="image/1.png" alt="MSI Laptop" /></a>
      <h3>MSI</h3>
      <p>Rs. 310,000</p>
      <div class="product-icons">
        <form action="add_to_cart.php" method="post">
          <input type="hidden" name="product_id" value="1" />
          <input type="hidden" name="product_name" value="MSI" />
          <input type="hidden" name="product_price" value="310000" />
          <input type="hidden" name="product_image" value="image/1.png" />
          <button type="submit" class="add-to-cart-btn">🛒</button>
        </form>
        <button
          class="add-to-wishlist-btn"
          data-product-id="1"
          data-product-name="MSI"
          data-product-price="310000"
          data-product-image="image/1.png"
        >
          ❤
        </button>
        <a href="buy-now.php?id=1" class="buy-now-btn">Buy Now</a>
      </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews">
      <h3>Item Reviews</h3>

      <!-- Display messages -->
      <?php if ($review_success): ?>
        <div class="review-message success"><?= htmlspecialchars($review_success) ?></div>
      <?php elseif ($review_error): ?>
        <div class="review-message error"><?= htmlspecialchars($review_error) ?></div>
      <?php endif; ?>

      <!-- List reviews -->
      <div class="review-list">
        <?php if (count($reviews) > 0): ?>
          <ul>
            <?php foreach ($reviews as $review): ?>
              <li>
                <div class="username"><?= htmlspecialchars($review['username']) ?></div>
                <div class="rating"><?= str_repeat("⭐", $review['rating']) ?></div>
                <div class="text"><?= nl2br(htmlspecialchars($review['review_text'])) ?></div>
                <div class="date"><?= date('F j, Y, g:i a', strtotime($review['created_at'])) ?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No reviews yet. Be the first to review!</p>
        <?php endif; ?>
      </div>

      <!-- Review submission form -->
      <?php if (isset($_SESSION['username'])): ?>
      <form class="review-form" method="post" action="">
        <h4>Submit Your Review</h4>
        <label for="rating">Rating:</label>
        <select name="rating" id="rating" required>
          <option value="">Select rating</option>
          <option value="5">⭐⭐⭐⭐⭐ 5</option>
          <option value="4">⭐⭐⭐⭐ 4</option>
          <option value="3">⭐⭐⭐ 3</option>
          <option value="2">⭐⭐ 2</option>
          <option value="1">⭐ 1</option>
        </select>
        <label for="review_text">Review:</label>
        <textarea name="review_text" id="review_text" rows="4" required><?= htmlspecialchars($_POST['review_text'] ?? '') ?></textarea>
        <button type="submit" name="submit_review">Submit Review</button>
      </form>
      <?php else: ?>
        <p><a href="login.html">Login</a> to submit a review.</p>
      <?php endif; ?>
    </div>

    <a href="index.php">← Back to Products</a>
  </div>

<script>
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
