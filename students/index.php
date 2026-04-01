<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/db.php';

$searchTerm  = '';
$searchQuery = '';

if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $searchTerm = strtolower(trim($_GET['search']));
    $safeTerm   = mysqli_real_escape_string($conn, $searchTerm);

    $searchQuery = "WHERE LOWER(name) LIKE '%$safeTerm%' OR LOWER(course) LIKE '%$safeTerm%'";
}

$sql    = "SELECT * FROM students $searchQuery ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$numRows = $result ? mysqli_num_rows($result) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Students — Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../dashboard.php">🎓 Student MS</a>
        <div class="ms-auto d-flex gap-2">
            <a href="add.php" class="btn btn-success btn-sm">Add Student</a>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3> Student Records</h3>
    <form method="GET" action="index.php" class="d-flex gap-2 my-3">
        <input type="text" name="search" class="form-control"
               placeholder="Search by name or course…"
               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($searchTerm): ?>
            <a href="index.php" class="btn btn-outline-secondary">Clear</a>
        <?php endif; ?>
    </form>

    <?php if ($searchTerm): ?>
        <p class="text-muted">
            Results for "<strong><?= htmlspecialchars($searchTerm) ?></strong>":
            <?= $numRows ?> student(s) found.
        </p>
    <?php else: ?>
        <p class="text-muted">Showing <strong><?= $numRows ?></strong> student(s).</p>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success"> Student deleted successfully.</div>
    <?php elseif (isset($_GET['delete_error'])): ?>
        <div class="alert alert-danger"> Failed to delete student. Please try again.</div>
    <?php endif; ?>

    <?php if ($numRows === 0): ?>
        <div class="alert alert-info">
            <?= $searchTerm
                ? 'No students match your search.'
                : 'No students found. <a href="add.php">Add the first one!</a>' ?>
        </div>

    <?php else: ?>
        <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name'])   ?></td>
                    <td><?= htmlspecialchars($row['email'])  ?></td>
                    <td><?= htmlspecialchars($row['course']) ?></td>
                    <td class="d-flex gap-1">
                        <a href="edit.php?id=<?= (int)$row['id'] ?>"
                           class="btn btn-warning btn-sm"> Edit</a>

                        <a href="delete.php?id=<?= (int)$row['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this student?');">
                            Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>

</div>
</body>

