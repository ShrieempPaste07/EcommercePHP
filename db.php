<?php
//update credentials as needed
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root');
define('DB_PASS', '061123');
define('DB_NAME', 'gamehaven');

//establish connection to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

//insert user data
function insert_user($data, $file)
{
    global $conn;
    $sql = "INSERT INTO users (first_name, last_name, email, phone_number, birth_date, address, username, user_password, user_type, user_image)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return ['ok'=>false, 'error'=>$conn->error];

    $img = null;
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $img = file_get_contents($file['tmp_name']);
    }

    $stmt->bind_param('ssssssssss',
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['phone_number'],
        $data['birth_date'],
        $data['address'],
        $data['username'],
        $data['user_password'],
        $data['user_type'],
        $img
    );

    $res = $stmt->execute();
    if (!$res) {
        $err = $stmt->error;
        $stmt->close();
        return ['ok'=>false, 'error'=>$err];
    }

    $insert_id = $conn->insert_id;
    $stmt->close();
    return ['ok'=>true, 'id'=>$insert_id];
}


//insert product data
function insert_product($data, $file)
{
    global $conn;
    $sql = "INSERT INTO products (product_name, product_category, product_description, product_price, product_image)
        VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return ['ok'=>false, 'error'=>$conn->error];

    $img = null;
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $img = file_get_contents($file['tmp_name']);
    }

    $stmt->bind_param(
        'sssss',
        $data['product_name'],
        $data['product_category'],
        $data['product_description'],
        $data['product_price'],
        $img
    );

    $res = $stmt->execute();
    if (!$res) {
        $err = $stmt->error;
        $stmt->close();
        return ['ok'=>false, 'error'=>$err];
    }

    $insert_id = $conn->insert_id;
    $stmt->close();
    return ['ok'=>true, 'id'=>$insert_id];
}


//GET MULTIPLE USERS
function get_users($limit = 100)
{
    global $conn;
    $limit = (int)$limit;

    $sql = "SELECT user_id, first_name, last_name, email, phone_number, birth_date, address, username, user_type 
            FROM users 
            ORDER BY user_id DESC 
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];

    $stmt->bind_param('i', $limit);

    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    $stmt->close();
    return $rows;
}


//LOGIN (NORMAL USERS)
function validate_user($username, $password)
{
    global $conn;
    $sql = "SELECT user_id, user_password FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) return null;

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    if (!$row) return null;

    return password_verify($password, $row['user_password']) ? (int)$row['user_id'] : null;
}


//GET SINGLE USER
function get_user($user_id)
{
    global $conn;
    $sql = "SELECT user_id, first_name, last_name, email, phone_number, birth_date, address, username, user_type 
            FROM users 
            WHERE user_id = ? LIMIT 1";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;

    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row;
}


//GET PRODUCTS ✔ FIXED (NO DUPLICATE)
function get_products($limit = 100)
{
    global $conn;
    $limit = (int)$limit;

    $sql = "SELECT product_id, product_name, product_category, product_description, product_price, product_image
            FROM products
            ORDER BY product_id DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];

    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $res = $stmt->get_result();

    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
}


//GET SINGLE PRODUCT
function get_product_by_id($id)
{
    global $conn;
    $id = (int)$id;

    $sql = "SELECT product_id, product_name, product_category, product_description, product_price, product_image
            FROM products WHERE product_id = ? LIMIT 1";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();

    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row;
}

function update_product($product_id, $data, $file = null) {
    global $conn;

    // Sanitize values
    $name = $conn->real_escape_string($data['product_name']);
    $category = $conn->real_escape_string($data['product_category']);
    $description = $conn->real_escape_string($data['product_description']);
    $price = floatval($data['product_price']);

    $query = "";
    $params = [];

    // If image exists & uploaded
    if ($file && isset($file['tmp_name']) && $file['tmp_name'] !== "") {
        
        // Read file binary
        $imgData = file_get_contents($file['tmp_name']);
        $imgData = base64_encode($imgData); // for SQL insert

        $query = "UPDATE products SET 
                    product_name = ?, 
                    product_category = ?, 
                    product_description = ?, 
                    product_price = ?, 
                    product_image = FROM_BASE64(?)
                  WHERE product_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $name, $category, $description, $price, $imgData, $product_id);
    } else {
        // No new image → update everything except image
        $query = "UPDATE products SET 
                    product_name = ?, 
                    product_category = ?, 
                    product_description = ?, 
                    product_price = ?
                  WHERE product_id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssdi", $name, $category, $description, $price, $product_id);
    }

    if ($stmt->execute()) {
        return ['ok' => true];
    }

    return [
        'ok' => false,
        'error' => $stmt->error
    ];
}

// DELETE PRODUCT
function delete_product($product_id) {
    global $conn;
    $product_id = (int)$product_id;

    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    if (!$stmt) {
        return ['ok' => false, 'error' => $conn->error];
    }

    $stmt->bind_param("i", $product_id);
    $res = $stmt->execute();
    $stmt->close();

    if ($res) {
        return ['ok' => true];
    } else {
        return ['ok' => false, 'error' => $conn->error];
    }
}

?>
