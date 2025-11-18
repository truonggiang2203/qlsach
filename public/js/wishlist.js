/**
 * Wishlist JavaScript - Xử lý thêm/xóa yêu thích với AJAX
 */

// Helper function để tạo SVG heart icon
function createHeartIcon(isActive = false) {
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '20');
    svg.setAttribute('height', '20');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', isActive ? 'currentColor' : 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '2');
    svg.setAttribute('stroke-linecap', 'round');
    svg.setAttribute('stroke-linejoin', 'round');
    
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', 'M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z');
    
    svg.appendChild(path);
    return svg;
}

// Cập nhật icon trong button
function updateWishlistIcon(button, isActive) {
    const svg = button.querySelector('svg');
    if (svg) {
        svg.setAttribute('fill', isActive ? 'currentColor' : 'none');
        if (isActive) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    } else {
        // Nếu chưa có SVG, tạo mới
        button.innerHTML = '';
        button.appendChild(createHeartIcon(isActive));
        if (isActive) {
            button.classList.add('active');
        }
    }
}

// Lưu trạng thái wishlist của user (nếu đã đăng nhập)
let wishlistStatus = {};

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    // Load trạng thái wishlist nếu user đã đăng nhập
    if (typeof userId !== 'undefined') {
        loadWishlistStatus();
    }
    
    // Gắn event cho tất cả nút wishlist
    initWishlistButtons();
});

/**
 * Khởi tạo các nút wishlist
 */
function initWishlistButtons() {
    const wishlistButtons = document.querySelectorAll('.wishlist-btn, .product-item-wishlist-btn, .book-card-wishlist-btn, .product-info-wishlist-btn');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const bookId = this.getAttribute('data-book-id') || this.getAttribute('data-id-sach');
            if (!bookId) return;
            
            toggleWishlist(bookId, this);
        });
    });
}

/**
 * Toggle wishlist (thêm/xóa)
 */
function toggleWishlist(bookId, button) {
    // Kiểm tra đăng nhập
    if (typeof userId === 'undefined' || !userId) {
        if (confirm('Bạn cần đăng nhập để thêm vào danh sách yêu thích. Bạn có muốn đăng nhập không?')) {
            window.location.href = '/qlsach/guest/login.php';
        }
        return;
    }
    
    const isActive = button.classList.contains('active');
    const action = isActive ? 'remove' : 'add';
    
    // Disable button và hiển thị loading
    button.classList.add('wishlist-btn-loading');
    button.style.pointerEvents = 'none';
    
    // Gửi request
    fetch(`/qlsach/controllers/wishlistController.php?action=${action}&id_sach=${bookId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        }
        return response.text();
    })
    .then(data => {
        // Xử lý JSON response
        if (typeof data === 'object' && data.success) {
            // Toggle trạng thái
            if (action === 'add') {
                button.classList.add('active');
                updateWishlistIcon(button, true);
                button.classList.add('wishlist-btn-success');
                button.title = 'Xóa khỏi yêu thích';
                updateWishlistStatus(bookId, true);
            } else {
                button.classList.remove('active');
                updateWishlistIcon(button, false);
                button.title = 'Thêm vào yêu thích';
                updateWishlistStatus(bookId, false);
            }
        } else if (typeof data === 'object' && !data.success) {
            // Lỗi từ server
            alert(data.message || 'Có lỗi xảy ra');
            button.classList.remove('wishlist-btn-loading');
            button.style.pointerEvents = '';
            return;
        } else {
            // Fallback cho text response (nếu không phải JSON)
            // Toggle trạng thái
            if (action === 'add') {
                button.classList.add('active');
                updateWishlistIcon(button, true);
                button.classList.add('wishlist-btn-success');
                updateWishlistStatus(bookId, true);
            } else {
                button.classList.remove('active');
                updateWishlistIcon(button, false);
                updateWishlistStatus(bookId, false);
            }
        }
        
        // Remove loading
        button.classList.remove('wishlist-btn-loading');
        button.style.pointerEvents = '';
        
        // Remove success animation sau 0.5s
        setTimeout(() => {
            button.classList.remove('wishlist-btn-success');
        }, 500);
    })
    .catch(error => {
        console.error('Error:', error);
        button.classList.remove('wishlist-btn-loading');
        button.style.pointerEvents = '';
        alert('Có lỗi xảy ra. Vui lòng thử lại!');
    });
}

/**
 * Load trạng thái wishlist từ server
 */
function loadWishlistStatus() {
    // Có thể gọi API để load trạng thái wishlist
    // Hiện tại sẽ dựa vào class 'active' trên button
}

/**
 * Cập nhật trạng thái wishlist trong memory
 */
function updateWishlistStatus(bookId, isWishlisted) {
    wishlistStatus[bookId] = isWishlisted;
}

/**
 * Kiểm tra sách có trong wishlist không
 */
function isInWishlist(bookId) {
    return wishlistStatus[bookId] === true;
}

