<?php
// C:\xampp\htdocs\سيارة\admin\index.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Enforce admin access
if (!isAdmin()) {
 header("Location: ../auth.php");
 exit;
}

$lang = getLanguage();
$dir = getDirection();

// Fetch statistics
$usersCount = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$carsCount = $db->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$bookingsCount = $db->query("SELECT COUNT(*) FROM requests WHERE type = 'booking'")->fetchColumn();
$installmentsCount = $db->query("SELECT COUNT(*) FROM requests WHERE type = 'installment'")->fetchColumn();

// Estimated total sales from booked/delivered vehicles
$totalSales = $db->query("SELECT SUM(cars.price) 
 FROM requests 
 JOIN cars ON requests.car_id = cars.id 
 WHERE requests.status IN ('booked', 'delivered')")->fetchColumn() ?? 0;

// Fetch 5 most recent requests
$stmtRecent = $db->query("SELECT requests.*, cars.name_ar AS car_name_ar, cars.name_en AS car_name_en, cars.price AS car_price
 FROM requests
 JOIN cars ON requests.car_id = cars.id
 ORDER BY requests.id DESC
 LIMIT 5");
$recentRequests = $stmtRecent->fetchAll();

// Fetch most viewed cars
$mostViewed = $db->query("SELECT name_ar, name_en, views FROM cars ORDER BY views DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?= $lang === 'ar' ? 'لوحة تحكم المشرف' : 'Admin Dashboard' ?> | <?= __('app_name') ?></title>
 <link rel="stylesheet" href="../assets/css/style.css?v=5">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
 <script>
 // Apply theme settings on load
 document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'light');
 </script>
</head>
<body class="lang-<?= $lang ?>" style="background-color: var(--bg-primary);">

    <div class="admin-layout">
        <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
 <!-- Main Workspace -->
 <main class="admin-content">
 <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
 <div>
 <h1 style="font-weight: 800; font-size: 2rem;"><?= $lang === 'ar' ? 'لوحة تحكم الإدارة' : 'System Administration' ?></h1>
 <p style="color: var(--text-secondary);"><?= $lang === 'ar' ? 'نظرة عامة على حالة النظام والعمليات الحالية.' : 'Overview of system status and current actions.' ?></p>
 </div>
 <div style="display: flex; gap: 1rem; align-items: center;">
 <div class="control-btn" onclick="let theme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'; document.documentElement.setAttribute('data-theme', theme); localStorage.setItem('theme', theme); this.innerHTML = theme === 'dark' ? '<i class=\'bi bi-moon-fill\'></i>' : '<i class=\'bi bi-sun-fill\'></i>';"><i class="bi bi-sun-fill"></i></div>
 <span style="font-weight: 700;"> <?= e($_SESSION['user_name']) ?></span>
 </div>
 </header>

 <!-- Metrics Statistics Cards -->
 <div class="stats-grid">
 <div class="stat-card">
 <div>
 <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;"><?= $lang === 'ar' ? 'إجمالي العملاء' : 'Total Customers' ?></span>
 <div class="stat-num"><?= $usersCount ?></div>
 </div>
 <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
 </div>

 <div class="stat-card">
 <div>
 <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;"><?= $lang === 'ar' ? 'السيارات بالمعرض' : 'Cars in Inventory' ?></span>
 <div class="stat-num"><?= $carsCount ?></div>
 </div>
 <div class="stat-icon"><i class="bi bi-car-front-fill"></i></div>
 </div>

 <div class="stat-card">
 <div>
 <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;"><?= $lang === 'ar' ? 'طلبات الحجز' : 'Booking Orders' ?></span>
 <div class="stat-num"><?= $bookingsCount ?></div>
 </div>
 <div class="stat-icon"><i class="bi bi-bookmark-check-fill"></i></div>
 </div>

 <div class="stat-card">
 <div>
 <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;"><?= $lang === 'ar' ? 'طلبات التقسيط' : 'Finance Requests' ?></span>
 <div class="stat-num"><?= $installmentsCount ?></div>
 </div>
 <div class="stat-icon"><i class="bi bi-credit-card-2-front-fill"></i></div>
 </div>

 <div class="stat-card" style="grid-column: span 2;">
 <div>
 <span style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;"><?= $lang === 'ar' ? 'إجمالي المبيعات المقدرة' : 'Estimated Total Sales' ?></span>
 <div class="stat-num" style="color: var(--success);"><?= formatPrice($totalSales) ?></div>
 </div>
 <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.15); color: var(--success);"><i class="bi bi-cash-stack"></i></div>
 </div>
 </div>

 <!-- Two Column layouts -->
 <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem; margin-top: 2rem;">
 
 <!-- Col 1: Recent Requests table -->
 <div>
 <h3 style="font-weight: 800; margin-bottom: 1.25rem;"><i class="bi bi-clock-history" style="margin-right:0.5rem"></i><?= $lang === 'ar' ? 'أحدث الطلبات الواردة' : 'Latest Incoming Orders' ?></h3>
 <div class="table-container">
 <table class="admin-table">
 <thead>
 <tr>
 <th><?= $lang === 'ar' ? 'العميل' : 'Customer' ?></th>
 <th><?= $lang === 'ar' ? 'السيارة' : 'Vehicle' ?></th>
 <th><?= $lang === 'ar' ? 'النوع' : 'Type' ?></th>
 <th><?= $lang === 'ar' ? 'الحالة' : 'Status' ?></th>
 </tr>
 </thead>
 <tbody>
 <?php if (empty($recentRequests)): ?>
 <tr><td colspan="4" class="text-center"><?= $lang === 'ar' ? 'لا توجد طلبات بعد.' : 'No orders found.' ?></td></tr>
 <?php else: ?>
 <?php foreach ($recentRequests as $req): ?>
 <tr>
 <td>
 <strong><?= e($req['name']) ?></strong>
 <small style="display: block; color: var(--text-muted);"><?= e($req['phone']) ?></small>
 </td>
 <td><?= $lang === 'ar' ? $req['car_name_ar'] : $req['car_name_en'] ?></td>
 <td><?= $req['type'] === 'booking' ? __('book_now') : __('installment_offers') ?></td>
 <td>
 <span class="badge-status badge-<?= $req['status'] ?>"><?= __('status_' . $req['status']) ?></span>
 </td>
 </tr>
 <?php endforeach; ?>
 <?php endif; ?>
 </tbody>
 </table>
 </div>
 <div style="margin-top: 1rem; text-align: end;">
 <a href="requests.php" class="btn-action btn-action-primary" style="font-size: 0.85rem;"><?= $lang === 'ar' ? 'عرض جميع الطلبات' : 'View all requests' ?> →</a>
 </div>
 </div>

 <!-- Col 2: Most Viewed Cars report -->
 <div>
 <h3 style="font-weight: 800; margin-bottom: 1.25rem;"><i class="bi bi-fire text-danger" style="margin-right:0.5rem"></i><?= $lang === 'ar' ? 'السيارات الأكثر مشاهدة' : 'Most Popular Vehicles' ?></h3>
 <div style="background-color: var(--bg-secondary); border-radius: 16px; border: 1px solid var(--border-color); padding: 1.5rem; box-shadow: var(--card-shadow);">
 <ul style="display: flex; flex-direction: column; gap: 1rem;">
 <?php foreach ($mostViewed as $index => $mv): ?>
 <li style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-color);">
 <div style="display: flex; align-items: center; gap: 0.75rem;">
 <span style="font-weight: 800; color: var(--primary); font-size: 1.1rem;">#<?= $index + 1 ?></span>
 <strong><?= $lang === 'ar' ? $mv['name_ar'] : $mv['name_en'] ?></strong>
 </div>
 <span style="background-color: var(--input-bg); padding: 2px 8px; border-radius: 8px; font-size: 0.8rem; font-weight: bold;"><i class="bi bi-eye"></i> <?= $mv['views'] ?></span>
 </li>
 <?php endforeach; ?>
 </ul>
 </div>
 </div>
 </div>
 </main>
 </div>

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
</body>
</html>
