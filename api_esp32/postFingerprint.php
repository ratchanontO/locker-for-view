<?php
    include '../connectdb.php';
    require_once '../config.php';
    header('Content-Type: application/json');
    $tokenapi = $api_keys['apikey'];

    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['userid'], $data['key'], $data['slot'])) {
        $userid = $data['userid'];
        $key = $data['key'];
        $slot = $data['slot'];

        if ($key !== $tokenapi) {
            echo json_encode(["status" => "error", "message" => "Invalid key"]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE users SET slot_fingerprint = ? WHERE id = ?");
        $stmt->bind_param("ii", $slot, $userid);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Data updated"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No rows updated"]);
        }
        exit;
    }

    echo json_encode(["status" => "error", "message" => "ไม่มีโพสดเข้ามา"]);
?>
