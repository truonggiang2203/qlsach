<?php
session_start();
include('../includes/header.php');

if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $id = $_GET['id'];
    $_SESSION['wishlist'][$id] = true;
}

if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    unset($_SESSION['wishlist'][$_GET['id']]);
}

echo "<h2>💖 Sách yêu thích</h2>";
if (!$_SESSION['wishlist']) echo "Danh sách trống.";
else foreach($_SESSION['wishlist'] as $id=>$_)
    echo "• Sách ID: $id <a href='?action=remove&id=$id'>[x]</a><br>";

include('../includes/footer.php');
?>
