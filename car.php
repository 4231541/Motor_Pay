<?php
// C:\xampp\htdocs\سيارة\car.php
require_once __DIR__ . '/includes/header.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
 header("Location: index.php");
 exit;
}

// Increment views
$db->prepare("UPDATE cars SET views = views + 1 WHERE id = ?")->execute([$id]);

// Fetch Car Details
$stmt = $db->prepare("SELECT cars.*, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en, models.name_ar AS model_name_ar, models.name_en AS model_name_en
 FROM cars
 JOIN brands ON cars.brand_id = brands.id
 JOIN models ON cars.model_id = models.id
 WHERE cars.id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
 echo "<div class='container section-padding text-center'><h3>Car not found.</h3><a href='index.php' class='btn-next'>Back Home</a></div>";
 require_once __DIR__ . '/includes/footer.php';
 exit;
}

// Handle Form Submissions (Booking / Financing / Callback / Test Drive)
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $formType = $_POST['form_type'] ?? '';
 
 // Core parameters
 $name = $_POST['name'] ?? '';
 $phone = $_POST['phone'] ?? '';
 $email = $_POST['email'] ?? '';
 $city = $_POST['city'] ?? '';
 $notes = $_POST['notes'] ?? '';
 
 if ($name === '' || $phone === '' || $email === '' || $city === '') {
 $message = __('fill_required');
 $messageType = 'danger';
 } else {
 try {
 if ($formType === 'booking') {
 $payment = $_POST['payment_method'] ?? 'cash';
 $stmtIns = $db->prepare("INSERT INTO requests (user_id, car_id, type, name, phone, email, city, payment_method, notes) VALUES (?, ?, 'booking', ?, ?, ?, ?, ?, ?)");
 $stmtIns->execute([$_SESSION['user_id'] ?? null, $id, $name, $phone, $email, $city, $payment, $notes]);
 
 // Increment order count on car
 $db->prepare("UPDATE cars SET orders_count = orders_count + 1 WHERE id = ?")->execute([$id]);
 
 $message = __('request_success');
 $messageType = 'success';
 } 
 elseif ($formType === 'installment') {
 $national_id = $_POST['national_id'] ?? '';
 $salary = floatval($_POST['salary'] ?? 0);
 $employer = $_POST['employer'] ?? '';
 $work_duration = intval($_POST['work_duration'] ?? 0);
 $downpayment = floatval($_POST['downpayment'] ?? 0);
 $term = intval($_POST['term'] ?? 60);
 $monthly = floatval($_POST['monthly'] ?? 0);
 
 $stmtIns = $db->prepare("INSERT INTO requests (user_id, car_id, type, name, phone, email, city, notes, national_id, salary, employer, work_duration, downpayment, term_months, monthly_installment, payment_method) VALUES (?, ?, 'installment', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'installment')");
 $stmtIns->execute([$_SESSION['user_id'] ?? null, $id, $name, $phone, $email, $city, $notes, $national_id, $salary, $employer, $work_duration, $downpayment, $term, $monthly]);
 
 // Increment order count
 $db->prepare("UPDATE cars SET orders_count = orders_count + 1 WHERE id = ?")->execute([$id]);
 
 $message = __('request_success');
 $messageType = 'success';
 }
 elseif ($formType === 'test_drive') {
 $driveNotes = "Test Drive Request. Preferred time: " . ($_POST['preferred_time'] ?? 'Anytime') . ". " . $notes;
 $stmtIns = $db->prepare("INSERT INTO requests (user_id, car_id, type, name, phone, email, city, payment_method, notes) VALUES (?, ?, 'booking', ?, ?, ?, ?, 'Test Drive', ?)");
 $stmtIns->execute([$_SESSION['user_id'] ?? null, $id, $name, $phone, $email, $city, $driveNotes]);
 
 $message = __('request_success');
 $messageType = 'success';
 }
 elseif ($formType === 'callback') {
 $callNotes = "Callback Request. Topic: " . ($_POST['topic'] ?? 'General') . ". " . $notes;
 $stmtIns = $db->prepare("INSERT INTO requests (user_id, car_id, type, name, phone, email, city, payment_method, notes) VALUES (?, ?, 'booking', ?, ?, ?, ?, 'Callback', ?)");
 $stmtIns->execute([$_SESSION['user_id'] ?? null, $id, $name, $phone, $email, $city, $callNotes]);
 
 $message = __('request_success');
 $messageType = 'success';
 }
 } catch (Exception $e) {
 $message = "Error submitting request: " . $e->getMessage();
 $messageType = 'danger';
 }
 }
}

// Fetch similar cars
$stmtSim = $db->prepare("SELECT * FROM cars WHERE (brand_id = ? OR type_en = ?) AND id != ? AND is_available = 1 LIMIT 3");
$stmtSim->execute([$car['brand_id'], $car['type_en'], $id]);
$similarCars = $stmtSim->fetchAll();

// Decodes specifications lists
$images = json_decode($car['images'], true) ?? [];
$safety = json_decode($car['specs_safety'], true) ?? [];
$comfort = json_decode($car['specs_comfort'], true) ?? [];
$tech = json_decode($car['specs_tech'], true) ?? [];
$exterior = json_decode($car['specs_exterior'], true) ?? [];

$lang = getLanguage();
?>

<div class="container section-padding">
 
 <!-- Success / Error Notifications banner -->
 <?php if ($message !== ''): ?>
 <div style="background-color: <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; color: <?= $messageType === 'success' ? 'var(--success)' : 'var(--danger)' ?>; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700; text-align: center; border: 1px solid <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)' ?>;">
 <?= $message ?>
 </div>
 <?php endif; ?>

 <div class="details-grid">
 
 <!-- Left: Image Gallery and Specifications tabs -->
 <div>
 <!-- Gallery component -->
 <div class="gallery-container">
 <?php 
    $firstImg = !empty($images) ? 'assets/images/cars/' . ltrim($images[0], '/') : 'assets/images/placeholder_car.jpg';
 ?>
 <div class="gallery-main" id="gallery-main-view" style="background-image: url('<?= e($firstImg) ?>'); background-size: cover; background-position: center; min-height: 400px; border-radius: 16px;">
 </div>
 <?php if (!empty($images) && count($images) > 1): ?>
 <div class="gallery-thumbs">
 <?php foreach ($images as $idx => $imgName): 
       $fullImgName = 'assets/images/cars/' . ltrim($imgName, '/');
 ?>
 <div class="gallery-thumb <?= $idx === 0 ? 'active' : '' ?>" data-src="<?= e($fullImgName) ?>" style="background-image: url('<?= e($fullImgName) ?>'); background-size: cover; background-position: center; border-radius: 8px; cursor: pointer; height: 80px;">
 </div>
 <?php endforeach; ?>
 </div>
 <?php endif; ?>
 </div>

 <!-- Accordion Specifications Tabs -->
 <div class="specs-section">
 <div class="specs-tab-header">
 <button class="specs-tab-btn active" onclick="switchSpecsTab(event, 'tab-basic')"><?= __('specs') ?></button>
 <button class="specs-tab-btn" onclick="switchSpecsTab(event, 'tab-technical')"><?= __('technical_specs') ?></button>
 <button class="specs-tab-btn" onclick="switchSpecsTab(event, 'tab-safety')"><?= __('safety_specs') ?></button>
 <button class="specs-tab-btn" onclick="switchSpecsTab(event, 'tab-comfort')"><?= __('comfort_specs') ?></button>
 <button class="specs-tab-btn" onclick="switchSpecsTab(event, 'tab-tech')"><?= __('tech_specs') ?></button>
 <button class="specs-tab-btn" onclick="switchSpecsTab(event, 'tab-exterior')"><?= __('exterior_specs') ?></button>
 </div>

 <!-- Basic specs -->
 <div id="tab-basic" class="specs-tab-content">
 <div class="specs-table">
 <div class="specs-item"><span class="specs-key"><?= __('brand') ?></span><span class="specs-value"><?= $lang === 'ar' ? $car['brand_name_ar'] : $car['brand_name_en'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('model') ?></span><span class="specs-value"><?= $lang === 'ar' ? $car['model_name_ar'] : $car['model_name_en'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('year') ?></span><span class="specs-value"><?= $car['year'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('grade') ?></span><span class="specs-value"><?= $lang === 'ar' ? $car['grade_ar'] : $car['grade_en'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('color_ext') ?></span><span class="specs-value"><?= $lang === 'ar' ? $car['color_ar'] : $car['color_en'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('color_int') ?></span><span class="specs-value"><?= $lang === 'ar' ? $car['color_inner_ar'] : $car['color_inner_en'] ?></span></div>
 </div>
 </div>

 <!-- Technical specs -->
 <div id="tab-technical" class="specs-tab-content" style="display: none;">
 <div class="specs-table">
 <div class="specs-item"><span class="specs-key"><?= __('fuel') ?></span><span class="specs-value"><?= $lang === 'ar' ? $car['fuel_ar'] : $car['fuel_en'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('transmission') ?></span><span class="specs-value"><?= $lang === 'ar' ? $car['transmission_ar'] : $car['transmission_en'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('drive') ?></span><span class="specs-value"><?= $lang === 'ar' ? $car['drive_ar'] : $car['drive_en'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('engine') ?></span><span class="specs-value"><?= $car['engine_size'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('seats') ?></span><span class="specs-value"><?= $car['seats'] ?></span></div>
 <div class="specs-item"><span class="specs-key"><?= __('doors') ?></span><span class="specs-value"><?= $car['doors'] ?></span></div>
 </div>
 </div>

 <!-- Safety list -->
 <div id="tab-safety" class="specs-tab-content" style="display: none;">
 <div class="specs-list-grid">
 <?php foreach ($safety as $item): ?>
 <div class="specs-list-item"><?= $item ?></div>
 <?php endforeach; ?>
 </div>
 </div>

 <!-- Comfort list -->
 <div id="tab-comfort" class="specs-tab-content" style="display: none;">
 <div class="specs-list-grid">
 <?php foreach ($comfort as $item): ?>
 <div class="specs-list-item"><?= $item ?></div>
 <?php endforeach; ?>
 </div>
 </div>

 <!-- Tech list -->
 <div id="tab-tech" class="specs-tab-content" style="display: none;">
 <div class="specs-list-grid">
 <?php foreach ($tech as $item): ?>
 <div class="specs-list-item"><?= $item ?></div>
 <?php endforeach; ?>
 </div>
 </div>

 <!-- Exterior list -->
 <div id="tab-exterior" class="specs-tab-content" style="display: none;">
 <div class="specs-list-grid">
 <?php foreach ($exterior as $item): ?>
 <div class="specs-list-item"><?= $item ?></div>
 <?php endforeach; ?>
 </div>
 </div>
 </div>
 </div>

 <!-- Right: Purchase Options & Installment calculator -->
 <div>
 <div class="car-details-card">
 <span class="badge-status badge-booked" style="margin-bottom: 0.5rem; font-size: 0.8rem;"><?= __('new_car_status') ?></span>
 <h1 style="font-size: 1.8rem; font-weight: 800; line-height: 1.3; margin-bottom: 0.5rem;"><?= $lang === 'ar' ? $car['name_ar'] : $car['name_en'] ?></h1>
 
 <div style="display: flex; justify-content: space-between; align-items: center; margin: 1.5rem 0; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
 <div>
 <span style="font-size: 0.85rem; color: var(--text-secondary); display: block;"><?= __('price') ?></span>
 <strong style="font-size: 1.6rem; color: var(--text-primary);"><?= formatPrice($car['price']) ?></strong>
 </div>
 <div style="text-align: end;">
 <span style="font-size: 0.85rem; color: var(--text-secondary); display: block;"><?= __('installment_starts') ?></span>
 <strong style="font-size: 1.6rem; color: var(--success);"><?= formatPrice($car['min_installment']) ?> <span style="font-size: 0.9rem; font-weight: 500;">/ <?= __('month') ?></span></strong>
 </div>
 </div>

 <div class="form-group" style="display: flex; gap: 1rem;">
 <button onclick="openModal('booking-modal')" class="btn-submit" style="margin: 0; flex: 1;"><?= __('book_now') ?></button>
 <button onclick="openModal('installment-modal')" class="btn-submit" style="margin: 0; flex: 1; background: var(--success); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);"><?= __('calc_btn') ?></button>
 </div>
 
 </div>

 <!-- Installment Calculator Card -->
 <div class="calc-container" style="margin-top: 2rem;">
 <h4 style="font-weight: 800; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem;"><?= __('installment_calculator') ?></h4>
 
 <!-- Raw inputs -->
 <input type="hidden" id="car-price-raw" value="<?= $car['price'] ?>">
 
 <!-- Down payment slider -->
 <div class="form-group">
 <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.5rem;">
 <span><?= __('downpayment') ?></span>
 <span><input type="number" id="downpayment-value" value="<?= round($car['price'] * 0.1) ?>" style="width: 80px; font-weight: 700; text-align: center; border-bottom: 1px solid var(--border-color);"> ريال</span>
 </div>
 <input type="range" id="downpayment-slider" min="0" max="<?= round($car['price'] * 0.9) ?>" step="1000" value="<?= round($car['price'] * 0.1) ?>" style="width: 100%; cursor: pointer;">
 </div>

 <!-- Terms drop down -->
 <div class="form-group">
 <label><?= __('duration') ?></label>
 <select id="term-select" class="form-control" style="font-weight: 700;">
 <option value="12">12 <?= $lang === 'ar' ? 'شهر (سنة)' : 'Months (1 Year)' ?></option>
 <option value="24">24 <?= $lang === 'ar' ? 'شهر (سنتين)' : 'Months (2 Years)' ?></option>
 <option value="36">36 <?= $lang === 'ar' ? 'شهر (3 سنوات)' : 'Months (3 Years)' ?></option>
 <option value="48">48 <?= $lang === 'ar' ? 'شهر (4 سنوات)' : 'Months (4 Years)' ?></option>
 <option value="60" selected>60 <?= $lang === 'ar' ? 'شهر (5 سنوات)' : 'Months (5 Years)' ?></option>
 </select>
 </div>

 <div class="calc-result-box">
 <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);"><?= __('monthly_installment') ?></div>
 <div class="calc-result-val"><span id="calc-monthly-val">0</span> <span style="font-size: 1rem; font-weight: 600;"><?= __('currency') ?></span></div>
 <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;"><?= __('total_finance') ?>: <span id="calc-total-val">0</span> ريال</div>
 </div>
 </div>
 </div>
 </div>

 <!-- Similar cars section -->
 <?php if (!empty($similarCars)): ?>
 <section class="section-padding" style="margin-top: 3rem; border-top: 1px solid var(--border-color);">
 <h3 style="font-weight: 800; margin-bottom: 2rem;"> <?= __('similar_cars') ?></h3>
 <div class="cars-grid">
 <?php foreach ($similarCars as $s): ?>
 <div class="car-card">
 <div class="car-badge"><?= __('new_car_status') ?></div>
 <div class="fav-btn" data-car-id="<?= $s['id'] ?>">️</div>
 <div class="car-img-container">
 <div class="car-image-mock"><?= $lang === 'ar' ? $s['name_ar'] : $s['name_en'] ?></div>
 </div>
 <div class="car-info">
 <div class="car-meta-year"><?= $s['year'] ?> | <?= $lang === 'ar' ? $s['type_ar'] : $s['type_en'] ?></div>
 <h4 class="car-title"><?= $lang === 'ar' ? $s['name_ar'] : $s['name_en'] ?></h4>
 <div class="car-price-row">
 <div class="car-cash-price"><small><?= __('price') ?></small><?= formatPrice($s['price']) ?></div>
 <div class="car-inst-price"><small><?= __('installment_starts') ?></small><span class="car-inst-val"><?= formatPrice($s['min_installment']) ?></span></div>
 </div>
 <div class="car-actions">
 <a href="car.php?id=<?= $s['id'] ?>" class="car-details-btn"><?= __('view_details') ?></a>
 <div class="car-compare-checkbox" data-car-id="<?= $s['id'] ?>">️</div>
 </div>
 </div>
 </div>
 <?php endforeach; ?>
 </div>
 </section>
 <?php endif; ?>
</div>

<!-- ================= MODALS OVERLAYS ================= -->

<!-- 1. Booking Modal -->
<div id="booking-modal" class="modal-overlay">
 <div class="modal-box">
 <span class="modal-close" onclick="closeModal('booking-modal')"></span>
 <h3 style="font-weight: 800; margin-bottom: 1.5rem;"> <?= __('booking_title') ?></h3>
 <form action="car.php?id=<?= $id ?>" method="POST">
 <input type="hidden" name="form_type" value="booking">
 <div class="form-group">
 <label><?= __('full_name') ?> *</label>
 <input type="text" name="name" class="form-control" value="<?= e($currentUser['name'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('phone_number') ?> *</label>
 <input type="tel" name="phone" class="form-control" value="<?= e($currentUser['phone'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= getLanguage() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' ?> *</label>
 <input type="email" name="email" class="form-control" value="<?= e($currentUser['email'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('city_label') ?> *</label>
 <input type="text" name="city" class="form-control" value="<?= e($currentUser['city'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('payment_method_label') ?></label>
 <select name="payment_method" class="form-control">
 <option value="cash"><?= __('payment_cash') ?></option>
 <option value="card"><?= __('payment_card') ?></option>
 <option value="installment"><?= __('payment_installment') ?></option>
 </select>
 </div>
 <div class="form-group">
 <label><?= __('notes_label') ?></label>
 <textarea name="notes" class="form-control" rows="3" placeholder="<?= $lang === 'ar' ? 'أي شروط أو متطلبات إضافية للتسليم...' : 'Any delivery or custom requirements...' ?>"></textarea>
 </div>
 <button type="submit" class="btn-submit"><?= __('book_now') ?></button>
 </form>
 </div>
</div>

<!-- 2. Installments Financing Modal -->
<div id="installment-modal" class="modal-overlay">
 <div class="modal-box">
 <span class="modal-close" onclick="closeModal('installment-modal')"></span>
 <h3 style="font-weight: 800; margin-bottom: 1.5rem;"> <?= __('installment_title') ?></h3>
 <form action="car.php?id=<?= $id ?>" method="POST">
 <input type="hidden" name="form_type" value="installment">
 
 <!-- Calculated carry-overs -->
 <input type="hidden" name="downpayment" id="req-downpayment" value="0">
 <input type="hidden" name="term" id="req-term" value="60">
 <input type="hidden" name="monthly" id="req-monthly" value="0">

 <div class="form-group">
 <label><?= __('full_name') ?> *</label>
 <input type="text" name="name" class="form-control" value="<?= e($currentUser['name'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('phone_number') ?> *</label>
 <input type="tel" name="phone" class="form-control" value="<?= e($currentUser['phone'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= getLanguage() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' ?> *</label>
 <input type="email" name="email" class="form-control" value="<?= e($currentUser['email'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('city_label') ?> *</label>
 <input type="text" name="city" class="form-control" value="<?= e($currentUser['city'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('national_id_label') ?> *</label>
 <input type="text" name="national_id" class="form-control" placeholder="1XXXXXXXXX" required>
 </div>
 <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
 <div>
 <label><?= __('salary_label') ?> *</label>
 <input type="number" name="salary" class="form-control" required>
 </div>
 <div>
 <label><?= __('employer_label') ?> *</label>
 <input type="text" name="employer" class="form-control" placeholder="<?= $lang === 'ar' ? 'حكومي، خاص...' : 'Gov, Private...' ?>" required>
 </div>
 </div>
 <div class="form-group">
 <label><?= __('work_duration_label') ?> *</label>
 <input type="number" name="work_duration" class="form-control" placeholder="<?= $lang === 'ar' ? 'عدد السنوات' : 'Years of service' ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('notes_label') ?></label>
 <textarea name="notes" class="form-control" rows="2"></textarea>
 </div>
 
 <button type="submit" class="btn-submit"><?= __('send_request') ?></button>
 </form>
 </div>
</div>

<!-- 3. Test Drive Modal -->
<div id="test-drive-modal" class="modal-overlay">
 <div class="modal-box">
 <span class="modal-close" onclick="closeModal('test-drive-modal')"></span>
 <h3 style="font-weight: 800; margin-bottom: 1.5rem;">⏱ <?= __('test_drive_title') ?></h3>
 <form action="car.php?id=<?= $id ?>" method="POST">
 <input type="hidden" name="form_type" value="test_drive">
 <div class="form-group">
 <label><?= __('full_name') ?> *</label>
 <input type="text" name="name" class="form-control" value="<?= e($currentUser['name'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('phone_number') ?> *</label>
 <input type="tel" name="phone" class="form-control" value="<?= e($currentUser['phone'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= getLanguage() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' ?> *</label>
 <input type="email" name="email" class="form-control" value="<?= e($currentUser['email'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('city_label') ?> *</label>
 <input type="text" name="city" class="form-control" value="<?= e($currentUser['city'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'الوقت المفضل للاتصال والتجربة' : 'Preferred Contact Time' ?></label>
 <input type="text" name="preferred_time" class="form-control" placeholder="<?= $lang === 'ar' ? 'صباحاً، مساءً...' : 'Morning, Evening...' ?>">
 </div>
 <button type="submit" class="btn-submit"><?= __('send_request') ?></button>
 </form>
 </div>
</div>

<!-- 4. Callback Modal -->
<div id="callback-modal" class="modal-overlay">
 <div class="modal-box">
 <span class="modal-close" onclick="closeModal('callback-modal')"></span>
 <h3 style="font-weight: 800; margin-bottom: 1.5rem;"> <?= __('callback_title') ?></h3>
 <form action="car.php?id=<?= $id ?>" method="POST">
 <input type="hidden" name="form_type" value="callback">
 <div class="form-group">
 <label><?= __('full_name') ?> *</label>
 <input type="text" name="name" class="form-control" value="<?= e($currentUser['name'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('phone_number') ?> *</label>
 <input type="tel" name="phone" class="form-control" value="<?= e($currentUser['phone'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= getLanguage() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' ?> *</label>
 <input type="email" name="email" class="form-control" value="<?= e($currentUser['email'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= __('city_label') ?> *</label>
 <input type="text" name="city" class="form-control" value="<?= e($currentUser['city'] ?? '') ?>" required>
 </div>
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'موضوع الاستفسار' : 'Topic of inquiry' ?></label>
 <select name="topic" class="form-control">
 <option value="Financing"><?= $lang === 'ar' ? 'استفسار عن التمويل والتقسيط' : 'Financing & Installment' ?></option>
 <option value="Specs"><?= $lang === 'ar' ? 'استفسار عن مواصفات السيارة' : 'Vehicle Specifications' ?></option>
 <option value="Delivery"><?= $lang === 'ar' ? 'استفسار عن التسليم والضمان' : 'Delivery & Warranty' ?></option>
 </select>
 </div>
 <button type="submit" class="btn-submit"><?= __('send_request') ?></button>
 </form>
 </div>
</div>

<!-- 5. Fullscreen Gallery modal overlay -->
<div id="fullscreen-gallery-overlay" class="modal-overlay fullscreen-gallery">
 <div class="modal-box">
 <span class="modal-close" onclick="closeModal('fullscreen-gallery-overlay')" style="color: #fff; font-size: 2.5rem; top: 10px; right: 10px;"></span>
 <img id="fullscreen-image-element" src="" class="fullscreen-img" alt="Zoomed Car Image">
 </div>
</div>

<!-- Specs Tab Switcher local helper script -->
<script>
function switchSpecsTab(event, tabId) {
 const contents = document.querySelectorAll('.specs-tab-content');
 contents.forEach(c => c.style.display = 'none');

 const buttons = document.querySelectorAll('.specs-tab-btn');
 buttons.forEach(b => b.classList.remove('active'));

 document.getElementById(tabId).style.display = 'block';
 event.currentTarget.classList.add('active');
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
