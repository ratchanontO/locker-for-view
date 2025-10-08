<?php
    function convertDateToThaiFormat($datetime_str) {
        // 1. กำหนดชื่อเดือนเป็นภาษาไทย
        $thai_months = array(
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        );

        // 2. สร้าง DateTime object
        $datetime = new DateTime($datetime_str);
        
        // 3. ดึงส่วนประกอบของวันที่
        $day = $datetime->format('d'); // วันที่ 2 หลัก
        $month_num = (int)$datetime->format('m'); // หมายเลขเดือน
        $year = (int)$datetime->format('Y') + 543; // ปี พ.ศ. (ค.ศ. + 543)
        $time = $datetime->format('H:i'); // เวลา ชั่วโมง:นาที

        // 4. แปลงเป็นรูปแบบ 'วัน/เดือนไทย/ปี เวลา'
        return $day . '/' . $thai_months[$month_num] . '/' . $year . ' ' . $time;
    }
?>