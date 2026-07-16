// C:\xampp\htdocs\سيارة\assets\js\compare.js

document.addEventListener('DOMContentLoaded', () => {
    updateCompareBadge();
    updateCompareCheckboxes();

    // Event listener for compare checkboxes
    const compareBoxes = document.querySelectorAll('.car-compare-checkbox');
    compareBoxes.forEach(box => {
        box.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const carId = box.dataset.carId;
            toggleCompare(carId, box);
        });
    });

    function toggleCompare(carId, boxElement) {
        let compareList = JSON.parse(localStorage.getItem('compare_list') || '[]');
        const idx = compareList.indexOf(carId);

        if (idx === -1) {
            if (compareList.length >= 3) {
                alert(getLanguage() === 'ar' ? 'يمكنك مقارنة حتى 3 سيارات فقط.' : 'You can compare up to 3 cars only.');
                return;
            }
            compareList.push(carId);
            boxElement.classList.add('active');
        } else {
            compareList.splice(idx, 1);
            boxElement.classList.remove('active');
        }

        localStorage.setItem('compare_list', JSON.stringify(compareList));
        updateCompareBadge();
    }

    function updateCompareCheckboxes() {
        let compareList = JSON.parse(localStorage.getItem('compare_list') || '[]');
        const compareBoxes = document.querySelectorAll('.car-compare-checkbox');
        compareBoxes.forEach(box => {
            const carId = box.dataset.carId;
            if (compareList.includes(carId)) {
                box.classList.add('active');
            } else {
                box.classList.remove('active');
            }
        });
    }

    function updateCompareBadge() {
        const badge = document.getElementById('compare-badge');
        if (!badge) return;
        
        let compareList = JSON.parse(localStorage.getItem('compare_list') || '[]');
        badge.innerText = compareList.length;
        badge.style.display = compareList.length > 0 ? 'flex' : 'none';
    }

    // Helper for language checking in compare scope
    function getLanguage() {
        return document.body.classList.contains('lang-en') ? 'en' : 'ar';
    }

    // Dynamic compare page loader
    const comparePageContent = document.getElementById('compare-page-content');
    if (comparePageContent) {
        loadCompareItems();
    }

    function loadCompareItems() {
        let compareList = JSON.parse(localStorage.getItem('compare_list') || '[]');
        if (compareList.length === 0) {
            comparePageContent.innerHTML = `
                <div class="container section-padding text-center">
                    <div style="font-size: 3rem; margin-bottom: 1.5rem;">⚖️</div>
                    <h3>${getLanguage() === 'ar' ? 'لم تقم بتحديد سيارات للمقارنة بعد.' : 'No cars selected for comparison.'}</h3>
                    <p style="margin: 1rem 0 2rem; color: var(--text-secondary);">
                        ${getLanguage() === 'ar' ? 'تصفح السيارات واضغط على زر الميزان لإضافتها هنا.' : 'Browse cars and click the scale icon to add them here.'}
                    </p>
                    <a href="search.php" class="btn-next" style="display: inline-block;">${getLanguage() === 'ar' ? 'تصفح السيارات' : 'Browse Cars'}</a>
                </div>
            `;
            return;
        }

        // Fetch details of selected cars for comparison table
        fetch('api.php?action=get_compare_details', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids: compareList })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                renderCompareTable(data.cars);
            } else {
                comparePageContent.innerHTML = `<p class="text-center text-danger">${data.message}</p>`;
            }
        })
        .catch(err => {
            console.error("Comparison load error:", err);
            comparePageContent.innerHTML = `<p class="text-center text-danger">Error loading comparison data.</p>`;
        });
    }

    function renderCompareTable(cars) {
        const isAr = getLanguage() === 'ar';
        
        let html = `
            <div class="table-container">
                <table class="admin-table" style="min-width: 800px;">
                    <thead>
                        <tr>
                            <th style="width: 200px;">${isAr ? 'المواصفات' : 'Specification'}</th>
                            ${cars.map(c => `
                                <th class="text-center">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; position: relative;">
                                        <button class="btn-action btn-action-danger remove-compare-btn" data-id="${c.id}" style="position: absolute; top: -5px; right: -5px; padding: 2px 6px;">✕</button>
                                        <div style="height: 100px; width: 140px; background-color: var(--input-bg); border-radius: 8px; display: flex; justify-content: center; align-items: center; font-weight: bold; color: #fff; background: linear-gradient(135deg, #1e293b, #334155);">
                                            ${c.brand_name}
                                        </div>
                                        <strong>${isAr ? c.name_ar : c.name_en}</strong>
                                        <span style="color: var(--success); font-weight: bold;">${formatPrice(c.price)}</span>
                                        <a href="car.php?id=${c.id}" class="btn-action btn-action-primary" style="font-size: 0.75rem; padding: 4px 8px; margin-top: 5px;">${isAr ? 'تفاصيل' : 'Details'}</a>
                                    </div>
                                </th>
                            `).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'سنة الصنع' : 'Manufacture Year'}</td>
                            ${cars.map(c => `<td>${c.year}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'يبدأ التقسيط من' : 'Installment From'}</td>
                            ${cars.map(c => `<td><strong style="color: var(--success);">${formatPrice(c.min_installment)} / ${isAr ? 'شهر' : 'mo'}</strong></td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'نوع الوقود' : 'Fuel Type'}</td>
                            ${cars.map(c => `<td>${isAr ? c.fuel_ar : c.fuel_en}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'ناقل الحركة' : 'Transmission'}</td>
                            ${cars.map(c => `<td>${isAr ? c.transmission_ar : c.transmission_en}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'نظام الدفع' : 'Drivetrain'}</td>
                            ${cars.map(c => `<td>${isAr ? c.drive_ar : c.drive_en}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'حجم المحرك' : 'Engine Size'}</td>
                            ${cars.map(c => `<td>${c.engine_size}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'عدد المقاعد' : 'Seats'}</td>
                            ${cars.map(c => `<td>${c.seats}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'عدد الأبواب' : 'Doors'}</td>
                            ${cars.map(c => `<td>${c.doors}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'اللون الخارجي' : 'Exterior Color'}</td>
                            ${cars.map(c => `<td>${isAr ? c.color_ar : c.color_en}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'اللون الداخلي' : 'Interior Color'}</td>
                            ${cars.map(c => `<td>${isAr ? c.color_inner_ar : c.color_inner_en}</td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'الأمان' : 'Safety Features'}</td>
                            ${cars.map(c => `<td>
                                <ul style="list-style-type: none; font-size: 0.8rem; text-align: start; padding-left: 0;">
                                    ${JSON.parse(c.specs_safety).map(item => `<li>✓ ${item}</li>`).join('')}
                                </ul>
                            </td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'الراحة' : 'Comfort'}</td>
                            ${cars.map(c => `<td>
                                <ul style="list-style-type: none; font-size: 0.8rem; text-align: start; padding-left: 0;">
                                    ${JSON.parse(c.specs_comfort).map(item => `<li>✓ ${item}</li>`).join('')}
                                </ul>
                            </td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'التقنيات' : 'Technology'}</td>
                            ${cars.map(c => `<td>
                                <ul style="list-style-type: none; font-size: 0.8rem; text-align: start; padding-left: 0;">
                                    ${JSON.parse(c.specs_tech).map(item => `<li>✓ ${item}</li>`).join('')}
                                </ul>
                            </td>`).join('')}
                        </tr>
                        <tr>
                            <td class="compare-row-title">${isAr ? 'التجهيزات الخارجية' : 'Exterior'}</td>
                            ${cars.map(c => `<td>
                                <ul style="list-style-type: none; font-size: 0.8rem; text-align: start; padding-left: 0;">
                                    ${JSON.parse(c.specs_exterior).map(item => `<li>✓ ${item}</li>`).join('')}
                                </ul>
                            </td>`).join('')}
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
        
        comparePageContent.innerHTML = html;

        // Add event listeners for removal
        const removeBtns = comparePageContent.querySelectorAll('.remove-compare-btn');
        removeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const idToRemove = btn.dataset.id;
                let compareList = JSON.parse(localStorage.getItem('compare_list') || '[]');
                compareList = compareList.filter(id => id !== idToRemove);
                localStorage.setItem('compare_list', JSON.stringify(compareList));
                updateCompareBadge();
                loadCompareItems();
            });
        });
    }

    function formatPrice(price) {
        const isAr = getLanguage() === 'ar';
        const formatted = new Intl.NumberFormat().format(price);
        return isAr ? `${formatted} ريال` : `SAR ${formatted}`;
    }
});
