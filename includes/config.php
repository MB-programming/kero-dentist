<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u186120816_tantawy');
define('DB_PASS', '54AC>TU/t');
define('DB_NAME', 'u186120816_tantawy');

// Site Configuration - Auto detect
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Detect if we're in admin or main site
$script_name = $_SERVER['SCRIPT_NAME'];
$is_admin = strpos($script_name, '/admin/') !== false;

// Base path detection
if ($is_admin) {
    // If in admin, go up one level
    $base_path = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
} else {
    $base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
}

define('SITE_URL', $protocol . '://' . $host . $base_path);
define('BASE_PATH', __DIR__ . '/..');
define('ADMIN_URL', SITE_URL . '/admin');
define('ASSETS_URL', SITE_URL . '/assets');
define('UPLOAD_DIR', BASE_PATH . '/uploads');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Timezone
date_default_timezone_set('Africa/Cairo');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
$conn = null;
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    // Log error and show friendly message
    error_log("Database connection error: " . $e->getMessage());
    die("خطأ في الاتصال بقاعدة البيانات. يرجى التأكد من:<br>1. إنشاء قاعدة البيانات (u186120816_tantawy)<br>2. استيراد ملف database.sql<br>3. التحقق من بيانات الاتصال في includes/config.php<br>4. وضع كلمة مرور قاعدة البيانات في DB_PASS<br><br>Error: " . $e->getMessage());
}

// Helper Functions
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

function getSetting($key, $default = '') {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch(PDOException $e) {
        return $default;
    }
}

function updateSetting($key, $value) {
    global $conn;
    try {
        $stmt = $conn->prepare("
            INSERT INTO settings (setting_key, setting_value, setting_type)
            VALUES (?, ?, 'text')
            ON DUPLICATE KEY UPDATE setting_value = ?
        ");
        return $stmt->execute([$key, $value, $value]);
    } catch(PDOException $e) {
        error_log("updateSetting error: " . $e->getMessage());
        return false;
    }
}

function uploadFile($file, $folder = 'blog') {
    $target_dir = UPLOAD_PATH . $folder . '/';

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }

    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return [
            'success' => true,
            'filename' => $new_filename,
            'path' => $folder . '/' . $new_filename,
            'url' => UPLOAD_URL . $folder . '/' . $new_filename
        ];
    }

    return ['success' => false, 'message' => 'Upload failed'];
}

function generateSlug($string) {
    $string = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $string);
    $string = preg_replace('/\s+/', '-', $string);
    $string = strtolower($string);
    return trim($string, '-');
}

function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function formatPrice($price) {
    return number_format($price, 2) . ' EGP';
}
