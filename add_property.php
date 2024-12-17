<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'PropertyManager.php';

$auth = new Auth($pdo);
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $propertyManager = new PropertyManager($pdo);
    
    $data = [
        'title' => $_POST['title'],
        'address' => $_POST['address'],
        'price' => $_POST['price'],
        'description' => $_POST['description'],
        'latitude' => $_POST['latitude'],
        'longitude' => $_POST['longitude'],
        'status' => $_POST['status'],
        'property_type' => $_POST['property_type']
    ];

    try {
        if ($propertyManager->addProperty($_SESSION['user_id'], $data)) {
            header('Location: dashboard.php?success=1');
        } else {
            header('Location: dashboard.php?error=1');
        }
    } catch (Exception $e) {
        header('Location: dashboard.php?error=1');
    }
    exit;
}