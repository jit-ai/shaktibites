        </div>
    </div>
</div>

<!-- Sidebar backdrop for mobile -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>

<!-- Bootstrap 5.3.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const menuToggle = document.getElementById('menu-toggle');
    const sidebarWrapper = document.getElementById('sidebar-wrapper');
    const pageContentWrapper = document.getElementById('page-content-wrapper');
    const sidebarBackdrop = document.getElementById('sidebar-backdrop');
    const wrapper = document.getElementById('wrapper');

    if (menuToggle) {
        menuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (wrapper) wrapper.classList.toggle('sidebar-collapsed');
            if (sidebarWrapper) sidebarWrapper.classList.toggle('toggled');
            if (pageContentWrapper) pageContentWrapper.classList.toggle('sidebar-toggled');
            if (sidebarBackdrop) sidebarBackdrop.classList.toggle('show');
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function () {
            if (wrapper) wrapper.classList.remove('sidebar-collapsed');
            if (sidebarWrapper) sidebarWrapper.classList.remove('toggled');
            if (pageContentWrapper) pageContentWrapper.classList.remove('sidebar-toggled');
            sidebarBackdrop.classList.remove('show');
        });
    }

    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 991 && sidebarWrapper) {
            if (sidebarWrapper.classList.contains('toggled') &&
                !sidebarWrapper.contains(event.target) &&
                (!menuToggle || !menuToggle.contains(event.target))) {
                if (wrapper) wrapper.classList.remove('sidebar-collapsed');
                sidebarWrapper.classList.remove('toggled');
                if (pageContentWrapper) pageContentWrapper.classList.remove('sidebar-toggled');
                if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
            }
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 991) {
            if (wrapper) wrapper.classList.remove('sidebar-collapsed');
            if (sidebarWrapper) sidebarWrapper.classList.remove('toggled');
            if (pageContentWrapper) pageContentWrapper.classList.remove('sidebar-toggled');
            if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
        }
    });
</script>
<style>
    #wrapper.sidebar-collapsed #sidebar-wrapper {
        transform: translateX(-100%);
    }
    #wrapper.sidebar-collapsed #page-content-wrapper {
        margin-left: 0;
    }
    #wrapper.sidebar-collapsed #page-content-wrapper .admin-navbar {
        left: 0;
    }
</style>
</body>
</html>
