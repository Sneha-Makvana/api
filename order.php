<?php
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
include "conn.php";
validateToken($authHeader);
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $query = "SELECT orders.order_id, product.product_name, product.price, cart.qty, (product.price * cart.qty) AS total_price,
                  orders.order_date
                  FROM orders
                  INNER JOIN cart on orders.cart_id = cart.cart_id
                  INNER JOIN product on cart.product_id = product.product_id";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $orders = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        echo json_encode($orders);
    } else {
        echo json_encode(["status" => "false", "message" => "No order found"]);
    }
} else {
    echo json_encode(["status" => "false", "message" => "Invalid Request method"]);
}

// if ($_SERVER['REQUEST_METHOD'] === 'GET') {

//     if (isset($_GET['order_id'])) {
//         $order_id = intval($_GET['order_id']);

//         $query = "SELECT orders.order_id, product.product_name, product.price, cart.qty, 
//                          (product.price * cart.qty) AS total_price, orders.order_date
//                   FROM orders
//                   INNER JOIN cart ON orders.cart_id = cart.cart_id
//                   INNER JOIN product ON cart.product_id = product.product_id
//                   WHERE orders.order_id = $order_id";

//         $result = mysqli_query($conn, $query);

//         if (mysqli_num_rows($result) > 0) {
//             $order = mysqli_fetch_assoc($result); 
//             echo json_encode($order);
//         } else {
//             echo json_encode(["status" => "false", "message" => "Order not found"]);
//         }
//     } else {
//         echo json_encode(["status" => "false", "message" => "Missing order_id parameter"]);
//     }
// } else {
//     echo json_encode(["status" => "false", "message" => "Invalid Request method"]);
// }