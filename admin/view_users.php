<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}
include("../includes/db.php");

$users = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View All Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            display: flex;
            background: #f9fafb;
        }

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

        .main {
            flex: 1;
            padding: 40px;
        }

        .main h2 {
            color: #2563eb;
            margin-bottom: 30px;
            font-size: 28px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background-color: #2563eb;
            color: white;
        }

        tr:hover {
            background-color: #f0f8ff;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main {
                padding: 20px;
            }

            table {
                font-size: 14px;
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
    <h2>👥 All Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
        <?php while ($row = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['role']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

