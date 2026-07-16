<?php
// C:\xampp\htdocs\سيارة\admin\requests.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
 header("Location: ../auth.php");
 exit;
}

$lang = getLanguage();
$dir = getDirection();

$message = '';
$messageType = '';

// Handle Status Updates & Notification Triggers
if (isset($_GET['update_status']) && isset($_GET['id'])) {
 $reqId = intval($_GET['id']);
 $newStatus = trim($_GET['update_status']);
 
 $validStatuses = ['received', 'reviewing', 'contacting', 'booked', 'delivered', 'rejected'];
 
 if (in_array($newStatus, $validStatuses)) {
 try {
 // Get user_id and car details to compose a relevant notification
 $stmtReq = $db->prepare("SELECT requests.*, cars.name_ar AS car_name_ar, cars.name_en AS car_name_en 
 FROM requests 
 JOIN cars ON requests.car_id = cars.id 
 WHERE requests.id = ?");
 $stmtReq->execute([$reqId]);
 $reqDetails = $stmtReq->fetch();
 
 if ($reqDetails) {
 // Update requests
 $stmtUpdate = $db->prepare("UPDATE requests SET status = ? WHERE id = ?");
 $stmtUpdate->execute([$newStatus, $reqId]);
 
 // If user is registered (has user_id), trigger a personalized system notification!
 if ($reqDetails['user_id'] !== null) {
 $carNameAr = $reqDetails['car_name_ar'];
 $carNameEn = $reqDetails['car_name_en'];
 
 $titleAr = "تحديث حالة طلب التمويل / الحجز";
 $titleEn = "Update on Your Reserving/Financing Request";
 
 $statusNamesAr = [
 'received' => 'تم استلام الطلب',
 'reviewing' => 'مراجعة البيانات والوثائق',
 'contacting' => 'جاري التواصل معك هاتفياً',
 'booked' => 'تم تأكيد الحجز بنجاح',
 'delivered' => 'تم تسليم السيارة بنجاح',
 'rejected' => 'مرفوض'
 ];
 
 $statusNamesEn = [
 'received' => 'Request Received',
 'reviewing' => 'Documents Reviewing',
 'contacting' => 'In Contact',
 'booked' => 'Car Reservation Confirmed',
 'delivered' => 'Car Delivered Successfully',
 'rejected' => 'Request Declined'
 ];
 
 $msgAr = "طلبك رقم #{$reqId} للسيارة ({$carNameAr}) تم تحديث حالته إلى: " . $statusNamesAr[$newStatus];
 $msgEn = "Your order #{$reqId} for {$carNameEn} has been updated to: " . $statusNamesEn[$newStatus];
 
 $stmtNotif = $db->prepare("INSERT INTO notifications (user_id, title_ar, title_en, message_ar, message_en) VALUES (?, ?, ?, ?, ?)");
 $stmtNotif->execute([$reqDetails['user_id'], $titleAr, $titleEn, $msgAr, $msgEn]);
 }
 
 $message = "Request #{$reqId} status successfully updated to " . strtoupper($newStatus);
 $messageType = 'success';
 }
 } catch (Exception $e) {
 $message = "Error: " . $e->getMessage();
 $messageType = 'danger';
 }
 }
}

// Fetch all requests joining cars
$sql = "SELECT requests.*, cars.name_ar AS car_name_ar, cars.name_en AS car_name_en, cars.price AS car_price, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en
 FROM requests
 JOIN cars ON requests.car_id = cars.id
 JOIN brands ON cars.brand_id = brands.id
 ORDER BY requests.id DESC";

$stmt = $db->query($sql);
$requests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?= $lang === 'ar' ? 'إدارة طلبات العملاء' : 'Requests Manager' ?></title>
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
 <h1 style="font-weight: 800; font-size: 2rem;"><?= $lang === 'ar' ? 'إدارة طلبات الحجز والتمويل' : 'Requests & Financing Manager' ?></h1>
 <p style="color: var(--text-secondary);"><?= $lang === 'ar' ? 'عرض طلبات العملاء الكاش والتقسيط وتحديث حالات سير العمل.' : 'Track cash bookings and financing requests.' ?></p>
 </header>

 <!-- Alerts Banner -->
 <?php if ($message !== ''): ?>
 <div style="background-color: <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; color: <?= $messageType === 'success' ? 'var(--success)' : 'var(--danger)' ?>; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700; border: 1px solid <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.25)' : 'rgba(239, 68, 68, 0.25)' ?>;">
 <?= $message ?>
 </div>
 <?php endif; ?>

 <div class="table-container">
 <table class="admin-table">
 <thead>
 <tr>
 <th>ID</th>
 <th><?= $lang === 'ar' ? 'العميل والتواصل' : 'Client & Contacts' ?></th>
 <th><?= $lang === 'ar' ? 'السيارة المحددة' : 'Vehicle Selected' ?></th>
 <th><?= $lang === 'ar' ? 'النوع وتفاصيل الدفع' : 'Financing Specs' ?></th>
 <th><?= $lang === 'ar' ? 'حالة الطلب' : 'Status' ?></th>
 <th><?= $lang === 'ar' ? 'تحديث الحالة' : 'Change Status' ?></th>
 </tr>
 </thead>
 <tbody>
 <?php if (empty($requests)): ?>
 <tr><td colspan="6" class="text-center"><?= $lang === 'ar' ? 'لا توجد طلبات مسجلة حالياً.' : 'No requests recorded yet.' ?></td></tr>
 <?php else: ?>
 <?php foreach ($requests as $req): 
 $isInstallment = $req['type'] === 'installment';
 ?>
 <tr>
 <td>#<?= $req['id'] ?><br><small style="color: var(--text-muted);"><?= $req['created_at'] ?></small></td>
 <td>
 <strong><?= e($req['name']) ?></strong>
 <small style="display: block; color: var(--text-muted);"><?= e($req['phone']) ?></small>
 <small style="display: block; color: var(--text-muted);"><?= e($req['email']) ?> | <?= e($req['city']) ?></small>
 </td>
 <td>
 <strong>[<?= $lang === 'ar' ? $req['brand_name_ar'] : $req['brand_name_en'] ?>]</strong><br>
 <?= $lang === 'ar' ? $req['car_name_ar'] : $req['car_name_en'] ?>
 </td>
 <td>
 <span class="badge-status" style="background-color: var(--input-bg);"><?= $isInstallment ? __('installment_offers') : __('book_now') ?></span>
 <?php if ($isInstallment): ?>
 <div style="font-size: 0.8rem; margin-top: 0.5rem; color: var(--text-secondary); line-height: 1.4;">
 • <?= $lang === 'ar' ? 'الهوية' : 'ID' ?>: <?= e($req['national_id']) ?><br>
 • <?= $lang === 'ar' ? 'الراتب' : 'Salary' ?>: <?= formatPrice($req['salary']) ?><br>
 • <?= $lang === 'ar' ? 'الجهة' : 'Employer' ?>: <?= e($req['employer']) ?> (<?= $req['work_duration'] ?> <?= $lang === 'ar' ? 'سنوات' : 'years' ?>)<br>
 • <?= $lang === 'ar' ? 'المقدم' : 'Down' ?>: <?= formatPrice($req['downpayment']) ?> | <?= $req['term_months'] ?> <?= __('month') ?><br>
 • <strong><?= $lang === 'ar' ? 'القسط' : 'Installment' ?>: <?= formatPrice($req['monthly_installment']) ?>/<?= __('month') ?></strong>
 </div>
 <?php else: ?>
 <div style="font-size: 0.8rem; margin-top: 0.5rem; color: var(--text-secondary);">
 • <?= $lang === 'ar' ? 'طريقة الدفع' : 'Payment' ?>: <?= $req['payment_method'] ?>
 </div>
 <?php endif; ?>
 
 <?php if ($req['notes'] !== ''): ?>
 <div style="font-size: 0.75rem; color: var(--accent-hover); margin-top: 0.5rem; max-width: 250px; white-space: normal;">
 <?= e($req['notes']) ?>
 </div>
 <?php endif; ?>
 </td>
 <td>
 <span class="badge-status badge-<?= $req['status'] ?>"><?= __('status_' . $req['status']) ?></span>
 </td>
 <td>
 <div style="display: flex; flex-direction: column; gap: 0.25rem;">
 <div style="display: flex; gap: 0.25rem;">
 <a href="requests.php?update_status=reviewing&id=<?= $req['id'] ?>" class="btn-action btn-action-secondary" style="font-size: 0.7rem; padding: 2px 6px;" title="Review Documents">️ <?= $lang === 'ar' ? 'مراجعة' : 'Review' ?></a>
 <a href="requests.php?update_status=contacting&id=<?= $req['id'] ?>" class="btn-action btn-action-secondary" style="font-size: 0.7rem; padding: 2px 6px;" title="In Contact"> <?= $lang === 'ar' ? 'تواصل' : 'Contact' ?></a>
 </div>
 <div style="display: flex; gap: 0.25rem;">
 <a href="requests.php?update_status=booked&id=<?= $req['id'] ?>" class="btn-action btn-action-primary" style="font-size: 0.7rem; padding: 2px 6px; background-color: var(--primary);" title="Confirm Reservation"> <?= $lang === 'ar' ? 'تأكيد الحجز' : 'Reserve' ?></a>
 <a href="requests.php?update_status=delivered&id=<?= $req['id'] ?>" class="btn-action btn-action-primary" style="font-size: 0.7rem; padding: 2px 6px; background-color: var(--success);" title="Deliver Vehicle"> <?= $lang === 'ar' ? 'تسليم' : 'Deliver' ?></a>
 </div>
 <a href="requests.php?update_status=rejected&id=<?= $req['id'] ?>" onclick="return confirm('Reject Request #<?= $req['id'] ?>?')" class="btn-action btn-action-danger" style="font-size: 0.7rem; padding: 2px 6px; text-align: center; width: 100%;" title="Decline Request"> <?= $lang === 'ar' ? 'رفض الطلب' : 'Decline' ?></a>
 </div>
 </td>
 </tr>
 <?php endforeach; ?>
 <?php endif; ?>
 </tbody>
 </table>
 </div>
 </main>
 </div>

</body>
</html>
