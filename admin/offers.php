<?php
// C:\xampp\htdocs\سيارة\admin\offers.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
 header("Location: ../auth.php");
 exit;
}

$lang = getLanguage();
$dir = getDirection();

$action = $_GET['action'] ?? 'list';
$offerId = intval($_GET['id'] ?? 0);

$message = '';
$messageType = '';

// Handle Delete Action
if ($action === 'delete' && $offerId > 0) {
 try {
 $db->prepare("DELETE FROM offers WHERE id = ?")->execute([$offerId]);
 header("Location: offers.php?msg=deleted");
 exit;
 } catch (Exception $e) {
 $message = "Error: " . $e->getMessage();
 $messageType = 'danger';
 }
}

// Handle Form Submission (Add Offer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $title_ar = trim($_POST['title_ar']);
 $title_en = trim($_POST['title_en']);
 $description_ar = trim($_POST['description_ar']);
 $description_en = trim($_POST['description_en']);
 $discount_pct = floatval($_POST['discount_pct'] ?? 0);
 $car_id = intval($_POST['car_id']);
 $valid_until = trim($_POST['valid_until']);
 
 if ($title_ar === '' || $title_en === '' || $valid_until === '') {
 $message = __('fill_required');
 $messageType = 'danger';
 } else {
 try {
 $stmt = $db->prepare("INSERT INTO offers (title_ar, title_en, description_ar, description_en, discount_pct, car_id, valid_until, image) VALUES (?, ?, ?, ?, ?, ?, ?, 'offer_placeholder.jpg')");
 $stmt->execute([$title_ar, $title_en, $description_ar, $description_en, $discount_pct, $car_id ?: null, $valid_until]);
 
 header("Location: offers.php?msg=added");
 exit;
 } catch (Exception $e) {
 $message = "Database Error: " . $e->getMessage();
 $messageType = 'danger';
 }
 }
}

if (isset($_GET['msg'])) {
 if ($_GET['msg'] === 'added') {
 $message = "Offer campaign successfully created!";
 $messageType = 'success';
 } elseif ($_GET['msg'] === 'deleted') {
 $message = "Offer campaign successfully deleted.";
 $messageType = 'success';
 }
}

// Fetch all cars for dropdown
$cars = $db->query("SELECT id, name_ar, name_en FROM cars ORDER BY name_en ASC")->fetchAll();

// Fetch active offers
$offers = $db->query("SELECT offers.*, cars.name_ar AS car_name_ar, cars.name_en AS car_name_en 
 FROM offers 
 LEFT JOIN cars ON offers.car_id = cars.id 
 ORDER BY offers.id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?= $lang === 'ar' ? 'إدارة العروض والخصومات' : 'Offers Management' ?></title>
 <link rel="stylesheet" href="../assets/css/style.css?v=5">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
 <script>
 document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'light');
 </script>
</head>
<body class="lang-<?= $lang ?>" style="background-color: var(--bg-primary);">

 <div class="admin-layout">
 <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

 <!-- Main Workspace -->
 <main class="admin-content">
 <header style="margin-bottom: 3rem;">
 <h1 style="font-weight: 800; font-size: 2rem;"><?= $lang === 'ar' ? 'إدارة العروض الترويجية' : 'Offers & Campaigns' ?></h1>
 <p style="color: var(--text-secondary);"><?= $lang === 'ar' ? 'إضافة وتعديل عروض التقسيط والخصومات النقدية الخاصة بالسيارات.' : 'Create, customize, and edit promotional campaign listings.' ?></p>
 </header>

 <!-- Alerts Banner -->
 <?php if ($message !== ''): ?>
 <div style="background-color: <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; color: <?= $messageType === 'success' ? 'var(--success)' : 'var(--danger)' ?>; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700; border: 1px solid <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.25)' : 'rgba(239, 68, 68, 0.25)' ?>;">
 <?= $message ?>
 </div>
 <?php endif; ?>

 <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
 
 <!-- Col 1: Add Offer form -->
 <div style="background-color: var(--bg-secondary); border-radius: 16px; padding: 2rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow); height: fit-content;">
 <h3 style="font-weight: 800; margin-bottom: 1.5rem;"><?= $lang === 'ar' ? 'إنشاء حملة عروض جديدة' : 'Create Offer Campaign' ?></h3>
 
 <form action="offers.php" method="POST">
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'عنوان العرض (عربي)' : 'Offer Title (Arabic)' ?> *</label>
 <input type="text" name="title_ar" class="form-control" placeholder="مثال: خصم الصيف المميز" required>
 </div>
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'عنوان العرض (إنجليزي)' : 'Offer Title (English)' ?> *</label>
 <input type="text" name="title_en" class="form-control" placeholder="e.g. Summer Special Sale" required>
 </div>
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'الوصف التفصيلي (عربي)' : 'Details (Arabic)' ?></label>
 <textarea name="description_ar" class="form-control" rows="3"></textarea>
 </div>
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'الوصف التفصيلي (إنجليزي)' : 'Details (English)' ?></label>
 <textarea name="description_en" class="form-control" rows="3"></textarea>
 </div>
 
 <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'نسبة الخصم (%)' : 'Discount Percentage (%)' ?></label>
 <input type="number" name="discount_pct" class="form-control" min="0" max="100" value="0">
 </div>
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'السيارة المشمولة بالعرض' : 'Applicable Car' ?></label>
 <select name="car_id" class="form-control">
 <option value="0"><?= $lang === 'ar' ? '-- لا يوجد (عرض عام) --' : '-- None (General Offer) --' ?></option>
 <?php foreach ($cars as $c): ?>
 <option value="<?= $c['id'] ?>"><?= $lang === 'ar' ? $c['name_ar'] : $c['name_en'] ?></option>
 <?php endforeach; ?>
 </select>
 </div>
 </div>

 <div class="form-group">
 <label><?= $lang === 'ar' ? 'صلاحية العرض حتى تاريخ' : 'Campaign Deadline (Expiry Date)' ?> *</label>
 <input type="date" name="valid_until" class="form-control" required>
 </div>

 <button type="submit" class="btn-submit"><?= $lang === 'ar' ? 'إطلاق الحملة الترويجية' : 'Launch Campaign' ?></button>
 </form>
 </div>

 <!-- Col 2: Active Offers list -->
 <div>
 <h3 style="font-weight: 800; margin-bottom: 1.25rem;"><i class="bi bi-megaphone-fill text-success" style="margin-right:0.5rem"></i><?= $lang === 'ar' ? 'الحملات الفعالة حالياً' : 'Active Offers' ?></h3>
 <div class="table-container">
 <table class="admin-table">
 <thead>
 <tr>
 <th><?= $lang === 'ar' ? 'العنوان' : 'Title' ?></th>
 <th><?= $lang === 'ar' ? 'الخصم والسيارة' : 'Car details' ?></th>
 <th><?= $lang === 'ar' ? 'تاريخ الانتهاء' : 'Expiry' ?></th>
 <th><?= $lang === 'ar' ? 'التحكم' : 'Action' ?></th>
 </tr>
 </thead>
 <tbody>
 <?php if (empty($offers)): ?>
 <tr><td colspan="4" class="text-center"><?= $lang === 'ar' ? 'لا توجد عروض ترويجية مسجلة.' : 'No active offers recorded.' ?></td></tr>
 <?php else: ?>
 <?php foreach ($offers as $off): ?>
 <tr>
 <td>
 <strong><?= $lang === 'ar' ? $off['title_ar'] : $off['title_en'] ?></strong>
 </td>
 <td>
 <?php if ($off['discount_pct'] > 0): ?>
 <span class="badge-status badge-rejected" style="font-size: 0.7rem; padding: 2px 6px;">-<?= $off['discount_pct'] ?>%</span>
 <?php endif; ?>
 <small style="display: block; color: var(--text-muted);"><?= $off['car_id'] ? ($lang === 'ar' ? $off['car_name_ar'] : $off['car_name_en']) : 'General Offer' ?></small>
 </td>
 <td><span style="color: var(--danger); font-weight: bold;"><?= $off['valid_until'] ?></span></td>
 <td>
 <a href="offers.php?action=delete&id=<?= $off['id'] ?>" onclick="return confirm('Delete this offer?')" class="btn-action btn-action-danger" style="font-size: 0.75rem;"><?= $lang === 'ar' ? 'حذف' : 'Delete' ?></a>
 </td>
 </tr>
 <?php endforeach; ?>
 <?php endif; ?>
 </tbody>
 </table>
 </div>
 </div>

 </div>
 </main>
 </div>

</body>
</html>
