<?php
require_once __DIR__ . '/config/functions.php';

if (!empty($_SESSION['user'])) {
    redirectByRole();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/elearning/index.php">E-Learning</a>
        <div>
            <a class="btn btn-outline-light me-2" href="/elearning/auth/login.php">Login</a>
            <a class="btn btn-light" href="/elearning/auth/register.php">Register</a>
        </div>
    </div>
</nav>
<div class="container" style="padding-top:100px;">
    <div class="p-5 bg-white rounded shadow-sm text-center">
        <h1 class="display-5 fw-bold">Platform Les Online Profesional</h1>
        <p class="lead">Belajar course, kerjakan mission, dan raih skor quiz terbaik Anda.</p>
        <a href="/elearning/auth/register.php" class="btn btn-primary btn-lg">Mulai Belajar</a>
    </div>
</div>
</body>
</html>
