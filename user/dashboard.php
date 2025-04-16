<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            display: flex;
            background: #f9fafb;
        }

        .sidebar {
            width: 220px;
            background: #111827;
            color: white;
            min-height: 100vh;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 20px;
        }

        .nav-link {
            color: #d1d5db;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .nav-link:hover {
            background: #1f2937;
        }

        .main {
            flex: 1;
            padding: 40px;
        }

        .main h1 {
            font-size: 26px;
            color: #2563eb;
        }

        .main p {
            margin-top: 10px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>📘 My Library</h2>
    <a href="dashboard.php" class="nav-link">🏠 Dashboard</a>
    <a href="books.php" class="nav-link">📚 View Books</a>
    <a href="../logout.php" class="nav-link">🚪 Logout</a>
</div>

<div class="main">
    <h1>Welcome, User 👋</h1>
    <p>This is your library dashboard. You can view available books, search, and track your borrowing.</p>
</div>

</body>
</html>

