<?php
// C:\xampp\htdocs\سيارة\admin\offers.php
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../shared/functions.php';

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

// Handle Form Submission (Add/Edit Offer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $id = intval($_POST['offer_id'] ?? 0);
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
     if ($id > 0) {
         $stmt = $db->prepare("UPDATE offers SET title_ar = ?, title_en = ?, description_ar = ?, description_en = ?, discount_pct = ?, car_id = ?, valid_until = ? WHERE id = ?");
         $stmt->execute([$title_ar, $title_en, $description_ar, $description_en, $discount_pct, $car_id ?: null, $valid_until, $id]);
         header("Location: offers.php?msg=updated");
     } else {
         $stmt = $db->prepare("INSERT INTO offers (title_ar, title_en, description_ar, description_en, discount_pct, car_id, valid_until, image) VALUES (?, ?, ?, ?, ?, ?, ?, 'offer_placeholder.jpg')");
         $stmt->execute([$title_ar, $title_en, $description_ar, $description_en, $discount_pct, $car_id ?: null, $valid_until]);
         header("Location: offers.php?msg=added");
     }
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
 } elseif ($_GET['msg'] === 'updated') {
 $message = "Offer campaign successfully updated!";
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
 
 <form action="offers.php" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
 <input type="hidden" name="offer_id" id="edit_offer_id" value="">
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'عنوان العرض (عربي)' : 'Title (Arabic)' ?> *</label>
 <input type="text" name="title_ar" id="offer_title_ar" class="form-control" placeholder="خصم 10% على سيارات تويوتا" required>
 </div>
 
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'عنوان العرض (إنجليزي)' : 'Title (English)' ?> *</label>
 <input type="text" name="title_en" id="offer_title_en" class="form-control" placeholder="10% Off on Toyota Cars" required>
 </div>
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'وصف العرض (عربي)' : 'Description (Arabic)' ?></label>
 <textarea name="description_ar" id="offer_desc_ar" class="form-control" rows="2"></textarea>
 </div>
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'وصف العرض (إنجليزي)' : 'Description (English)' ?></label>
 <textarea name="description_en" id="offer_desc_en" class="form-control" rows="2"></textarea>
 </div>
 
 <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'نسبة الخصم (%)' : 'Discount Percentage (%)' ?></label>
 <input type="number" name="discount_pct" id="offer_discount" class="form-control" placeholder="15" min="0" max="100" step="0.01">
 </div>
 <div class="form-group" style="margin:0;">
 <label><?= __('car') ?> (<?= $lang === 'ar' ? 'اختياري إذا كان العرض عام' : 'Optional for general offer' ?>)</label>
 <select name="car_id" id="offer_car_id" class="form-control">
 <option value="0"><?= $lang === 'ar' ? 'عرض عام لكل السيارات' : 'General - All Cars' ?></option>
 <?php foreach ($cars as $c): ?>
 <option value="<?= $c['id'] ?>"><?= $lang === 'ar' ? $c['name_ar'] : $c['name_en'] ?></option>
 <?php endforeach; ?>
 </select>
 </div>
 </div>

 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'صلاحية العرض حتى تاريخ' : 'Campaign Deadline (Expiry Date)' ?> *</label>
 <input type="date" name="valid_until" id="offer_valid_until" class="form-control" required>
 </div>
 
 <button type="submit" id="btn_submit_offer" class="btn-submit"><?= $lang === 'ar' ? 'إطلاق الحملة الترويجية' : 'Launch Campaign' ?></button>
 <button type="button" id="btn_cancel_offer" onclick="cancelEditOffer()" class="btn-action btn-action-danger" style="display:none; text-align:center; padding: 0.8rem; border-radius: 8px; font-weight: bold;"><?= $lang === 'ar' ? 'إلغاء التعديل' : 'Cancel Edit' ?></button>
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
 <a href="javascript:void(0)" onclick="editOffer(<?= $off['id'] ?>, '<?= addslashes($off['title_ar']) ?>', '<?= addslashes($off['title_en']) ?>', '<?= addslashes($off['description_ar']) ?>', '<?= addslashes($off['description_en']) ?>', <?= $off['discount_pct'] ?>, <?= $off['car_id'] ? $off['car_id'] : 0 ?>, '<?= $off['valid_until'] ?>')" class="btn-action btn-action-primary" style="font-size: 0.75rem;"><?= $lang === 'ar' ? 'تعديل' : 'Edit' ?></a>
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
<script>
function editOffer(id, titleAr, titleEn, descAr, descEn, discount, carId, validUntil) {
    document.getElementById('edit_offer_id').value = id;
    document.getElementById('offer_title_ar').value = titleAr;
    document.getElementById('offer_title_en').value = titleEn;
    document.getElementById('offer_desc_ar').value = descAr;
    document.getElementById('offer_desc_en').value = descEn;
    document.getElementById('offer_discount').value = discount;
    document.getElementById('offer_car_id').value = carId;
    
    // validUntil comes in as 'YYYY-MM-DD HH:MM:SS', but input type=date wants 'YYYY-MM-DD'
    document.getElementById('offer_valid_until').value = validUntil.split(' ')[0];
    
    document.getElementById('btn_submit_offer').innerText = '<?= $lang === 'ar' ? 'تحديث الحملة' : 'Update Campaign' ?>';
    document.getElementById('btn_cancel_offer').style.display = 'block';
    window.scrollTo(0, 0);
}

function cancelEditOffer() {
    document.getElementById('edit_offer_id').value = '';
    document.getElementById('offer_title_ar').value = '';
    document.getElementById('offer_title_en').value = '';
    document.getElementById('offer_desc_ar').value = '';
    document.getElementById('offer_desc_en').value = '';
    document.getElementById('offer_discount').value = '';
    document.getElementById('offer_car_id').selectedIndex = 0;
    document.getElementById('offer_valid_until').value = '';
    document.getElementById('btn_submit_offer').innerText = '<?= $lang === 'ar' ? 'إطلاق الحملة الترويجية' : 'Launch Campaign' ?>';
    document.getElementById('btn_cancel_offer').style.display = 'none';
}
</script>
</body>
</html>
