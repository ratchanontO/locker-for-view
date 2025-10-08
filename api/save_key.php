<?php
include '../connectdb.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

$locker_id = $data['locker_id'];
$user_id = $data['user_id'];
$key = $data['key'];

if (!$locker_id || !$user_id || !$key) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
    exit;
}

$stmt = $conn->prepare("UPDATE user_locker SET reset_key = ? WHERE locker_id = ? AND user_id = ?");
$stmt->bind_param("sii", $key, $locker_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'บันทึกไม่ได้']);
}
?>