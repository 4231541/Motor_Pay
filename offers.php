<?php
// C:\xampp\htdocs\سيارة\offers.php
require_once __DIR__ . '/shared/header.php';

// Fetch active offers
$stmt = $db->query("SELECT offers.*, cars.id AS car_id, cars.name_ar AS car_name_ar, cars.name_en AS car_name_en, cars.price, cars.min_installment, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en
 FROM offers
 LEFT JOIN cars ON offers.car_id = cars.id
 LEFT JOIN brands ON cars.brand_id = brands.id
 ORDER BY offers.id DESC");
$offers = $stmt->fetchAll();

$lang = getLanguage();
?>

<div class="container section-padding">
 <div class="section-title">
 <h2><?= __('offers') ?></h2>
 <p style="color: var(--text-secondary); margin-top: 0.5rem;">
 <?= $lang === 'ar' 
 ? 'اكتشف أفضل الخصومات الحصرية وعروض تمويل تقسيط السيارات الجديدة بالتنسيق مع البنوك المعتمدة.' 
 : 'Explore exclusive cash discounts and new car financing offers in coordination with authorized lenders.' ?>
 </p>
 </div>

 <div style="display: flex; flex-direction: column; gap: 3rem; margin-top: 3rem;">
 <?php if (empty($offers)): ?>
 <p class="text-center" style="color: var(--text-secondary); padding: 4rem 0;"><?= $lang === 'ar' ? 'لا توجد عروض فعالة حالياً.' : 'No active offers at this time.' ?></p>
 <?php else: ?>
 <?php foreach ($offers as $off): ?>
 <div style="background-color: var(--bg-secondary); border-radius: 24px; border: 1px solid var(--border-color); box-shadow: var(--card-shadow); overflow: hidden; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
 
 <!-- Left: Graphic / mockup presentation -->
 <div style="background: linear-gradient(135deg, var(--primary), var(--info)); color: #fff; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 3rem; text-align: center; position: relative;">
 <?php if ($off['discount_pct'] > 0): ?>
 <div style="position: absolute; top: 20px; right: 20px; background-color: var(--accent); color: #fff; font-weight: bold; border-radius: 50%; width: 60px; height: 60px; display: flex; justify-content: center; align-items: center; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
 -<?= $off['discount_pct'] ?>%
 </div>
 <?php endif; ?>
 
 <div style="font-size: 5rem; margin-bottom: 1rem; animation: float 3s infinite ease-in-out;"></div>
 <h4 style="font-size: 1.6rem; font-weight: 800;"><?= $lang === 'ar' ? $off['brand_name_ar'] : $off['brand_name_en'] ?></h4>
 <p style="font-size: 1.1rem; font-weight: 600; opacity: 0.9; margin-top: 0.5rem;"><?= $lang === 'ar' ? $off['car_name_ar'] : $off['car_name_en'] ?></p>
 </div>

 <!-- Right: Info Details -->
 <div style="padding: 3rem; display: flex; flex-direction: column; justify-content: center;">
 <span style="font-size: 0.8rem; font-weight: bold; color: var(--accent); text-transform: uppercase; margin-bottom: 0.5rem;"> <?= $lang === 'ar' ? 'عرض حصري' : 'Exclusive Deal' ?></span>
 <h3 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 1rem; line-height: 1.3;"><?= $lang === 'ar' ? $off['title_ar'] : $off['title_en'] ?></h3>
 <p style="color: var(--text-secondary); margin-bottom: 2rem;"><?= $lang === 'ar' ? $off['description_ar'] : $off['description_en'] ?></p>
 
 <div style="display: flex; gap: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-bottom: 2.5rem; font-size: 0.9rem;">
 <div>
 <span style="color: var(--text-muted); display: block;"><?= __('price') ?></span>
 <strong><?= formatPrice($off['price']) ?></strong>
 </div>
 <div>
 <span style="color: var(--text-muted); display: block;"><?= __('installment_starts') ?></span>
 <strong style="color: var(--success);"><?= formatPrice($off['min_installment']) ?> / <?= __('month') ?></strong>
 </div>
 <div>
 <span style="color: var(--text-muted); display: block;"><?= $lang === 'ar' ? 'صالح حتى' : 'Valid Until' ?></span>
 <strong style="color: var(--danger);"><?= $off['valid_until'] ?></strong>
 </div>
 </div>

 <?php if ($off['car_id']): ?>
 <a href="car.php?id=<?= $off['car_id'] ?>" class="btn-submit" style="margin: 0; align-self: flex-start; padding: 0.8rem 2.5rem;"><?= $lang === 'ar' ? 'اغتنم العرض الآن' : 'Claim Deal Now' ?></a>
 <?php endif; ?>
 </div>
 </div>
 <?php endforeach; ?>
 <?php endif; ?>
 </div>
</div>

<style>
@media (max-width: 768px) {
 .container > div > div {
 grid-template-columns: 1fr !important;
 }
}
</style>

<?php
require_once __DIR__ . '/shared/footer.php';
?>
