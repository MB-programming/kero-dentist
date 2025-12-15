<?php
$page_title = 'الإعدادات';
include 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Handle logo upload
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadFile($_FILES['site_logo'], 'logo');
            if ($upload_result['success']) {
                updateSetting('site_logo', $upload_result['path']);
            }
        }

        // Handle doctor image upload
        if (isset($_FILES['doctor_image']) && $_FILES['doctor_image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadFile($_FILES['doctor_image'], 'doctor');
            if ($upload_result['success']) {
                updateSetting('doctor_image', $upload_result['path']);
            }
        }

        // Handle about image upload
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadFile($_FILES['about_image'], 'about');
            if ($upload_result['success']) {
                updateSetting('about_image', $upload_result['path']);
            }
        }

        // Handle other settings
        foreach ($_POST as $key => $value) {
            if ($key !== 'submit') {
                updateSetting($key, sanitize($value));
            }
        }
        $success_message = 'تم حفظ الإعدادات بنجاح';
    } catch(Exception $e) {
        $error_message = 'حدث خطأ أثناء حفظ الإعدادات';
    }
}
?>

<div class="page-header">
    <h1>الإعدادات</h1>
    <p>إدارة إعدادات الموقع</p>
</div>

<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <?php echo htmlspecialchars($success_message); ?>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <?php echo htmlspecialchars($error_message); ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <!-- General Settings -->
    <div class="card">
        <div class="card-header">
            <h2>الإعدادات العامة</h2>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>اسم الموقع</label>
                    <input type="text" name="site_name" value="<?php echo htmlspecialchars(getSetting('site_name')); ?>">
                </div>
                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="site_email" value="<?php echo htmlspecialchars(getSetting('site_email')); ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>الهاتف</label>
                    <input type="text" name="site_phone" value="<?php echo htmlspecialchars(getSetting('site_phone')); ?>">
                </div>
                <div class="form-group">
                    <label>العنوان</label>
                    <input type="text" name="site_address" value="<?php echo htmlspecialchars(getSetting('site_address')); ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Logo Settings -->
    <div class="card">
        <div class="card-header">
            <h2>شعار الموقع</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>رفع شعار جديد</label>
                <input type="file" name="site_logo" accept="image/*">
                <small style="color: var(--text-light); display: block; margin-top: 5px;">
                    الحجم الموصى به: 200x60 بكسل (شفاف PNG)
                </small>
                <?php
                $current_logo = getSetting('site_logo');
                if ($current_logo): ?>
                    <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                        <p style="margin-bottom: 10px; color: var(--text-light);">الشعار الحالي:</p>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($current_logo); ?>"
                             alt="Current Logo" style="max-width: 200px; max-height: 100px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Doctor Info -->
    <div class="card">
        <div class="card-header">
            <h2>معلومات الطبيب</h2>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>اسم الطبيب</label>
                    <input type="text" name="doctor_name" value="<?php echo htmlspecialchars(getSetting('doctor_name')); ?>">
                </div>
                <div class="form-group">
                    <label>التخصص</label>
                    <input type="text" name="doctor_title" value="<?php echo htmlspecialchars(getSetting('doctor_title')); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>نبذة عن الطبيب</label>
                <textarea name="doctor_bio" rows="4"><?php echo htmlspecialchars(getSetting('doctor_bio')); ?></textarea>
            </div>

            <div class="form-group">
                <label>صورة الطبيب</label>
                <input type="file" name="doctor_image" accept="image/*">
                <small style="color: var(--text-light); display: block; margin-top: 5px;">
                    الحجم الموصى به: 400x400 بكسل
                </small>
                <?php
                $doctor_image = getSetting('doctor_image');
                if ($doctor_image): ?>
                    <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                        <p style="margin-bottom: 10px; color: var(--text-light);">الصورة الحالية:</p>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($doctor_image); ?>"
                             alt="Doctor Image" style="max-width: 200px; max-height: 200px; border-radius: 50%;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>عنوان الصفحة الرئيسية</label>
                    <input type="text" name="hero_title" value="<?php echo htmlspecialchars(getSetting('hero_title')); ?>">
                </div>
                <div class="form-group">
                    <label>العنوان الفرعي</label>
                    <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars(getSetting('hero_subtitle')); ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Doctor Statistics & Achievements -->
    <div class="card">
        <div class="card-header">
            <h2>إحصائيات ومعلومات إضافية عن الدكتور</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                هذه المعلومات تظهر في صفحة "عن الدكتور"
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>سنوات الخبرة</label>
                    <input type="number" name="doctor_years_experience"
                           value="<?php echo htmlspecialchars(getSetting('doctor_years_experience', '15')); ?>"
                           placeholder="15">
                </div>
                <div class="form-group">
                    <label>عدد المرضى</label>
                    <input type="number" name="doctor_total_patients"
                           value="<?php echo htmlspecialchars(getSetting('doctor_total_patients', '5000')); ?>"
                           placeholder="5000">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>عدد الحالات الناجحة</label>
                    <input type="number" name="doctor_success_cases"
                           value="<?php echo htmlspecialchars(getSetting('doctor_success_cases', '3500')); ?>"
                           placeholder="3500">
                </div>
                <div class="form-group">
                    <label>نسبة رضا العملاء (%)</label>
                    <input type="number" name="doctor_satisfaction_rate"
                           value="<?php echo htmlspecialchars(getSetting('doctor_satisfaction_rate', '98')); ?>"
                           placeholder="98">
                </div>
            </div>

            <div class="form-group">
                <label>نبذة كاملة عن الدكتور (للصفحة المخصصة)</label>
                <textarea name="doctor_about_full" rows="6" class="tinymce-editor"><?php echo htmlspecialchars(getSetting('doctor_about_full', 'طبيب أسنان متخصص مع خبرة واسعة في جميع مجالات طب وجراحة الفم والأسنان. حاصل على شهادات دولية ومعتمد من أفضل الجامعات العالمية. يؤمن بأن الابتسامة الجميلة هي مفتاح الثقة بالنفس.')); ?></textarea>
                <small style="color: var(--text-light); display: block; margin-top: 5px;">
                    هذا النص يظهر في صفحة "عن الدكتور" المخصصة
                </small>
            </div>
        </div>
    </div>

    <!-- WhatsApp API Settings -->
    <div class="card">
        <div class="card-header">
            <h2>إعدادات WhatsApp API</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                لإعداد WhatsApp API، راجع ملف <strong>WHATSAPP_API_SETUP.md</strong>
            </div>

            <div class="form-group">
                <label>WhatsApp API Token</label>
                <input type="text" name="whatsapp_api_token" value="<?php echo htmlspecialchars(getSetting('whatsapp_api_token')); ?>" placeholder="أدخل Token الخاص بك">
            </div>

            <div class="form-group">
                <label>WhatsApp API URL</label>
                <input type="text" name="whatsapp_api_url" value="<?php echo htmlspecialchars(getSetting('whatsapp_api_url')); ?>" placeholder="مثال: https://api.whatsapp.com/send">
            </div>

            <p style="color: var(--text-light); font-size: 0.9rem;">
                <strong>ملاحظة:</strong> يمكنك استخدام خدمات مثل:
                <a href="https://www.twilio.com/whatsapp" target="_blank">Twilio</a> أو
                <a href="https://developers.facebook.com/docs/whatsapp" target="_blank">WhatsApp Business API</a>
            </p>
        </div>
    </div>

    <!-- Gmail API Settings -->
    <div class="card">
        <div class="card-header">
            <h2>إعدادات Gmail API للتقييمات</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                لإعداد Gmail API، راجع ملف <strong>GMAIL_API_SETUP.md</strong>
            </div>

            <div class="form-group">
                <label>البريد الإلكتروني للتقييمات</label>
                <input type="email" name="reviews_email" value="<?php echo htmlspecialchars(getSetting('reviews_email')); ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Gmail API Client ID</label>
                    <input type="text" name="gmail_api_client_id" value="<?php echo htmlspecialchars(getSetting('gmail_api_client_id')); ?>">
                </div>
                <div class="form-group">
                    <label>Gmail API Client Secret</label>
                    <input type="text" name="gmail_api_client_secret" value="<?php echo htmlspecialchars(getSetting('gmail_api_client_secret')); ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- SMTP Email Settings -->
    <div class="card">
        <div class="card-header">
            <h2>إعدادات البريد الإلكتروني (SMTP)</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                سيتم استخدام هذه الإعدادات لإرسال إشعارات البريد الإلكتروني للحجوزات والتأكيدات
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="enable_email_notifications" value="1"
                           <?php echo getSetting('enable_email_notifications') ? 'checked' : ''; ?>>
                    تفعيل إشعارات البريد الإلكتروني
                </label>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>SMTP Host</label>
                    <input type="text" name="smtp_host"
                           value="<?php echo htmlspecialchars(getSetting('smtp_host')); ?>"
                           placeholder="smtp.gmail.com">
                    <small style="color: var(--text-light); display: block; margin-top: 5px;">
                        مثال: smtp.gmail.com أو smtp.hostinger.com
                    </small>
                </div>
                <div class="form-group">
                    <label>SMTP Port</label>
                    <input type="number" name="smtp_port"
                           value="<?php echo htmlspecialchars(getSetting('smtp_port', '587')); ?>"
                           placeholder="587">
                    <small style="color: var(--text-light); display: block; margin-top: 5px;">
                        587 (TLS) أو 465 (SSL)
                    </small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>SMTP Username</label>
                    <input type="text" name="smtp_username"
                           value="<?php echo htmlspecialchars(getSetting('smtp_username')); ?>"
                           placeholder="your-email@gmail.com">
                </div>
                <div class="form-group">
                    <label>SMTP Password</label>
                    <input type="password" name="smtp_password"
                           value="<?php echo htmlspecialchars(getSetting('smtp_password')); ?>"
                           placeholder="كلمة مرور التطبيق">
                    <small style="color: var(--text-light); display: block; margin-top: 5px;">
                        للـ Gmail استخدم كلمة مرور التطبيق (App Password)
                    </small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>البريد المرسِل (From Email)</label>
                    <input type="email" name="smtp_from_email"
                           value="<?php echo htmlspecialchars(getSetting('smtp_from_email')); ?>"
                           placeholder="noreply@clinic.com">
                </div>
                <div class="form-group">
                    <label>اسم المرسِل (From Name)</label>
                    <input type="text" name="smtp_from_name"
                           value="<?php echo htmlspecialchars(getSetting('smtp_from_name')); ?>"
                           placeholder="عيادة الدكتور">
                </div>
            </div>

            <div class="form-group">
                <label>نوع التشفير (Encryption)</label>
                <select name="smtp_encryption">
                    <option value="tls" <?php echo getSetting('smtp_encryption', 'tls') === 'tls' ? 'selected' : ''; ?>>TLS (موصى به)</option>
                    <option value="ssl" <?php echo getSetting('smtp_encryption') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                    <option value="" <?php echo getSetting('smtp_encryption') === '' ? 'selected' : ''; ?>>بدون تشفير</option>
                </select>
            </div>
        </div>
    </div>

    <!-- External APIs -->
    <div class="card">
        <div class="card-header">
            <h2>مفاتيح APIs الخارجية</h2>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                أدخل مفاتيح APIs الخارجية للخدمات التي تستخدمها
            </div>

            <div class="form-group">
                <label>TinyMCE API Key</label>
                <input type="text" name="tinymce_api_key"
                       value="<?php echo htmlspecialchars(getSetting('tinymce_api_key')); ?>"
                       placeholder="أدخل مفتاح TinyMCE API">
                <small style="color: var(--text-light); display: block; margin-top: 5px;">
                    احصل على المفتاح المجاني من: <a href="https://www.tiny.cloud/auth/signup/" target="_blank">tiny.cloud</a>
                </small>
            </div>

            <div class="form-group">
                <label>Google Maps API Key</label>
                <input type="text" name="google_maps_api_key"
                       value="<?php echo htmlspecialchars(getSetting('google_maps_api_key')); ?>"
                       placeholder="أدخل مفتاح Google Maps API">
                <small style="color: var(--text-light); display: block; margin-top: 5px;">
                    احصل على المفتاح من: <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>
                </small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Google Analytics ID</label>
                    <input type="text" name="google_analytics_id"
                           value="<?php echo htmlspecialchars(getSetting('google_analytics_id')); ?>"
                           placeholder="G-XXXXXXXXXX أو UA-XXXXXXXXX">
                </div>
                <div class="form-group">
                    <label>Facebook Pixel ID</label>
                    <input type="text" name="facebook_pixel_id"
                           value="<?php echo htmlspecialchars(getSetting('facebook_pixel_id')); ?>"
                           placeholder="أدخل Facebook Pixel ID">
                </div>
            </div>
        </div>
    </div>

    <!-- About Us Page -->
    <div class="card">
        <div class="card-header">
            <h2>صفحة من نحن</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>عنوان الصفحة</label>
                <input type="text" name="about_title"
                       value="<?php echo htmlspecialchars(getSetting('about_title', 'من نحن')); ?>"
                       placeholder="من نحن">
            </div>

            <div class="form-group">
                <label>محتوى الصفحة</label>
                <textarea name="about_content" rows="8" class="tinymce-editor"><?php echo htmlspecialchars(getSetting('about_content')); ?></textarea>
                <small style="color: var(--text-light); display: block; margin-top: 5px;">
                    يمكنك استخدام HTML لتنسيق النص
                </small>
            </div>

            <div class="form-group">
                <label>صورة الصفحة</label>
                <input type="file" name="about_image" accept="image/*">
                <small style="color: var(--text-light); display: block; margin-top: 5px;">
                    الحجم الموصى به: 1200x600 بكسل
                </small>
                <?php
                $about_image = getSetting('about_image');
                if ($about_image): ?>
                    <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                        <p style="margin-bottom: 10px; color: var(--text-light);">الصورة الحالية:</p>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($about_image); ?>"
                             alt="About Image" style="max-width: 100%; max-height: 300px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contact Us Page -->
    <div class="card">
        <div class="card-header">
            <h2>صفحة تواصل معنا</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label>عنوان الصفحة</label>
                <input type="text" name="contact_title"
                       value="<?php echo htmlspecialchars(getSetting('contact_title', 'تواصل معنا')); ?>"
                       placeholder="تواصل معنا">
            </div>

            <div class="form-group">
                <label>وصف قصير</label>
                <textarea name="contact_description" rows="3"><?php echo htmlspecialchars(getSetting('contact_description', 'نسعد بتواصلكم معنا')); ?></textarea>
            </div>

            <div class="form-group">
                <label>كود Google Maps (Embed)</label>
                <textarea name="contact_map_embed" rows="4" placeholder='<iframe src="https://www.google.com/maps/embed?..." ...></iframe>'><?php echo htmlspecialchars(getSetting('contact_map_embed')); ?></textarea>
                <small style="color: var(--text-light); display: block; margin-top: 5px;">
                    انسخ كود الـ iframe من Google Maps لإظهار الموقع على الخريطة
                </small>
            </div>
        </div>
    </div>

    <button type="submit" name="submit" class="btn btn-primary btn-block" style="max-width: 300px; margin: 0 auto;">
        <i class="fas fa-save"></i> حفظ الإعدادات
    </button>
</form>

<!-- TinyMCE Integration -->
<script src="https://cdn.tiny.cloud/1/<?php echo getSetting('tinymce_api_key', 'no-api-key-set'); ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiKey = '<?php echo getSetting('tinymce_api_key'); ?>';

    if (apiKey) {
        tinymce.init({
            selector: 'textarea.tinymce-editor',
            language: 'ar',
            directionality: 'rtl',
            height: 300,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family:Arial,sans-serif; font-size:14px; direction:rtl; text-align:right; }'
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
