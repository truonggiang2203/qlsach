<?php
session_start();
require_once '../models/Wishlist.php';

$wishlist = new Wishlist();

if (!isset($_SESSION['id_tk'])) {
    header("Location: /qlsach/guest/login.php");
    exit;
}

$action = $_GET['action'] ?? '';
$id_tk = $_SESSION['id_tk'];
$id_sach = $_GET['id_sach'] ?? '';

if ($action === 'add' && $id_sach) {
    $wishlist->add($id_tk, $id_sach);
    header("Location: /qlsach/public/book_detail.php?id_sach=$id_sach");
    exit;
}

if ($action === 'remove' && $id_sach) {
    $wishlist->remove($id_tk, $id_sach);
    header("Location: /qlsach/public/book_detail.php?id_sach=$id_sach");
    exit;
}

if ($action === 'view') {
    header("Location: /qlsach/user/wishlist.php");
    exit;
}

echo "Invalid action";
