<?php
// C:\xampp\htdocs\سيارة\api.php

header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$action = $_GET['action'] ?? '';

// Sync input parsing for POST requests
$inputData = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $inputData = json_decode($rawInput, true) ?? [];
}

try {
    switch ($action) {
        case 'toggle_favorite':
            if (!isLoggedIn()) {
                echo json_encode(['status' => 'unauthorized']);
                exit;
            }
            $car_id = intval($_GET['car_id'] ?? 0);
            if ($car_id <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid car ID']);
                exit;
            }
            
            $user_id = $_SESSION['user_id'];
            
            // Check if exists
            $stmt = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND car_id = ?");
            $stmt->execute([$user_id, $car_id]);
            $fav = $stmt->fetch();
            
            if ($fav) {
                // Delete
                $stmtDel = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND car_id = ?");
                $stmtDel->execute([$user_id, $car_id]);
                echo json_encode(['status' => 'success', 'action' => 'removed']);
            } else {
                // Add
                $stmtAdd = $db->prepare("INSERT INTO favorites (user_id, car_id) VALUES (?, ?)");
                $stmtAdd->execute([$user_id, $car_id]);
                echo json_encode(['status' => 'success', 'action' => 'added']);
            }
            break;

        case 'get_favorites_count':
            if (!isLoggedIn()) {
                echo json_encode(['status' => 'unauthorized']);
                exit;
            }
            $user_id = $_SESSION['user_id'];
            $stmt = $db->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $count = $stmt->fetchColumn();
            echo json_encode(['status' => 'success', 'count' => intval($count)]);
            break;

        case 'sync_favorites':
            if (!isLoggedIn()) {
                echo json_encode(['status' => 'unauthorized']);
                exit;
            }
            $user_id = $_SESSION['user_id'];
            $car_ids = array_map('intval', $inputData['car_ids'] ?? []);
            
            if (!empty($car_ids)) {
                $stmtCheck = $db->prepare("SELECT car_id FROM favorites WHERE user_id = ?");
                $stmtCheck->execute([$user_id]);
                $existing = $stmtCheck->fetchAll(PDO::FETCH_COLUMN);
                
                $stmtAdd = $db->prepare("INSERT OR IGNORE INTO favorites (user_id, car_id) VALUES (?, ?)");
                foreach ($car_ids as $cid) {
                    if (!in_array($cid, $existing)) {
                        $stmtAdd->execute([$user_id, $cid]);
                    }
                }
            }
            echo json_encode(['status' => 'success']);
            break;

        case 'get_compare_details':
            $car_ids = array_map('intval', $inputData['ids'] ?? []);
            if (empty($car_ids)) {
                echo json_encode(['status' => 'error', 'message' => 'No cars selected for comparison.']);
                exit;
            }
            
            // Build in clause
            $inList = implode(',', $car_ids);
            
            $sql = "SELECT cars.*, brands.name_ar AS brand_name_ar, brands.name_en AS brand_name_en, models.name_ar AS model_name_ar, models.name_en AS model_name_en
                    FROM cars
                    JOIN brands ON cars.brand_id = brands.id
                    JOIN models ON cars.model_id = models.id
                    WHERE cars.id IN ($inList)";
            
            $stmt = $db->query($sql);
            $cars = $stmt->fetchAll();
            
            // Format some columns
            $lang = getLanguage();
            foreach ($cars as &$c) {
                $c['brand_name'] = $lang === 'ar' ? $c['brand_name_ar'] : $c['brand_name_en'];
                $c['model_name'] = $lang === 'ar' ? $c['model_name_ar'] : $c['model_name_en'];
            }
            
            echo json_encode(['status' => 'success', 'cars' => $cars]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Action not found']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
