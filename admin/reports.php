<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch data
$most_borrowed = $conn->query("SELECT books.title, COUNT(loans.book_id) AS borrow_count FROM loans JOIN books ON loans.book_id = books.id GROUP BY loans.book_id ORDER BY borrow_count DESC LIMIT 5");
$overdue_books = $conn->query("SELECT books.title, COUNT(loans.book_id) AS overdue_count FROM loans JOIN books ON loans.book_id = books.id WHERE loans.return_date IS NULL AND loans.due_date < CURDATE() GROUP BY loans.book_id");
$active_users = $conn->query("SELECT users.name, COUNT(loans.book_id) AS total_borrowed FROM loans JOIN users ON loans.user_id = users.id WHERE loans.return_date IS NULL GROUP BY loans.user_id ORDER BY total_borrowed DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Reports</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            padding: 30px;
        }
        h1 {
            text-align: center;
            color: #2563eb;
            margin-bottom: 40px;
        }
        .charts {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
        }
        .chart-box {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            max-width: 550px;
            width: 100%;
        }
        canvas {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h1>📊 Library Reports</h1>

<div class="charts">
    <div class="chart-box">
        <h3>📚 Most Borrowed Books</h3>
        <canvas id="borrowedChart"></canvas>
    </div>

    <div class="chart-box">
        <h3>⏳ Overdue Books</h3>
        <canvas id="overdueChart"></canvas>
    </div>

    <div class="chart-box">
        <h3>👥 Active Users</h3>
        <canvas id="userChart"></canvas>
    </div>
</div>

<script>
    // Most Borrowed Books
    const borrowedChart = new Chart(document.getElementById('borrowedChart'), {
        type: 'bar',
        data: {
            labels: [<?php while ($row = $most_borrowed->fetch_assoc()) echo '"' . $row['title'] . '",'; ?>],
            datasets: [{
                label: 'Times Borrowed',
                backgroundColor: '#2563eb',
                borderRadius: 6,
                data: [<?php $most_borrowed->data_seek(0); while ($row = $most_borrowed->fetch_assoc()) echo $row['borrow_count'] . ','; ?>]
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#333', titleColor: '#fff', bodyColor: '#fff' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#555' },
                    grid: { color: '#eee' }
                },
                x: {
                    ticks: { color: '#555' },
                    grid: { display: false }
                }
            }
        }
    });

    // Overdue Books
    const overdueChart = new Chart(document.getElementById('overdueChart'), {
        type: 'doughnut',
        data: {
            labels: [<?php while ($row = $overdue_books->fetch_assoc()) echo '"' . $row['title'] . '",'; ?>],
            datasets: [{
                label: 'Overdue Count',
                backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#a855f7'],
                borderWidth: 1,
                data: [<?php $overdue_books->data_seek(0); while ($row = $overdue_books->fetch_assoc()) echo $row['overdue_count'] . ','; ?>]
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#444', font: { size: 14 } }
                }
            }
        }
    });

    // Active Users
    const userChart = new Chart(document.getElementById('userChart'), {
        type: 'bar',
        data: {
            labels: [<?php while ($row = $active_users->fetch_assoc()) echo '"' . $row['name'] . '",'; ?>],
            datasets: [{
                label: 'Books Borrowed',
                backgroundColor: '#10b981',
                borderRadius: 6,
                data: [<?php $active_users->data_seek(0); while ($row = $active_users->fetch_assoc()) echo $row['total_borrowed'] . ','; ?>]
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#333', titleColor: '#fff', bodyColor: '#fff' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#555' },
                    grid: { color: '#eee' }
                },
                x: {
                    ticks: { color: '#555' },
                    grid: { display: false }
                }
            }
        }
    });
</script>

</body>
</html>


