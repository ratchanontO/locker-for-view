<?php
    include '../connectdb.php';
    require_once '../config.php';
    $tokenapi = $api_keys['apikey'];

    $data = json_decode(file_get_contents("php://input"), true);

    $userid = $data["userid"];
    $slot_fingerprint = $data["slot_fingerprint"];
    $key = $data["key"];

    if(!$userid || !$slot_fingetprint || !$key){
        echo json_encode(["status" => "fail", "message" => "ข้อมูลไม่ครบ"]);
        exit;
    }
    if($key == $tokenapi){

        $stmt = $conn->prepare("UPDATE users SET slot_fingerprint = ? WHERE id = ?");
        $stmt->bind_param("si", $slot_fingerprint, $userid);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => "success", "message" => "ok"]);
        }else{
            echo json_encode(["status" => "fail", "message" => "fail"]);
        }
    }
?>