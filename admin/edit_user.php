<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id=$id");
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    $stmt->execute();

    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f9fafb; padding: 40px; }
        form { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); }
        h2 { text-align: center; color: #2563eb; }
        input, select { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 10px; border: 1px solid #ccc; }
        button { background: #2563eb; color: white; border: none; padding: 12px 30px; border-radius: 10px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Edit User</h2>
    <form method="post">
        <input type="text" name="name" value="<?= $user['name'] ?>" required>
        <input type="email" name="email" value="<?= $user['email'] ?>" required>
        <select name="role" required>
            <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
            <option value="User" <?= $user['role'] === 'User' ? 'selected' : '' ?>>User</option>
        </select>
        <button type="submit">Update User</button>
    </form>
</body>
</html>
