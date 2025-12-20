<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Phone validation function - Detects Egyptian mobile carriers
function detect_phone_carrier($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);

    $carriers = [
        'Vodafone' => ['010', '011'],
        'Etisalat' => ['011'],
        'Orange' => ['012'],
        'WE' => ['015']
    ];

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

function format_egyptian_phone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);

    if (substr($phone, 0, 2) === '20') {
        $phone = substr($phone, 2);
    }

    if (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
        return substr($phone, 0, 4) . ' ' . substr($phone, 4, 4) . ' ' . substr($phone, 8);
    } elseif (strlen($phone) === 10) {
        return '0' . substr($phone, 0, 2) . ' ' . substr($phone, 2, 4) . ' ' . substr($phone, 6);
    }

    return $phone;
}

function validate_egyptian_mobile($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);

    if (substr($phone, 0, 2) === '20') {
        $phone = substr($phone, 2);
    }

    if (strlen($phone) === 11 && substr($phone, 0, 2) === '01') {
        return true;
    }

    if (strlen($phone) === 10 && substr($phone, 0, 1) === '1') {
        return true;
    }

    return false;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Create bookings table if not exists
    $conn->exec("
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
            package_price DECIMAL(10,2),
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

    // Validate required fields
    $required_fields = ['client_name', 'client_phone', 'client_whatsapp', 'booking_date', 'booking_time', 'package_id'];
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
    $booking_time = sanitize($_POST['booking_time']);
    $booking_day = isset($_POST['booking_day']) ? sanitize($_POST['booking_day']) : '';
    $package_id = intval($_POST['package_id']);

    // Validate and detect phone carrier
    $phone_validation = detect_phone_carrier($client_phone);
    if (!$phone_validation['valid']) {
        echo json_encode([
            'success' => false,
            'message' => 'رقم الهاتف غير صحيح. يرجى إدخال رقم هاتف مصري صحيح يبدأ بـ 010، 011، 012، أو 015'
        ]);
        exit;
    }

    // Validate Egyptian mobile
    if (!validate_egyptian_mobile($client_phone)) {
        echo json_encode([
            'success' => false,
            'message' => 'رقم الهاتف يجب أن يكون رقم موبايل مصري (11 رقم يبدأ بـ 01)'
        ]);
        exit;
    }

    // Validate and detect WhatsApp carrier
    $whatsapp_validation = detect_phone_carrier($client_whatsapp);
    if (!$whatsapp_validation['valid']) {
        echo json_encode([
            'success' => false,
            'message' => 'رقم الواتساب غير صحيح. يرجى إدخال رقم واتساب مصري صحيح'
        ]);
        exit;
    }

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

    // Get package details
    $stmt = $conn->prepare("SELECT id, name, price FROM packages WHERE id = ? AND is_active = 1");
    $stmt->execute([$package_id]);
    $package = $stmt->fetch();

    if (!$package) {
        echo json_encode(['success' => false, 'message' => 'الباقة المحددة غير متاحة']);
        exit;
    }

    // Format phone numbers
    $formatted_phone = $phone_validation['formatted'];
    $formatted_whatsapp = $whatsapp_validation['formatted'];

    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO bookings (
            client_name,
            client_email,
            client_address,
            client_phone,
            client_phone_carrier,
            client_whatsapp,
            client_whatsapp_carrier,
            booking_date,
            booking_time,
            booking_day,
            package_id,
            package_name,
            package_price,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $success = $stmt->execute([
        $client_name,
        $client_email,
        $client_address,
        $formatted_phone,
        $phone_validation['carrier'],
        $formatted_whatsapp,
        $whatsapp_validation['carrier'],
        $booking_date,
        $booking_time,
        $booking_day,
        $package_id,
        $package['name'],
        $package['price']
    ]);

    if (!$success) {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('فشل حفظ الحجز: ' . implode(' - ', $errorInfo));
    }

    $booking_id = $conn->lastInsertId();

    // Build success message with booking details AND carrier info
    $success_msg = sprintf(
        "✅ تم تأكيد حجزك بنجاح!\n\n" .
        "📋 رقم الحجز: #%d\n" .
        "📅 التاريخ: %s\n" .
        "⏰ الموعد: %s\n" .
        "📦 الباقة: %s\n\n" .
        "📱 رقم الهاتف: %s (%s)\n" .
        "💬 رقم الواتساب: %s (%s)\n\n" .
        "سنتواصل معك قريباً عبر واتساب.\n" .
        "يمكنك متابعة حالة حجزك من خلال التواصل معنا.",
        $booking_id,
        date('Y/m/d', strtotime($booking_date)),
        $booking_time,
        $package['name'],
        $formatted_phone,
        $phone_validation['carrier'],
        $formatted_whatsapp,
        $whatsapp_validation['carrier']
    );

    echo json_encode([
        'success' => true,
        'message' => $success_msg,
        'booking_id' => $booking_id,
        'booking_details' => [
            'date' => $booking_date,
            'time' => $booking_time,
            'package' => $package['name'],
            'package_price' => $package['price'],
            'phone' => $formatted_phone,
            'phone_carrier' => $phone_validation['carrier'],
            'whatsapp' => $formatted_whatsapp,
            'whatsapp_carrier' => $whatsapp_validation['carrier']
        ]
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
