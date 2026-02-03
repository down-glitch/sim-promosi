<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>@yield('title', 'SIM-PROMOSI')</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
            rel="stylesheet"
        />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
            rel="stylesheet"
        />
        <link
            href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
            rel="stylesheet"
        />
        @livewireStyles
        <style>
            :root {
                --primary-color: #276a2b;
                --secondary-color: #1f5522;
                --accent-color: #4caf50;
                --light-bg: #f8f9fa;
                --dark-text: #212529;
                --light-text: #f8f9fa;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background-color: var(--light-bg);
                overflow-x: hidden;
            }

            /* Sidebar Styles */
            .sidebar {
                width: 260px;
                background: linear-gradient(
                    180deg,
                    var(--primary-color) 0%,
                    var(--secondary-color) 100%
                );
                color: white;
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                overflow-y: auto;
                z-index: 1000;
                transition: all 0.3s ease;
                box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar.collapsed {
                width: 70px;
            }

            .sidebar-header {
                padding: 20px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .sidebar-logo {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 18px;
                font-weight: 600;
            }

            .sidebar-logo i {
                font-size: 24px;
            }

            .sidebar-toggle {
                background: none;
                border: none;
                color: white;
                font-size: 18px;
                cursor: pointer;
                padding: 5px;
                border-radius: 4px;
                display: none;
            }

            .sidebar-toggle:hover {
                background: rgba(255, 255, 255, 0.1);
            }

            .sidebar-user {
                padding: 20px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
            }

            .user-info {
                flex: 1;
            }

            .user-name {
                font-weight: 600;
                font-size: 14px;
                margin-bottom: 3px;
            }

            .user-role {
                font-size: 12px;
                opacity: 0.8;
            }

            .sidebar-nav {
                padding: 15px 0;
            }

            .nav-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .nav-item {
                margin-bottom: 5px;
            }

            .nav-link {
                display: flex;
                align-items: center;
                padding: 12px 20px;
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                transition: all 0.3s ease;
                position: relative;
                border-left: 3px solid transparent;
            }

            .nav-link:hover {
                background: rgba(255, 255, 255, 0.1);
                color: white;
                border-left: 3px solid rgba(255, 255, 255, 0.5);
            }

            .nav-link.active {
                background: rgba(255, 255, 255, 0.15);
                color: white;
                border-left: 3px solid white;
            }

            .nav-link i {
                font-size: 18px;
                margin-right: 12px;
                width: 20px;
                text-align: center;
            }

            .dropdown-toggle {
                cursor: pointer;
            }

            .dropdown-icon {
                margin-left: auto;
                transition: transform 0.3s ease;
                font-size: 14px;
            }

            .dropdown-toggle.active .dropdown-icon {
                transform: rotate(180deg);
            }

            .dropdown-menu {
                list-style: none;
                padding: 0;
                margin: 0;
                background: rgba(0, 0, 0, 0.2);
                overflow: hidden;
                max-height: 0;
                opacity: 0;
                transition:
                    max-height 0.4s ease,
                    opacity 0.4s ease,
                    transform 0.4s ease;
                border-radius: 0 0 8px 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                position: relative;
                z-index: 999;
                transform: scaleY(0);
                transform-origin: top;
            }

            .dropdown-menu.show {
                max-height: 500px;
                opacity: 1;
                transform: scaleY(1);
            }

            /* Ensure dropdown is visible when sidebar is collapsed */
            .sidebar.collapsed .dropdown-menu {
                position: absolute;
                left: 70px; /* Width of collapsed sidebar */
                top: 0;
                background: var(--secondary-color);
                min-width: 200px;
                z-index: 1000;
                border-radius: 8px;
                box-shadow: 3px 0 10px rgba(0, 0, 0, 0.2);
            }

            .dropdown-item {
                display: flex;
                align-items: center;
                padding: 12px 20px 12px 50px;
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                transition: all 0.3s ease;
                font-size: 14px;
                border-left: 3px solid transparent;
                position: relative;
                z-index: 1000;
            }

            .dropdown-item:hover {
                background: rgba(255, 255, 255, 0.15);
                color: white;
                border-left: 3px solid var(--accent-color);
            }

            .dropdown-item.active {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                border-left: 3px solid white;
            }

            .dropdown-item i {
                font-size: 14px;
                margin-right: 12px;
                width: 16px;
                text-align: center;
            }

            .nav-item {
                position: relative;
                margin-bottom: 5px;
                z-index: 1001;
            }

            .sidebar-footer {
                padding: 15px 20px;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .logout-btn {
                display: flex;
                align-items: center;
                width: 100%;
                padding: 10px 15px;
                background: rgba(244, 67, 54, 0.2);
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                font-size: 14px;
            }

            .logout-btn:hover {
                background: rgba(244, 67, 54, 0.3);
                transform: translateY(-2px);
            }

            .logout-btn i {
                font-size: 16px;
                margin-right: 10px;
            }

            /* Main Content Styles */
            .main-content {
                margin-left: 260px;
                transition: margin-left 0.3s ease;
                min-height: 100vh;
                padding-bottom: 60px;
            }

            .main-content.expanded {
                margin-left: 70px;
            }

            /* Top Navigation */
            .top-nav {
                background: white;
                padding: 15px 30px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                border-bottom: 1px solid #eaecef;
                position: sticky;
                top: 0;
                z-index: 100;
            }

            .nav-toggle {
                background: none;
                border: none;
                font-size: 24px;
                color: #333;
                cursor: pointer;
                padding: 8px;
                border-radius: 5px;
                transition: background 0.3s;
            }

            .nav-toggle:hover {
                background: #f0f0f0;
            }

            .page-title {
                font-size: 20px;
                font-weight: 600;
                color: #333;
            }

            .user-menu {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .user-profile {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .user-avatar-small {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: linear-gradient(
                    135deg,
                    var(--primary-color) 0%,
                    var(--secondary-color) 100%
                );
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
            }

            .user-info-small {
                text-align: right;
            }

            .user-name-small {
                font-size: 14px;
                font-weight: 600;
                color: #333;
            }

            .user-role-small {
                font-size: 12px;
                color: #777;
            }

            /* Content Area */
            .content-area {
                padding: 30px;
            }

            .page-header {
                margin-bottom: 30px;
            }

            .page-title-large {
                font-size: 28px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 10px;
            }

            .page-description {
                color: #7f8c8d;
                font-size: 14px;
            }

            /* Stats Cards */
            .stats-container {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 25px;
                margin-bottom: 30px;
            }

            .stat-card {
                background: white;
                border-radius: 12px;
                padding: 20px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                transition:
                    transform 0.3s ease,
                    box-shadow 0.3s ease;
                border-left: 4px solid var(--primary-color);
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 15px;
                font-size: 24px;
            }

            .bg-green {
                background: linear-gradient(
                    135deg,
                    var(--primary-color) 0%,
                    #3d8b40 100%
                );
                color: white;
            }

            .bg-blue {
                background: linear-gradient(135deg, #1976d2 0%, #2196f3 100%);
                color: white;
            }

            .bg-orange {
                background: linear-gradient(135deg, #fb8c00 0%, #ff9800 100%);
                color: white;
            }

            .bg-purple {
                background: linear-gradient(135deg, #7e57c2 0%, #9575cd 100%);
                color: white;
            }

            .stat-number {
                font-size: 28px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 5px;
            }

            .stat-label {
                font-size: 14px;
                color: #7f8c8d;
            }

            /* Charts and Tables */
            .dashboard-row {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 25px;
                margin-bottom: 30px;
            }

            .card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                padding: 20px;
            }

            .card-header {
                font-size: 18px;
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }

            .recent-activity {
                list-style: none;
                padding: 0;
            }

            .activity-item {
                display: flex;
                padding: 12px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .activity-item:last-child {
                border-bottom: none;
            }

            .activity-icon {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
                flex-shrink: 0;
            }

            .activity-content {
                flex: 1;
            }

            .activity-title {
                font-weight: 600;
                color: #2c3e50;
                margin-bottom: 3px;
            }

            .activity-desc {
                font-size: 13px;
                color: #7f8c8d;
                margin-bottom: 3px;
            }

            .activity-time {
                font-size: 12px;
                color: #95a5a6;
            }

            /* Footer */
            .dashboard-footer {
                background: white;
                padding: 20px 30px;
                text-align: center;
                font-size: 14px;
                color: #7f8c8d;
                border-top: 1px solid #eee;
                margin-top: 30px;
            }

            /* Responsive */
            @media (max-width: 992px) {
                .dashboard-row {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 768px) {
                .sidebar {
                    transform: translateX(-100%);
                    width: 260px;
                }

                .sidebar.active {
                    transform: translateX(0);
                }

                .main-content {
                    margin-left: 0;
                }

                .stats-container {
                    grid-template-columns: 1fr;
                }

                .top-nav {
                    padding: 15px;
                }

                .sidebar-toggle {
                    display: block;
                }

                .overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 999;
                    display: none;
                }

                .overlay.active {
                    display: block;
                }
            }

            .sidebar.collapsed .sidebar-logo span,
            .sidebar.collapsed .user-info,
            .sidebar.collapsed .nav-text,
            .sidebar.collapsed .dropdown-icon,
            .sidebar.collapsed .logout-btn span {
                display: none;
            }

            .sidebar.collapsed .sidebar-logo {
                justify-content: center;
            }

            .sidebar.collapsed .user-avatar {
                margin-right: 0;
            }

            .sidebar.collapsed .nav-link {
                justify-content: center;
            }

            .sidebar.collapsed .nav-item.has-dropdown .dropdown-menu {
                display: none;
            }

            /* Show dropdown when sidebar is collapsed but dropdown is active */
            .sidebar.collapsed .nav-item.has-dropdown.active .dropdown-menu {
                position: absolute;
                left: 70px; /* Width of collapsed sidebar */
                top: 0;
                background: var(--secondary-color);
                min-width: 200px;
                display: block !important;
                z-index: 1000;
                border-radius: 8px;
                box-shadow: 3px 0 10px rgba(0, 0, 0, 0.2);
            }

            /* Flexbox layout for sidebar and main content */
            .main-content-wrapper {
                min-height: 100vh;
            }

            .main-content {
                margin-left: 260px;
                transition: margin-left 0.3s ease;
                width: calc(100% - 260px);
                min-height: 100vh;
            }

            .main-content.expanded {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            /* Responsive */
            @media (max-width: 768px) {
                .main-content {
                    margin-left: 0;
                    width: 100%;
                }
            }
        </style>
    </head>
    <body class="overflow-x-hidden">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Overlay for mobile -->
        <div class="overlay" id="overlay"></div>

        <!-- Main Content -->
        <div class="main-content-wrapper d-flex">
            <div class="main-content flex-grow-1" id="mainContent">
                <!-- Top Navigation -->
                <nav class="top-nav">
                    <div class="d-flex align-items-center">
                        <button class="nav-toggle" id="sidebarCollapse">
                            <i class="bi bi-list"></i>
                        </button>
                        <h1 class="page-title ms-3">
                            @yield('page-title', 'Dashboard')
                        </h1>
                    </div>
                    <div class="user-menu">
                        <div class="user-profile">
                            <div class="user-avatar-small">
                                {{
                                    strtoupper(
                                        substr(
                                            session("user_name", "A")[0] ?? "A",
                                            0,
                                            1
                                        )
                                    )
                                }}
                            </div>
                            <div class="user-info-small">
                                <div class="user-name-small">
                                    {{ session("user_name") ?? "Admin" }}
                                </div>
                                <div class="user-role-small">Administrator</div>
                            </div>
                        </div>
                        <a
                            href="{{ route('logout') }}"
                            class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('Yakin ingin logout?');"
                        >
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </div>
                </nav>

                <!-- Content Area -->
                <div class="content-area">@yield('content')</div>

                <!-- Footer -->
                <footer class="dashboard-footer">
                    <p>&copy; 2026 SIM-PROMOSI. All rights reserved.</p>
                </footer>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            // Toggle dropdown menus
            document.addEventListener("DOMContentLoaded", function () {
                const dropdownToggles =
                    document.querySelectorAll(".dropdown-toggle");

                dropdownToggles.forEach((toggle) => {
                    toggle.addEventListener("click", function (e) {
                        e.preventDefault();

                        // Close other dropdowns
                        dropdownToggles.forEach((otherToggle) => {
                            if (otherToggle !== toggle) {
                                const otherDropdown =
                                    otherToggle.nextElementSibling;
                                const otherNavItem =
                                    otherToggle.closest(".nav-item");
                                if (
                                    otherDropdown &&
                                    otherDropdown.classList.contains(
                                        "dropdown-menu",
                                    )
                                ) {
                                    otherDropdown.classList.remove("show");
                                    otherToggle.classList.remove("active");
                                    if (otherNavItem)
                                        otherNavItem.classList.remove("active");
                                }
                            }
                        });

                        // Toggle current dropdown
                        const dropdown = this.nextElementSibling;
                        const navItem = this.closest(".nav-item");
                        if (
                            dropdown &&
                            dropdown.classList.contains("dropdown-menu")
                        ) {
                            dropdown.classList.toggle("show");
                            this.classList.toggle("active");
                            if (navItem) navItem.classList.toggle("active");

                            // Smooth scroll to dropdown if needed
                            if (dropdown.classList.contains("show")) {
                                dropdown.scrollIntoView({
                                    behavior: "smooth",
                                    block: "nearest",
                                    inline: "start",
                                });
                            }
                        }
                    });
                });

                // Toggle sidebar collapse
                const sidebarCollapseBtn =
                    document.getElementById("sidebarCollapse");
                const sidebar = document.getElementById("sidebar");
                const mainContent = document.getElementById("mainContent");

                if (sidebarCollapseBtn) {
                    sidebarCollapseBtn.addEventListener("click", function () {
                        sidebar.classList.toggle("collapsed");
                        mainContent.classList.toggle("expanded");
                    });
                }

                // Mobile sidebar toggle
                const sidebarToggle = document.getElementById("sidebarToggle");
                const overlay = document.getElementById("overlay");

                if (sidebarToggle) {
                    sidebarToggle.addEventListener("click", function () {
                        sidebar.classList.toggle("active");
                        overlay.classList.toggle("active");
                    });
                }

                if (overlay) {
                    overlay.addEventListener("click", function () {
                        sidebar.classList.remove("active");
                        overlay.classList.remove("active");
                    });
                }

                // Prevent back button
                window.history.forward();
            });
        </script>
        @livewireScripts
    </body>
</html>
