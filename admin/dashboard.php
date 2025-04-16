<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}
include("../includes/db.php");

// Optional: Count summary stats
$total_books = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        .header {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #111827;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: start;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .card h3 {
            margin: 0;
            font-size: 20px;
            color: #2563eb;
        }

        .card p {
            margin: 10px 0 0;
            font-size: 16px;
            color: #555;
        }

        .card i {
            font-size: 28px;
            margin-bottom: 10px;
            color: #2563eb;
        }

        .card-link {
            text-decoration: none;
            color: inherit;
            display: block;
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
    <a href="reports.php" class="nav-link">📊 Reports</a>
</div>

<div class="main">
    <div class="header">📊 Admin Dashboard</div>

    <div class="cards">
    <a href="view_books.php" class="card-link">
        <div class="card">
            <i class="fas fa-book"></i>
            <h3>Total Books</h3>
            <p><?= $total_books ?> books in the system</p>
        </div>
    </a>

    <a href="view_users.php" class="card-link">
        <div class="card">
            <i class="fas fa-users"></i>
            <h3>Total Users</h3>
            <p><?= $total_users ?> registered users</p>
        </div>
    </a>

    <a href="manage_books.php" class="card-link">
        <div class="card">
            <i class="fas fa-plus-circle"></i>
            <h3>Add Book</h3>
            <p>Go to Manage Books</p>
        </div>
    </a>

    <a href="manage_users.php" class="card-link">
        <div class="card">
            <i class="fas fa-user-plus"></i>
            <h3>Add User</h3>
            <p>Go to Manage Users</p>
        </div>
    </a>
</div>
</body>
</html>
