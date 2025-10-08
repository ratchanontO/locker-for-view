<?php
    include '../connectdb.php';
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents("php://input"), true);

    $userid = $data['userid'];
    $password = $data['password'];
    $key = $data['key'];

    if (!$userid || !$password || !$key) {
        echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
        exit;
    }
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if($row["resetpassword_key"] == $key){
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ?, resetpassword_key = NULL WHERE id = ?");
            $stmt->bind_param("si", $hash_password, $userid);
            if($stmt->execute()){
                echo json_encode(['status' => 'success']);
            }else{
                echo json_encode(['status' => 'error', 'message' => 'บันทึกไม่ได้']);
            }
        }else{
            echo json_encode(['status' => 'error', 'message' => 'คียไม่ตรง']);
        }
    }

?>