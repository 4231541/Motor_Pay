<?php
// C:\xampp\htdocs\سيارة\database\seed.php

$dbPath = __DIR__ . '/syarah.db';
$dbDir = dirname($dbPath);

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

try {
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    echo "Connected to SQLite database successfully.\n";

    // Create Tables
    
    // 1. Brands Table
    $db->exec("CREATE TABLE IF NOT EXISTS brands (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name_ar TEXT NOT NULL,
        name_en TEXT NOT NULL,
        logo TEXT NOT NULL
    )");

    // 2. Models Table
    $db->exec("CREATE TABLE IF NOT EXISTS models (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        brand_id INTEGER NOT NULL,
        name_ar TEXT NOT NULL,
        name_en TEXT NOT NULL,
        FOREIGN KEY(brand_id) REFERENCES brands(id) ON DELETE CASCADE
    )");

    // 3. Cars Table
    $db->exec("CREATE TABLE IF NOT EXISTS cars (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        brand_id INTEGER NOT NULL,
        model_id INTEGER NOT NULL,
        name_ar TEXT NOT NULL,
        name_en TEXT NOT NULL,
        year INTEGER NOT NULL,
        price REAL NOT NULL,
        min_installment REAL NOT NULL,
        images TEXT NOT NULL, -- JSON array
        type_ar TEXT,
        type_en TEXT,
        grade_ar TEXT,
        grade_en TEXT,
        fuel_ar TEXT,
        fuel_en TEXT,
        transmission_ar TEXT,
        transmission_en TEXT,
        drive_ar TEXT,
        drive_en TEXT,
        color_ar TEXT,
        color_en TEXT,
        color_inner_ar TEXT,
        color_inner_en TEXT,
        engine_size TEXT,
        seats INTEGER,
        doors INTEGER,
        specs_safety TEXT, -- JSON array
        specs_comfort TEXT, -- JSON array
        specs_tech TEXT, -- JSON array
        specs_exterior TEXT, -- JSON array
        is_available INTEGER DEFAULT 1,
        views INTEGER DEFAULT 0,
        orders_count INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(brand_id) REFERENCES brands(id) ON DELETE CASCADE,
        FOREIGN KEY(model_id) REFERENCES models(id) ON DELETE CASCADE
    )");

    // 4. Users Table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        phone TEXT,
        city TEXT,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'user',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Favorites Table
    $db->exec("CREATE TABLE IF NOT EXISTS favorites (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        car_id INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(car_id) REFERENCES cars(id) ON DELETE CASCADE,
        UNIQUE(user_id, car_id)
    )");

    // 6. Requests Table
    $db->exec("CREATE TABLE IF NOT EXISTS requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        car_id INTEGER NOT NULL,
        type TEXT NOT NULL, -- 'booking' or 'installment'
        name TEXT NOT NULL,
        phone TEXT NOT NULL,
        email TEXT NOT NULL,
        city TEXT NOT NULL,
        payment_method TEXT,
        notes TEXT,
        national_id TEXT,
        salary REAL,
        employer TEXT,
        work_duration INTEGER,
        downpayment REAL,
        term_months INTEGER,
        monthly_installment REAL,
        status TEXT DEFAULT 'received', -- 'received', 'reviewing', 'contacting', 'booked', 'delivered', 'rejected'
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY(car_id) REFERENCES cars(id) ON DELETE CASCADE
    )");

    // 7. Notifications Table
    $db->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER, -- NULL for all
        title_ar TEXT NOT NULL,
        title_en TEXT NOT NULL,
        message_ar TEXT NOT NULL,
        message_en TEXT NOT NULL,
        is_read INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 8. Offers Table
    $db->exec("CREATE TABLE IF NOT EXISTS offers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title_ar TEXT NOT NULL,
        title_en TEXT NOT NULL,
        description_ar TEXT NOT NULL,
        description_en TEXT NOT NULL,
        discount_pct REAL DEFAULT 0,
        car_id INTEGER,
        image TEXT,
        valid_until DATE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(car_id) REFERENCES cars(id) ON DELETE SET NULL
    )");

    echo "Tables created successfully.\n";

    // Helper check if data exists
    $countBrands = $db->query("SELECT COUNT(*) FROM brands")->fetchColumn();
    if ($countBrands == 0) {
        echo "Seeding data...\n";

        // Seed Brands
        $brands = [
            ['name_ar' => 'تويوتا', 'name_en' => 'Toyota', 'logo' => 'toyota.svg'],
            ['name_ar' => 'هيونداي', 'name_en' => 'Hyundai', 'logo' => 'hyundai.svg'],
            ['name_ar' => 'كيا', 'name_en' => 'Kia', 'logo' => 'kia.svg'],
            ['name_ar' => 'نيسان', 'name_en' => 'Nissan', 'logo' => 'nissan.svg'],
            ['name_ar' => 'بي إم دبليو', 'name_en' => 'BMW', 'logo' => 'bmw.svg'],
            ['name_ar' => 'مرسيدس بنز', 'name_en' => 'Mercedes-Benz', 'logo' => 'mercedes.svg'],
            ['name_ar' => 'أودي', 'name_en' => 'Audi', 'logo' => 'audi.svg'],
            ['name_ar' => 'هوندا', 'name_en' => 'Honda', 'logo' => 'honda.svg']
        ];

        $stmtBrand = $db->prepare("INSERT INTO brands (name_ar, name_en, logo) VALUES (:name_ar, :name_en, :logo)");
        foreach ($brands as $b) {
            $stmtBrand->execute($b);
        }
        
        // Seed Models mapping
        $brandModels = [
            'Toyota' => [
                ['name_ar' => 'كامري', 'name_en' => 'Camry'],
                ['name_ar' => 'لاند كروزر', 'name_en' => 'Land Cruiser'],
                ['name_ar' => 'كورولا', 'name_en' => 'Corolla']
            ],
            'Hyundai' => [
                ['name_ar' => 'توسان', 'name_en' => 'Tucson'],
                ['name_ar' => 'إلنترا', 'name_en' => 'Elantra'],
                ['name_ar' => 'سوناتا', 'name_en' => 'Sonata']
            ],
            'Kia' => [
                ['name_ar' => 'K5', 'name_en' => 'K5'],
                ['name_ar' => 'سبورتج', 'name_en' => 'Sportage'],
                ['name_ar' => 'سورينتو', 'name_en' => 'Sorento']
            ],
            'Nissan' => [
                ['name_ar' => 'باترول', 'name_en' => 'Patrol'],
                ['name_ar' => 'ألتيما', 'name_en' => 'Altima'],
                ['name_ar' => 'اكس تريل', 'name_en' => 'X-Trail']
            ],
            'BMW' => [
                ['name_ar' => 'الفئة الخامسة', 'name_en' => '5 Series'],
                ['name_ar' => 'X5', 'name_en' => 'X5']
            ],
            'Mercedes-Benz' => [
                ['name_ar' => 'الفئة C', 'name_en' => 'C-Class'],
                ['name_ar' => 'الفئة S', 'name_en' => 'S-Class']
            ],
            'Audi' => [
                ['name_ar' => 'Q7', 'name_en' => 'Q7'],
                ['name_ar' => 'A6', 'name_en' => 'A6']
            ],
            'Honda' => [
                ['name_ar' => 'أكورد', 'name_en' => 'Accord'],
                ['name_ar' => 'سيفيك', 'name_en' => 'Civic']
            ]
        ];

        $stmtModel = $db->prepare("INSERT INTO models (brand_id, name_ar, name_en) VALUES (:brand_id, :name_ar, :name_en)");
        foreach ($brandModels as $brandName => $models) {
            $brandId = $db->query("SELECT id FROM brands WHERE name_en = " . $db->quote($brandName))->fetchColumn();
            foreach ($models as $m) {
                $stmtModel->execute([
                    ':brand_id' => $brandId,
                    ':name_ar' => $m['name_ar'],
                    ':name_en' => $m['name_en']
                ]);
            }
        }

        // Add Cars
        $carsData = [
            [
                'brand' => 'Toyota', 'model' => 'Camry', 'name_ar' => 'تويوتا كامري GLE 2026', 'name_en' => 'Toyota Camry GLE 2026',
                'year' => 2026, 'price' => 112000, 'min_installment' => 1650, 'type_ar' => 'سيدان', 'type_en' => 'Sedan',
                'grade_ar' => 'فل كامل GLE', 'grade_en' => 'Full GLE', 'fuel_ar' => 'بنزين', 'fuel_en' => 'Petrol',
                'transmission_ar' => 'أوتوماتيك', 'transmission_en' => 'Automatic', 'drive_ar' => 'دفع أمامي', 'drive_en' => 'FWD',
                'color_ar' => 'أبيض لؤلؤي', 'color_en' => 'Pearl White', 'color_inner_ar' => 'بيج جلد', 'color_inner_en' => 'Beige Leather',
                'engine_size' => '2.5L', 'seats' => 5, 'doors' => 4,
                'images' => json_encode(['camry_1.jpg', 'camry_2.jpg', 'camry_3.jpg']),
                'safety' => json_encode(['ABS', 'ESP', 'وسائد هوائية أمامية وجانبية', 'كاميرا خلفية', 'حساسات أمامية وخلفية', 'مثبت سرعة متكيف', 'مراقبة ضغط الإطارات', 'المحافظة على المسار']),
                'comfort' => json_encode(['دخول ذكي', 'تشغيل بصمة', 'مقاعد جلد', 'مقاعد كهربائية', 'مكيف أوتوماتيك ثنائي المناطق', 'فتحة سقف']),
                'tech' => json_encode(['شاشة لمس 9 بوصة', 'Apple CarPlay', 'Android Auto', 'بلوتوث', 'شاحن لاسلكي', 'نظام صوتي JBL 9 سماعات']),
                'exterior' => json_encode(['جنوط ألمنيوم 18 بوصة', 'مصابيح LED', 'إضاءة نهارية LED', 'مرايا كهربائية قابلة للطي'])
            ],
            [
                'brand' => 'Hyundai', 'model' => 'Tucson', 'name_ar' => 'هيونداي توسان سمارت 2026', 'name_en' => 'Hyundai Tucson Smart 2026',
                'year' => 2026, 'price' => 104000, 'min_installment' => 1490, 'type_ar' => 'عائلية / SUV', 'type_en' => 'SUV',
                'grade_ar' => 'نصف فل سمارت', 'grade_en' => 'Smart Mid', 'fuel_ar' => 'بنزين', 'fuel_en' => 'Petrol',
                'transmission_ar' => 'أوتوماتيك', 'transmission_en' => 'Automatic', 'drive_ar' => 'دفع رباعي مستمر', 'drive_en' => 'AWD',
                'color_ar' => 'رمادي معدني', 'color_en' => 'Metallic Gray', 'color_inner_ar' => 'مخمل رمادي', 'color_inner_en' => 'Gray Cloth',
                'engine_size' => '2.0L', 'seats' => 5, 'doors' => 5,
                'images' => json_encode(['tucson_1.jpg', 'tucson_2.jpg']),
                'safety' => json_encode(['ABS', 'ESP', 'وسائد هوائية أمامية', 'كاميرا خلفية', 'حساسات خلفية', 'مثبت سرعة']),
                'comfort' => json_encode(['دخول ذكي', 'تشغيل بصمة', 'مكيف خلفي', 'فتحة سقف بانوراما']),
                'tech' => json_encode(['شاشة لمس 8 بوصة', 'Apple CarPlay', 'Android Auto', 'بلوتوث', 'USB']),
                'exterior' => json_encode(['جنوط 17 بوصة', 'مصابيح LED أمامية', 'إضاءة نهارية', 'مرايا كهربائية'])
            ],
            [
                'brand' => 'Kia', 'model' => 'K5', 'name_ar' => 'كيا K5 LX 2026', 'name_en' => 'Kia K5 LX 2026',
                'year' => 2026, 'price' => 96000, 'min_installment' => 1380, 'type_ar' => 'سيدان', 'type_en' => 'Sedan',
                'grade_ar' => 'ستاندرد LX', 'grade_en' => 'Standard LX', 'fuel_ar' => 'بنزين', 'fuel_en' => 'Petrol',
                'transmission_ar' => 'أوتوماتيك', 'transmission_en' => 'Automatic', 'drive_ar' => 'دفع أمامي', 'drive_en' => 'FWD',
                'color_ar' => 'فضي', 'color_en' => 'Silver', 'color_inner_ar' => 'أسود مخمل', 'color_inner_en' => 'Black Cloth',
                'engine_size' => '2.0L', 'seats' => 5, 'doors' => 4,
                'images' => json_encode(['k5_1.jpg', 'k5_2.jpg']),
                'safety' => json_encode(['ABS', 'ESP', 'وسائد هوائية أمامية', 'حساسات خلفية', 'كاميرا خلفية', 'مانع تشغيل ضد السرقة']),
                'comfort' => json_encode(['مكيف أوتوماتيك', 'مرايا كهربائية', 'مثبت سرعة']),
                'tech' => json_encode(['شاشة لمس 8 بوصة', 'Apple CarPlay', 'Android Auto', 'بلوتوث']),
                'exterior' => json_encode(['جنوط ألمنيوم 17 بوصة', 'إضاءة نهارية LED'])
            ],
            [
                'brand' => 'Nissan', 'model' => 'Patrol', 'name_ar' => 'نيسان باترول بلاتينيوم 2026', 'name_en' => 'Nissan Patrol Platinum 2026',
                'year' => 2026, 'price' => 285000, 'min_installment' => 3950, 'type_ar' => 'عائلية / SUV', 'type_en' => 'SUV',
                'grade_ar' => 'بلاتينيوم فل كامل', 'grade_en' => 'Platinum Full Option', 'fuel_ar' => 'بنزين', 'fuel_en' => 'Petrol',
                'transmission_ar' => 'أوتوماتيك', 'transmission_en' => 'Automatic', 'drive_ar' => 'دفع رباعي 4x4', 'drive_en' => '4WD',
                'color_ar' => 'أسود ملكي', 'color_en' => 'Royal Black', 'color_inner_ar' => 'جلد جملي ذو جودة عالية', 'color_inner_en' => 'Tan Premium Leather',
                'engine_size' => '4.0L V6', 'seats' => 8, 'doors' => 5,
                'images' => json_encode(['patrol_1.jpg', 'patrol_2.jpg', 'patrol_3.jpg']),
                'safety' => json_encode(['ABS', 'ESP', 'وسائد هوائية أمامية وجانبية وستائرية', 'كاميرا 360 درجة', 'حساسات أمامية وخلفية', 'مثبت سرعة راداري', 'مراقبة ضغط الإطارات', 'المحافظة على المسار', 'رادار منع التصادم']),
                'comfort' => json_encode(['دخول ذكي', 'تشغيل بصمة', 'مقاعد جلد فاخرة', 'تبريد وتدفئة المقاعد', 'مكيف أوتوماتيك متعدد المناطق', 'فتحة سقف بانوراما', 'مقاعد كهربائية مع ذاكرة']),
                'tech' => json_encode(['شاشة لمس 12.3 بوصة', 'شاشة عدادات رقمية', 'Apple CarPlay لاسلكي', 'Android Auto', 'بلوتوث', 'نظام ملاحة GPS', 'نظام صوتي BOSE 13 سماعة']),
                'exterior' => json_encode(['جنوط ألمنيوم 20 بوصة', 'مصابيح LED متكيفة', 'مرايا قابلة للطي كهربائياً مع ذاكرة', 'عتبات جانية مضيئة', 'باب شنطة كهربائي'])
            ],
            [
                'brand' => 'BMW', 'model' => '5 Series', 'name_ar' => 'بي إم دبليو الفئة الخامسة 520i 2026', 'name_en' => 'BMW 5 Series 520i 2026',
                'year' => 2026, 'price' => 310000, 'min_installment' => 4300, 'type_ar' => 'سيدان فخم', 'type_en' => 'Luxury Sedan',
                'grade_ar' => 'M Sport', 'grade_en' => 'M Sport Package', 'fuel_ar' => 'هايبرد (بنزين/كهرباء)', 'fuel_en' => 'Hybrid',
                'transmission_ar' => 'أوتوماتيك', 'transmission_en' => 'Automatic', 'drive_ar' => 'دفع خلفي', 'drive_en' => 'RWD',
                'color_ar' => 'أزرق داكن', 'color_en' => 'Dark Blue', 'color_inner_ar' => 'جلد كونياك بني', 'color_inner_en' => 'Cognac Brown Leather',
                'engine_size' => '2.0L Turbo', 'seats' => 5, 'doors' => 4,
                'images' => json_encode(['bmw5_1.jpg', 'bmw5_2.jpg']),
                'safety' => json_encode(['ABS', 'ESP', 'وسائد هوائية متكاملة', 'مساعد القيادة الاحترافي', 'كاميرا 360 درجة', 'حساسات محيطية', 'المحافظة على المسار النشط', 'نظام الحماية الوقائية']),
                'comfort' => json_encode(['دخول ذكي مريح', 'تشغيل بصمة/مفتاح رقمي', 'مقاعد رياضية كهربائية مع تدفئة', 'إنارة داخلية تفاعلية', 'سقف بانورامي']),
                'tech' => json_encode(['شاشة منحنية BMW Curved Display', 'نظام الملاحة المتقدم', 'Apple CarPlay / Android Auto', 'شاحن لاسلكي وسريع', 'شاشة عرض على الزجاج (HUD)', 'نظام صوتي Harman Kardon']),
                'exterior' => json_encode(['جنوط M مقاس 19 بوصة', 'حزمة M الرياضية الخارجية', 'إضاءة ترحيبية خارجية LED'])
            ],
            [
                'brand' => 'Mercedes-Benz', 'model' => 'C-Class', 'name_ar' => 'مرسيدس C200 2026', 'name_en' => 'Mercedes C200 2026',
                'year' => 2026, 'price' => 275000, 'min_installment' => 3800, 'type_ar' => 'سيدان فخم', 'type_en' => 'Luxury Sedan',
                'grade_ar' => 'AMG Line', 'grade_en' => 'AMG Line Package', 'fuel_ar' => 'بنزين', 'fuel_en' => 'Petrol',
                'transmission_ar' => 'أوتوماتيك 9 سرعات', 'transmission_en' => '9-Speed Automatic', 'drive_ar' => 'دفع خلفي', 'drive_en' => 'RWD',
                'color_ar' => 'رمادي سيلينيت', 'color_en' => 'Selenite Gray', 'color_inner_ar' => 'جلد أحمر مع أسود الماني', 'color_inner_en' => 'Red/Black Nappa Leather',
                'engine_size' => '1.5L Turbo EQ Boost', 'seats' => 5, 'doors' => 4,
                'images' => json_encode(['c200_1.jpg', 'c200_2.jpg']),
                'safety' => json_encode(['ABS', 'ESP', 'وسائد هوائية محيطة', 'فرملة الطوارئ النشطة', 'مساعد النقطة العمياء', 'مثبت سرعة تفاعلي DISTRONIC', 'كاميرا خلفية عالية الدقة']),
                'comfort' => json_encode(['مفتاح ذكي KEYLESS-GO', 'تشغيل بصمة', 'مقاعد AMG جلدية كهربائية بالكامل', 'تكييف هواء أوتوماتيكي متطور THERMATIC', 'إضاءة محيطية 64 لوناً']),
                'tech' => json_encode(['نظام MBUX المحدث بشاشة 11.9 بوصة', 'شاشة عدادات 12.3 بوصة', 'شاحن لاسلكي', 'تكامل الهاتف الذكي']),
                'exterior' => json_encode(['جنوط رياضية AMG قياس 18 بوصة', 'شبك AMG الرياضي بفتحات ماسية', 'مرايا قابلة للطي والتعتيم تلقائياً'])
            ]
        ];

        $stmtCar = $db->prepare("INSERT INTO cars (brand_id, model_id, name_ar, name_en, year, price, min_installment, images, type_ar, type_en, grade_ar, grade_en, fuel_ar, fuel_en, transmission_ar, transmission_en, drive_ar, drive_en, color_ar, color_en, color_inner_ar, color_inner_en, engine_size, seats, doors, specs_safety, specs_comfort, specs_tech, specs_exterior) VALUES (:brand_id, :model_id, :name_ar, :name_en, :year, :price, :min_installment, :images, :type_ar, :type_en, :grade_ar, :grade_en, :fuel_ar, :fuel_en, :transmission_ar, :transmission_en, :drive_ar, :drive_en, :color_ar, :color_en, :color_inner_ar, :color_inner_en, :engine_size, :seats, :doors, :specs_safety, :specs_comfort, :specs_tech, :specs_exterior)");

        foreach ($carsData as $c) {
            $brandId = $db->query("SELECT id FROM brands WHERE name_en = " . $db->quote($c['brand']))->fetchColumn();
            $modelId = $db->query("SELECT id FROM models WHERE name_en = " . $db->quote($c['model']) . " AND brand_id = $brandId")->fetchColumn();
            
            $stmtCar->execute([
                ':brand_id' => $brandId,
                ':model_id' => $modelId,
                ':name_ar' => $c['name_ar'],
                ':name_en' => $c['name_en'],
                ':year' => $c['year'],
                ':price' => $c['price'],
                ':min_installment' => $c['min_installment'],
                ':images' => $c['images'],
                ':type_ar' => $c['type_ar'],
                ':type_en' => $c['type_en'],
                ':grade_ar' => $c['grade_ar'],
                ':grade_en' => $c['grade_en'],
                ':fuel_ar' => $c['fuel_ar'],
                ':fuel_en' => $c['fuel_en'],
                ':transmission_ar' => $c['transmission_ar'],
                ':transmission_en' => $c['transmission_en'],
                ':drive_ar' => $c['drive_ar'],
                ':drive_en' => $c['drive_en'],
                ':color_ar' => $c['color_ar'],
                ':color_en' => $c['color_en'],
                ':color_inner_ar' => $c['color_inner_ar'],
                ':color_inner_en' => $c['color_inner_en'],
                ':engine_size' => $c['engine_size'],
                ':seats' => $c['seats'],
                ':doors' => $c['doors'],
                ':specs_safety' => $c['safety'],
                ':specs_comfort' => $c['comfort'],
                ':specs_tech' => $c['tech'],
                ':specs_exterior' => $c['exterior']
            ]);
        }

        // Add Users
        $stmtUser = $db->prepare("INSERT INTO users (name, email, phone, city, password, role) VALUES (:name, :email, :phone, :city, :password, :role)");
        
        $stmtUser->execute([
            ':name' => 'مشرف النظام',
            ':email' => 'admin@syarah.com',
            ':phone' => '0500000000',
            ':city' => 'الرياض',
            ':password' => password_hash('admin123', PASSWORD_BCRYPT),
            ':role' => 'admin'
        ]);

        $stmtUser->execute([
            ':name' => 'أحمد العتيبي',
            ':email' => 'user@syarah.com',
            ':phone' => '0512345678',
            ':city' => 'جدة',
            ':password' => password_hash('user123', PASSWORD_BCRYPT),
            ':role' => 'user'
        ]);

        // Add Offers
        $camryId = $db->query("SELECT id FROM cars WHERE name_en LIKE '%Camry%'")->fetchColumn();
        $tucsonId = $db->query("SELECT id FROM cars WHERE name_en LIKE '%Tucson%'")->fetchColumn();

        $stmtOffer = $db->prepare("INSERT INTO offers (title_ar, title_en, description_ar, description_en, discount_pct, car_id, image, valid_until) VALUES (:title_ar, :title_en, :description_ar, :description_en, :discount_pct, :car_id, :image, :valid_until)");
        
        $stmtOffer->execute([
            ':title_ar' => 'عرض الصيف المميز على كامري 2026',
            ':title_en' => 'Summer Deal on Camry 2026',
            ':description_ar' => 'احصل على خصم 5% ودعم للدفعة الأولى مع فترة سداد مرنة تصل لـ 60 شهراً بدون رسوم إدارية.',
            ':description_en' => 'Get 5% discount and downpayment assistance with flexible term options up to 60 months and 0 admin fees.',
            ':discount_pct' => 5,
            ':car_id' => $camryId,
            ':image' => 'offer_camry.jpg',
            ':valid_until' => '2026-09-30'
        ]);

        $stmtOffer->execute([
            ':title_ar' => 'قسطها بسعر الكاش! توسان 2026',
            ':title_en' => 'Installments at Cash Price! Tucson 2026',
            ':description_ar' => 'عروض تمويلية مميزة بالتعاون مع البنك الأهلي بقسط شهري يبدأ من 1,490 ريال وهامش ربح 0%.',
            ':description_en' => 'Exclusive financing program with SNB, monthly installment starting from 1,490 SAR and 0% profit margin.',
            ':discount_pct' => 0,
            ':car_id' => $tucsonId,
            ':image' => 'offer_tucson.jpg',
            ':valid_until' => '2026-08-31'
        ]);

        // Add Mock requests
        $stmtReq = $db->prepare("INSERT INTO requests (user_id, car_id, type, name, phone, email, city, payment_method, notes, national_id, salary, employer, work_duration, downpayment, term_months, monthly_installment, status, created_at) VALUES (:user_id, :car_id, :type, :name, :phone, :email, :city, :payment_method, :notes, :national_id, :salary, :employer, :work_duration, :downpayment, :term_months, :monthly_installment, :status, :created_at)");
        
        $userId = $db->query("SELECT id FROM users WHERE email = 'user@syarah.com'")->fetchColumn();
        $stmtReq->execute([
            ':user_id' => $userId,
            ':car_id' => $camryId,
            ':type' => 'installment',
            ':name' => 'أحمد العتيبي',
            ':phone' => '0512345678',
            ':email' => 'user@syarah.com',
            ':city' => 'جدة',
            ':payment_method' => 'installment',
            ':notes' => 'أرغب في الاستلام بجدة، والتواصل عبر واتساب.',
            ':national_id' => '1023456789',
            ':salary' => 12500,
            ':employer' => 'وزارة التعليم',
            ':work_duration' => 5,
            ':downpayment' => 20000,
            ':term_months' => 60,
            ':monthly_installment' => 1650,
            ':status' => 'received',
            ':created_at' => '2026-07-10 14:32:00'
        ]);

        $patrolId = $db->query("SELECT id FROM cars WHERE name_en LIKE '%Patrol%'")->fetchColumn();
        $stmtReq->execute([
            ':user_id' => null,
            ':car_id' => $patrolId,
            ':type' => 'booking',
            ':name' => 'سلطان المطيري',
            ':phone' => '0544444444',
            ':email' => 'sultan@example.com',
            ':city' => 'الرياض',
            ':payment_method' => 'card',
            ':notes' => 'تم دفع قيمة الحجز المبدئي عبر بوابة مدى أونلاين.',
            ':national_id' => null,
            ':salary' => null,
            ':employer' => null,
            ':work_duration' => null,
            ':downpayment' => null,
            ':term_months' => null,
            ':monthly_installment' => null,
            ':status' => 'booked',
            ':created_at' => '2026-07-12 09:15:00'
        ]);

        $bmwId = $db->query("SELECT id FROM cars WHERE name_en LIKE '%5 Series%'")->fetchColumn();
        $stmtReq->execute([
            ':user_id' => null,
            ':car_id' => $bmwId,
            ':type' => 'installment',
            ':name' => 'خالد الحربي',
            ':phone' => '0533333333',
            ':email' => 'khaled@example.com',
            ':city' => 'الدمام',
            ':payment_method' => 'installment',
            ':notes' => 'يرجى مراجعة الطلب بأسرع وقت.',
            ':national_id' => '1098765432',
            ':salary' => 19500,
            ':employer' => 'أرامكو السعودية',
            ':work_duration' => 8,
            ':downpayment' => 50000,
            ':term_months' => 48,
            ':monthly_installment' => 5800,
            ':status' => 'reviewing',
            ':created_at' => '2026-07-13 18:22:00'
        ]);

        echo "Seeding completed successfully.\n";
    } else {
        echo "Database already contains seed data.\n";
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
