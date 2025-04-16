<?php
session_start();
include("../includes/db.php");

// Check user session and role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id']; // ✅ Needed for SQL and return logic

// Handle book return
if (isset($_GET['return'])) {
    $loan_id = $_GET['return'];

    // Get book ID from the loan
    $loan = $conn->query("SELECT book_id FROM loans WHERE id = $loan_id AND user_id = $user_id AND return_date IS NULL")->fetch_assoc();

    if ($loan) {
        $book_id = $loan['book_id'];

        // 1. Mark as returned
        $conn->query("UPDATE loans SET return_date = CURDATE() WHERE id = $loan_id");

        // 2. Increase available quantity
        $conn->query("UPDATE books SET quantity = quantity + 1 WHERE id = $book_id");
    }

    header("Location: borrowed.php");
    exit();
}

// Fetch all loans for current user
$sql = "SELECT loans.id AS loan_id, books.title, books.author, books.isbn, books.genre,
               loans.borrow_date, loans.due_date, loans.return_date
        FROM loans
        JOIN books ON loans.book_id = books.id
        WHERE loans.user_id = $user_id
        ORDER BY loans.borrow_date DESC";

$loans = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Borrowed Books</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Outfit', sans-serif; display: flex; background: #f9fafb; }
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
        .nav-link {
            color: #d1d5db;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .nav-link:hover { background: #1f2937; }
        .main {
            flex: 1;
            padding: 40px;
        }
        .main h2 {
            font-size: 26px;
            margin-bottom: 25px;
            color: #2563eb;
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
        th { background-color: #2563eb; color: white; }
        tr:hover { background-color: #f0f8ff; }
        .return-btn {
            background-color: #e11d48;
            color: white;
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .badge-returned {
            background: #10b981;
            color: white;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>📘 My Library</h2>
    <a href="dashboard.php" class="nav-link">🏠 Dashboard</a>
    <a href="books.php" class="nav-link">📚 View Books</a>
    <a href="borrowed.php" class="nav-link">📥 My Borrowed</a>
    <a href="../logout.php" class="nav-link">🚪 Logout</a>
</div>

<div class="main">
    <h2>📥 My Borrowed Books</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Borrowed On</th>
            <th>Due Date</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $loans->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['author']) ?></td>
            <td><?= htmlspecialchars($row['genre']) ?></td>
            <td><?= $row['borrow_date'] ?></td>
            <td><?= $row['due_date'] ?></td>
            <td>
                <?php if ($row['return_date']): ?>
                    <span class="badge-returned">Returned</span>
                <?php else: ?>
                    <a href="borrowed.php?return=<?= $row['loan_id'] ?>" class="return-btn">Return</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
