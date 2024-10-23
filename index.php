<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #64FFDB;
        }
        .header-bg {
            background-color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container mx-auto p-4">
        <!-- Header -->
        <header class="flex header-bg items-center justify-between p-4 shadow-md rounded-lg">
            <div class="flex items-center space-x-4">
            </div>
            <div class="flex items-center space-x-10">
                <a href="login.php" class="text-lg font-semibold">Login</a>
                <a href="register.php" class="text-lg font-semibold">Register</a>
            </div>
        </header>
        <main class="mt-10 text-center">
            <h1 class="text-4xl font-bold mb-4">WEB PROGRAMMING</h1>
            <p class="text-lg">SELAMAT DATANG DI ASSIGNMENT SCHEDULING.</p>
            <div class="mt-6">
                <a href="login.php" class="bg-pink-500 text-white px-6 py-3 rounded-full mr-4">Login</a>
                <a href="register.php" class="bg-green-500 text-white px-6 py-3 rounded-full">Register</a>
            </div>
        </main>
    </div>
</body>
</html>
