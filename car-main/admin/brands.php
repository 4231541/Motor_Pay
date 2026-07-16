<?php
// C:\xampp\htdocs\سيارة\admin\brands.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
 header("Location: ../auth.php");
 exit;
}

$lang = getLanguage();
$dir = getDirection();

$action = $_GET['action'] ?? 'list';
$message = '';
$messageType = '';

// 1. Process Brand Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_brand'])) {
 $name_ar = trim($_POST['name_ar']);
 $name_en = trim($_POST['name_en']);
 $logo = 'brand_placeholder.svg';
 
 if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
     $upload = uploadImage($_FILES['logo_file'], 'assets/images/brands');
     if (isset($upload['success'])) {
         $logo = $upload['path'];
     } else {
         $message = $upload['error'];
         $messageType = 'danger';
     }
 }
 
 if ($messageType !== 'danger') {
     if ($name_ar === '' || $name_en === '') {
         $message = __('fill_required');
         $messageType = 'danger';
     } else {
         try {
             $stmt = $db->prepare("INSERT INTO brands (name_ar, name_en, logo) VALUES (?, ?, ?)");
             $stmt->execute([$name_ar, $name_en, $logo]);
             $message = "Brand successfully added!";
             $messageType = 'success';
         } catch (Exception $e) {
             $message = "Error: " . $e->getMessage();
             $messageType = 'danger';
         }
     }
 }
}

// 2. Process Model Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_model'])) {
 $brand_id = intval($_POST['brand_id']);
 $name_ar = trim($_POST['name_ar']);
 $name_en = trim($_POST['name_en']);
 
 if ($brand_id <= 0 || $name_ar === '' || $name_en === '') {
 $message = __('fill_required');
 $messageType = 'danger';
 } else {
 try {
 $stmt = $db->prepare("INSERT INTO models (brand_id, name_ar, name_en) VALUES (?, ?, ?)");
 $stmt->execute([$brand_id, $name_ar, $name_en]);
 $message = "Model successfully added!";
 $messageType = 'success';
 } catch (Exception $e) {
 $message = "Error: " . $e->getMessage();
 $messageType = 'danger';
 }
 }
}

// 3. Process Deletions
if (isset($_GET['delete_brand'])) {
 $bid = intval($_GET['delete_brand']);
 try {
 $db->prepare("DELETE FROM brands WHERE id = ?")->execute([$bid]);
 // Models will delete cascade if supported by SQLite foreign keys constraint (enabled in seed.php)
 $db->prepare("DELETE FROM models WHERE brand_id = ?")->execute([$bid]);
 header("Location: brands.php?msg=brand_deleted");
 exit;
 } catch (Exception $e) {
 $message = "Error: " . $e->getMessage();
 $messageType = 'danger';
 }
}

if (isset($_GET['delete_model'])) {
 $mid = intval($_GET['delete_model']);
 try {
 $db->prepare("DELETE FROM models WHERE id = ?")->execute([$mid]);
 header("Location: brands.php?msg=model_deleted");
 exit;
 } catch (Exception $e) {
 $message = "Error: " . $e->getMessage();
 $messageType = 'danger';
 }
}

if (isset($_GET['msg'])) {
 if ($_GET['msg'] === 'brand_deleted') {
 $message = "Brand deleted successfully.";
 $messageType = 'success';
 } elseif ($_GET['msg'] === 'model_deleted') {
 $message = "Model deleted successfully.";
 $messageType = 'success';
 }
}

// Fetch all brands and their mapped models count
$brands = $db->query("SELECT brands.*, (SELECT COUNT(*) FROM models WHERE models.brand_id = brands.id) AS models_count FROM brands ORDER BY name_en ASC")->fetchAll();

// Fetch models with brand details
$models = $db->query("SELECT models.*, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en 
 FROM models 
 JOIN brands ON models.brand_id = brands.id 
 ORDER BY brands.name_en ASC, models.name_en ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title><?= $lang === 'ar' ? 'الماركات والموديلات' : 'Brands & Models' ?></title>
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
 <h1 style="font-weight: 800; font-size: 2rem;"><?= $lang === 'ar' ? 'إدارة الماركات والموديلات' : 'Brands & Models Directory' ?></h1>
 <p style="color: var(--text-secondary);"><?= $lang === 'ar' ? 'إدارة ماركات السيارات المتاحة بالمعرض وموديلاتها التابعة.' : 'Configure car manufacturers and sub-model relationships.' ?></p>
 </header>

 <!-- Alerts Banner -->
 <?php if ($message !== ''): ?>
 <div style="background-color: <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; color: <?= $messageType === 'success' ? 'var(--success)' : 'var(--danger)' ?>; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700; border: 1px solid <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.25)' : 'rgba(239, 68, 68, 0.25)' ?>;">
 <?= $message ?>
 </div>
 <?php endif; ?>

 <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
 
 <!-- COLUMN 1: BRANDS CRUD -->
 <div>
 <!-- Form: Add Brand -->
 <div style="background-color: var(--bg-secondary); border-radius: 16px; padding: 1.5rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow); margin-bottom: 2rem;">
 <h4 style="font-weight: 800; margin-bottom: 1rem;"><?= $lang === 'ar' ? 'إضافة ماركة تجارية جديدة' : 'Add New Brand' ?></h4>
 <form action="brands.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1rem;">
 <input type="hidden" name="submit_brand" value="1">
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'الاسم بالعربية' : 'Name in Arabic' ?> *</label>
 <input type="text" name="name_ar" class="form-control" placeholder="تويوتا" required>
 </div>
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'الاسم بالإنجليزية' : 'Name in English' ?> *</label>
 <input type="text" name="name_en" class="form-control" placeholder="Toyota" required>
 </div>
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'شعار الماركة' : 'Brand Logo' ?> (اختياري)</label>
 <input type="file" name="logo_file" class="form-control" accept="image/jpeg, image/png, image/webp">
 </div>
 <button type="submit" class="btn-submit" style="margin: 0; padding: 0.6rem 1rem; font-size: 0.9rem;"><?= $lang === 'ar' ? 'إضافة الماركة' : 'Add Brand' ?></button>
 </form>
 </div>

 <!-- List Brands -->
 <h3 style="font-weight: 800; margin-bottom: 1.25rem;"><i class="bi bi-tags-fill text-primary" style="margin-right:0.5rem"></i><?= $lang === 'ar' ? 'الماركات المسجلة' : 'Registered Brands' ?></h3>
 <div class="table-container">
 <table class="admin-table">
 <thead>
 <tr>
 <th>#</th>
 <th><?= $lang === 'ar' ? 'الماركة' : 'Brand' ?></th>
 <th><?= $lang === 'ar' ? 'عدد الموديلات' : 'Models Count' ?></th>
 <th><?= $lang === 'ar' ? 'التحكم' : 'Action' ?></th>
 </tr>
 </thead>
 <tbody>
 <?php foreach ($brands as $b): ?>
 <tr>
 <td><?= $b['id'] ?></td>
 <td>
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <?php if (!empty($b['logo'])): ?>
            <img src="../<?= e($b['logo']) ?>" style="width: 32px; height: 32px; object-fit: contain;">
        <?php endif; ?>
        <strong><?= $lang === 'ar' ? $b['name_ar'] : $b['name_en'] ?></strong> (<?= $b['name_en'] ?>)
    </div>
 </td>
 <td><?= $b['models_count'] ?></td>
 <td>
 <a href="brands.php?delete_brand=<?= $b['id'] ?>" onclick="return confirm('<?= $lang === 'ar' ? 'حذف هذه الماركة سيحذف جميع موديلاتها التابعة، هل أنت متأكد؟' : 'Deleting a brand removes all associated sub-models. Proceed?' ?>')" class="btn-action btn-action-danger" style="font-size: 0.75rem;"><?= $lang === 'ar' ? 'حذف' : 'Delete' ?></a>
 </td>
 </tr>
 <?php endforeach; ?>
 </tbody>
 </table>
 </div>
 </div>

 <!-- COLUMN 2: MODELS CRUD -->
 <div>
 <!-- Form: Add Model -->
 <div style="background-color: var(--bg-secondary); border-radius: 16px; padding: 1.5rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow); margin-bottom: 2rem;">
 <h4 style="font-weight: 800; margin-bottom: 1rem;"><?= $lang === 'ar' ? 'إضافة موديل تابع لماركة' : 'Add Sub-Model' ?></h4>
 <form action="brands.php" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
 <input type="hidden" name="submit_model" value="1">
 
 <div class="form-group" style="margin:0;">
 <label><?= __('brand') ?> *</label>
 <select name="brand_id" class="form-control" required>
 <?php foreach ($brands as $b): ?>
 <option value="<?= $b['id'] ?>"><?= $lang === 'ar' ? $b['name_ar'] : $b['name_en'] ?></option>
 <?php endforeach; ?>
 </select>
 </div>
 
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'اسم الموديل بالعربية' : 'Model Name (Arabic)' ?> *</label>
 <input type="text" name="name_ar" class="form-control" placeholder="كامري" required>
 </div>
 <div class="form-group" style="margin:0;">
 <label><?= $lang === 'ar' ? 'اسم الموديل بالإنجليزية' : 'Model Name (English)' ?> *</label>
 <input type="text" name="name_en" class="form-control" placeholder="Camry" required>
 </div>
 
 <button type="submit" class="btn-submit" style="margin: 0; padding: 0.6rem 1rem; font-size: 0.9rem; background-color: var(--accent);"><?= $lang === 'ar' ? 'إضافة الموديل' : 'Add Model' ?></button>
 </form>
 </div>

 <!-- List Models -->
 <h3 style="font-weight: 800; margin-bottom: 1.25rem;"><i class="bi bi-diagram-3-fill text-primary" style="margin-right:0.5rem"></i><?= $lang === 'ar' ? 'الموديلات التابعة' : 'Sub-Models List' ?></h3>
 <div class="table-container" style="max-height: 400px; overflow-y: auto;">
 <table class="admin-table">
 <thead>
 <tr>
 <th><?= $lang === 'ar' ? 'الماركة' : 'Brand' ?></th>
 <th><?= $lang === 'ar' ? 'الموديل' : 'Model' ?></th>
 <th><?= $lang === 'ar' ? 'التحكم' : 'Action' ?></th>
 </tr>
 </thead>
 <tbody>
 <?php foreach ($models as $m): ?>
 <tr>
 <td><?= $lang === 'ar' ? $m['brand_name_ar'] : $m['brand_name_en'] ?></td>
 <td><strong><?= $lang === 'ar' ? $m['name_ar'] : $m['name_en'] ?></strong> (<?= $m['name_en'] ?>)</td>
 <td>
 <a href="brands.php?delete_model=<?= $m['id'] ?>" onclick="return confirm('<?= $lang === 'ar' ? 'هل تريد حذف هذا الموديل؟' : 'Are you sure you want to delete this model?' ?>')" class="btn-action btn-action-danger" style="font-size: 0.75rem;"><?= $lang === 'ar' ? 'حذف' : 'Delete' ?></a>
 </td>
 </tr>
 <?php endforeach; ?>
 </tbody>
 </table>
 </div>
 </div>

 </div>
 </main>
 </div>

</body>
</html>
