<?php
require_once '../includes/config.php';
requireLogin();

$format = isset($_GET['format']) ? sanitize($_GET['format']) : 'excel';

// Fetch all bookings
$query = "
    SELECT b.*, p.name as package_name, p.price
    FROM bookings b
    LEFT JOIN packages p ON b.package_id = p.id
    ORDER BY b.created_at DESC
";
$stmt = $conn->query($query);
$bookings = $stmt->fetchAll();

$status_labels = [
    'pending' => 'قيد الانتظار',
    'approved' => 'موافق عليه',
    'rejected' => 'مرفوض',
    'completed' => 'مكتمل',
    'cancelled' => 'ملغي'
];

// Prepare export data
$html = '
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>تقرير الحجوزات</title>
    <style>
        body { font-family: Arial, sans-serif; direction: rtl; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: right; }
        th { background-color: #2563eb; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        h1 { text-align: center; color: #1f2937; }
        .header { text-align: center; margin-bottom: 20px; }
        .export-date { text-align: center; color: #6b7280; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير الحجوزات</h1>
        <p class="export-date">تاريخ التصدير: ' . date('Y-m-d H:i:s') . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>رقم الحجز</th>
                <th>اسم العميل</th>
                <th>البريد الإلكتروني</th>
                <th>الهاتف</th>
                <th>واتساب</th>
                <th>العنوان</th>
                <th>الباقة</th>
                <th>السعر</th>
                <th>تاريخ الحجز</th>
                <th>اليوم</th>
                <th>الحالة</th>
                <th>تاريخ الإنشاء</th>
            </tr>
        </thead>
        <tbody>';

foreach ($bookings as $booking) {
    $status = $status_labels[$booking['status']] ?? $booking['status'];
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($booking['id']) . '</td>';
    $html .= '<td>' . htmlspecialchars($booking['client_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($booking['client_email'] ?? '-') . '</td>';
    $html .= '<td>' . htmlspecialchars($booking['client_phone']) . '</td>';
    $html .= '<td>' . htmlspecialchars($booking['client_whatsapp']) . '</td>';
    $html .= '<td>' . htmlspecialchars($booking['client_address'] ?? '-') . '</td>';
    $html .= '<td>' . htmlspecialchars($booking['package_name']) . '</td>';
    $html .= '<td>' . number_format($booking['price'], 2) . ' جنيه</td>';
    $html .= '<td>' . htmlspecialchars($booking['booking_date']) . '</td>';
    $html .= '<td>' . htmlspecialchars($booking['booking_day']) . '</td>';
    $html .= '<td>' . htmlspecialchars($status) . '</td>';
    $html .= '<td>' . htmlspecialchars($booking['created_at']) . '</td>';
    $html .= '</tr>';
}

$html .= '
        </tbody>
    </table>
    <div style="margin-top: 30px; text-align: center; color: #6b7280;">
        <p>إجمالي الحجوزات: ' . count($bookings) . '</p>
        <p>تم التصدير من: ' . getSetting('site_name', 'Dr. Ahmed Clinic') . '</p>
    </div>
</body>
</html>';

if ($format === 'word') {
    // Export as Word Document
    header('Content-Type: application/vnd.ms-word; charset=utf-8');
    header('Content-Disposition: attachment; filename="bookings_' . date('Y-m-d') . '.doc"');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    echo $html;
} else {
    // Export as Excel
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="bookings_' . date('Y-m-d') . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    echo $html;
}
exit;
