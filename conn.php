<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "rest_api_example";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<?php

function validateToken($authHeader, $valid_token = "h12jkdsh23sdsssd435")
{
    if ($authHeader == '') {
        echo json_encode(['status' => 'fail', 'message' => 'Authorization header is missing.']);
        exit();
    }

    if ($authHeader !== $valid_token) {
        echo json_encode(['status' => 'fail', 'message' => 'Invalid token. Access denied.']);
        exit();
    }
}

?>
