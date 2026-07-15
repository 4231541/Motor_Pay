<?php
// C:\xampp\htdocs\سيارة\admin\includes\sidebar.php
// Shared admin sidebar with SVG icons - no emojis
$lang = getLanguage();
$currentAdminPage = basename($_SERVER['PHP_SELF']);
?><!-- Mobile top bar (visible ≤992px) -->
<div class="admin-mobile-bar" id="admin-mobile-bar">
    <div class="admin-mobile-logo">
        <span style="color: var(--gold);">MOTOR</span>&nbsp;<span style="color:#fff;">PAY</span>
    </div>
    <button class="admin-hamburger" id="admin-hamburger" aria-label="القائمة">
        <span></span><span></span><span></span>
    </button>
</div>

<!-- Sidebar overlay -->
<div class="admin-sidebar-overlay" id="admin-sidebar-overlay"></div><aside class="admin-sidebar" id="admin-sidebar">
    <a href="../index.php" class="logo-link" style="margin-bottom: 2rem;">
        <div class="logo-img-wrap">
            <img src="../assets/images/logo.jpg" alt="Motor Pay">
        </div>
        <span><span class="logo-text-motor">MOTOR</span> <span style="color: #fff;">PAY</span></span>
    </a>

    <div class="admin-menu">
        <a href="index.php" class="admin-menu-item <?= $currentAdminPage === 'index.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
            <?= $lang === 'ar' ? 'الإحصائيات والتقارير' : 'Dashboard Stats' ?>
        </a>
        <a href="cars.php" class="admin-menu-item <?= $currentAdminPage === 'cars.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="1" y="13" width="22" height="7" rx="2"/><path d="M3 13l2-5h14l2 5"/><circle cx="6" cy="20" r="2.5"/><circle cx="18" cy="20" r="2.5"/></svg>
            <?= $lang === 'ar' ? 'إدارة السيارات' : 'Manage Cars' ?>
        </a>
        <a href="brands.php" class="admin-menu-item <?= $currentAdminPage === 'brands.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <?= $lang === 'ar' ? 'الماركات والموديلات' : 'Brands & Models' ?>
        </a>
        <a href="requests.php" class="admin-menu-item <?= $currentAdminPage === 'requests.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            <?= $lang === 'ar' ? 'طلبات الحجز والتمويل' : 'Manage Requests' ?>
        </a>
        <a href="users.php" class="admin-menu-item <?= $currentAdminPage === 'users.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <?= $lang === 'ar' ? 'إدارة العملاء' : 'Customers Directory' ?>
        </a>
        <a href="offers.php" class="admin-menu-item <?= $currentAdminPage === 'offers.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            <?= $lang === 'ar' ? 'إدارة العروض' : 'Manage Offers' ?>
        </a>
        <a href="notifications.php" class="admin-menu-item <?= $currentAdminPage === 'notifications.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <?= $lang === 'ar' ? 'الإشعارات' : 'Notifications' ?>
        </a>
        <a href="settings.php" class="admin-menu-item <?= $currentAdminPage === 'settings.php' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <?= $lang === 'ar' ? 'إعدادات النظام' : 'System Settings' ?>
        </a>
        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 1rem 0;">
        <a href="../index.php" class="admin-menu-item" style="color: var(--danger);">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <?= $lang === 'ar' ? 'الرجوع للموقع' : 'Back to Portal' ?>
        </a>
    </div>
</aside>

<!-- Mobile Sidebar Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const hamburger = document.getElementById('admin-hamburger');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('admin-sidebar-overlay');

        if (hamburger && sidebar && overlay) {
            function toggleMenu() {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            }
            hamburger.addEventListener('click', toggleMenu);
            overlay.addEventListener('click', toggleMenu);
        }
    });
</script>
