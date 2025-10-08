<?php
include '../connectdb.php';
require_once '../config.php';
$tokenapi = $api_keys['apikey'];

$data = json_decode(file_get_contents("php://input"), true);

$mode = $data["mode"];
$weight = $data["weight"];
$locker_number = $data["locker_number"];
$key = $data["key"];

$one = 1;

$event_type = 1; //1ปกติ 2งัด
if (!$mode || !$weight || !$key) {
    echo json_encode(["status" => "fail", "message" => "ข้อมูลไม่ครบ"]);
    exit;
}

if ($key == $tokenapi) {
    $stmt = $conn->prepare("SELECT user_id, locker_id FROM user_locker WHERE locker_number = ?");
    $stmt->bind_param("i", $locker_number);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
            $locker_id = $row['locker_id'];
        }
    }

    if ($mode == 1) { // kg_before
        $o = 0;
        // $stmt = $conn->prepare("UPDATE locker_status SET take_open = ? WHERE number_locker = ?");
        // $stmt->bind_param("ii", $o, $locker_number);
        // $stmt->execute();
        if (1) {
            $stmt = $conn->prepare("UPDATE dataforlog SET kg_before = ?, user_id = ?, event_type = ? WHERE locker_number = ?");
            $stmt->bind_param("diii", $weight, $user_id, $event_type, $locker_number);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "ok kg_before "]);
            } else {
                echo json_encode(["status" => "fail", "message" => "kg_before no"]);
            }
        }

    } elseif ($mode == 2) { // kg_after
        $stmt = $conn->prepare("UPDATE locker_status SET take_open = ? WHERE number_locker = ?");
        $stmt->bind_param("ii", $o, $locker_number);
        if($stmt->execute()){
            $stmt = $conn->prepare("UPDATE dataforlog SET kg_after = ? WHERE locker_number = ?");
            $stmt->bind_param("di", $weight, $locker_number);
            if ($stmt->execute()) {
                
                echo json_encode(["status" => "success", "message" => "ok kg_after "]);
            } else {
                echo json_encode(["status" => "fail", "message" => "kg_after No"]);
            }
        }else{
            echo json_encode(["status" => "fail", "message" => "kg_after No"]);
        }

    } elseif ($mode == 3) { 
        $stmt = $conn->prepare("UPDATE dataforlog SET take_photo = ? WHERE locker_number = ?");
        $stmt->bind_param("ii",$one, $locker_number);
        if ($stmt->execute()) {   
            echo json_encode(["status" => "success", "message" => "insert log Done!!"]);
        }else {
            echo json_encode(["status" => "fail", "message" => "take_open cant"]);
        }
    }
}
?>
