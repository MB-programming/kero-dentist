<?php
require_once '../includes/config.php';
requireLogin();

// Handle add/edit slider
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_slider'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title = sanitize($_POST['title']);
    $subtitle = sanitize($_POST['subtitle']);
    $button_text = sanitize($_POST['button_text']);
    $button_link = sanitize($_POST['button_link']);
    $display_order = intval($_POST['display_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadFile($_FILES['image'], 'sliders');
        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        }
    }

    if ($id > 0) {
        // Update existing slider
        if ($image_path) {
            $stmt = $conn->prepare("UPDATE sliders SET title = ?, subtitle = ?, image = ?, button_text = ?, button_link = ?, display_order = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $subtitle, $image_path, $button_text, $button_link, $display_order, $is_active, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE sliders SET title = ?, subtitle = ?, button_text = ?, button_link = ?, display_order = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $subtitle, $button_text, $button_link, $display_order, $is_active, $id]);
        }
        $success_message = 'تم تحديث السلايدر بنجاح';
    } else {
        // Add new slider
        if ($image_path) {
            $stmt = $conn->prepare("INSERT INTO sliders (title, subtitle, image, button_text, button_link, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $subtitle, $image_path, $button_text, $button_link, $display_order, $is_active]);
            $success_message = 'تم إضافة السلايدر بنجاح';
        } else {
            $error_message = 'يرجى اختيار صورة السلايدر';
        }
    }
}

// Handle delete slider
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM sliders WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: sliders.php?deleted=1');
    exit;
}

// Fetch all sliders
$stmt = $conn->query("SELECT * FROM sliders ORDER BY display_order ASC");
$sliders = $stmt->fetchAll();

// Get slider for editing
$edit_slider = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM sliders WHERE id = ?");
    $stmt->execute([$id]);
    $edit_slider = $stmt->fetch();
}

$page_title = 'إدارة السلايدر';
include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-images"></i> إدارة السلايدر</h1>
    <p>إضافة وتعديل سلايدرات الصفحة الرئيسية</p>
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

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    تم حذف السلايدر بنجاح
</div>
<?php endif; ?>

<!-- Add/Edit Form -->
<div class="card">
    <div class="card-header">
        <h2><?php echo $edit_slider ? 'تعديل السلايدر' : 'إضافة سلايدر جديد'; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($edit_slider): ?>
            <input type="hidden" name="id" value="<?php echo $edit_slider['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label for="title">عنوان السلايدر *</label>
                    <input type="text" id="title" name="title" required
                           value="<?php echo $edit_slider ? htmlspecialchars($edit_slider['title']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="display_order">ترتيب العرض</label>
                    <input type="number" id="display_order" name="display_order"
                           value="<?php echo $edit_slider ? $edit_slider['display_order'] : 0; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="subtitle">العنوان الفرعي</label>
                <textarea id="subtitle" name="subtitle" rows="2"><?php echo $edit_slider ? htmlspecialchars($edit_slider['subtitle']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">صورة السلايدر <?php echo !$edit_slider ? '*' : '(اتركها فارغة للإبقاء على الصورة الحالية)'; ?></label>
                <input type="file" id="image" name="image" accept="image/*" <?php echo !$edit_slider ? 'required' : ''; ?>>
                <?php if ($edit_slider && $edit_slider['image']): ?>
                <div style="margin-top: 10px;">
                    <img src="<?php echo UPLOAD_URL . htmlspecialchars($edit_slider['image']); ?>"
                         alt="Current Slider" style="max-width: 300px; border-radius: 8px; box-shadow: var(--shadow-md);">
                </div>
                <?php endif; ?>
                <small style="color: var(--text-light); display: block; margin-top: 5px;">الحجم الموصى به: 1920x800 بكسل</small>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="button_text">نص الزر</label>
                    <input type="text" id="button_text" name="button_text"
                           value="<?php echo $edit_slider ? htmlspecialchars($edit_slider['button_text']) : ''; ?>"
                           placeholder="مثال: احجز الآن">
                </div>

                <div class="form-group">
                    <label for="button_link">رابط الزر</label>
                    <input type="text" id="button_link" name="button_link"
                           value="<?php echo $edit_slider ? htmlspecialchars($edit_slider['button_link']) : ''; ?>"
                           placeholder="مثال: #booking">
                </div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1"
                           <?php echo (!$edit_slider || $edit_slider['is_active']) ? 'checked' : ''; ?>>
                    تفعيل السلايدر
                </label>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" name="save_slider" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $edit_slider ? 'تحديث' : 'إضافة'; ?>
                </button>
                <?php if ($edit_slider): ?>
                <a href="sliders.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Sliders List -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h2>السلايدرات الحالية</h2>
    </div>
    <div class="card-body">
        <?php if (count($sliders) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>الصورة</th>
                        <th>العنوان</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sliders as $slider): ?>
                    <tr>
                        <td>
                            <img src="<?php echo UPLOAD_URL . htmlspecialchars($slider['image']); ?>"
                                 alt="<?php echo htmlspecialchars($slider['title']); ?>"
                                 style="width: 120px; height: 60px; object-fit: cover; border-radius: 5px;">
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($slider['title']); ?></strong>
                            <?php if ($slider['subtitle']): ?>
                            <br><small style="color: var(--text-light);"><?php echo htmlspecialchars($slider['subtitle']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $slider['display_order']; ?></td>
                        <td>
                            <?php if ($slider['is_active']): ?>
                            <span class="badge badge-success">نشط</span>
                            <?php else: ?>
                            <span class="badge badge-secondary">غير نشط</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="sliders.php?edit=<?php echo $slider['id']; ?>"
                                   class="btn btn-sm btn-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteSlider(<?php echo $slider['id']; ?>)"
                                        class="btn btn-sm btn-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <h3>لا توجد سلايدرات</h3>
            <p>قم بإضافة سلايدر جديد من النموذج أعلاه</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteSlider(id) {
    if (confirm('هل أنت متأكد من حذف هذا السلايدر؟')) {
        window.location.href = 'sliders.php?delete=' + id;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
