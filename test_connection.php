<?php
/**
 * Test Database Connection
 * هذا الملف للتحقق من الاتصال بقاعدة البيانات الصحيحة
 */

echo "<!DOCTYPE html>";
echo "<html dir='rtl'><head><meta charset='UTF-8'><title>فحص الاتصال</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;direction:rtl;}";
echo ".box{background:white;padding:20px;margin:10px 0;border-radius:8px;box-shadow:0 2px 5px rgba(0,0,0,0.1);}";
echo ".success{border-right:4px solid #28a745;background:#d4edda;}";
echo ".error{border-right:4px solid #dc3545;background:#f8d7da;}";
echo ".info{border-right:4px solid #17a2b8;background:#d1ecf1;}";
echo "pre{background:#f8f9fa;padding:10px;border-radius:4px;overflow-x:auto;}";
echo "table{width:100%;border-collapse:collapse;margin:10px 0;}";
echo "th,td{padding:10px;border:1px solid #ddd;text-align:right;}";
echo "th{background:#007bff;color:white;}";
echo "</style></head><body>";

echo "<h1>🔍 فحص اتصال قاعدة البيانات</h1>";

// Load config
require_once 'includes/config.php';

// Test 1: Show config values
echo "<div class='box info'>";
echo "<h2>📋 إعدادات قاعدة البيانات من config.php:</h2>";
echo "<table>";
echo "<tr><th>الإعداد</th><th>القيمة</th></tr>";
echo "<tr><td>DB_HOST</td><td><strong>" . DB_HOST . "</strong></td></tr>";
echo "<tr><td>DB_NAME</td><td><strong style='color:#dc3545;font-size:18px;'>" . DB_NAME . "</strong></td></tr>";
echo "<tr><td>DB_USER</td><td><strong>" . DB_USER . "</strong></td></tr>";
echo "<tr><td>DB_PASS</td><td>" . (DB_PASS ? str_repeat('*', strlen(DB_PASS)) : 'فارغ') . "</td></tr>";
echo "</table>";
echo "</div>";

// Test 2: Check actual connection
try {
    $query = $conn->query("SELECT DATABASE() as current_db");
    $result = $query->fetch();
    $current_database = $result['current_db'];

    if ($current_database === 'u186120816_kero_dentist') {
        echo "<div class='box success'>";
        echo "<h2>✅ الاتصال صحيح!</h2>";
        echo "<p>متصل بقاعدة البيانات: <strong style='font-size:20px;color:green;'>$current_database</strong></p>";
        echo "</div>";
    } else {
        echo "<div class='box error'>";
        echo "<h2>❌ خطأ: متصل بقاعدة بيانات خاطئة!</h2>";
        echo "<p>القاعدة الحالية: <strong style='font-size:20px;color:red;'>$current_database</strong></p>";
        echo "<p>المفروض: <strong style='font-size:20px;color:green;'>u186120816_kero_dentist</strong></p>";
        echo "<p><strong>الحل:</strong> راجع ملف includes/config.php وتأكد من:</p>";
        echo "<pre>define('DB_NAME', 'u186120816_kero_dentist');</pre>";
        echo "</div>";
    }
} catch(PDOException $e) {
    echo "<div class='box error'>";
    echo "<h2>❌ فشل الاتصال</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

// Test 3: Check admin_users table
try {
    // Check if table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'admin_users'");
    if ($stmt->rowCount() > 0) {
        echo "<div class='box success'>";
        echo "<h2>✅ جدول admin_users موجود</h2>";

        // Count users
        $stmt = $conn->query("SELECT COUNT(*) as count FROM admin_users");
        $count = $stmt->fetch()['count'];
        echo "<p>عدد المستخدمين: <strong>$count</strong></p>";

        // Show users
        if ($count > 0) {
            $stmt = $conn->query("SELECT id, username, email, created_at FROM admin_users ORDER BY id ASC");
            $users = $stmt->fetchAll();

            echo "<table>";
            echo "<tr><th>#</th><th>اسم المستخدم</th><th>البريد</th><th>تاريخ الإنشاء</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>{$user['id']}</td>";
                echo "<td><strong>{$user['username']}</strong></td>";
                echo "<td>{$user['email']}</td>";
                echo "<td>{$user['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color:orange;'>⚠️ لا يوجد مستخدمين في هذه القاعدة</p>";
        }
        echo "</div>";
    } else {
        echo "<div class='box error'>";
        echo "<h2>❌ جدول admin_users غير موجود!</h2>";
        echo "<p>يجب استيراد ملف complete_database.sql</p>";
        echo "</div>";
    }
} catch(PDOException $e) {
    echo "<div class='box error'>";
    echo "<h2>❌ خطأ في فحص جدول admin_users</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

// Test 4: Check sliders table
try {
    $stmt = $conn->query("SHOW TABLES LIKE 'sliders'");
    if ($stmt->rowCount() > 0) {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM sliders");
        $count = $stmt->fetch()['count'];

        if ($count > 0) {
            echo "<div class='box success'>";
            echo "<h2>✅ جدول sliders موجود</h2>";
            echo "<p>عدد السلايدرز: <strong>$count</strong></p>";
            echo "</div>";
        } else {
            echo "<div class='box error'>";
            echo "<h2>⚠️ جدول sliders فارغ!</h2>";
            echo "<p>لا توجد سلايدرز في قاعدة البيانات</p>";
            echo "<p><strong>الحل:</strong></p>";
            echo "<ol>";
            echo "<li>استورد ملف complete_database.sql للحصول على بيانات تجريبية</li>";
            echo "<li>أو أضف سلايدرز من: <a href='admin/sliders.php'>لوحة التحكم</a></li>";
            echo "</ol>";
            echo "</div>";
        }
    } else {
        echo "<div class='box error'>";
        echo "<h2>❌ جدول sliders غير موجود!</h2>";
        echo "<p>يجب استيراد ملف complete_database.sql</p>";
        echo "</div>";
    }
} catch(PDOException $e) {
    echo "<div class='box error'>";
    echo "<h2>❌ خطأ في فحص جدول sliders</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

// Test 5: List all tables
try {
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<div class='box info'>";
    echo "<h2>📊 جميع الجداول في قاعدة البيانات الحالية:</h2>";
    echo "<p>إجمالي الجداول: <strong>" . count($tables) . "</strong></p>";

    if (count($tables) > 0) {
        echo "<div style='display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:10px 0;'>";
        foreach ($tables as $table) {
            echo "<div style='background:#f8f9fa;padding:10px;border-radius:4px;'>📁 $table</div>";
        }
        echo "</div>";
    } else {
        echo "<p style='color:red;'>❌ قاعدة البيانات فارغة تماماً!</p>";
    }
    echo "</div>";
} catch(PDOException $e) {
    echo "<div class='box error'>";
    echo "<h2>❌ خطأ في عرض الجداول</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

// Test 6: Quick fix option
echo "<div class='box info'>";
echo "<h2>🛠️ حلول سريعة:</h2>";
echo "<ol>";
echo "<li><strong>إذا كانت القاعدة خاطئة:</strong> عدّل includes/config.php</li>";
echo "<li><strong>إذا كانت الجداول مفقودة:</strong> استورد complete_database.sql</li>";
echo "<li><strong>إذا كان جدول admin_users فارغ:</strong> افتح <a href='check_and_fix_database.php'>أداة الإصلاح</a></li>";
echo "<li><strong>إذا كان جدول sliders فارغ:</strong> أضف من <a href='admin/sliders.php'>لوحة التحكم</a></li>";
echo "</ol>";
echo "</div>";

echo "<div style='text-align:center;margin:30px 0;'>";
echo "<a href='check_and_fix_database.php' style='background:#007bff;color:white;padding:12px 30px;text-decoration:none;border-radius:5px;display:inline-block;margin:5px;'>أداة الإصلاح الكاملة</a>";
echo "<a href='admin/login.php' style='background:#28a745;color:white;padding:12px 30px;text-decoration:none;border-radius:5px;display:inline-block;margin:5px;'>لوحة التحكم</a>";
echo "<a href='test_connection.php' style='background:#17a2b8;color:white;padding:12px 30px;text-decoration:none;border-radius:5px;display:inline-block;margin:5px;'>تحديث الصفحة</a>";
echo "</div>";

echo "</body></html>";
?>
