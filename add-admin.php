<?php
// صفحة سريعة لإضافة admin جديد
// احذف هذا الملف بعد الاستخدام!

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u186120816_tantawy');
define('DB_PASS', '54AC>TU/t');
define('DB_NAME', 'u186120816_tantawy');

try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);

    if (!empty($username) && !empty($password) && !empty($email)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $email]);
            $message = "✅ تم إضافة المستخدم بنجاح!";
            $success = true;
        } catch(PDOException $e) {
            $message = "❌ خطأ: " . $e->getMessage();
        }
    } else {
        $message = "❌ يرجى ملء جميع الحقول";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مستخدم إداري</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }
        .quick-add {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .quick-add h3 {
            color: #1976d2;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .quick-add code {
            background: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ إضافة مستخدم إداري</h1>
        <p class="subtitle">أضف مستخدم جديد للوحة التحكم</p>

        <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <div class="quick-add">
            <h3>⚡ إضافة سريعة للمستخدم: mina</h3>
            <form method="POST" style="display: flex; gap: 10px;">
                <input type="hidden" name="username" value="mina">
                <input type="hidden" name="password" value="mina2002306">
                <input type="hidden" name="email" value="mina@example.com">
                <button type="submit" style="width: auto; padding: 10px 20px; font-size: 14px;">
                    ➕ إضافة mina فوراً
                </button>
            </form>
            <p style="margin-top: 10px; font-size: 13px; color: #555;">
                Username: <code>mina</code> | Password: <code>mina2002306</code>
            </p>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">

        <form method="POST">
            <div class="form-group">
                <label>اسم المستخدم</label>
                <input type="text" name="username" required placeholder="mina">
            </div>

            <div class="form-group">
                <label>كلمة المرور</label>
                <input type="password" name="password" required placeholder="mina2002306">
            </div>

            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" required placeholder="mina@example.com">
            </div>

            <button type="submit">➕ إضافة المستخدم</button>
        </form>
        <?php else: ?>
        <div style="text-align: center; margin-top: 20px;">
            <a href="admin/login.php" style="display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                🔐 تسجيل الدخول
            </a>
        </div>
        <?php endif; ?>

        <div class="warning">
            ⚠️ <strong>تحذير أمني:</strong><br>
            احذف هذا الملف (<code>add-admin.php</code>) فوراً بعد إضافة المستخدم!
        </div>
    </div>
</body>
</html>
