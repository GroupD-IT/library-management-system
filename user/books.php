<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'User') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Count current active loans
$active_loans = $conn->query("SELECT COUNT(*) AS total FROM loans WHERE user_id=$user_id AND return_date IS NULL")->fetch_assoc()['total'];

// Handle borrow
if (isset($_GET['borrow'])) {
    $book_id = $_GET['borrow'];
    $borrow_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days'));

    if ($active_loans >= 3) {
        echo "<script>alert('⛔ You can only borrow up to 3 books at a time.'); window.location='books.php';</script>";
        exit();
    }

    // Check if already borrowed
    $check = $conn->query("SELECT * FROM loans WHERE user_id=$user_id AND book_id=$book_id AND return_date IS NULL");
    if ($check->num_rows == 0) {
        // Reduce quantity if available
        $book_q = $conn->query("SELECT quantity FROM books WHERE id=$book_id")->fetch_assoc();
        if ($book_q['quantity'] > 0) {
            $conn->query("INSERT INTO loans (user_id, book_id, borrow_date, due_date) VALUES ($user_id, $book_id, '$borrow_date', '$due_date')");
            $conn->query("UPDATE books SET quantity = quantity - 1 WHERE id = $book_id");
        }
    }
    header("Location: books.php");
    exit();
}

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM books WHERE 
    title LIKE '%$search%' 
    OR author LIKE '%$search%' 
    OR genre LIKE '%$search%'";
$books = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search & Borrow Books</title>
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
        form { margin-bottom: 20px; }
        input[type="text"] {
            padding: 10px 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            width: 300px;
        }
        button.search-btn {
            padding: 10px 18px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            margin-left: 10px;
            cursor: pointer;
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
        .borrow-btn {
            background-color: #16a34a;
            color: white;
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
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
    <h2>📚 Search & Borrow Books</h2>

    <form method="get">
        <input type="text" name="search" placeholder="Search title, author, genre..." value="<?= htmlspecialchars($search) ?>">
        <button class="search-btn">Search</button>
    </form>

    <p style='color: #2563eb; margin-bottom: 20px;'>📦 You can borrow <strong><?= (3 - $active_loans) ?></strong> more book(s).</p>

    <table>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Genre</th>
            <th>Quantity</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $books->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['author']) ?></td>
            <td><?= htmlspecialchars($row['isbn']) ?></td>
            <td><?= htmlspecialchars($row['genre']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>
                <?php if ($row['quantity'] > 0): ?>
                    <a href="books.php?borrow=<?= $row['id'] ?>" class="borrow-btn">Borrow</a>
                <?php else: ?>
                    <span style="color: gray;">Unavailable</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
