<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
requireLogin();

if (!isset($_FILES['file'])) {
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$result = uploadFile($_FILES['file'], 'blog');

if ($result['success']) {
    echo json_encode([
        'location' => $result['url']
    ]);
} else {
    echo json_encode([
        'error' => $result['message'] ?? 'Upload failed'
    ]);
}
