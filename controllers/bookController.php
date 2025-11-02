<?php
require_once '../models/Book.php';
require_once '../models/Category.php';

$action = $_GET['action'] ?? '';
$bookModel = new Book();

switch ($action) {
    case 'list':
        $books = $bookModel->getAllBooks();
        include '../public/book_list.php';
        break;

    case 'detail':
        $id_sach = $_GET['id_sach'] ?? '';
        $book = $bookModel->getBookById($id_sach);
        if ($book) {
            include '../public/book_detail.php';
        } else {
            echo "Không tìm thấy sách.";
        }
        break;

    case 'search':
        $keyword = $_GET['keyword'] ?? '';
        $books = $bookModel->searchBooks($keyword);
        include '../public/search.php';
        break;

    default:
        header('Location: ../public/index.php');
        break;
}
?>
