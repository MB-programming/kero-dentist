<?php
$page_title = 'إدارة الدكاترة';
include 'includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Get image to delete
    $stmt = $conn->prepare("SELECT image FROM doctors WHERE id = ?");
    $stmt->execute([$id]);
    $doctor = $stmt->fetch();

    if ($doctor && !empty($doctor['image'])) {
        $image_path = UPLOAD_PATH . $doctor['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success_message = 'تم حذف الدكتور بنجاح';
    } else {
        $error_message = 'فشل حذف الدكتور';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = sanitize($_POST['name']);
        $title = sanitize($_POST['title']);
        $bio = sanitize($_POST['bio']);
        $specialization = sanitize($_POST['specialization']);
        $years_experience = intval($_POST['years_experience']);
        $phone = sanitize($_POST['phone']);
        $email = sanitize($_POST['email']);
        $facebook = sanitize($_POST['facebook']);
        $instagram = sanitize($_POST['instagram']);
        $twitter = sanitize($_POST['twitter']);
        $display_order = intval($_POST['display_order']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadFile($_FILES['image'], 'doctors');
            if ($upload_result['success']) {
                $image = $upload_result['path'];

                // Delete old image if editing
                if ($id > 0) {
                    $stmt = $conn->prepare("SELECT image FROM doctors WHERE id = ?");
                    $stmt->execute([$id]);
                    $old_doctor = $stmt->fetch();
                    if ($old_doctor && !empty($old_doctor['image'])) {
                        $old_image_path = UPLOAD_PATH . $old_doctor['image'];
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                }
            }
        }

        if ($id > 0) {
            // Update existing
            if ($image) {
                $stmt = $conn->prepare("
                    UPDATE doctors SET
                    name = ?, title = ?, bio = ?, image = ?, specialization = ?,
                    years_experience = ?, phone = ?, email = ?, facebook = ?,
                    instagram = ?, twitter = ?, display_order = ?, is_active = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $title, $bio, $image, $specialization,
                               $years_experience, $phone, $email, $facebook,
                               $instagram, $twitter, $display_order, $is_active, $id]);
            } else {
                $stmt = $conn->prepare("
                    UPDATE doctors SET
                    name = ?, title = ?, bio = ?, specialization = ?,
                    years_experience = ?, phone = ?, email = ?, facebook = ?,
                    instagram = ?, twitter = ?, display_order = ?, is_active = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $title, $bio, $specialization,
                               $years_experience, $phone, $email, $facebook,
                               $instagram, $twitter, $display_order, $is_active, $id]);
            }
            $success_message = 'تم تحديث بيانات الدكتور بنجاح';
        } else {
            // Insert new
            $stmt = $conn->prepare("
                INSERT INTO doctors (name, title, bio, image, specialization,
                                   years_experience, phone, email, facebook,
                                   instagram, twitter, display_order, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $title, $bio, $image, $specialization,
                           $years_experience, $phone, $email, $facebook,
                           $instagram, $twitter, $display_order, $is_active]);
            $success_message = 'تم إضافة الدكتور بنجاح';
        }

    } catch(PDOException $e) {
        $error_message = 'حدث خطأ: ' . $e->getMessage();
    }
}

// Get doctor for editing
$edit_doctor = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_doctor = $stmt->fetch();
}

// Fetch all doctors
$stmt = $conn->query("SELECT * FROM doctors ORDER BY display_order ASC, created_at DESC");
$doctors = $stmt->fetchAll();
?>

<style>
.doctor-image-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--fb-blue);
    margin-bottom: 16px;
}
</style>

<!-- Success/Error Messages -->
<?php if (isset($success_message)): ?>
<div class="fb-card" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 20px;">
    <div class="fb-card-body">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
    </div>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="fb-card" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin-bottom: 20px;">
    <div class="fb-card-body">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
    </div>
</div>
<?php endif; ?>

<!-- Add/Edit Form -->
<div class="fb-card">
    <div class="fb-card-header">
        <h2 class="fb-card-title">
            <?php echo $edit_doctor ? 'تعديل بيانات الدكتور' : 'إضافة دكتور جديد'; ?>
        </h2>
    </div>
    <div class="fb-card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($edit_doctor): ?>
            <input type="hidden" name="id" value="<?php echo $edit_doctor['id']; ?>">
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="fb-form-group">
                    <label class="fb-label">اسم الدكتور *</label>
                    <input type="text" name="name" class="fb-input" required
                           value="<?php echo $edit_doctor ? htmlspecialchars($edit_doctor['name']) : ''; ?>">
                </div>

                <div class="fb-form-group">
                    <label class="fb-label">المسمى الوظيفي *</label>
                    <input type="text" name="title" class="fb-input" required
                           value="<?php echo $edit_doctor ? htmlspecialchars($edit_doctor['title']) : ''; ?>">
                </div>

                <div class="fb-form-group">
                    <label class="fb-label">التخصص *</label>
                    <input type="text" name="specialization" class="fb-input" required
                           value="<?php echo $edit_doctor ? htmlspecialchars($edit_doctor['specialization']) : ''; ?>">
                </div>

                <div class="fb-form-group">
                    <label class="fb-label">سنوات الخبرة</label>
                    <input type="number" name="years_experience" class="fb-input" min="0"
                           value="<?php echo $edit_doctor ? $edit_doctor['years_experience'] : '0'; ?>">
                </div>

                <div class="fb-form-group">
                    <label class="fb-label">رقم الهاتف</label>
                    <input type="tel" name="phone" class="fb-input"
                           value="<?php echo $edit_doctor ? htmlspecialchars($edit_doctor['phone']) : ''; ?>">
                </div>

                <div class="fb-form-group">
                    <label class="fb-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="fb-input"
                           value="<?php echo $edit_doctor ? htmlspecialchars($edit_doctor['email']) : ''; ?>">
                </div>

                <div class="fb-form-group">
                    <label class="fb-label">Facebook</label>
                    <input type="url" name="facebook" class="fb-input"
                           value="<?php echo $edit_doctor ? htmlspecialchars($edit_doctor['facebook']) : ''; ?>">
                </div>

                <div class="fb-form-group">
                    <label class="fb-label">Instagram</label>
                    <input type="url" name="instagram" class="fb-input"
                           value="<?php echo $edit_doctor ? htmlspecialchars($edit_doctor['instagram']) : ''; ?>">
                </div>

                <div class="fb-form-group">
                    <label class="fb-label">Twitter</label>
                    <input type="url" name="twitter" class="fb-input"
                           value="<?php echo $edit_doctor ? htmlspecialchars($edit_doctor['twitter']) : ''; ?>">
                </div>

                <div class="fb-form-group">
                    <label class="fb-label">ترتيب العرض</label>
                    <input type="number" name="display_order" class="fb-input" min="0"
                           value="<?php echo $edit_doctor ? $edit_doctor['display_order'] : '0'; ?>">
                </div>
            </div>

            <div class="fb-form-group">
                <label class="fb-label">نبذة عن الدكتور *</label>
                <textarea name="bio" class="fb-textarea" required rows="4"><?php echo $edit_doctor ? htmlspecialchars($edit_doctor['bio']) : ''; ?></textarea>
            </div>

            <div class="fb-form-group">
                <label class="fb-label">صورة الدكتور</label>
                <?php if ($edit_doctor && !empty($edit_doctor['image'])): ?>
                    <div style="margin-bottom: 16px;">
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($edit_doctor['image']); ?>"
                             alt="Current" class="doctor-image-preview">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" class="fb-input" accept="image/*">
                <small style="color: var(--fb-secondary-text); display: block; margin-top: 6px;">
                    الحجم الموصى به: 400x400 بكسل
                </small>
            </div>

            <div class="fb-form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="is_active"
                           <?php echo (!$edit_doctor || $edit_doctor['is_active']) ? 'checked' : ''; ?>>
                    <span class="fb-label" style="margin-bottom: 0;">نشط</span>
                </label>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="fb-btn fb-btn-primary">
                    <i class="fas fa-save"></i>
                    <?php echo $edit_doctor ? 'تحديث' : 'إضافة'; ?>
                </button>

                <?php if ($edit_doctor): ?>
                <a href="doctors.php" class="fb-btn fb-btn-secondary">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Doctors List -->
<div class="fb-card" style="margin-top: 20px;">
    <div class="fb-card-header">
        <h2 class="fb-card-title">قائمة الدكاترة (<?php echo count($doctors); ?>)</h2>
    </div>
    <div class="fb-card-body no-padding">
        <?php if (count($doctors) > 0): ?>
        <table class="fb-table">
            <thead>
                <tr>
                    <th>الصورة</th>
                    <th>الاسم</th>
                    <th>التخصص</th>
                    <th>الخبرة</th>
                    <th>الترتيب</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td>
                        <?php if (!empty($doctor['image'])): ?>
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($doctor['image']); ?>"
                             alt="<?php echo htmlspecialchars($doctor['name']); ?>"
                             style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--fb-gray);
                                    display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-md" style="color: var(--fb-secondary-text);"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($doctor['name']); ?></strong><br>
                        <small style="color: var(--fb-secondary-text);">
                            <?php echo htmlspecialchars($doctor['title']); ?>
                        </small>
                    </td>
                    <td><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                    <td><?php echo $doctor['years_experience']; ?> سنة</td>
                    <td><?php echo $doctor['display_order']; ?></td>
                    <td>
                        <?php if ($doctor['is_active']): ?>
                        <span class="fb-badge fb-badge-success">نشط</span>
                        <?php else: ?>
                        <span class="fb-badge fb-badge-danger">غير نشط</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="doctors.php?edit=<?php echo $doctor['id']; ?>"
                               class="fb-btn fb-btn-secondary fb-btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="if(confirm('هل أنت متأكد من حذف هذا الدكتور؟')) location.href='doctors.php?delete=<?php echo $doctor['id']; ?>'"
                                    class="fb-btn fb-btn-danger fb-btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="fb-empty-state">
            <div class="fb-empty-icon">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="fb-empty-title">لا يوجد دكاترة</div>
            <div class="fb-empty-text">قم بإضافة دكتور جديد من النموذج أعلاه</div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer-facebook.php'; ?>
