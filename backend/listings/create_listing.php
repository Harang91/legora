<?php
require_once __DIR__ . '/../shared/init.php';

// Csak POST kérést engedünk
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    errorResponse("Csak POST kérés engedélyezett");
}

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    errorResponse("Bejelentkezés szükséges");
}

// Adatok fogadása
// --- 1. MÓDOSÍTÁS: A név (name) átvétele ---
$name = $_POST['name'] ?? ''; 
$item_type = $_POST['item_type'] ?? null;
$item_id = $_POST['item_id'] ?? null;
$price = $_POST['price'] ?? null;
$quantity = $_POST['quantity'] ?? 1;
$item_condition = $_POST['item_condition'] ?? 'used';
$description = $_POST['description'] ?? '';

// Validálás
if (!$item_type || !$item_id || !$price) {
    http_response_code(422);
    errorResponse("Hiányzó adatok (típus, azonosító, ár kötelező)");
}

// FÁJL FELTÖLTÉS KEZELÉSE (Ez a rész változatlan)
$custom_image_path = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    $fileType = $_FILES['image']['type'];
    
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg', 'webp'];

    if (in_array($fileExtension, $allowedfileExtensions)) {
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = __DIR__ . '/../uploads/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        $dest_path = $uploadFileDir . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $custom_image_path = 'uploads/' . $newFileName;
        } else {
            errorResponse("Hiba a fájl mozgatásakor. Írási jogok?");
        }
    } else {
        errorResponse("Csak képfájlok engedélyezettek (jpg, png, webp)!");
    }
}

try {
    // --- 2. MÓDOSÍTÁS: 'name' hozzáadása az SQL parancshoz ---
    $sql = "INSERT INTO listings (user_id, name, item_type, item_id, price, quantity, item_condition, description, custom_image_url) 
            VALUES (:user_id, :name, :item_type, :item_id, :price, :quantity, :item_condition, :description, :custom_image_url)";
    
    $stmt = $pdo->prepare($sql);
    
    // --- 3. MÓDOSÍTÁS: 'name' paraméter átadása ---
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':name' => $name, // <--- Itt adjuk át az új adatot
        ':item_type' => $item_type,
        ':item_id' => $item_id,
        ':price' => $price,
        ':quantity' => $quantity,
        ':item_condition' => $item_condition,
        ':description' => $description,
        ':custom_image_url' => $custom_image_path
    ]);

    successResponse("Hirdetés sikeresen létrehozva", ["id" => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    http_response_code(500);
    errorResponse("Adatbázis hiba: " . $e->getMessage());
}