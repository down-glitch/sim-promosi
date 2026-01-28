/* resources/js/admin-sidebar.js */
document.addEventListener('DOMContentLoaded', function() {
    // Toggle dropdown menus
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close other dropdowns
            dropdownToggles.forEach(otherToggle => {
                if (otherToggle !== toggle) {
                    const otherDropdown = otherToggle.nextElementSibling;
                    if (otherDropdown && otherDropdown.classList.contains('dropdown-menu')) {
                        otherDropdown.classList.remove('show');
                        otherToggle.classList.remove('active');
                    }
                }
            });
            
            // Toggle current dropdown
            const dropdown = this.nextElementSibling;
            if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                dropdown.classList.toggle('show');
                this.classList.toggle('active');
            }
        });
    });
    
    // Toggle sidebar collapse
    const sidebarCollapseBtn = document.getElementById('sidebarCollapse');
    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('collapsed');
        });
    }
    
    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Add overlay to body if not already added
            if (!document.body.contains(overlay)) {
                document.body.appendChild(overlay);
            }
        });
        
        // Close sidebar when clicking on overlay
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
    
    // Prevent back button
    window.history.forward();
});

// Function to toggle sidebar collapse (can be called from outside)
function toggleSidebarCollapse() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('collapsed');
    }
}