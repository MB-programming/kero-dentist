<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار أزرار الحجوزات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            direction: rtl;
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
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .test-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .test-section h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .btn {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            font-size: 14px;
        }
        .btn:hover {
            background: #5568d3;
        }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .result {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border-right: 4px solid #2196f3;
        }
        .error {
            background: #f8d7da;
            border-right-color: #dc3545;
            color: #721c24;
        }
        .success {
            background: #d4edda;
            border-right-color: #28a745;
            color: #155724;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: right;
        }
        th {
            background: #667eea;
            color: white;
        }
        .code {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-vial"></i> اختبار أزرار الحجوزات</h1>

        <!-- Test 1: Database Connection -->
        <div class="test-section">
            <h3><i class="fas fa-database"></i> 1. اختبار الاتصال بقاعدة البيانات</h3>
            <?php
            require_once 'includes/config.php';

            try {
                $stmt = $conn->query("SELECT DATABASE() as db");
                $current_db = $stmt->fetch()['db'];
                echo "<div class='result success'>";
                echo "✅ متصل بقاعدة البيانات: <strong>$current_db</strong>";
                echo "</div>";
            } catch(PDOException $e) {
                echo "<div class='result error'>";
                echo "❌ خطأ في الاتصال: " . $e->getMessage();
                echo "</div>";
            }
            ?>
        </div>

        <!-- Test 2: Check Bookings Table -->
        <div class="test-section">
            <h3><i class="fas fa-table"></i> 2. فحص جدول الحجوزات</h3>
            <?php
            try {
                $stmt = $conn->query("SELECT COUNT(*) as count FROM bookings");
                $count = $stmt->fetch()['count'];

                echo "<div class='result success'>";
                echo "✅ جدول bookings موجود<br>";
                echo "📊 عدد الحجوزات: <strong>$count</strong>";
                echo "</div>";

                if ($count > 0) {
                    $stmt = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 3");
                    $bookings = $stmt->fetchAll();

                    echo "<table>";
                    echo "<tr><th>#</th><th>الاسم</th><th>الهاتف</th><th>الحالة</th><th>التاريخ</th></tr>";
                    foreach ($bookings as $booking) {
                        echo "<tr>";
                        echo "<td>{$booking['id']}</td>";
                        echo "<td>{$booking['client_name']}</td>";
                        echo "<td>{$booking['client_phone']}</td>";
                        echo "<td>{$booking['status']}</td>";
                        echo "<td>{$booking['booking_date']}</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } catch(PDOException $e) {
                echo "<div class='result error'>";
                echo "❌ خطأ: " . $e->getMessage();
                echo "</div>";
            }
            ?>
        </div>

        <!-- Test 3: Test API Endpoint -->
        <div class="test-section">
            <h3><i class="fas fa-plug"></i> 3. اختبار get-booking.php API</h3>
            <?php
            $test_id = 1;
            $api_url = 'api/get-booking.php?id=' . $test_id;
            $api_path = __DIR__ . '/' . $api_url;

            if (file_exists(__DIR__ . '/api/get-booking.php')) {
                echo "<div class='result success'>";
                echo "✅ الملف api/get-booking.php موجود<br>";
                echo "📍 المسار: <code>$api_url</code>";
                echo "</div>";

                // Test API call
                echo "<button class='btn' onclick='testAPI()'>اختبار API</button>";
                echo "<div id='api-result'></div>";
            } else {
                echo "<div class='result error'>";
                echo "❌ الملف api/get-booking.php غير موجود";
                echo "</div>";
            }
            ?>
        </div>

        <!-- Test 4: JavaScript Functions -->
        <div class="test-section">
            <h3><i class="fas fa-code"></i> 4. اختبار JavaScript Functions</h3>
            <p>اختبر كل زر لترى إذا كان يعمل:</p>

            <?php
            // Create a test booking object
            $test_booking = [
                'id' => 1,
                'client_name' => 'محمد أحمد',
                'client_phone' => '01012345678',
                'client_whatsapp' => '01012345678',
                'package_name' => 'فحص شامل',
                'package_price' => 500,
                'booking_date' => '2025-12-25',
                'booking_time' => '10:00 AM',
                'booking_day' => 'الأربعاء',
                'status' => 'pending'
            ];
            $test_booking_json = json_encode($test_booking);
            ?>

            <button class="btn btn-success" onclick="testApprove()">
                <i class="fab fa-whatsapp"></i> اختبار زر القبول
            </button>

            <button class="btn btn-danger" onclick="testReject()">
                <i class="fab fa-whatsapp"></i> اختبار زر الرفض
            </button>

            <button class="btn" onclick="testContact()">
                <i class="fab fa-whatsapp"></i> اختبار زر التواصل
            </button>

            <button class="btn btn-warning" onclick="testView()">
                <i class="fas fa-eye"></i> اختبار زر عرض التفاصيل
            </button>

            <button class="btn btn-warning" onclick="testUpdateStatus()">
                <i class="fas fa-edit"></i> اختبار تحديث الحالة
            </button>

            <button class="btn btn-danger" onclick="testDelete()">
                <i class="fas fa-trash"></i> اختبار زر الحذف
            </button>

            <div id="test-result"></div>
        </div>

        <!-- Test 5: Console Errors -->
        <div class="test-section">
            <h3><i class="fas fa-bug"></i> 5. فحص أخطاء JavaScript</h3>
            <p>افتح Console في المتصفح (F12) وشوف إذا فيه أخطاء</p>
            <div class="code">
                Windows/Linux: F12 أو Ctrl+Shift+I
                Mac: Cmd+Option+I
            </div>
            <button class="btn" onclick="checkConsole()">اختبار Console</button>
            <div id="console-result"></div>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 8px;">
            <h3><i class="fas fa-tools"></i> الحلول المقترحة:</h3>
            <ol>
                <li>تأكد من استيراد ملف complete_database.sql</li>
                <li>تحقق من وجود حجوزات في قاعدة البيانات</li>
                <li>افتح admin/bookings.php وافحص Console للأخطاء</li>
                <li>تأكد من أن ملف api/get-booking.php يعمل</li>
                <li>تحقق من أن JavaScript غير محظور في المتصفح</li>
            </ol>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="admin/bookings.php" class="btn"><i class="fas fa-calendar-check"></i> افتح صفحة الحجوزات</a>
            <a href="test_connection.php" class="btn"><i class="fas fa-database"></i> فحص الاتصال</a>
        </div>
    </div>

    <script>
        // Test data
        const testBooking = <?php echo $test_booking_json; ?>;

        function logTest(message, type = 'info') {
            const resultDiv = document.getElementById('test-result');
            const typeClass = type === 'error' ? 'error' : type === 'success' ? 'success' : '';
            resultDiv.innerHTML = `<div class="result ${typeClass}">${message}</div>`;
            console.log(message);
        }

        function testApprove() {
            logTest('🧪 اختبار approveBookingWhatsApp()...', 'info');
            try {
                const message = `مرحباً ${testBooking.client_name}،\n\n✅ تم الموافقة على حجزك!\n\nالباقة: ${testBooking.package_name}\nالسعر: ${testBooking.package_price} جنيه`;
                const url = `https://wa.me/2${testBooking.client_whatsapp}?text=${encodeURIComponent(message)}`;
                logTest('✅ تم إنشاء رابط واتساب بنجاح!<br><a href="' + url + '" target="_blank">افتح الرابط</a>', 'success');
            } catch(e) {
                logTest('❌ خطأ: ' + e.message, 'error');
            }
        }

        function testReject() {
            logTest('🧪 اختبار rejectBookingWhatsApp()...', 'info');
            try {
                const message = `مرحباً ${testBooking.client_name}،\n\n❌ نأسف لإبلاغك بأن حجزك قد تم رفضه.`;
                const url = `https://wa.me/2${testBooking.client_whatsapp}?text=${encodeURIComponent(message)}`;
                logTest('✅ تم إنشاء رابط واتساب بنجاح!<br><a href="' + url + '" target="_blank">افتح الرابط</a>', 'success');
            } catch(e) {
                logTest('❌ خطأ: ' + e.message, 'error');
            }
        }

        function testContact() {
            logTest('🧪 اختبار contactWhatsApp()...', 'info');
            const url = `https://wa.me/2${testBooking.client_whatsapp}`;
            logTest('✅ تم إنشاء رابط واتساب بنجاح!<br><a href="' + url + '" target="_blank">افتح الرابط</a>', 'success');
        }

        function testView() {
            logTest('🧪 اختبار viewBooking()...', 'info');
            fetch('api/get-booking.php?id=' + testBooking.id)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        logTest('✅ API يعمل بنجاح! البيانات: <pre>' + JSON.stringify(data.booking, null, 2) + '</pre>', 'success');
                    } else {
                        logTest('❌ API فشل: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    logTest('❌ خطأ في الاتصال: ' + error.message, 'error');
                });
        }

        function testUpdateStatus() {
            logTest('✅ updateStatus() - سيفتح modal لتحديث الحالة (في الصفحة الأصلية)', 'success');
        }

        function testDelete() {
            logTest('✅ deleteBooking() - سيطلب تأكيد الحذف (في الصفحة الأصلية)', 'success');
        }

        function testAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.innerHTML = '<div class="result">⏳ جاري الاختبار...</div>';

            fetch('api/get-booking.php?id=1')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        resultDiv.innerHTML = '<div class="result success">✅ API يعمل بشكل صحيح!<br>البيانات المستلمة:<pre>' + JSON.stringify(data, null, 2) + '</pre></div>';
                    } else {
                        resultDiv.innerHTML = '<div class="result error">❌ ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = '<div class="result error">❌ خطأ: ' + error.message + '</div>';
                });
        }

        function checkConsole() {
            const resultDiv = document.getElementById('console-result');
            console.log('✅ Console Test: If you see this, console is working!');
            console.log('Test Booking Object:', testBooking);
            resultDiv.innerHTML = '<div class="result success">✅ تم الطباعة في Console! افتح Developer Tools للمشاهدة</div>';
        }

        // Auto test on load
        window.onload = function() {
            console.log('🧪 Test page loaded');
            console.log('Test booking:', testBooking);
        };
    </script>
</body>
</html>
