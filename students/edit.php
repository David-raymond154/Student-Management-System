<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/db.php';

$errors  = [];
$success = '';
$student = null;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM students WHERE id = $id");

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: index.php');
    exit;
}

$student = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = trim(htmlspecialchars($_POST['name']   ?? ''));
    $email  = trim(htmlspecialchars($_POST['email']  ?? ''));
    $course = trim(htmlspecialchars($_POST['course'] ?? ''));

    if (empty($name)) {
        $errors['name'] = 'Student name is required.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email address is not valid.';
    }

    if (empty($course)) {
        $errors['course'] = 'Course is required.';
    }

    if (empty($errors)) {
        $safeName   = mysqli_real_escape_string($conn, $name);
        $safeEmail  = mysqli_real_escape_string($conn, $email);
        $safeCourse = mysqli_real_escape_string($conn, $course);

        $sql = "UPDATE students
                SET name = '$safeName', email = '$safeEmail', course = '$safeCourse'
                WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            $success = 'Student record updated successfully!';
            $student = ['id' => $id, 'name' => $name, 'email' => $email, 'course' => $course];
        } else {
            $errors['general'] = 'Update failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Student — Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../dashboard.php">
            Student MS</a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-outline-light btn-sm">← Back to List</a>
        </div>
    </div>
</nav>

<div class="container mt-4" style="max-width: 600px;">
    <h3>Edit Student</h3>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= $success ?>
            <a href="index.php" class="alert-link">← Back to student list</a>
        </div>
    <?php endif; ?>

    <?php if (isset($errors['general'])): ?>
        <div class="alert alert-danger"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <div class="card p-4">
    <form method="POST" action="edit.php?id=<?= $id ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                   value="<?= htmlspecialchars($student['name']) ?>">
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?= $errors['name'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email"
                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                   value="<?= htmlspecialchars($student['email']) ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
            <input type="text" name="course" id="course"
                   class="form-control <?= isset($errors['course']) ? 'is-invalid' : '' ?>"
                   value="<?= htmlspecialchars($student['course']) ?>">
            <?php if (isset($errors['course'])): ?>
                <div class="invalid-feedback"><?= $errors['course'] ?></div>
            <?php endif; ?>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning w-100">Save Changes</button>
            <a href="index.php" class="btn btn-outline-secondary w-100">Cancel</a>
        </div>
    </form>
    </div>
</div>
</body>

