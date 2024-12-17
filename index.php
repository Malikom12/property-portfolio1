<?php
require_once 'config.php';
require_once 'auth.php';

$auth = new Auth($pdo);

// If user is already logged in, redirect to dashboard
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Portfolio Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            padding: 100px 0;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://via.placeholder.com/1920x1080'); /* Replace with your own property image */
            background-size: cover;
            background-position: center;
            color: white;
        }
        .feature-card {
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Property Portfolio Manager</a>
            <div class="navbar-nav ms-auto">
                <a class="btn btn-outline-light me-2" href="login.php">Login</a>
                <a class="btn btn-primary" href="register.php">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 mb-4">Manage Your Property Portfolio</h1>
            <p class="lead mb-4">Track, analyze, and optimize your real estate investments in one place</p>
            <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container py-5">
        <h2 class="text-center mb-5">Key Features</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <h3 class="h5 mb-3">Property Tracking</h3>
                        <p class="card-text">Keep track of all your properties with detailed information and analytics.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <h3 class="h5 mb-3">Location Mapping</h3>
                        <p class="card-text">Visualize your properties on an interactive map using Google Maps integration.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card h-100">
                    <div class="card-body text-center">
                        <h3 class="h5 mb-3">Portfolio Analytics</h3>
                        <p class="card-text">Get insights into your portfolio performance with detailed analytics.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">© 2024 Property Portfolio Manager. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>