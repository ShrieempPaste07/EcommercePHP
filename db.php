<?php
//update credentials as needed
define('DB_HOST', 'localhost:3306'); // change to your database host
define('DB_USER', 'root'); // change to your database user(usual default is root)
define('DB_PASS', '061123'); // change to your database password
define('DB_NAME', 'gamehaven'); // change to your database name

//establish connection to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

//insert data to the users table
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

    // bind all as strings
    $stmt->bind_param('ssssssssss', $data['first_name'], $data['last_name'], $data['email'], $data['phone_number'], $data['birth_date'], $data['address'], $data['username'], $data['user_password'], $data['user_type'], $img);
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
//insert product data to the products table
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

    $stmt->bind_param('sssss', $data['product_name'], $data['product_category'], $data['product_description'], $data['product_price'], $img);
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
//get users multiple
function get_users($limit = 100)
{
    global $conn;
    $limit = (int)$limit;
    $sql = "SELECT user_id, first_name, last_name, email, phone_number, birth_date, address, username, user_type FROM users ORDER BY user_id DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param('i', $limit);
    if (!$stmt->execute()) {
        $stmt->close();
        return [];
    }
    $res = $stmt->get_result();
    if (!$res) {
        $stmt->close();
        return [];
    }
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}
//Login validation
function validate_user($username, $password)
{
    global $conn;
    $sql = "SELECT user_id, user_password FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;
    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) { $stmt->close(); return null; }
    $res = $stmt->get_result();
    $row = $res ? $row = $res->fetch_assoc() : null;
    $stmt->close();

    if (!$row) return null;

    // <-- THIS IS THE FIX
    if (password_verify($password, $row['user_password'])) {
        return (int)$row['user_id']; // login success
    }

    return null; // invalid password
}


// Get single user by id
function get_user($user_id)
{
    global $conn;
    $sql = "SELECT user_id, first_name, last_name, email, phone_number, birth_date, address, username, user_type FROM users WHERE user_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;
    $stmt->bind_param('i', $user_id);
    if (!$stmt->execute()) { $stmt->close(); return null; }
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row;
}

// Update user (if $file provided and upload OK, update image too)
function update_user($user_id, $data, $file = null)
{
    global $conn;
    $parts = [];
    $params = [];
    $types = '';

    // Build set clause and params
    $fields = ['first_name','last_name','email','phone_number','birth_date','address','username','user_password','user_type'];
    foreach ($fields as $f) {
        if (isset($data[$f])) {
            $parts[] = "$f = ?";
            $params[] = $data[$f];
            $types .= 's';
        }
    }

    $img = null;
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $img = file_get_contents($file['tmp_name']);
        $parts[] = "user_image = ?";
        $params[] = $img;
        $types .= 'b';
    }

    if (empty($parts)) return ['ok'=>false, 'error'=>'No data to update'];

    $sql = "UPDATE users SET " . implode(', ', $parts) . " WHERE user_id = ?";
    $params[] = $user_id;
    $types .= 'i';

    $stmt = $conn->prepare($sql);
    if (!$stmt) return ['ok'=>false, 'error'=>$conn->error];//return if error occurs

    // bind dynamically
    $bind_names[] = $types;
    for ($i=0;$i<count($params);$i++) { $bind_name = 'bind'.$i; $$bind_name = $params[$i]; $bind_names[] = &$$bind_name; }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    $res = $stmt->execute();
    if (!$res) { $err = $stmt->error; $stmt->close(); return ['ok'=>false, 'error'=>$err]; }
    $stmt->close();
    return ['ok'=>true];//return if success
}
//delete user
function delete_user($user_id)
{
    global $conn;
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return ['ok'=>false, 'error'=>$conn->error];
    $stmt->bind_param('i', $user_id);
    $res = $stmt->execute();
    if (!$res) { $err = $stmt->error; $stmt->close(); return ['ok'=>false, 'error'=>$err]; }
    $stmt->close();
    return ['ok'=>true];
}

//get multiple products limit is as shown
function get_products($limit = 100)
{
    global $conn;
    $limit = (int)$limit;
    $sql = "SELECT product_id, product_name, product_category, product_description, product_price FROM products ORDER BY product_id DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param('i', $limit);
    if (!$stmt->execute()) { $stmt->close(); return []; }
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
}

//search for products by name or category return products matching query
function search_products($q, $limit = 100)
{
    global $conn;
    $limit = (int)$limit;
    $like = '%' . $q . '%';
    $sql = "SELECT product_id, product_name, product_category, product_description, product_price FROM products WHERE product_name LIKE ? OR product_category LIKE ? ORDER BY product_id DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param('ssi', $like, $like, $limit);
    if (!$stmt->execute()) { $stmt->close(); return []; }
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
}
//returns single product
function get_product($product_id)
{
    global $conn;
    $sql = "SELECT product_id, product_name, product_category, product_description, product_price FROM products WHERE product_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;
    $stmt->bind_param('i', $product_id);
    if (!$stmt->execute()) { $stmt->close(); return null; }
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row;
}
//update product (if $file provided and upload OK, update image too)
function update_product($product_id, $data, $file = null)
{
    global $conn;
    $parts = [];
    $params = [];
    $types = '';

    $fields = ['product_name','product_category','product_description','product_price'];
    foreach ($fields as $f) {
        if (isset($data[$f])) { $parts[] = "$f = ?"; $params[] = $data[$f]; $types .= 's'; }
    }

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $img = file_get_contents($file['tmp_name']);
        $parts[] = "product_image = ?";
        $params[] = $img;
        $types .= 'b';
    }

    if (empty($parts)) return ['ok'=>false, 'error'=>'No data to update'];

    $sql = "UPDATE products SET " . implode(', ', $parts) . " WHERE product_id = ?";
    $params[] = $product_id; $types .= 'i';

    $stmt = $conn->prepare($sql);
    if (!$stmt) return ['ok'=>false, 'error'=>$conn->error];//return if error occurs

    $bind_names[] = $types;
    for ($i=0;$i<count($params);$i++) { $bind_name = 'bparam'.$i; $$bind_name = $params[$i]; $bind_names[] = &$$bind_name; }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);

    $res = $stmt->execute();
    if (!$res) { $err = $stmt->error; $stmt->close(); return ['ok'=>false, 'error'=>$err]; }
    $stmt->close();
    return ['ok'=>true];//return if success
}
//delete product
function delete_product($product_id)
{
    global $conn;
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return ['ok'=>false, 'error'=>$conn->error];
    $stmt->bind_param('i', $product_id);
    $res = $stmt->execute();
    if (!$res) { $err = $stmt->error; $stmt->close(); return ['ok'=>false, 'error'=>$err]; }
    $stmt->close();
    return ['ok'=>true];
}


?>
