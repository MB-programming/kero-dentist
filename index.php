<?php
/**
 * Main Entry Point
 * Redirects to appropriate location based on setup status
 */

// Check if database is configured
$config_file = __DIR__ . '/includes/config.php';
$setup_file = __DIR__ . '/setup.php';

// If setup file exists, check if DB is configured
if (file_exists($setup_file) && file_exists($config_file)) {
    include $config_file;

    // Try to connect to database
    try {
        if (!isset($conn) || $conn === null) {
            // Database not configured, redirect to setup
            header('Location: setup.php');
            exit();
        }

        // Test database connection
        $stmt = $conn->query("SELECT 1");

        // Database is configured, redirect to public site
        header('Location: public/index.php');
        exit();

    } catch(PDOException $e) {
        // Database connection failed, redirect to setup
        header('Location: setup.php');
        exit();
    }
} else {
    // Default redirect to public site
    header('Location: public/index.php');
    exit();
}
