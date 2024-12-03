<?php
header("Content-Type: application/json");
include "conn.php";

$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $query = "SELECT * FROM products WHERE id = $id";
            $result = mysqli_query($conn, $query);
            $data = mysqli_fetch_assoc($result);
            echo json_encode($data ?: ["message" => "Product not found"]);
        } else {
            $query = "SELECT * FROM products";
            $result = mysqli_query($conn, $query);
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            echo json_encode($data);
        }
        break;

    case 'POST':
        if (isset($input['name']) && isset($input['price'])) {
            $name = mysqli_real_escape_string($conn, $input['name']);
            $price = floatval($input['price']);
            $query = "INSERT INTO products (name, price) VALUES ('$name', $price)";
            if (mysqli_query($conn, $query)) {
                echo json_encode(["message" => "Product added successfully", "id" => mysqli_insert_id($conn)]);
            } else {
                echo json_encode(["error" => "Failed to add product"]);
            }
        } else {
            echo json_encode(["error" => "Invalid input"]);
        }
        break;

    case 'PUT':
        if (isset($_GET['id']) && isset($input['name']) && isset($input['price'])) {
            $id = intval($_GET['id']);
            $name = mysqli_real_escape_string($conn, $input['name']);
            $price = floatval($input['price']);
            $query = "UPDATE products SET name = '$name', price = $price WHERE id = $id";
            if (mysqli_query($conn, $query)) {
                echo json_encode(["message" => "Product updated successfully"]);
            } else {
                echo json_encode(["error" => "Failed to update product"]);
            }
        } else {
            echo json_encode(["error" => "Invalid input"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $query = "DELETE FROM products WHERE id = $id";
            if (mysqli_query($conn, $query)) {
                echo json_encode(["message" => "Product deleted successfully"]);
            } else {
                echo json_encode(["error" => "Failed to delete product"]);
            }
        } else {
            echo json_encode(["error" => "Invalid input"]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
        break;
}
