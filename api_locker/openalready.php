<?php
    include '../connectdb.php';
    require_once '../config.php';
    $tokenapi = $api_keys['apikey'];

    $data = json_decode(file_get_contents("php://input"), true);
    $key = $data["key"] ?? null;
    $locker_number = $data["locker_number"] ?? null;

    if (!$key || !$locker_number) {
        echo json_encode(["status" => "fail", "message" => "ข้อมูลไม่ครบ"]);
        exit;
    }

    if ($key === $tokenapi) {
        $stmt = $conn->prepare("UPDATE locker_status SET take_open = 0 WHERE number_locker = ?");
        $stmt->bind_param("i",  $locker_number);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success","message" => "done $locker_number"]);
        }else{
            echo json_encode(["status" => "fail","message" => "can't"]);
        }
    }
?>
