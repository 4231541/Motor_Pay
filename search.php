<?php
// C:\xampp\htdocs\سيارة\search.php
require_once __DIR__ . '/shared/header.php';

// Fetch options for dropdowns
$brands = $db->query("SELECT * FROM brands")->fetchAll();
$bodyTypes = $db->query("SELECT DISTINCT type_ar, type_en FROM cars WHERE type_en IS NOT NULL")->fetchAll();
$fuels = $db->query("SELECT DISTINCT fuel_ar, fuel_en FROM cars WHERE fuel_en IS NOT NULL")->fetchAll();
$transmissions = $db->query("SELECT DISTINCT transmission_ar, transmission_en FROM cars WHERE transmission_en IS NOT NULL")->fetchAll();
$drives = $db->query("SELECT DISTINCT drive_ar, drive_en FROM cars WHERE drive_en IS NOT NULL")->fetchAll();

// Capture filter variables
$q = $_GET['q'] ?? '';
$brandId = $_GET['brand_id'] ?? '';
$type = $_GET['type'] ?? '';
$fuel = $_GET['fuel'] ?? '';
$transmission = $_GET['transmission'] ?? '';
$drive = $_GET['drive'] ?? '';
$priceMax = $_GET['price_max'] ?? '';

// Build Query
$sql = "SELECT cars.*, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en 
        FROM cars 
        JOIN brands ON cars.brand_id = brands.id 
        WHERE cars.is_available = 1";

$conditions = [];
$params = [];

if ($q !== '') {
    $conditions[] = "(cars.name_ar LIKE :q OR cars.name_en LIKE :q OR brands.name_ar LIKE :q OR brands.name_en LIKE :q)";
    $params[':q'] = '%' . $q . '%';
}
if ($brandId !== '') {
    $conditions[] = "cars.brand_id = :brand_id";
    $params[':brand_id'] = intval($brandId);
}
if ($type !== '') {
    $conditions[] = "cars.type_en = :type";
    $params[':type'] = $type;
}
if ($fuel !== '') {
    $conditions[] = "cars.fuel_en = :fuel";
    $params[':fuel'] = $fuel;
}
if ($transmission !== '') {
    $conditions[] = "cars.transmission_en = :transmission";
    $params[':transmission'] = $transmission;
}
if ($drive !== '') {
    $conditions[] = "cars.drive_en = :drive";
    $params[':drive'] = $drive;
}
if ($priceMax !== '') {
    $conditions[] = "cars.price <= :price_max";
    $params[':price_max'] = floatval($priceMax);
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY cars.id DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();

$lang = getLanguage();
?>

<div class="container section-padding">
    <div class="section-title">
        <h2><?= __('search') ?></h2>
    </div>

    <div class="search-layout">
        <!-- Sticky Sidebar Filters -->
        <aside class="filter-sidebar">
            <form action="search.php" method="GET" id="search-filter-form">
                <!-- Search term carry-over -->
                <div class="form-group">
                    <label><?= $lang === 'ar' ? 'كلمة البحث' : 'Keyword' ?></label>
                    <input type="text" name="q" class="form-control" placeholder="<?= __('search_placeholder') ?>" value="<?= e($q) ?>">
                </div>

                <!-- Brand selection -->
                <div class="form-group">
                    <label><?= __('brand') ?></label>
                    <select name="brand_id" class="form-control">
                        <option value=""><?= __('brand_select') ?></option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= $brandId == $b['id'] ? 'selected' : '' ?>>
                                <?= $lang === 'ar' ? $b['name_ar'] : $b['name_en'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Body type -->
                <div class="form-group">
                    <label><?= $lang === 'ar' ? 'نوع الهيكل' : 'Body Type' ?></label>
                    <select name="type" class="form-control">
                        <option value=""><?= $lang === 'ar' ? 'جميع الأنواع' : 'All Types' ?></option>
                        <?php foreach ($bodyTypes as $bt): ?>
                            <option value="<?= $bt['type_en'] ?>" <?= $type == $bt['type_en'] ? 'selected' : '' ?>>
                                <?= $lang === 'ar' ? $bt['type_ar'] : $bt['type_en'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Max Price Slider -->
                <div class="form-group">
                    <label><?= __('price_range') ?></label>
                    <input type="number" name="price_max" id="price_max_input" class="form-control" placeholder="<?= $lang === 'ar' ? 'الحد الأقصى للسعر' : 'Max price limit' ?>" value="<?= e($priceMax) ?>">
                </div>

                <!-- Fuel Type -->
                <div class="form-group">
                    <label><?= __('fuel') ?></label>
                    <select name="fuel" class="form-control">
                        <option value=""><?= __('fuel_select') ?></option>
                        <?php foreach ($fuels as $f): ?>
                            <option value="<?= $f['fuel_en'] ?>" <?= $fuel == $f['fuel_en'] ? 'selected' : '' ?>>
                                <?= $lang === 'ar' ? $f['fuel_ar'] : $f['fuel_en'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Transmission -->
                <div class="form-group">
                    <label><?= __('transmission') ?></label>
                    <select name="transmission" class="form-control">
                        <option value=""><?= __('trans_select') ?></option>
                        <?php foreach ($transmissions as $t): ?>
                            <option value="<?= $t['transmission_en'] ?>" <?= $transmission == $t['transmission_en'] ? 'selected' : '' ?>>
                                <?= $lang === 'ar' ? $t['transmission_ar'] : $t['transmission_en'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Drivetrain -->
                <div class="form-group">
                    <label><?= __('drive') ?></label>
                    <select name="drive" class="form-control">
                        <option value=""><?= $lang === 'ar' ? 'نظام الدفع (الكل)' : 'All Drivetrains' ?></option>
                        <?php foreach ($drives as $d): ?>
                            <option value="<?= $d['drive_en'] ?>" <?= $drive == $d['drive_en'] ? 'selected' : '' ?>>
                                <?= $lang === 'ar' ? $d['drive_ar'] : $d['drive_en'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-submit"><?= __('filter_btn') ?></button>
                <a href="search.php" class="btn-submit" style="background: var(--input-bg); color: var(--text-secondary); display: block; margin-top: 0.5rem;"><?= __('reset_btn') ?></a>
            </form>
        </aside>

        <!-- Search Results Grid -->
        <main>
            <div style="margin-bottom: 1.5rem; color: var(--text-secondary); font-weight: 600;">
                <?= count($cars) ?> <?= $lang === 'ar' ? 'سيارة مطابقة للبحث' : 'cars matching search criteria' ?>
            </div>

            <?php if (empty($cars)): ?>
                <div class="text-center" style="padding: 4rem 0; background-color: var(--bg-secondary); border-radius: 20px; border: 1px solid var(--border-color);">
                    <div style="width: 72px; height: 72px; background: var(--primary-light); border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 1.25rem;">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width:30px;height:30px;stroke:var(--primary);fill:none;stroke-width:2;stroke-linecap:round;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <h4><?= $lang === 'ar' ? 'عذراً، لم نجد أي نتائج تطابق بحثك.' : 'Sorry, we found no matches for your search.' ?></h4>
                    <p style="color: var(--text-secondary); margin-top: 0.5rem;"><?= $lang === 'ar' ? 'يرجى مراجعة خيارات الفلاتر وإعادة المحاولة.' : 'Try adjusting your filters or expanding your keyword search.' ?></p>
                </div>
            <?php else: ?>
                <div class="cars-grid">
                    <?php foreach ($cars as $c): 
                        $cImages = json_decode($c['images'], true);
                        $primaryImage = !empty($cImages) ? 'assets/images/cars/' . ltrim($cImages[0], '/') : '';
                    ?>
                        <div class="car-card">
                            <div class="car-badge"><?= __('new_car_status') ?></div>
                            <div class="fav-btn" data-car-id="<?= $c['id'] ?>">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                            </div>
                            
                            <div class="car-img-container" style="background-image: url('<?= empty($primaryImage) ? 'assets/images/placeholder_car.jpg' : e($primaryImage) ?>'); background-size: cover; background-position: center;">
                            </div>
                            
                            <div class="car-info">
                                <div class="car-meta-year"><?= $c['year'] ?> | <?= $lang === 'ar' ? $c['type_ar'] : $c['type_en'] ?></div>
                                <h4 class="car-title"><?= $lang === 'ar' ? $c['name_ar'] : $c['name_en'] ?></h4>
                                
                                <div class="car-price-row">
                                    <div class="car-cash-price">
                                        <small><?= __('price') ?></small>
                                        <?= formatPrice($c['price']) ?>
                                    </div>
                                    <div class="car-inst-price">
                                        <small><?= __('installment_starts') ?></small>
                                        <span class="car-inst-val"><?= formatPrice($c['min_installment']) ?></span><small style="display:inline;">/<?= __('month') ?></small>
                                    </div>
                                </div>
                                
                                <div class="car-actions">
                                    <a href="car.php?id=<?= $c['id'] ?>" class="car-details-btn"><?= __('view_details') ?></a>
                                    <div class="car-compare-checkbox" data-car-id="<?= $c['id'] ?>" title="<?= __('compare') ?>">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php
require_once __DIR__ . '/shared/footer.php';
?>
