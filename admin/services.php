<?php
$page_title = 'إدارة الخدمات';
include 'includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
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

    if ($id) {
        $stmt = $conn->prepare("UPDATE services SET title = ?, description = ?, icon = ?, display_order = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$title, $description, $icon, $display_order, $is_active, $id]);
        $success_message = 'تم تحديث الخدمة بنجاح';
    } else {
        $stmt = $conn->prepare("INSERT INTO services (title, description, icon, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $icon, $display_order, $is_active]);
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
                        <th>الأيقونة</th>
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
                        <td><i class="fas <?php echo htmlspecialchars($service['icon']); ?>"></i></td>
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
            <form method="POST">
                <input type="hidden" name="id" id="service_id">
                <div class="form-group">
                    <label>عنوان الخدمة *</label>
                    <input type="text" name="title" id="service_title" required>
                </div>
                <div class="form-group">
                    <label>الوصف</label>
                    <textarea name="description" id="service_description" rows="4"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>الأيقونة (Font Awesome)</label>
                        <input type="text" name="icon" id="service_icon" placeholder="fa-tooth">
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
