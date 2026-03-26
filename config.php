<?php
// config.php - Database Configuration File
// Restaurant Table Reservation System

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'Kelvin@254!');
define('DB_NAME', 'resturant_system');

// Admin credentials (in production, use proper authentication)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // Change this to a secure password

// Application settings
define('APP_NAME', 'Zest Restaurant Reservation System');
define('TIMEZONE', 'UTC');

// Database connection function
function getDBConnection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>