<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="bi bi-megaphone"></i>
            <span>SIM-PROMOSI</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="user-info">
            <div class="user-name">{{ session('user_name') ?? 'Admin' }}</div>
            <div class="user-role">Administrator</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item has-dropdown">
                <a href="#" class="nav-link {{ request()->routeIs('master-data*') ? 'active' : '' }} dropdown-toggle">
                    <i class="bi bi-database"></i>
                    <span class="nav-text">Master Data</span>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('master-data.institutions') }}" class="dropdown-item {{ request()->routeIs('master-data.institutions') ? 'active' : '' }}"><i class="bi bi-building"></i> <span>Data Institusi</span></a></li>
                    <li><a href="{{ route('master-data.departments') }}" class="dropdown-item {{ request()->routeIs('master-data.departments') ? 'active' : '' }}"><i class="bi bi-people"></i> <span>Data Departemen</span></a></li>
                    <li><a href="{{ route('master-data.users') }}" class="dropdown-item {{ request()->routeIs('master-data.users') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> <span>Data Pengguna</span></a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('schedules.index') }}" class="nav-link {{ request()->routeIs('schedules*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i>
                    <span class="nav-text">Jadwal</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('letters.index') }}" class="nav-link {{ request()->routeIs('letters*') ? 'active' : '' }}">
                    <i class="bi bi-envelope"></i>
                    <span class="nav-text">Surat Promos / Perumaru</span>
                </a>
            </li>

            <li class="nav-item has-dropdown">
                <a href="#" class="nav-link {{ request()->routeIs('activities*') ? 'active' : '' }} dropdown-toggle">
                    <i class="bi bi-megaphone"></i>
                    <span class="nav-text">Pelaksanaan Promosi</span>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('activities.roadshow') }}" class="dropdown-item {{ request()->routeIs('activities.roadshow') ? 'active' : '' }}"><i class="bi bi-megaphone"></i> <span>Roadshow</span></a></li>
                    <li><a href="{{ route('activities.expo') }}" class="dropdown-item {{ request()->routeIs('activities.expo') ? 'active' : '' }}"><i class="bi bi-ticket-perforated"></i> <span>Expo</span></a></li>
                    <li><a href="{{ route('activities.sponsorship') }}" class="dropdown-item {{ request()->routeIs('activities.sponsorship') ? 'active' : '' }}"><i class="bi bi-gift"></i> <span>Sponsorship</span></a></li>
                    <li><a href="{{ route('activities.tour') }}" class="dropdown-item {{ request()->routeIs('activities.tour') ? 'active' : '' }}"><i class="bi bi-buildings"></i> <span>Wisata Kampus</span></a></li>
                    <li><a href="{{ route('activities.presentation') }}" class="dropdown-item {{ request()->routeIs('activities.presentation') ? 'active' : '' }}"><i class="bi bi-projector"></i> <span>Presentasi & AMT</span></a></li>
                    <li><a href="{{ route('activities.other') }}" class="dropdown-item {{ request()->routeIs('activities.other') ? 'active' : '' }}"><i class="bi bi-stars"></i> <span>Promosi Lain</span></a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span class="nav-text">Laporan</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Pengaturan</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="logout-btn" onclick="return confirm('Yakin ingin logout?');">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>
</div>