// Basic JavaScript for interactivity
document.addEventListener('DOMContentLoaded', function () {
    // Mobile navigation drawer: animate the hamburger and dismiss the panel
    // once a destination is chosen.
    var drawer = document.getElementById('sbMobileNav');
    var toggle = document.querySelector('[data-bs-target="#sbMobileNav"]');

    if (!drawer || !toggle || typeof bootstrap === 'undefined') {
        return;
    }

    drawer.addEventListener('show.bs.offcanvas', function () {
        toggle.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
    });

    drawer.addEventListener('hide.bs.offcanvas', function () {
        toggle.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    });

    drawer.addEventListener('click', function (event) {
        var link = event.target.closest ? event.target.closest('a[href]') : null;
        if (!link || link.getAttribute('data-bs-toggle') === 'dropdown') {
            return;
        }
        var href = link.getAttribute('href') || '';
        if (href === '' || href.charAt(0) === '#') {
            return;
        }
        var drawerInstance = bootstrap.Offcanvas.getInstance(drawer);
        if (drawerInstance) {
            drawerInstance.hide();
        }
    });

    // Reviews Slider
    var reviewsSlider = document.getElementById('reviewsSlider');
    if (reviewsSlider) {
        var track = document.getElementById('reviewsTrack');
        var slides = track ? track.querySelectorAll('.review-slide') : [];
        var prevBtn = document.getElementById('reviewsPrev');
        var nextBtn = document.getElementById('reviewsNext');
        var dotsContainer = document.getElementById('reviewsDots');

        var slideCount = slides.length;
        var slidesPerView = 3;
        var currentPage = 0;
        var autoSlideTimer = null;
        var AUTO_SLIDE_DELAY = 5000;

        function updateSlidesPerView() {
            var width = window.innerWidth;
            if (width <= 575) {
                slidesPerView = 1;
            } else if (width <= 991) {
                slidesPerView = 2;
            } else {
                slidesPerView = 3;
            }
        }

        function pageCount() {
            if (slideCount === 0) return 1;
            return Math.max(1, Math.ceil(slideCount / slidesPerView));
        }

        function createDots() {
            if (!dotsContainer) return;
            var dots = pageCount();
            dotsContainer.innerHTML = '';
            for (var i = 0; i < dots; i++) {
                (function (idx) {
                    var dot = document.createElement('button');
                    dot.className = 'reviews-dot' + (idx === 0 ? ' active' : '');
                    dot.setAttribute('aria-label', 'Go to page ' + (idx + 1));
                    dot.addEventListener('click', function () {
                        stopAutoSlide();
                        goToPage(idx);
                        startAutoSlide();
                    });
                    dotsContainer.appendChild(dot);
                })(i);
            }
        }

        function updateButtons() {
            var pages = pageCount();
            if (prevBtn) prevBtn.disabled = currentPage === 0;
            if (nextBtn) nextBtn.disabled = pages <= 1;
            if (dotsContainer) {
                var dots = dotsContainer.querySelectorAll('.reviews-dot');
                for (var i = 0; i < dots.length; i++) {
                    dots[i].classList.toggle('active', i === currentPage);
                }
            }
        }

        function goToPage(index) {
            updateSlidesPerView();
            var pages = pageCount();
            if (index < 0) index = pages - 1;
            if (index >= pages) index = 0;
            currentPage = index;
            if (track) {
                /* The track is block-level, so it is exactly
                   slidesPerView slides wide (the container width).
                   translateX percentages are relative to that
                   track width, so moving N slides means
                   N / slidesPerView * 100 percent. On the final
                   page we stop once the last slide reaches the
                   right edge instead of leaving empty space. */
                var slidesToMove = Math.min(
                    currentPage * slidesPerView,
                    slideCount - slidesPerView
                );
                var percent = (slidesToMove / slidesPerView) * 100;
                track.style.transform = 'translateX(-' + percent + '%)';
            }
            updateButtons();
        }

        function nextPage() {
            var pages = pageCount();
            goToPage((currentPage + 1) % pages);
        }

        function prevPage() {
            var pages = pageCount();
            goToPage((currentPage - 1 + pages) % pages);
        }

        function startAutoSlide() {
            stopAutoSlide();
            if (pageCount() <= 1) return;
            autoSlideTimer = setInterval(function () {
                nextPage();
            }, AUTO_SLIDE_DELAY);
        }

        function stopAutoSlide() {
            if (autoSlideTimer) {
                clearInterval(autoSlideTimer);
                autoSlideTimer = null;
            }
        }

        if (prevBtn) prevBtn.addEventListener('click', function () {
            stopAutoSlide();
            prevPage();
            startAutoSlide();
        });
        if (nextBtn) nextBtn.addEventListener('click', function () {
            stopAutoSlide();
            nextPage();
            startAutoSlide();
        });

        // Pause the auto slide while the visitor is reading a review.
        reviewsSlider.addEventListener('mouseenter', stopAutoSlide);
        reviewsSlider.addEventListener('mouseleave', startAutoSlide);
        reviewsSlider.addEventListener('touchstart', stopAutoSlide, { passive: true });
        reviewsSlider.addEventListener('touchend', startAutoSlide, { passive: true });

        // Recalculate when the viewport changes.
        var resizeTimer = null;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                updateSlidesPerView();
                var pages = pageCount();
                if (currentPage >= pages) currentPage = pages - 1;
                createDots();
                goToPage(currentPage);
                startAutoSlide();
            }, 150);
        });

        updateSlidesPerView();
        createDots();
        goToPage(0);
        startAutoSlide();
    }
});
