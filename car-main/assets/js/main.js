// C:\xampp\htdocs\ط³ظٹط§ط±ط©\assets\js\main.js

document.addEventListener('DOMContentLoaded', () => {
    // 2. Onboarding Slider variables
    const onboarding = document.getElementById('onboarding-overlay');
    const slides = document.querySelectorAll('.onboarding-slide');
    const dots = document.querySelectorAll('.onboarding-dot');
    const nextBtn = document.querySelector('.btn-next');
    const startBtn = document.querySelector('.btn-start');
    const skipBtn = document.querySelector('.btn-skip');
    let currentSlide = 0;

    // 1. Splash Screen
    const splash = document.getElementById('splash-screen');
    if (splash) {
        setTimeout(() => {
            splash.classList.add('fade-out');
            setTimeout(() => {
                splash.remove();
                checkOnboarding();
            }, 500);
        }, 1500);
    } else {
        checkOnboarding();
    }

    function checkOnboarding() {
        if (onboarding && !localStorage.getItem('onboarding_done')) {
            onboarding.classList.add('active');
        }
    }

    function showSlide(index) {
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        currentSlide = index;

        if (index === slides.length - 1) {
            if (nextBtn) nextBtn.style.display = 'none';
            if (startBtn) startBtn.style.display = 'block';
            if (skipBtn) skipBtn.style.display = 'none';
        } else {
            if (nextBtn) nextBtn.style.display = 'block';
            if (startBtn) startBtn.style.display = 'none';
            if (skipBtn) skipBtn.style.display = 'block';
        }
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentSlide < slides.length - 1) {
                showSlide(currentSlide + 1);
            }
        });
    }

    if (skipBtn) {
        skipBtn.addEventListener('click', closeOnboarding);
    }

    if (startBtn) {
        startBtn.addEventListener('click', closeOnboarding);
    }

    dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => showSlide(idx));
    });

    function closeOnboarding() {
        if (onboarding) {
            onboarding.classList.remove('active');
            localStorage.setItem('onboarding_done', 'true');
        }
    }

    // 3. Theme Toggle (Dark / Light)
    const themeBtn = document.getElementById('theme-toggle');
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon(currentTheme);

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme');
            let newTheme = theme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    function updateThemeIcon(theme) {
        const moonIcon = document.getElementById('icon-moon');
        const sunIcon  = document.getElementById('icon-sun');
        if (!moonIcon || !sunIcon) return;
        if (theme === 'dark') {
            moonIcon.style.display = 'none';
            sunIcon.style.display  = 'block';
        } else {
            moonIcon.style.display = 'block';
            sunIcon.style.display  = 'none';
        }
    }

    // 4. Mobile Nav Menu Drawer Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (navMenu && navMenu.classList.contains('active') && !navMenu.contains(e.target) && e.target !== menuToggle) {
            navMenu.classList.remove('active');
        }
    });

    // 5. Language Switcher
    window.switchLanguage = function(lang) {
        const url = new URL(window.location.href);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    }

    // 6. Generic Modal Helper (Booking / Installments / Test Drive / Callbacks)
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Close modal on background click
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(m => {
        m.addEventListener('click', (e) => {
            if (e.target === m) {
                closeModal(m.id);
            }
        });
    });

    // 7. Dynamic Installment Calculator calculations
    const downpaymentSlider = document.getElementById('downpayment-slider');
    const downpaymentInput = document.getElementById('downpayment-value');
    const termSelect = document.getElementById('term-select');
    
    if (downpaymentSlider && downpaymentInput && termSelect) {
        const carPrice = parseFloat(document.getElementById('car-price-raw').value);
        
        downpaymentSlider.addEventListener('input', (e) => {
            downpaymentInput.value = e.target.value;
            calculateInstallments(carPrice, parseFloat(e.target.value), parseInt(termSelect.value));
        });

        downpaymentInput.addEventListener('change', (e) => {
            let val = parseFloat(e.target.value);
            if (val < 0) val = 0;
            if (val > carPrice * 0.9) val = carPrice * 0.9;
            downpaymentSlider.value = val;
            downpaymentInput.value = val;
            calculateInstallments(carPrice, val, parseInt(termSelect.value));
        });

        termSelect.addEventListener('change', (e) => {
            calculateInstallments(carPrice, parseFloat(downpaymentInput.value), parseInt(e.target.value));
        });

        // Run initial calculation
        calculateInstallments(carPrice, parseFloat(downpaymentInput.value), parseInt(termSelect.value));
    }

    function calculateInstallments(price, downpayment, months) {
        const principal = price - downpayment;
        const flatRateAnnual = 0.035; // 3.5% flat interest rate annually
        const years = months / 12;
        const totalInterest = principal * flatRateAnnual * years;
        const totalFinance = principal + totalInterest;
        const monthlyInstallment = Math.round(totalFinance / months);

        const monthlyValEl = document.getElementById('calc-monthly-val');
        const totalValEl = document.getElementById('calc-total-val');
        
        if (monthlyValEl) {
            monthlyValEl.innerText = formatNumber(monthlyInstallment);
        }
        if (totalValEl) {
            totalValEl.innerText = formatNumber(Math.round(totalFinance));
        }

        // Auto-update request form inputs if they exist in the DOM
        const formDownpayment = document.getElementById('req-downpayment');
        const formTerm = document.getElementById('req-term');
        const formMonthly = document.getElementById('req-monthly');
        
        if (formDownpayment) formDownpayment.value = downpayment;
        if (formTerm) formTerm.value = months;
        if (formMonthly) formMonthly.value = monthlyInstallment;
    }

    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    // 8. Favorites Actions
    const favButtons = document.querySelectorAll('.fav-btn');
    favButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const carId = btn.dataset.carId;
            toggleFavorite(carId, btn);
        });
    });

    function toggleFavorite(carId, btnElement) {
        fetch('api.php?action=toggle_favorite&car_id=' + carId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.action === 'added') {
                        btnElement.classList.add('active');
                    } else {
                        btnElement.classList.remove('active');
                    }
                    updateFavoriteBadge();
                } else if (data.status === 'unauthorized') {
                    // Save to localStorage if guest
                    let guestFavs = JSON.parse(localStorage.getItem('guest_favorites') || '[]');
                    const idx = guestFavs.indexOf(carId);
                    if (idx === -1) {
                        guestFavs.push(carId);
                        btnElement.classList.add('active');
                    } else {
                        guestFavs.splice(idx, 1);
                        btnElement.classList.remove('active');
                    }
                    localStorage.setItem('guest_favorites', JSON.stringify(guestFavs));
                    updateFavoriteBadge();
                }
            })
            .catch(err => console.error("Favorite Error:", err));
    }

    function updateFavoriteBadge() {
        const badge = document.getElementById('fav-badge');
        if (!badge) return;
        
        fetch('api.php?action=get_favorites_count')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    badge.innerText = data.count;
                    badge.style.display = data.count > 0 ? 'flex' : 'none';
                } else {
                    // Guest favorites count
                    let guestFavs = JSON.parse(localStorage.getItem('guest_favorites') || '[]');
                    badge.innerText = guestFavs.length;
                    badge.style.display = guestFavs.length > 0 ? 'flex' : 'none';
                }
            });
    }
    
    // Sync guest favorites on page load if logged in
    function syncFavorites() {
        let guestFavs = JSON.parse(localStorage.getItem('guest_favorites') || '[]');
        if (guestFavs.length > 0) {
            fetch('api.php?action=sync_favorites', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ car_ids: guestFavs })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    localStorage.removeItem('guest_favorites');
                    updateFavoriteBadge();
                }
            });
        } else {
            updateFavoriteBadge();
        }
    }

    syncFavorites();

    // 9. Fullscreen Image Gallery Previewer
    const galleryMain = document.getElementById('gallery-main-view');
    const zoomOverlay = document.getElementById('fullscreen-gallery-overlay');
    const zoomImg = document.getElementById('fullscreen-image-element');
    
    if (galleryMain && zoomOverlay && zoomImg) {
        galleryMain.addEventListener('click', () => {
            const imgStyle = galleryMain.style.backgroundImage;
            const imgUrl = imgStyle.slice(4, -1).replace(/"/g, "");
            zoomImg.src = imgUrl;
            openModal('fullscreen-gallery-overlay');
        });
    }

    const thumbs = document.querySelectorAll('.gallery-thumb');
    thumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            thumbs.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
            if (galleryMain) {
                galleryMain.style.backgroundImage = `url('${thumb.dataset.src}')`;
            }
        });
    });
});


// Auth Modal Logic
function openAuthModal(tab = 'login') {
    const modal = document.getElementById('auth-modal');
    if(modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        switchAuthTab(tab);
    }
}

function closeAuthModal() {
    const modal = document.getElementById('auth-modal');
    if(modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function switchAuthTab(tab) {
    document.getElementById('auth-login-panel').style.display = tab === 'login' ? 'block' : 'none';
    document.getElementById('auth-register-panel').style.display = tab === 'register' ? 'block' : 'none';
    document.getElementById('auth-forgot-panel').style.display = tab === 'forgot' ? 'block' : 'none';
    document.getElementById('auth-modal-error').style.display = 'none';
    document.getElementById('auth-modal-success').style.display = 'none';
}

async function submitAuth(e, action) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.append(action + '_submit', '1');
    formData.append('ajax', '1');

    try {
        const res = await fetch('auth.php?action=' + action, {
            method: 'POST',
            body: formData
        });
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch(err) {
            console.error('Invalid JSON response:', text);
            document.getElementById('auth-modal-error').innerText = 'حدث خطأ في السيرفر.';
            document.getElementById('auth-modal-error').style.display = 'block';
            document.getElementById('auth-modal-success').style.display = 'none';
            return;
        }
        
        if (data.success) {
            if (data.message) {
                document.getElementById('auth-modal-success').innerText = data.message;
                document.getElementById('auth-modal-success').style.display = 'block';
            }
            document.getElementById('auth-modal-error').style.display = 'none';
            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (action === 'register') {
                form.reset();
                setTimeout(() => switchAuthTab('login'), 1500);
            } else if (action === 'forgot') {
                form.reset();
                setTimeout(() => switchAuthTab('login'), 1500);
            }
        } else {
            document.getElementById('auth-modal-error').innerText = data.error;
            document.getElementById('auth-modal-error').style.display = 'block';
            document.getElementById('auth-modal-success').style.display = 'none';
        }
    } catch (err) {
        console.error(err);
    }
}
