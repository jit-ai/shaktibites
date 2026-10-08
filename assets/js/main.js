// Basic JavaScript for interactivity
document.addEventListener('DOMContentLoaded', function () {
    // Mobile navigation drawer: animate the hamburger and dismiss the panel
    // once a destination is chosen.
    var drawer = document.getElementById('sbMobileNav');
    var toggle = document.querySelector('[data-bs-target="#sbMobileNav"]');

    if (drawer && toggle && typeof bootstrap !== 'undefined') {

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

    }

    // Native scrolling keeps touch gestures and video controls independent.
    var slider = document.getElementById('reviewsSlider');
    if (!slider) return;
    var track = document.getElementById('reviewsTrack');
    var slides = Array.from(track.querySelectorAll('.review-slide'));
    var prev = document.getElementById('reviewsPrev');
    var next = document.getElementById('reviewsNext');
    var dots = document.getElementById('reviewsDots');
    var status = document.getElementById('reviewsStatus');
    var positions = [];
    var page = 0;
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    slider.querySelector('.reviews-nav').hidden = false;

    function update() {
        page = positions.reduce(function (nearest, position, index) {
            return Math.abs(position - track.scrollLeft) < Math.abs(positions[nearest] - track.scrollLeft) ? index : nearest;
        }, 0);
        prev.disabled = page === 0;
        next.disabled = page === positions.length - 1;
        Array.from(dots.children).forEach(function (dot, index) {
            dot.classList.toggle('active', index === page);
            dot.setAttribute('aria-current', index === page ? 'true' : 'false');
        });
        var first = Math.round(track.scrollLeft / (slides[0].getBoundingClientRect().width + parseFloat(getComputedStyle(track).gap))) + 1;
        var visible = Math.round(track.clientWidth / slides[0].getBoundingClientRect().width);
        status.textContent = 'Reviews ' + first + '\u2013' + Math.min(slides.length, first + visible - 1) + ' of ' + slides.length;
    }

    function goTo(index) {
        index = Math.max(0, Math.min(positions.length - 1, index));
        track.scrollTo({ left: positions[index], behavior: reducedMotion.matches ? 'instant' : 'smooth' });
    }

    function layout() {
        var max = Math.max(0, track.scrollWidth - track.clientWidth);
        var step = slides[0].getBoundingClientRect().width + parseFloat(getComputedStyle(track).gap);
        var visible = Math.max(1, Math.round(track.clientWidth / step));
        positions = [0];
        for (var offset = step * visible; offset < max - 1; offset += step * visible) positions.push(offset);
        if (max > 1) positions.push(max);
        dots.replaceChildren();
        positions.forEach(function (_, index) {
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'reviews-dot';
            dot.setAttribute('aria-label', 'Show review page ' + (index + 1));
            dot.setAttribute('aria-controls', 'reviewsTrack');
            dot.addEventListener('click', function () { goTo(index); });
            dots.appendChild(dot);
        });
        update();
    }

    prev.addEventListener('click', function () { goTo(page - 1); });
    next.addEventListener('click', function () { goTo(page + 1); });
    track.addEventListener('keydown', function (event) {
        if (event.target !== track) return;
        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            event.preventDefault();
            goTo(page + (event.key === 'ArrowRight' ? 1 : -1));
        }
    });
    var scrollTimer;
    track.addEventListener('scroll', function () {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(update, 100);
    }, { passive: true });
    if (typeof ResizeObserver !== 'undefined') new ResizeObserver(layout).observe(track);
    else window.addEventListener('resize', layout);
    layout();
});
