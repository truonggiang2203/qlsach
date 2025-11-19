document.addEventListener('DOMContentLoaded', function () {

    const banner = document.querySelector('.home-banner');
    if (!banner) return;

    const slidesContainer = banner.querySelector('.banner-slides');
    const slides = banner.querySelectorAll('.banner-slide');
    if (!slides.length) return;

    let index = 0;
    let timer = null;

    const prevBtn = banner.querySelector('.banner-prev');
    const nextBtn = banner.querySelector('.banner-next');
    const indicatorsWrap = banner.querySelector('.banner-indicators');

    /* ===============================
       TẠO INDICATORS (DOTS)
    =============================== */
    indicatorsWrap.innerHTML = "";
    slides.forEach((_, i) => {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.className = "indicator" + (i === 0 ? " active" : "");
        dot.dataset.index = i;

        dot.addEventListener("click", () => {
            goToSlide(i);
            restartTimer();
        });

        indicatorsWrap.appendChild(dot);
    });

    const indicators = indicatorsWrap.querySelectorAll(".indicator");

    /* ===============================
       CHUYỂN SLIDE
    =============================== */
    function goToSlide(i) {
        index = (i + slides.length) % slides.length;

        // Mượt, dùng transform đúng chuẩn
        slidesContainer.style.transform = `translateX(-${index * 100}%)`;

        indicators.forEach((btn, idx) =>
            btn.classList.toggle("active", idx === index)
        );
    }

    function nextSlide() {
        goToSlide(index + 1);
    }

    function prevSlide() {
        goToSlide(index - 1);
    }

    /* ===============================
       EVENT BUTTONS
    =============================== */
    if (nextBtn) nextBtn.addEventListener("click", () => {
        nextSlide();
        restartTimer();
    });

    if (prevBtn) prevBtn.addEventListener("click", () => {
        prevSlide();
        restartTimer();
    });

    /* ===============================
       AUTO SLIDE
    =============================== */
    function startTimer() {
        timer = setInterval(nextSlide, 4500);
    }

    function stopTimer() {
        clearInterval(timer);
        timer = null;
    }

    function restartTimer() {
        stopTimer();
        startTimer();
    }

    banner.addEventListener("mouseenter", stopTimer);
    banner.addEventListener("mouseleave", startTimer);

    /* ===============================
       INIT
    =============================== */
    goToSlide(0);
    startTimer();
    
    /* ===============================
       LOAD MORE FOR PRODUCT GRIDS
    =============================== */
    function initLoadMore() {
        const grids = document.querySelectorAll('.product-grid[data-initial]');
        grids.forEach(grid => {
            const initial = parseInt(grid.dataset.initial, 10) || 4;
            const items = Array.from(grid.querySelectorAll('.product-item'));
            if (items.length <= initial) return;

            // hide items after initial
            items.forEach((it, idx) => {
                if (idx >= initial) it.classList.add('hidden');
            });

            // add load-more button
            const wrap = document.createElement('div');
            wrap.className = 'load-more-wrap';
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn load-more';
            btn.textContent = 'Xem thêm';
            wrap.appendChild(btn);
            grid.parentNode.insertBefore(wrap, grid.nextSibling);

            let shown = initial;
            btn.addEventListener('click', () => {
                const next = Math.min(shown + initial, items.length);
                for (let i = shown; i < next; i++) {
                    items[i].classList.remove('hidden');
                }
                shown = next;
                if (shown >= items.length) {
                    btn.style.display = 'none';
                }
            });
        });
    }

    initLoadMore();

    /* ===============================
       CATEGORY ACCORDION
    =============================== */
    function initCategoryAccordion() {
        const acc = document.querySelector('.category-accordion');
        if (!acc) return;
        acc.querySelectorAll('.category-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', (!expanded).toString());
                const list = btn.parentElement.querySelector('.subcategory-list');
                if (list) {
                    if (expanded) {
                        list.hidden = true;
                    } else {
                        list.hidden = false;
                    }
                }
            });
        });
    }

    initCategoryAccordion();
});
