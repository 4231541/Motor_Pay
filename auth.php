<?php
// C:\xampp\htdocs\سيارة\auth.php
require_once __DIR__ . '/database/db.php';
require_once __DIR__ . '/shared/functions.php';

// Log out helper (if accessed directly via auth.php?logout=1)
if (isset($_GET['logout'])) {
 session_destroy();
 header("Location: index.php");
 exit;
}

// Redirect if already logged in
if (isLoggedIn()) {
 header("Location: profile.php");
 exit;
}

$error = '';
$success = '';
$action = $_GET['action'] ?? 'login'; // 'login', 'register', 'forgot'

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
 $email = trim($_POST['email'] ?? '');
 $password = $_POST['password'] ?? '';
 
 if ($email === '' || $password === '') {
 $error = __('fill_required');
 } else {
 $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
 $stmt->execute([$email, $email]);
 $user = $stmt->fetch();
 
 if ($user && password_verify($password, $user['password'])) {
 $_SESSION['user_id'] = $user['id'];
 $_SESSION['user_name'] = $user['name'];
 $_SESSION['user_role'] = $user['role'];
 
 // Redirect or JSON
 if (isset($_POST['ajax'])) {
 echo json_encode(['success' => true, 'redirect' => $user['role'] === 'admin' ? 'backend/index.php' : 'profile.php']);
 exit;
 } else {
 if ($user['role'] === 'admin') {
 header("Location: backend/index.php");
 } else {
 header("Location: profile.php");
 }
 exit;
 }
 } else {
 $error = getLanguage() === 'ar' ? 'البريد الإلكتروني أو كلمة المرور غير صحيحة.' : 'Incorrect email or password.';
 if (isset($_POST['ajax'])) {
 echo json_encode(['success' => false, 'error' => $error]);
 exit;
 }
 }
 }
}

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
 $name = trim($_POST['name'] ?? '');
 $email = trim($_POST['email'] ?? '');
 $phone = trim($_POST['phone'] ?? '');
 $city = trim($_POST['city'] ?? '');
 $password = $_POST['password'] ?? '';
 
 if ($name === '' || $email === '' || $password === '') {
 $error = __('fill_required');
 } else {
 // Check if email exists
 $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
 $stmt->execute([$email]);
 if ($stmt->fetch()) {
 $error = getLanguage() === 'ar' ? 'البريد الإلكتروني مسجل بالفعل.' : 'Email is already registered.';
 } else {
 $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
 $stmtIns = $db->prepare("INSERT INTO users (name, email, phone, city, password, role) VALUES (?, ?, ?, ?, ?, 'user')");
 $stmtIns->execute([$name, $email, $phone, $city, $hashedPassword]);
 
 $success = getLanguage() === 'ar' ? 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.' : 'Account created successfully! You can login now.';
 if (isset($_POST['ajax'])) {
 echo json_encode(['success' => true, 'message' => $success]);
 exit;
 }
 $action = 'login'; // Switch to login screen
 }
 }
 if ($error !== '' && isset($_POST['ajax'])) {
 echo json_encode(['success' => false, 'error' => $error]);
 exit;
 }
}

// Handle Forgot Password Mock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_submit'])) {
 $email = trim($_POST['email'] ?? '');
 if ($email === '') {
 $error = __('fill_required');
 } else {
 $success = getLanguage() === 'ar' 
 ? 'تم إرسال تعليمات استعادة كلمة المرور لبريدك الإلكتروني.' 
 : 'Password recovery instructions have been sent to your email.';
 if (isset($_POST['ajax'])) {
 echo json_encode(['success' => true, 'message' => $success]);
 exit;
 }
 $action = 'login';
 }
 if ($error !== '' && isset($_POST['ajax'])) {
 echo json_encode(['success' => false, 'error' => $error]);
 exit;
 }
}

require_once __DIR__ . '/shared/header.php';
$lang = getLanguage();
?>

<div class="container section-padding">
 
 <div class="auth-container">
 <!-- Error / Success notifications -->
 <?php if ($error !== ''): ?>
 <div style="background-color: rgba(239, 68, 68, 0.15); color: var(--danger); padding: 0.8rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; font-weight: 700; border: 1px solid rgba(239, 68, 68, 0.25);">
 <?= $error ?>
 </div>
 <?php endif; ?>

 <?php if ($success !== ''): ?>
 <div style="background-color: rgba(16, 185, 129, 0.15); color: var(--success); padding: 0.8rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.25);">
 <?= $success ?>
 </div>
 <?php endif; ?>

 <!-- LOGIN PANEL -->
 <?php if ($action === 'login'): ?>
 <h3 style="font-weight: 800; text-align: center; margin-bottom: 2rem;"><?= __('welcome_back') ?></h3>
 <form action="auth.php?action=login" method="POST">
 <div class="form-group">
 <label><?= __('email_or_phone') ?></label>
 <input type="text" name="email" class="form-control" placeholder="user@syarah.com" required>
 </div>
 <div class="form-group">
 <label><?= __('password') ?></label>
 <input type="password" name="password" class="form-control" required>
 <div style="text-align: end; margin-top: 0.5rem;">
 <a href="auth.php?action=forgot" style="font-size: 0.8rem; color: var(--primary); font-weight: 600;"><?= __('forgot_password') ?></a>
 </div>
 </div>
 <button type="submit" name="login_submit" class="btn-submit"><?= __('login') ?></button>
 </form>

 <div style="margin: 2rem 0; text-align: center; color: var(--text-muted); font-size: 0.85rem; position: relative;">
 <span style="background-color: var(--bg-secondary); padding: 0 10px; z-index: 1; position: relative;"><?= __('or_sign_in_with') ?></span>
 <hr style="position: absolute; top: 50%; width: 100%; border: 0; border-top: 1px solid var(--border-color); z-index: 0;">
 </div>

 <!-- OAuth Buttons -->
 <div class="oauth-row">
 <button onclick="alert('Mock Google OAuth Triggered');" class="oauth-btn"> Google</button>
 <button onclick="alert('Mock Apple OAuth Triggered');" class="oauth-btn"> Apple</button>
 </div>

 <div style="text-align: center; margin-top: 2rem;">
 <a href="index.php" style="display: block; margin-bottom: 1rem; color: var(--text-secondary); font-weight: 600; font-size: 0.9rem;"><?= __('guest_login') ?></a>
 <a href="auth.php?action=register" style="color: var(--primary); font-weight: 700; font-size: 0.9rem;"><?= __('dont_have_account') ?></a>
 </div>

 <!-- REGISTRATION PANEL -->
 <?php elseif ($action === 'register'): ?>
 <h3 style="font-weight: 800; text-align: center; margin-bottom: 2rem;"><?= __('create_account') ?></h3>
 <form action="auth.php?action=register" method="POST">
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
 <label><?= __('city_label') ?></label>
 <input type="text" name="city" class="form-control">
 </div>
 <div class="form-group">
 <label><?= __('password') ?> *</label>
 <input type="password" name="password" class="form-control" required>
 </div>
 <button type="submit" name="register_submit" class="btn-submit"><?= __('register') ?></button>
 </form>

 <div style="text-align: center; margin-top: 2rem;">
 <a href="auth.php?action=login" style="color: var(--primary); font-weight: 700; font-size: 0.9rem;"><?= __('already_have_account') ?></a>
 </div>

 <!-- FORGOT PASSWORD PANEL -->
 <?php elseif ($action === 'forgot'): ?>
 <h3 style="font-weight: 800; text-align: center; margin-bottom: 1.5rem;"><?= __('forgot_password') ?></h3>
 <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; margin-bottom: 1.5rem;">
 <?= $lang === 'ar' ? 'أدخل بريدك الإلكتروني المسجل وسنقوم بإرسال رابط مخصص لاستعادة كلمة المرور.' : 'Enter your registered email and we will send a password reset link.' ?>
 </p>
 <form action="auth.php?action=forgot" method="POST">
 <div class="form-group">
 <label><?= getLanguage() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' ?></label>
 <input type="email" name="email" class="form-control" required>
 </div>
 <button type="submit" name="forgot_submit" class="btn-submit"><?= getLanguage() === 'ar' ? 'إرسال رابط الاستعادة' : 'Send Recovery Link' ?></button>
 </form>

 <div style="text-align: center; margin-top: 2rem;">
 <a href="auth.php?action=login" style="color: var(--primary); font-weight: 700; font-size: 0.9rem;"><?= $lang === 'ar' ? 'الرجوع لتسجيل الدخول' : 'Back to Login' ?></a>
 </div>
 <?php endif; ?>
 </div>
</div>

<?php
require_once __DIR__ . '/shared/footer.php';
?>
