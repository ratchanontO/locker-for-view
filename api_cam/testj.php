<?php
include '../connectdb.php';
require_once '../config.php';
$token = $api_keys['apikey'];
$data = json_decode(file_get_contents("php://input"), true);

$key = $data["key"];
$status = 1;

if(!$key || $key != $token){
    echo json_encode(["message" => "key is null or worng key"]);
    exit;
}

$stmt = $conn->prepare("SELECT locker_number, take_photo FROM dataforlog WHERE take_photo = ?");
$stmt->bind_param("i", $status);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo json_encode(["locker_number" => $row['locker_number'], "take_photo" => true]);
    }else{
        echo json_encode(["take_photo" => false]);
    }
}else{
    echo json_encode(["message" => "db is down"]);
}

?>