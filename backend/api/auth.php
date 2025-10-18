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
    $password = password_hash($data["password"] ?? '', PASSWORD_DEFAULT);
    $full_name = $data["full_name"] ?? '';
    $phone = $data["phone"] ?? '';
    $email = $data["email"] ?? '';
    $address = $data["address"] ?? '';

    if (!$login || !$password || !$email) {
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

    $stmt = $conn->prepare("INSERT INTO users (login, password, full_name, phone, email, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $login, $password, $full_name, $phone, $email, $address);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    }
    exit;
}

if ($action === 'login') {
    $login = $data["login"] ?? '';
    $password = $data["password"] ?? '';

    $stmt = $conn->prepare("SELECT id, password, full_name, email, role FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "user" => [
                "id" => $user["id"],
                "full_name" => $user["full_name"],
                "email" => $user["email"],
                "role" => $user["role"]
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Неверный логин или пароль"]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => "Unknown action"]);
