<?php
// C:\xampp\htdocs\سيارة\admin\cars.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (false) {
    header("Location: ../auth.php");
    exit;
}

$lang = getLanguage();
$dir = getDirection();

$action = $_GET['action'] ?? 'list'; // 'list', 'add', 'edit', 'delete'
$carId = intval($_GET['id'] ?? 0);

$message = '';
$messageType = '';

// Handle Delete Action
if ($action === 'delete' && $carId > 0) {
    try {
        $stmt = $db->prepare("DELETE FROM cars WHERE id = ?");
        $stmt->execute([$carId]);
        header("Location: cars.php?msg=deleted");
        exit;
    } catch (Exception $e) {
        $message = "Error deleting car: " . $e->getMessage();
        $messageType = 'danger';
        $action = 'list';
    }
}

// Handle Add/Edit Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_id = intval($_POST['brand_id']);
    $model_id = intval($_POST['model_id']);
    $name_ar = trim($_POST['name_ar']);
    $name_en = trim($_POST['name_en']);
    $year = intval($_POST['year']);
    $price = floatval($_POST['price']);
    $min_installment = floatval($_POST['min_installment']);
    
    $type_ar = trim($_POST['type_ar']);
    $type_en = trim($_POST['type_en']);
    $grade_ar = trim($_POST['grade_ar']);
    $grade_en = trim($_POST['grade_en']);
    $fuel_ar = trim($_POST['fuel_ar']);
    $fuel_en = trim($_POST['fuel_en']);
    $transmission_ar = trim($_POST['transmission_ar']);
    $transmission_en = trim($_POST['transmission_en']);
    $drive_ar = trim($_POST['drive_ar']);
    $drive_en = trim($_POST['drive_en']);
    
    $color_ar = trim($_POST['color_ar']);
    $color_en = trim($_POST['color_en']);
    $color_inner_ar = trim($_POST['color_inner_ar']);
    $color_inner_en = trim($_POST['color_inner_en']);
    $engine_size = trim($_POST['engine_size']);
    $seats = intval($_POST['seats']);
    $doors = intval($_POST['doors']);

    // Parse Textarea lists to JSON array
    $safety = array_filter(array_map('trim', explode("\n", $_POST['specs_safety'])));
    $comfort = array_filter(array_map('trim', explode("\n", $_POST['specs_comfort'])));
    $tech = array_filter(array_map('trim', explode("\n", $_POST['specs_tech'])));
    $exterior = array_filter(array_map('trim', explode("\n", $_POST['specs_exterior'])));
    
    $safety_json = json_encode(array_values($safety));
    $comfort_json = json_encode(array_values($comfort));
    $tech_json = json_encode(array_values($tech));
    $exterior_json = json_encode(array_values($exterior));

    // Handle image uploads
    $final_images = [];
    
    // If editing, try to keep existing images if no new ones are uploaded
    if ($action === 'edit' && $carId > 0) {
        $stmt_existing = $db->prepare("SELECT images FROM cars WHERE id = ?");
        $stmt_existing->execute([$carId]);
        $existing = $stmt_existing->fetchColumn();
        if ($existing) {
            $final_images = json_decode($existing, true) ?? [];
        }
    }

    if (isset($_FILES['car_images']) && !empty($_FILES['car_images']['name'][0])) {
        $uploaded_paths = [];
        $file_count = count($_FILES['car_images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['car_images']['error'][$i] === UPLOAD_ERR_OK) {
                // Reconstruct a single file array for the helper
                $single_file = [
                    'name' => $_FILES['car_images']['name'][$i],
                    'type' => $_FILES['car_images']['type'][$i],
                    'tmp_name' => $_FILES['car_images']['tmp_name'][$i],
                    'error' => $_FILES['car_images']['error'][$i],
                    'size' => $_FILES['car_images']['size'][$i]
                ];
                $upload = uploadImage($single_file, 'assets/images/cars');
                if (isset($upload['success'])) {
                    $uploaded_paths[] = $upload['path'];
                }
            }
        }
        if (!empty($uploaded_paths)) {
            // Overwrite existing images with new ones, or you could merge them.
            // For now, replacing them if new ones are uploaded.
            $final_images = $uploaded_paths;
        }
    }
    
    // Fallback if no images at all
    if (empty($final_images)) {
        $final_images = ['assets/images/cars/placeholder_car.jpg'];
    }

    $images_json = json_encode(array_values($final_images));

    if ($name_ar === '' || $name_en === '' || $price <= 0) {
        $message = __('fill_required');
        $messageType = 'danger';
    } else {
        try {
            if ($action === 'add') {
                $stmt = $db->prepare("INSERT INTO cars (brand_id, model_id, name_ar, name_en, year, price, min_installment, images, type_ar, type_en, grade_ar, grade_en, fuel_ar, fuel_en, transmission_ar, transmission_en, drive_ar, drive_en, color_ar, color_en, color_inner_ar, color_inner_en, engine_size, seats, doors, specs_safety, specs_comfort, specs_tech, specs_exterior) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$brand_id, $model_id, $name_ar, $name_en, $year, $price, $min_installment, $images_json, $type_ar, $type_en, $grade_ar, $grade_en, $fuel_ar, $fuel_en, $transmission_ar, $transmission_en, $drive_ar, $drive_en, $color_ar, $color_en, $color_inner_ar, $color_inner_en, $engine_size, $seats, $doors, $safety_json, $comfort_json, $tech_json, $exterior_json]);
                
                header("Location: cars.php?msg=added");
                exit;
            } 
            elseif ($action === 'edit' && $carId > 0) {
                $stmt = $db->prepare("UPDATE cars SET brand_id = ?, model_id = ?, name_ar = ?, name_en = ?, year = ?, price = ?, min_installment = ?, images = ?, type_ar = ?, type_en = ?, grade_ar = ?, grade_en = ?, fuel_ar = ?, fuel_en = ?, transmission_ar = ?, transmission_en = ?, drive_ar = ?, drive_en = ?, color_ar = ?, color_en = ?, color_inner_ar = ?, color_inner_en = ?, engine_size = ?, seats = ?, doors = ?, specs_safety = ?, specs_comfort = ?, specs_tech = ?, specs_exterior = ? WHERE id = ?");
                $stmt->execute([$brand_id, $model_id, $name_ar, $name_en, $year, $price, $min_installment, $images_json, $type_ar, $type_en, $grade_ar, $grade_en, $fuel_ar, $fuel_en, $transmission_ar, $transmission_en, $drive_ar, $drive_en, $color_ar, $color_en, $color_inner_ar, $color_inner_en, $engine_size, $seats, $doors, $safety_json, $comfort_json, $tech_json, $exterior_json, $carId]);
                
                header("Location: cars.php?msg=updated");
                exit;
            }
        } catch (Exception $e) {
            $message = "Database Error: " . $e->getMessage();
            $messageType = 'danger';
        }
    }
}

// Messages checks from redirects
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $message = "Car listing created successfully!";
        $messageType = 'success';
    } elseif ($_GET['msg'] === 'updated') {
        $message = "Car listing details updated successfully!";
        $messageType = 'success';
    } elseif ($_GET['msg'] === 'deleted') {
        $message = "Car listing deleted successfully!";
        $messageType = 'success';
    }
}

// Fetch Brands and Models
$brands = $db->query("SELECT * FROM brands ORDER BY name_en ASC")->fetchAll();
$models = $db->query("SELECT * FROM models ORDER BY name_en ASC")->fetchAll();

// Fetch all cars for listing
$stmtCars = $db->query("SELECT cars.*, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en, models.name_ar AS model_name_ar, models.name_en AS model_name_en 
                        FROM cars
                        JOIN brands ON cars.brand_id = brands.id
                        JOIN models ON cars.model_id = models.id
                        ORDER BY cars.id DESC");
$cars = $stmtCars->fetchAll();

// Fetch single car for edit
$car = null;
if ($action === 'edit' && $carId > 0) {
    $stmtEdit = $db->prepare("SELECT * FROM cars WHERE id = ?");
    $stmtEdit->execute([$carId]);
    $car = $stmtEdit->fetch();
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang === 'ar' ? 'إدارة السيارات' : 'Cars Management' ?></title>
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
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                <div>
                    <h1 style="font-weight: 800; font-size: 2rem;"><?= $lang === 'ar' ? 'إدارة السيارات' : 'Cars Management' ?></h1>
                    <p style="color: var(--text-secondary);"><?= $lang === 'ar' ? 'إضافة وتعديل وحذف تفاصيل سيارات المعرض.' : 'Add, edit, delete and structure showroom vehicles catalog.' ?></p>
                </div>
                <div>
                    <?php if ($action === 'list'): ?>
                        <a href="cars.php?action=add" class="btn-submit" style="margin: 0; padding: 0.6rem 1.5rem; font-size: 0.9rem;">+ <?= $lang === 'ar' ? 'إضافة سيارة جديدة' : 'Add New Car' ?></a>
                    <?php else: ?>
                        <a href="cars.php" class="btn-submit" style="margin: 0; padding: 0.6rem 1.5rem; font-size: 0.9rem; background-color: var(--input-bg); color: var(--text-secondary);"><?= $lang === 'ar' ? 'الرجوع للقائمة' : 'Back to List' ?></a>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Alerts Banner -->
            <?php if ($message !== ''): ?>
                <div style="background-color: <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; color: <?= $messageType === 'success' ? 'var(--success)' : 'var(--danger)' ?>; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700; border: 1px solid <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.25)' : 'rgba(239, 68, 68, 0.25)' ?>;">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- 1. LIST CARS VIEW -->
            <?php if ($action === 'list'): ?>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= __('brand') ?></th>
                                <th><?= $lang === 'ar' ? 'الاسم العربي' : 'Arabic Name' ?></th>
                                <th><?= $lang === 'ar' ? 'الاسم الإنجليزي' : 'English Name' ?></th>
                                <th><?= __('price') ?></th>
                                <th><?= $lang === 'ar' ? 'أدنى قسط' : 'Min installment' ?></th>
                                <th><?= $lang === 'ar' ? 'سنة' : 'Year' ?></th>
                                <th><?= $lang === 'ar' ? 'التحكم' : 'Actions' ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($cars)): ?>
                                <tr><td colspan="8" class="text-center">No cars in inventory yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($cars as $c): ?>
                                    <tr>
                                        <td><?= $c['id'] ?></td>
                                        <td><?= $lang === 'ar' ? $c['brand_name_ar'] : $c['brand_name_en'] ?></td>
                                        <td><strong><?= e($c['name_ar']) ?></strong></td>
                                        <td><?= e($c['name_en']) ?></td>
                                        <td><?= formatPrice($c['price']) ?></td>
                                        <td><?= formatPrice($c['min_installment']) ?></td>
                                        <td><?= $c['year'] ?></td>
                                        <td>
                                            <a href="cars.php?action=edit&id=<?= $c['id'] ?>" class="btn-action btn-action-primary" style="font-size: 0.75rem;"><?= $lang === 'ar' ? 'تعديل' : 'Edit' ?></a>
                                            <a href="cars.php?action=delete&id=<?= $c['id'] ?>" onclick="return confirm('<?= $lang === 'ar' ? 'هل أنت متأكد من حذف هذه السيارة؟' : 'Are you sure you want to delete this car?' ?>');" class="btn-action btn-action-danger" style="font-size: 0.75rem;"><?= $lang === 'ar' ? 'حذف' : 'Delete' ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <!-- 2. ADD & EDIT FORM VIEW -->
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <div style="background-color: var(--bg-secondary); border-radius: 20px; padding: 2.5rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow);">
                    <h3 style="font-weight: 800; margin-bottom: 2rem;"><?= $action === 'add' ? ($lang === 'ar' ? 'إضافة سيارة جديدة' : 'Add New Car') : ($lang === 'ar' ? 'تعديل بيانات سيارة' : 'Edit Car Details') ?></h3>
                    
                    <form action="cars.php?action=<?= $action ?>&id=<?= $carId ?>" method="POST" enctype="multipart/form-data">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <!-- Brand & Model dropdowns -->
                            <div class="form-group">
                                <label><?= __('brand') ?> *</label>
                                <select name="brand_id" id="form-brand-select" class="form-control" required>
                                    <?php foreach ($brands as $b): ?>
                                        <option value="<?= $b['id'] ?>" <?= ($car && $car['brand_id'] == $b['id']) ? 'selected' : '' ?>><?= $lang === 'ar' ? $b['name_ar'] : $b['name_en'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= __('model') ?> *</label>
                                <select name="model_id" id="form-model-select" class="form-control" required>
                                    <?php foreach ($models as $m): ?>
                                        <option value="<?= $m['id'] ?>" data-brand="<?= $m['brand_id'] ?>" <?= ($car && $car['model_id'] == $m['id']) ? 'selected' : '' ?>><?= $lang === 'ar' ? $m['name_ar'] : $m['name_en'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Name strings -->
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'الاسم بالكامل (عربي)' : 'Full Name (Arabic)' ?> *</label>
                                <input type="text" name="name_ar" class="form-control" value="<?= e($car['name_ar'] ?? '') ?>" required>
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'الاسم بالكامل (إنجليزي)' : 'Full Name (English)' ?> *</label>
                                <input type="text" name="name_en" class="form-control" value="<?= e($car['name_en'] ?? '') ?>" required>
                            </div>

                            <!-- Financial estimates -->
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'سعر الكاش (ريال)' : 'Cash Price (SAR)' ?> *</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="<?= $car['price'] ?? '' ?>" required>
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'أدنى قسط شهري يبدأ منه (ريال)' : 'Min Starting Installment (SAR)' ?> *</label>
                                <input type="number" step="0.01" name="min_installment" class="form-control" value="<?= $car['min_installment'] ?? '' ?>" required>
                            </div>

                            <!-- Vehicle Specifications -->
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'نوع الهيكل (عربي)' : 'Body Type (Arabic)' ?></label>
                                <input type="text" name="type_ar" class="form-control" placeholder="سيدان، عائلية..." value="<?= e($car['type_ar'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'نوع الهيكل (إنجليزي)' : 'Body Type (English)' ?></label>
                                <input type="text" name="type_en" class="form-control" placeholder="Sedan, SUV..." value="<?= e($car['type_en'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'الفئة (عربي)' : 'Grade/Class (Arabic)' ?></label>
                                <input type="text" name="grade_ar" class="form-control" value="<?= e($car['grade_ar'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'الفئة (إنجليزي)' : 'Grade/Class (English)' ?></label>
                                <input type="text" name="grade_en" class="form-control" value="<?= e($car['grade_en'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'نوع الوقود (عربي)' : 'Fuel (Arabic)' ?></label>
                                <input type="text" name="fuel_ar" class="form-control" value="<?= e($car['fuel_ar'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'نوع الوقود (إنجليزي)' : 'Fuel (English)' ?></label>
                                <input type="text" name="fuel_en" class="form-control" value="<?= e($car['fuel_en'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'نوع القير (عربي)' : 'Transmission (Arabic)' ?></label>
                                <input type="text" name="transmission_ar" class="form-control" value="<?= e($car['transmission_ar'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'نوع القير (إنجليزي)' : 'Transmission (English)' ?></label>
                                <input type="text" name="transmission_en" class="form-control" value="<?= e($car['transmission_en'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'نظام الدفع (عربي)' : 'Drivetrain (Arabic)' ?></label>
                                <input type="text" name="drive_ar" class="form-control" value="<?= e($car['drive_ar'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'نظام الدفع (إنجليزي)' : 'Drivetrain (English)' ?></label>
                                <input type="text" name="drive_en" class="form-control" value="<?= e($car['drive_en'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'سنة الصنع' : 'Manufacture Year' ?> *</label>
                                <input type="number" name="year" class="form-control" value="<?= $car['year'] ?? 2026 ?>" required>
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'حجم المحرك' : 'Engine Size' ?></label>
                                <input type="text" name="engine_size" class="form-control" placeholder="2.5L / 1.5L Turbo" value="<?= e($car['engine_size'] ?? '') ?>">
                            </div>

                            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div>
                                    <label><?= $lang === 'ar' ? 'المقاعد' : 'Seats' ?></label>
                                    <input type="number" name="seats" class="form-control" value="<?= $car['seats'] ?? 5 ?>">
                                </div>
                                <div>
                                    <label><?= $lang === 'ar' ? 'الأبواب' : 'Doors' ?></label>
                                    <input type="number" name="doors" class="form-control" value="<?= $car['doors'] ?? 4 ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'اللون الخارجي (عربي)' : 'Color Exterior (Arabic)' ?></label>
                                <input type="text" name="color_ar" class="form-control" value="<?= e($car['color_ar'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'اللون الخارجي (إنجليزي)' : 'Color Exterior (English)' ?></label>
                                <input type="text" name="color_en" class="form-control" value="<?= e($car['color_en'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'اللون الداخلي (عربي)' : 'Color Interior (Arabic)' ?></label>
                                <input type="text" name="color_inner_ar" class="form-control" value="<?= e($car['color_inner_ar'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'اللون الداخلي (إنجليزي)' : 'Color Interior (English)' ?></label>
                                <input type="text" name="color_inner_en" class="form-control" value="<?= e($car['color_inner_en'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <!-- Images Upload -->
                        <div class="form-group" style="margin-top: 1.5rem;">
                            <label><?= $lang === 'ar' ? 'صور السيارة (يمكنك رفع صورة أو أكثر كمعرض صور للسيارة)' : 'Car Images (Upload one or more images for gallery)' ?></label>
                            <input type="file" name="car_images[]" class="form-control" accept="image/jpeg, image/png, image/webp" multiple>
                            <?php if ($action === 'edit' && !empty($car['images'])): ?>
                                <?php $existingImages = json_decode($car['images'], true) ?? []; ?>
                                <?php if (count($existingImages) > 0): ?>
                                    <div style="display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap;">
                                        <?php foreach ($existingImages as $img): ?>
                                            <div style="width: 80px; height: 60px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color);">
                                                <img src="../<?= e($img) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <small style="color: var(--text-muted); display: block; margin-top: 5px;">
                                        <?= $lang === 'ar' ? 'رفع صور جديدة سيستبدل الصور الحالية' : 'Uploading new images will replace existing ones' ?>
                                    </small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Checklists specifications (JSON formatting) -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 2rem;">
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'مواصفات الأمان والسلامة (سطر لكل ميزة)' : 'Safety Features (One per line)' ?></label>
                                <textarea name="specs_safety" class="form-control" rows="5"><?= isset($car['specs_safety']) ? implode("\n", json_decode($car['specs_safety'], true)) : "ABS\nESP\nوسائد هوائية خلفية وأمامية\nكاميرا خلفية\nحساسات أمامية وخلفية\nمثبت سرعة" ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'مواصفات الراحة (سطر لكل ميزة)' : 'Comfort Features (One per line)' ?></label>
                                <textarea name="specs_comfort" class="form-control" rows="5"><?= isset($car['specs_comfort']) ? implode("\n", json_decode($car['specs_comfort'], true)) : "دخول ذكي\nتشغيل بصمة\nمقاعد جلد\nمكيف أوتوماتيك" ?></textarea>
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'مواصفات التقنية (سطر لكل ميزة)' : 'Technology Features (One per line)' ?></label>
                                <textarea name="specs_tech" class="form-control" rows="5"><?= isset($car['specs_tech']) ? implode("\n", json_decode($car['specs_tech'], true)) : "شاشة لمس\nApple CarPlay\nAndroid Auto\nبلوتوث\nشاحن لاسلكي" ?></textarea>
                            </div>

                            <div class="form-group">
                                <label><?= $lang === 'ar' ? 'التجهيزات الخارجية (سطر لكل ميزة)' : 'Exterior Features (One per line)' ?></label>
                                <textarea name="specs_exterior" class="form-control" rows="5"><?= isset($car['specs_exterior']) ? implode("\n", json_decode($car['specs_exterior'], true)) : "جنوط ألمنيوم\nمصابيح LED\nإضاءة نهارية\nمرايا كهربائية" ?></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit" style="margin-top: 2rem;"><?= $lang === 'ar' ? 'حفظ البيانات' : 'Save Details' ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Script to dynamically filter models by selected brand in the form view -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const brandSelect = document.getElementById('form-brand-select');
            const modelSelect = document.getElementById('form-model-select');

            if (brandSelect && modelSelect) {
                const filterModels = () => {
                    const selectedBrand = brandSelect.value;
                    const options = modelSelect.querySelectorAll('option');
                    
                    let firstVisible = null;
                    options.forEach(opt => {
                        if (opt.dataset.brand === selectedBrand) {
                            opt.style.display = 'block';
                            if (!firstVisible) firstVisible = opt;
                        } else {
                            opt.style.display = 'none';
                        }
                    });

                    // Set default selected model to first matched
                    if (firstVisible && !options[modelSelect.selectedIndex]?.style.display === 'block') {
                        firstVisible.selected = true;
                    }
                };

                brandSelect.addEventListener('change', filterModels);
                // Run on load
                filterModels();
            }
        });
    </script>

</body>
</html>
