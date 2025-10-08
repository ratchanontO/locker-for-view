<?php
    include '../../connectdb.php';
    // include '../jsoncounttime.json';
    require_once '../../config.php';

    header("Content-Type: application/json");
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");

    $tokenapi = $api_keys['apikey'];

    $data = json_decode(file_get_contents("php://input"), true);

    $key = $data["key"];
    $statusopen = 1;

    if (!$key) {
        echo json_encode(["status" => "fail", "message" => "ข้อมูลไม่ครบ"]);
        exit;
    }

    if ($key === $tokenapi) {
        $stmt = $conn->prepare("SELECT number_locker, take_open FROM locker_status WHERE take_open = ?");
        $stmt->bind_param("i", $statusopen);
        $stmt->execute();
        $result = $stmt->get_result();
        if($row = $result->fetch_assoc()){
            echo json_encode(["status" => "success","locker" => $row["number_locker"]]);
        }else{
            echo json_encode(["status" => "fail","locker" => "nothing"]);
        }
    }else{
        echo json_encode(["status" => "fail","message" => "คีย์บ่ถูกอย่ามั่ว"]);
    }
    
?>
