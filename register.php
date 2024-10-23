<?php
include('db.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<p style='color:red; text-align:center;'>Username or Email already exists!</p>";
    } 
    else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            echo "<p style='color:green; text-align:center;'>Registration successful! You can now <a href='index.php'>login</a>.</p>";
        } 
        else {
            echo "<p style='color:red; text-align:center;'>Registration failed. Please try again.</p>";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REGISTER</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #64FFDB;
        }
        .login-container {
            margin-top: 45px;
        }
        .login-box {
            max-width: 400px;
            margin: auto;
            padding: 60px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <div class="text-center">
            <img src="images/logo.png" alt="Logo" class="logo">
        </div>
        <form method="POST" action="register.php">
            <h3 class="text-center">REGISTER</h3>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
            <p class="text-center mt-3">Sudah Memiliki Akun ? <a href="login.php">Login here</a></p>
        </form>
    </div>
</div>
</body>
</html>
