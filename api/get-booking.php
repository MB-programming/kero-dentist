<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT b.*, p.name as package_name, p.price
        FROM bookings b
        LEFT JOIN packages p ON b.package_id = p.id
        WHERE b.id = ?
    ");
    $stmt->execute([$id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    // Format dates
    $booking['booking_date'] = formatDate($booking['booking_date']);
    $booking['created_at'] = date('d/m/Y H:i', strtotime($booking['created_at']));

    echo json_encode([
        'success' => true,
        'booking' => $booking
    ]);

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
