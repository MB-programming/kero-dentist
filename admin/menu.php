<?php
require_once '../includes/config.php';
requireLogin();

// Handle add/edit menu item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_menu_item'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $menu_location = sanitize($_POST['menu_location']);
    $title = sanitize($_POST['title']);
    $url = sanitize($_POST['url']);
    $display_order = intval($_POST['display_order']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $parent_id = !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;

    try {
        if ($id > 0) {
            // Update existing menu item
            $stmt = $conn->prepare("UPDATE menu_items SET menu_location = ?, title = ?, url = ?, display_order = ?, is_active = ?, parent_id = ? WHERE id = ?");
            $stmt->execute([$menu_location, $title, $url, $display_order, $is_active, $parent_id, $id]);
            $success_message = 'تم تحديث عنصر القائمة بنجاح';
        } else {
            // Add new menu item
            $stmt = $conn->prepare("INSERT INTO menu_items (menu_location, title, url, display_order, is_active, parent_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$menu_location, $title, $url, $display_order, $is_active, $parent_id]);
            $success_message = 'تم إضافة عنصر القائمة بنجاح';
        }
    } catch(PDOException $e) {
        $error_message = 'حدث خطأ أثناء حفظ عنصر القائمة';
    }
}

// Handle delete menu item
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        // Delete children first
        $stmt = $conn->prepare("DELETE FROM menu_items WHERE parent_id = ?");
        $stmt->execute([$id]);

        // Delete parent
        $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: menu.php?deleted=1');
        exit;
    } catch(PDOException $e) {
        $error_message = 'حدث خطأ أثناء حذف عنصر القائمة';
    }
}

// Fetch all menu items
$stmt = $conn->query("SELECT * FROM menu_items ORDER BY menu_location, display_order ASC");
$menu_items = $stmt->fetchAll();

// Group by location
$grouped_items = [
    'header' => [],
    'footer' => []
];

foreach ($menu_items as $item) {
    $grouped_items[$item['menu_location']][] = $item;
}

// Get parent menu items for dropdown
$parent_items = $conn->query("SELECT id, title, menu_location FROM menu_items WHERE parent_id IS NULL ORDER BY menu_location, display_order")->fetchAll();

// Get menu item for editing
$edit_item = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $edit_item = $stmt->fetch();
}

$page_title = 'إدارة القوائم';
include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-bars"></i> إدارة القوائم</h1>
    <p>إضافة وتعديل عناصر القوائم (Header & Footer)</p>
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
    تم حذف عنصر القائمة بنجاح
</div>
<?php endif; ?>

<!-- Add/Edit Form -->
<div class="card">
    <div class="card-header">
        <h2><?php echo $edit_item ? 'تعديل عنصر القائمة' : 'إضافة عنصر جديد'; ?></h2>
    </div>
    <div class="card-body">
        <form method="POST">
            <?php if ($edit_item): ?>
            <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="title">عنوان العنصر *</label>
                    <input type="text" id="title" name="title" required
                           value="<?php echo $edit_item ? htmlspecialchars($edit_item['title']) : ''; ?>"
                           placeholder="مثال: الرئيسية">
                </div>

                <div class="form-group">
                    <label for="url">الرابط *</label>
                    <input type="text" id="url" name="url" required
                           value="<?php echo $edit_item ? htmlspecialchars($edit_item['url']) : ''; ?>"
                           placeholder="مثال: #home أو /about.php">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="menu_location">موقع القائمة *</label>
                    <select id="menu_location" name="menu_location" required>
                        <option value="header" <?php echo ($edit_item && $edit_item['menu_location'] === 'header') ? 'selected' : ''; ?>>Header (القائمة العلوية)</option>
                        <option value="footer" <?php echo ($edit_item && $edit_item['menu_location'] === 'footer') ? 'selected' : ''; ?>>Footer (القائمة السفلية)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="display_order">ترتيب العرض</label>
                    <input type="number" id="display_order" name="display_order"
                           value="<?php echo $edit_item ? $edit_item['display_order'] : 0; ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="parent_id">عنصر أب (للقوائم المنسدلة - اختياري)</label>
                    <select id="parent_id" name="parent_id">
                        <option value="">بدون (عنصر رئيسي)</option>
                        <?php foreach ($parent_items as $parent): ?>
                        <option value="<?php echo $parent['id']; ?>"
                                <?php echo ($edit_item && $edit_item['parent_id'] == $parent['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parent['title']); ?> (<?php echo $parent['menu_location']; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: var(--text-light); display: block; margin-top: 5px;">
                        اختر عنصر أب لإنشاء قائمة منسدلة
                    </small>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" value="1"
                               <?php echo (!$edit_item || $edit_item['is_active']) ? 'checked' : ''; ?>>
                        تفعيل العنصر
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" name="save_menu_item" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $edit_item ? 'تحديث' : 'إضافة'; ?>
                </button>
                <?php if ($edit_item): ?>
                <a href="menu.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Header Menu Items -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h2><i class="fas fa-arrow-up"></i> عناصر القائمة العلوية (Header)</h2>
    </div>
    <div class="card-body">
        <?php if (count($grouped_items['header']) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>الرابط</th>
                        <th>الترتيب</th>
                        <th>عنصر أب</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grouped_items['header'] as $item): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                        </td>
                        <td>
                            <small style="color: var(--text-light); font-family: monospace;">
                                <?php echo htmlspecialchars($item['url']); ?>
                            </small>
                        </td>
                        <td><?php echo $item['display_order']; ?></td>
                        <td>
                            <?php
                            if ($item['parent_id']) {
                                $parent = array_filter($parent_items, fn($p) => $p['id'] == $item['parent_id']);
                                $parent = reset($parent);
                                echo $parent ? htmlspecialchars($parent['title']) : '-';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($item['is_active']): ?>
                            <span class="badge badge-success">نشط</span>
                            <?php else: ?>
                            <span class="badge badge-secondary">غير نشط</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="menu.php?edit=<?php echo $item['id']; ?>"
                                   class="btn btn-sm btn-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteMenuItem(<?php echo $item['id']; ?>)"
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
            <i class="fas fa-bars"></i>
            <h3>لا توجد عناصر في القائمة العلوية</h3>
            <p>قم بإضافة عناصر جديدة من النموذج أعلاه</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer Menu Items -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h2><i class="fas fa-arrow-down"></i> عناصر القائمة السفلية (Footer)</h2>
    </div>
    <div class="card-body">
        <?php if (count($grouped_items['footer']) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>العنوان</th>
                        <th>الرابط</th>
                        <th>الترتيب</th>
                        <th>عنصر أب</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grouped_items['footer'] as $item): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                        </td>
                        <td>
                            <small style="color: var(--text-light); font-family: monospace;">
                                <?php echo htmlspecialchars($item['url']); ?>
                            </small>
                        </td>
                        <td><?php echo $item['display_order']; ?></td>
                        <td>
                            <?php
                            if ($item['parent_id']) {
                                $parent = array_filter($parent_items, fn($p) => $p['id'] == $item['parent_id']);
                                $parent = reset($parent);
                                echo $parent ? htmlspecialchars($parent['title']) : '-';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($item['is_active']): ?>
                            <span class="badge badge-success">نشط</span>
                            <?php else: ?>
                            <span class="badge badge-secondary">غير نشط</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="menu.php?edit=<?php echo $item['id']; ?>"
                                   class="btn btn-sm btn-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteMenuItem(<?php echo $item['id']; ?>)"
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
            <i class="fas fa-bars"></i>
            <h3>لا توجد عناصر في القائمة السفلية</h3>
            <p>قم بإضافة عناصر جديدة من النموذج أعلاه</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteMenuItem(id) {
    if (confirm('هل أنت متأكد من حذف هذا العنصر؟ (سيتم حذف جميع العناصر الفرعية أيضاً)')) {
        window.location.href = 'menu.php?delete=' + id;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
