<?php
include '../include/db.php';

$order_id = $_GET['order_id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order #<?= $order_id ?> Items</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .box {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #bbb;
        }
        h2 { text-align: center; }
        table {
            width: 100%; border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc; padding: 10px;
            text-align: center;
        }
        th { background: #333; color: white; }
        a.back { display: inline-block; margin-top: 20px; text-decoration: none; color: #2980b9; }
    </style>
</head>
<body>
<div class="box">
    <h2>🧾 Order #<?= $order_id ?> - Items</h2>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Price (Rs.)</th>
                <th>Qty</th>
                <th>Subtotal (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $item['product_id'] ?></td>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= number_format($item['product_price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['product_price'] * $item['quantity'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a class="back" href="manage_orders.php">← Back to Orders</a>
</div>
</body>
</html>
