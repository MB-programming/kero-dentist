<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص وإصلاح قاعدة البيانات</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            text-align: right;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-right: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-right: 4px solid #dc3545;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-right: 4px solid #ffc107;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-right: 4px solid #17a2b8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: right;
        }
        th {
            background: #3498db;
            color: white;
        }
        .btn {
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #2980b9;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 فحص وإصلاح قاعدة البيانات</h1>

<?php
require_once 'includes/config.php';

$results = [];
$errors = [];

// 1. Check database connection
try {
    $results[] = "✅ الاتصال بقاعدة البيانات: <strong>" . DB_NAME . "</strong> - ناجح";
} catch(PDOException $e) {
    $errors[] = "❌ فشل الاتصال بقاعدة البيانات: " . $e->getMessage();
    echo "<div class='error'>" . end($errors) . "</div>";
    exit;
}

// 2. Check if tables exist
$required_tables = [
    'admin_users',
    'services',
    'packages',
    'bookings',
    'blog_posts',
    'reviews',
    'settings',
    'sliders',
    'email_templates',
    'menu_items',
    'doctors'
];

$existing_tables = [];
$missing_tables = [];

foreach ($required_tables as $table) {
    try {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $existing_tables[] = $table;
        } else {
            $missing_tables[] = $table;
        }
    } catch(PDOException $e) {
        $missing_tables[] = $table;
    }
}

echo "<div class='info'>";
echo "<h3>📊 فحص الجداول:</h3>";
echo "<p>الجداول الموجودة: " . count($existing_tables) . " / " . count($required_tables) . "</p>";
echo "</div>";

if (count($missing_tables) > 0) {
    echo "<div class='warning'>";
    echo "<h3>⚠️ جداول مفقودة:</h3>";
    echo "<ul>";
    foreach ($missing_tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    echo "<p><strong>الحل:</strong> يجب استيراد ملف <code>complete_database.sql</code> عبر phpMyAdmin</p>";
    echo "</div>";
}

// 3. Check critical data
echo "<div class='info'><h3>📈 إحصائيات البيانات:</h3></div>";

echo "<table>";
echo "<tr><th>الجدول</th><th>عدد السجلات</th><th>الحالة</th></tr>";

$data_stats = [];

foreach ($existing_tables as $table) {
    try {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $stmt->fetch()['count'];
        $data_stats[$table] = $count;

        $status_class = $count > 0 ? 'success' : 'warning';
        $status_icon = $count > 0 ? '✅' : '⚠️';

        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td>$count</td>";
        echo "<td style='color: " . ($count > 0 ? 'green' : 'orange') . "'>$status_icon</td>";
        echo "</tr>";
    } catch(PDOException $e) {
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td colspan='2' style='color: red'>❌ خطأ: " . $e->getMessage() . "</td>";
        echo "</tr>";
    }
}

echo "</table>";

// 4. Check for admin users
if (in_array('admin_users', $existing_tables)) {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM admin_users");
    $admin_count = $stmt->fetch()['count'];

    if ($admin_count == 0) {
        echo "<div class='warning'>";
        echo "<h3>⚠️ لا يوجد مستخدمين أدمن!</h3>";
        echo "<p>سيتم إنشاء مستخدم افتراضي...</p>";

        try {
            $default_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute(['admin', $default_password, 'admin@example.com']);
            echo "<div class='success'>";
            echo "<h4>✅ تم إنشاء مستخدم افتراضي:</h4>";
            echo "<p><strong>اسم المستخدم:</strong> admin</p>";
            echo "<p><strong>كلمة المرور:</strong> admin123</p>";
            echo "<p style='color: red;'><strong>⚠️ مهم جداً:</strong> قم بتغيير كلمة المرور فوراً بعد تسجيل الدخول!</p>";
            echo "</div>";
        } catch(PDOException $e) {
            echo "<div class='error'>❌ فشل إنشاء المستخدم: " . $e->getMessage() . "</div>";
        }
        echo "</div>";
    } else {
        echo "<div class='success'>";
        echo "✅ يوجد $admin_count مستخدم أدمن";
        echo "</div>";
    }
}

// 5. Check for sliders
if (in_array('sliders', $existing_tables)) {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM sliders");
    $slider_count = $stmt->fetch()['count'];

    if ($slider_count == 0) {
        echo "<div class='warning'>";
        echo "<h3>⚠️ لا توجد سلايدرز!</h3>";
        echo "<p>يمكنك إضافة سلايدرز من لوحة التحكم: <a href='admin/sliders.php' class='btn'>إدارة السلايدرز</a></p>";
        echo "<p>أو استيراد الملف الكامل <code>complete_database.sql</code> الذي يحتوي على بيانات تجريبية</p>";
        echo "</div>";
    } else {
        echo "<div class='success'>";
        echo "✅ يوجد $slider_count سلايدر";
        echo "</div>";
    }
}

// 6. Database configuration summary
echo "<div class='info'>";
echo "<h3>🔧 إعدادات قاعدة البيانات الحالية:</h3>";
echo "<table>";
echo "<tr><th>الإعداد</th><th>القيمة</th></tr>";
echo "<tr><td>Database Host</td><td>" . DB_HOST . "</td></tr>";
echo "<tr><td>Database Name</td><td><strong>" . DB_NAME . "</strong></td></tr>";
echo "<tr><td>Database User</td><td>" . DB_USER . "</td></tr>";
echo "<tr><td>Connection Status</td><td style='color: green;'>✅ متصل</td></tr>";
echo "</table>";
echo "</div>";

// 7. Quick fix options
echo "<div class='info'>";
echo "<h3>🛠️ خيارات الإصلاح:</h3>";
echo "<ol>";
echo "<li><strong>إذا كانت قاعدة البيانات فارغة أو الجداول مفقودة:</strong><br>";
echo "استورد ملف <code>complete_database.sql</code> عبر phpMyAdmin أو سطر الأوامر:</li>";
echo "<pre>mysql -u " . DB_USER . " -p " . DB_NAME . " < complete_database.sql</pre>";
echo "<li><strong>إذا كنت تريد نقل البيانات من قاعدة بيانات أخرى:</strong><br>";
echo "استخدم phpMyAdmin للتصدير من القاعدة القديمة والاستيراد في القاعدة الجديدة</li>";
echo "<li><strong>للوصول إلى لوحة التحكم:</strong><br>";
echo "<a href='admin/login.php' class='btn'>تسجيل الدخول للوحة التحكم</a></li>";
echo "</ol>";
echo "</div>";

// 8. Show all results
if (count($results) > 0) {
    echo "<div class='success'>";
    echo "<h3>✅ العمليات الناجحة:</h3>";
    echo "<ul>";
    foreach ($results as $result) {
        echo "<li>$result</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (count($errors) > 0) {
    echo "<div class='error'>";
    echo "<h3>❌ الأخطاء:</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Summary
echo "<div style='background: #e8f4fd; padding: 20px; border-radius: 10px; margin-top: 30px;'>";
echo "<h3 style='color: #0066cc;'>📝 الملخص:</h3>";
echo "<p>✅ قاعدة البيانات: <strong>" . DB_NAME . "</strong></p>";
echo "<p>✅ الجداول الموجودة: <strong>" . count($existing_tables) . "</strong> من " . count($required_tables) . "</p>";
if (count($missing_tables) > 0) {
    echo "<p style='color: orange;'>⚠️ جداول مفقودة: <strong>" . count($missing_tables) . "</strong></p>";
}
echo "<p>📊 إجمالي السجلات: <strong>" . array_sum($data_stats) . "</strong></p>";
echo "</div>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='index.php' class='btn'>الذهاب للموقع</a>";
echo "<a href='admin/login.php' class='btn'>لوحة التحكم</a>";
echo "<a href='check_and_fix_database.php' class='btn'>تحديث الصفحة</a>";
echo "</div>";
?>

    </div>
</body>
</html>
