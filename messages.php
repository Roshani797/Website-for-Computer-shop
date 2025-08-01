<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../include/admin_auth.php';
include '../include/db.php';

$success = '';
$error = '';

// Handle deletion
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $success = "Message deleted successfully.";
    } else {
        $error = "Failed to delete message.";
    }
    $stmt->close();
}

// Search functionality
$search = trim($_GET['search'] ?? '');

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Count total messages
if ($search) {
    $like = "%$search%";
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM contact_messages WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?");
    $stmt->bind_param("sss", $like, $like, $like);
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM contact_messages");
}
$stmt->execute();
$result = $stmt->get_result();
$totalRow = $result->fetch_assoc();
$totalMessages = $totalRow['total'] ?? 0;
$totalPages = ceil($totalMessages / $limit);
$stmt->close();

// Fetch messages
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("sssii", $like, $like, $like, $limit, $offset);
} else {
    $stmt = $conn->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();

// For chart: messages per day (last 7 days)
$chartData = [];
$chartQuery = $conn->query("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM contact_messages 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
    GROUP BY DATE(created_at)
    ORDER BY date ASC
");
while ($row = $chartQuery->fetch_assoc()) {
    $chartData[$row['date']] = $row['count'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Contact Messages</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f0f0f0; }
        .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
        }
        .reply-btn { background-color: #3498db; color: white; }
        .delete-btn { background-color: #e74c3c; color: white; }
        .back-link { display: inline-block; margin-bottom: 10px; text-decoration: none; }
        .pagination { text-align: center; margin-top: 20px; }
        .pagination a {
            margin: 0 5px;
            padding: 6px 10px;
            text-decoration: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .pagination a[style*="font-weight:bold"] {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }
        form.search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        form.search-bar input[type="text"] {
            padding: 6px;
            width: 300px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        canvas { margin: 30px auto 10px auto; display: block; max-width: 700px; }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    <h2>📩 Contact Messages</h2>

    <?php if ($success): ?><div class="alert success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- Chart -->
    <canvas id="messageChart"></canvas>
    <script>
        const ctx = document.getElementById('messageChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($chartData)) ?>,
                datasets: [{
                    label: 'Messages per Day',
                    data: <?= json_encode(array_values($chartData)) ?>,
                    backgroundColor: '#3498db'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

    <!-- Search -->
    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (empty($messages)): ?>
        <p>No messages found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?= $msg['id'] ?></td>
                    <td><?= htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']) ?></td>
                    <td><?= htmlspecialchars($msg['email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                    <td><?= $msg['created_at'] ?></td>
                    <td>
                        <a href="reply.php?email=<?= urlencode($msg['email']) ?>" class="btn reply-btn">Reply</a>
                        <a href="?delete=<?= $msg['id'] ?>" onclick="return confirm('Delete this message?')" class="btn delete-btn">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" <?= $i == $page ? 'style="font-weight:bold;"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
