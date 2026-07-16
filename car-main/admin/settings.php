<?php
// C:\xampp\htdocs\سيارة\admin\settings.php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_logo'])) {
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['logo'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
            // Overwrite existing logo.jpg (or logo.png)
            // Determine extension or just save as logo.jpg
            $targetPath = __DIR__ . '/../assets/images/logo.jpg';
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $message = "System Logo updated successfully.";
                $messageType = "success";
            } else {
                $message = "Failed to upload logo.";
                $messageType = "danger";
            }
        } else {
            $message = "Invalid image format for Logo.";
            $messageType = "danger";
        }
    } else {
        $message = "No valid file uploaded.";
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang === 'ar' ? 'إعدادات النظام' : 'System Settings' ?></title>
    <link rel="stylesheet" href="../assets/css/style.css?v=5">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="lang-<?= $lang ?>">
    <div class="admin-layout">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-title">
                    <h1><?= $lang === 'ar' ? 'إعدادات النظام' : 'System Settings' ?></h1>
                    <p><?= $lang === 'ar' ? 'التحكم بالصور الأساسية للموقع' : 'Manage core system images' ?></p>
                </div>
            </header>

            <div class="admin-content">
                <?php if ($message): ?>
                    <div style="padding: 1rem; margin-bottom: 1rem; border-radius: 8px; background-color: <?= $messageType === 'success' ? 'rgba(34,197,94,0.1)' : 'rgba(220,38,38,0.1)' ?>; color: <?= $messageType === 'success' ? 'var(--success)' : 'var(--danger)' ?>; border: 1px solid currentColor;">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <!-- Logo Update Form -->
                <div class="admin-card">
                    <h2 style="margin-bottom: 1rem; color: var(--gold);"><?= $lang === 'ar' ? 'شعار الموقع (Logo)' : 'System Logo' ?></h2>
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <div class="form-group" style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem;">
                            <div style="width: 100px; height: 100px; background: var(--black); padding: 10px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <img src="../assets/images/logo.jpg?t=<?= time() ?>" alt="Current Logo" style="max-width: 100%; max-height: 100%;">
                            </div>
                            <div style="flex: 1;">
                                <label for="logo"><?= $lang === 'ar' ? 'اختر صورة جديدة' : 'Choose New Image' ?></label>
                                <input type="file" id="logo" name="logo" class="form-control" accept="image/jpeg, image/png, image/webp" required>
                                <small style="color: var(--text-muted); display: block; margin-top: 5px;">
                                    <?= $lang === 'ar' ? 'ينصح بصورة عرضية (Landscape). سيتم تطبيقها في كامل الموقع.' : 'Recommended landscape image. Applies everywhere.' ?>
                                </small>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="update_logo" class="btn-submit" style="background: var(--gold-gradient); color: var(--black); border: none; padding: 0.75rem 2rem; font-weight: bold; cursor: pointer;">
                                <?= $lang === 'ar' ? 'تحديث الشعار' : 'Update Logo' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
