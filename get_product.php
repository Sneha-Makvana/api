<?php
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
include "conn.php";
validateToken($authHeader);
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['product_name']) && isset($_POST['price']) && isset($_FILES['image'])) {

        $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
        $price = floatval($_POST['price']);

        $image = $_FILES['image'];

        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image["name"]);

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "gif", "webp");

        if (!in_array($imageFileType, $allowed_types)) {
            echo json_encode(["error" => "Invalid image file type. Only JPG, JPEG, PNG, and GIF are allowed."]);
            exit();
        }

        if (move_uploaded_file($image["tmp_name"], $target_file)) {

            $query = "INSERT INTO product(product_name, price, image) VALUES ('$product_name', '$price', '$target_file')";

            if (mysqli_query($conn, $query)) {
                echo json_encode([
                    "status" => "success", "message" => "Product added successfully.",
                    "product_id" => mysqli_insert_id($conn),
                    "image" => $target_file
                ]);
            } else {
                echo json_encode(["status" => "false", "message" => "Failed to add product."]);
            }
        } else {
            echo json_encode(["status" => "false", "message" => "Failed to upload image."]);
        }
    } else {
        echo json_encode(["status" => "false", "message" => "Missing name, price, or image."]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['product_id'])) {

        $product_id = intval($_GET['product_id']);
        $query = "SELECT * FROM product WHERE product_id = $product_id";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $product = mysqli_fetch_assoc($result);
            echo json_encode($product);
        } else {
            echo json_encode(["status" => "false", "message" => "Product not Found"]);
        }
    } else {
        $query = "SELECT * from product";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $product = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $product[] = $row;
            }
            echo json_encode($product);
        } else {
            echo json_encode(["status" => "false", "message" => "Failed to fetch products"]);
        }
    }
}
