<?php
include '../connectdb.php';
require_once '../config.php';
header('Content-Type: application/json');

$tokenapi = $api_keys['apikey'];

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['key'], $data['slot'])) {

    $key = $data['key'];
    $slot = $data['slot'];

    if ($key !== $tokenapi) {
        echo json_encode(["status" => "error", "message" => "Invalid key"]);
        exit;
    }

    // หา user ตาม slot_fingerprint
    $stmt = $conn->prepare("SELECT id FROM users WHERE slot_fingerprint = ?");
    $stmt->bind_param("i", $slot);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userid = $row["id"];

            // หา locker ของ user
            $stmt2 = $conn->prepare("SELECT locker_number FROM user_locker WHERE user_id = ?");
            $stmt2->bind_param("i", $userid);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows > 0) {
                $row2 = $result2->fetch_assoc(); // ต้องใช้ $result2 ไม่ใช่ $result

                // อัปเดตสถานะ locker
                $take_open = 1; // หรือค่าอื่นที่ต้องการ
                $stmt3 = $conn->prepare("UPDATE locker_status SET take_open = ? WHERE number_locker = ?");
                $stmt3->bind_param("ii", $take_open, $row2["locker_number"]);

                if ($stmt3->execute()) {
                    echo json_encode(["status" => "success", "message" => "ok"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to update locker"]);
                }

            } else {
                echo json_encode(["status" => "error", "message" => "ไม่พบ locker ของ user นี้"]);
            }

        } else {
            echo json_encode(["status" => "error", "message" => "ไม่พบ user ที่ slot_fingerprint = $slot"]);
        }

    } else {
        echo json_encode(["status" => "error", "message" => "Query failed"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "no post"]);
}
?>
