<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (!$conn) {
    die("<p style='color:red;'>Connection failed: " . mysqli_connect_error() . "</p>");
}

$createDB = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (!mysqli_query($conn, $createDB)) {
    die("<p style='color:red;'>Could not create database: " . mysqli_error($conn) . "</p>");
}

if (!mysqli_select_db($conn, DB_NAME)) {
    die("<p style='color:red;'>Could not select database: " . mysqli_error($conn) . "</p>");
}

$createStudents = "CREATE TABLE IF NOT EXISTS students (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(100) NOT NULL,
    email  VARCHAR(150) NOT NULL,
    course VARCHAR(100) NOT NULL
)";
if (!mysqli_query($conn, $createStudents)) {
    die("<p style='color:red;'>Could not create students table: " . mysqli_error($conn) . "</p>");
}

$createUsers = "CREATE TABLE IF NOT EXISTS users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
if (!mysqli_query($conn, $createUsers)) {
    die("<p style='color:red;'>Could not create users table: " . mysqli_error($conn) . "</p>");
}

$check = mysqli_query($conn, "SELECT id FROM users WHERE username = 'admin'");
if (mysqli_num_rows($check) === 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $safe = mysqli_real_escape_string($conn, $hash);
    mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('admin', '$safe')");
}