<?php
require_once '../includes/config.php';
requireLogin();

$page_title = 'إدارة التخصصات والخبرات';

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $title = sanitize($_POST['title']);
            $description = sanitize($_POST['description']);
            $icon = sanitize($_POST['icon']);
            $display_order = (int)$_POST['display_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if ($_POST['action'] === 'add') {
                // Create table if not exists
                $conn->exec("
                    CREATE TABLE IF NOT EXISTS specialties (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255) NOT NULL,
                        description TEXT,
                        icon VARCHAR(100),
                        display_order INT DEFAULT 0,
                        is_active TINYINT(1) DEFAULT 1,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        INDEX idx_active (is_active),
                        INDEX idx_order (display_order)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");

                $stmt = $conn->prepare("INSERT INTO specialties (title, description, icon, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $icon, $display_order, $is_active]);
                $success = "تم إضافة التخصص بنجاح";
            } else {
                $id = (int)$_POST['id'];
                $stmt = $conn->prepare("UPDATE specialties SET title = ?, description = ?, icon = ?, display_order = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$title, $description, $icon, $display_order, $is_active, $id]);
                $success = "تم تحديث التخصص بنجاح";
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM specialties WHERE id = ?");
            $stmt->execute([$id]);
            $success = "تم حذف التخصص بنجاح";
        }
    }
}

// Fetch all specialties
try {
    // Create table if not exists
    $conn->exec("
        CREATE TABLE IF NOT EXISTS specialties (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            icon VARCHAR(100),
            display_order INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_active (is_active),
            INDEX idx_order (display_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $stmt = $conn->query("SELECT * FROM specialties ORDER BY display_order ASC, id DESC");
    $specialties = $stmt->fetchAll();
} catch(PDOException $e) {
    $specialties = [];
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $page_title; ?></h1>
        <button class="btn btn-primary" onclick="openModal('addModal')">
            <i class="fas fa-plus"></i> إضافة تخصص جديد
        </button>
    </div>

    <?php if (isset($success)): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>الأيقونة</th>
                            <th>العنوان</th>
                            <th>الوصف</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($specialties as $specialty): ?>
                        <tr>
                            <td><?php echo $specialty['display_order']; ?></td>
                            <td><i class="fas <?php echo htmlspecialchars($specialty['icon']); ?>" style="font-size: 24px; color: var(--primary);"></i></td>
                            <td><?php echo htmlspecialchars($specialty['title']); ?></td>
                            <td><?php echo htmlspecialchars(substr($specialty['description'], 0, 60)) . '...'; ?></td>
                            <td>
                                <span class="badge <?php echo $specialty['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $specialty['is_active'] ? 'نشط' : 'غير نشط'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick='editSpecialty(<?php echo json_encode($specialty); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $specialty['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($specialties) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center">لا توجد تخصصات. قم بإضافة تخصص جديد</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal" id="addModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h2>إضافة تخصص جديد</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label>العنوان *</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label>الوصف *</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>الأيقونة *</label>
                <select name="icon" class="form-control" required>
                    <option value="">-- اختر أيقونة --</option>
                    <option value="fa-teeth-open">🦷 أسنان مفتوحة - fa-teeth-open</option>
                    <option value="fa-tooth">🦷 سن - fa-tooth</option>
                    <option value="fa-teeth">🦷 أسنان - fa-teeth</option>
                    <option value="fa-smile">😊 ابتسامة - fa-smile</option>
                    <option value="fa-syringe">💉 حقنة - fa-syringe</option>
                    <option value="fa-child">👶 طفل - fa-child</option>
                    <option value="fa-user-md">👨‍⚕️ طبيب - fa-user-md</option>
                    <option value="fa-heartbeat">💓 نبض القلب - fa-heartbeat</option>
                    <option value="fa-shield-alt">🛡️ درع - fa-shield-alt</option>
                    <option value="fa-star">⭐ نجمة - fa-star</option>
                    <option value="fa-award">🏆 جائزة - fa-award</option>
                    <option value="fa-certificate">📜 شهادة - fa-certificate</option>
                    <option value="fa-gem">💎 جوهرة - fa-gem</option>
                    <option value="fa-crown">👑 تاج - fa-crown</option>
                    <option value="fa-magic">✨ سحر - fa-magic</option>
                </select>
            </div>

            <div class="form-group">
                <label>ترتيب العرض</label>
                <input type="number" name="display_order" class="form-control" value="0" min="0">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" checked> نشط
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ
            </button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h2>تعديل التخصص</h2>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div class="form-group">
                <label>العنوان *</label>
                <input type="text" name="title" id="edit_title" class="form-control" required>
            </div>

            <div class="form-group">
                <label>الوصف *</label>
                <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>الأيقونة *</label>
                <select name="icon" id="edit_icon" class="form-control" required>
                    <option value="">-- اختر أيقونة --</option>
                    <option value="fa-teeth-open">🦷 أسنان مفتوحة - fa-teeth-open</option>
                    <option value="fa-tooth">🦷 سن - fa-tooth</option>
                    <option value="fa-teeth">🦷 أسنان - fa-teeth</option>
                    <option value="fa-smile">😊 ابتسامة - fa-smile</option>
                    <option value="fa-syringe">💉 حقنة - fa-syringe</option>
                    <option value="fa-child">👶 طفل - fa-child</option>
                    <option value="fa-user-md">👨‍⚕️ طبيب - fa-user-md</option>
                    <option value="fa-heartbeat">💓 نبض القلب - fa-heartbeat</option>
                    <option value="fa-shield-alt">🛡️ درع - fa-shield-alt</option>
                    <option value="fa-star">⭐ نجمة - fa-star</option>
                    <option value="fa-award">🏆 جائزة - fa-award</option>
                    <option value="fa-certificate">📜 شهادة - fa-certificate</option>
                    <option value="fa-gem">💎 جوهرة - fa-gem</option>
                    <option value="fa-crown">👑 تاج - fa-crown</option>
                    <option value="fa-magic">✨ سحر - fa-magic</option>
                </select>
            </div>

            <div class="form-group">
                <label>ترتيب العرض</label>
                <input type="number" name="display_order" id="edit_display_order" class="form-control" value="0" min="0">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" id="edit_is_active"> نشط
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ التعديلات
            </button>
        </form>
    </div>
</div>

<script>
function editSpecialty(specialty) {
    document.getElementById('edit_id').value = specialty.id;
    document.getElementById('edit_title').value = specialty.title;
    document.getElementById('edit_description').value = specialty.description;
    document.getElementById('edit_icon').value = specialty.icon;
    document.getElementById('edit_display_order').value = specialty.display_order;
    document.getElementById('edit_is_active').checked = specialty.is_active == 1;
    openModal('editModal');
}
</script>

<?php include 'includes/footer.php'; ?>
