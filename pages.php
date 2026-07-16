<?php
// C:\xampp\htdocs\سيارة\pages.php
require_once __DIR__ . '/shared/header.php';

$page = $_GET['page'] ?? 'about'; // 'about', 'faq', 'contact', 'privacy'
$lang = getLanguage();
?>

<div class="container section-padding">

 <!-- 1. ABOUT US PAGE -->
 <?php if ($page === 'about'): ?>
 <div style="max-width: 800px; margin: 0 auto;">
 <div class="section-title text-center" style="margin-bottom: 3rem;">
 <h2><?= __('about_us') ?></h2>
 </div>
 
 <div style="background-color: var(--bg-secondary); border-radius: 24px; padding: 3rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow); font-size: 1.1rem; color: var(--text-secondary);">
 <h3 style="font-weight: 800; color: var(--text-primary); margin-bottom: 1.5rem;">
 <?= $lang === 'ar' ? 'من نحن - Motor Pay' : 'About Us - Motor Pay' ?>
 </h3>
 <p style="margin-bottom: 1.5rem; line-height: 1.8;">
 <?= $lang === 'ar' 
 ? 'Motor Pay هي المنصة الرقمية الرائدة في المملكة العربية السعودية لشراء وحجز السيارات الجديدة كلياً بالتقسيط التمويلي الميسر. نحن نسهل على العميل رحلة تملك السيارة بضغطة زر واحدة بالتعاون مع شبكة شركائنا من وكلاء السيارات والمصارف والشركات التمويلية.' 
 : 'Motor Pay is the leading digital automotive platform in Saudi Arabia dedicated to purchasing brand new cars with flexible financing and installment programs. We make the car ownership journey fully digital in partnership with auto dealers and financial institutions.' ?>

 </p>
 
 <h4 style="font-weight: 800; color: var(--text-primary); margin-top: 2rem; margin-bottom: 1rem;"><?= $lang === 'ar' ? 'رؤيتنا' : 'Our Vision' ?></h4>
 <p style="margin-bottom: 1.5rem; line-height: 1.8;">
 <?= $lang === 'ar' 
 ? 'أن نكون الخيار الأول والوجهة الأكثر موثوقية لكل من يبحث عن حلول تمويل وتقسيط سيارة في الشرق الأوسط، من خلال تقديم تجربة رقمية آمنة وذكية.' 
 : 'To be the first choice and most trusted ecosystem for anyone seeking vehicle financing and installments in the Middle East by delivering a secure and smart digital experience.' ?>
 </p>

 <h4 style="font-weight: 800; color: var(--text-primary); margin-top: 2rem; margin-bottom: 1rem;"><?= $lang === 'ar' ? 'رسالتنا' : 'Our Mission' ?></h4>
 <p style="line-height: 1.8;">
 <?= $lang === 'ar' 
 ? 'توفير خيارات تمويلية متعددة تتناسب مع جميع شرائح المجتمع بمعدلات ربح تنافسية، وتبسيط الإجراءات الورقية التقليدية وتسريع استلام السيارة لباب العميل.' 
 : 'Providing various financing packages that suit all user demographics at competitive profit margins, streamlining paperwork, and delivering the vehicles straight to the user\'s doorstep.' ?>
 </p>
 </div>
 </div>

 <!-- 2. FAQ PAGE (Accordions) -->
 <?php elseif ($page === 'faq'): ?>
 <div style="max-width: 800px; margin: 0 auto;">
 <div class="section-title text-center" style="margin-bottom: 3rem;">
 <h2><?= __('faqs') ?></h2>
 </div>

 <div style="display: flex; flex-direction: column; gap: 1.25rem;">
 <!-- Q1 -->
 <details style="background-color: var(--bg-secondary); border-radius: 16px; border: 1px solid var(--border-color); padding: 1.25rem; box-shadow: var(--card-shadow); cursor: pointer;" open>
 <summary style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary); outline: none; list-style: none;">
 <?= $lang === 'ar' ? 'كيف يمكنني حجز سيارة من خلال المنصة؟' : 'How can I book a car on the portal?' ?>
 </summary>
 <p style="margin-top: 1rem; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; border-top: 1px solid var(--border-color); padding-top: 1rem;">
 <?= $lang === 'ar' 
 ? 'يمكنك حجز السيارة من خلال اختيار سيارة أحلامك، والضغط على زر "احجزها الآن"، ثم ملء بيانات التواصل واختيار طريقة الدفع المفضلة (كاش أو تقسيط). سيقوم مستشارو المبيعات لدينا بالاتصال بك فوراً لإتمام المعاملة.' 
 : 'You can book a car by navigating to your desired model, clicking the "Book Now" button, and filling out your details while selecting your preferred payment route (Cash or installment). A sales representative will contact you immediately.' ?>
 </p>
 </details>

 <!-- Q2 -->
 <details style="background-color: var(--bg-secondary); border-radius: 16px; border: 1px solid var(--border-color); padding: 1.25rem; box-shadow: var(--card-shadow); cursor: pointer;">
 <summary style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary); outline: none; list-style: none;">
 <?= $lang === 'ar' ? 'ما هي المستندات المطلوبة لطلب التقسيط؟' : 'What documents are required for installments?' ?>
 </summary>
 <p style="margin-top: 1rem; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; border-top: 1px solid var(--border-color); padding-top: 1rem;">
 <?= $lang === 'ar' 
 ? 'المستندات الأساسية المطلوبة تشمل: صورة الهوية الوطنية أو الإقامة سارية المفعول، تعريف بالراتب مصدق من جهة العمل، كشف حساب بنكي لآخر 3 أشهر، ورخصة قيادة سارية المفعول.' 
 : 'The primary files requested include: a valid National ID or Iqama, an official salary certificate certified by your employer, a bank statement copy for the last 3 months, and a valid driver\'s license.' ?>
 </p>
 </details>

 <!-- Q3 -->
 <details style="background-color: var(--bg-secondary); border-radius: 16px; border: 1px solid var(--border-color); padding: 1.25rem; box-shadow: var(--card-shadow); cursor: pointer;">
 <summary style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary); outline: none; list-style: none;">
 <?= $lang === 'ar' ? 'هل تتوفر كفالة وضمان على السيارات المباعة؟' : 'Is there a warranty on sold vehicles?' ?>
 </summary>
 <p style="margin-top: 1rem; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; border-top: 1px solid var(--border-color); padding-top: 1rem;">
 <?= $lang === 'ar' 
 ? 'نعم، جميع السيارات المعروضة في منصتنا هي سيارات جديدة 100% وتخضع لضمان المصنع والوكيل الرسمي في المملكة العربية السعودية لمدة لا تقل عن 3 إلى 5 سنوات أو 100,000 كم (أيهما أسبق).' 
 : 'Yes! All vehicles listed on our portal are 100% brand new and carry full manufacturer warranties through authorized dealers in KSA for 3 to 5 years or 100,000 KM (whichever comes first).' ?>
 </p>
 </details>

 <!-- Q4 -->
 <details style="background-color: var(--bg-secondary); border-radius: 16px; border: 1px solid var(--border-color); padding: 1.25rem; box-shadow: var(--card-shadow); cursor: pointer;">
 <summary style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary); outline: none; list-style: none;">
 <?= $lang === 'ar' ? 'كيف يتم تسليم السيارة؟' : 'How is the car delivered?' ?>
 </summary>
 <p style="margin-top: 1rem; color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; border-top: 1px solid var(--border-color); padding-top: 1rem;">
 <?= $lang === 'ar' 
 ? 'بعد إتمام إجراءات الترخيص والتمويل والتأمين بنجاح، يتم تحميل السيارة على ناقلات مغلقة وتسليمها مباشرة لباب منزلك في أي مدينة بالمملكة.' 
 : 'After licensing, insurance, and financing clearance, we dispatch the vehicle on enclosed transport trucks directly to your doorstep anywhere in KSA.' ?>
 </p>
 </details>
 </div>
 </div>

 <!-- 3. CONTACT US PAGE -->
 <?php elseif ($page === 'contact'): ?>
 <div class="section-title text-center" style="margin-bottom: 3rem;">
 <h2><?= __('contact_us') ?></h2>
 </div>

 <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start;">
 <!-- Contacts Details & Map -->
 <div style="background-color: var(--bg-secondary); border-radius: 24px; padding: 2.5rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow);">
 <h3 style="font-weight: 800; margin-bottom: 1.5rem;"><?= $lang === 'ar' ? 'معلومات الاتصال المباشر' : 'Direct Contacts' ?></h3>
 
 <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2rem;">
 <div>
 <strong style="display: block; font-size: 0.85rem; color: var(--text-muted);"><?= $lang === 'ar' ? 'رقم الهاتف الموحد' : 'Unified Hotline' ?></strong>
 <span style="font-size: 1.1rem; font-weight: 700; color: var(--primary);">920001234</span>
 </div>
 <div>
 <strong style="display: block; font-size: 0.85rem; color: var(--text-muted);"><?= $lang === 'ar' ? 'البريد الإلكتروني للدعم' : 'Support Email' ?></strong>
 <span style="font-size: 1.1rem; font-weight: 700;">support@syarahplus.com</span>
 </div>
 <div>
 <strong style="display: block; font-size: 0.85rem; color: var(--text-muted);">WhatsApp</strong>
 <span style="font-size: 1.1rem; font-weight: 700; color: var(--success);">+966 50 123 4567</span>
 </div>
 <div>
 <strong style="display: block; font-size: 0.85rem; color: var(--text-muted);"><?= $lang === 'ar' ? 'المقر الرئيسي' : 'Headquarters' ?></strong>
 <span><?= $lang === 'ar' ? 'طريق الملك فهد، حي الصحافة، الرياض، المملكة العربية السعودية' : 'King Fahd Road, Al Sahafah, Riyadh, Saudi Arabia' ?></span>
 </div>
 </div>

 <!-- Mock Map visual design -->
 <div style="background: linear-gradient(135deg, #e2e8f0, #cbd5e1); [data-theme='dark'] & { background: linear-gradient(135deg, #374151, #1f2937); } height: 180px; border-radius: 16px; display: flex; justify-content: center; align-items: center; color: var(--text-primary); font-weight: bold; border: 1px solid var(--border-color);">
 <?= $lang === 'ar' ? 'خريطة الموقع التفاعلية' : 'Interactive Location Map' ?>
 </div>
 </div>

 <!-- Inquiry Submission form -->
 <div style="background-color: var(--bg-secondary); border-radius: 24px; padding: 2.5rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow);">
 <h3 style="font-weight: 800; margin-bottom: 1.5rem;"><?= $lang === 'ar' ? 'أرسل لنا رسالة' : 'Send us an inquiry' ?></h3>
 <form onsubmit="event.preventDefault(); alert('<?= $lang === 'ar' ? 'تم إرسال رسالتك بنجاح! شكراً لك.' : 'Your message has been sent successfully! Thank you.' ?>'); this.reset();">
 <div class="form-group">
 <label><?= __('full_name') ?> *</label>
 <input type="text" class="form-control" required>
 </div>
 <div class="form-group">
 <label><?= getLanguage() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' ?> *</label>
 <input type="email" class="form-control" required>
 </div>
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'موضوع الرسالة' : 'Subject' ?> *</label>
 <input type="text" class="form-control" required>
 </div>
 <div class="form-group">
 <label><?= $lang === 'ar' ? 'مضمون الرسالة' : 'Message details' ?> *</label>
 <textarea class="form-control" rows="4" required></textarea>
 </div>
 <button type="submit" class="btn-submit"><?= $lang === 'ar' ? 'إرسال الرسالة' : 'Submit Message' ?></button>
 </form>
 </div>
 </div>

 <!-- 4. PRIVACY POLICY PAGE -->
 <?php elseif ($page === 'privacy'): ?>
 <div style="max-width: 800px; margin: 0 auto;">
 <div class="section-title text-center" style="margin-bottom: 3rem;">
 <h2><?= __('privacy_policy') ?></h2>
 </div>

 <div style="background-color: var(--bg-secondary); border-radius: 24px; padding: 3rem; border: 1px solid var(--border-color); box-shadow: var(--card-shadow); font-size: 1rem; color: var(--text-secondary); line-height: 1.8;">
 <p style="margin-bottom: 1.5rem;">
 <?= $lang === 'ar' 
 ? 'في منصة Motor Pay، نلتزم بحماية خصوصية بيانات عملائنا. توضح هذه السياسة كيف نقوم بجمع واستخدام ومشاركة وحفظ معلوماتك عند زيارة واستخدام خدمات المنصة.' 
 : 'At Motor Pay, we are committed to safeguarding our clients\' privacy. This policy details how we collect, store, share, and utilize your personal data upon browsing our platform.' ?>
 </p>
 <h4 style="font-weight: 800; color: var(--text-primary); margin-top: 2rem; margin-bottom: 1rem;"><?= $lang === 'ar' ? 'جمع البيانات الشخصية' : 'Data Collection' ?></h4>
 <p style="margin-bottom: 1.5rem;">
 <?= $lang === 'ar' 
 ? 'نقوم بجمع البيانات التي تزودنا بها طواعية كأجزاء من النماذج والطلبات، ومنها الاسم ورقم الجوال والبريد الإلكتروني والهوية ومعلومات الدخل الشهري وجهة العمل، وذلك لأغراض تقييم طلبات التمويل والتواصل مع الجهات التمويلية.' 
 : 'We collect data you voluntarily supply via application forms, including full name, phone number, email, national ID, and financing details (salary, employer) to coordinate and evaluate loan criteria with banks.' ?>
 </p>
 <h4 style="font-weight: 800; color: var(--text-primary); margin-top: 2rem; margin-bottom: 1rem;"><?= $lang === 'ar' ? 'مشاركة البيانات' : 'Data Sharing' ?></h4>
 <p>
 <?= $lang === 'ar' 
 ? 'نحن لا نبيع بياناتك الشخصية لأي أطراف ثالثة. تتم مشاركة بيانات التمويل والحجز حصراً مع المصارف والشركات التمويلية المعتمدة ووكلائنا المرخصين لغايات دراسة طلبك وإتمام حجز السيارة فحسب.' 
 : 'We do not sell your personal data. Finance inputs are strictly shared with accredited financial institutions and registered auto dealerships to verify credit ratings and clear vehicle reserving protocols.' ?>
 </p>
 </div>
 </div>
 <?php endif; ?>

</div>

<style>
details summary::-webkit-details-marker {
 display: none;
}
</style>

<?php
require_once __DIR__ . '/shared/footer.php';
?>
