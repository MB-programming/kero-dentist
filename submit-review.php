<?php
require_once 'includes/config.php';

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = sanitize($_POST['client_name']);
    $client_email = sanitize($_POST['client_email'] ?? '');
    $rating = intval($_POST['rating']);
    $review_text = sanitize($_POST['review_text']);

    if (empty($client_name) || $rating < 1 || $rating > 5 || empty($review_text)) {
        $error_message = 'يرجى ملء جميع الحقول المطلوبة';
    } else {
        try {
            $stmt = $conn->prepare("
                INSERT INTO reviews (client_name, client_email, rating, review_text, source, is_approved, is_displayed, created_at)
                VALUES (?, ?, ?, ?, 'email', 0, 0, NOW())
            ");

            if ($stmt->execute([$client_name, $client_email, $rating, $review_text])) {
                $success_message = 'شكراً لك! تم إرسال تقييمك بنجاح. سيتم مراجعته قريباً.';
                // Clear form
                $_POST = [];
            } else {
                $error_message = 'حدث خطأ. يرجى المحاولة مرة أخرى.';
            }
        } catch (PDOException $e) {
            $error_message = 'حدث خطأ في النظام. يرجى المحاولة لاحقاً.';
        }
    }
}

$page_title = 'قيم تجربتك معنا';
$page_description = 'شارك رأيك حول خدماتنا وساعد الآخرين في اتخاذ قرارهم';
?>
<?php include 'includes/header.php'; ?>

<style>
.review-form-section {
    background: linear-gradient(135deg, #f8f9ff 0%, #fff5f7 100%);
    padding: 100px 0 80px;
    min-height: 100vh;
}

.review-form-container {
    max-width: 700px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
    padding: 50px;
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.review-header {
    text-align: center;
    margin-bottom: 40px;
}

.review-header h1 {
    font-size: 2.5rem;
    color: #1e293b;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #0ea5e9, #10b981);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.review-header p {
    font-size: 1.1rem;
    color: #64748b;
}

.star-rating {
    text-align: center;
    margin: 30px 0;
}

.star-rating label {
    font-size: 1.2rem;
    color: #1e293b;
    display: block;
    margin-bottom: 15px;
    font-weight: 600;
}

.stars {
    display: inline-flex;
    gap: 10px;
    direction: ltr;
}

.star {
    font-size: 3rem;
    color: #e2e8f0;
    cursor: pointer;
    transition: all 0.3s ease;
}

.star:hover,
.star.active {
    color: #fbbf24;
    transform: scale(1.2);
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #1e293b;
    font-weight: 600;
    font-size: 1rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.form-group textarea {
    min-height: 150px;
    resize: vertical;
}

.submit-btn {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #0ea5e9, #10b981);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.2rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(14, 165, 233, 0.3);
}

.submit-btn:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
}

.alert {
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    text-align: center;
    font-size: 1.1rem;
}

.alert-success {
    background: #f0fdf4;
    border: 2px solid #10b981;
    color: #065f46;
}

.alert-danger {
    background: #fef2f2;
    border: 2px solid #ef4444;
    color: #991b1b;
}

.back-link {
    text-align: center;
    margin-top: 30px;
}

.back-link a {
    color: #0ea5e9;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.back-link a:hover {
    color: #10b981;
}

@media (max-width: 768px) {
    .review-form-container {
        padding: 30px 20px;
        margin: 20px;
    }

    .review-header h1 {
        font-size: 2rem;
    }

    .star {
        font-size: 2.5rem;
    }
}
</style>

<div class="review-form-section">
    <div class="container">
        <div class="review-form-container">
            <div class="review-header">
                <h1>✨ قيم تجربتك معنا</h1>
                <p>رأيك يهمنا! شارك تجربتك لمساعدة الآخرين</p>
            </div>

            <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <?php if (!isset($success_message)): ?>
            <form method="POST" id="reviewForm">
                <div class="star-rating">
                    <label>كم نجمة تعطينا؟</label>
                    <div class="stars">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" required>
                </div>

                <div class="form-group">
                    <label>الاسم الكامل <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="client_name" value="<?php echo htmlspecialchars($_POST['client_name'] ?? ''); ?>" required placeholder="أدخل اسمك">
                </div>

                <div class="form-group">
                    <label>البريد الإلكتروني (اختياري)</label>
                    <input type="email" name="client_email" value="<?php echo htmlspecialchars($_POST['client_email'] ?? ''); ?>" placeholder="example@email.com">
                </div>

                <div class="form-group">
                    <label>رأيك وتجربتك <span style="color: #ef4444;">*</span></label>
                    <textarea name="review_text" required placeholder="شاركنا تجربتك مع خدماتنا... ما الذي أعجبك؟ هل كانت الخدمة على مستوى توقعاتك؟"><?php echo htmlspecialchars($_POST['review_text'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn" disabled>
                    <i class="fas fa-paper-plane"></i> إرسال التقييم
                </button>
            </form>
            <?php endif; ?>

            <div class="back-link">
                <a href="index.php">
                    <i class="fas fa-arrow-right"></i> العودة للصفحة الرئيسية
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Star rating functionality
const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('ratingInput');
const submitBtn = document.getElementById('submitBtn');
let selectedRating = 0;

stars.forEach(star => {
    star.addEventListener('click', function() {
        selectedRating = this.getAttribute('data-rating');
        ratingInput.value = selectedRating;
        updateStars(selectedRating);
        submitBtn.disabled = false;
    });

    star.addEventListener('mouseenter', function() {
        const rating = this.getAttribute('data-rating');
        updateStars(rating);
    });
});

document.querySelector('.stars').addEventListener('mouseleave', function() {
    updateStars(selectedRating);
});

function updateStars(rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

// Form validation
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    if (!selectedRating) {
        e.preventDefault();
        alert('يرجى اختيار تقييم بالنجوم');
        return false;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
});
</script>

<?php include 'includes/footer.php'; ?>
