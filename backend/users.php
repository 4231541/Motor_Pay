<?php
// C:\xampp\htdocs\سيارة\admin\users.php
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../shared/functions.php';

if (!isAdmin()) {
    header("Location: ../auth.php");
    exit;
}

$lang = getLanguage();
$dir = getDirection();

$message = '';
$messageType = '';

// Handle Delete Customer Action
if (isset($_GET['delete_user'])) {
    $uid = intval($_GET['delete_user']);
    try {
        $db->prepare("DELETE FROM users WHERE id = ? AND role = 'user'")->execute([$uid]);
        header("Location: users.php?msg=deleted");
        exit;
    } catch (Exception $e) {
        $message = "Error deleting customer account: " . $e->getMessage();
        $messageType = 'danger';
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = "Customer account deleted successfully.";
    $messageType = 'success';
}

// Fetch all registered customers
$stmt = $db->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC");
$customers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang === 'ar' ? 'إدارة العملاء' : 'Customers Management' ?></title>
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
                <h1 style="font-weight: 800; font-size: 2rem;"><?= $lang === 'ar' ? 'دليل إدارة العملاء' : 'Customers Directory' ?></h1>
                <p style="color: var(--text-secondary);"><?= $lang === 'ar' ? 'عرض حسابات المستخدمين المسجلين بالمنصة وإدارتها.' : 'View and manage registered client profiles.' ?></p>
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
                            <th><?= $lang === 'ar' ? 'الاسم الكامل' : 'Customer Name' ?></th>
                            <th><?= getLanguage() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' ?></th>
                            <th><?= $lang === 'ar' ? 'الهاتف' : 'Phone' ?></th>
                            <th><?= $lang === 'ar' ? 'المدينة' : 'City' ?></th>
                            <th><?= $lang === 'ar' ? 'تاريخ التسجيل' : 'Registered At' ?></th>
                            <th><?= $lang === 'ar' ? 'التحكم' : 'Action' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customers)): ?>
                            <tr><td colspan="7" class="text-center"><?= $lang === 'ar' ? 'لا يوجد عملاء مسجلين بعد.' : 'No customers registered yet.' ?></td></tr>
                        <?php else: ?>
                            <?php foreach ($customers as $cust): ?>
                                <tr>
                                    <td>#<?= $cust['id'] ?></td>
                                    <td><strong><?= e($cust['name']) ?></strong></td>
                                    <td><?= e($cust['email']) ?></td>
                                    <td><?= e($cust['phone'] ?: '-') ?></td>
                                    <td><?= e($cust['city'] ?: '-') ?></td>
                                    <td><?= $cust['created_at'] ?></td>
                                    <td>
                                        <a href="users.php?delete_user=<?= $cust['id'] ?>" onclick="return confirm('<?= $lang === 'ar' ? 'هل أنت متأكد من حذف حساب هذا العميل نهائياً؟' : 'Are you sure you want to delete this customer account permanently?' ?>')" class="btn-action btn-action-danger" style="font-size: 0.75rem;"><?= $lang === 'ar' ? 'حذف الحساب' : 'Delete Account' ?></a>
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
