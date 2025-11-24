<?php
require_once '../includes/header.php';
require_once '../models/Blog.php';

$blogModel = new Blog();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Lấy danh sách bài viết
$posts = $blogModel->getAllPosts($limit, $offset);
$totalPosts = $blogModel->countPosts();
$totalPages = ceil($totalPosts / $limit);

// Lấy bài viết nổi bật
$featuredPosts = $blogModel->getFeaturedPosts(3);

// Lấy danh mục
$categories = $blogModel->getAllCategories();

// Lấy bài viết phổ biến
$popularPosts = $blogModel->getPopularPosts(5);

$baseUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
?>

<link rel="stylesheet" href="<?= $baseUrl ?>/css/blog.css">

<div class="blog-page">
    <!-- Hero Section -->
    <section class="blog-hero">
        <div class="blog-hero-content">
            <h1>Tin Tức & Blog</h1>
            <p>Khám phá thế giới sách qua những bài viết, review và tin tức văn học mới nhất</p>
        </div>
    </section>

    <!-- Featured Posts -->
    <?php if (!empty($featuredPosts)): ?>
    <section class="featured-section">
        <h2>Bài Viết Nổi Bật</h2>
        <div class="featured-grid">
            <?php foreach ($featuredPosts as $post): ?>
                <article class="featured-card">
                    <?php if ($post->anh_dai_dien): ?>
                        <div class="featured-image">
                            <img src="<?= $baseUrl ?>/uploads/blog/<?= $post->anh_dai_dien ?>" alt="<?= htmlspecialchars($post->tieu_de) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="featured-content">
                        <span class="category-badge"><?= htmlspecialchars($post->ten_danh_muc) ?></span>
                        <h3><a href="blog_detail.php?slug=<?= htmlspecialchars($post->slug) ?>"><?= htmlspecialchars($post->tieu_de) ?></a></h3>
                        <p><?= htmlspecialchars(mb_substr($post->tom_tat, 0, 150)) ?>...</p>
                        <div class="post-meta">
                            <span>📅 <?= date('d/m/Y', strtotime($post->ngay_xuat_ban)) ?></span>
                            <span>👁️ <?= number_format($post->luot_xem) ?> lượt xem</span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <div class="blog-layout">
        <!-- Main Content -->
        <main class="blog-main">
            <h2>Tất Cả Bài Viết</h2>
            
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <p>Chưa có bài viết nào.</p>
                </div>
            <?php else: ?>
                <div class="posts-grid">
                    <?php foreach ($posts as $post): ?>
                        <article class="post-card">
                            <?php if ($post->anh_dai_dien): ?>
                                <div class="post-image">
                                    <a href="blog_detail.php?slug=<?= htmlspecialchars($post->slug) ?>">
                                        <img src="<?= $baseUrl ?>/uploads/blog/<?= $post->anh_dai_dien ?>" alt="<?= htmlspecialchars($post->tieu_de) ?>">
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="post-content">
                                <span class="category-badge"><?= htmlspecialchars($post->ten_danh_muc) ?></span>
                                <h3><a href="blog_detail.php?slug=<?= htmlspecialchars($post->slug) ?>"><?= htmlspecialchars($post->tieu_de) ?></a></h3>
                                <p><?= htmlspecialchars(mb_substr($post->tom_tat, 0, 120)) ?>...</p>
                                <div class="post-meta">
                                    <span>📅 <?= date('d/m/Y', strtotime($post->ngay_xuat_ban)) ?></span>
                                    <span>👁️ <?= number_format($post->luot_xem) ?></span>
                                </div>
                                <a href="blog_detail.php?slug=<?= htmlspecialchars($post->slug) ?>" class="read-more">Đọc tiếp →</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="page-link">← Trước</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="page-link">Sau →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>

        <!-- Sidebar -->
        <aside class="blog-sidebar">
            <!-- Categories -->
            <div class="sidebar-widget">
                <h3>Danh Mục</h3>
                <ul class="category-list">
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="blog_category.php?slug=<?= htmlspecialchars($cat->slug) ?>">
                                <?= htmlspecialchars($cat->ten_danh_muc) ?>
                                <span class="count">(<?= $cat->so_bai_viet ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Popular Posts -->
            <?php if (!empty($popularPosts)): ?>
                <div class="sidebar-widget">
                    <h3>Bài Viết Phổ Biến</h3>
                    <ul class="popular-list">
                        <?php foreach ($popularPosts as $popular): ?>
                            <li>
                                <a href="blog_detail.php?slug=<?= htmlspecialchars($popular->slug) ?>">
                                    <h4><?= htmlspecialchars($popular->tieu_de) ?></h4>
                                    <span class="views">👁️ <?= number_format($popular->luot_xem) ?> lượt xem</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
