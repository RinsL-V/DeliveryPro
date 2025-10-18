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
        // Получение статистики пользователей
        $result = $conn->query("
            SELECT 
                COUNT(*) as total_users,
                SUM(role = 'admin') as admin_count,
                SUM(role = 'user') as user_count,
                SUM(role = 'manager') as manager_count
            FROM users
        ");
        $stats = $result->fetch_assoc();
        
        // Последние 5 пользователей
        $result = $conn->query("SELECT id, login, full_name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
        $recent_users = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            "stats" => $stats,
            "recent_users" => $recent_users
        ]);
        break;

    default:
        echo json_encode(["error" => "Метод не поддерживается"]);
}
?>