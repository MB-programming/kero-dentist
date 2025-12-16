<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly
ini_set('log_errors', 1);

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Validate required fields
    $required_fields = ['client_name', 'client_phone', 'client_whatsapp', 'booking_date', 'booking_time', 'package_id'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => 'الرجاء ملء جميع الحقول المطلوبة: ' . $field]);
            exit;
        }
    }

    // Sanitize inputs
    $client_name = sanitize($_POST['client_name']);
    $client_email = isset($_POST['client_email']) ? sanitize($_POST['client_email']) : '';
    $client_phone = sanitize($_POST['client_phone']);
    $client_whatsapp = sanitize($_POST['client_whatsapp']);
    $client_address = isset($_POST['client_address']) ? sanitize($_POST['client_address']) : '';
    $booking_date = sanitize($_POST['booking_date']);
    $booking_time = sanitize($_POST['booking_time']);
    $booking_day = isset($_POST['booking_day']) ? sanitize($_POST['booking_day']) : '';
    $package_id = intval($_POST['package_id']);

    // Validate date format
    $date_obj = DateTime::createFromFormat('Y-m-d', $booking_date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $booking_date) {
        echo json_encode(['success' => false, 'message' => 'تاريخ غير صالح']);
        exit;
    }

    // Check if date is in the past
    if ($date_obj < new DateTime('today')) {
        echo json_encode(['success' => false, 'message' => 'لا يمكن الحجز في تاريخ سابق']);
        exit;
    }

    // Calculate day if not provided
    if (empty($booking_day)) {
        $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        $booking_day = $days[$date_obj->format('w')];
    }

    // Verify package exists
    $stmt = $conn->prepare("SELECT id, name, price FROM packages WHERE id = ? AND is_active = 1");
    $stmt->execute([$package_id]);
    $package = $stmt->fetch();

    if (!$package) {
        echo json_encode(['success' => false, 'message' => 'الباقة المحددة غير متاحة']);
        exit;
    }

    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO bookings (
            client_name,
            client_email,
            client_address,
            client_phone,
            client_whatsapp,
            booking_date,
            booking_time,
            booking_day,
            package_id,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $success = $stmt->execute([
        $client_name,
        $client_email,
        $client_address,
        $client_phone,
        $client_whatsapp,
        $booking_date,
        $booking_time,
        $booking_day,
        $package_id
    ]);

    if (!$success) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('فشل حفظ الحجز: ' . implode(' - ', $errorInfo));
    }

    $booking_id = $conn->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال حجزك بنجاح! سنتواصل معك قريباً عبر واتساب.',
        'booking_id' => $booking_id
    ]);

} catch(PDOException $e) {
    error_log("Booking PDO error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ في قاعدة البيانات. يرجى المحاولة مرة أخرى.',
        'debug' => 'PDO Error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log("Booking error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء حفظ الحجز. يرجى المحاولة مرة أخرى.',
        'debug' => $e->getMessage()
    ]);
}
