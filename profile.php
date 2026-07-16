<?php
// C:\xampp\htdocs\سيارة\profile.php
require_once __DIR__ . '/database/db.php';
require_once __DIR__ . '/shared/functions.php';

// Enforce login
if (!isLoggedIn()) {
 header("Location: auth.php");
 exit;
}

$user_id = $_SESSION['user_id'];
$lang = getLanguage();
$currentUser = getCurrentUser($db);

$activeTab = $_GET['tab'] ?? 'profile'; // 'profile', 'requests', 'favorites', 'notifications', 'settings'

$successMsg = '';
$errorMsg = '';

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
 $name = trim($_POST['name'] ?? '');
 $phone = trim($_POST['phone'] ?? '');
 $city = trim($_POST['city'] ?? '');
 
 if ($name === '') {
 $errorMsg = __('fill_required');
 } else {
 $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, city = ? WHERE id = ?");
 $stmt->execute([$name, $phone, $city, $user_id]);
 $_SESSION['user_name'] = $name;
 $successMsg = $lang === 'ar' ? 'تم تحديث البيانات بنجاح.' : 'Profile updated successfully.';
 $currentUser = getCurrentUser($db); // Refresh
 }
}

// Handle Password Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
 $oldPass = $_POST['old_password'] ?? '';
 $newPass = $_POST['new_password'] ?? '';
 
 if ($oldPass === '' || $newPass === '') {
 $errorMsg = __('fill_required');
 } else {
 // Verify old password
 if (password_verify($oldPass, $currentUser['password'])) {
 $hashed = password_hash($newPass, PASSWORD_BCRYPT);
 $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
 $stmt->execute([$hashed, $user_id]);
 $successMsg = $lang === 'ar' ? 'تم تغيير كلمة المرور بنجاح.' : 'Password changed successfully.';
 } else {
 $errorMsg = $lang === 'ar' ? 'كلمة المرور الحالية غير صحيحة.' : 'Current password is incorrect.';
 }
 }
}

// Handle notification mark as read
if (isset($_GET['read_notif'])) {
 $notifId = intval($_GET['read_notif']);
 $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND (user_id = ? OR user_id IS NULL)");
 $stmt->execute([$notifId, $user_id]);
 header("Location: profile.php?tab=notifications");
 exit;
}

require_once __DIR__ . '/shared/header.php';

// Fetch user requests
$stmtReq = $db->prepare("SELECT requests.*, cars.name_ar AS car_name_ar, cars.name_en AS car_name_en, cars.price AS car_price, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en
 FROM requests
 JOIN cars ON requests.car_id = cars.id
 JOIN brands ON cars.brand_id = brands.id
 WHERE requests.user_id = ?
 ORDER BY requests.id DESC");
$stmtReq->execute([$user_id]);
$requests = $stmtReq->fetchAll();

// Fetch user favorites
$stmtFav = $db->prepare("SELECT favorites.id AS fav_id, cars.*, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en
 FROM favorites
 JOIN cars ON favorites.car_id = cars.id
 JOIN brands ON cars.brand_id = brands.id
 WHERE favorites.user_id = ?
 ORDER BY favorites.id DESC");
$stmtFav->execute([$user_id]);
$favorites = $stmtFav->fetchAll();

// Fetch notifications
$stmtNot = $db->prepare("SELECT * FROM notifications 
 WHERE user_id IS NULL OR user_id = ? 
 ORDER BY id DESC");
$stmtNot->execute([$user_id]);
$notifications = $stmtNot->fetchAll();
?>

<div class="container section-padding">
 
 <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 3rem; background-color: var(--bg-secondary); padding: 2rem; border-radius: 20px; border: 1px solid var(--border-color); box-shadow: var(--card-shadow);">
 <div style="width: 70px; height: 70px; border-radius: 50%; background-color: var(--primary); display: flex; justify-content: center; align-items: center; color: #fff; font-size: 2.2rem; font-weight: 800;">
 <?= mb_substr($currentUser['name'], 0, 1) ?>
 </div>
 <div>
 <h2 style="font-weight: 800;"><?= e($currentUser['name']) ?></h2>
 <p style="color: var(--text-secondary); font-size: 0.9rem;"><?= e($currentUser['email']) ?> | <?= e($currentUser['city']) ?></p>
 </div>
 </div>

 <!-- Feedback alerts -->
 <?php if ($successMsg !== ''): ?>
 <div style="background-color: rgba(16, 185, 129, 0.15); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700; text-align: center; border: 1px solid rgba(16, 185, 129, 0.25);">
 <?= $successMsg ?>
 </div>
 <?php endif; ?>

 <?php if ($errorMsg !== ''): ?>
 <div style="background-color: rgba(239, 68, 68, 0.15); color: var(--danger); padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700; text-align: center; border: 1px solid rgba(239, 68, 68, 0.25);">
 <?= $errorMsg ?>
 </div>
 <?php endif; ?>

 <div class="profile-layout">
 <!-- Sidebar Menu -->
 <aside class="profile-sidebar">
 <div class="profile-sidebar-item <?= $activeTab === 'profile' ? 'active' : '' ?>" onclick="location.href='profile.php?tab=profile'">
 <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
 <?= $lang === 'ar' ? 'الملف الشخصي' : 'Profile Settings' ?>
 </div>
 <div class="profile-sidebar-item <?= $activeTab === 'requests' ? 'active' : '' ?>" onclick="location.href='profile.php?tab=requests'">
 <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1" ry="1"/></svg>
 <?= __('my_requests') ?>
 </div>
 <div class="profile-sidebar-item <?= $activeTab === 'favorites' ? 'active' : '' ?>" onclick="location.href='profile.php?tab=favorites'">
 <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
 <?= __('favorites') ?>
 </div>
 <div class="profile-sidebar-item <?= $activeTab === 'notifications' ? 'active' : '' ?>" onclick="location.href='profile.php?tab=notifications'">
 <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
 <?= __('notifications') ?>
 </div>
 <div class="profile-sidebar-item <?= $activeTab === 'settings' ? 'active' : '' ?>" onclick="location.href='profile.php?tab=settings'">
 <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
 <?= __('settings') ?>
 </div>
 </aside>

 <!-- Main Content Pane -->
 <main style="background-color: var(--bg-secondary); border-radius: 20px; padding: 2rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow);">
 
 <!-- 1. PROFILE DETAILS TAB -->
 <?php if ($activeTab === 'profile'): ?>
 <h4 style="font-weight: 800; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 2rem;"><?= $lang === 'ar' ? 'تعديل البيانات الشخصية' : 'Edit Profile Info' ?></h4>
 <form action="profile.php?tab=profile" method="POST" style="max-width: 500px; margin-bottom: 3rem;">
 <input type="hidden" name="update_profile" value="1">
 <div class="form-group">
 <label><?= __('full_name') ?></label>
 <input type="text" name="name" class="form-control" value="<?= e($currentUser['name']) ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('phone_number') ?></label>
 <input type="tel" name="phone" class="form-control" value="<?= e($currentUser['phone']) ?>">
 </div>
 <div class="form-group">
 <label><?= __('city_label') ?></label>
 <input type="text" name="city" class="form-control" value="<?= e($currentUser['city']) ?>">
 </div>
 <button type="submit" class="btn-submit" style="margin-top: 1rem;"><?= $lang === 'ar' ? 'حفظ التعديلات' : 'Save Modifications' ?></button>
 </form>

 <h4 style="font-weight: 800; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 2rem;"><?= $lang === 'ar' ? 'تغيير كلمة المرور' : 'Change Password' ?></h4>
 <form action="profile.php?tab=profile" method="POST" style="max-width: 500px;">
 <input type="hidden" name="update_password" value="1">
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'كلمة المرور الحالية' : 'Current Password' ?></label>
 <input type="password" name="old_password" class="form-control" required>
 </div>
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'كلمة المرور الجديدة' : 'New Password' ?></label>
 <input type="password" name="new_password" class="form-control" required>
 </div>
 <button type="submit" class="btn-submit" style="margin-top: 1rem; background-color: var(--accent);"><?= $lang === 'ar' ? 'تحديث كلمة المرور' : 'Update Password' ?></button>
 </form>

 <!-- 2. MY REQUESTS TAB -->
 <?php elseif ($activeTab === 'requests'): ?>
 <h4 style="font-weight: 800; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 2rem;"><?= __('my_requests') ?></h4>
 
 <?php if (empty($requests)): ?>
 <p class="text-center" style="color: var(--text-secondary); padding: 2rem 0;"><?= __('requests_empty') ?></p>
 <?php else: ?>
 <div style="display: flex; flex-direction: column; gap: 2rem;">
 <?php foreach ($requests as $req): 
 $isFinancing = $req['type'] === 'installment';
 ?>
 <div style="border: 1px solid var(--border-color); border-radius: 16px; padding: 1.5rem; background-color: var(--bg-primary);">
 <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
 <div>
 <h5 style="font-size: 1.1rem; font-weight: 700;"><?= $lang === 'ar' ? $req['car_name_ar'] : $req['car_name_en'] ?></h5>
 <span style="font-size: 0.8rem; color: var(--text-muted);"><?= $req['created_at'] ?> | ID: #<?= $req['id'] ?></span>
 </div>
 <span class="badge-status badge-<?= $req['status'] ?>"><?= __('status_' . $req['status']) ?></span>
 </div>

 <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; font-size: 0.85rem; padding: 1rem 0; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
 <div><strong><?= $lang === 'ar' ? 'نوع الطلب' : 'Request Type' ?>:</strong> <?= $isFinancing ? __('installment_offers') : __('book_now') ?></div>
 <div><strong><?= __('price') ?>:</strong> <?= formatPrice($req['car_price']) ?></div>
 <?php if ($isFinancing): ?>
 <div><strong><?= __('downpayment') ?>:</strong> <?= formatPrice($req['downpayment']) ?></div>
 <div><strong><?= __('monthly_installment') ?>:</strong> <?= formatPrice($req['monthly_installment']) ?> / <?= __('month') ?></div>
 <div><strong><?= __('duration') ?>:</strong> <?= $req['term_months'] ?> <?= __('month') ?></div>
 <?php endif; ?>
 </div>

 <!-- Workflow checklist visual tracker -->
 <div class="workflow-tracker">
 <?php
 $statuses = ['received', 'reviewing', 'contacting', 'booked', 'delivered'];
 $currentStatusIdx = array_search($req['status'], $statuses);
 if ($req['status'] === 'rejected') $currentStatusIdx = -1; // If rejected, hide progress
 
 foreach ($statuses as $idx => $s):
 $class = '';
 if ($idx < $currentStatusIdx) $class = 'completed';
 elseif ($idx === $currentStatusIdx) $class = 'active';
 ?>
 <div class="workflow-step <?= $class ?>">
 <div class="workflow-circle"><?= $idx + 1 ?></div>
 <span class="workflow-title"><?= __('status_' . $s) ?></span>
 </div>
 <?php endforeach; ?>
 </div>
 </div>
 <?php endforeach; ?>
 </div>
 <?php endif; ?>

 <!-- 3. SAVED CARS (FAVORITES) TAB -->
 <?php elseif ($activeTab === 'favorites'): ?>
 <h4 style="font-weight: 800; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 2rem;"><?= __('favorites') ?></h4>
 
 <?php if (empty($favorites)): ?>
 <p class="text-center" style="color: var(--text-secondary); padding: 2rem 0;"><?= __('favorites_empty') ?></p>
 <?php else: ?>
 <div class="cars-grid">
 <?php foreach ($favorites as $f): ?>
 <div class="car-card">
 <div class="car-badge"><?= __('new_car_status') ?></div>
 <div class="fav-btn active" data-car-id="<?= $f['id'] ?>">️</div>
 <div class="car-img-container">
 <div class="car-image-mock"><?= $lang === 'ar' ? $f['name_ar'] : $f['name_en'] ?></div>
 </div>
 <div class="car-info">
 <div class="car-meta-year"><?= $f['year'] ?> | <?= $lang === 'ar' ? $f['type_ar'] : $f['type_en'] ?></div>
 <h4 class="car-title"><?= $lang === 'ar' ? $f['name_ar'] : $f['name_en'] ?></h4>
 <div class="car-price-row">
 <div class="car-cash-price"><small><?= __('price') ?></small><?= formatPrice($f['price']) ?></div>
 <div class="car-inst-price"><small><?= __('installment_starts') ?></small><span class="car-inst-val"><?= formatPrice($f['min_installment']) ?></span></div>
 </div>
 <div class="car-actions">
 <a href="car.php?id=<?= $f['id'] ?>" class="car-details-btn"><?= __('view_details') ?></a>
 <div class="car-compare-checkbox" data-car-id="<?= $f['id'] ?>">️</div>
 </div>
 </div>
 </div>
 <?php endforeach; ?>
 </div>
 <?php endif; ?>

 <!-- 4. SYSTEM NOTIFICATIONS TAB -->
 <?php elseif ($activeTab === 'notifications'): ?>
 <h4 style="font-weight: 800; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 2rem;"><?= __('notifications') ?></h4>
 
 <?php if (empty($notifications)): ?>
 <p class="text-center" style="color: var(--text-secondary); padding: 2rem 0;"><?= __('no_notifications') ?></p>
 <?php else: ?>
 <div style="display: flex; flex-direction: column; gap: 1rem;">
 <?php foreach ($notifications as $not): ?>
 <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; background-color: <?= $not['is_read'] ? 'var(--bg-primary)' : 'var(--bg-secondary)' ?>; border-left: 4px solid <?= $not['is_read'] ? 'var(--text-muted)' : 'var(--primary)' ?>; position: relative;">
 <h5 style="font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem;"><?= $lang === 'ar' ? $not['title_ar'] : $not['title_en'] ?></h5>
 <p style="font-size: 0.85rem; color: var(--text-secondary);"><?= $lang === 'ar' ? $not['message_ar'] : $not['message_en'] ?></p>
 <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.5rem;"><?= $not['created_at'] ?></span>
 
 <?php if (!$not['is_read']): ?>
 <a href="profile.php?read_notif=<?= $not['id'] ?>" class="btn-action btn-action-primary" style="position: absolute; top: 15px; left: 15px; font-size: 0.75rem; padding: 2px 8px;"><?= $lang === 'ar' ? 'تحديد كمقروء' : 'Mark Read' ?></a>
 <?php endif; ?>
 </div>
 <?php endforeach; ?>
 </div>
 <?php endif; ?>

 <!-- 5. GENERAL PREFERENCES SETTINGS -->
 <?php elseif ($activeTab === 'settings'): ?>
 <h4 style="font-weight: 800; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 2rem;"><?= __('settings') ?></h4>
 
 <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 500px;">
 <!-- Language selector -->
 <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
 <div>
 <strong><?= __('language') ?></strong>
 <p style="font-size: 0.8rem; color: var(--text-secondary);"><?= $lang === 'ar' ? 'تعديل لغة واجهة التطبيق' : 'Change application layout language' ?></p>
 </div>
 <div style="display: flex; gap: 0.5rem;">
 <button onclick="switchLanguage('ar')" class="btn-action <?= $lang === 'ar' ? 'btn-action-primary' : 'btn-action-secondary' ?>">العربية</button>
 <button onclick="switchLanguage('en')" class="btn-action <?= $lang === 'en' ? 'btn-action-primary' : 'btn-action-secondary' ?>">English</button>
 </div>
 </div>

 <!-- Theme setting -->
 <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
 <div>
 <strong><?= __('theme_mode') ?></strong>
 <p style="font-size: 0.8rem; color: var(--text-secondary);"><?= $lang === 'ar' ? 'التبديل بين المظهر الداكن والمضيء' : 'Choose dark or light appearance' ?></p>
 </div>
 <button id="profile-theme-btn" class="btn-action btn-action-secondary" onclick="document.getElementById('theme-toggle').click();"> <?= $lang === 'ar' ? 'تغيير المظهر' : 'Toggle' ?></button>
 </div>

 <!-- Notifications preference toggle -->
 <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
 <div>
 <strong><?= $lang === 'ar' ? 'إشعارات البريد الإلكتروني' : 'Email Alerts' ?></strong>
 <p style="font-size: 0.8rem; color: var(--text-secondary);"><?= $lang === 'ar' ? 'استقبال رسائل إلكترونية عند تحديث حالة الطلبات' : 'Receive emails when request workflow status changes' ?></p>
 </div>
 <input type="checkbox" checked style="width: 20px; height: 20px;">
 </div>
 </div>
 <?php endif; ?>

 </main>
 </div>
</div>

<?php
require_once __DIR__ . '/shared/footer.php';
?>
