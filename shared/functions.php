<?php
// C:\xampp\htdocs\سيارة\includes\functions.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Language setting
if (isset($_GET['lang'])) {
    if ($_GET['lang'] === 'en' || $_GET['lang'] === 'ar') {
        $_SESSION['lang'] = $_GET['lang'];
    }
}
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'ar'; // Default language is Arabic
}

// Translations Library
$translations = [
    'ar' => [
        'app_name' => 'Motor Pay',
        'app_slogan' => 'بوابتك الذكية لشراء سيارتك الجديدة بالتقسيط',
        'home' => 'الرئيسية',
        'search' => 'البحث المتقدم',
        'offers' => 'العروض المميزة',
        'compare' => 'مقارنة السيارات',
        'favorites' => 'المفضلة',
        'profile' => 'الملف الشخصي',
        'my_requests' => 'طلباتي',
        'admin_panel' => 'لوحة الإدارة',
        'login' => 'تسجيل الدخول',
        'register' => 'إنشاء حساب',
        'logout' => 'تسجيل الخروج',
        'search_placeholder' => 'ابحث باسم السيارة أو الماركة...',
        'new_cars' => 'السيارات الجديدة',
        'installment_offers' => 'عروض التقسيط',
        'latest_cars' => 'أحدث السيارات',
        'most_viewed' => 'الأكثر مشاهدة',
        'most_requested' => 'الأكثر طلباً',
        'brands' => 'الماركات التجارية',
        'currency' => 'ريال',
        'month' => 'شهر',
        'installment_starts' => 'يبدأ التقسيط من',
        'view_details' => 'عرض التفاصيل',
        'book_now' => 'احجزها الآن',
        'test_drive' => 'حجز تجربة قيادة',
        'call_back' => 'طلب معاودة الاتصال',
        'year' => 'سنة الصنع',
        'price' => 'السعر النقدي',
        'specs' => 'المواصفات والتفاصيل',
        'technical_specs' => 'المواصفات الفنية',
        'safety_specs' => 'الأمان والسلامة',
        'comfort_specs' => 'وسائل الراحة',
        'tech_specs' => 'التقنيات والاتصال',
        'exterior_specs' => 'التجهيزات الخارجية',
        'installment_calculator' => 'حاسبة التقسيط التفاعلية',
        'downpayment' => 'قيمة المقدم (الدفعة الأولى)',
        'duration' => 'مدة السداد (بالأشهر)',
        'monthly_installment' => 'قسط شهري متوقع',
        'total_finance' => 'إجمالي التمويل',
        'similar_cars' => 'سيارات مشابهة قد تعجبك',
        'brand' => 'الماركة',
        'model' => 'الموديل',
        'grade' => 'الفئة',
        'fuel' => 'نوع الوقود',
        'transmission' => 'نوع القير',
        'drive' => 'نظام الدفع',
        'color_ext' => 'اللون الخارجي',
        'color_int' => 'اللون الداخلي',
        'engine' => 'حجم المحرك',
        'seats' => 'عدد المقاعد',
        'doors' => 'عدد الأبواب',
        'status' => 'الحالة',
        'available' => 'متوفر',
        'unavailable' => 'غير متوفر',
        'new_car_status' => 'جديدة 100%',
        'onboarding_title_1' => 'شراء سيارات جديدة بسهولة',
        'onboarding_desc_1' => 'تصفح تشكيلة واسعة من أحدث موديلات السيارات ووكلائها المعتمدين في المملكة.',
        'onboarding_title_2' => 'التقسيط بأفضل العروض',
        'onboarding_desc_2' => 'احسب قسطك المناسب وقدم طلب تمويل مباشر مع كبرى الجهات التمويلية والبنوك.',
        'onboarding_title_3' => 'احجز سيارتك أونلاين',
        'onboarding_desc_3' => 'أتمم حجزك بلمسة زر واحدة وسنتولى كافة المعاملات الورقية وتسليم السيارة لباب بيتك.',
        'start_now' => 'ابدأ الآن',
        'skip' => 'تخطي',
        'next' => 'التالي',
        'contact_us' => 'اتصل بنا',
        'about_us' => 'من نحن',
        'faqs' => 'الأسئلة الشائعة',
        'settings' => 'الإعدادات',
        'theme_mode' => 'الوضع الليلي / النهاري',
        'language' => 'اللغة',
        'notifications' => 'الإشعارات',
        'privacy_policy' => 'سياسة الخصوصية',
        'terms' => 'الشروط والأحكام',
        'compare_limit' => 'يمكنك مقارنة حتى 3 سيارات فقط.',
        'compare_empty' => 'لم تقم بإضافة سيارات للمقارنة بعد.',
        'favorites_empty' => 'قائمة المفضلة فارغة حالياً.',
        'requests_empty' => 'ليس لديك طلبات سابقة.',
        'status_received' => 'تم استلام الطلب',
        'status_reviewing' => 'مراجعة البيانات',
        'status_contacting' => 'جاري التواصل',
        'status_booked' => 'تم الحجز بنجاح',
        'status_delivered' => 'تم التسليم والحمد لله',
        'status_rejected' => 'مرفوض',
        'no_notifications' => 'لا توجد إشعارات جديدة حالياً.',
        'welcome_back' => 'أهلاً بك مجدداً',
        'email_or_phone' => 'البريد الإلكتروني أو رقم الهاتف',
        'password' => 'كلمة المرور',
        'forgot_password' => 'نسيت كلمة المرور؟',
        'or_sign_in_with' => 'أو سجل الدخول بواسطة',
        'guest_login' => 'تخطي والدخول كزائر',
        'dont_have_account' => 'ليس لديك حساب؟ سجل الآن',
        'create_account' => 'إنشاء حساب جديد',
        'full_name' => 'الاسم الكامل',
        'phone_number' => 'رقم الهاتف',
        'city_label' => 'المدينة',
        'already_have_account' => 'لديك حساب بالفعل؟ تسجيل الدخول',
        'calc_btn' => 'احسب القسط',
        'booking_title' => 'حجز سيارة جديدة أونلاين',
        'installment_title' => 'طلب تقسيط وتمويل سيارة',
        'payment_method_label' => 'طريقة الدفع المفضلة',
        'payment_cash' => 'كاش (تحويل بنكي)',
        'payment_card' => 'مدى / فيزا / ماستركارد',
        'payment_installment' => 'طلب تقسيط تمويلي',
        'notes_label' => 'ملاحظات إضافية',
        'national_id_label' => 'رقم الهوية الوطنية / الإقامة',
        'salary_label' => 'الراتب الشهري (ريال)',
        'employer_label' => 'جهة العمل',
        'work_duration_label' => 'مدة الخدمة بالسنوات',
        'send_request' => 'إرسال الطلب الآن',
        'request_success' => 'تم إرسال طلبك بنجاح! سيتم التواصل معك قريباً.',
        'fill_required' => 'الرجاء ملء جميع الحقول المطلوبة.',
        'test_drive_title' => 'طلب تجربة قيادة',
        'callback_title' => 'طلب معاودة الاتصال',
        'similar_specs' => 'مواصفات مطابقة للبحث',
        'brand_select' => 'اختر الماركة',
        'model_select' => 'اختر الموديل',
        'fuel_select' => 'نوع الوقود',
        'trans_select' => 'نوع ناقل الحركة',
        'price_range' => 'نطاق السعر (ريال)',
        'filter_btn' => 'تطبيق الفلاتر',
        'reset_btn' => 'إعادة ضبط',
        'view_all' => 'عرض الكل',
        'compare_specs' => 'مقارنة المواصفات الفنية والتقنية'
    ],
    'en' => [
        'app_name' => 'Motor Pay',
        'app_slogan' => 'Your smart gateway to new cars on installment',
        'home' => 'Home',
        'search' => 'Advanced Search',
        'offers' => 'Special Offers',
        'compare' => 'Compare Cars',
        'favorites' => 'Favorites',
        'profile' => 'Profile',
        'my_requests' => 'My Requests',
        'admin_panel' => 'Admin Panel',
        'login' => 'Login',
        'register' => 'Register',
        'logout' => 'Logout',
        'search_placeholder' => 'Search by car name or brand...',
        'new_cars' => 'New Cars',
        'installment_offers' => 'Installment Offers',
        'latest_cars' => 'Latest Cars',
        'most_viewed' => 'Most Viewed',
        'most_requested' => 'Most Requested',
        'brands' => 'Brands',
        'currency' => 'SAR',
        'month' => 'month',
        'installment_starts' => 'Installment starts from',
        'view_details' => 'View Details',
        'book_now' => 'Book Now',
        'test_drive' => 'Book Test Drive',
        'call_back' => 'Request Callback',
        'year' => 'Year',
        'price' => 'Cash Price',
        'specs' => 'Specifications & Details',
        'technical_specs' => 'Technical Specs',
        'safety_specs' => 'Safety & Driver Assist',
        'comfort_specs' => 'Comfort & Luxury',
        'tech_specs' => 'Tech & Multimedia',
        'exterior_specs' => 'Exterior Features',
        'installment_calculator' => 'Installment Calculator',
        'downpayment' => 'Down Payment',
        'duration' => 'Duration (Months)',
        'monthly_installment' => 'Expected Monthly Payment',
        'total_finance' => 'Total Financing',
        'similar_cars' => 'Similar Cars You May Like',
        'brand' => 'Brand',
        'model' => 'Model',
        'grade' => 'Grade',
        'fuel' => 'Fuel Type',
        'transmission' => 'Transmission',
        'drive' => 'Drivetrain',
        'color_ext' => 'Exterior Color',
        'color_int' => 'Interior Color',
        'engine' => 'Engine Size',
        'seats' => 'Seats',
        'doors' => 'Doors',
        'status' => 'Status',
        'available' => 'Available',
        'unavailable' => 'Out of Stock',
        'new_car_status' => '100% Brand New',
        'onboarding_title_1' => 'Buy New Cars Easily',
        'onboarding_desc_1' => 'Browse a wide collection of the latest car models from authorized local dealers.',
        'onboarding_title_2' => 'Installments & Financing',
        'onboarding_desc_2' => 'Calculate your budget and apply for direct financing from major banks and financing firms.',
        'onboarding_title_3' => 'Book Online Instantly',
        'onboarding_desc_3' => 'Complete your booking at the tap of a button, and we will handle papers and deliver to your door.',
        'start_now' => 'Get Started',
        'skip' => 'Skip',
        'next' => 'Next',
        'contact_us' => 'Contact Us',
        'about_us' => 'About Us',
        'faqs' => 'FAQs',
        'settings' => 'Settings',
        'theme_mode' => 'Dark / Light Mode',
        'language' => 'Language',
        'notifications' => 'Notifications',
        'privacy_policy' => 'Privacy Policy',
        'terms' => 'Terms & Conditions',
        'compare_limit' => 'You can compare up to 3 cars only.',
        'compare_empty' => 'No cars selected for comparison.',
        'favorites_empty' => 'Your favorite list is empty.',
        'requests_empty' => 'You have no active or past requests.',
        'status_received' => 'Request Received',
        'status_reviewing' => 'Document Review',
        'status_contacting' => 'In Contact',
        'status_booked' => 'Car Reserved',
        'status_delivered' => 'Car Delivered',
        'status_rejected' => 'Rejected',
        'no_notifications' => 'You have no new notifications.',
        'welcome_back' => 'Welcome Back',
        'email_or_phone' => 'Email or Phone Number',
        'password' => 'Password',
        'forgot_password' => 'Forgot Password?',
        'or_sign_in_with' => 'Or Sign In with',
        'guest_login' => 'Skip & Enter as Guest',
        'dont_have_account' => "Don't have an account? Sign Up",
        'create_account' => 'Create New Account',
        'full_name' => 'Full Name',
        'phone_number' => 'Phone Number',
        'city_label' => 'City',
        'already_have_account' => 'Already have an account? Login',
        'calc_btn' => 'Calculate Payment',
        'booking_title' => 'Book New Car Online',
        'installment_title' => 'Apply for Financing / Installments',
        'payment_method_label' => 'Preferred Payment Method',
        'payment_cash' => 'Cash (Bank Transfer)',
        'payment_card' => 'Mada / Visa / MasterCard',
        'payment_installment' => 'Finance / Installment Request',
        'notes_label' => 'Additional Notes',
        'national_id_label' => 'National ID / Iqama Number',
        'salary_label' => 'Monthly Salary (SAR)',
        'employer_label' => 'Employer / Organization',
        'work_duration_label' => 'Service Duration (Years)',
        'send_request' => 'Submit Request Now',
        'request_success' => 'Your request was sent successfully! We will contact you shortly.',
        'fill_required' => 'Please fill out all required fields.',
        'test_drive_title' => 'Book a Test Drive',
        'callback_title' => 'Request a Callback',
        'similar_specs' => 'Matching Specifications',
        'brand_select' => 'Select Brand',
        'model_select' => 'Select Model',
        'fuel_select' => 'Fuel Type',
        'trans_select' => 'Transmission Type',
        'price_range' => 'Price Range (SAR)',
        'filter_btn' => 'Apply Filters',
        'reset_btn' => 'Reset All',
        'view_all' => 'View All',
        'compare_specs' => 'Compare Car Specs'
    ]
];

// Translate function
function __($key) {
    global $translations;
    $lang = getLanguage();
    if (isset($translations[$lang][$key])) {
        return $translations[$lang][$key];
    }
    return $key;
}

// Current language helper
function getLanguage() {
    return isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ar';
}

// Text direction helper
function getDirection() {
    return getLanguage() === 'ar' ? 'rtl' : 'ltr';
}

// Check logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Get user data
function getCurrentUser($db) {
    if (!isLoggedIn()) return null;
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Clean string output
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Formats prices
function formatPrice($price) {
    $lang = getLanguage();
    if ($lang === 'ar') {
        return number_format($price) . ' ' . __('currency');
    } else {
        return __('currency') . ' ' . number_format($price);
    }
}

// Image upload helper
function uploadImage($file, $destinationDir) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['error' => 'Invalid parameters.'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error code: ' . $file['error']];
    }
    if ($file['size'] > 5000000) { // 5MB limit
        return ['error' => 'File size exceeded.'];
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'text/xml', 'text/plain'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Strict MIME check for everything except SVG (which can have weird mimes depending on server setup)
    if (!in_array($mime, $allowedMimes, true) && $ext !== 'svg') {
        return ['error' => 'Invalid file format. Only JPG, PNG, GIF, WEBP, SVG allowed. (Mime detected: ' . $mime . ')'];
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = uniqid('img_', true) . '.' . strtolower($ext);
    
    // Ensure destination dir exists
    $targetDir = __DIR__ . '/../' . $destinationDir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }
    
    $targetPath = $targetDir . '/' . $newName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['error' => 'Failed to move uploaded file.'];
    }
    
    // Return relative path from root
    return ['success' => true, 'path' => $destinationDir . '/' . $newName];
}
