<?php
// Tệp này không cần session_start() nữa vì header.php đã làm
include_once '../includes/header.php';
?>

<div class="main-container">
    
    <aside class="sidebar">
        <h3>Danh mục sách</h3>
        <ul>
            <li><a href="#">Sách Bán Chạy</a></li>
            <li><a href="#">Sách Mới Phát Hành</a></li>
            <li><a href="#">Sách Kinh Tế</a></li>
            <li><a href="#">Sách Văn Học</a></li>
            <li><a href="#">Sách Kỹ Năng Sống</a></li>
            <li><a href="#">Sách Thiếu Nhi</a></li>
            <li><a href="#">Sách Ngoại Ngữ</a></li>
        </ul>
    </aside>

    <main class="content-area">
        <h2>Sách Mới Nổi Bật</h2>
        
        <div class="product-grid">
            
            <div class="product-item">
                <img src="https://via.placeholder.com/250x350" alt="Bìa sách mẫu">
                <div class="product-info">
                    <h4>Tên Sách Rất Dài...</h4>
                    <div class="product-price">120.000đ</div>
                    <button class="btn">Thêm vào giỏ</button>
                </div>
            </div>
            
            <div class="product-item">
                <img src="https://via.placeholder.com/250x350" alt="Bìa sách mẫu">
                <div class="product-info">
                    <h4>Một Cuốn Sách Hay</h4>
                    <div class="product-price">99.000đ</div>
                    <button class="btn">Thêm vào giỏ</button>
                </div>
            </div>

            <div class="product-item">
                <img src="https://via.placeholder.com/250x350" alt="Bìa sách mẫu">
                <div class="product-info">
                    <h4>Lập Trình PHP</h4>
                    <div class="product-price">250.000đ</div>
                    <button class="btn">Thêm vào giỏ</button>
                </div>
            </div>

            <div class="product-item">
                <img src="https://via.placeholder.com/250x350" alt="Bìa sách mẫu">
                <div class="product-info">
                    <h4>Tư Duy Thiết Kế</h4>
                    <div class="product-price">150.000đ</div>
                    <button class="btn">Thêm vào giỏ</button>
                </div>
            </div>

            </div>
    </main>

</div>

<?php
include_once '../includes/footer.php';
?>