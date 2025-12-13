<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تثبيت الموقع</title>
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
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 { color: #333; margin-bottom: 10px; text-align: center; }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; }
        .success {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .error {
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .info {
            color: #004085;
            background: #cce5ff;
            border: 1px solid #b8daff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
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
        .steps {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .steps ol {
            margin-right: 20px;
        }
        .steps li {
            margin-bottom: 10px;
            line-height: 1.6;
        }
        code {
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🦷 تثبيت موقع العيادة</h1>
        <p class="subtitle">إعداد قاعدة البيانات والموقع</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db_host = trim($_POST['db_host']);
            $db_user = trim($_POST['db_user']);
            $db_pass = $_POST['db_pass'];
            $db_name = trim($_POST['db_name']);

            try {
                // Connect to MySQL server
                $conn = new PDO(
                    "mysql:host={$db_host};charset=utf8mb4",
                    $db_user,
                    $db_pass,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                echo "<div class='success'>✅ الاتصال بخادم MySQL ناجح</div>";

                // Create database
                $conn->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                echo "<div class='success'>✅ تم إنشاء قاعدة البيانات '{$db_name}'</div>";

                // Connect to the new database
                $conn->exec("USE `{$db_name}`");

                // Read and execute SQL file
                $sql_file = __DIR__ . '/database.sql';
                if (file_exists($sql_file)) {
                    $sql = file_get_contents($sql_file);

                    // Remove USE database command if exists
                    $sql = preg_replace('/USE\s+\w+;/i', '', $sql);

                    // Split by semicolon and execute each statement
                    $statements = array_filter(array_map('trim', explode(';', $sql)));

                    foreach ($statements as $statement) {
                        if (!empty($statement)) {
                            $conn->exec($statement);
                        }
                    }

                    echo "<div class='success'>✅ تم استيراد جداول قاعدة البيانات بنجاح</div>";
                } else {
                    echo "<div class='error'>❌ ملف database.sql غير موجود</div>";
                }

                // Update config file
                $config_file = __DIR__ . '/includes/config.php';
                $config_content = file_get_contents($config_file);
                $config_content = preg_replace("/define\('DB_HOST',\s*'[^']*'\);/", "define('DB_HOST', '{$db_host}');", $config_content);
                $config_content = preg_replace("/define\('DB_USER',\s*'[^']*'\);/", "define('DB_USER', '{$db_user}');", $config_content);
                $config_content = preg_replace("/define\('DB_PASS',\s*'[^']*'\);/", "define('DB_PASS', '{$db_pass}');", $config_content);
                $config_content = preg_replace("/define\('DB_NAME',\s*'[^']*'\);/", "define('DB_NAME', '{$db_name}');", $config_content);

                file_put_contents($config_file, $config_content);
                echo "<div class='success'>✅ تم تحديث ملف الإعدادات</div>";

                echo "<div class='info'>";
                echo "<h3>✨ تم التثبيت بنجاح!</h3>";
                echo "<p><strong>الخطوات التالية:</strong></p>";
                echo "<ol>";
                echo "<li>احذف ملفات التثبيت (setup.php و check-setup.php)</li>";
                echo "<li>افتح لوحة التحكم: <a href='admin/login.php' style='color: #667eea; font-weight: bold;'>admin/login.php</a></li>";
                echo "<li><strong>بيانات الدخول:</strong><br>Username: <code>admin</code><br>Password: <code>admin123</code></li>";
                echo "<li><strong>⚠️ مهم جداً:</strong> غيّر كلمة المرور فوراً من لوحة التحكم!</li>";
                echo "</ol>";
                echo "</div>";

            } catch(PDOException $e) {
                echo "<div class='error'>";
                echo "<strong>❌ خطأ:</strong> " . $e->getMessage();
                echo "</div>";
            }

        } else {
            ?>
            <div class="info">
                <strong>ℹ️ قبل البدء:</strong><br>
                تأكد من أن لديك صلاحيات إنشاء قواعد البيانات في MySQL
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>اسم الخادم (Host)</label>
                    <input type="text" name="db_host" value="localhost" required>
                </div>

                <div class="form-group">
                    <label>اسم المستخدم (Username)</label>
                    <input type="text" name="db_user" value="root" required>
                </div>

                <div class="form-group">
                    <label>كلمة المرور (Password)</label>
                    <input type="password" name="db_pass" placeholder="اتركها فارغة إذا لم يكن هناك كلمة مرور">
                </div>

                <div class="form-group">
                    <label>اسم قاعدة البيانات (Database Name)</label>
                    <input type="text" name="db_name" value="dentist_booking" required>
                </div>

                <button type="submit">🚀 ابدأ التثبيت</button>
            </form>

            <div class="steps">
                <h3>📋 ماذا سيحدث عند التثبيت؟</h3>
                <ol>
                    <li>سيتم إنشاء قاعدة البيانات</li>
                    <li>سيتم إنشاء جميع الجداول</li>
                    <li>سيتم إضافة البيانات الافتراضية</li>
                    <li>سيتم تحديث ملف الإعدادات</li>
                    <li>سيكون الموقع جاهزاً للاستخدام!</li>
                </ol>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
