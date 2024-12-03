<?php
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

include "conn.php";
validateToken($authHeader);

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['product_id']) && isset($_POST['qty'])) {

        $product_id = intval($_POST['product_id']);
        $qty = intval($_POST['qty']);

        $query = "INSERT INTO cart (product_id, qty) values ('$product_id', '$qty')";
        if (mysqli_query($conn, $query)) {

            echo json_encode(["status" => "Success", "message" => "Cart added successfully", "id" => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(["status" => "False", "message" => "Failed to add cart"]);
        }
    } else {
        // echo json_encode(["error" => "Invalid input"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $query = "SELECT cart.qty, product.product_name, product.price, 
                     (product.price * cart.qty) AS total_price
              FROM cart
              INNER JOIN product ON cart.product_id = product.product_id";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $cart = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $cart[] = $row;
        }

        echo json_encode(["status" => "true", "data" => $cart]);
    } else {
        echo json_encode(["status" => "false", "message" => "No records found"]);
    }
}



