<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';

$totalStudents = 0;
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalStudents = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Student MS</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">

    <div class="alert alert-success">
        You are logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>.
        Welcome to the Student Management System.
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary text-center p-3">
                <h2><?= $totalStudents ?></h2>
                <p class="mb-0">Total Students</p>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="students/index.php" class="btn btn-outline-primary">View All Students</a>
        <a href="students/add.php"   class="btn btn-outline-success">Add New Student</a>
        <a href="logout.php"         class="btn btn-outline-danger">Logout</a>
    </div>

</div>
</body>

