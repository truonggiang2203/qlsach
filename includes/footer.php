<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-column">
            <h4>Về Chúng Tôi</h4>
            <ul>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Liên hệ</a></li>
                <li><a href="#">Tuyển dụng</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Hỗ Trợ Khách Hàng</h4>
            <ul>
                <li><a href="#">Câu hỏi thường gặp</a></li>
                <li><a href="#">Chính sách đổi trả</a></li>
                <li><a href="#">Chính sách bảo mật</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Thanh Toán</h4>
            <p>An toàn, bảo mật</p>
        </div>
        <div class="footer-column">
            <h4>Kết Nối Với Chúng Tôi</h4>
        </div>
    </div>
</footer>

<!-- ===================== CHATBOT WIDGET ===================== -->
<link rel="stylesheet" href="/qlsach/public/css/chatbot.css">
<script src="/qlsach/public/js/chatbot.js"></script>

<!-- Old Chatbot Styles (Keep for compatibility) -->
<style>
/* Bong bóng bật chat */
#chat-bubble {
    width: 60px;
    height: 60px;
    background: #0084ff;
    color: white;
    border-radius: 50%;
    position: fixed;
    bottom: 25px;
    right: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 27px;
    cursor: pointer;
    z-index: 99999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    transition: 0.25s;
}
#chat-bubble:hover { transform: scale(1.13); }

/* Chatbox */
#chatbot-box {
    width: 330px;
    height: 480px;
    background: #fff;
    position: fixed;
    bottom: 95px;
    right: 25px;
    border-radius: 15px;
    display: none;
    flex-direction: column;
    box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    z-index: 99998;
    overflow: hidden;
    animation: slideUp 0.25s ease-out;
}
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0 }
    to   { transform: translateY(0); opacity: 1 }
}

/* Header */
#chatbot-header {
    background: #0084ff;
    color: white;
    padding: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
#chat-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: white;
}

/* Message container */
#chatbot-messages {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    background: #f1f1f1;
}

/* Tin nhắn có avatar */
.msg-line {
    display: flex;
    align-items: flex-start;
    margin-bottom: 12px;
}

.avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    margin-right: 8px;
}

.avatar-user {
    margin-left: 8px;
    margin-right: 0;
}

/* Bong bóng bot */
.msg-bot {
    background: white;
    padding: 10px;
    border-radius: 12px;
    border: 1px solid #ccc;
    max-width: 70%;
}

/* Bong bóng user */
.msg-user {
    background: #0084ff;
    color: white;
    padding: 10px;
    border-radius: 12px;
    margin-left: auto;
    max-width: 70%;
}

/* Khu nhập */
#chatbot-input-area {
    display: flex;
    padding: 8px;
    background: #fff;
    border-top: 1px solid #ddd;
}
#chatbot-input {
    flex: 1;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 18px;
}
#chatbot-btn {
    margin-left: 8px;
    background: #0084ff;
    color: white;
    border: none;
    padding: 8px 18px;
    border-radius: 18px;
    cursor: pointer;
}

/* Gợi ý */
.suggest-btn {
    background: #e4f0ff;
    border: 1px solid #bcd4ff;
    padding: 6px 10px;
    margin-top: 5px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 13px;
}
.suggest-btn:hover { background: #cfe0ff; }
.chat-btn-detail, .chat-btn-add {
    padding: 6px 10px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
}

.chat-btn-detail {
    background: #e3e3ff;
    color: #333;
}

.chat-btn-add {
    background: #00b400;
    color: white;
}

.chat-btn-detail:hover {
    background: #d0d0ff;
}

.chat-btn-add:hover {
    background: #009900;
}

</style>


<script>
const bubble = document.getElementById("chat-bubble");
const chatbox = document.getElementById("chatbot-box");
const closeBtn = document.getElementById("chat-close");

const botAvatar = "uploads/chatbot/bot.jpg";
const userAvatar = "uploads/chatbot/user.jpg";

const suggestions = [
    "Giá sách S0001",
    "Đơn hàng DH176",
    "Khuyến mãi",
    "S0018 còn hàng không",
    "Tìm sách Harry Potter"
];

// Render gợi ý
function renderSuggestions() {
    let box = document.getElementById("chatbot-messages");

    let html = `<div class="msg-line">
        <img src="${botAvatar}" class="avatar">
        <div class="msg-bot">Bạn có thể thử: <br>`;

    suggestions.forEach(s => {
        html += `<button class="suggest-btn" onclick="quickAsk('${s}')">${s}</button><br>`;
    });

    html += `</div></div>`;

    box.innerHTML += html;
    box.scrollTop = box.scrollHeight;
}

function quickAsk(text) {
    document.getElementById("chatbot-input").value = text;
    sendMsg();
}

// Typing...
function showTyping() {
    let box = document.getElementById("chatbot-messages");

    box.innerHTML += `
        <div id="typing" class="msg-line">
            <img src="${botAvatar}" class="avatar">
            <div class="msg-bot"><i>Bot đang nhập...</i></div>
        </div>
    `;
    box.scrollTop = box.scrollHeight;
}
function hideTyping() {
    let t = document.getElementById("typing");
    if (t) t.remove();
}

// Gửi tin nhắn
function sendMsg() {
    let input = document.getElementById("chatbot-input");
    let msg = input.value.trim();
    if (!msg) return;

    let box = document.getElementById("chatbot-messages");

    box.innerHTML += `
        <div class="msg-line" style="justify-content:flex-end;">
            <div class="msg-user">${msg}</div>
            <img src="${userAvatar}" class="avatar avatar-user">
        </div>
    `;
    box.scrollTop = box.scrollHeight;

    showTyping();

    fetch("chat.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ message: msg })
    })
    .then(res => res.json())
    .then(data => {
        hideTyping();
        box.innerHTML += `
            <div class="msg-line">
                <img src="${botAvatar}" class="avatar">
                <div class="msg-bot">${data.reply}</div>
            </div>
        `;
        box.scrollTop = box.scrollHeight;
    });

    input.value = "";
}

// Mở chat + lời chào
let greeted = false;
bubble.onclick = () => {
    chatbox.style.display = "flex";

    if (!greeted) {
        setTimeout(() => {
            document.getElementById("chatbot-messages").innerHTML += `
                <div class="msg-line">
                    <img src="${botAvatar}" class="avatar">
                    <div class="msg-bot">
                        Xin chào!<br>
                        Mình là chatbot hỗ trợ QL Sách.<br>
                        Bạn muốn hỏi gì không?
                    </div>
                </div>
            `;
            renderSuggestions();
        }, 300);

        greeted = true;
    }
};

// Đóng chat
closeBtn.onclick = () => {
    chatbox.style.display = "none";
};
// ==============================
//  XỬ LÝ NÚT XEM CHI TIẾT SÁCH
// ==============================
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("chat-btn-detail")) {

        let id = e.target.getAttribute("data-id");

        // BOOK DETAIL NHẬN id_sach → KHÔNG PHẢI id
        window.location.href = "book_detail.php?id_sach=" + id;
    }
});

// ==============================
//  XỬ LÝ THÊM VÀO GIỎ HÀNG
// ==============================
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("chat-btn-add")) {

        let id = e.target.getAttribute("data-id");

        let form = new FormData();
        form.append("so_luong", 1);

        fetch("/qlsach/controllers/cartController.php?action=add&id_sach=" + id, {
            method: "POST",
            body: form
        })
        .then(res => res.json())
        .then(() => {
            let box = document.getElementById("chatbot-messages");
            box.innerHTML += `
                <div class="msg-line">
                    <img src="${botAvatar}" class="avatar">
                    <div class="msg-bot">Đã thêm <strong>${id}</strong> vào giỏ hàng!</div>
                </div>
            `;
            box.scrollTop = box.scrollHeight;
        })
        .catch(err => console.error(err));
    }
});
</script>


<!-- ================= END CHAT MESSENGER ================= -->


</body>

</html>