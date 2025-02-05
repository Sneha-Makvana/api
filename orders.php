<?php
include "conn.php";
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    handleCreateOrder($conn);
}
function handleCreateOrder($conn)
{
    if (isset(
        $_POST['cart_id'],
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['address'],
        $_POST['phone'],
        $_POST['note'],
        $_POST['email']
    )) {

        $cart_id = intval($_POST['cart_id']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $note = mysqli_real_escape_string($conn, $_POST['note']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        $cartSql = "SELECT c.product_id, c.qty, p.price 
                    FROM cart c
                    INNER JOIN product p ON c.product_id = p.product_id
                    WHERE c.cart_id = $cart_id";
        $cartResult = mysqli_query($conn, $cartSql);

        if ($cartResult && mysqli_num_rows($cartResult) > 0) {
            $total_price = 0;
            while ($cartRow = mysqli_fetch_assoc($cartResult)) {
                $total_price += $cartRow['price'] * $cartRow['qty'];
            }

            $orderSql = "INSERT INTO orders (cart_id, order_date, first_name, last_name, address, phone, note, total, email)
                         VALUES ($cart_id, NOW(), '$first_name', '$last_name', '$address', '$phone', '$note', $total_price, '$email')";

            if (mysqli_query($conn, $orderSql)) {
                $order_id = mysqli_insert_id($conn);

                mysqli_data_seek($cartResult, 0);
                while ($cartRow = mysqli_fetch_assoc($cartResult)) {
                    $product_id = $cartRow['product_id'];
                    $qty = $cartRow['qty'];
                    $price = $cartRow['price'];
                    $total_item_price = $price * $qty;

                    $orderDetailSql = "INSERT INTO order_details (cart_id, product_id, qty, total_price)
                                       VALUES ($cart_id, $product_id, $qty, $total_item_price)";
                    mysqli_query($conn, $orderDetailSql);
                }

                $clearCartSql = "DELETE FROM cart WHERE cart_id = $cart_id";
                mysqli_query($conn, $clearCartSql);

                echo json_encode(["status" => "success", "message" => "Order placed successfully."]);
            } else {
                echo json_encode(["status" => "fail", "message" => "Failed to create order.", "error" => mysqli_error($conn)]);
            }
        } else {
            echo json_encode(["status" => "fail", "message" => "No products found in the cart."]);
        }
    } else {
        echo json_encode(["status" => "fail", "message" => "Missing required fields in the request."]);
    }
}

if ($method == "GET") {
    handleGet($conn);
}

function handleGet($conn)
{
    $sql = "SELECT * FROM order_details";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        echo json_encode(["status" => "fail", "message" => "Failed to fetch order details.", "error" => mysqli_error($conn)]);
    }
}
