<?php
$page_title = 'إدارة المقالات';
include 'includes/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        // Get featured image to delete
        $stmt = $conn->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();

        if ($post && $post['featured_image']) {
            @unlink(UPLOAD_PATH . $post['featured_image']);
        }

        $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $success_message = 'تم حذف المقال بنجاح';
    } catch(PDOException $e) {
        $error_message = 'حدث خطأ أثناء حذف المقال';
    }
}

// Fetch all blog posts
$stmt = $conn->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>إدارة المقالات</h1>
    <p>إضافة وتعديل وحذف المقالات</p>
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
        <h2>المقالات</h2>
        <a href="blog-edit.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة مقال جديد
        </a>
    </div>
    <div class="card-body">
        <?php if (count($posts) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>الكاتب</th>
                        <th>الحالة</th>
                        <th>تاريخ النشر</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                            <?php if ($post['featured_image']): ?>
                            <br><small style="color: var(--text-light);"><i class="fas fa-image"></i> بها صورة</small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($post['author'] ?: '-'); ?></td>
                        <td>
                            <?php if ($post['is_published']): ?>
                            <span class="badge badge-success">منشور</span>
                            <?php else: ?>
                            <span class="badge badge-warning">مسودة</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $post['published_at'] ? formatDate($post['published_at']) : '-'; ?></td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <?php if ($post['is_published']): ?>
                                <a href="../../public/blog-post.php?slug=<?php echo urlencode($post['slug']); ?>" target="_blank" class="btn btn-sm btn-success" title="مشاهدة">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php endif; ?>
                                <a href="blog-edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deletePost(<?php echo $post['id']; ?>)" class="btn btn-sm btn-danger" title="حذف">
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
        <p class="text-center" style="padding: 40px; color: var(--text-light);">
            <i class="fas fa-newspaper" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
            لا توجد مقالات. <a href="blog-edit.php">أضف مقال جديد</a>
        </p>
        <?php endif; ?>
    </div>
</div>

<script>
function deletePost(id) {
    if (confirm('هل أنت متأكد من حذف هذا المقال؟')) {
        window.location.href = 'blog.php?delete=' + id;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
