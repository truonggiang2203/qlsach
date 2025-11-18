/**
 * Comment & Rating JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initStarRating();
    initCommentForm();
    initEditDeleteButtons();
});

/**
 * Khởi tạo Star Rating
 */
function initStarRating() {
    const starRating = document.getElementById('starRating');
    const starRatingValue = document.getElementById('starRatingValue');
    
    if (!starRating || !starRatingValue) return;
    
    const stars = starRating.querySelectorAll('input[type="radio"]');
    const labels = starRating.querySelectorAll('label');
    
    stars.forEach((star, index) => {
        star.addEventListener('change', function() {
            const value = parseInt(this.value);
            starRatingValue.textContent = value + ' sao';
            updateStarDisplay(labels, value);
        });
        
        star.addEventListener('mouseenter', function() {
            const value = parseInt(this.value);
            updateStarDisplay(labels, value, true);
        });
    });
    
    starRating.addEventListener('mouseleave', function() {
        const checkedStar = starRating.querySelector('input[type="radio"]:checked');
        const value = checkedStar ? parseInt(checkedStar.value) : 0;
        updateStarDisplay(labels, value);
    });
}

/**
 * Cập nhật hiển thị sao
 */
function updateStarDisplay(labels, value, isHover = false) {
    labels.forEach((label, index) => {
        const svg = label.querySelector('svg');
        const starValue = 5 - index;
        
        if (starValue <= value) {
            svg.setAttribute('fill', '#ffc107');
            svg.setAttribute('stroke', '#ffc107');
            if (isHover) {
                svg.style.transform = 'scale(1.1)';
            }
        } else {
            svg.setAttribute('fill', 'none');
            svg.setAttribute('stroke', '#ddd');
            svg.style.transform = 'scale(1)';
        }
    });
}

/**
 * Khởi tạo Comment Form
 */
function initCommentForm() {
    const commentForm = document.getElementById('commentForm');
    if (!commentForm) return;
    
    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitCommentBtn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="spinner" style="width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite;"></div> Đang gửi...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
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
            if (typeof data === 'object' && data.success) {
                // Reload trang để cập nhật bình luận
                window.location.reload();
            } else {
                alert(typeof data === 'object' ? data.message : 'Có lỗi xảy ra. Vui lòng thử lại!');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
}

/**
 * Khởi tạo nút Edit/Delete
 */
function initEditDeleteButtons() {
    // Edit buttons
    document.querySelectorAll('.btn-edit-comment').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            const rating = parseInt(this.getAttribute('data-rating'));
            const content = this.getAttribute('data-content');
            
            editComment(commentId, rating, content);
        });
    });
    
    // Delete buttons
    document.querySelectorAll('.btn-delete-comment').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Bạn có chắc muốn xóa bình luận này?')) {
                return;
            }
            
            const commentId = this.getAttribute('data-comment-id');
            const idSach = this.getAttribute('data-id-sach');
            
            deleteComment(commentId, idSach);
        });
    });
}

/**
 * Chỉnh sửa bình luận
 */
function editComment(commentId, rating, content) {
    const commentForm = document.getElementById('commentForm');
    const commentTextarea = commentForm.querySelector('textarea[name="binh_luan"]');
    const starInputs = commentForm.querySelectorAll('input[name="so_sao"]');
    const submitBtn = document.getElementById('submitCommentBtn');
    const cancelBtn = document.getElementById('cancelCommentBtn');
    const formTitle = document.querySelector('.comment-form-title');
    
    // Cập nhật form
    commentTextarea.value = content;
    starInputs.forEach(input => {
        input.checked = parseInt(input.value) === rating;
    });
    
    // Trigger change event để cập nhật star display
    const checkedInput = commentForm.querySelector(`input[name="so_sao"][value="${rating}"]`);
    if (checkedInput) {
        checkedInput.dispatchEvent(new Event('change'));
    }
    
    // Thêm hidden input cho id_bl nếu chưa có
    if (!commentForm.querySelector('input[name="id_bl"]')) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'id_bl';
        hiddenInput.value = commentId;
        commentForm.appendChild(hiddenInput);
    }
    
    // Cập nhật action và button
    commentForm.action = '/qlsach/controllers/commentController.php?action=update';
    submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> Cập nhật đánh giá';
    cancelBtn.style.display = 'inline-flex';
    formTitle.textContent = 'Chỉnh sửa đánh giá của bạn';
    
    // Scroll to form
    commentForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Cancel button handler
    cancelBtn.onclick = function() {
        location.reload();
    };
}

/**
 * Xóa bình luận
 */
function deleteComment(commentId, idSach) {
    fetch(`/qlsach/controllers/commentController.php?action=delete&id_bl=${commentId}&id_sach=${idSach}`, {
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
        if (typeof data === 'object' && data.success) {
            // Reload trang
            window.location.reload();
        } else {
            alert(typeof data === 'object' ? data.message : 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại!');
    });
}

