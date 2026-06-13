</div>
     <!-- /#page-content-wrapper -->
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
      
      if (menuToggle) {
          menuToggle.addEventListener('click', function (e) {
              e.stopPropagation();
              if (sidebarWrapper) sidebarWrapper.classList.toggle('toggled');
              if (pageContentWrapper) pageContentWrapper.classList.toggle('sidebar-toggled');
              if (sidebarBackdrop) sidebarBackdrop.classList.toggle('show');
          });
      }
      
      if (sidebarBackdrop) {
          sidebarBackdrop.addEventListener('click', function () {
              if (sidebarWrapper) sidebarWrapper.classList.remove('toggled');
              if (pageContentWrapper) pageContentWrapper.classList.remove('sidebar-toggled');
              sidebarBackdrop.classList.remove('show');
          });
      }
      
      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(event) {
          if (window.innerWidth <= 991 && sidebarWrapper) {
              if (sidebarWrapper.classList.contains('toggled') && 
                  !sidebarWrapper.contains(event.target) && 
                  (!menuToggle || !menuToggle.contains(event.target))) {
                  sidebarWrapper.classList.remove('toggled');
                  if (pageContentWrapper) pageContentWrapper.classList.remove('sidebar-toggled');
                  if (sidebarBackdrop) sidebarBackdrop.classList.remove('show');
              }
          }
      });
  </script>
 <style>
 .sidebar-backdrop {
     display: none;
     position: fixed;
     top: 0;
     left: 0;
     right: 0;
     bottom: 0;
     background: rgba(0,0,0,0.5);
     z-index: 999;
 }
 .sidebar-backdrop.show {
     display: block;
 }
 </style>
 </body>
 </html>