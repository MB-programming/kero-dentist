<?php
/**
 * Add Admin User Utility
 * أداة إضافة مستخدم أدمن في قاعدة البيانات الجديدة
 * يستخدم نفس الاتصال من config.php
 */

// Load configuration from the main config file
require_once 'includes/config.php';

// Get current database name
$current_db = '';
try {
    $stmt = $conn->query("SELECT DATABASE() as db");
    $result = $stmt->fetch();
    $current_db = $result['db'];
} catch(PDOException $e) {
    die("خطأ في الاتصال: " . $e->getMessage());
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);

    if (!empty($username) && !empty($password) && !empty($email)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO admin_users (username, password, email, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$username, $hashed_password, $email]);

            $message = "✅ تم إضافة المستخدم بنجاح في قاعدة البيانات: <strong style='color:#28a745;'>{$current_db}</strong><br><br>";
            $message .= "<strong>بيانات الدخول:</strong><br>";
            $message .= "اسم المستخدم: <strong>{$username}</strong><br>";
            $message .= "كلمة المرور: <strong>{$password}</strong><br><br>";
            $message .= "<a href='admin/login.php' style='color:#667eea;font-weight:bold;'>انتقل لصفحة تسجيل الدخول »</a>";
            $message_type = 'success';
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "❌ اسم المستخدم أو البريد الإلكتروني موجود مسبقاً!";
            } else {
                $message = "❌ خطأ: " . $e->getMessage();
            }
            $message_type = 'error';
        }
    } else {
        $message = "❌ يرجى ملء جميع الحقول";
        $message_type = 'error';
    }
}

// Get existing admins
$admins = [];
try {
    $stmt = $conn->query("SELECT id, username, email, created_at FROM admin_users ORDER BY created_at DESC LIMIT 10");
    $admins = $stmt->fetchAll();
} catch(PDOException $e) {
    $admins = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مستخدم أدمن - <?php echo $current_db; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
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
            max-width: 600px;
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
            margin-bottom: 20px;
            font-size: 14px;
        }
        .db-info {
            background: #e3f2fd;
            padding: 12px;
            border-radius: 8px;
            border-right: 4px solid #2196f3;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .db-info strong {
            color: #1976d2;
        }
        .db-name {
            font-weight: bold;
            color: <?php echo $current_db === 'u186120816_kero_dentist' ? 'green' : 'red'; ?>;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: right;
            line-height: 1.8;
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
            font-family: inherit;
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
        <h1><i class="fas fa-user-shield"></i> إضافة مستخدم إداري</h1>
        <p class="subtitle">أضف مستخدم جديد للوحة التحكم</p>

        <div class="db-info">
            <i class="fas fa-database"></i> <strong>قاعدة البيانات:</strong>
            <span class="db-name"><?php echo $current_db; ?></span>
            <?php if ($current_db === 'u186120816_kero_dentist'): ?>
                <i class="fas fa-check-circle" style="color:green;"></i>
            <?php else: ?>
                <i class="fas fa-exclamation-triangle" style="color:red;"></i>
            <?php endif; ?>
        </div>

        <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <?php if ($message_type !== 'success'): ?>
        <div class="quick-add">
            <h3><i class="fas fa-bolt"></i> إضافة سريعة للمستخدم: mina</h3>
            <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                <input type="hidden" name="username" value="mina">
                <input type="hidden" name="password" value="mina2002306">
                <input type="hidden" name="email" value="mina@example.com">
                <button type="submit" style="width: auto; padding: 10px 20px; font-size: 14px;">
                    <i class="fas fa-user-plus"></i> إضافة mina فوراً
                </button>
            </form>
            <p style="margin-top: 10px; font-size: 13px; color: #555;">
                Username: <code>mina</code> | Password: <code>mina2002306</code>
            </p>
        </div>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> اسم المستخدم</label>
                <input type="text" name="username" required placeholder="أدخل اسم المستخدم">
            </div>

            <div class="form-group">
                <label><i class="fas fa-lock"></i> كلمة المرور</label>
                <input type="password" name="password" required placeholder="أدخل كلمة المرور">
            </div>

            <div class="form-group">
                <label><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
                <input type="email" name="email" required placeholder="example@domain.com">
            </div>

            <button type="submit"><i class="fas fa-user-plus"></i> إضافة المستخدم</button>
        </form>
        <?php endif; ?>

        <?php if (count($admins) > 0): ?>
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #f0f0f0;">
            <h3 style="margin-bottom: 15px; color: #333;"><i class="fas fa-users"></i> المستخدمين الحاليين (<?php echo count($admins); ?>)</h3>
            <div style="max-height: 200px; overflow-y: auto;">
                <?php foreach ($admins as $admin): ?>
                <div style="padding: 10px; background: #f9f9f9; border-radius: 6px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong><?php echo htmlspecialchars($admin['username']); ?></strong>
                        <small style="color: #666; display: block;"><?php echo htmlspecialchars($admin['email']); ?></small>
                    </div>
                    <span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px;">#<?php echo $admin['id']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <a href="test_connection.php" style="flex: 1; padding: 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                <i class="fas fa-database"></i> فحص الاتصال
            </a>
            <a href="admin/login.php" style="flex: 1; padding: 12px; background: #28a745; color: white; text-decoration: none; border-radius: 8px; text-align: center; font-weight: 600;">
                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
            </a>
        </div>

        <div class="warning">
            <i class="fas fa-exclamation-triangle"></i> <strong>تحذير أمني:</strong><br>
            احذف هذا الملف (<code>add-admin.php</code>) فوراً بعد إضافة المستخدم!
        </div>
    </div>
</body>
</html>
