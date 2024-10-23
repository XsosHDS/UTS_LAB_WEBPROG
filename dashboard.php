<?php
session_start();
include('db.php');
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['mark_complete'])) {
    $assignment_id = $_POST['assignment_id'];
    $new_status = $_POST['status'];
    $query = "UPDATE assignments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $new_status, $assignment_id);
    $stmt->execute();
}
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($filter == 'completed') {
    $query = "SELECT * FROM assignments WHERE status = 'completed'";
} elseif ($filter == 'unfinished') {
    $query = "SELECT * FROM assignments WHERE status = 'unfinished'";
} else {
    $query = "SELECT * FROM assignments";
}
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #64FFDB;
            padding-top: 50px;
        }
        .container {
            max-width: 800px;
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
            padding-left: 10px;
            padding-right: 10px;
        }
        .filter-buttons {
            margin-bottom: 20px;
        }
        .nav-link {
            color: black !important;
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
    <h2>Dashboard</h2>
    <p>Selamat datang di Assignment :</p>
    <div class="filter-buttons">
        <a href="dashboard.php?filter=all" class="btn btn-primary">All</a>
        <a href="dashboard.php?filter=completed" class="btn btn-success">Completed</a>
        <a href="dashboard.php?filter=unfinished" class="btn btn-warning">Unfinished</a>
    </div>

    <!-- Display assignment data -->
    <form method="post">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Name</th>
                    <th>Assignment</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Checkmark</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['assignment']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['deadline']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>";
                        echo "<form method='post' action='dashboard.php'>";
                        echo "<input type='hidden' name='assignment_id' value='" . $row['id'] . "'>";
                        if ($row['status'] == 'unfinished') {
                            echo "<input type='hidden' name='status' value='completed'>";
                            echo "<button type='submit' name='mark_complete' class='btn btn-sm btn-success'>Mark as Complete</button>";
                        } else {
                            echo "<input type='hidden' name='status' value='unfinished'>";
                            echo "<button type='submit' name='mark_complete' class='btn btn-sm btn-warning'>Undo Assignments</button>";
                        }
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No assignments found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
