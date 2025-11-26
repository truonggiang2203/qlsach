// =========================================================
// 🤖 CHATBOT WIDGET
// =========================================================

class Chatbot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        this.init();
    }

    init() {
        this.createWidget();
        this.attachEventListeners();
        this.showWelcomeMessage();
    }

    createWidget() {
        const widget = document.createElement('div');
        widget.innerHTML = `
            <!-- Chat Button -->
            <button class="chat-button" id="chatButton">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
                </svg>
                <span class="chat-badge" id="chatBadge" style="display: none;">1</span>
            </button>

            <!-- Chat Window -->
            <div class="chat-window" id="chatWindow">
                <!-- Header -->
                <div class="chat-header">
                    <div class="chat-header-info">
                        <div class="chat-avatar">🤖</div>
                        <div class="chat-header-text">
                            <h3>Nhà Sách Tri Thức</h3>
                            <p>Luôn sẵn sàng hỗ trợ bạn</p>
                        </div>
                    </div>
                    <button class="chat-close" id="chatClose">✕</button>
                </div>

                <!-- Body -->
                <div class="chat-body" id="chatBody">
                    <div class="welcome-message">
                        <h4>Xin chào!</h4>
                        <p>Tôi có thể giúp gì cho bạn?</p>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div class="typing-indicator" id="typingIndicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>

                <!-- Footer -->
                <div class="chat-footer">
                    <div class="chat-input-wrapper">
                        <input 
                            type="text" 
                            class="chat-input" 
                            id="chatInput" 
                            placeholder="Nhập câu hỏi của bạn..."
                            autocomplete="off"
                        >
                        <button class="chat-send" id="chatSend">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="quick-actions" id="quickActions"></div>
                </div>
            </div>
        `;
        document.body.appendChild(widget);
    }

    attachEventListeners() {
        const chatButton = document.getElementById('chatButton');
        const chatClose = document.getElementById('chatClose');
        const chatSend = document.getElementById('chatSend');
        const chatInput = document.getElementById('chatInput');

        chatButton.addEventListener('click', () => this.toggleChat());
        chatClose.addEventListener('click', () => this.closeChat());
        chatSend.addEventListener('click', () => this.sendMessage());
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });

        // Delegate event cho các nút động
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('chat-btn-detail')) {
                const id = e.target.dataset.id;
                window.open(`/qlsach/public/book_detail.php?id_sach=${id}`, '_blank');
            }
            if (e.target.classList.contains('chat-btn-add')) {
                const id = e.target.dataset.id;
                this.addToCart(id);
            }
            if (e.target.classList.contains('quick-action')) {
                const text = e.target.textContent;
                document.getElementById('chatInput').value = text;
                this.sendMessage();
            }
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        const chatWindow = document.getElementById('chatWindow');
        const chatButton = document.getElementById('chatButton');
        const chatBadge = document.getElementById('chatBadge');

        if (this.isOpen) {
            chatWindow.classList.add('active');
            chatButton.classList.add('active');
            chatBadge.style.display = 'none';
            document.getElementById('chatInput').focus();
        } else {
            chatWindow.classList.remove('active');
            chatButton.classList.remove('active');
        }
    }

    closeChat() {
        this.isOpen = false;
        document.getElementById('chatWindow').classList.remove('active');
        document.getElementById('chatButton').classList.remove('active');
    }

    showWelcomeMessage() {
        setTimeout(() => {
            this.addBotMessage('Xin chào! Tôi có thể giúp bạn tìm sách, kiểm tra đơn hàng, hoặc xem khuyến mãi. Bạn cần gì?');
            this.showQuickActions([
                'Sách bán chạy',
                'Khuyến mãi',
                'Sách kinh tế',
                'Sách thiếu nhi'
            ]);
        }, 1000);
    }

    async sendMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();

        if (!message) return;

        // Hiển thị tin nhắn user
        this.addUserMessage(message);
        input.value = '';

        // Hiển thị typing indicator
        this.showTyping();

        // Gửi đến server
        try {
            const response = await fetch('/qlsach/public/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            
            // Ẩn typing và hiển thị reply
            setTimeout(() => {
                this.hideTyping();
                this.addBotMessage(data.reply);
            }, 500);

        } catch (error) {
            this.hideTyping();
            this.addBotMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.');
        }
    }

    addUserMessage(text) {
        const chatBody = document.getElementById('chatBody');
        const time = this.getCurrentTime();

        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message user';
        messageDiv.innerHTML = `
            <div class="chat-message-avatar">👤</div>
            <div class="chat-message-content">
                <div class="chat-message-bubble">${this.escapeHtml(text)}</div>
                <div class="chat-message-time">${time}</div>
            </div>
        `;

        chatBody.appendChild(messageDiv);
        this.scrollToBottom();
    }

    addBotMessage(html) {
        const chatBody = document.getElementById('chatBody');
        const time = this.getCurrentTime();

        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message bot';
        messageDiv.innerHTML = `
            <div class="chat-message-avatar">🤖</div>
            <div class="chat-message-content">
                <div class="chat-message-bubble">${html}</div>
                <div class="chat-message-time">${time}</div>
            </div>
        `;

        chatBody.appendChild(messageDiv);
        this.scrollToBottom();

        // Hiển thị badge nếu chat đóng
        if (!this.isOpen) {
            document.getElementById('chatBadge').style.display = 'flex';
        }
    }

    showTyping() {
        document.getElementById('typingIndicator').classList.add('active');
        this.scrollToBottom();
    }

    hideTyping() {
        document.getElementById('typingIndicator').classList.remove('active');
    }

    showQuickActions(actions) {
        const container = document.getElementById('quickActions');
        container.innerHTML = '';
        
        actions.forEach(action => {
            const btn = document.createElement('button');
            btn.className = 'quick-action';
            btn.textContent = action;
            container.appendChild(btn);
        });
    }

    scrollToBottom() {
        const chatBody = document.getElementById('chatBody');
        setTimeout(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 100);
    }

    getCurrentTime() {
        const now = new Date();
        return now.getHours().toString().padStart(2, '0') + ':' + 
               now.getMinutes().toString().padStart(2, '0');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async addToCart(id_sach) {
        try {
            const response = await fetch('/qlsach/controllers/cartController.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_sach=${id_sach}&so_luong=1`
            });

            if (response.ok) {
                this.addBotMessage('✅ Đã thêm sách vào giỏ hàng!');
                // Cập nhật số lượng giỏ hàng nếu có
                if (typeof updateCartCount === 'function') {
                    updateCartCount();
                }
            } else {
                this.addBotMessage('Không thể thêm vào giỏ hàng. Vui lòng thử lại.');
            }
        } catch (error) {
            this.addBotMessage('Có lỗi xảy ra. Vui lòng thử lại.');
        }
    }
}

// Khởi tạo chatbot khi trang load
document.addEventListener('DOMContentLoaded', () => {
    window.chatbot = new Chatbot();
});
