<?php
include '../include/admin_auth.php';
include '../include/db.php';

function getCount($conn, $query) {
    $res = $conn->query($query);
    return ($res && ($row = $res->fetch_assoc())) ? (int)$row['total'] : 0;
}
function getSum($conn, $query) {
    $res = $conn->query($query);
    return ($res && ($row = $res->fetch_assoc())) ? (float)$row['total'] : 0;
}

$tdate = date('Y-m-d');

$totalOrders    = getCount($conn, "SELECT COUNT(*) AS total FROM orders");
$totalProducts  = getCount($conn, "SELECT COUNT(*) AS total FROM products");
$totalUsers     = getCount($conn, "SELECT COUNT(*) AS total FROM users");
$totalMessages  = getCount($conn, "SELECT COUNT(*) AS total FROM contact_messages");
$todaySales     = getSum($conn, "SELECT SUM(total) AS total FROM orders WHERE DATE(created_at) = '$tdate'");
$todayUsers     = getCount($conn, "SELECT COUNT(*) AS total FROM users WHERE DATE(created_at) = '$tdate'");
$newClients     = $todayUsers;
$activeUsers    = getCount($conn, "SELECT COUNT(DISTINCT session_id) AS total FROM cart");

$recentOrders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recentUsers = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    .sidebar .submenu {
      padding-left: 20px;
      background-color: #3d566e;
    }

    .main {
      margin-left: 220px;
      padding: 30px;
    }

    h1 {
      margin-bottom: 25px;
      color: #2c3e50;
    }

    .card-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .card {
      flex: 1 1 calc(25% - 20px);
      background: white;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      transition: transform 0.2s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card h2 {
      font-size: 2.2em;
      color: #2c3e50;
    }

    .card p {
      margin-top: 10px;
      font-size: 1em;
      color: #7f8c8d;
    }

    .chart-container, .table-container {
      margin-top: 40px;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
    }

    th { background: #f9f9f9; }

    @media (max-width: 768px) {
      .card { flex: 1 1 100%; }
      .main { margin-left: 0; padding: 15px; }
      .sidebar { position: relative; width: 100%; height: auto; top: 0; }
    }
  </style>
</head>
<body>

<div class="topnav">
  <div>📊 Admin Dashboard</div>
  <div>
    Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?> |
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="sidebar">
  <a href="dashboard.php">Dashboard</a>
  <a href="manage_products.php">Manage Products</a>
  <div class="submenu">
    <a href="admin_manage_peripherals.php">→ Manage Peripherals</a>
    <a href="admin_manage_laptops.php">→ Manage Laptops</a>
  </div>
  <a href="manage_orders.php">Manage Orders</a>
  <a href="manage_users.php">Manage Users</a>
  <a href="messages.php">Messages</a>
</div>

<div class="main">
  <h1>Overview</h1>
  <div class="card-container">
    <div class="card"><h2><?= $totalOrders ?></h2><p>Total Orders</p></div>
    <div class="card"><h2><?= $totalProducts ?></h2><p>Total Products</p></div>
    <div class="card"><h2><?= $totalUsers ?></h2><p>Total Users</p></div>
    <div class="card"><h2><?= $totalMessages ?></h2><p>Total Messages</p></div>
    <div class="card"><h2>Rs. <?= number_format($todaySales, 2) ?></h2><p>Today's Sales</p></div>
    <div class="card"><h2><?= $todayUsers ?></h2><p>Today's Users</p></div>
    <div class="card"><h2><?= $activeUsers ?></h2><p>Active Users</p></div>
    <div class="card"><h2><?= $newClients ?></h2><p>New Clients</p></div>
  </div>

  <div class="chart-container">
    <h3>📈 Weekly Sales Overview</h3>
    <canvas id="weeklyChart"></canvas>
  </div>

  <div class="chart-container">
    <h3>📊 Monthly Sales Trends</h3>
    <canvas id="monthlyChart"></canvas>
  </div>

  <div class="table-container">
    <h3>🧾 Recent Orders</h3>
    <table>
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Total</th><th>Date</th></tr>
      <?php while($row = $recentOrders->fetch_assoc()): ?>
        <tr>
          <td>#<?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td>Rs. <?= number_format($row['total'], 2) ?></td>
          <td><?= $row['created_at'] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <div class="table-container">
    <h3>👥 Recent Users</h3>
    <table>
      <tr><th>ID</th><th>Username</th><th>Email</th><th>Registered</th></tr>
      <?php while($row = $recentUsers->fetch_assoc()): ?>
        <tr>
          <td>#<?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= $row['created_at'] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>

<script>
const weeklyChart = document.getElementById('weeklyChart').getContext('2d');
new Chart(weeklyChart, {
  type: 'line',
  data: {
    labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
    datasets: [{
      label: 'Daily Sales (Rs.)',
      data: [180, 250, 220, 310, 400, 370, 290],
      backgroundColor: 'rgba(52,152,219,0.2)',
      borderColor: '#2980b9',
      borderWidth: 2,
      fill: true,
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    scales: { y: { beginAtZero: true } }
  }
});

const monthlyChart = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyChart, {
  type: 'bar',
  data: {
    labels: ['Jan','Feb','Mar','Apr','May','Jun'],
    datasets: [{
      label: 'Monthly Sales (Rs.)',
      data: [4000, 4500, 5200, 4800, 6000, 6500],
      backgroundColor: '#2ecc71',
      borderColor: '#27ae60',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: { y: { beginAtZero: true } }
  }
});
</script>

</body>
</html>


