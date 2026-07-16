<?php
// C:\xampp\htdocs\سيارة\admin\notifications.php
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

// Process Notification Broadcast/Direct Send
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $targetUser = intval($_POST['user_id']); // 0 means broadcast to all
 $title_ar = trim($_POST['title_ar']);
 $title_en = trim($_POST['title_en']);
 $message_ar = trim($_POST['message_ar']);
 $message_en = trim($_POST['message_en']);
 
 if ($title_ar === '' || $title_en === '' || $message_ar === '' || $message_en === '') {
 $message = __('fill_required');
 $messageType = 'danger';
 } else {
 try {
 $stmt = $db->prepare("INSERT INTO notifications (user_id, title_ar, title_en, message_ar, message_en) VALUES (?, ?, ?, ?, ?)");
 $stmt->execute([$targetUser ?: null, $title_ar, $title_en, $message_ar, $message_en]);
 
 $message = $targetUser === 0 
 ? "Broadcast notification sent to all registered users!" 
 : "Targeted notification sent successfully to user ID #{$targetUser}!";
 $messageType = 'success';
 } catch (Exception $e) {
 $message = "Database Error: " . $e->getMessage();
 $messageType = 'danger';
 }
 }
}

// Fetch users list for dropdown
$users = $db->query("SELECT id, name, email FROM users WHERE role = 'user' ORDER BY name ASC")->fetchAll();

// Fetch recently sent notifications
$notifications = $db->query("SELECT notifications.*, users.name AS user_name, users.email AS user_email 
 FROM notifications 
 LEFT JOIN users ON notifications.user_id = users.id 
 ORDER BY notifications.id DESC 
 LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?= $lang === 'ar' ? 'إرسال الإشعارات' : 'Send Notifications' ?></title>
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
 <h1 style="font-weight: 800; font-size: 2rem;"><?= $lang === 'ar' ? 'مركز إرسال الإشعارات' : 'Notifications Dispatcher' ?></h1>
 <p style="color: var(--text-secondary);"><?= $lang === 'ar' ? 'إرسال إشعارات جماعية لكافة المستخدمين أو إشعار خاص بعميل محدد.' : 'Send push/in-app system notifications to specific users or broadcast to all.' ?></p>
 </header>

 <!-- Alerts Banner -->
 <?php if ($message !== ''): ?>
 <div style="background-color: <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; color: <?= $messageType === 'success' ? 'var(--success)' : 'var(--danger)' ?>; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700; border: 1px solid <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.25)' : 'rgba(239, 68, 68, 0.25)' ?>;">
 <?= $message ?>
 </div>
 <?php endif; ?>

 <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
 
 <!-- Col 1: Form to send notification -->
 <div style="background-color: var(--bg-secondary); border-radius: 16px; padding: 2rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow); height: fit-content;">
 <h3 style="font-weight: 800; margin-bottom: 1.5rem;"><?= $lang === 'ar' ? 'تأليف إشعار جديد' : 'Compose Notification' ?></h3>
 
 <form action="notifications.php" method="POST">
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'المستلم المستهدف' : 'Recipient' ?> *</label>
 <select name="user_id" class="form-control" required>
 <option value="0"><?= $lang === 'ar' ? ' إرسال للجميع (عام)' : ' Broadcast to All (General)' ?></option>
 <?php foreach ($users as $u): ?>
 <option value="<?= $u['id'] ?>"> <?= e($u['name']) ?> (<?= e($u['email']) ?>)</option>
 <?php endforeach; ?>
 </select>
 </div>
 
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'عنوان الإشعار (عربي)' : 'Notification Title (Arabic)' ?> *</label>
 <input type="text" name="title_ar" class="form-control" required>
 </div>
 
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'عنوان الإشعار (إنجليزي)' : 'Notification Title (English)' ?> *</label>
 <input type="text" name="title_en" class="form-control" required>
 </div>

 <div class="form-group">
 <label><?= $lang === 'ar' ? 'نص الرسالة (عربي)' : 'Message (Arabic)' ?> *</label>
 <textarea name="message_ar" class="form-control" rows="3" required></textarea>
 </div>

 <div class="form-group">
 <label><?= $lang === 'ar' ? 'نص الرسالة (إنجليزي)' : 'Message (English)' ?> *</label>
 <textarea name="message_en" class="form-control" rows="3" required></textarea>
 </div>

 <button type="submit" class="btn-submit"><?= $lang === 'ar' ? 'إرسال الإشعار' : 'Dispatch Notification' ?></button>
 </form>
 </div>

 <!-- Col 2: Recent Notifications sent list -->
 <div>
 <h3 style="font-weight: 800; margin-bottom: 1.25rem;"><i class="bi bi-bell-fill text-warning" style="margin-right:0.5rem"></i><?= $lang === 'ar' ? 'آخر الإشعارات المرسلة' : 'Recent Dispatched Alerts' ?></h3>
 <div style="display: flex; flex-direction: column; gap: 1rem;">
 <?php if (empty($notifications)): ?>
 <p class="text-center" style="color: var(--text-secondary);"><?= $lang === 'ar' ? 'لا توجد سجلات إشعارات.' : 'No alerts logs recorded yet.' ?></p>
 <?php else: ?>
 <?php foreach ($notifications as $not): ?>
 <div style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; box-shadow: var(--card-shadow); border-left: 4px solid <?= $not['user_id'] ? 'var(--primary)' : 'var(--accent)' ?>;">
 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
 <span style="font-size: 0.75rem; font-weight: bold; background-color: var(--input-bg); padding: 2px 6px; border-radius: 4px;">
 <?= $not['user_id'] ? (" " . e($not['user_name'])) : " Broadcast" ?>
 </span>
 <span style="font-size: 0.7rem; color: var(--text-muted);"><?= $not['created_at'] ?></span>
 </div>
 <h5 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.25rem;"><?= $lang === 'ar' ? $not['title_ar'] : $not['title_en'] ?></h5>
 <p style="font-size: 0.8rem; color: var(--text-secondary);"><?= $lang === 'ar' ? $not['message_ar'] : $not['message_en'] ?></p>
 </div>
 <?php endforeach; ?>
 <?php endif; ?>
 </div>
 </div>

 </div>
 </main>
 </div>

</body>
</html>
