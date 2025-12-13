<?php
$page_title = 'إدارة المستخدمين';
include 'includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Prevent deleting yourself
    if ($id != $_SESSION['admin_id']) {
        $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
        $stmt->execute([$id]);
        $success_message = 'تم حذف المستخدم بنجاح';
    } else {
        $error_message = 'لا يمكنك حذف حسابك الخاص';
    }
}

// Handle add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);

    if (!empty($username) && !empty($password) && !empty($email)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $email]);
            $success_message = 'تم إضافة المستخدم بنجاح';
        } catch(PDOException $e) {
            $error_message = 'خطأ: اسم المستخدم موجود مسبقاً';
        }
    } else {
        $error_message = 'يرجى ملء جميع الحقول';
    }
}

// Handle edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['user_id']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'] ?? '';

    try {
        if (!empty($new_password)) {
            // Update with new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admin_users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $email, $hashed_password, $id]);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE admin_users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $id]);
        }
        $success_message = 'تم تحديث المستخدم بنجاح';
    } catch(PDOException $e) {
        $error_message = 'خطأ: ' . $e->getMessage();
    }
}

// Fetch all users
$stmt = $conn->query("SELECT * FROM admin_users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>إدارة المستخدمين</h1>
    <button onclick="openAddModal()" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
    </button>
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

<div class="card">
    <div class="card-header">
        <h2>المستخدمين الإداريين (<?php echo count($users); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (count($users) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                            <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                            <span class="badge badge-primary">أنت</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo formatDate($user['created_at'], 'd/m/Y H:i'); ?></td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button onclick='editUser(<?php echo json_encode($user); ?>)' class="btn btn-sm btn-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')" class="btn btn-sm btn-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php else: ?>
                                <button class="btn btn-sm" disabled title="لا يمكن حذف حسابك">
                                    <i class="fas fa-ban"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center" style="padding: 40px; color: var(--text-light);">
            <i class="fas fa-users" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
            لا يوجد مستخدمين
        </p>
        <?php endif; ?>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>إضافة مستخدم جديد</h3>
            <button class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <div class="form-group">
                    <label>اسم المستخدم *</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>البريد الإلكتروني *</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>كلمة المرور *</label>
                    <input type="password" name="password" required minlength="6">
                    <small style="color: var(--text-light); display: block; margin-top: 5px;">
                        الحد الأدنى 6 أحرف
                    </small>
                </div>
                <button type="submit" name="add_user" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> إضافة المستخدم
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>تعديل المستخدم</h3>
            <button class="modal-close" onclick="closeModal('editUserModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="form-group">
                    <label>اسم المستخدم *</label>
                    <input type="text" name="username" id="edit_username" required>
                </div>
                <div class="form-group">
                    <label>البريد الإلكتروني *</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label>كلمة مرور جديدة (اختياري)</label>
                    <input type="password" name="new_password" id="edit_password" minlength="6">
                    <small style="color: var(--text-light); display: block; margin-top: 5px;">
                        اتركها فارغة للاحتفاظ بكلمة المرور الحالية
                    </small>
                </div>
                <button type="submit" name="edit_user" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> حفظ التغييرات
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    openModal('addUserModal');
}

function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_password').value = '';
    openModal('editUserModal');
}

function deleteUser(id, username) {
    if (confirm('هل أنت متأكد من حذف المستخدم "' + username + '"؟')) {
        window.location.href = 'users.php?delete=' + id;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
