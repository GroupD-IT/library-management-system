<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_books.php");
    exit();
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM books WHERE id=$id");
$book = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $genre = $_POST['genre'];
    $quantity = $_POST['quantity'];

    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, isbn=?, genre=?, quantity=? WHERE id=?");
    $stmt->bind_param("ssssii", $title, $author, $isbn, $genre, $quantity, $id);
    $stmt->execute();

    header("Location: manage_books.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f9fafb; padding: 40px; }
        form { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); }
        h2 { text-align: center; color: #2563eb; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 10px; border: 1px solid #ccc; }
        button { background: #2563eb; color: white; border: none; padding: 12px 30px; border-radius: 10px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Edit Book</h2>
    <form method="post">
        <input type="text" name="title" value="<?= $book['title'] ?>" required>
        <input type="text" name="author" value="<?= $book['author'] ?>" required>
        <input type="text" name="isbn" value="<?= $book['isbn'] ?>" required>
        <input type="text" name="genre" value="<?= $book['genre'] ?>" required>
        <input type="number" name="quantity" value="<?= $book['quantity'] ?>" required>
        <button type="submit">Update Book</button>
    </form>
</body>
</html>
