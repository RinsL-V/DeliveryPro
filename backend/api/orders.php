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

switch ($method) {
    case 'GET':
        $user_id = $_GET['user_id'] ?? null;
        $admin = $_GET['admin'] ?? false;

        if ($admin) {
            $stmt = $conn->prepare("
                SELECT o.*, u.full_name as user_name, u.email as user_email, u.phone as user_phone 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC
            ");
            $stmt->execute();
            $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } elseif ($user_id) {
            $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            echo json_encode(["error" => "User ID is required"]);
            exit;
        }
        echo json_encode($orders);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $data['user_id'];
        $from_address = $data['from_address'];
        $to_address = $data['to_address'];
        $package_type = $data['package_type'];
        $weight = $data['weight'];
        $dimensions = $data['dimensions'] ?? null;
        $description = $data['description'] ?? null;

        $stmt = $conn->prepare("INSERT INTO orders (user_id, from_address, to_address, package_type, weight, dimensions, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdss", $user_id, $from_address, $to_address, $package_type, $weight, $dimensions, $description);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "order_id" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Ошибка при создании заказа"]);
        }
        break;

    default:
        echo json_encode(["error" => "Метод не поддерживается"]);
}
?>