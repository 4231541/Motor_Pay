<?php
// C:\xampp\htdocs\سيارة\includes\footer.php
?>
    <footer class="footer">
        <div class="container footer-grid">
            <div>
                <div class="footer-logo-wrap">
                    <div class="footer-logo-img">
                        <img src="assets/images/logo.jpg" alt="Motor Pay">
                    </div>
                    <div class="footer-logo-text"><span class="logo-text-motor">MOTOR</span> <span style="color: #fff;">PAY</span></div>
                </div>
                <p class="footer-about">
                    <?= getLanguage() === 'ar' 
                        ? 'Motor Pay هي بوابتك الذكية لشراء سيارتك الجديدة بالتقسيط التمويلي الميسر بالتعاون مع كبرى البنوك والشركات التمويلية في المملكة.' 
                        : 'Motor Pay is your smart gateway to browse and purchase brand new cars with flexible financing in partnership with major Saudi banks and lenders.' ?>
                </p>
            </div>
            <div>
                <h4 class="footer-title"><?= __('home') ?></h4>
                <ul class="footer-links">
                    <li><a href="index.php"><?= __('home') ?></a></li>
                    <li><a href="search.php"><?= __('search') ?></a></li>
                    <li><a href="offers.php"><?= __('offers') ?></a></li>
                    <li><a href="compare.php"><?= __('compare') ?></a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-title"><?= getLanguage() === 'ar' ? 'معلومات' : 'Information' ?></h4>
                <ul class="footer-links">
                    <li><a href="pages.php?page=about"><?= __('about_us') ?></a></li>
                    <li><a href="pages.php?page=faq"><?= __('faqs') ?></a></li>
                    <li><a href="pages.php?page=contact"><?= __('contact_us') ?></a></li>
                    <li><a href="pages.php?page=privacy"><?= __('privacy_policy') ?></a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-title"><?= getLanguage() === 'ar' ? 'النشرة البريدية' : 'Newsletter' ?></h4>
                <p class="footer-newsletter-p">
                    <?= getLanguage() === 'ar' ? 'اشترك معنا للحصول على آخر عروض السيارات والتقسيط المميزة.' : 'Subscribe to receive the latest car deals and financing promotions.' ?>
                </p>
                <form class="newsletter-form" onsubmit="event.preventDefault(); alert('<?= getLanguage() === 'ar' ? 'تم الاشتراك بنجاح!' : 'Successfully subscribed!' ?>'); this.reset();">
                    <input type="email" placeholder="example@domain.com" required>
                    <button type="submit"><?= getLanguage() === 'ar' ? 'اشتراك' : 'Subscribe' ?></button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?= date('Y') ?> <?= __('app_name') ?>. <?= getLanguage() === 'ar' ? 'جميع الحقوق محفوظة.' : 'All Rights Reserved.' ?></p>
            </div>
        </div>
    </footer>

    <!-- Auth Modal -->
    <div id="auth-modal" class="modal-overlay" style="z-index: 9999;">
        <div class="auth-container" style="position: relative; max-width: 450px; width: 90%; background: var(--bg-primary); padding: 2.5rem 2rem; border-radius: 24px; box-shadow: var(--shadow-xl);">
            <button onclick="closeAuthModal()" style="position: absolute; top: 15px; right: 15px; background: transparent; border: none; font-size: 1.5rem; color: var(--text-secondary); cursor: pointer; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%;">&times;</button>
            
            <div id="auth-modal-error" style="display:none; background-color: rgba(239, 68, 68, 0.15); color: var(--danger); padding: 0.8rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; font-weight: 700; border: 1px solid rgba(239, 68, 68, 0.25);"></div>
            <div id="auth-modal-success" style="display:none; background-color: rgba(16, 185, 129, 0.15); color: var(--success); padding: 0.8rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.25);"></div>

            <!-- LOGIN PANEL -->
            <div id="auth-login-panel">
                <h3 style="font-weight: 800; text-align: center; margin-bottom: 2rem;"><?= __('welcome_back') ?></h3>
                <form id="login-form" onsubmit="submitAuth(event, 'login')">
                    <div class="form-group">
                        <label><?= __('email_or_phone') ?></label>
                        <input type="text" name="email" class="form-control" placeholder="user@syarah.com" required>
                    </div>
                    <div class="form-group">
                        <label><?= __('password') ?></label>
                        <input type="password" name="password" class="form-control" required>
                        <div style="text-align: end; margin-top: 0.5rem;">
                            <a href="javascript:void(0)" onclick="switchAuthTab('forgot')" style="font-size: 0.8rem; color: var(--primary); font-weight: 600;"><?= __('forgot_password') ?></a>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit"><?= __('login') ?></button>
                </form>

                <div style="text-align: center; margin-top: 2rem;">
                    <a href="javascript:void(0)" onclick="switchAuthTab('register')" style="color: var(--primary); font-weight: 700; font-size: 0.9rem;"><?= __('dont_have_account') ?></a>
                </div>
            </div>

            <!-- REGISTRATION PANEL -->
            <div id="auth-register-panel" style="display: none;">
                <h3 style="font-weight: 800; text-align: center; margin-bottom: 2rem;"><?= __('create_account') ?></h3>
                <form id="register-form" onsubmit="submitAuth(event, 'register')">
                    <div class="form-group">
                        <label><?= __('full_name') ?> *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><?= getLanguage() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' ?> *</label>
                        <input type="email" name="email" class="form-control" placeholder="example@domain.com" required>
                    </div>
                    <div class="form-group">
                        <label><?= __('phone_number') ?></label>
                        <input type="tel" name="phone" class="form-control" placeholder="05XXXXXXXX">
                    </div>
                    <div class="form-group">
                        <label><?= __('password') ?> *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn-submit"><?= __('register') ?></button>
                </form>

                <div style="text-align: center; margin-top: 2rem;">
                    <a href="javascript:void(0)" onclick="switchAuthTab('login')" style="color: var(--primary); font-weight: 700; font-size: 0.9rem;"><?= __('already_have_account') ?></a>
                </div>
            </div>

            <!-- FORGOT PASSWORD PANEL -->
            <div id="auth-forgot-panel" style="display: none;">
                <h3 style="font-weight: 800; text-align: center; margin-bottom: 1.5rem;"><?= __('forgot_password') ?></h3>
                <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; margin-bottom: 1.5rem;">
                    <?= getLanguage() === 'ar' ? 'أدخل بريدك الإلكتروني المسجل وسنقوم بإرسال رابط مخصص لاستعادة كلمة المرور.' : 'Enter your registered email and we will send a password reset link.' ?>
                </p>
                <form id="forgot-form" onsubmit="submitAuth(event, 'forgot')">
                    <div class="form-group">
                        <label><?= getLanguage() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' ?></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn-submit"><?= getLanguage() === 'ar' ? 'إرسال رابط الاستعادة' : 'Send Recovery Link' ?></button>
                </form>

                <div style="text-align: center; margin-top: 2rem;">
                    <a href="javascript:void(0)" onclick="switchAuthTab('login')" style="color: var(--primary); font-weight: 700; font-size: 0.9rem;"><?= getLanguage() === 'ar' ? 'الرجوع لتسجيل الدخول' : 'Back to Login' ?></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Javascript Assets -->
    <script src="assets/js/main.js?v=<?= time() ?>"></script>
    <script src="assets/js/compare.js"></script>
</body>
</html>
