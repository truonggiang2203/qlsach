<?php
require_once '../includes/header.php';
require_once '../models/Blog.php';

$blogModel = new Blog();
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header("Location: blog.php");
    exit;
}

$post = $blogModel->getPostBySlug($slug);

if (!$post) {
    header("Location: blog.php");
    exit;
}

// Tăng lượt xem
$blogModel->incrementViews($post->id_bai_viet);

// Lấy tags
$tags = $blogModel->getPostTags($post->id_bai_viet);

// Lấy bài viết liên quan
$relatedPosts = $blogModel->getRelatedPosts($post->id_bai_viet, $post->id_danh_muc, 3);

$baseUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
?>

<link rel="stylesheet" href="<?= $baseUrl ?>/css/blog.css">

<div class="blog-detail-page">
    <article class="blog-article">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="<?= $baseUrl ?>/index.php">Trang chủ</a> / 
            <a href="blog.php">Blog</a> / 
            <a href="blog_category.php?slug=<?= htmlspecialchars($post->danh_muc_slug) ?>"><?= htmlspecialchars($post->ten_danh_muc) ?></a> /
            <span><?= htmlspecialchars($post->tieu_de) ?></span>
        </nav>

        <!-- Article Header -->
        <header class="article-header">
            <span class="category-badge"><?= htmlspecialchars($post->ten_danh_muc) ?></span>
            <h1><?= htmlspecialchars($post->tieu_de) ?></h1>
            <div class="article-meta">
                <span>✍️ <?= htmlspecialchars($post->tac_gia) ?></span>
                <span>📅 <?= date('d/m/Y H:i', strtotime($post->ngay_xuat_ban)) ?></span>
                <span>👁️ <?= number_format($post->luot_xem) ?> lượt xem</span>
            </div>
        </header>

        <!-- Featured Image -->
        <?php if ($post->anh_dai_dien): ?>
            <div class="article-image">
                <img src="<?= $baseUrl ?>/uploads/blog/<?= $post->anh_dai_dien ?>" alt="<?= htmlspecialchars($post->tieu_de) ?>">
            </div>
        <?php endif; ?>

        <!-- Article Content -->
        <div class="article-content">
            <?= $post->noi_dung ?>
        </div>

        <!-- Tags -->
        <?php if (!empty($tags)): ?>
            <div class="article-tags">
                <strong>Tags:</strong>
                <?php foreach ($tags as $tag): ?>
                    <a href="blog.php?tag=<?= htmlspecialchars($tag->slug) ?>" class="tag"><?= htmlspecialchars($tag->ten_tag) ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Share Buttons -->
        <div class="article-share">
            <strong>Chia sẻ:</strong>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" class="share-btn facebook">Facebook</a>
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($post->tieu_de) ?>" target="_blank" class="share-btn twitter">Twitter</a>
        </div>
    </article>

    <!-- Related Posts -->
    <?php if (!empty($relatedPosts)): ?>
        <section class="related-posts">
            <h2>Bài Viết Liên Quan</h2>
            <div class="posts-grid">
                <?php foreach ($relatedPosts as $related): ?>
                    <article class="post-card">
                        <?php if ($related->anh_dai_dien): ?>
                            <div class="post-image">
                                <a href="blog_detail.php?slug=<?= htmlspecialchars($related->slug) ?>">
                                    <img src="<?= $baseUrl ?>/uploads/blog/<?= $related->anh_dai_dien ?>" alt="<?= htmlspecialchars($related->tieu_de) ?>">
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="post-content">
                            <span class="category-badge"><?= htmlspecialchars($related->ten_danh_muc) ?></span>
                            <h3><a href="blog_detail.php?slug=<?= htmlspecialchars($related->slug) ?>"><?= htmlspecialchars($related->tieu_de) ?></a></h3>
                            <div class="post-meta">
                                <span>📅 <?= date('d/m/Y', strtotime($related->ngay_xuat_ban)) ?></span>
                                <span>👁️ <?= number_format($related->luot_xem) ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
