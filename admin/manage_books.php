<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}
include("../includes/db.php");

// Add book
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $genre = $_POST['genre'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, genre, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $title, $author, $isbn, $genre, $quantity);
    $stmt->execute();
}

// Delete book
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM books WHERE id=$id");
}

$books = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books</title>
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

        /* Main Content */
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

        input {
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

        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .book-card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .book-card:hover {
            transform: translateY(-4px);
        }

        .book-card h4 {
            margin-bottom: 5px;
            font-size: 18px;
            color: #111827;
        }

        .book-card p {
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
    <div class="header">📚 Manage Books</div>

    <div class="form-card">
        <h3>Add a New Book</h3>
        <form method="post">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" required>
            </div>

            <div class="form-group">
                <label>ISBN</label>
                <input type="text" name="isbn" required>
            </div>

            <div class="form-group">
                <label>Genre</label>
                <input type="text" name="genre" required>
            </div>

            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" required min="1">
            </div>

            <input type="submit" name="add_book" value="Add Book" class="btn-submit">
        </form>
    </div>

    <h3 style="margin-bottom: 20px;">📖 Book List</h3>
    <div class="book-grid">
        <?php while ($row = $books->fetch_assoc()): ?>
            <div class="book-card">
                <h4><?= htmlspecialchars($row['title']) ?></h4>
                <p><strong>Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                <p><strong>ISBN:</strong> <?= htmlspecialchars($row['isbn']) ?></p>
                <p><strong>Genre:</strong> <?= htmlspecialchars($row['genre']) ?></p>
                <p><strong>Quantity:</strong> <?= $row['quantity'] ?></p>
                <div class="actions">
                    <a href="manage_books.php?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Delete this book?')">Delete</a>
                    <a href="edit_books.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>

