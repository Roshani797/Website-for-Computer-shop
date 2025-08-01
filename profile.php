<?php
session_start();
require 'db.php'; // Make sure db.php correctly connects to your database

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// --- Fetch User Info ---
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
if (!$stmt) {
    die("Prepare failed to fetch user info: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close(); // Close the statement after use

// --- Fetch all orders with user names and product details ---
// This query fetches data for the 'All Orders' section, showing ALL users' orders for an admin-like view.
// If you only want the *current user's* orders, you'd add WHERE orders.user_id = ? and bind $user_id.
$sql_orders = "
    SELECT users.username, products.name AS product_name, order_items.quantity, order_items.product_price AS price, orders.order_date, orders.created_at
    FROM orders
    JOIN users ON users.id = orders.user_id
    JOIN order_items ON orders.id = order_items.order_id
    JOIN products ON products.id = order_items.product_id
    ORDER BY orders.created_at DESC
";
$orderQuery = $conn->query($sql_orders);

if (!$orderQuery) {
    die("Query failed for orders: " . $conn->error);
}

$userOrders = [];
while ($row = $orderQuery->fetch_assoc()) {
    $username = $row['username'];
    if (!isset($userOrders[$username])) {
        $userOrders[$username] = [];
    }
    // Only add to userOrders if there's a product name (to avoid empty order entries for users without products)
    if (!empty($row['product_name'])) {
        $userOrders[$username][] = $row;
    }
}

// --- Handle Profile Update ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $language = $_POST['language'] ?? '';
    $notifications_enabled = isset($_POST['notifications']) ? 1 : 0;

    $profile_update_errors = [];

    // Basic server-side validation
    if (empty($first)) $profile_update_errors[] = "First Name is required.";
    if (empty($last)) $profile_update_errors[] = "Last Name is required.";
    if (empty($email)) $profile_update_errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $profile_update_errors[] = "Invalid email format.";

    if (empty($profile_update_errors)) {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, mobile = ?, dob = ?, language = ?, notifications_enabled = ? WHERE id = ?";
        $update = $conn->prepare($sql);
        if (!$update) {
            error_log("Profile update prepare failed: (" . $conn->errno . ") " . $conn->error);
            $_SESSION['profile_message'] = "An internal error occurred. Please try again later.";
        } else {
            $update->bind_param("ssssssii", $first, $last, $email, $mobile, $dob, $language, $notifications_enabled, $user_id);

            if ($update->execute()) {
                $_SESSION['profile_message'] = "Profile updated successfully!";
                $_SESSION['profile_message_type'] = "success";
                // Re-fetch user data to reflect changes immediately
                $stmt_re_fetch = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt_re_fetch->bind_param("i", $user_id);
                $stmt_re_fetch->execute();
                $user = $stmt_re_fetch->get_result()->fetch_assoc();
                $stmt_re_fetch->close();

            } else {
                error_log("Error updating profile for user $user_id: " . $update->error);
                $_SESSION['profile_message'] = "Error updating profile: " . $update->error; // For debugging, but ideally a generic message
                $_SESSION['profile_message_type'] = "danger";
            }
            $update->close();
        }
    } else {
        $_SESSION['profile_message'] = implode("<br>", $profile_update_errors);
        $_SESSION['profile_message_type'] = "danger";
    }
    header("Location: profile.php#update");
    exit();
}

// --- Handle Password Change ---
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    $password_errors = [];

    // Fetch current hashed password from DB
    $pass_stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    if (!$pass_stmt) {
        error_log("Password change prepare failed (fetch old pass): (" . $conn->errno . ") " . $conn->error);
        $password_errors[] = "An internal error occurred.";
    } else {
        $pass_stmt->bind_param("i", $user_id);
        $pass_stmt->execute();
        $result = $pass_stmt->get_result();
        $current_user_pass = $result->fetch_assoc();
        $pass_stmt->close();

        if ($current_user_pass && !password_verify($old_password, $current_user_pass['password'])) {
            $password_errors[] = "Current password does not match.";
        }
    }

    if (empty($new_password)) {
        $password_errors[] = "New password cannot be empty.";
    } elseif (strlen($new_password) < 8) { // Example: enforce minimum length
        $password_errors[] = "New password must be at least 8 characters long.";
    }
    // Add more password strength checks (e.g., requires uppercase, number, symbol)
    // if (!preg_match('/[A-Z]/', $new_password)) $password_errors[] = "Password needs an uppercase letter.";
    // if (!preg_match('/[0-9]/', $new_password)) $password_errors[] = "Password needs a number.";

    if ($new_password !== $confirm_new_password) {
        $password_errors[] = "New password and confirm password do not match.";
    }

    if (empty($password_errors)) {
        $new_hashed_pass = password_hash($new_password, PASSWORD_DEFAULT);
        $updatePass = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        if (!$updatePass) {
            error_log("Password change prepare failed (update new pass): (" . $conn->errno . ") " . $conn->error);
            $_SESSION['password_message'] = "An internal error occurred. Please try again later.";
            $_SESSION['password_message_type'] = "danger";
        } else {
            $updatePass->bind_param("si", $new_hashed_pass, $user_id);
            if ($updatePass->execute()) {
                $_SESSION['password_message'] = "Password changed successfully!";
                $_SESSION['password_message_type'] = "success";
            } else {
                error_log("Error updating password for user $user_id: " . $updatePass->error);
                $_SESSION['password_message'] = "Error changing password.";
                $_SESSION['password_message_type'] = "danger";
            }
            $updatePass->close();
        }
    } else {
        $_SESSION['password_message'] = implode("<br>", $password_errors);
        $_SESSION['password_message_type'] = "danger";
    }
    header("Location: profile.php#password");
    exit();
}


// --- Handle Add Card ---
if (isset($_POST['add_card'])) {
    $name = trim($_POST['cardholder_name']);
    $card = trim($_POST['card_number']);
    $expiry_month = trim($_POST['expiry_month']);
    $expiry_year = trim($_POST['expiry_year']);
    $last4 = substr($card, -4); // Only store last 4 digits

    // Basic validation for card details
    if (empty($name) || empty($card) || empty($expiry_month) || empty($expiry_year) || !is_numeric($card) || strlen($card) < 13 || strlen($card) > 19) {
        $_SESSION['payment_message'] = "Invalid card details provided.";
        $_SESSION['payment_message_type'] = "danger";
    } else {
        $insertCard = $conn->prepare("INSERT INTO payment_methods (user_id, method_type, cardholder_name, card_number_last4, expiry_month, expiry_year) VALUES (?, 'card', ?, ?, ?, ?)");
        if (!$insertCard) {
            error_log("Add card prepare failed: (" . $conn->errno . ") " . $conn->error);
            $_SESSION['payment_message'] = "An internal error occurred. Please try again.";
            $_SESSION['payment_message_type'] = "danger";
        } else {
            $insertCard->bind_param("issss", $user_id, $name, $last4, $expiry_month, $expiry_year);
            if ($insertCard->execute()) {
                $_SESSION['payment_message'] = "Card added successfully!";
                $_SESSION['payment_message_type'] = "success";
            } else {
                error_log("Error adding card for user $user_id: " . $insertCard->error);
                $_SESSION['payment_message'] = "Error adding card.";
                $_SESSION['payment_message_type'] = "danger";
            }
            $insertCard->close();
        }
    }
    header("Location: profile.php#payments");
    exit();
}

// --- Handle Add PayPal ---
if (isset($_POST['add_paypal'])) {
    $paypal_email = trim($_POST['paypal_email']);
    if (empty($paypal_email) || !filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['payment_message'] = "Invalid PayPal email provided.";
        $_SESSION['payment_message_type'] = "danger";
    } else {
        $insertPaypal = $conn->prepare("INSERT INTO payment_methods (user_id, method_type, paypal_email) VALUES (?, 'paypal', ?)");
        if (!$insertPaypal) {
            error_log("Add PayPal prepare failed: (" . $conn->errno . ") " . $conn->error);
            $_SESSION['payment_message'] = "An internal error occurred. Please try again.";
            $_SESSION['payment_message_type'] = "danger";
        } else {
            $insertPaypal->bind_param("is", $user_id, $paypal_email);
            if ($insertPaypal->execute()) {
                $_SESSION['payment_message'] = "PayPal added successfully!";
                $_SESSION['payment_message_type'] = "success";
            } else {
                error_log("Error adding PayPal for user $user_id: " . $insertPaypal->error);
                $_SESSION['payment_message'] = "Error adding PayPal.";
                $_SESSION['payment_message_type'] = "danger";
            }
            $insertPaypal->close();
        }
    }
    header("Location: profile.php#payments");
    exit();
}

// --- Handle Add UPI ---
if (isset($_POST['add_upi'])) {
    $upi_id = trim($_POST['upi_id']);
    if (empty($upi_id)) { // More robust validation might be needed for UPI format
        $_SESSION['payment_message'] = "UPI ID cannot be empty.";
        $_SESSION['payment_message_type'] = "danger";
    } else {
        $insertUPI = $conn->prepare("INSERT INTO payment_methods (user_id, method_type, upi_id) VALUES (?, 'upi', ?)");
        if (!$insertUPI) {
            error_log("Add UPI prepare failed: (" . $conn->errno . ") " . $conn->error);
            $_SESSION['payment_message'] = "An internal error occurred. Please try again.";
            $_SESSION['payment_message_type'] = "danger";
        } else {
            $insertUPI->bind_param("is", $user_id, $upi_id);
            if ($insertUPI->execute()) {
                $_SESSION['payment_message'] = "UPI added successfully!";
                $_SESSION['payment_message_type'] = "success";
            } else {
                error_log("Error adding UPI for user $user_id: " . $insertUPI->error);
                $_SESSION['payment_message'] = "Error adding UPI.";
                $_SESSION['payment_message_type'] = "danger";
            }
            $insertUPI->close();
        }
    }
    header("Location: profile.php#payments");
    exit();
}

// --- Handle Delete Payment Method ---
if (isset($_POST['delete_payment']) && isset($_POST['payment_id'])) {
    $payment_id = intval($_POST['payment_id']);
    $del = $conn->prepare("DELETE FROM payment_methods WHERE id = ? AND user_id = ?");
    if (!$del) {
        error_log("Delete payment prepare failed: (" . $conn->errno . ") " . $conn->error);
        $_SESSION['payment_message'] = "An internal error occurred. Please try again.";
        $_SESSION['payment_message_type'] = "danger";
    } else {
        $del->bind_param("ii", $payment_id, $user_id);
        if ($del->execute()) {
            $_SESSION['payment_message'] = "Payment method deleted successfully!";
            $_SESSION['payment_message_type'] = "success";
        } else {
            error_log("Error deleting payment method $payment_id for user $user_id: " . $del->error);
            $_SESSION['payment_message'] = "Error deleting payment method.";
            $_SESSION['payment_message_type'] = "danger";
        }
        $del->close();
    }
    header("Location: profile.php#payments");
    exit();
}

// --- Handle Language Update ---
if (isset($_POST['update_language'])) {
    $language = $_POST['language'] ?? '';
    $langStmt = $conn->prepare("UPDATE users SET language = ? WHERE id = ?");
    if (!$langStmt) {
        error_log("Language update prepare failed: (" . $conn->errno . ") " . $conn->error);
        $_SESSION['language_message'] = "An internal error occurred. Please try again.";
        $_SESSION['language_message_type'] = "danger";
    } else {
        $langStmt->bind_param("si", $language, $user_id);
        if ($langStmt->execute()) {
            $_SESSION['language_message'] = "Language updated successfully!";
            $_SESSION['language_message_type'] = "success";
            // Re-fetch user data to reflect changes immediately
            $stmt_re_fetch = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt_re_fetch->bind_param("i", $user_id);
            $stmt_re_fetch->execute();
            $user = $stmt_re_fetch->get_result()->fetch_assoc();
            $stmt_re_fetch->close();
        } else {
            error_log("Error updating language for user $user_id: " . $langStmt->error);
            $_SESSION['language_message'] = "Error updating language.";
            $_SESSION['language_message_type'] = "danger";
        }
        $langStmt->close();
    }
    header("Location: profile.php#languages");
    exit();
}

// --- Handle Notifications Toggle ---
if (isset($_POST['update_notifications'])) {
    $enabled = isset($_POST['notifications']) ? 1 : 0;
    $notiStmt = $conn->prepare("UPDATE users SET notifications_enabled = ? WHERE id = ?");
    if (!$notiStmt) {
        error_log("Notifications update prepare failed: (" . $conn->errno . ") " . $conn->error);
        $_SESSION['notifications_message'] = "An internal error occurred. Please try again.";
        $_SESSION['notifications_message_type'] = "danger";
    } else {
        $notiStmt->bind_param("ii", $enabled, $user_id);
        if ($notiStmt->execute()) {
            $_SESSION['notifications_message'] = "Notification preferences updated successfully!";
            $_SESSION['notifications_message_type'] = "success";
            // Re-fetch user data to reflect changes immediately
            $stmt_re_fetch = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt_re_fetch->bind_param("i", $user_id);
            $stmt_re_fetch->execute();
            $user = $stmt_re_fetch->get_result()->fetch_assoc();
            $stmt_re_fetch->close();
        } else {
            error_log("Error updating notifications for user $user_id: " . $notiStmt->error);
            $_SESSION['notifications_message'] = "Error updating notification preferences.";
            $_SESSION['notifications_message_type'] = "danger";
        }
        $notiStmt->close();
    }
    header("Location: profile.php#notifications");
    exit();
}

// --- Handle Switch Account ---
if (isset($_POST['switch_user']) && isset($_POST['switch_user_id'])) {
    $new_user_id = intval($_POST['switch_user_id']);
    // Optional: Add a check to ensure the $new_user_id actually exists in the database
    // before switching. This prevents switching to non-existent IDs.
    $check_user_stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $check_user_stmt->bind_param("i", $new_user_id);
    $check_user_stmt->execute();
    $check_user_stmt->store_result();

    if ($check_user_stmt->num_rows > 0) {
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['switch_message'] = "Account switched successfully!";
        $_SESSION['switch_message_type'] = "success";
    } else {
        $_SESSION['switch_message'] = "Invalid user ID for switching.";
        $_SESSION['switch_message_type'] = "danger";
    }
    $check_user_stmt->close();
    header("Location: profile.php"); // Redirect to self to reload with new user's data
    exit();
}

// --- Handle Add/Edit Address ---
if (isset($_POST['save_address'])) {
    $address_id = $_POST['address_id'] ?? null;
    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $country = trim($_POST['country']);
    $province = trim($_POST['province']);
    $district = trim($_POST['district']);
    $city = trim($_POST['city']);
    $street = trim($_POST['street']);

    $address_errors = [];
    if (empty($fname) || empty($lname) || empty($phone) || empty($country) || empty($province) || empty($district) || empty($city) || empty($street)) {
        $address_errors[] = "All address fields are required.";
    }
    // Add more specific validation for phone number, postal codes etc.

    if (empty($address_errors)) {
        if ($address_id) {
            $stmt = $conn->prepare("UPDATE addresses SET first_name=?, last_name=?, phone=?, country=?, province=?, district=?, city=?, street=? WHERE id=? AND user_id=?");
            if (!$stmt) {
                error_log("Update address prepare failed: (" . $conn->errno . ") " . $conn->error);
                $_SESSION['address_message'] = "An internal error occurred. Please try again.";
                $_SESSION['address_message_type'] = "danger";
            } else {
                $stmt->bind_param("ssssssssii", $fname, $lname, $phone, $country, $province, $district, $city, $street, $address_id, $user_id);
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO addresses (user_id, first_name, last_name, phone, country, province, district, city, street) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                error_log("Insert address prepare failed: (" . $conn->errno . ") " . $conn->error);
                $_SESSION['address_message'] = "An internal error occurred. Please try again.";
                $_SESSION['address_message_type'] = "danger";
            } else {
                $stmt->bind_param("issssssss", $user_id, $fname, $lname, $phone, $country, $province, $district, $city, $street);
            }
        }

        if (isset($stmt) && $stmt->execute()) {
            $_SESSION['address_message'] = "Address saved successfully!";
            $_SESSION['address_message_type'] = "success";
        } else if (isset($stmt)) { // If statement prepared but execution failed
            error_log("Error saving address for user $user_id: " . $stmt->error);
            $_SESSION['address_message'] = "Error saving address.";
            $_SESSION['address_message_type'] = "danger";
        }
        if (isset($stmt)) $stmt->close();
    } else {
        $_SESSION['address_message'] = implode("<br>", $address_errors);
        $_SESSION['address_message_type'] = "danger";
    }
    header("Location: profile.php#addresses");
    exit();
}

// --- Handle Delete Address ---
if (isset($_POST['delete_address']) && isset($_POST['address_id'])) {
    $address_id = intval($_POST['address_id']);
    $del = $conn->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
    if (!$del) {
        error_log("Delete address prepare failed: (" . $conn->errno . ") " . $conn->error);
        $_SESSION['address_message'] = "An internal error occurred. Please try again.";
        $_SESSION['address_message_type'] = "danger";
    } else {
        $del->bind_param("ii", $address_id, $user_id);
        if ($del->execute()) {
            $_SESSION['address_message'] = "Address deleted successfully!";
            $_SESSION['address_message_type'] = "success";
        } else {
            error_log("Error deleting address $address_id for user $user_id: " . $del->error);
            $_SESSION['address_message'] = "Error deleting address.";
            $_SESSION['address_message_type'] = "danger";
        }
        $del->close();
    }
    header("Location: profile.php#addresses");
    exit();
}

// --- Fetch Saved Payments ---
$payments = $conn->prepare("SELECT * FROM payment_methods WHERE user_id = ?");
if (!$payments) {
    die("Prepare failed to fetch payments: (" . $conn->errno . ") " . $conn->error);
}
$payments->bind_param("i", $user_id);
$payments->execute();
$paymentResults = $payments->get_result();

// --- Fetch All Users (for switching) ---
$allUsers = $conn->query("SELECT id, username FROM users");
if (!$allUsers) {
    die("Query failed to fetch all users: " . $conn->error);
}

// --- Fetch Addresses ---
$addrStmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
if (!$addrStmt) {
    die("Prepare failed to fetch addresses: (" . $conn->errno . ") " . $conn->error);
}
$addrStmt->bind_param("i", $user_id);
$addrStmt->execute();
$addressResults = $addrStmt->get_result();

// --- Fetch Messages ---
// Order by sent_at DESC for most recent first
$msgStmt = $conn->prepare("SELECT messages.*, u.username AS sender_name FROM messages JOIN users u ON messages.sender_id = u.id WHERE recipient_id = ? ORDER BY sent_at DESC");
if (!$msgStmt) {
    die("Prepare failed to fetch messages: (" . $conn->errno . ") " . $conn->error);
}
$msgStmt->bind_param("i", $user_id);
$msgStmt->execute();
$messages = $msgStmt->get_result();

// --- Fetch Reviews ---
// If you want reviews *by* the current user, change WHERE to reviewer_id = ?.
// Current code fetches all reviews regardless of who wrote them.
$reviewStmt = $conn->prepare("SELECT reviews.*, u.username AS reviewer_name FROM reviews JOIN users u ON reviews.reviewer_id = u.id ORDER BY reviewed_at DESC");
if (!$reviewStmt) {
    die("Prepare failed to fetch reviews: (" . $conn->errno . ") " . $conn->error);
}
$reviewStmt->execute();
$reviews = $reviewStmt->get_result();

// --- Clear session messages after displaying ---
$profile_message = $_SESSION['profile_message'] ?? null;
$profile_message_type = $_SESSION['profile_message_type'] ?? null;
unset($_SESSION['profile_message']);
unset($_SESSION['profile_message_type']);

$password_message = $_SESSION['password_message'] ?? null;
$password_message_type = $_SESSION['password_message_type'] ?? null;
unset($_SESSION['password_message']);
unset($_SESSION['password_message_type']);

$payment_message = $_SESSION['payment_message'] ?? null;
$payment_message_type = $_SESSION['payment_message_type'] ?? null;
unset($_SESSION['payment_message']);
unset($_SESSION['payment_message_type']);

$address_message = $_SESSION['address_message'] ?? null;
$address_message_type = $_SESSION['address_message_type'] ?? null;
unset($_SESSION['address_message']);
unset($_SESSION['address_message_type']);

$language_message = $_SESSION['language_message'] ?? null;
$language_message_type = $_SESSION['language_message_type'] ?? null;
unset($_SESSION['language_message']);
unset($_SESSION['language_message_type']);

$notifications_message = $_SESSION['notifications_message'] ?? null;
$notifications_message_type = $_SESSION['notifications_message_type'] ?? null;
unset($_SESSION['notifications_message']);
unset($_SESSION['notifications_message_type']);

$switch_message = $_SESSION['switch_message'] ?? null;
$switch_message_type = $_SESSION['switch_message_type'] ?? null;
unset($_SESSION['switch_message']);
unset($_SESSION['switch_message_type']);

?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile - <?= htmlspecialchars($user['username'] ?? 'User') ?></title>
    <style>
        /* Basic styling (as in your original code) */
        body { font-family: Arial, sans-serif; margin: 0; display: flex; background: var(--bg-color, #f4f4f4); }
        .sidebar {
            width: 220px; background: var(--sidebar-bg, #333); color: var(--sidebar-link, #fff); height: 100vh; padding-top: 20px;
            position: fixed; left: 0; transition: width 0.3s ease-in-out;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar a {
            padding: 12px 20px; display: block; color: var(--sidebar-link, #fff); text-decoration: none;
            cursor: pointer;
        }
        .sidebar a:hover { background: var(--sidebar-hover, #444); }
        .sidebar.hidden { width: 0; overflow: hidden; } /* For toggle sidebar */
        .main {
            margin-left: 220px; padding: 30px; width: 100%; transition: margin-left 0.3s ease-in-out;
            color: var(--text-color, #333);
        }
        .main.fullwidth { margin-left: 0; } /* For toggle sidebar */
        .topbar {
            background: var(--sidebar-bg, #222); padding: 10px 20px; color: white;
            display: flex; justify-content: space-between; align-items: center;
            border-radius: 5px; margin-bottom: 20px;
        }
        .section { display: none; background: var(--main-bg, white); padding: 20px; margin-top: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .section.active { display: block; }
        input[type=text], input[type=email], input[type=date], input[type=password], select, textarea {
            width: calc(100% - 22px); /* Account for padding */
            padding: 10px; margin: 8px 0; border: 1px solid var(--border-color, #ccc); border-radius: 4px;
            background: var(--input-bg, white); color: var(--text-color, #333);
            box-sizing: border-box; /* Include padding in width */
        }
        button {
            background: var(--primary, #28a745); color: white; padding: 10px 15px; border: none; border-radius: 4px;
            cursor: pointer; margin-top: 10px;
            transition: background-color 0.2s ease;
        }
        button:hover {
            opacity: 0.9;
        }
        button.danger { background: var(--danger, #dc3545); }
        button.danger:hover { background: #c82333; }

        .card {
            background: var(--card-bg, #f9f9f9); padding: 15px; margin: 15px 0; border: 1px solid var(--border-color-light, #ddd); border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .message.danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .message p { margin: 0; }

        /* Custom properties for themes (from your script) */
        :root {
            --bg-color: #f0f2f5;
            --sidebar-bg: #24292e;
            --sidebar-link: #ccc;
            --sidebar-hover: #2c313a;
            --text-color: #333;
            --main-bg: #ffffff;
            --primary: #007bff;
            --danger: #dc3545;
            --card-bg: #f9f9f9;
            --input-bg: white;
            --border-color: #ccc;
            --border-color-light: #ddd;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a onclick="showSection('details')">👤 Profile Details</a>
    <a onclick="showSection('update')">✏ Update Profile</a>
    <a onclick="showSection('password')">🔒 Change Password</a>
    <a onclick="showSection('payments')">💳 Payment Methods</a>
    <a onclick="showSection('languages')">🌐 Languages</a>
    <a onclick="showSection('notifications')">🔔 Notifications</a>
    <a onclick="showSection('switch')">🔁 Switch Account</a>
    <a onclick="showSection('addresses')">🏠 Addresses</a>
    <a onclick="showSection('messages')">📨 User Messages</a>
    <a onclick="showSection('reviews')">🌟 User Reviews</a>
    <a onclick="showSection('all-orders')">📦 All Orders</a>
    <a href="index.php">🚪 Home</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main">
  <div class="topbar">
    <h2>Welcome, <?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'User'; ?></h2>
    <a onclick="showSection('Theme')">🎨Theme</a>
    <button id="toggleSidebarBtn" style="background:#007bff; color:white; border:none; padding:8px 12px; border-radius:4px;">⚙</button>
</div>

    <div class="section active" id="details">
        <h3>👤 Profile Details</h3>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username'] ?? 'Not set') ?></p>
        <p><strong>First Name:</strong> <?= htmlspecialchars($user['first_name'] ?? 'Not set') ?></p>
        <p><strong>Last Name:</strong> <?= htmlspecialchars($user['last_name'] ?? 'Not set') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'Not set') ?></p>
        <p><strong>Mobile:</strong> <?= htmlspecialchars($user['mobile'] ?? 'Not set') ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($user['dob'] ?? 'Not set') ?></p>
        <p><strong>Language:</strong> <?= htmlspecialchars($user['language'] ?? 'Not set') ?></p>
        <p><strong>Notifications:</strong> <?= ($user['notifications_enabled'] ?? 0) ? 'Enabled' : 'Disabled' ?></p>
    </div>

    <div class="section" id="update">
        <h3>✏ Update Profile</h3>
        <?php if ($profile_message): ?>
            <div class="message <?= $profile_message_type ?>">
                <p><?= $profile_message ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="#update">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

            <label for="mobile">Mobile:</label>
            <input type="text" id="mobile" name="mobile" value="<?= htmlspecialchars($user['mobile'] ?? '') ?>">

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">

            <label for="language_profile">Language:</label>
            <select id="language_profile" name="language">
                <option value="English" <?= ($user['language'] ?? '') == 'English' ? 'selected' : '' ?>>English</option>
                <option value="Spanish" <?= ($user['language'] ?? '') == 'Spanish' ? 'selected' : '' ?>>Spanish</option>
                <option value="French" <?= ($user['language'] ?? '') == 'French' ? 'selected' : '' ?>>French</option>
            </select>

            <label>
                <input type="checkbox" name="notifications" <?= !empty($user['notifications_enabled']) ? 'checked' : '' ?>>
                Enable Notifications
            </label>
            <br>
            <button type="submit" name="update_profile">Update Profile</button>
        </form>
    </div>

    <div class="section" id="password">
        <h3>🔒 Change Password</h3>
        <?php if ($password_message): ?>
            <div class="message <?= $password_message_type ?>">
                <p><?= $password_message ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="#password">
            <label for="old_password">Current Password:</label>
            <input type="password" id="old_password" name="old_password" placeholder="Current Password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" placeholder="New Password" required>

            <label for="confirm_new_password">Confirm New Password:</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Confirm New Password" required>
            <button type="submit" name="change_password">Change Password</button>
        </form>
    </div>

    <div class="section" id="payments">
        <h3>💳 Payment Methods</h3>
        <?php if ($payment_message): ?>
            <div class="message <?= $payment_message_type ?>">
                <p><?= $payment_message ?></p>
            </div>
        <?php endif; ?>

        <h4>Add Card</h4>
        <form method="POST" action="#payments">
            <input type="text" name="cardholder_name" placeholder="Cardholder Name" required>
            <input type="text" name="card_number" placeholder="Card Number (e.g., 16 digits)" minlength="13" maxlength="19" required>
            <select name="expiry_month" required>
                <option value="">Month</option>
                <?php for ($m = 1; $m <= 12; $m++) echo "<option value='".str_pad($m, 2, "0", STR_PAD_LEFT)."'>".str_pad($m, 2, "0", STR_PAD_LEFT)."</option>"; ?>
            </select>
            <select name="expiry_year" required>
                <option value="">Year</option>
                <?php for ($y = date('Y'); $y <= date('Y')+10; $y++) echo "<option value='$y'>$y</option>"; ?>
            </select>
            <button type="submit" name="add_card">Add Card</button>
        </form>

        <h4>Add PayPal</h4>
        <form method="POST" action="#payments">
            <input type="email" name="paypal_email" placeholder="PayPal Email" required>
            <button type="submit" name="add_paypal">Add PayPal</button>
        </form>

        <h4>Add UPI</h4>
        <form method="POST" action="#payments">
            <input type="text" name="upi_id" placeholder="UPI ID (e.g. name@bank)" required>
            <button type="submit" name="add_upi">Add UPI</button>
        </form>

        <h4>💾 Saved Payment Methods</h4>
        <?php if ($paymentResults->num_rows > 0): ?>
            <?php while ($payment = $paymentResults->fetch_assoc()): ?>
                <div class="card">
                    <?php if ($payment['method_type'] === 'card'): ?>
                        <p><strong>Cardholder:</strong> <?= htmlspecialchars($payment['cardholder_name']) ?></p>
                        <p><strong>Card:</strong> **** **** **** <?= htmlspecialchars($payment['card_number_last4']) ?></p>
                        <p><strong>Expires:</strong> <?= htmlspecialchars($payment['expiry_month']) ?>/<?= htmlspecialchars($payment['expiry_year']) ?></p>
                    <?php elseif ($payment['method_type'] === 'paypal'): ?>
                        <p><strong>PayPal:</strong> <?= htmlspecialchars($payment['paypal_email']) ?></p>
                    <?php elseif ($payment['method_type'] === 'upi'): ?>
                        <p><strong>UPI ID:</strong> <?= htmlspecialchars($payment['upi_id']) ?></p>
                    <?php endif; ?>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this payment method?');" style="display:inline-block;">
                        <input type="hidden" name="payment_id" value="<?= htmlspecialchars($payment['id']) ?>">
                        <button type="submit" name="delete_payment" class="danger">🗑 Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
            <?php $paymentResults->data_seek(0); // Reset pointer for potential re-display if needed ?>
        <?php else: ?>
            <p>No payment methods saved.</p>
        <?php endif; ?>
    </div>

    <div class="section" id="languages">
        <h3>🌐 Language Preferences</h3>
        <?php if ($language_message): ?>
            <div class="message <?= $language_message_type ?>">
                <p><?= $language_message ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="#languages">
            <label for="language_pref">Choose Language:</label>
            <select id="language_pref" name="language">
                <option value="English" <?php echo (isset($user['language']) && $user['language'] == 'English') ? 'selected' : ''; ?>>English</option>
                <option value="Spanish" <?php echo (isset($user['language']) && $user['language'] == 'Spanish') ? 'selected' : ''; ?>>Spanish</option>
                <option value="French" <?php echo (isset($user['language']) && $user['language'] == 'French') ? 'selected' : ''; ?>>French</option>
            </select>
            <button type="submit" name="update_language">Save Language</button>
        </form>
    </div>

    <div class="section" id="notifications">
        <h3>🔔 Notification Settings</h3>
        <?php if ($notifications_message): ?>
            <div class="message <?= $notifications_message_type ?>">
                <p><?= $notifications_message ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="#notifications">
            <label>
                <input type="checkbox" name="notifications" <?= isset($user) && isset($user['notifications_enabled']) && $user['notifications_enabled'] ? 'checked' : '' ?>>
                Enable Email Notifications
            </label>
            <br>
            <button type="submit" name="update_notifications">Save Preferences</button>
        </form>
    </div>

    <div class="section" id="switch">
        <h3>🔁 Switch Account</h3>
        <?php if ($switch_message): ?>
            <div class="message <?= $switch_message_type ?>">
                <p><?= $switch_message ?></p>
            </div>
        <?php endif; ?>
        <form method="POST" action="#switch">
            <label for="switch_user_id">Select User:</label>
            <select id="switch_user_id" name="switch_user_id">
                <?php while ($other = $allUsers->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($other['id']) ?>" <?= $other['id'] == $user_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($other['username']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="switch_user">Switch User</button>
        </form>
    </div>

    <div class="section" id="addresses">
        <h3>🏠 Saved Addresses</h3>
        <?php if ($address_message): ?>
            <div class="message <?= $address_message_type ?>">
                <p><?= $address_message ?></p>
            </div>
        <?php endif; ?>

        <?php if ($addressResults->num_rows > 0): ?>
            <?php while ($addr = $addressResults->fetch_assoc()): ?>
                <div class="card">
                    <p><strong><?= htmlspecialchars($addr['first_name'] . ' ' . $addr['last_name']) ?></strong></p>
                    <p><?= htmlspecialchars($addr['street']) ?>, <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['district']) ?></p>
                    <p><?= htmlspecialchars($addr['province']) ?>, <?= htmlspecialchars($addr['country']) ?></p>
                    <p>📞 <?= htmlspecialchars($addr['phone']) ?></p>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');" style="display:inline-block; margin-right: 5px;">
                        <input type="hidden" name="address_id" value="<?= htmlspecialchars($addr['id']) ?>">
                        <button type="submit" name="delete_address" class="danger">🗑 Delete</button>
                    </form>
                    <button onclick='copyAddress(<?= json_encode($addr) ?>)'>📋 Copy</button>
                    <button onclick='editAddress(<?= json_encode($addr) ?>)'>✏ Edit</button>
                </div>
            <?php endwhile; ?>
            <?php $addressResults->data_seek(0); // Reset pointer for potential re-display if needed ?>
        <?php else: ?>
            <p>No addresses saved.</p>
        <?php endif; ?>

        <h3 id="addressFormTitle">➕ Add New Address</h3>
        <form method="POST" id="addressForm" action="#addresses">
            <input type="hidden" name="address_id" id="address_id">
            <label for="afirst">First Name:</label>
            <input type="text" name="first_name" id="afirst" placeholder="First Name" required>
            <label for="alast">Last Name:</label>
            <input type="text" name="last_name" id="alast" placeholder="Last Name" required>
            <label for="aphone">Phone Number:</label>
            <input type="text" name="phone" id="aphone" placeholder="Phone Number" required>
            <label for="acountry">Country:</label>
            <input type="text" name="country" id="acountry" placeholder="Country" required>
            <label for="aprovince">Province:</label>
            <input type="text" name="province" id="aprovince" placeholder="Province" required>
            <label for="adistrict">District:</label>
            <input type="text" name="district" id="adistrict" placeholder="District" required>
            <label for="acity">City:</label>
            <input type="text" name="city" id="acity" placeholder="City" required>
            <label for="astreet">Street Name and Number:</label>
            <input type="text" name="street" id="astreet" placeholder="Street Name and Number" required>
            <button type="submit" name="save_address">Save Address</button>
            <button type="button" onclick="resetAddressForm()" style="background-color: #6c757d;">Clear Form</button>
        </form>
    </div>

    <div class="section" id="messages">
        <h3>📨 Messages</h3>
        <h4>Filter by Category:</h4>
        <button onclick="filterMessages('all')">All</button>
        <button onclick="filterMessages('promotions')">Promotions</button>
        <button onclick="filterMessages('orders')">Orders</button>
        <button onclick="filterMessages('shipping')">Shipping</button>
        <div id="messageList">
            <?php if ($messages->num_rows > 0): ?>
                <?php while ($msg = $messages->fetch_assoc()): ?>
                    <div class="card message-card" data-category="<?= htmlspecialchars($msg['category']) ?>">
                        <p><strong>From:</strong> <?= htmlspecialchars($msg['sender_name']) ?></p>
                        <p><strong>Subject:</strong> <?= htmlspecialchars($msg['subject']) ?></p>
                        <p><strong>Category:</strong> <?= ucfirst(htmlspecialchars($msg['category'])) ?></p>
                        <p><?= nl2br(htmlspecialchars($msg['content'])) ?></p>
                        <p><em>Sent: <?= htmlspecialchars($msg['sent_at']) ?></em></p>
                    </div>
                <?php endwhile; ?>
                <?php $messages->data_seek(0); // Reset pointer ?>
            <?php else: ?>
                <p>No messages found for you.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="section" id="reviews">
        <h3>🌟 User Reviews</h3>
        <?php if ($reviews->num_rows > 0): ?>
            <?php while ($rev = $reviews->fetch_assoc()): ?>
                <div class="card">
                    <p><strong><?= htmlspecialchars($rev['reviewer_name']) ?>:</strong></p>
                    <p>Rating: <?= str_repeat('⭐', (int)$rev['rating']) ?> (<?= htmlspecialchars($rev['rating']) ?>/5)</p>
                    <p><?= nl2br(htmlspecialchars($rev['review_text'])) ?></p>
                    <p><em>Reviewed on: <?= htmlspecialchars($rev['reviewed_at']) ?></em></p>
                </div>
            <?php endwhile; ?>
            <?php $reviews->data_seek(0); // Reset pointer ?>
        <?php else: ?>
            <p>No reviews found.</p>
        <?php endif; ?>
    </div>

    <div class="section" id="all-orders">
        <h3>📦 User Orders</h3>
        <?php if (!empty($userOrders)): ?>
            <?php foreach ($userOrders as $username => $orders): ?>
                <div class="card">
                    <h4>👤 <?= htmlspecialchars($username) ?></h4>
                    <?php if (count($orders) > 0): ?>
                        <ul>
                            <?php foreach ($orders as $order): ?>
                                <li>
                                    <?= htmlspecialchars($order['product_name']) ?> (x<?= htmlspecialchars($order['quantity']) ?>)
                                    - Price: <?= htmlspecialchars($order['price']) ?> - <small><?= htmlspecialchars($order['created_at']) ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No orders found for this user.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No orders found in the system.</p>
        <?php endif; ?>
    </div>

    <div class="section" id="Theme">
        <h3>🎨 Theme Settings</h3>
        <div>
            <label for="themeSelect">Choose Theme:</label>
            <select id="themeSelect">
                <option value="default">Default</option>
                <option value="dark">Dark</option>
                <option value="pastel">Pastel</option>
            </select>
        </div>
    </div>

</div> <script>
// Function to show/hide sections
function showSection(id) {
    document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    // Update URL hash without reloading
    window.location.hash = id;
}

// Sidebar toggle functionality
document.getElementById('toggleSidebarBtn').addEventListener('click', function() {
    const sidebar = document.querySelector('.sidebar');
    const main = document.querySelector('.main');
    sidebar.classList.toggle('hidden');
    main.classList.toggle('fullwidth');
});

// Copy address to clipboard
function copyAddress(data) {
    const copiedText = `${data.first_name} ${data.last_name}\n${data.street}, ${data.city}, ${data.district}\n${data.province}, ${data.country}\nPhone: ${data.phone}`;
    navigator.clipboard.writeText(copiedText).then(() => {
        alert('Address copied to clipboard.');
    }).catch(err => {
        console.error('Failed to copy text: ', err);
        alert('Failed to copy address. Please try again or copy manually.');
    });
}

// Edit address form population
function editAddress(data) {
    document.getElementById('addressFormTitle').innerText = '✏ Edit Address';
    document.getElementById('address_id').value = data.id;
    document.getElementById('afirst').value = data.first_name;
    document.getElementById('alast').value = data.last_name;
    document.getElementById('aphone').value = data.phone;
    document.getElementById('acountry').value = data.country;
    document.getElementById('aprovince').value = data.province;
    document.getElementById('adistrict').value = data.district;
    document.getElementById('acity').value = data.city;
    document.getElementById('astreet').value = data.street;

    showSection('addresses'); // Make sure the address section is visible
    window.scrollTo(0, document.body.scrollHeight); // Scroll to the bottom to see the form
}

// Reset address form
function resetAddressForm() {
    document.getElementById('addressFormTitle').innerText = '➕ Add New Address';
    document.getElementById('address_id').value = '';
    document.getElementById('addressForm').reset(); // Resets all form fields
}


// Filter messages by category
function filterMessages(category) {
    const cards = document.querySelectorAll('.message-card');
    cards.forEach(card => {
        const cardCat = card.getAttribute('data-category');
        card.style.display = (category === 'all' || cardCat === category) ? 'block' : 'none';
    });
}

// Theme switching logic (with local storage)
document.getElementById('themeSelect').addEventListener('change', function() {
    const root = document.documentElement;
    const theme = this.value;

    if (theme === 'default') {
        root.style.setProperty('--bg-color', '#f0f2f5');
        root.style.setProperty('--sidebar-bg', '#24292e');
        root.style.setProperty('--sidebar-link', '#ccc');
        root.style.setProperty('--sidebar-hover', '#2c313a');
        root.style.setProperty('--text-color', '#333');
        root.style.setProperty('--main-bg', '#ffffff');
        root.style.setProperty('--primary', '#007bff');
        root.style.setProperty('--danger', '#dc3545');
        root.style.setProperty('--card-bg', '#f9f9f9');
        root.style.setProperty('--input-bg', 'white');
        root.style.setProperty('--border-color', '#ccc');
        root.style.setProperty('--border-color-light', '#ddd');
    } else if (theme === 'dark') {
        root.style.setProperty('--bg-color', '#181818');
        root.style.setProperty('--sidebar-bg', '#111');
        root.style.setProperty('--sidebar-link', '#aaa');
        root.style.setProperty('--sidebar-hover', '#333');
        root.style.setProperty('--text-color', '#eee');
        root.style.setProperty('--main-bg', '#222');
        root.style.setProperty('--primary', '#1e90ff');
        root.style.setProperty('--danger', '#ff4d4d');
        root.style.setProperty('--card-bg', '#333');
        root.style.setProperty('--input-bg', '#444');
        root.style.setProperty('--border-color', '#555');
        root.style.setProperty('--border-color-light', '#444');
    } else if (theme === 'pastel') {
        root.style.setProperty('--bg-color', '#ffe9ec');
        root.style.setProperty('--sidebar-bg', '#ffd6e0');
        root.style.setProperty('--sidebar-link', '#a55');
        root.style.setProperty('--sidebar-hover', '#ffc0cb');
        root.style.setProperty('--text-color', '#5a1f2f');
        root.style.setProperty('--main-bg', '#fff0f5');
        root.style.setProperty('--primary', '#ff85a2');
        root.style.setProperty('--danger', '#ff4d6d');
        root.style.setProperty('--card-bg', '#fff5f7');
        root.style.setProperty('--input-bg', '#ffe9f0');
        root.style.setProperty('--border-color', '#ffc2d1');
        root.style.setProperty('--border-color-light', '#ffe9f0');
    }
    localStorage.setItem("theme", this.value); // Save selected theme
});

// Apply saved theme on page load and handle URL hash
document.addEventListener("DOMContentLoaded", () => {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) {
        document.getElementById('themeSelect').value = savedTheme;
        // Trigger change event to apply styles immediately
        document.getElementById('themeSelect').dispatchEvent(new Event('change'));
    }

    // Show section based on URL hash
    const hash = window.location.hash.substring(1); // Remove the '#'
    if (hash && document.getElementById(hash)) {
        showSection(hash);
    } else {
        showSection('details'); // Default to details section
    }
});
</script>

</body>
</html>
    


















   

