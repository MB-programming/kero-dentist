<?php
$page_title = 'إدارة الحجوزات';
include 'includes/header.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = sanitize($_POST['status']);

    try {
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $booking_id]);
        $success_message = 'تم تحديث حالة الحجز بنجاح';
    } catch(PDOException $e) {
        $error_message = 'حدث خطأ أثناء تحديث الحالة';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $booking_id = intval($_GET['delete']);
    try {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $success_message = 'تم حذف الحجز بنجاح';
    } catch(PDOException $e) {
        $error_message = 'حدث خطأ أثناء حذف الحجز';
    }
}

// Filter and pagination
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page_num = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

// Build query
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "b.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $where_conditions[] = "(b.client_name LIKE ? OR b.client_phone LIKE ? OR b.client_whatsapp LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total count
$count_query = "SELECT COUNT(*) FROM bookings b $where_clause";
$stmt = $conn->prepare($count_query);
$stmt->execute($params);
$total_bookings = $stmt->fetchColumn();
$total_pages = ceil($total_bookings / $per_page);

// Fetch bookings
$query = "
    SELECT b.*, p.name as package_name, p.price
    FROM bookings b
    LEFT JOIN packages p ON b.package_id = p.id
    $where_clause
    ORDER BY b.created_at DESC
    LIMIT ? OFFSET ?
";
$params[] = $per_page;
$params[] = $offset;
$stmt = $conn->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>إدارة الحجوزات</h1>
    <p>عرض وإدارة جميع الحجوزات</p>
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

<!-- Filters -->
<div class="card">
    <div class="card-body">
        <form method="GET" class="flex align-center gap-10" style="gap: 15px;">
            <input type="text" name="search" placeholder="بحث بالاسم أو الهاتف..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; padding: 10px; border: 2px solid var(--border-color); border-radius: 6px;">

            <select name="status" style="padding: 10px; border: 2px solid var(--border-color); border-radius: 6px;">
                <option value="">جميع الحالات</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>قيد الانتظار</option>
                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>مؤكد</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>مكتمل</option>
                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>ملغي</option>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> بحث
            </button>

            <?php if ($search || $status_filter): ?>
            <a href="bookings.php" class="btn btn-warning">
                <i class="fas fa-times"></i> إعادة تعيين
            </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Bookings Table -->
<div class="card">
    <div class="card-header">
        <h2>الحجوزات (<?php echo $total_bookings; ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (count($bookings) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>واتساب</th>
                        <th>الباقة</th>
                        <th>التاريخ</th>
                        <th>اليوم</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking['id']; ?></td>
                        <td><?php echo htmlspecialchars($booking['client_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['client_phone']); ?></td>
                        <td><?php echo htmlspecialchars($booking['client_whatsapp']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($booking['package_name']); ?>
                            <br>
                            <small style="color: var(--text-light);"><?php echo formatPrice($booking['price']); ?></small>
                        </td>
                        <td><?php echo formatDate($booking['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_day']); ?></td>
                        <td>
                            <?php
                            $status_badges = [
                                'pending' => 'warning',
                                'confirmed' => 'success',
                                'cancelled' => 'danger',
                                'completed' => 'info'
                            ];
                            $status_labels = [
                                'pending' => 'قيد الانتظار',
                                'confirmed' => 'مؤكد',
                                'cancelled' => 'ملغي',
                                'completed' => 'مكتمل'
                            ];
                            $badge_class = $status_badges[$booking['status']] ?? 'primary';
                            $status_label = $status_labels[$booking['status']] ?? $booking['status'];
                            ?>
                            <span class="badge badge-<?php echo $badge_class; ?>">
                                <?php echo $status_label; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button onclick="viewBooking(<?php echo $booking['id']; ?>)" class="btn btn-sm btn-primary" title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="sendWhatsApp(<?php echo $booking['id']; ?>, '<?php echo htmlspecialchars($booking['client_whatsapp']); ?>', '<?php echo htmlspecialchars($booking['client_name']); ?>', '<?php echo formatDate($booking['booking_date']); ?>', '<?php echo htmlspecialchars($booking['booking_day']); ?>', '<?php echo number_format($booking['price'], 0); ?>')" class="btn btn-sm btn-success" title="إرسال واتساب">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                                <button onclick="updateStatus(<?php echo $booking['id']; ?>, '<?php echo $booking['status']; ?>')" class="btn btn-sm btn-warning" title="تحديث الحالة">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteBooking(<?php echo $booking['id']; ?>)" class="btn btn-sm btn-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
            <?php if ($page_num > 1): ?>
            <a href="?page=<?php echo $page_num - 1; ?><?php echo $status_filter ? '&status=' . urlencode($status_filter) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-primary btn-sm">السابق</a>
            <?php endif; ?>

            <?php for ($i = max(1, $page_num - 2); $i <= min($total_pages, $page_num + 2); $i++): ?>
                <?php if ($i == $page_num): ?>
                <span class="btn btn-primary btn-sm"><?php echo $i; ?></span>
                <?php else: ?>
                <a href="?page=<?php echo $i; ?><?php echo $status_filter ? '&status=' . urlencode($status_filter) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-sm" style="background: var(--bg-light);"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page_num < $total_pages): ?>
            <a href="?page=<?php echo $page_num + 1; ?><?php echo $status_filter ? '&status=' . urlencode($status_filter) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-primary btn-sm">التالي</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <p class="text-center" style="padding: 40px; color: var(--text-light);">
            <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
            لا توجد حجوزات
        </p>
        <?php endif; ?>
    </div>
</div>

<!-- View Booking Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>تفاصيل الحجز</h3>
            <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
        </div>
        <div class="modal-body" id="viewModalBody">
            <!-- Content will be loaded via JS -->
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>تحديث حالة الحجز</h3>
            <button class="modal-close" onclick="closeModal('statusModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST">
                <input type="hidden" name="booking_id" id="status_booking_id">
                <div class="form-group">
                    <label>الحالة</label>
                    <select name="status" id="status_select" class="form-control">
                        <option value="pending">قيد الانتظار</option>
                        <option value="confirmed">مؤكد</option>
                        <option value="completed">مكتمل</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> حفظ
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function viewBooking(id) {
    fetch('../api/get-booking.php?id=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const booking = data.booking;
                document.getElementById('viewModalBody').innerHTML = `
                    <table style="width: 100%;">
                        <tr><td style="padding: 10px; font-weight: bold;">رقم الحجز:</td><td style="padding: 10px;">${booking.id}</td></tr>
                        <tr><td style="padding: 10px; font-weight: bold;">اسم العميل:</td><td style="padding: 10px;">${booking.client_name}</td></tr>
                        <tr><td style="padding: 10px; font-weight: bold;">العنوان:</td><td style="padding: 10px;">${booking.client_address || '-'}</td></tr>
                        <tr><td style="padding: 10px; font-weight: bold;">الهاتف:</td><td style="padding: 10px;">${booking.client_phone}</td></tr>
                        <tr><td style="padding: 10px; font-weight: bold;">واتساب:</td><td style="padding: 10px;">${booking.client_whatsapp}</td></tr>
                        <tr><td style="padding: 10px; font-weight: bold;">الباقة:</td><td style="padding: 10px;">${booking.package_name}</td></tr>
                        <tr><td style="padding: 10px; font-weight: bold;">السعر:</td><td style="padding: 10px;">${booking.price} جنيه</td></tr>
                        <tr><td style="padding: 10px; font-weight: bold;">تاريخ الحجز:</td><td style="padding: 10px;">${booking.booking_date}</td></tr>
                        <tr><td style="padding: 10px; font-weight: bold;">اليوم:</td><td style="padding: 10px;">${booking.booking_day}</td></tr>
                        <tr><td style="padding: 10px; font-weight: bold;">الحالة:</td><td style="padding: 10px;">${booking.status}</td></tr>
                        ${booking.notes ? `<tr><td style="padding: 10px; font-weight: bold;">ملاحظات:</td><td style="padding: 10px;">${booking.notes}</td></tr>` : ''}
                        <tr><td style="padding: 10px; font-weight: bold;">تاريخ الإنشاء:</td><td style="padding: 10px;">${booking.created_at}</td></tr>
                    </table>
                `;
                openModal('viewModal');
            }
        });
}

function updateStatus(id, currentStatus) {
    document.getElementById('status_booking_id').value = id;
    document.getElementById('status_select').value = currentStatus;
    openModal('statusModal');
}

function deleteBooking(id) {
    if (confirm('هل أنت متأكد من حذف هذا الحجز؟')) {
        window.location.href = 'bookings.php?delete=' + id;
    }
}

function sendWhatsApp(id, whatsapp, name, date, day, price) {
    const message = `مرحباً ${name}،\n\nنؤكد لك حجزك في العيادة:\n\n📅 التاريخ: ${date} (${day})\n💰 التكلفة: ${price} جنيه\n\nنتطلع لرؤيتك!\n\nمع تحياتنا،\nعيادة الدكتور`;

    const url = `https://wa.me/${whatsapp.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(message)}`;

    // Mark as sent
    fetch('../api/mark-whatsapp-sent.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({booking_id: id})
    });

    window.open(url, '_blank');
}

function openModal(id) {
    document.getElementById(id).classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

// Close modal on backdrop click
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
