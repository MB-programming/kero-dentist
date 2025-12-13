<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص الإعدادات</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid green; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid red; margin: 10px 0; }
        .info { color: blue; padding: 10px; background: #d1ecf1; border: 1px solid blue; margin: 10px 0; }
        h2 { border-bottom: 2px solid #333; padding-bottom: 10px; }
        pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 فحص إعدادات الموقع</h1>

    <?php
    // 1. Check PHP Version
    echo "<h2>1. إصدار PHP</h2>";
    $php_version = phpversion();
    if (version_compare($php_version, '7.4', '>=')) {
        echo "<div class='success'>✅ إصدار PHP مناسب: {$php_version}</div>";
    } else {
        echo "<div class='error'>❌ إصدار PHP قديم: {$php_version} (يتطلب 7.4 أو أحدث)</div>";
    }

    // 2. Check Required Extensions
    echo "<h2>2. PHP Extensions</h2>";
    $required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'fileinfo'];
    foreach ($required_extensions as $ext) {
        if (extension_loaded($ext)) {
            echo "<div class='success'>✅ {$ext} مفعّل</div>";
        } else {
            echo "<div class='error'>❌ {$ext} غير مفعّل</div>";
        }
    }

    // 3. Database Configuration
    echo "<h2>3. إعدادات قاعدة البيانات</h2>";
    $db_config = [
        'DB_HOST' => 'localhost',
        'DB_USER' => 'root',
        'DB_PASS' => '',
        'DB_NAME' => 'dentist_booking'
    ];

    echo "<div class='info'>";
    echo "<strong>الإعدادات الحالية:</strong><br>";
    echo "Host: {$db_config['DB_HOST']}<br>";
    echo "User: {$db_config['DB_USER']}<br>";
    echo "Database: {$db_config['DB_NAME']}<br>";
    echo "</div>";

    // 4. Test Database Connection
    echo "<h2>4. اختبار الاتصال بقاعدة البيانات</h2>";
    try {
        $conn = new PDO(
            "mysql:host={$db_config['DB_HOST']};charset=utf8mb4",
            $db_config['DB_USER'],
            $db_config['DB_PASS']
        );
        echo "<div class='success'>✅ الاتصال بخادم MySQL ناجح</div>";

        // Check if database exists
        $stmt = $conn->query("SHOW DATABASES LIKE '{$db_config['DB_NAME']}'");
        if ($stmt->rowCount() > 0) {
            echo "<div class='success'>✅ قاعدة البيانات '{$db_config['DB_NAME']}' موجودة</div>";

            // Connect to specific database
            $conn = new PDO(
                "mysql:host={$db_config['DB_HOST']};dbname={$db_config['DB_NAME']};charset=utf8mb4",
                $db_config['DB_USER'],
                $db_config['DB_PASS']
            );

            // Check tables
            echo "<h3>الجداول الموجودة:</h3>";
            $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            if (count($tables) > 0) {
                echo "<div class='success'>✅ " . count($tables) . " جدول موجود:<br>";
                echo implode(', ', $tables) . "</div>";
            } else {
                echo "<div class='error'>❌ لا توجد جداول. قم باستيراد ملف database.sql</div>";
            }

            // Check admin user
            if (in_array('admin_users', $tables)) {
                $stmt = $conn->query("SELECT COUNT(*) FROM admin_users");
                $count = $stmt->fetchColumn();
                if ($count > 0) {
                    echo "<div class='success'>✅ يوجد {$count} مستخدم إداري</div>";
                } else {
                    echo "<div class='error'>❌ لا يوجد مستخدمين إداريين</div>";
                }
            }

        } else {
            echo "<div class='error'>❌ قاعدة البيانات '{$db_config['DB_NAME']}' غير موجودة!</div>";
            echo "<div class='info'>
                <strong>لإنشاء قاعدة البيانات:</strong><br>
                1. افتح phpMyAdmin أو MySQL CLI<br>
                2. قم بتنفيذ الأمر:<br>
                <pre>CREATE DATABASE dentist_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</pre>
                3. استورد ملف database.sql
            </div>";
        }

    } catch(PDOException $e) {
        echo "<div class='error'>❌ خطأ في الاتصال: " . $e->getMessage() . "</div>";
        echo "<div class='info'>
            <strong>تحقق من:</strong><br>
            1. أن MySQL يعمل<br>
            2. بيانات الاتصال في includes/config.php صحيحة<br>
            3. المستخدم لديه صلاحيات الوصول
        </div>";
    }

    // 5. File Permissions
    echo "<h2>5. صلاحيات الملفات</h2>";
    $upload_dir = __DIR__ . '/public/uploads';
    if (is_dir($upload_dir)) {
        if (is_writable($upload_dir)) {
            echo "<div class='success'>✅ مجلد uploads قابل للكتابة</div>";
        } else {
            echo "<div class='error'>❌ مجلد uploads غير قابل للكتابة. قم بتغيير الصلاحيات إلى 755</div>";
        }
    } else {
        echo "<div class='error'>❌ مجلد uploads غير موجود</div>";
    }

    // 6. Site Configuration
    echo "<h2>6. إعدادات الموقع</h2>";
    echo "<div class='info'>";
    echo "<strong>URL الحالي:</strong> " . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : 'غير محدد') . "<br>";
    echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
    echo "<strong>Script Path:</strong> " . __DIR__ . "<br>";
    echo "</div>";

    // 7. Next Steps
    echo "<h2>7. الخطوات التالية</h2>";
    echo "<div class='info'>";
    echo "<ol>";
    echo "<li>إذا كانت جميع الفحوصات ✅، احذف هذا الملف (check-setup.php)</li>";
    echo "<li>افتح لوحة التحكم: <a href='admin/login.php'>admin/login.php</a></li>";
    echo "<li>بيانات الدخول الافتراضية:<br>Username: admin<br>Password: admin123</li>";
    echo "<li><strong>مهم:</strong> غيّر كلمة المرور فوراً!</li>";
    echo "</ol>";
    echo "</div>";
    ?>

    <hr>
    <p style="text-align: center; color: #666;">
        <small>يمكنك حذف هذا الملف بعد التأكد من أن كل شيء يعمل</small>
    </p>
</body>
</html>
