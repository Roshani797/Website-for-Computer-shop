<?php
session_start();
include '../include/db.php';

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $conn->query("UPDATE orders SET status = '$new_status' WHERE id = $order_id");
}

// Handle order delete
if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];
    $conn->query("DELETE FROM order_items WHERE order_id = $order_id");
    $conn->query("DELETE FROM orders WHERE id = $order_id");
    header("Location: manage_orders.php");
    exit;
}

// Search functionality
$search = $_GET['search'] ?? '';
$filter_query = $search ? "WHERE full_name LIKE '%$search%' OR email LIKE '%$search%' OR status LIKE '%$search%'" : '';
$result = $conn->query("SELECT * FROM orders $filter_query ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders - Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background: #f4f4f4; }
        h2 { text-align: center; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 25px; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; font-size: 14px; }
        th { background: #eee; }
        .action-btn { padding: 5px 10px; font-size: 13px; cursor: pointer; }
        .view-btn { background: #3498db; color: white; border: none; border-radius: 4px; }
        .delete-btn { background: #e74c3c; color: white; border: none; border-radius: 4px; }
        .status-select { padding: 5px; }
        form.inline { display: inline-block; margin: 0; }
        .search-bar { margin-top: 15px; text-align: right; }
        .search-bar input[type="text"] {
            padding: 7px; width: 250px; border: 1px solid #ccc; border-radius: 5px;
        }
        .search-bar button {
            padding: 7px 12px; background: green; color: white; border: none; border-radius: 5px;
            cursor: pointer;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 15px;
            padding: 8px 16px;
            background: #555;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-btn:hover {
            background: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    <h2>📦 Manage Orders</h2>

    <div class="search-bar">
        <form method="get">
            <input type="text" name="search" placeholder="Search by name, email, or status" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Total (Rs.)</th>
            <th>Payment</th>
            <th>Shipping</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Items</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['phone'] ?></td>
                <td>Rs.<?= number_format($row['total'], 2) ?></td>
                <td><?= ucfirst($row['payment_method']) ?></td>
                <td><?= ucfirst($row['shipping_method']) ?></td>
                <td>
                    <form method="POST" class="inline">
                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                        <select name="status" class="status-select">
                            <?php
                            $statuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];
                            foreach ($statuses as $status) {
                                $selected = ($row['status'] ?? 'Pending') === $status ? 'selected' : '';
                                echo "<option value='$status' $selected>$status</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="update_status" class="action-btn">✔</button>
                    </form>
                </td>
                <td><?= $row['created_at'] ?? '-' ?></td>
                <td>
                    <a href="view_order_items.php?order_id=<?= $row['id'] ?>" class="action-btn view-btn">View</a>
                </td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this order?')" class="action-btn delete-btn">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

