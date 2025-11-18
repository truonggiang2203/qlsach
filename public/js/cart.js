document.addEventListener("DOMContentLoaded", function () {
    
    /* =====================================================
     * 1. CẬP NHẬT SỐ LƯỢNG (TĂNG/GIẢM)
     * ===================================================== */
    function updateQuantity(id, type) {
        const qtyInput = document.querySelector(`.qty-input[data-id="${id}"]`);
        const decreaseBtn = document.querySelector(`.qty-decrease[data-id="${id}"]`);
        const increaseBtn = document.querySelector(`.qty-increase[data-id="${id}"]`);
        const maxQty = parseInt(qtyInput.getAttribute('max'));
        const currentQty = parseInt(qtyInput.value);

        fetch("/qlsach/controllers/cartController.php?action=update_qty", {
            method: "POST",
            headers: { 
                "Content-Type": "application/x-www-form-urlencoded",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: `id_sach=${id}&type=${type}`
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || 'Có lỗi xảy ra');
                return;
            }

            // Cập nhật số lượng
            qtyInput.value = data.new_qty;
            
            // Cập nhật nút disable/enable
            if (data.new_qty <= 1) {
                decreaseBtn.disabled = true;
            } else {
                decreaseBtn.disabled = false;
            }
            
            if (data.new_qty >= maxQty) {
                increaseBtn.disabled = true;
            } else {
                increaseBtn.disabled = false;
            }

            // Cập nhật thành tiền của sản phẩm
            const subtotalEl = document.querySelector(`.subtotal-amount[data-id="${id}"]`);
            if (subtotalEl) {
                subtotalEl.textContent = data.itemSubtotal;
            }

            // Cập nhật tổng giỏ hàng
            updateCartTotals(data);
            
            // Cập nhật số lượng trong header
            if (data.cartCount !== undefined) {
                updateHeaderCartCount(data.cartCount);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật số lượng');
        });
    }

    // Event listeners cho nút tăng/giảm
    document.querySelectorAll(".qty-btn.qty-increase").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            updateQuantity(id, "increase");
        });
    });

    document.querySelectorAll(".qty-btn.qty-decrease").forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            updateQuantity(id, "decrease");
        });
    });

    /* =====================================================
     * 2. XÓA SẢN PHẨM KHỎI GIỎ HÀNG
     * ===================================================== */
    window.removeItem = function(id, name) {
        if (!confirm(`Bạn có chắc muốn xóa "${name}" khỏi giỏ hàng?`)) {
            return;
        }

        const itemEl = document.getElementById(`cart-item-${id}`);
        if (itemEl) {
            itemEl.setAttribute('data-removing', 'true');
        }

        fetch(`/qlsach/controllers/cartController.php?action=remove&id_sach=${id}`, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Xóa phần tử khỏi DOM
                const itemEl = document.getElementById(`cart-item-${id}`);
                if (itemEl) {
                    itemEl.style.transition = 'opacity 0.3s, transform 0.3s';
                    itemEl.style.opacity = '0';
                    itemEl.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        itemEl.remove();
                        
                        // Kiểm tra nếu giỏ hàng trống
                        const remainingItems = document.querySelectorAll('.cart-item').length;
                        if (remainingItems === 0) {
                            location.reload();
                        } else {
                            // Cập nhật tổng tiền
                            if (data.totals) {
                                updateCartTotalsFromData(data.totals);
                            }
                            
                            // Cập nhật số lượng trong header
                            if (data.cartCount !== undefined) {
                                updateHeaderCartCount(data.cartCount);
                            }
                            
                            // Cập nhật số lượng sản phẩm trong header
                            updateCartItemCount();
                        }
                    }, 300);
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra khi xóa sản phẩm');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa sản phẩm');
        });
    };

    /* =====================================================
     * 3. XÓA TẤT CẢ GIỎ HÀNG
     * ===================================================== */
    window.clearCart = function() {
        if (!confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
            return;
        }

        fetch("/qlsach/controllers/cartController.php?action=clear", {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra khi xóa giỏ hàng');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa giỏ hàng');
        });
    };

    /* =====================================================
     * 4. CẬP NHẬT TỔNG TIỀN GIỎ HÀNG
     * ===================================================== */
    function updateCartTotals(data) {
        if (data.cart_total) {
            const totalEl = document.getElementById('cart-total');
            if (totalEl) totalEl.textContent = data.cart_total;
        }
        
        if (data.cart_subtotal) {
            const subtotalEl = document.getElementById('cart-subtotal');
            if (subtotalEl) subtotalEl.textContent = data.cart_subtotal;
        }
        
        if (data.cart_discount) {
            const discountEl = document.getElementById('cart-discount');
            if (discountEl) {
                discountEl.textContent = '-' + data.cart_discount;
                discountEl.parentElement.style.display = 'flex';
            }
        } else {
            const discountEl = document.getElementById('cart-discount');
            if (discountEl) {
                discountEl.parentElement.style.display = 'none';
            }
        }
    }

    function updateCartTotalsFromData(totals) {
        const totalEl = document.getElementById('cart-total');
        const subtotalEl = document.getElementById('cart-subtotal');
        const discountEl = document.getElementById('cart-discount');
        
        if (totalEl) {
            totalEl.textContent = formatCurrency(totals.total);
        }
        
        if (subtotalEl) {
            subtotalEl.textContent = formatCurrency(totals.subtotal);
        }
        
        if (discountEl) {
            if (totals.totalDiscount > 0) {
                discountEl.textContent = '-' + formatCurrency(totals.totalDiscount);
                discountEl.parentElement.style.display = 'flex';
            } else {
                discountEl.parentElement.style.display = 'none';
            }
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }

    /* =====================================================
     * 5. CẬP NHẬT SỐ LƯỢNG TRONG HEADER
     * ===================================================== */
    function updateHeaderCartCount(count) {
        // Tìm tất cả các element hiển thị số lượng giỏ hàng
        const cartCountElements = document.querySelectorAll('.cart-count-badge, .action-badge');
        cartCountElements.forEach(el => {
            if (el.textContent.includes('sản phẩm')) {
                el.textContent = count + ' sản phẩm';
            } else {
                el.textContent = count;
            }
        });
    }

    function updateCartItemCount() {
        const itemCount = document.querySelectorAll('.cart-item').length;
        const countBadge = document.querySelector('.cart-count-badge');
        const selectAllText = document.querySelector('.select-all-checkbox span');
        
        if (countBadge) {
            countBadge.textContent = itemCount + ' sản phẩm';
        }
        
        if (selectAllText) {
            selectAllText.textContent = `Chọn tất cả (${itemCount})`;
        }
    }

    /* =====================================================
     * 6. XỬ LÝ CHECKBOX CHỌN SẢN PHẨM
     * ===================================================== */
    const btnCheckout = document.getElementById("btn-checkout-submit");
    const checkAll = document.getElementById("cart-select-all");
    const checks = document.querySelectorAll(".cart-item-select");

    function updateCheckoutButton() {
        const anyChecked = Array.from(checks).some(c => c.checked && !c.closest('.cart-item').hasAttribute('data-removing'));
        
        if (btnCheckout) {
            if (anyChecked) {
                btnCheckout.disabled = false;
                btnCheckout.classList.remove("disabled");
            } else {
                btnCheckout.disabled = true;
                btnCheckout.classList.add("disabled");
            }
        }
    }

    // Checkbox "Chọn tất cả"
    if (checkAll) {
        checkAll.addEventListener("change", function () {
            checks.forEach(c => {
                if (!c.closest('.cart-item').hasAttribute('data-removing')) {
                    c.checked = checkAll.checked;
                }
            });
            updateCheckoutButton();
        });
    }

    // Checkbox từng sản phẩm
    checks.forEach(c => {
        c.addEventListener("change", function () {
            // Cập nhật trạng thái "Chọn tất cả"
            if (checkAll) {
                const allChecked = Array.from(checks).every(ch => 
                    ch.checked || ch.closest('.cart-item').hasAttribute('data-removing')
                );
                checkAll.checked = allChecked;
            }
            updateCheckoutButton();
        });
    });

    // Khởi tạo trạng thái
    updateCheckoutButton();
    
    // Kiểm tra trạng thái "Chọn tất cả" ban đầu
    if (checkAll && checks.length > 0) {
        const allChecked = Array.from(checks).every(c => c.checked);
        checkAll.checked = allChecked;
    }
});
