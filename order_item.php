<?php
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
include "conn.php";
validateToken($authHeader);
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
    
if ($method == "POST") {

    handlePlaceOrder($conn);
} elseif ($method == "GET") {
    getAllOrders($conn);
}

function handlePlaceOrder($conn)
{
    if (isset($_POST['cart_id'])) {
        $cart_id = intval($_POST['cart_id']);

        $cartSql = "SELECT * FROM cart WHERE cart_id = $cart_id";
        $cartResult = mysqli_query($conn, $cartSql);

        if ($cartResult && mysqli_num_rows($cartResult) > 0) {

            $orderSql = "INSERT INTO orders (cart_id, order_date) VALUES ($cart_id, NOW())";

            if (mysqli_query($conn, $orderSql)) {
                echo json_encode(["status" => "success", "message" => "Order placed successfully."]);
            } else {
                echo json_encode(["status" => "fail", "message" => "Failed to place the order.", "error" => mysqli_error($conn)]);
            }

            $clearCartSql = "DELETE FROM cart WHERE cart_id = $cart_id";
            mysqli_query($conn, $clearCartSql);
        } else {
            echo json_encode(["status" => "fail", "message" => "No items in the cart."]);
        }
    } else {
        echo json_encode(["status" => "fail", "message" => "Missing cart_id in the request."]);
    }
}

function getAllOrders($conn)
{
    $query = "SELECT 
                  orders.order_id, 
                  product.product_name, 
                  product.price, 
                  cart.qty, 
                  (product.price * cart.qty) AS total_price, 
                  orders.order_date
              FROM orders
              INNER JOIN cart ON orders.cart_id = cart.cart_id
              INNER JOIN product ON cart.product_id = product.product_id
              ORDER BY orders.order_date DESC";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $orders = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }

        echo json_encode(["status" => "success", "data" => $orders]);
    } else {
        echo json_encode(["status" => "fail", "message" => "No orders found."]);
    }
}
