<?php
session_start();
include('db.php');
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit();
}
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $db_username, $email, $db_password);
                $stmt->fetch();
                if (password_verify($password, $db_password)) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['id']       = $id;
                    $_SESSION['username'] = $db_username;
                    $_SESSION['email']    = $email;
                    header('Location: dashboard.php');
                    exit();
                } 
                else {
                    $error = "Invalid username or password!";
                }
            } 
            else {
                $error = "Invalid username or password!";
            }
            $stmt->close();
        } 
        else {
            $error = "Something went wrong. Please try again later.";
        }
    } 
    else {
        $error = "Please enter both username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LOGIN</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #64FFDB;
        }
        .login-container {
            margin-top: 80px;
        }
        .login-box {
            max-width: 400px;
            margin: auto;
            padding: 40px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
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
            <div class="logo-container">
                <img src="images/logo.png" alt="Logo" class="logo">
            </div>
            <form method="POST" action="login.php">
                <h3 class="text-center text-2xl font-bold mb-6">Login</h3>
                <?php
                if ($error) {
                    echo '<div class="bg-red-100 text-red-800 p-3 rounded mb-4">' . htmlspecialchars($error) . '</div>';
                }
                ?>
                <div class="form-group mb-4">
                    <label for="username" class="block text-left mb-2">Username</label>
                    <input type="text" class="w-full px-4 py-2 border rounded" name="username" id="username" required
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                <div class="form-group mb-6">
                    <label for="password" class="block text-left mb-2">Password</label>
                    <input type="password" class="w-full px-4 py-2 border rounded" name="password" id="password" required>
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded">Login</button>
                <p class="text-center mt-4">Tidak Punya Akun ? <a href="register.php" class="text-blue-500">Register here</a></p>
            </form>
        </div>
    </div>
</body>
</html>
