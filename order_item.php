<?php
include "conn.php";
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    handlePlaceOrder($conn);
}

function handlePlaceOrder($conn) {
    
    if (isset($_POST['cart_id'])) {
        $cart_id = intval($_POST['cart_id']);
        
        $cartSql = "SELECT * FROM cart WHERE id = $cart_id";
        $cartResult = mysqli_query($conn, $cartSql);

        if ($cartResult && mysqli_num_rows($cartResult) > 0) {

            $orderSql = "INSERT INTO orders (cart_id, order_date) VALUES ($cart_id, NOW())";

            if (mysqli_query($conn, $orderSql)) {
                echo json_encode(["status" => "success", "message" => "Order placed successfully."]);
            } else {
                echo json_encode(["status" => "fail", "message" => "Failed to place the order.", "error" => mysqli_error($conn)]);
            }

            
            $clearCartSql = "DELETE FROM cart WHERE id = $cart_id";
            mysqli_query($conn, $clearCartSql);
        } else {
            echo json_encode(["status" => "fail", "message" => "No items in the cart."]);
        }
    } else {
        echo json_encode(["status" => "fail", "message" => "Missing cart_id in the request."]);
    }
}
?>
