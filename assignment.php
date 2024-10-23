<?php
session_start();
include('db.php');
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit();
}
$editAssignment = [];

if (isset($_GET['edit'])) {
    $assignment_id = $_GET['edit'];
    $query = "SELECT * FROM assignments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $editAssignment = $result->fetch_assoc();
    } else {
        $errorMessage = "Assignment not found.";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'];
    $name = $_POST['name'];
    $assignment = $_POST['assignment'];
    $deadline = $_POST['deadline'];
    
    if (isset($_POST['assignment_id'])) {
        $assignment_id = $_POST['assignment_id'];
        $query = "UPDATE assignments SET nim = ?, name = ?, assignment = ?, deadline = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $nim, $name, $assignment, $deadline, $assignment_id);
    } else {
        $query = "INSERT INTO assignments (nim, name, assignment, deadline) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $nim, $name, $assignment, $deadline);
    }
    if ($stmt->execute()) {
        $successMessage = isset($_POST['assignment_id']) ? "Assignment updated successfully!" : "Assignment added successfully!";
    } else {
        $errorMessage = "Failed to save assignment. Please try again.";
    }
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $assignment_id = $_GET['delete'];
    $query = "DELETE FROM assignments WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $assignment_id);
    if ($stmt->execute()) {
        $successMessage = "Assignment deleted successfully!";
    } else {
        $errorMessage = "Failed to delete assignment. Please try again.";
    }
    $stmt->close();
}
$query = "SELECT * FROM assignments";
$result = $conn->query($query);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #64FFDB;
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
    <h2>Assignment Form</h2>
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php elseif (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>
    <form action="assignment.php" method="POST">
        <div class="form-group">
            <label for="nim">NIM</label>
            <input type="text" class="form-control" id="nim" name="nim" placeholder="Enter NIM" value="<?= isset($_GET['edit']) ? htmlspecialchars($editAssignment['nim']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="<?= isset($_GET['edit']) ? htmlspecialchars($editAssignment['name']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="assignment">Assignment</label>
            <input type="text" class="form-control" id="assignment" name="assignment" placeholder="Enter Assignment" value="<?= isset($_GET['edit']) ? htmlspecialchars($editAssignment['assignment']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="deadline">Deadline</label>
            <input type="date" class="form-control" id="deadline" name="deadline" value="<?= isset($_GET['edit']) ? htmlspecialchars($editAssignment['deadline']) : '' ?>" required>
        </div>
        <?php if (isset($_GET['edit'])): ?>
            <input type="hidden" name="assignment_id" value="<?= $_GET['edit'] ?>">
        <?php endif; ?>
        <button type="submit" class="btn btn-primary"><?= isset($_GET['edit']) ? 'Update Assignment' : 'Submit' ?></button>
    </form>
    <h3 class="mt-4">Assignments</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>NIM</th>
                <th>Name</th>
                <th>Assignment</th>
                <th>Deadline</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nim']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['assignment']) ?></td>
                        <td><?= htmlspecialchars($row['deadline']) ?></td>
                        <td>
                            <a href="assignment.php?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="assignment.php?delete=<?= $row['id'] ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No assignments found.</td>
                </tr>
            <?php endif; ?>
            <?php
                include('db.php');
                    if (isset($_GET['delete'])) {
                         $assignment_id = $_GET['delete'];
                         $query = "DELETE FROM assignments WHERE id = ?";
                         $stmt = $conn->prepare($query);
                         $stmt->bind_param("i", $assignment_id);
                    if ($stmt->execute()) {
                        header("Location: assignment.php?message=deleted");
                    } 
                    else {
                        header("Location: assignment.php?message=error");
                    }
                $stmt->close();
                $conn->close();
                }
            ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>