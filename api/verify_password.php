<?php
    include '../connectdb.php';

    $data = json_decode(file_get_contents("php://input"), true);

    $user_id = $data["user_id"];
    $password = $data["password"];
    $key = $data["key"];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row || !password_verify($password, $row["password"])) {
        echo json_encode(["status" => "fail", "message" => "รหัสผ่านไม่ถูกต้อง"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET resetpassword_key = ? WHERE id = ?");
    $stmt->bind_param("si", $key, $user_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "fail", "message" => "ไม่สามารถบันทึก key ได้"]);
    }
?>