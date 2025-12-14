<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
$required_fields = ['name', 'email', 'phone', 'subject', 'message'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'الرجاء ملء جميع الحقول المطلوبة']);
        exit;
    }
}

// Sanitize inputs
$name = sanitize($_POST['name']);
$email = sanitize($_POST['email']);
$phone = sanitize($_POST['phone']);
$subject = sanitize($_POST['subject']);
$message = sanitize($_POST['message']);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير صالح']);
    exit;
}

try {
    // Create contacts table if not exists
    $conn->exec("CREATE TABLE IF NOT EXISTS `contact_messages` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `name` VARCHAR(200) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `subject` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `is_read` BOOLEAN DEFAULT FALSE,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_is_read` (`is_read`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Insert contact message
    $stmt = $conn->prepare("
        INSERT INTO contact_messages (name, email, phone, subject, message)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([$name, $email, $phone, $subject, $message]);

    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً!'
    ]);

} catch(PDOException $e) {
    error_log("Contact message error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.'
    ]);
}
