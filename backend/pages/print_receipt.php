<?php
session_start();
require_once '../include/head_php.inc';

$traineeId = $_GET['trainee_id'];
$paid = (int)$_GET['paid'];

$sql = "SELECT ct.id, ct.form_number, ct.title, ct.first_name, ct.last_name, ct.phone, ct.email, ct.created_at, ct.register_status, ct.course_registration_id,
               cr.course_id, cr.receipt_address, cr.receipt_sub_district, cr.receipt_district, cr.receipt_province, cr.receipt_postal_code, cr.receipt_tax_id
            FROM course_trainee ct 
                INNER JOIN course_registration cr 
                    ON ct.course_registration_id = cr.id
            WHERE ct.id = $traineeId";

if ($result = $db->query($sql)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $traineeName = sprintf("%s%s %s", $row['title'], $row['first_name'], $row['last_name']);
    } else {
        echo 'ผิดพลาด: ไม่พบข้อมูล!';
        $db->close();
        exit();
    }
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $sql;
    $db->close();
    exit();
}
?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <?php require_once('../include/head.inc'); ?>
        <style>
            #paymentDate {
                position: absolute;
                top: 2.5cm;
                left: 9cm;
            }
        </style>
        <script type="text/javascript">
            window.onload = function() { window.print(); }
        </script>
    </head>
    <body>
        <div id="paymentDate">11/06/62</div>
    </body>
    </html>
<?php
$db->close();
?>