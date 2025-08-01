<?php
session_start();
include '../include/admin_auth.php';
include '../include/db.php';

$message = "";

// === Handle DELETE ===
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM peripheral WHERE id = $id");
    $message = "Product ID $id deleted.";
}

// === Handle ADD or UPDATE ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $image = "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imgName = basename($_FILES["image"]["name"]);
        $targetDir = "../image/";
        $targetFile = $targetDir . $imgName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
        $image = "image/" . $imgName;
    }

    $check = $conn->query("SELECT id FROM peripheral WHERE id = $id");
    if ($check && $check->num_rows > 0) {
        $query = "UPDATE peripheral SET name = '$name', price = $price";
        if ($image) $query .= ", image = '$image'";
        $query .= " WHERE id = $id";
        $conn->query($query);
        $message = "Updated product ID $id.";
    } else {
        $conn->query("INSERT INTO peripheral (id, name, price, image) VALUES ($id, '$name', $price, '$image')");
        $message = "Added new product ID $id.";
    }
}

// === Fetch all peripherals ===
$products = $conn->query("SELECT * FROM peripheral ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Peripherals</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', sans-serif; background: #eef1f5; }

    .topnav {
      background-color: #2c3e50;
      color: white;
      padding: 15px 20px;
      font-size: 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .topnav a {
      color: #f39c12;
      text-decoration: none;
      font-weight: bold;
    }

    .sidebar {
      position: fixed;
      top: 55px;
      left: 0;
      width: 220px;
      height: 100%;
      background-color: #34495e;
      padding-top: 20px;
    }

    .sidebar a {
      display: block;
      color: #ecf0f1;
      padding: 15px 20px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .sidebar a:hover {
      background-color: #2c3e50;
    }

    .main {
      margin-left: 220px;
      padding: 30px;
    }

    h1 {
      margin-bottom: 20px;
      color: #2c3e50;
    }

    .message {
      color: green;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .edit-form {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }

    .edit-form input, .edit-form button {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }

    th {
      background-color: #2c3e50;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .actions a {
      color: red;
      font-weight: bold;
      text-decoration: none;
    }

    img {
      max-width: 60px;
      height: auto;
    }

    @media (max-width: 768px) {
      .main { margin-left: 0; padding: 15px; }
      .sidebar { position: relative; width: 100%; height: auto; top: 0; }
    }
  </style>
</head>
<body>

<div class="topnav">
  <div>🛠 Manage Peripherals</div>
  <div>
    Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?> |
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="sidebar">
  <a href="dashboard.php">Dashboard</a>
  <a href="products.php">Manage Products</a>
  <a href="admin_manage_peripherals.php">Manage Peripherals</a>
  <a href="orders.php">Manage Orders</a>
  <a href="manage_users.php">Manage Users</a>
  <a href="messages.php">Messages</a>
</div>

<div class="main">
  <h1>Peripheral Products</h1>

  <?php if ($message): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>

  <!-- Add / Update Form -->
  <form class="edit-form" method="POST" enctype="multipart/form-data">
    <h3>Add or Update Product</h3>
    <input type="number" name="id" placeholder="Product ID" required>
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="number" name="price" step="0.01" placeholder="Price (Rs.)" required>
    <input type="file" name="image">
    <button type="submit">Save Product</button>
  </form>

  <!-- Products Table -->
  <table>
    <tr>
      <th>ID</th>
      <th>Image</th>
      <th>Name</th>
      <th>Price (Rs.)</th>
      <th>Action</th>
    </tr>
    <?php foreach ($products as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><img src="../<?= $p['image'] ?>" alt="<?= $p['name'] ?>"></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= number_format($p['price'], 2) ?></td>
        <td class="actions">
          <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Delete this product?');">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

</body>
</html>
