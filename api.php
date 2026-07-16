<?php
// C:\xampp\htdocs\سيارة\api.php
require_once __DIR__ . '/database/db.php';
require_once __DIR__ . '/shared/functions.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

switch ($action) {

    // =============================================
    // تبديل المفضلة (إضافة/حذف)
    // =============================================
    case 'toggle_favorite':
        if (!isLoggedIn()) {
            echo json_encode(['status' => 'unauthorized']);
            exit;
        }
        $carId = intval($_GET['car_id'] ?? 0);
        if ($carId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid car ID']);
            exit;
        }

        $userId = $_SESSION['user_id'];

        // تحقق إذا كانت موجودة بالفعل
        $stmt = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND car_id = ?");
        $stmt->execute([$userId, $carId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // حذف من المفضلة
            $del = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND car_id = ?");
            $del->execute([$userId, $carId]);
            echo json_encode(['status' => 'success', 'action' => 'removed']);
        } else {
            // إضافة للمفضلة
            $ins = $db->prepare("INSERT INTO favorites (user_id, car_id) VALUES (?, ?)");
            $ins->execute([$userId, $carId]);
            echo json_encode(['status' => 'success', 'action' => 'added']);
        }
        break;

    // =============================================
    // عدد المفضلة
    // =============================================
    case 'get_favorites_count':
        if (!isLoggedIn()) {
            echo json_encode(['status' => 'unauthorized', 'count' => 0]);
            exit;
        }
        $userId = $_SESSION['user_id'];
        $stmt = $db->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
        $stmt->execute([$userId]);
        $count = intval($stmt->fetchColumn());
        echo json_encode(['status' => 'success', 'count' => $count]);
        break;

    // =============================================
    // مزامنة مفضلة الزائر بعد تسجيل الدخول
    // =============================================
    case 'sync_favorites':
        if (!isLoggedIn()) {
            echo json_encode(['status' => 'unauthorized']);
            exit;
        }
        $body = json_decode(file_get_contents('php://input'), true);
        $carIds = $body['car_ids'] ?? [];

        if (empty($carIds) || !is_array($carIds)) {
            echo json_encode(['status' => 'success', 'synced' => 0]);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $synced = 0;

        foreach ($carIds as $carId) {
            $carId = intval($carId);
            if ($carId <= 0) continue;

            // تحقق إذا موجودة مسبقاً
            $check = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND car_id = ?");
            $check->execute([$userId, $carId]);
            if (!$check->fetch()) {
                $ins = $db->prepare("INSERT INTO favorites (user_id, car_id) VALUES (?, ?)");
                $ins->execute([$userId, $carId]);
                $synced++;
            }
        }

        echo json_encode(['status' => 'success', 'synced' => $synced]);
        break;

    // =============================================
    // حالة غير معروفة
    // =============================================
    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown action: ' . htmlspecialchars($action)]);
        break;
}
