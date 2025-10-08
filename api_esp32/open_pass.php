<?php
    include '../connectdb.php';
    require_once '../config.php';
    $tokenapi = $api_keys['apikey'];

    $data = json_decode(file_get_contents("php://input"), true);
    $key = $data["key"] ?? null;
    $user_id = $data["id"] ?? null;

    if (!$key) {
        echo json_encode(["status" => "fail", "message" => "ข้อมูลไม่ครบ"]);
        exit;
    }

    if ($key === $tokenapi) {

        $stmt = $conn->prepare("SELECT locker_number FROM user_locker WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute(); 
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $locker_number = $row["locker_number"];

            $stmt = $conn->prepare("UPDATE locker_status SET take_open = 1 WHERE number_locker = ?");
            $stmt->bind_param("i",  $locker_number);

            if ($stmt->execute()) {
                echo json_encode(["status" => "success","message" => "opening $locker_number"]);
            }else{
                echo json_encode(["status" => "fail","message" => "can't"]);
            }
        }else{
            echo json_encode(["status" => "fail","message" => "can't find crad"]);
        }
    }
?>
