<?php
require_once '../includes/config.php';
requireLogin();

// Handle save template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_template'])) {
    $id = intval($_POST['id']);
    $subject = sanitize($_POST['subject']);
    $body = $_POST['body']; // Don't sanitize HTML content
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    try {
        $stmt = $conn->prepare("UPDATE email_templates SET subject = ?, body = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$subject, $body, $is_active, $id]);
        $success_message = 'تم تحديث القالب بنجاح';
    } catch(PDOException $e) {
        $error_message = 'حدث خطأ أثناء حفظ القالب';
    }
}

// Fetch all templates
$stmt = $conn->query("SELECT * FROM email_templates ORDER BY template_name ASC");
$templates = $stmt->fetchAll();

// Get template for editing
$edit_template = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM email_templates WHERE id = ?");
    $stmt->execute([$id]);
    $edit_template = $stmt->fetch();
}

// Template name translations
$template_names = [
    'booking_pending' => 'حجز قيد الانتظار',
    'booking_approved' => 'حجز مؤكد',
    'booking_rejected' => 'حجز مرفوض',
    'booking_invoice' => 'فاتورة الحجز'
];

$page_title = 'قوالب البريد الإلكتروني';
include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-envelope"></i> قوالب البريد الإلكتروني</h1>
    <p>إدارة وتحرير قوالب رسائل البريد الإلكتروني</p>
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

<?php if ($edit_template): ?>
<!-- Edit Template Form -->
<div class="card">
    <div class="card-header">
        <h2>تعديل قالب: <?php echo htmlspecialchars($template_names[$edit_template['template_name']] ?? $edit_template['template_name']); ?></h2>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>المتغيرات المتاحة:</strong>
            <ul style="margin: 10px 0 0 20px; line-height: 1.8;">
                <li><code>{client_name}</code> - اسم العميل</li>
                <li><code>{client_email}</code> - بريد العميل</li>
                <li><code>{client_phone}</code> - هاتف العميل</li>
                <li><code>{client_whatsapp}</code> - واتساب العميل</li>
                <li><code>{client_address}</code> - عنوان العميل</li>
                <li><code>{booking_date}</code> - تاريخ الحجز</li>
                <li><code>{booking_day}</code> - يوم الحجز</li>
                <li><code>{package_name}</code> - اسم الباقة</li>
                <li><code>{package_price}</code> - سعر الباقة</li>
                <li><code>{site_name}</code> - اسم الموقع</li>
                <li><code>{site_email}</code> - بريد الموقع</li>
                <li><code>{site_phone}</code> - هاتف الموقع</li>
                <li><code>{site_address}</code> - عنوان الموقع</li>
            </ul>
        </div>

        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_template['id']; ?>">

            <div class="form-group">
                <label for="subject">موضوع الرسالة *</label>
                <input type="text" id="subject" name="subject" required
                       value="<?php echo htmlspecialchars($edit_template['subject']); ?>"
                       placeholder="مثال: تأكيد حجزك في {site_name}">
            </div>

            <div class="form-group">
                <label for="body">محتوى الرسالة (HTML) *</label>
                <textarea id="body" name="body" rows="20" style="font-family: monospace; direction: ltr; text-align: left;"><?php echo htmlspecialchars($edit_template['body']); ?></textarea>
                <small style="color: var(--text-light); display: block; margin-top: 5px;">
                    يمكنك استخدام HTML لتنسيق الرسالة
                </small>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1"
                           <?php echo $edit_template['is_active'] ? 'checked' : ''; ?>>
                    تفعيل القالب
                </label>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" name="save_template" class="btn btn-primary">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
                <a href="email-templates.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<!-- Templates List -->
<div class="card">
    <div class="card-header">
        <h2>القوالب المتاحة</h2>
    </div>
    <div class="card-body">
        <?php if (count($templates) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>اسم القالب</th>
                        <th>الموضوع</th>
                        <th>الحالة</th>
                        <th>آخر تحديث</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $template): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($template_names[$template['template_name']] ?? $template['template_name']); ?></strong>
                            <br><small style="color: var(--text-light); font-family: monospace;"><?php echo htmlspecialchars($template['template_name']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($template['subject']); ?></td>
                        <td>
                            <?php if ($template['is_active']): ?>
                            <span class="badge badge-success">نشط</span>
                            <?php else: ?>
                            <span class="badge badge-secondary">غير نشط</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small style="color: var(--text-light);">
                                <?php echo formatDate($template['updated_at'], 'd/m/Y H:i'); ?>
                            </small>
                        </td>
                        <td>
                            <a href="email-templates.php?edit=<?php echo $template['id']; ?>"
                               class="btn btn-sm btn-primary" title="تعديل">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-envelope"></i>
            <h3>لا توجد قوالب</h3>
            <p>لم يتم العثور على قوالب بريد إلكتروني</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<style>
code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
    color: #e83e8c;
    font-size: 0.9em;
}

.alert ul {
    list-style-type: none;
}

.alert ul li {
    padding: 3px 0;
}
</style>

<script>
// Add TinyMCE for better HTML editing (optional enhancement)
// You can add TinyMCE initialization here if needed
</script>

<?php include 'includes/footer.php'; ?>
