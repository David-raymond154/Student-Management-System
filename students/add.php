<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/db.php';

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = trim(htmlspecialchars($_POST['name']   ?? ''));
    $email  = trim(htmlspecialchars($_POST['email']  ?? ''));
    $course = trim(htmlspecialchars($_POST['course'] ?? ''));

    if (empty($name)) {
        $errors['name'] = 'Student name is required.';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Name must be 100 characters or fewer.';
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
        $safeEmail = mysqli_real_escape_string($conn, $email);
        $checkDup  = mysqli_query($conn, "SELECT id FROM students WHERE email = '$safeEmail'");

        if ($checkDup && mysqli_num_rows($checkDup) > 0) {
            $errors['email'] = 'A student with this email address already exists.';
        } else {
            $safeName   = mysqli_real_escape_string($conn, $name);
            $safeCourse = mysqli_real_escape_string($conn, $course);

            $sql = "INSERT INTO students (name, email, course)
                    VALUES ('$safeName', '$safeEmail', '$safeCourse')";

            if (mysqli_query($conn, $sql)) {
                $success = 'Student added successfully!';
            } else {
                $errors['general'] = 'Failed to add student. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Student - Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../dashboard.php"> Student MS</a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-outline-light btn-sm">← Back to List</a>
        </div>
    </div>
</nav>

<div class="container mt-4" style="max-width: 600px;">
    <h3>Add New Student</h3>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= $success ?>
            <a href="index.php" class="alert-link">View all students</a> or add another below.
        </div>
    <?php endif; ?>

    <?php if (isset($errors['general'])): ?>
        <div class="alert alert-danger"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <div class="card p-4">
    <form method="POST" action="add.php">

        <div class="mb-3">
            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name"
                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                   placeholder="e.g. Jane Wanjiku">
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?= $errors['name'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email"
                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="e.g. jane@example.com">
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
            <input type="text" name="course" id="course"
                   class="form-control <?= isset($errors['course']) ? 'is-invalid' : '' ?>"
                   value="<?= htmlspecialchars($_POST['course'] ?? '') ?>"
                   placeholder="e.g. Computer Security">
            <?php if (isset($errors['course'])): ?>
                <div class="invalid-feedback"><?= $errors['course'] ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-success w-100">Add Student</button>
    </form>
    </div>
</div>
</body>

