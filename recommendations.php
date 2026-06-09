<?php

require_once __DIR__ . '/includes/layout.php';
require_login();

// এআই সুপারিশ ইঞ্জিন লোড করুন
require_once __DIR__ . '/includes/ai-recommender.php';

$userEmail = $_SESSION['user_email'];
$config = include 'config.php';
if (!is_array($config)) {
    $config = [];
}
$config = array_merge([
    'recommendation_limit' => 5,
    'ai_recommendation_enabled' => true,
    'min_rating_threshold' => 3,
    'collaboration_boost' => 2.0,
    'enable_openlibrary' => true,
], $config);

$recommender = new BookRecommender(db(), $config);

// রেটিং সংরক্ষণ করুন
if (is_post() && isset($_POST['action'])) {
    if ($_POST['action'] === 'rate_book') {
        $bookName = trim($_POST['book_name'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 0);
        $review = trim($_POST['review'] ?? '');
        
        if ($rating >= 1 && $rating <= 5 && !empty($bookName)) {
            $success = $recommender->addOrUpdateRating($userEmail, $bookName, $rating, $review);
            set_flash($success ? 'ধন্যবাদ! আপনার রেটিং সংরক্ষিত হয়েছে। 🎯' : 'রেটিং সংরক্ষণে ত্রুটি', $success ? 'success' : 'error');
        }
    }
    redirect_to('recommendations.php');
}

// শিক্ষার্থী কিনা তা পরীক্ষা করুন
$isStudent = (int) fetch_one(
    'SELECT COUNT(*) as count FROM students WHERE email = ?',
    [$userEmail]
)['count'] > 0;

if (!$isStudent) {
    // প্রশাসকদের জন্য জনপ্রিয় বই দেখান
    $recommendations = $recommender->getPopularBooks($config['recommendation_limit']);
    $title = 'জনপ্রিয় বই';
} else {
    // শিক্ষার্থীর জন্য ব্যক্তিগতকৃত সুপারিশ
    $recommendations = $recommender->getRecommendations($userEmail, $config['recommendation_limit']);
    if (empty($recommendations)) {
        $recommendations = $recommender->getPopularBooks($config['recommendation_limit']);
    }
    $title = 'আপনার জন্য সুপারিশকৃত বই 🤖';
}

// ব্যবহারকারীর বর্তমান রেটিং পান
$userRatings = [];
$ratingsStmt = db()->prepare('SELECT book_name, rating, review FROM book_ratings WHERE student_email = ?');
$ratingsStmt->execute([$userEmail]);
foreach ($ratingsStmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $userRatings[$r['book_name']] = $r;
}

render_header('বই সুপারিশ');
?>

<!-- সুপারিশ হিরো -->
<div class="recommendation-hero">
    <h1>📚 <?php echo htmlspecialchars($title); ?></h1>
    <p>আপনার পছন্দ অনুযায়ী নির্বাচিত সেরা বই</p>
</div>

<?php if (count($recommendations) > 0): ?>
    <!-- সুপারিশ কার্ড -->
    <div class="recommendations-container">
        <?php foreach ($recommendations as $book): ?>
            <div class="recommendation-card" data-book="<?php echo htmlspecialchars($book['book_name']); ?>">
                <div class="rec-image">
                    <?php 
                        // OpenLibrary থেকে কভার প্রথমে, তারপর স্থানীয় ইমেজ
                        $coverUrl = $book['cover_url'] ?? null;
                        if (empty($coverUrl) && !empty($book['book_image']) && file_exists($book['book_image'])) {
                            $coverUrl = htmlspecialchars($book['book_image']);
                        }
                        
                        if (!empty($coverUrl)):
                    ?>
                        <img src="<?php echo htmlspecialchars($coverUrl); ?>" alt="<?php echo htmlspecialchars($book['book_name']); ?>" onerror="this.style.display='none'">
                    <?php endif; ?>
                    <?php if (empty($coverUrl)): ?>
                        📖
                    <?php endif; ?>
                </div>
                
                <div class="rec-content">
                    <div class="rec-title"><?php echo htmlspecialchars($book['book_name']); ?></div>
                    <div class="rec-author">লেখক: <?php echo htmlspecialchars($book['author_name'] ?? 'অজানা'); ?></div>
                    
                    <?php if (!empty($book['reason'])): ?>
                        <div class="rec-reason">✨ <?php echo htmlspecialchars($book['reason']); ?></div>
                    <?php endif; ?>
                    
                    <!-- স্থানীয় রেটিং -->
                    <?php if (isset($book['community_rating']) && $book['rating_count'] > 0): ?>
                        <div class="rec-rating">
                            <div class="stars">
                                <?php 
                                    $rating = round($book['community_rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '⭐' : '☆';
                                    }
                                ?>
                            </div>
                            <span><?php echo number_format($book['community_rating'], 1); ?>/5</span>
                            <span class="rating-count">(<?php echo $book['rating_count']; ?> আমাদের রেটিং)</span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- বৈশ্বিক রেটিং (OpenLibrary) -->
                    <?php if (isset($book['global_rating']) && $book['global_rating'] > 0): ?>
                        <div class="rec-rating rec-rating-global">
                            <div class="stars">
                                <?php 
                                    $grating = round($book['global_rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $grating ? '⭐' : '☆';
                                    }
                                ?>
                            </div>
                            <span><?php echo number_format($book['global_rating'], 1); ?>/5</span>
                            <span class="rating-count">(সারা বিশ্বে <?php echo $book['global_ratings_count']; ?>)</span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- বই বিবরণ -->
                    <?php if (!empty($book['description'])): ?>
                        <div class="rec-description">
                            <?php echo htmlspecialchars(substr($book['description'], 0, 100)) . '...'; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- অতিরিক্ত তথ্য (ISBN, পৃষ্ঠা, প্রকাশনা বছর) -->
                    <div class="rec-meta">
                        <?php if (!empty($book['isbn'])): ?>
                            <div>📚 ISBN: <?php echo htmlspecialchars($book['isbn']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($book['pages'])): ?>
                            <div>📄 পৃষ্ঠা: <?php echo $book['pages']; ?></div>
                        <?php endif; ?>
                        <?php if (!empty($book['first_publish_year'])): ?>
                            <div>📅 প্রকাশনা: <?php echo $book['first_publish_year']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="availability <?php echo $book['available'] ? 'available' : 'unavailable'; ?>">
                        <?php 
                            if ($book['available']) {
                                echo "✓ উপলব্ধ (" . $book['available_quantity'] . " কপি)";
                            } else {
                                echo "✗ বর্তমানে স্টক শেষ";
                            }
                        ?>
                    </div>
                    
                    <div class="rec-actions">
                        <?php if ($book['available']): ?>
                            <button class="btn-request" onclick="requestBook('<?php echo htmlspecialchars($book['book_name']); ?>')">
                                বই চাওয়া করুন
                            </button>
                        <?php endif; ?>
                        <button class="btn-rate" onclick="openRatingModal('<?php echo htmlspecialchars($book['book_name']); ?>')">
                            ⭐ রেটিং দিন
                        </button>
                    </div>
                    
                    <?php if (isset($userRatings[$book['book_name']])): ?>
                        <div class="user-rating-badge">
                            <strong>আপনার রেটিং:</strong> 
                            <?php 
                                $ur = $userRatings[$book['book_name']];
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $ur['rating'] ? '⭐' : '☆';
                                }
                                echo ' (' . $ur['rating'] . '/5)';
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-recommendations">
        <div class="no-recommendations-icon">🔍</div>
        <h2>এখনও কোনো সুপারিশ নেই</h2>
        <p>কয়েকটি বইতে রেটিং দিন এবং আমরা আপনার জন্য সেরা বই খুঁজে দেব।</p>
    </div>
<?php endif; ?>

<!-- রেটিং মডাল -->
<div class="rating-modal" id="ratingModal">
    <div class="rating-modal-content">
        <div class="modal-title" id="modalTitle">বই রেট করুন</div>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="rate_book">
            <input type="hidden" name="book_name" id="modalBookName">
            
            <div class="rating-input">
                <label>আপনার রেটিং:</label>
                <div class="star-rating" id="starRating">
                    <label class="star-label">
                        <input type="radio" name="rating" value="1" hidden required>
                        <span class="star" data-value="1">⭐</span>
                    </label>
                    <label class="star-label">
                        <input type="radio" name="rating" value="2" hidden>
                        <span class="star" data-value="2">⭐</span>
                    </label>
                    <label class="star-label">
                        <input type="radio" name="rating" value="3" hidden>
                        <span class="star" data-value="3">⭐</span>
                    </label>
                    <label class="star-label">
                        <input type="radio" name="rating" value="4" hidden>
                        <span class="star" data-value="4">⭐</span>
                    </label>
                    <label class="star-label">
                        <input type="radio" name="rating" value="5" hidden>
                        <span class="star" data-value="5">⭐</span>
                    </label>
                </div>
            </div>
            
            <div class="rating-input">
                <label for="review">আপনার মতামত (ঐচ্ছিক):</label>
                <textarea class="review-textarea" name="review" id="review" placeholder="এই বইটি সম্পর্কে আপনার চিন্তা শেয়ার করুন..."></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="submit" class="btn-submit">রেটিং জমা দিন</button>
                <button type="button" class="btn-cancel" onclick="closeRatingModal()">বাতিল করুন</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRatingModal(bookName) {
    document.getElementById('ratingModal').classList.add('active');
    document.getElementById('modalBookName').value = bookName;
    document.getElementById('modalTitle').textContent = 'রেট করুন: ' + bookName;
    resetRating();
}

function closeRatingModal() {
    document.getElementById('ratingModal').classList.remove('active');
    resetRating();
}

function resetRating() {
    const reviewInput = document.getElementById('review');
    const stars = document.querySelectorAll('#starRating .star');
    const ratingRadios = document.querySelectorAll('#starRating input[name="rating"]');

    ratingRadios.forEach(radio => radio.checked = false);
    if (reviewInput) {
        reviewInput.value = '';
    }
    stars.forEach(star => {
        star.classList.remove('active');
        star.style.color = '#ddd';
    });
}

function ensureRatingSetup() {
    const stars = document.querySelectorAll('#starRating .star');
    const ratingRadios = document.querySelectorAll('#starRating input[name="rating"]');
    const ratingForm = document.querySelector('#ratingModal form');

    stars.forEach((star, idx) => {
        star.style.cursor = 'pointer';
        star.addEventListener('click', function() {
            const value = Number(this.dataset.value);
            ratingRadios.forEach((radio, radioIdx) => {
                radio.checked = radioIdx === idx;
            });
            stars.forEach((s, sIdx) => {
                s.classList.toggle('active', sIdx < value);
                s.style.color = sIdx < value ? '#ffc107' : '#ddd';
            });
        });

        star.addEventListener('mouseover', function() {
            const value = Number(this.dataset.value);
            stars.forEach((s, sIdx) => {
                s.style.color = sIdx < value ? '#ffc107' : '#ddd';
            });
        });
    });

    const starRating = document.getElementById('starRating');
    if (starRating) {
        starRating.addEventListener('mouseleave', function() {
            const checkedRadio = document.querySelector('#starRating input[name="rating"]:checked');
            const currentValue = Number(checkedRadio?.value || 0);
            stars.forEach((s, sIdx) => {
                s.style.color = sIdx < currentValue ? '#ffc107' : '#ddd';
            });
        });
    }

    if (ratingForm) {
        ratingForm.addEventListener('submit', function(event) {
            const checkedRadio = document.querySelector('#starRating input[name="rating"]:checked');
            const selectedRating = Number(checkedRadio?.value || 0);
            if (selectedRating < 1 || selectedRating > 5) {
                event.preventDefault();
                alert('অনুগ্রহ করে ১ থেকে ৫ এর মধ্যে একটি রেটিং নির্বাচন করুন।');
            }
        });
    }
}

// মডাল ক্লিক করে বন্ধ করুন
document.getElementById('ratingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRatingModal();
    }
});

function requestBook(bookName) {
    alert('বই চাওয়ার সিস্টেম শীঘ্রই আসছে। 📚');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ensureRatingSetup);
} else {
    ensureRatingSetup();
}
</script>

<?php render_footer(); ?>
