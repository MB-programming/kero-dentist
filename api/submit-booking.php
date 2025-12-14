<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
$required_fields = ['client_name', 'client_phone', 'client_whatsapp', 'booking_date', 'package_id'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'الرجاء ملء جميع الحقول المطلوبة']);
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
$booking_day = isset($_POST['booking_day']) ? sanitize($_POST['booking_day']) : '';
$package_id = intval($_POST['package_id']);
$notes = isset($_POST['notes']) ? sanitize($_POST['notes']) : '';

// Validate date
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
try {
    $stmt = $conn->prepare("SELECT * FROM packages WHERE id = ? AND is_active = 1");
    $stmt->execute([$package_id]);
    $package = $stmt->fetch();

    if (!$package) {
        echo json_encode(['success' => false, 'message' => 'الباقة المحددة غير متاحة']);
        exit;
    }

    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO bookings (
            client_name, client_email, client_address, client_phone, client_whatsapp,
            booking_date, booking_day, package_id, admin_notes, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $stmt->execute([
        $client_name,
        $client_email,
        $client_address,
        $client_phone,
        $client_whatsapp,
        $booking_date,
        $booking_day,
        $package_id,
        $notes
    ]);

    $booking_id = $conn->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'تم إرسال حجزك بنجاح',
        'booking_id' => $booking_id
    ]);

} catch(PDOException $e) {
    error_log("Booking error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'حدث خطأ أثناء حفظ الحجز. يرجى المحاولة مرة أخرى.'
    ]);
}
