<?php
// C:\xampp\htdocs\سيارة\index.php
require_once __DIR__ . '/includes/header.php';

// Fetch all brands
$brands = $db->query("SELECT * FROM brands")->fetchAll();

// Fetch cars with their brands
$sqlCars = "SELECT cars.*, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en 
            FROM cars 
            JOIN brands ON cars.brand_id = brands.id 
            WHERE cars.is_available = 1";
$cars = $db->query($sqlCars)->fetchAll();

// Language toggle helper
$lang = getLanguage();

// Handle search form submit
$searchQuery = $_GET['q'] ?? '';
?>

<!-- Hero Banner Section -->
<header class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <div class="eyebrow">
                <?= $lang === 'ar' ? 'السيارات الجديدة 2025 / 2026' : 'New Cars 2025 / 2026' ?>
            </div>
            <h1>
                <?php if ($lang === 'ar'): ?>
                    احجز سيارتك <span>الجديدة بالتقسيط</span> في دقائق
                <?php else: ?>
                    Book Your <span>New Car on Installment</span> in Minutes
                <?php endif; ?>
            </h1>
            <p>
                <?= $lang === 'ar' 
                    ? 'ابحث عن سيارة أحلامك من بين مئات الموديلات، واحسب قسطك الشهري بدقة، وقدم طلب تمويلك كاش أو تقسيط بضغطة زر.' 
                    : 'Search for your dream car from hundreds of models, calculate your monthly payment, and submit your cash or financing request instantly.' ?>
            </p>

            <form action="search.php" method="GET" class="hero-search-box">
                <input type="text" name="q" placeholder="<?= __('search_placeholder') ?>" value="<?= e($searchQuery) ?>">
                <button type="submit"><?= $lang === 'ar' ? 'بحث' : 'Search' ?></button>
            </form>

            <div class="hero-stats">
                <div class="hero-stat"><strong>500+</strong><span><?= $lang === 'ar' ? 'موديل متاح' : 'Car Models' ?></span></div>
                <div class="hero-stat"><strong>15+</strong><span><?= $lang === 'ar' ? 'علامة تجارية' : 'Top Brands' ?></span></div>
                <div class="hero-stat"><strong>0%</strong><span><?= $lang === 'ar' ? 'فائدة على بعض العروض' : 'Interest on Select Offers' ?></span></div>
            </div>
        </div>
        <div class="hero-visual">
            <div style="position: relative; border-radius: 24px; overflow: hidden; box-shadow: var(--shadow-xl), var(--gold-glow); border: 2px solid rgba(212,175,55,0.4); max-width: 100%;">
                <img src="assets/images/hero-1.jpg" alt="Motor Pay Hero" style="width: 100%; height: auto; display: block;">
            </div>
        </div>
    </div>
</header>

<!-- Brands Slider Section -->
<section class="brands-section">
    <div class="container">
        <h4 style="font-weight: 700; margin-bottom: 1rem;"><?= __('brands') ?></h4>
        <div class="brand-wrapper">
            <?php foreach ($brands as $b): ?>
                <a href="search.php?brand_id=<?= $b['id'] ?>" class="brand-card">
                    <div class="brand-logo-mock">
                        <?= mb_substr($b['name_en'], 0, 2) ?>
                    </div>
                    <span class="brand-name"><?= $lang === 'ar' ? $b['name_ar'] : $b['name_en'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Cars Showcase Section -->
<section class="section-padding">
    <div class="container">
        <div class="section-header">
            <div class="section-title">
                <h2><?= __('new_cars') ?></h2>
            </div>
            <div class="category-tabs" id="home-tabs">
                <button class="tab-btn active" data-filter="all"><?= $lang === 'ar' ? 'الكل' : 'All' ?></button>
                <button class="tab-btn" data-filter="installment"><?= __('installment_offers') ?></button>
                <button class="tab-btn" data-filter="latest"><?= __('latest_cars') ?></button>
                <button class="tab-btn" data-filter="viewed"><?= __('most_viewed') ?></button>
                <button class="tab-btn" data-filter="requested"><?= __('most_requested') ?></button>
            </div>
        </div>

        <div class="cars-grid" id="cars-grid">
            <?php foreach ($cars as $index => $c): 
                $cImages = json_decode($c['images'], true);
                $primaryImage = !empty($cImages) ? 'assets/images/cars/' . ltrim($cImages[0], '/') : '';
                
                // Determine tags for dynamic client filtering
                $isInstallment = $c['min_installment'] < 2000 ? 'true' : 'false';
                $isLatest = $c['year'] >= 2026 ? 'true' : 'false';
                $isViewed = $index % 2 === 0 ? 'true' : 'false'; // Mocked statistics views
                $isRequested = $index % 3 === 0 ? 'true' : 'false'; // Mocked order count
            ?>
                <div class="car-card" 
                     data-installment="<?= $isInstallment ?>" 
                     data-latest="<?= $isLatest ?>" 
                     data-viewed="<?= $isViewed ?>" 
                     data-requested="<?= $isRequested ?>">
                     
                    <div class="car-badge"><?= __('new_car_status') ?></div>
                    <div class="fav-btn" data-car-id="<?= $c['id'] ?>">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </div>

                    <div class="car-img-container" style="background-image: url('<?= empty($primaryImage) ? 'assets/images/placeholder_car.jpg' : e($primaryImage) ?>'); background-size: cover; background-position: center;">
                        <!-- Mock overlay removed, actual image displayed as background -->
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
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <polyline points="9 11 12 14 22 4"/>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="section-padding" style="padding-top: 0; margin-bottom: 2rem;">
    <div class="container">
        <div class="cta-banner">
            <h2><?= $lang === 'ar' ? 'هل ترغب بحساب تمويل خاص بك؟' : 'Looking for a personalized finance quote?' ?></h2>
            <p>
                <?= $lang === 'ar' 
                    ? 'استخدم حاسبة التقسيط التفاعلية المتوفرة في تفاصيل أي سيارة لتحديد الدفعة الأولى ومدة التمويل وإرسال طلبك فوراً للبنوك.' 
                    : 'Use the interactive financing calculator on any car detail page to adjust your downpayment and term, then submit directly to lenders.' ?>
            </p>
            <a href="search.php" class="btn-cta-light">
                <?= $lang === 'ar' ? 'تصفح السيارات الآن' : 'Browse Cars Now' ?>
            </a>
        </div>
    </div>
</section>

<!-- Inline Home Tab Filter Controller -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('#home-tabs .tab-btn');
    const cards = document.querySelectorAll('#cars-grid .car-card');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const filter = tab.dataset.filter;

            cards.forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'flex';
                } else if (filter === 'installment') {
                    card.style.display = card.dataset.installment === 'true' ? 'flex' : 'none';
                } else if (filter === 'latest') {
                    card.style.display = card.dataset.latest === 'true' ? 'flex' : 'none';
                } else if (filter === 'viewed') {
                    card.style.display = card.dataset.viewed === 'true' ? 'flex' : 'none';
                } else if (filter === 'requested') {
                    card.style.display = card.dataset.requested === 'true' ? 'flex' : 'none';
                }
            });
        });
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
