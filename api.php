<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'restaurant_db';
$user = getenv('DB_USER') ?: 'restaurant_user';
$pass = getenv('DB_PASS') ?: 'restaurant_pass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$action = $_GET['action'] ?? '';

switch($action) {
    case 'categories':
        getCategories($pdo);
        break;
    case 'products':
        getProducts($pdo);
        break;
    case 'product':
        getProduct($pdo);
        break;
    case 'orders':
        handleOrders($pdo);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}

function getCategories($pdo) {
    $stmt = $pdo->query("SELECT item_id as id, name, price FROM MENU_ITEMS ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);
}

function getProducts($pdo) {
    $search = $_GET['search'] ?? '';
    $categoryId = $_GET['category_id'] ?? null;

    $sql = "SELECT item_id as id, name, CONCAT('Delicious ', name, ' prepared fresh.') as description, price, '' as image_name, 1 as category_id, 'Main Dishes' as category_name FROM MENU_ITEMS WHERE 1=1";
    $params = [];

    if ($search) {
        $sql .= " AND name LIKE ?";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
}

function getProduct($pdo) {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT item_id as id, name, CONCAT('Delicious ', name, ' prepared fresh.') as description, price, '' as image_name, 1 as category_id, 'Main Dishes' as category_name FROM MENU_ITEMS WHERE item_id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
}

function handleOrders($pdo) {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        getOrders($pdo);
    } elseif ($method === 'POST') {
        createOrder($pdo);
    } elseif ($method === 'PUT') {
        updateOrder($pdo);
    }
}

function getOrders($pdo) {
    $stmt = $pdo->query("SELECT * FROM ORDERS ORDER BY order_date DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($orders);
}

function createOrder($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }

    try {
        $pdo->beginTransaction();

        // Insert user if not exists
        $stmt = $pdo->prepare("INSERT IGNORE INTO USERS (name, email, phone) VALUES (?, ?, ?)");
        $stmt->execute([$data['name'], $data['email'], $data['phone']]);

        $userId = $pdo->lastInsertId();
        if ($userId == 0) {
            $stmt = $pdo->prepare("SELECT user_id FROM USERS WHERE email = ?");
            $stmt->execute([$data['email']]);
            $userId = $stmt->fetchColumn();
        }

        // Create order
        $stmt = $pdo->prepare("INSERT INTO ORDERS (user_id, order_type, total_amount, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$userId, $data['order_type'], $data['total_amount']]);
        $orderId = $pdo->lastInsertId();

        // Insert order items
        foreach ($data['items'] as $item) {
            $stmt = $pdo->prepare("INSERT INTO ORDER_ITEMS (order_id, item_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        }

        // Insert delivery info if delivery
        if ($data['order_type'] === 'delivery') {
            $stmt = $pdo->prepare("INSERT INTO DELIVERY (order_id, address, city, state, zip_code, estimated_time) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$orderId, $data['address'], $data['city'] ?? '', $data['state'] ?? '', $data['zip_code'] ?? '', $data['estimated_time']]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'order_id' => $orderId]);

    } catch(Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create order: ' . $e->getMessage()]);
    }
}

function updateOrder($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $orderId = $data['order_id'] ?? 0;
    $status = $data['status'] ?? '';

    if (!$orderId || !$status) {
        http_response_code(400);
        echo json_encode(['error' => 'Order ID and status required']);
        return;
    }

    $stmt = $pdo->prepare("UPDATE ORDERS SET status = ? WHERE order_id = ?");
    $stmt->execute([$status, $orderId]);

    echo json_encode(['success' => true]);
}
?>