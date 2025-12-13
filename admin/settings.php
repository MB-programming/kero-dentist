<?php
$page_title = 'الإعدادات';
include 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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

<form method="POST">
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

    <button type="submit" name="submit" class="btn btn-primary btn-block" style="max-width: 300px; margin: 0 auto;">
        <i class="fas fa-save"></i> حفظ الإعدادات
    </button>
</form>

<?php include 'includes/footer.php'; ?>
