<?php
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

include "conn.php";
validateToken($authHeader);

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['price'])) {

        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);

        $query = "UPDATE products set name = '$name', price = $price where id = $id";
        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "Product update successfully"]);
        } else {
            echo json_encode(["status" => "false", "message" => "failed to update product"]);
        }
    } elseif (isset($_POST['name']) && isset($_POST['price'])) {

        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $price = floatval($_POST['price']);

        $query = "INSERT INTO products (name, price) values ('$name', '$price')";
        if (mysqli_query($conn, $query)) {

            echo json_encode(["status" => "Success", "message" => "Products added successfully", "id" => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(["status" => "False", "message" => "Failed to add products"]);
        }
    } else {
        // echo json_encode(["error" => "Invalid input"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {

        $id = intval($_GET['id']);
        $query = "SELECT * FROM products WHERE id = $id";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $product = mysqli_fetch_assoc($result);
            echo json_encode($product);
        } else {
            echo json_encode(["status" => "false", "message" => "Product not Found"]);
        }
    } else {
        $query = "SELECT * from products";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $products = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
            echo json_encode($products);
        } else {
            echo json_encode(["status" => "false", "message" => "Failed to fetch products"]);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $query = "DELETE FROM products where id = $id";

        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "Product Delete Successfully"]);
        } else {
            echo json_encode(["status" => "false", "message" => "failed to delete product"]);
        }
    } else {
        echo json_encode(["status" => "false", "message" => "product ID is required for deletion"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    if (isset($_REQUEST['id']) && isset($_REQUEST['name']) && isset($_REQUEST['price'])) {
        $id = intval($_REQUEST['id']);
        $name = mysqli_real_escape_string($conn, $_REQUEST['name']);
        $price = floatval($_REQUEST['price']);

        $query = "UPDATE products SET name = '$name', price = $price WHERE id = $id";
        if (mysqli_query($conn, $query)) {
            echo json_encode(["status" => "success", "message" => "Product updated successfully"]);
        } else {
            echo json_encode(["status" => "false", "error" => "Failed to update product"]);
        }
    } else {
        echo json_encode(["error" => "Invalid input. ID, name, and price are required"]);
    }
}
