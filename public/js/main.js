document.addEventListener("DOMContentLoaded", function() {
    
    // Chỉ thực thi code này nếu chúng ta đang ở trang giỏ hàng
    // Bằng cách kiểm tra sự tồn tại của một phần tử đặc trưng
    const selectAllCheckbox = document.getElementById('cart-select-all');
    
    // Nếu không tìm thấy, nghĩa là không phải trang giỏ hàng, thì dừng lại
    if (!selectAllCheckbox) {
        return; 
    }

    // Lấy các phần tử DOM còn lại
    const itemCheckboxes = document.querySelectorAll('.cart-item-select');
    const checkoutButton = document.getElementById('btn-checkout-submit');
    const subtotalEl = document.getElementById('cart-subtotal');
    const discountEl = document.getElementById('cart-discount');
    const totalEl = document.getElementById('cart-total');

    // Hàm định dạng tiền tệ
    const formatter = new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND' 
    });

    // Hàm cập nhật tổng tiền
    function updateCartTotals() {
        let subtotal = 0;
        let total = 0;
        let itemsSelected = 0;

        itemCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                itemsSelected++;
                const row = checkbox.closest('tr');
                const originalPrice = parseFloat(row.dataset.originalPrice);
                const discountedPrice = parseFloat(row.dataset.discountedPrice);
                
                // Đọc số lượng từ data-attribute
                const quantityCell = row.querySelector('.cart-item-quantity');
                const quantity = parseInt(quantityCell.dataset.quantity, 10);
                
                if (!isNaN(originalPrice) && !isNaN(discountedPrice) && !isNaN(quantity)) {
                    subtotal += originalPrice * quantity;
                    total += discountedPrice * quantity;
                }
            }
        });

        let discount = subtotal - total;

        subtotalEl.textContent = formatter.format(subtotal);
        discountEl.textContent = formatter.format(discount);
        totalEl.textContent = formatter.format(total);

        // Kích hoạt/Vô hiệu hóa nút thanh toán
        if (checkoutButton) {
            checkoutButton.disabled = (itemsSelected === 0);
        }
    }

    // Sự kiện cho nút "Chọn tất cả"
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateCartTotals();
    });

    // Sự kiện cho từng checkbox của sản phẩm
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else {
                // Kiểm tra nếu tất cả đều được chọn thì check cả nút "Chọn tất cả"
                if ([...itemCheckboxes].every(cb => cb.checked)) {
                    selectAllCheckbox.checked = true;
                }
            }
            updateCartTotals();
        });
    });

    // Cập nhật tổng tiền khi tải trang lần đầu
    updateCartTotals();
});