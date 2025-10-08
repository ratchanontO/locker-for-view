<?php
include '../connectdb.php';
require_once '../config.php';
$token = $api_keys['apikey'];

// บังคับไม่ให้ cache
header("Content-Type: application/json");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");


$data = json_decode(file_get_contents("php://input"), true);
$key = $data["key"];
$status = 1;

if(!$key || $key != $token){
    echo json_encode(["message" => "key is null or wrong key"]);
    exit;
}

$stmt = $conn->prepare("SELECT locker_number, take_photo FROM dataforlog WHERE take_photo = ?");
$stmt->bind_param("i", $status);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $lockers = [];
    while ($row = $result->fetch_assoc()) {
        $lockers[] = [
            "locker_number" => $row['locker_number'],
            "take_photo" => true
        ];
    }
    
    if (count($lockers) > 0) {
        echo json_encode($lockers);
    } else {
        echo json_encode(["take_photo" => false]);
    }
} else {
    echo json_encode(["message" => "db is down"]);
}
?>
