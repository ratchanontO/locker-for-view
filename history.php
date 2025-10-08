<?php
session_start();
include 'connectdb.php';
require "head.php";
require "datefn.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['userid']; 


?>
<style>
    .mitr-extralight200 {
        font-family: "Mitr", sans-serif;
        font-weight: 300;
        font-style: normal;
    }

    .mitr-extralight300, .mitr-extralight300 th, .mitr-extralight300 td {
        font-family: 'Mitr', sans-serif;
        font-weight: 300;
    }

    .table {
        font-family: "Mitr", sans-serif;  
        font-size: 15px;                  
        font-weight: 300;
    }

    .table th {
    font-weight: 600;                 
    font-size: 16px;
    }

    .table td {
    font-weight: 300;                 
    }

</style>
<body>
<?php require "navbar.php"; ?>

<div class="container custom-narrow ">
    <div class="row align-items-center mitr-extralight300" style="background-color:rgba(255, 255, 255, 0.79); margin: 14px 12px; border-radius: 15px; padding: 1rem 0;">
        <h4 class="mb-4 mitr-extralight300">ประวัติการใช้งาน</h4>

        <div class="container">
            <table class="table table-bordered text-center mitr-extralight300">
                <thead class="table-light mitr-extralight300">
                    <tr>
                        <th>วันที่และเวลา</th>
                        <th>ตู้</th>
                        <th>ดูประวัติ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM log_locker WHERE user_id = ? ORDER BY time DESC");
                    $stmt->bind_param("i", $user_id); 
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($row = $result->fetch_assoc()):
                        // $inputDate = date('Y-m-d\TH:i', strtotime($row['time']));
                        $thaiDateAndTime = convertDateToThaiFormat($row['time']);
                        ?>
                        <tr class="mitr-extralight300">
                            <td class="<?= $row["event_type"] == 2 ? 'bg-danger text-white' : '' ?>">
                                <!-- <input type="datetime-local" name="date[]" class="form-control" value="" readonly> -->
                                <p class="mitr-extralight300">
                                     <?= htmlspecialchars($thaiDateAndTime) ?>
                                </p>
                            </td>
                            <td><?= htmlspecialchars($row['locker_number']) ?></td>
                            <td>
                                <a href="watch_history.php?locker_number=<?= $row['locker_number'] ?>&id=<?= $row['id'] ?>" class="btn btn-primary btn-sm mitr-extralight300">ดูประวัติ</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<?php require "low_menu.php"; ?>
</body>
