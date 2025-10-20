<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../db.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

$action = $data["action"] ?? '';

if ($action === 'register') {
    $login = $data["login"] ?? '';
    $password_input = $data["password"] ?? '';
    $full_name = $data["full_name"] ?? '';
    $phone = $data["phone"] ?? '';
    $email = $data["email"] ?? '';
    $address = $data["address"] ?? '';

    if (!$login || !$password_input || !$email) {
        echo json_encode(["success" => false, "message" => "Не заполнены обязательные поля"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Пользователь с таким логином уже существует"]);
        exit;
    }

    // Хешируем пароль
    $password_hashed = password_hash($password_input, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (login, password, full_name, phone, email, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $login, $password_hashed, $full_name, $phone, $email, $address);

    if ($stmt->execute()) {
        // Получаем ID нового пользователя
        $user_id = $stmt->insert_id;
        
        // Получаем данные нового пользователя
        $stmt = $conn->prepare("SELECT id, login, full_name, email, phone, address, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        echo json_encode([
            "success" => true, 
            "message" => "Регистрация успешна",
            "user" => [
                "id" => $user["id"],
                "login" => $user["login"],
                "full_name" => $user["full_name"],
                "email" => $user["email"],
                "phone" => $user["phone"],
                "address" => $user["address"],
                "role" => $user["role"] ?: "user"
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Ошибка базы данных: " . $stmt->error]);
    }
    exit;
}

if ($action === 'login') {
    $login = $data["login"] ?? '';
    $password = $data["password"] ?? '';

    $stmt = $conn->prepare("SELECT id, password, login, full_name, email, phone, address, role FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        echo json_encode([
            "success" => true,
            "message" => "Вход выполнен успешно",
            "user" => [
                "id" => $user["id"],
                "login" => $user["login"],
                "full_name" => $user["full_name"],
                "email" => $user["email"],
                "phone" => $user["phone"],
                "address" => $user["address"],
                "role" => $user["role"] ?: "user"
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Неверный логин или пароль"]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => "Неизвестное действие"]);
?>