<?php
/**
 * Booking Database Connection
 * This is a separate database connection specifically for booking form submissions
 * Isolated from the main website database for better security and performance
 */

// Database configuration for bookings
define('BOOKING_DB_HOST', 'localhost');
define('BOOKING_DB_NAME', 'u186120816_kero_dentist');
define('BOOKING_DB_USER', 'u186120816_kero_dentist');
define('BOOKING_DB_PASS', '9Ea$eZnZ#');
define('BOOKING_DB_CHARSET', 'utf8mb4');

// Sanitization function
function sanitize_booking_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_booking_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Phone validation function - Detects Egyptian mobile carriers
function detect_phone_carrier($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Egyptian mobile number patterns
    $carriers = [
        'Vodafone' => ['010', '011'],
        'Etisalat' => ['011'],
        'Orange' => ['012'],
        'WE' => ['015']
    ];

    // Get first 3 digits
    $prefix = substr($phone, 0, 3);

    foreach ($carriers as $carrier => $prefixes) {
        if (in_array($prefix, $prefixes)) {
            return [
                'carrier' => $carrier,
                'valid' => true,
                'formatted' => format_egyptian_phone($phone)
            ];
        }
    }

    // Check if starts with +20
    if (substr($phone, 0, 2) === '20') {
        $prefix = substr($phone, 2, 3);
        foreach ($carriers as $carrier => $prefixes) {
            if (in_array($prefix, $prefixes)) {
                return [
                    'carrier' => $carrier,
                    'valid' => true,
                    'formatted' => format_egyptian_phone($phone)
                ];
            }
        }
    }

    return [
        'carrier' => 'غير معروف',
        'valid' => false,
        'formatted' => $phone
    ];
}

// Format Egyptian phone number
function format_egyptian_phone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // If starts with +20 or 20, remove it
    if (substr($phone, 0, 2) === '20') {
        $phone = substr($phone, 2);
    }

    // Format as: 0XX XXXX XXXX
    if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
        return substr($phone, 0, 4) . ' ' . substr($phone, 4, 4) . ' ' . substr($phone, 8);
    } elseif (strlen($phone) === 10) {
        return '0' . substr($phone, 0, 2) . ' ' . substr($phone, 2, 4) . ' ' . substr($phone, 6);
    }

    return $phone;
}

// Validate Egyptian mobile number
function validate_egyptian_mobile($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Remove +20 or 20 prefix if exists
    if (substr($phone, 0, 2) === '20') {
        $phone = substr($phone, 2);
    }

    // Should be 11 digits starting with 01
    if (strlen($phone) === 11 && substr($phone, 0, 2) === '01') {
        return true;
    }

    // Or 10 digits starting with 1
    if (strlen($phone) === 10 && substr($phone, 0, 1) === '1') {
        return true;
    }

    return false;
}

try {
    // Create PDO connection for bookings database
    $dsn = "mysql:host=" . BOOKING_DB_HOST . ";dbname=" . BOOKING_DB_NAME . ";charset=" . BOOKING_DB_CHARSET;
    $booking_conn = new PDO($dsn, BOOKING_DB_USER, BOOKING_DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    // Create bookings table
    $booking_conn->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_name VARCHAR(255) NOT NULL,
            client_email VARCHAR(255),
            client_address TEXT,
            client_phone VARCHAR(50) NOT NULL,
            client_phone_carrier VARCHAR(50),
            client_whatsapp VARCHAR(50) NOT NULL,
            client_whatsapp_carrier VARCHAR(50),
            booking_date DATE NOT NULL,
            booking_time VARCHAR(20) NOT NULL,
            booking_day VARCHAR(50),
            package_id INT,
            package_name VARCHAR(255),
            package_price DECIMAL(10, 2),
            status VARCHAR(50) DEFAULT 'pending',
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_date (booking_date),
            INDEX idx_created (created_at),
            INDEX idx_phone (client_phone),
            INDEX idx_whatsapp (client_whatsapp)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Create packages table (copy from main database initially)
    $booking_conn->exec("
        CREATE TABLE IF NOT EXISTS packages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            duration VARCHAR(100),
            features TEXT,
            icon VARCHAR(100),
            is_active TINYINT(1) DEFAULT 1,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

} catch(PDOException $e) {
    error_log("Booking Database Connection Error: " . $e->getMessage());
    die(json_encode([
        'success' => false,
        'message' => 'خطأ في الاتصال بقاعدة البيانات. يرجى المحاولة مرة أخرى لاحقاً.',
        'error' => 'Database connection failed'
    ]));
}
