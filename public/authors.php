<?php
require_once '../includes/header.php';
require_once '../models/Author.php';

$authorModel = new Author();

// Lấy từ khóa tìm kiếm
$keyword = trim($_GET['keyword'] ?? '');

// Lấy danh sách tác giả
if (!empty($keyword)) {
    $authors = $authorModel->searchAuthors($keyword);
} else {
    $authors = $authorModel->getAllAuthors();
}

$totalAuthors = count($authors);
?>

<link rel="stylesheet" href="<?= rtrim(dirname($_SERVER['PHP_SELF']), '/\\') ?>/css/authors.css">

<div class="authors-page full-width-authors">
    <!-- Hero Section -->
    <section class="authors-hero">
        <div class="authors-hero-content">
            <p>Khám phá</p>
            <h1>Tác giả nổi tiếng</h1>
            <span>Tìm hiểu về các tác giả và tác phẩm của họ</span>
        </div>
    </section>

    <!-- Search Bar -->
    <section class="authors-search">
        <form action="authors.php" method="GET" class="search-form">
            <input 
                type="text" 
                name="keyword" 
                value="<?= htmlspecialchars($keyword) ?>" 
                placeholder="Tìm kiếm tác giả..." 
                class="search-input"
            >
            <button type="submit" class="search-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                Tìm kiếm
            </button>
        </form>
        <?php if (!empty($keyword)): ?>
            <p class="search-result">Tìm thấy <strong><?= $totalAuthors ?></strong> tác giả với từ khóa "<?= htmlspecialchars($keyword) ?>"</p>
            <a href="authors.php" class="clear-search">✕ Xóa tìm kiếm</a>
        <?php endif; ?>
    </section>

    <!-- Authors Grid -->
    <section class="authors-list">
        <div class="section-header">
            <h2>Danh sách tác giả</h2>
            <p><?= $totalAuthors ?> tác giả</p>
        </div>

        <?php if (empty($authors)): ?>
            <div class="empty-state">
                <h3>Không tìm thấy tác giả</h3>
                <p>Hãy thử tìm kiếm với từ khóa khác.</p>
                <a href="authors.php" class="btn-primary">Xem tất cả tác giả</a>
            </div>
        <?php else: ?>
            <div class="authors-grid">
                <?php foreach ($authors as $author): ?>
                    <a href="author_detail.php?id=<?= htmlspecialchars($author->id_tac_gia) ?>" class="author-card">
                        <div class="author-card-avatar">
                            <span class="avatar-icon">✍️</span>
                        </div>
                        <div class="author-card-info">
                            <h3><?= htmlspecialchars($author->ten_tac_gia) ?></h3>
                            <p class="book-count">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <?= $author->book_count ?> tác phẩm
                            </p>
                        </div>
                        <div class="author-card-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include_once '../includes/footer.php'; ?>