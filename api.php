<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$host = "localhost";
$db_name = "apitest";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $conn->exec("CREATE DATABASE IF NOT EXISTS $db_name");
    $conn->exec("USE $db_name");
    
    // Create tables if not exist
    $conn->exec("CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        author VARCHAR(100) NOT NULL,
        image_url VARCHAR(255) DEFAULT 'https://picsum.photos/200/300',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    $conn->exec("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        content TEXT NOT NULL,
        author VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
    )");
    
    // Add sample data if tables are empty
    $stmt = $conn->query("SELECT COUNT(*) FROM posts");
    if ($stmt->fetchColumn() == 0) {
        $conn->exec("INSERT INTO posts (title, content, author, image_url) VALUES
            ('أول منشور', 'محتوى المنشور الأول', 'أحمد', 'https://picsum.photos/id/1/200/300'),
            ('ثاني منشور', 'محتوى المنشور الثاني', 'محمد', 'https://picsum.photos/id/2/200/300'),
            ('ثالث منشور', 'محتوى المنشور الثالث', 'فاطمة', 'https://picsum.photos/id/3/200/300')");
        
        $conn->exec("INSERT INTO comments (post_id, content, author) VALUES
            (1, 'تعليق رائع على المنشور الأول', 'سارة'),
            (1, 'أنا أتفق مع هذا المنشور', 'خالد'),
            (2, 'معلومات مفيدة، شكراً لك', 'ليلى'),
            (3, 'أحببت هذا المنشور كثيراً', 'عمر')");
    }
} catch(PDOException $e) {
    echo json_encode(["message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// The first element will be empty string and the second will be 'api'
$resource = $uri[2] ?? null; // 'posts' or 'comments'
$id = $uri[3] ?? null;

switch($method) {
    case 'GET':
        if ($resource == 'posts' && $id && isset($uri[4]) && $uri[4] == 'comments') {
            getPostComments($conn, $id);
        } elseif ($id) {
            getOne($conn, $resource, $id);
        } elseif(isset($_GET['search'])) {
            search($conn, $resource, $_GET['search']);
        } else {
            getAll($conn, $resource);
        }
        break;
    case 'POST':
        create($conn, $resource);
        break;
    case 'PUT':
        if ($id) {
            update($conn, $resource, $id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID is required for update"]);
        }
        break;
    case 'DELETE':
        if ($id) {
            delete($conn, $resource, $id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID is required for delete"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

function getAll($conn, $table) {
    $sql = "SELECT * FROM $table";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function getOne($conn, $table, $id) {
    $sql = "SELECT * FROM $table WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}

function create($conn, $table) {
    $input = json_decode(file_get_contents('php://input'), true);
    $fields = implode(", ", array_keys($input));
    $placeholders = ":" . implode(", :", array_keys($input));
    $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    foreach($input as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->execute();
    echo json_encode(["message" => "Record created successfully", "id" => $conn->lastInsertId()]);
}

function update($conn, $table, $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    $fields = "";
    foreach($input as $key => $value) {
        $fields .= "$key=:$key,";
    }
    $fields = rtrim($fields, ",");
    $sql = "UPDATE $table SET $fields WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    foreach($input as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->execute();
    echo json_encode(["message" => "Record updated successfully"]);
}

function delete($conn, $table, $id) {
    $sql = "DELETE FROM $table WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    echo json_encode(["message" => "Record deleted successfully"]);
}

function search($conn, $table, $keyword) {
    $sql = "SELECT * FROM $table WHERE title LIKE :keyword OR content LIKE :keyword";
    if ($table == 'comments') {
        $sql = "SELECT * FROM $table WHERE content LIKE :keyword";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':keyword', "%$keyword%");
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function getPostComments($conn, $postId) {
    $sql = "SELECT * FROM comments WHERE post_id = :post_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':post_id', $postId);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}