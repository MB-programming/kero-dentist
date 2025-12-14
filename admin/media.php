<?php
$page_title = 'مكتبة الوسائط';
include 'includes/header.php';

// Handle file upload
if (isset($_FILES['media_files']) && !empty($_FILES['media_files']['name'][0])) {
    $upload_count = 0;
    $error_count = 0;

    foreach ($_FILES['media_files']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['media_files']['error'][$key] === UPLOAD_ERR_OK) {
            $file = [
                'name' => $_FILES['media_files']['name'][$key],
                'type' => $_FILES['media_files']['type'][$key],
                'tmp_name' => $tmp_name,
                'error' => $_FILES['media_files']['error'][$key],
                'size' => $_FILES['media_files']['size'][$key]
            ];

            $result = uploadFile($file, 'media');
            if ($result['success']) {
                $upload_count++;
            } else {
                $error_count++;
            }
        }
    }

    if ($upload_count > 0) {
        $success_message = "تم رفع {$upload_count} ملف بنجاح";
    }
    if ($error_count > 0) {
        $error_message = "فشل رفع {$error_count} ملف";
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $file_path = sanitize($_GET['delete']);
    $full_path = UPLOAD_DIR . '/' . $file_path;

    if (file_exists($full_path) && unlink($full_path)) {
        $success_message = 'تم حذف الملف بنجاح';
    } else {
        $error_message = 'فشل حذف الملف';
    }
    header('Location: media.php');
    exit;
}

// Get all media files
$media_files = [];
$upload_dir = UPLOAD_DIR;

if (is_dir($upload_dir)) {
    $scan = scandir($upload_dir);
    foreach ($scan as $file) {
        if ($file !== '.' && $file !== '..' && is_file($upload_dir . '/' . $file)) {
            $file_info = [
                'name' => $file,
                'path' => $file,
                'url' => UPLOAD_URL . $file,
                'size' => filesize($upload_dir . '/' . $file),
                'modified' => filemtime($upload_dir . '/' . $file),
                'extension' => strtolower(pathinfo($file, PATHINFO_EXTENSION))
            ];

            // Determine file type
            $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $file_info['is_image'] = in_array($file_info['extension'], $image_exts);

            $media_files[] = $file_info;
        }
    }

    // Sort by modification time (newest first)
    usort($media_files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
}

// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' Bytes';
    }
}
?>

<div class="page-header">
    <h1><i class="fas fa-photo-video"></i> مكتبة الوسائط</h1>
    <p>إدارة الصور والملفات المرفوعة</p>
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

<!-- Upload Section -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-cloud-upload-alt"></i> رفع ملفات جديدة</h2>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="upload-area" id="uploadArea">
                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 15px;"></i>
                <h3>اسحب الملفات هنا أو انقر للاختيار</h3>
                <p style="color: var(--text-light); margin-top: 10px;">يدعم: JPG, PNG, GIF, WEBP, SVG, PDF</p>
                <input type="file" name="media_files[]" id="mediaFiles" multiple accept="image/*,.pdf,.doc,.docx" style="display: none;">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('mediaFiles').click();" style="margin-top: 20px;">
                    <i class="fas fa-folder-open"></i> اختيار ملفات
                </button>
            </div>

            <div id="selectedFiles" style="margin-top: 20px;"></div>

            <button type="submit" class="btn btn-success" id="uploadButton" style="display: none; margin-top: 20px;">
                <i class="fas fa-upload"></i> رفع الملفات
            </button>
        </form>
    </div>
</div>

<!-- Media Grid -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-images"></i> الملفات المرفوعة (<?php echo count($media_files); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (count($media_files) > 0): ?>
        <div class="media-grid">
            <?php foreach ($media_files as $file): ?>
            <div class="media-item">
                <div class="media-preview">
                    <?php if ($file['is_image']): ?>
                        <img src="<?php echo htmlspecialchars($file['url']); ?>" alt="<?php echo htmlspecialchars($file['name']); ?>">
                    <?php else: ?>
                        <div class="file-icon">
                            <i class="fas fa-file-<?php echo $file['extension'] === 'pdf' ? 'pdf' : 'alt'; ?>"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="media-info">
                    <div class="media-name" title="<?php echo htmlspecialchars($file['name']); ?>">
                        <?php echo htmlspecialchars(strlen($file['name']) > 30 ? substr($file['name'], 0, 30) . '...' : $file['name']); ?>
                    </div>
                    <div class="media-meta">
                        <span><?php echo formatFileSize($file['size']); ?></span>
                        <span><?php echo date('d/m/Y', $file['modified']); ?></span>
                    </div>
                </div>

                <div class="media-actions">
                    <button onclick="copyToClipboard('<?php echo htmlspecialchars($file['url']); ?>')" class="btn btn-sm btn-primary" title="نسخ الرابط">
                        <i class="fas fa-copy"></i>
                    </button>
                    <a href="<?php echo htmlspecialchars($file['url']); ?>" target="_blank" class="btn btn-sm btn-info" title="فتح">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    <button onclick="deleteFile('<?php echo htmlspecialchars($file['path']); ?>', '<?php echo htmlspecialchars($file['name']); ?>')" class="btn btn-sm btn-danger" title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <h3>لا توجد ملفات</h3>
            <p>قم برفع ملفات جديدة لعرضها هنا</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.upload-area {
    border: 3px dashed var(--border-color);
    border-radius: 12px;
    padding: 60px 20px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: var(--primary-color);
    background: #f8fafc;
}

.upload-area.dragover {
    border-color: var(--primary-color);
    background: #eff6ff;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.media-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.media-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.media-preview {
    width: 100%;
    height: 200px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.media-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.file-icon {
    font-size: 4rem;
    color: var(--primary-color);
}

.media-info {
    padding: 15px;
}

.media-name {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.media-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: var(--text-light);
}

.media-actions {
    padding: 0 15px 15px;
    display: flex;
    gap: 8px;
}

#selectedFiles {
    display: none;
}

#selectedFiles.active {
    display: block;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
}

.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    background: white;
    border-radius: 6px;
    margin-bottom: 8px;
}
</style>

<script>
// File upload handling
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('mediaFiles');
const selectedFilesDiv = document.getElementById('selectedFiles');
const uploadButton = document.getElementById('uploadButton');

// Click to select files
uploadArea.addEventListener('click', () => {
    fileInput.click();
});

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// Highlight drop area when file is dragged over it
['dragenter', 'dragover'].forEach(eventName => {
    uploadArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    uploadArea.classList.add('dragover');
}

function unhighlight(e) {
    uploadArea.classList.remove('dragover');
}

// Handle dropped files
uploadArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    fileInput.files = files;
    handleFiles(files);
}

// Handle selected files
fileInput.addEventListener('change', function() {
    handleFiles(this.files);
});

function handleFiles(files) {
    if (files.length > 0) {
        selectedFilesDiv.classList.add('active');
        selectedFilesDiv.innerHTML = '<h4 style="margin-bottom: 10px;">الملفات المحددة:</h4>';

        Array.from(files).forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <span><i class="fas fa-file"></i> ${file.name}</span>
                <span style="color: var(--text-light); font-size: 0.9rem;">${formatBytes(file.size)}</span>
            `;
            selectedFilesDiv.appendChild(fileItem);
        });

        uploadButton.style.display = 'block';
    }
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('تم نسخ الرابط إلى الحافظة!');
    });
}

// Delete file
function deleteFile(path, name) {
    if (confirm(`هل أنت متأكد من حذف الملف: ${name}؟`)) {
        window.location.href = 'media.php?delete=' + encodeURIComponent(path);
    }
}
</script>

<?php include 'includes/footer.php'; ?>
