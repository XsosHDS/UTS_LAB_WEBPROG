<?php
session_start();
include('db.php');
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['id'])) {
    header('Location: index.php');
    exit();
}
$success = "";
$error   = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = trim($_POST['new_username']);
    $new_email    = trim($_POST['new_email']);
    $new_password = trim($_POST['new_password']);
    if ($new_username == "" || $new_email == "") {
        $error = "Username and Email cannot be empty.";
    } 
    else {
        if ($stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?")) {
            $stmt->bind_param("ssi", $new_username, $new_email, $_SESSION['id']);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = "Username or Email already taken by another user.";
            } 
            else {
                if ($new_password != "") {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $new_username, $new_email, $hashed_password, $_SESSION['id']);
                } 
                else {
                    // If password is not provided, only update username and email
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $new_username, $new_email, $_SESSION['id']);
                }
                if ($stmt->execute()) {
                    $_SESSION['username'] = $new_username;
                    $_SESSION['email']    = $new_email;
                    $success = "Profile updated successfully!";
                } 
                else {
                    $error = "Failed to update profile. Please try again.";
                }
            }
            $stmt->close();
        } 
        else {
            $error = "Database error: " . $conn->error;
        }
    }
}

if ($stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?")) {
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($username, $email);
    $stmt->fetch();
    $stmt->close();
} 
else {
    $error = "Failed to fetch user data: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">    
    <style>
        body {
            background-color: #64FFDB;
            padding-top: 50px;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar {
            justify-content: center;
        }
        .navbar-nav {
            flex-direction: row;
        }
        .nav-item {
            padding-left: 15px;
            padding-right: 15px;
        }
        .navbar-custom {
        background-color: #64FFDB;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light navbar-custom">
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="assignment.php">Assignment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <h2>User Profile Management</h2>
    <hr>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <div id="viewProfile">
        <h4>View Profile</h4>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <button class="btn btn-info" onclick="showEditProfile()">Edit Profile</button>
    </div>
    <div id="editProfile" style="display: none;">
        <h4>Edit Profile</h4>
        <form method="POST" action="profile.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="new_username" required value="<?php echo htmlspecialchars($username); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="new_email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password <small>(Leave blank to keep current password)</small></label>
                <input type="password" class="form-control" id="password" name="new_password" placeholder="Enter new password">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">Cancel</button>
        </form>
    </div>
</div>
<script>
    function showEditProfile() {
        document.getElementById('viewProfile').style.display = 'none';
        document.getElementById('editProfile').style.display = 'block';
    }

    function cancelEdit() {
        document.getElementById('editProfile').style.display = 'none';
        document.getElementById('viewProfile').style.display = 'block';
    }
</script>

</body>
</html>
