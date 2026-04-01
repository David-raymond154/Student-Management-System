<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

$sql = "DELETE FROM students WHERE id = $id";

if (mysqli_query($conn, $sql)) {
    header('Location: index.php?deleted=1');
} else {
    header('Location: index.php?delete_error=1');
}
