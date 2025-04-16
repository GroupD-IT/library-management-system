<?php
session_start();
include("includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "SELECT * FROM users WHERE email='$email' AND role='$role'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($role === "Admin") {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid email or role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #f1f8ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
            max-width: 420px;
            width: 100%;
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #2563eb;
            font-weight: 600;
        }

        .login-box h2 i {
            margin-right: 8px;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #2563eb;
            font-size: 16px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px 12px 45px;
            border: 1px solid #ccc;
            border-radius: 12px;
            font-size: 15px;
            background: #fff;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #2563eb;
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #2563eb;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .login-btn:hover {
            background-color: #174fc0;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2><i class="fa-solid fa-book"></i>Library Login</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post" action="">
        <div class="form-group">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <div class="form-group">
            <i class="fa-solid fa-user"></i>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="User">User</option>
                <option value="Admin">Admin</option>
            </select>
        </div>

        <input type="submit" value="Login" class="login-btn">
    </form>
</div>

</body>
</html>


