<?php
$page_title = 'إدارة الباقات';
include 'includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM packages WHERE id = ?");
    $stmt->execute([$id]);
    $success_message = 'تم حذف الباقة بنجاح';
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $duration = sanitize($_POST['duration']);
    $features = sanitize($_POST['features']);
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $display_order = intval($_POST['display_order']);

    if ($id) {
        $stmt = $conn->prepare("UPDATE packages SET name = ?, description = ?, price = ?, duration = ?, features = ?, is_popular = ?, is_active = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $duration, $features, $is_popular, $is_active, $display_order, $id]);
        $success_message = 'تم تحديث الباقة بنجاح';
    } else {
        $stmt = $conn->prepare("INSERT INTO packages (name, description, price, duration, features, is_popular, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $duration, $features, $is_popular, $is_active, $display_order]);
        $success_message = 'تم إضافة الباقة بنجاح';
    }
}

$stmt = $conn->query("SELECT * FROM packages ORDER BY display_order ASC");
$packages = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>إدارة الباقات</h1>
    <button onclick="openAddModal()" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة باقة
    </button>
</div>

<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (count($packages) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>الترتيب</th>
                        <th>الاسم</th>
                        <th>السعر</th>
                        <th>المدة</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $package): ?>
                    <tr>
                        <td><?php echo $package['display_order']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($package['name']); ?>
                            <?php if ($package['is_popular']): ?>
                            <span class="badge badge-warning">الأكثر طلباً</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatPrice($package['price']); ?></td>
                        <td><?php echo htmlspecialchars($package['duration']); ?></td>
                        <td>
                            <?php if ($package['is_active']): ?>
                            <span class="badge badge-success">نشط</span>
                            <?php else: ?>
                            <span class="badge badge-warning">غير نشط</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button onclick='editPackage(<?php echo json_encode($package); ?>)' class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                            <a href="?delete=<?php echo $package['id']; ?>" onclick="return confirm('هل أنت متأكد؟')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center" style="padding: 40px;">لا توجد باقات</p>
        <?php endif; ?>
    </div>
</div>

<div id="packageModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">إضافة باقة</h3>
            <button class="modal-close" onclick="closeModal('packageModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="id" id="package_id">
                <div class="form-group">
                    <label>اسم الباقة *</label>
                    <input type="text" name="name" id="package_name" required>
                </div>
                <div class="form-group">
                    <label>الوصف</label>
                    <textarea name="description" id="package_description" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>السعر *</label>
                        <input type="number" step="0.01" name="price" id="package_price" required>
                    </div>
                    <div class="form-group">
                        <label>المدة</label>
                        <input type="text" name="duration" id="package_duration" placeholder="مثال: 1 ساعة">
                    </div>
                </div>
                <div class="form-group">
                    <label>المميزات (كل ميزة في سطر)</label>
                    <textarea name="features" id="package_features" rows="4"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><input type="checkbox" name="is_popular" id="package_popular"> باقة مميزة (الأكثر طلباً)</label>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_active" id="package_active" checked> نشط</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>الترتيب</label>
                    <input type="number" name="display_order" id="package_order" value="0">
                </div>
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> حفظ</button>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'إضافة باقة';
    document.getElementById('package_id').value = '';
    document.getElementById('package_name').value = '';
    document.getElementById('package_description').value = '';
    document.getElementById('package_price').value = '';
    document.getElementById('package_duration').value = '';
    document.getElementById('package_features').value = '';
    document.getElementById('package_popular').checked = false;
    document.getElementById('package_active').checked = true;
    document.getElementById('package_order').value = '0';
    openModal('packageModal');
}

function editPackage(pkg) {
    document.getElementById('modalTitle').textContent = 'تعديل الباقة';
    document.getElementById('package_id').value = pkg.id;
    document.getElementById('package_name').value = pkg.name;
    document.getElementById('package_description').value = pkg.description;
    document.getElementById('package_price').value = pkg.price;
    document.getElementById('package_duration').value = pkg.duration;
    document.getElementById('package_features').value = pkg.features;
    document.getElementById('package_popular').checked = pkg.is_popular == 1;
    document.getElementById('package_active').checked = pkg.is_active == 1;
    document.getElementById('package_order').value = pkg.display_order;
    openModal('packageModal');
}
</script>

<?php include 'includes/footer.php'; ?>
