<?php
// Authenticate admin and connect to DB
include '../include/admin_auth.php';
include '../include/db.php';


$success = '';
$error = '';

// Pagination settings
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');
$users = [];

// Count total users
if ($search !== '') {
    $like = "%$search%";
    $totalQuery = $conn->prepare("SELECT COUNT(*) FROM users WHERE username LIKE ? OR email LIKE ?");
    $totalQuery->bind_param("ss", $like, $like);
} else {
    $totalQuery = $conn->prepare("SELECT COUNT(*) FROM users");
}
$totalQuery->execute();
$totalQuery->bind_result($totalUsers);
$totalQuery->fetch();
$totalQuery->close();

$totalPages = ceil($totalUsers / $limit);

// Fetch paginated users
if ($search !== '') {
    $stmt = $conn->prepare("SELECT id, username, email, is_active, created_at FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $like, $like, $limit, $offset);
} else {
    $stmt = $conn->prepare("SELECT id, username, email, is_active, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();

// Delete user
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $success = "User deleted successfully.";
    } else {
        $error = "Failed to delete user.";
    }
    $stmt->close();
    header("Location: manage_users.php?success=" . urlencode($success));
    exit;
}

// Toggle user status
if (isset($_GET['toggle'])) {
    $userId = intval($_GET['toggle']);
    $stmt = $conn->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $success = "User status updated.";
    } else {
        $error = "Failed to update user status.";
    }
    $stmt->close();
    header("Location: manage_users.php?success=" . urlencode($success));
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Users</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #f0f0f0; }
        .success { color: green; }
        .error { color: red; }
        .action-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
        }
        .delete-btn { background-color: #e74c3c; color: white; }
        .toggle-btn { background-color: #3498db; color: white; }
        .edit-btn { background-color: #f1c40f; color: white; }
        .back-link { display: inline-block; margin-bottom: 10px; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    <h2>👥 User Management</h2>

    <?php if (isset($_GET['success'])): ?>
        <p class="success"><?= htmlspecialchars($_GET['success']) ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="GET" style="text-align: center;">
        <input type="text" name="search" placeholder="Search username or email..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) === 0): ?>
                <tr><td colspan="6">No users found.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['is_active'] ? '<span style="color:green;">Active</span>' : '<span style="color:red;">Deactivated</span>' ?></td>
                    <td><?= $user['created_at'] ?></td>
                    <td>
                        <a href="?toggle=<?= $user['id'] ?>" class="action-btn toggle-btn" onclick="return confirm('Change user status?')">
                            <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                        </a>
                        <a href="?delete=<?= $user['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Delete this user?')">Delete</a>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="action-btn edit-btn">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div style="text-align:center; margin-top: 20px;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" style="margin: 0 5px; <?= $i == $page ? 'font-weight: bold;' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>
