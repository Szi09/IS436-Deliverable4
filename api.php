<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$host = getenv('DB_HOST') ?: 'mysql';
$dbname = getenv('DB_NAME') ?: 'restaurant_db';
$user = getenv('DB_USER') ?: 'restaurant_user';
$pass = getenv('DB_PASS') ?: 'restaurant_pass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$action = $_GET['action'] ?? '';

switch($action) {
    case 'products':
        getProducts($pdo);
        break;

    case 'product':
        getProduct($pdo);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}

function getProducts($pdo) {

    $sql = "SELECT 
    item_id as id,
    name,
    description,
    price,

    CASE
      WHEN name LIKE '%BBQ%' THEN 'bbq-chicken-pizza-09-2.jpg'
      WHEN name LIKE '%Caesar%' THEN '220905_DD_Chx-Caesar-Salad_051.jpg'
      WHEN name LIKE '%Fries%' THEN 'French-Fries.jpg'
      WHEN name LIKE '%Garlic%' THEN 'GarlicBread.jpg'
      WHEN name LIKE '%Greek%' THEN 'Greek-Salad.jpg'
      WHEN name LIKE '%Margherita%' THEN 'margherita-pizza.jpg'
      WHEN name LIKE '%Spaghetti%' THEN 'Spaghetti-Bolognese.jpg'
      WHEN name LIKE '%Tiramisu%' THEN 'tiramisu.jpg'
      ELSE ''
    END as image_name,

    category_id,
    category_name

    FROM MENU_ITEMS

    ORDER BY name";

    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);
}

function getProduct($pdo) {

    $id = $_GET['id'] ?? 0;

    $sql = "SELECT 
    item_id as id,
    name,
    description,
    price,

    CASE
      WHEN name LIKE '%BBQ%' THEN 'bbq-chicken-pizza-09-2.jpg'
      WHEN name LIKE '%Caesar%' THEN '220905_DD_Chx-Caesar-Salad_051.jpg'
      WHEN name LIKE '%Fries%' THEN 'French-Fries.jpg'
      WHEN name LIKE '%Garlic%' THEN 'GarlicBread.jpg'
      WHEN name LIKE '%Greek%' THEN 'Greek-Salad.jpg'
      WHEN name LIKE '%Margherita%' THEN 'margherita-pizza.jpg'
      WHEN name LIKE '%Spaghetti%' THEN 'Spaghetti-Bolognese.jpg'
      WHEN name LIKE '%Tiramisu%' THEN 'tiramisu.jpg'
      ELSE ''
    END as image_name,

    category_id,
    category_name

    FROM MENU_ITEMS
    WHERE item_id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($product);
}
?>
