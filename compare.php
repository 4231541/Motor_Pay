<?php
// C:\xampp\htdocs\سيارة\compare.php
require_once __DIR__ . '/includes/header.php';
?>

<div class="container section-padding">
    <div class="section-title">
        <h2><?= __('compare') ?></h2>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">
            <?= getLanguage() === 'ar' 
                ? 'قارن بين المواصفات الفنية والميزات التفصيلية لثلاث سيارات كحد أقصى لمساعدتك في اتخاذ القرار المناسب.' 
                : 'Compare technical specs and key options for up to 3 cars to help you make the right choice.' ?>
        </p>
    </div>

    <!-- Dynamic Compare layout placeholder. Controlled by compare.js -->
    <div id="compare-page-content" style="margin-top: 3rem;">
        <!-- Loaded via AJAX -->
        <div class="text-center" style="padding: 4rem 0;">
            <div class="splash-loader" style="margin: 0 auto;">
                <div class="splash-loader-bar"></div>
            </div>
            <p style="margin-top: 1rem; color: var(--text-secondary);"><?= getLanguage() === 'ar' ? 'جاري تحميل جدول المقارنة...' : 'Loading comparison table...' ?></p>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
