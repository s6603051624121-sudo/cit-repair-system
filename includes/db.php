<?php
$host = 'localhost';
$db   = 'cit_fixit';
$user = 'root';
$pass = ''; // Keep empty if using default Laragon, or 'root' if you changed it

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// FIX: Only start the session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>