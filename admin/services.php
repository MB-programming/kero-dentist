<?php
$page_title = 'إدارة الخدمات';
include 'includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Get image to delete
    $stmt = $conn->prepare("SELECT image FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch();

    if ($service && !empty($service['image'])) {
        $image_path = UPLOAD_PATH . $service['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $success_message = 'تم حذف الخدمة بنجاح';
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $icon = sanitize($_POST['icon']);
    $display_order = intval($_POST['display_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if updating
        if ($id) {
            $stmt = $conn->prepare("SELECT image FROM services WHERE id = ?");
            $stmt->execute([$id]);
            $old_service = $stmt->fetch();
            if ($old_service && !empty($old_service['image'])) {
                $old_image_path = UPLOAD_PATH . $old_service['image'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        }

        $upload_result = uploadFile($_FILES['image'], 'services');
        if ($upload_result['success']) {
            $image = $upload_result['path'];
        }
    }

    if ($id) {
        if (!empty($image)) {
            $stmt = $conn->prepare("UPDATE services SET title = ?, description = ?, icon = ?, image = ?, display_order = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $description, $icon, $image, $display_order, $is_active, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE services SET title = ?, description = ?, icon = ?, display_order = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$title, $description, $icon, $display_order, $is_active, $id]);
        }
        $success_message = 'تم تحديث الخدمة بنجاح';
    } else {
        $stmt = $conn->prepare("INSERT INTO services (title, description, icon, image, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $icon, $image, $display_order, $is_active]);
        $success_message = 'تم إضافة الخدمة بنجاح';
    }
}

$stmt = $conn->query("SELECT * FROM services ORDER BY display_order ASC");
$services = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>إدارة الخدمات</h1>
    <button onclick="openAddModal()" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة خدمة
    </button>
</div>

<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (count($services) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>الترتيب</th>
                        <th>العنوان</th>
                        <th>الوصف</th>
                        <th>الصورة/الأيقونة</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo $service['display_order']; ?></td>
                        <td><?php echo htmlspecialchars($service['title']); ?></td>
                        <td><?php echo htmlspecialchars(substr($service['description'], 0, 100)); ?>...</td>
                        <td>
                            <?php if (!empty($service['image'])): ?>
                                <img src="<?php echo UPLOAD_URL . htmlspecialchars($service['image']); ?>" alt="Service Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            <?php elseif (!empty($service['icon'])): ?>
                                <i class="fas <?php echo htmlspecialchars($service['icon']); ?>" style="font-size: 24px;"></i>
                            <?php else: ?>
                                <span>-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($service['is_active']): ?>
                            <span class="badge badge-success">نشط</span>
                            <?php else: ?>
                            <span class="badge badge-warning">غير نشط</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button onclick='editService(<?php echo json_encode($service); ?>)' class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                            <a href="?delete=<?php echo $service['id']; ?>" onclick="return confirm('هل أنت متأكد؟')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center" style="padding: 40px;">لا توجد خدمات</p>
        <?php endif; ?>
    </div>
</div>

<div id="serviceModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">إضافة خدمة</h3>
            <button class="modal-close" onclick="closeModal('serviceModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="service_id">
                <div class="form-group">
                    <label>عنوان الخدمة *</label>
                    <input type="text" name="title" id="service_title" required>
                </div>
                <div class="form-group">
                    <label>الوصف</label>
                    <textarea name="description" id="service_description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>صورة الخدمة</label>
                    <input type="file" name="image" id="service_image" accept="image/*">
                    <small style="display: block; margin-top: 5px; color: #6b7280;">اختر صورة للخدمة (يُفضل استخدام الصور بدلاً من الأيقونات)</small>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>الأيقونة (Font Awesome - اختياري)</label>
                        <input type="text" name="icon" id="service_icon" placeholder="fa-tooth">
                        <small style="display: block; margin-top: 5px; color: #6b7280;">تُستخدم فقط إذا لم يتم رفع صورة</small>
                    </div>
                    <div class="form-group">
                        <label>الترتيب</label>
                        <input type="number" name="display_order" id="service_order" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_active" id="service_active" checked> نشط</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> حفظ</button>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'إضافة خدمة';
    document.getElementById('service_id').value = '';
    document.getElementById('service_title').value = '';
    document.getElementById('service_description').value = '';
    document.getElementById('service_icon').value = '';
    document.getElementById('service_order').value = '0';
    document.getElementById('service_active').checked = true;
    openModal('serviceModal');
}

function editService(service) {
    document.getElementById('modalTitle').textContent = 'تعديل الخدمة';
    document.getElementById('service_id').value = service.id;
    document.getElementById('service_title').value = service.title;
    document.getElementById('service_description').value = service.description;
    document.getElementById('service_icon').value = service.icon;
    document.getElementById('service_order').value = service.display_order;
    document.getElementById('service_active').checked = service.is_active == 1;
    openModal('serviceModal');
}
</script>

<?php include 'includes/footer.php'; ?>
