<?php
include('../config/db.php');
include('../includes/header.php');

$where = [];
if (!empty($_GET['keyword'])) {
    $kw = mysqli_real_escape_string($conn, $_GET['keyword']);
    $where[] = "ten_sach LIKE '%$kw%'";
}
if (!empty($_GET['category'])) {
    $cat = mysqli_real_escape_string($conn, $_GET['category']);
    $where[] = "id_loai = '$cat'";
}
if (!empty($_GET['min_price']) && !empty($_GET['max_price'])) {
    $min = (int)$_GET['min_price'];
    $max = (int)$_GET['max_price'];
    $where[] = "gia_sach_ban BETWEEN $min AND $max";
}

$sql = "SELECT s.*, g.gia_sach_ban, l.ten_loai 
        FROM sach s 
        JOIN gia_sach g ON s.id_sach = g.id_sach
        JOIN loai_sach l ON s.id_loai = l.id_loai
        WHERE 1";
if ($where) $sql .= " AND " . implode(' AND ', $where);
$sql .= " ORDER BY g.gia_sach_ban ASC";

$result = mysqli_query($conn, $sql);
?>

<h2>Tìm kiếm nâng cao</h2>
<form method="GET">
    Từ khóa: <input type="text" name="keyword" value="<?= $_GET['keyword'] ?? '' ?>">
    <br>Thể loại:
    <select name="category">
        <option value="">--Tất cả--</option>
        <?php
        $cats = mysqli_query($conn, "SELECT * FROM loai_sach");
        while ($c = mysqli_fetch_assoc($cats))
            echo "<option value='{$c['id_loai']}'" . 
                 (($c['id_loai']==($_GET['category']??''))?'selected':'') . 
                 ">{$c['ten_loai']}</option>";
        ?>
    </select>
    <br>Giá từ: <input type="number" name="min_price"> đến <input type="number" name="max_price">
    <br><button type="submit">Tìm</button>
</form>

<hr>
<h3>Kết quả:</h3>
<?php while ($r = mysqli_fetch_assoc($result)): ?>
<div style="border:1px solid #ccc; margin:5px; padding:10px;">
    <b><?= $r['ten_sach'] ?></b> - <?= number_format($r['gia_sach_ban']) ?> đ <br>
    <small>Thể loại: <?= $r['ten_loai'] ?></small>
</div>
<?php endwhile; ?>

<?php include('../includes/footer.php'); ?>
