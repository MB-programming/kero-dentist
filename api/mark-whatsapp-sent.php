<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = isset($data['booking_id']) ? intval($data['booking_id']) : 0;

if (!$booking_id) {
    echo json_encode(['success' => false]);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE bookings SET whatsapp_sent = 1 WHERE id = ?");
    $stmt->execute([$booking_id]);

    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false]);
}
