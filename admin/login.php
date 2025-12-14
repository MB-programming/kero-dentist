<?php
require_once '../includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/admin/index.php');
}

$error = '';
$debug_info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'الرجاء إدخال اسم المستخدم وكلمة المرور';
    } else {
        try {
            // Check database connection
            if (!$conn) {
                $error = 'خطأ في الاتصال بقاعدة البيانات';
            } else {
                $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                if (!$admin) {
                    $error = 'اسم المستخدم غير موجود';
                    // Debug: Show available usernames
                    $stmt = $conn->query("SELECT username FROM admin_users");
                    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    $debug_info = 'المستخدمين المتاحين: ' . implode(', ', $users);
                } elseif (!password_verify($password, $admin['password'])) {
                    $error = 'كلمة المرور غير صحيحة';
                    // Debug info
                    $debug_info = 'جرب: admin123 للمستخدم admin';
                } else {
                    // Success - login
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_email'] = $admin['email'];

                    // Redirect to admin panel
                    header("Location: " . SITE_URL . "/admin/index.php");
                    exit();
                }
            }
        } catch(PDOException $e) {
            $error = 'حدث خطأ في قاعدة البيانات';
            $debug_info = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <i class="fas fa-tooth"></i>
                <h1>لوحة التحكم</h1>
                <p>مرحباً بك في نظام الإدارة</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if ($debug_info): ?>
            <div class="alert alert-info" style="background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;">
                <i class="fas fa-info-circle"></i>
                <?php echo htmlspecialchars($debug_info); ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> اسم المستخدم
                    </label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> كلمة المرور
                    </label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>
            </form>

            <div class="login-footer">
                <a href="../index.php">
                    <i class="fas fa-arrow-right"></i> العودة إلى الموقع
                </a>
            </div>
        </div>
    </div>
</body>
</html>
