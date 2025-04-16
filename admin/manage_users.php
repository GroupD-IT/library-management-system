<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}
include("../includes/db.php");

// Add new user
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    $stmt->execute();
}

// Delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
}

// Fetch all users
$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            display: flex;
            background: #f9fafb;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: #111827;
            color: white;
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .sidebar h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .nav-link {
            color: #d1d5db;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.3s;
        }

        .nav-link:hover {
            background: #1f2937;
        }

        /* Main content */
        .main {
            flex: 1;
            padding: 40px;
        }

        .header {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #111827;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 40px;
            max-width: 600px;
        }

        .form-card h3 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
        }

        .btn-submit {
            background: #2563eb;
            color: white;
            padding: 12px 30px;
            font-size: 15px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-submit:hover {
            background: #1d4ed8;
        }

        /* User cards */
        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .user-card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .user-card:hover {
            transform: translateY(-4px);
        }

        .user-card h4 {
            margin-bottom: 5px;
            font-size: 18px;
            color: #111827;
        }

        .user-card p {
            margin: 4px 0;
            color: #555;
        }

        .actions {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
            padding: 6px 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-edit {
            background: #facc15;
            color: #111827;
            padding: 6px 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .main {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>📚 Library</h2>
    <a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
    <a href="manage_books.php" class="nav-link"><i class="fas fa-book"></i> Manage Books</a>
    <a href="manage_users.php" class="nav-link"><i class="fas fa-users"></i> Manage Users</a>
    <a href="../logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
    <div class="header">👤 Manage Users</div>

    <div class="form-card">
        <h3>Add a New User</h3>
        <form method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>

            <input type="submit" name="add_user" value="Add User" class="btn-submit">
        </form>
    </div>

    <h3 style="margin-bottom: 20px;">👥 User List</h3>
    <div class="user-grid">
        <?php while ($row = $users->fetch_assoc()): ?>
            <div class="user-card">
                <h4><?= htmlspecialchars($row['name']) ?></h4>
                <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                <p><strong>Role:</strong> <?= htmlspecialchars($row['role']) ?></p>
                <div class="actions">
                    <a href="manage_users.php?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>


