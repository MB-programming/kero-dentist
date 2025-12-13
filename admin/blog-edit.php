<?php
$page_title = 'تعديل المقال';
include 'includes/header.php';

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$post = null;

if ($post_id) {
    $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) {
        redirect(SITE_URL . '/admin/blog.php');
    }
    $page_title = 'تعديل: ' . $post['title'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $slug = !empty($_POST['slug']) ? generateSlug($_POST['slug']) : generateSlug($title);
    $content = $_POST['content']; // Don't sanitize HTML content
    $excerpt = sanitize($_POST['excerpt']);
    $author = sanitize($_POST['author']);
    $meta_title = sanitize($_POST['meta_title']);
    $meta_description = sanitize($_POST['meta_description']);
    $meta_keywords = sanitize($_POST['meta_keywords']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    // Handle file upload
    $featured_image = $post ? $post['featured_image'] : '';
    if (!empty($_FILES['featured_image']['name'])) {
        $upload_result = uploadFile($_FILES['featured_image'], 'blog');
        if ($upload_result['success']) {
            // Delete old image
            if ($featured_image) {
                @unlink(UPLOAD_PATH . $featured_image);
            }
            $featured_image = $upload_result['path'];
        }
    }

    $published_at = $is_published ? ($post && $post['is_published'] ? $post['published_at'] : date('Y-m-d H:i:s')) : null;

    try {
        if ($post_id) {
            // Update existing post
            $stmt = $conn->prepare("
                UPDATE blog_posts SET
                title = ?, slug = ?, content = ?, excerpt = ?, featured_image = ?,
                author = ?, meta_title = ?, meta_description = ?, meta_keywords = ?,
                is_published = ?, published_at = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $title, $slug, $content, $excerpt, $featured_image,
                $author, $meta_title, $meta_description, $meta_keywords,
                $is_published, $published_at, $post_id
            ]);
            $success_message = 'تم تحديث المقال بنجاح';
        } else {
            // Insert new post
            $stmt = $conn->prepare("
                INSERT INTO blog_posts
                (title, slug, content, excerpt, featured_image, author, meta_title, meta_description, meta_keywords, is_published, published_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title, $slug, $content, $excerpt, $featured_image,
                $author, $meta_title, $meta_description, $meta_keywords,
                $is_published, $published_at
            ]);
            $post_id = $conn->lastInsertId();
            $success_message = 'تم إضافة المقال بنجاح';
        }

        // Refresh post data
        $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();

    } catch(PDOException $e) {
        $error_message = 'حدث خطأ: ' . $e->getMessage();
    }
}
?>

<div class="page-header">
    <h1><?php echo $post_id ? 'تعديل المقال' : 'إضافة مقال جديد'; ?></h1>
    <a href="blog.php" class="btn btn-primary btn-sm">
        <i class="fas fa-arrow-right"></i> العودة إلى المقالات
    </a>
</div>

<?php if (isset($success_message)): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    <?php echo htmlspecialchars($success_message); ?>
    <?php if ($post && $post['is_published']): ?>
    <a href="../../public/blog-post.php?slug=<?php echo urlencode($post['slug']); ?>" target="_blank" style="margin-right: 15px;">
        <i class="fas fa-external-link-alt"></i> مشاهدة المقال
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <?php echo htmlspecialchars($error_message); ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="card">
        <div class="card-header">
            <h2>بيانات المقال</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="title">عنوان المقال *</label>
                <input type="text" id="title" name="title" required value="<?php echo $post ? htmlspecialchars($post['title']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="slug">الرابط (Slug)</label>
                <input type="text" id="slug" name="slug" value="<?php echo $post ? htmlspecialchars($post['slug']) : ''; ?>" placeholder="يتم إنشاؤه تلقائياً من العنوان">
                <small style="color: var(--text-light); display: block; margin-top: 5px;">اترك فارغاً للإنشاء التلقائي</small>
            </div>

            <div class="form-group">
                <label for="excerpt">المقتطف (ملخص قصير)</label>
                <textarea id="excerpt" name="excerpt" rows="3"><?php echo $post ? htmlspecialchars($post['excerpt']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="content">المحتوى *</label>
                <textarea id="content" name="content" required><?php echo $post ? htmlspecialchars($post['content']) : ''; ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="author">الكاتب</label>
                    <input type="text" id="author" name="author" value="<?php echo $post ? htmlspecialchars($post['author']) : getSetting('doctor_name', ''); ?>">
                </div>

                <div class="form-group">
                    <label for="featured_image">الصورة المميزة</label>
                    <input type="file" id="featured_image" name="featured_image" accept="image/*">
                    <?php if ($post && $post['featured_image']): ?>
                    <div style="margin-top: 10px;">
                        <img src="<?php echo UPLOAD_URL . htmlspecialchars($post['featured_image']); ?>" alt="Featured Image" style="max-width: 200px; border-radius: 8px;">
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_published" value="1" <?php echo ($post && $post['is_published']) ? 'checked' : ''; ?>>
                    نشر المقال
                </label>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>إعدادات SEO</h2>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="meta_title">عنوان الصفحة (Meta Title)</label>
                <input type="text" id="meta_title" name="meta_title" value="<?php echo $post ? htmlspecialchars($post['meta_title']) : ''; ?>" maxlength="60">
                <small style="color: var(--text-light); display: block; margin-top: 5px;">الطول الموصى به: 50-60 حرف</small>
            </div>

            <div class="form-group">
                <label for="meta_description">الوصف (Meta Description)</label>
                <textarea id="meta_description" name="meta_description" rows="3" maxlength="160"><?php echo $post ? htmlspecialchars($post['meta_description']) : ''; ?></textarea>
                <small style="color: var(--text-light); display: block; margin-top: 5px;">الطول الموصى به: 150-160 حرف</small>
            </div>

            <div class="form-group">
                <label for="meta_keywords">الكلمات المفتاحية (Keywords)</label>
                <input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo $post ? htmlspecialchars($post['meta_keywords']) : ''; ?>" placeholder="افصل بينها بفواصل">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block" style="max-width: 300px; margin: 0 auto;">
        <i class="fas fa-save"></i> <?php echo $post_id ? 'تحديث المقال' : 'إضافة المقال'; ?>
    </button>
</form>

<!-- TinyMCE Rich Text Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#content',
    height: 500,
    directionality: 'rtl',
    language: 'ar',
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image link | code | help',
    menubar: 'file edit view insert format tools table help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; direction:rtl; }',
    images_upload_url: '../api/upload-image.php',
    automatic_uploads: true,
    file_picker_types: 'image',
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    }
});

// Auto-generate slug from title
document.getElementById('title').addEventListener('blur', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value) {
        const title = this.value;
        const slug = title.toLowerCase()
            .replace(/[\u0621-\u064A]+/g, function(match) {
                // Keep Arabic as is
                return match;
            })
            .replace(/\s+/g, '-')
            .replace(/[^\u0621-\u064A\u0660-\u0669a-z0-9-]/g, '');
        slugInput.value = slug;
    }
});
</script>

<?php include 'includes/footer.php'; ?>
