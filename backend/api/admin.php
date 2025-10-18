<?php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
require_once "../db.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Статистика для админ панели
    $stats = [];
    
    // Статистика заказов
    $result = $conn->query("
        SELECT 
            COUNT(*) as total_orders,
            SUM(status = 'new') as new_orders,
            SUM(status = 'processing') as processing_orders,
            SUM(status = 'delivering') as delivering_orders,
            SUM(status = 'delivered') as delivered_orders,
            SUM(status = 'cancelled') as cancelled_orders
        FROM orders
    ");
    $stats['orders'] = $result->fetch_assoc();
    
    // Статистика пользователей
    $result = $conn->query("SELECT COUNT(*) as total_users FROM users");
    $stats['users'] = $result->fetch_assoc();
    
    // Статистика курьеров
    $result = $conn->query("SELECT COUNT(*) as total_couriers, SUM(is_active = 1) as active_couriers FROM couriers");
    $stats['couriers'] = $result->fetch_assoc();
    
    // Заказы на модерации
    $result = $conn->query("
        SELECT o.*, u.full_name as user_name, u.phone as user_phone 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.status = 'new' 
        ORDER BY o.created_at DESC
    ");
    $stats['moderation_orders'] = $result->fetch_all(MYSQLI_ASSOC);
    
    // Активные курьеры
    $result = $conn->query("SELECT * FROM couriers WHERE is_active = 1");
    $stats['active_couriers'] = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode($stats);
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $order_id = $data['order_id'];
    $status = $data['status'];
    $courier_id = $data['courier_id'] ?? null;
    $comments = $data['comments'] ?? null;

    $stmt = $conn->prepare("UPDATE orders SET status = ?, courier_id = ?, comments = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("sisi", $status, $courier_id, $comments, $order_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Ошибка обновления статуса: " . $stmt->error]);
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'] ?? '';
    
    if ($action === 'add_courier') {
        $name = $data['name'];
        $phone = $data['phone'];
        $vehicle_type = $data['vehicle_type'];
        
        $stmt = $conn->prepare("INSERT INTO couriers (name, phone, vehicle_type) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $phone, $vehicle_type);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "courier_id" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Ошибка добавления курьера"]);
        }
    }
}
?>