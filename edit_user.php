<?php
include '../include/admin_auth.php';
include '../include/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$username = $email = '';
$success = $error = '';

if ($id > 0) {
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);

        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $id);

        if ($stmt->execute()) {
            $success = "User updated successfully.";
        } else {
            $error = "Failed to update user.";
        }
        $stmt->close();
    }

    // Fetch user info
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($username, $email);
    $stmt->fetch();
    $stmt->close();
} else {
    $error = "Invalid user ID.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 20px;
        }
        .form-box {
            background: white;
            padding: 20px;
            max-width: 500px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        button {
            margin-top: 15px;
            padding: 8px 20px;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Edit User</h2>

        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <button type="submit">Update</button>
        </form>

        <a class="back-link" href="manage_users.php">← Back to User Management</a>
    </div>
</body>
</html>
