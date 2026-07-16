<?php
// C:\xampp\htdocs\سيارة\includes\header.php
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/functions.php';

// Fetch user profile if logged in
$currentUser = isLoggedIn() ? getCurrentUser($db) : null;

// Fetch notification count
$notifCount = 0;
if (isLoggedIn()) {
    $stmtN = $db->prepare("SELECT COUNT(*) FROM notifications WHERE (user_id IS NULL OR user_id = ?) AND is_read = 0");
    $stmtN->execute([$_SESSION['user_id']]);
    $notifCount = intval($stmtN->fetchColumn());
}
?>
<!DOCTYPE html>
<html lang="<?= getLanguage() ?>" dir="<?= getDirection() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= getLanguage() === 'ar' ? 'منصة سيارة - احجز سيارتك الجديدة بالتقسيط بأفضل العروض والأسعار' : 'Syara - Book your new car with the best installment offers and prices' ?>">
    <title><?= __('app_name') ?> | <?= __('app_slogan') ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="lang-<?= getLanguage() ?>">

    <?php if (!isset($_SESSION['splash_shown'])): $_SESSION['splash_shown'] = true; ?>
    <div id="splash-screen">
        <div class="splash-logo">
            <img src="assets/images/logo.jpg" alt="Motor Pay Logo">
        </div>
        <div class="splash-title"><span class="logo-text-motor">MOTOR</span> <span style="color: #fff;">PAY</span></div>
        <div class="splash-sub">Fast, Secure Financing</div>
        <div class="splash-loader">
            <div class="splash-loader-bar"></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 2. Onboarding Screen Modal -->
    <div id="onboarding-overlay" class="modal-overlay">
        <div class="onboarding-card">
            <div class="onboarding-slides">
                <!-- Slide 1 -->
                <div class="onboarding-slide active">
                    <div class="onboarding-icon">
                        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="22" cy="22" r="12"/>
                            <line x1="30" y1="30" x2="40" y2="40"/>
                        </svg>
                    </div>
                    <h3 class="onboarding-title"><?= __('onboarding_title_1') ?></h3>
                    <p class="onboarding-desc"><?= __('onboarding_desc_1') ?></p>
                </div>
                <!-- Slide 2 -->
                <div class="onboarding-slide">
                    <div class="onboarding-icon">
                        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <rect x="6" y="12" width="36" height="26" rx="4"/>
                            <path d="M6 20h36"/>
                            <path d="M16 30h6M26 30h6"/>
                        </svg>
                    </div>
                    <h3 class="onboarding-title"><?= __('onboarding_title_2') ?></h3>
                    <p class="onboarding-desc"><?= __('onboarding_desc_2') ?></p>
                </div>
                <!-- Slide 3 -->
                <div class="onboarding-slide">
                    <div class="onboarding-icon">
                        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24 4l5 10 11 1.5-8 7.5 2 11L24 28l-10 6 2-11-8-7.5 11-1.5z"/>
                        </svg>
                    </div>
                    <h3 class="onboarding-title"><?= __('onboarding_title_3') ?></h3>
                    <p class="onboarding-desc"><?= __('onboarding_desc_3') ?></p>
                </div>
            </div>

            <div class="onboarding-dots">
                <span class="onboarding-dot active"></span>
                <span class="onboarding-dot"></span>
                <span class="onboarding-dot"></span>
            </div>

            <div class="onboarding-actions">
                <span class="btn-skip"><?= __('skip') ?></span>
                <button class="btn-next"><?= __('next') ?></button>
                <button class="btn-start" style="display: none;"><?= __('start_now') ?></button>
            </div>
        </div>
    </div>

    <!-- 3. Navigation Bar -->
    <nav class="navbar">
        <div class="container navbar-container">
            <a href="index.php" class="logo-link">
                <div class="logo-img-wrap">
                    <img src="assets/images/logo.jpg" alt="Motor Pay">
                </div>
                <span><span class="logo-text-motor">MOTOR</span> <span class="logo-text-pay">PAY</span></span>
            </a>

            <div class="menu-toggle" id="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <ul class="nav-menu" id="nav-menu">
                <li><a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"><?= __('home') ?></a></li>
                <li><a href="search.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'search.php' ? 'active' : '' ?>"><?= __('search') ?></a></li>
                <li><a href="offers.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'offers.php' ? 'active' : '' ?>"><?= __('offers') ?></a></li>
                <li><a href="compare.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'compare.php' ? 'active' : '' ?>"><?= __('compare') ?></a></li>
                <li>
                    <a href="profile.php?tab=favorites" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'profile.php' && ($_GET['tab'] ?? '') == 'favorites') ? 'active' : '' ?>">
                        <?= __('favorites') ?>
                    </a>
                </li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php?tab=requests" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'profile.php' && ($_GET['tab'] ?? '') == 'requests') ? 'active' : '' ?>"><?= __('my_requests') ?></a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="backend/index.php" class="nav-link" style="color: var(--accent);"><?= __('admin_panel') ?></a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <div class="nav-controls">
                <!-- Theme Toggle -->
                <div class="control-btn" id="theme-toggle" title="<?= __('theme_mode') ?>">
                    <svg viewBox="0 0 24 24" id="icon-moon" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    <svg viewBox="0 0 24 24" id="icon-sun" style="display:none;" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/>
                        <line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                </div>

                <!-- Language Toggle -->
                <?php if (getLanguage() === 'ar'): ?>
                    <div class="control-btn" onclick="switchLanguage('en')" title="English" style="font-size:0.75rem; font-weight:800; letter-spacing:0.05em;">EN</div>
                <?php else: ?>
                    <div class="control-btn" onclick="switchLanguage('ar')" title="العربية" style="font-size:0.75rem; font-weight:800; letter-spacing:0.05em;">AR</div>
                <?php endif; ?>

                <!-- Favorites -->
                <a href="profile.php?tab=favorites" class="control-btn" title="<?= __('favorites') ?>">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    <span class="badge-count" id="fav-badge" style="display: none;">0</span>
                </a>

                <!-- Compare -->
                <a href="compare.php" class="control-btn" title="<?= __('compare') ?>">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <polyline points="9 11 12 14 22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    <span class="badge-count" id="compare-badge" style="display: none;">0</span>
                </a>

                <!-- Notifications -->
                <?php if (isLoggedIn()): ?>
                    <a href="profile.php?tab=notifications" class="control-btn" title="<?= __('notifications') ?>">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <?php if ($notifCount > 0): ?>
                            <span class="badge-count"><?= $notifCount ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <!-- User / Login -->
                <?php if (isLoggedIn()): ?>
                    <a href="profile.php" class="control-btn" title="<?= __('profile') ?>">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </a>
                    <a href="auth.php?logout=1" class="btn-login-nav" style="background: var(--danger); box-shadow: 0 2px 10px rgba(211,47,47,0.25);"><?= __('logout') ?></a>
                <?php else: ?>
                    <button class="btn-login-nav" onclick="openAuthModal('login')" style="border: none; cursor: pointer;"><?= __('login') ?></button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

