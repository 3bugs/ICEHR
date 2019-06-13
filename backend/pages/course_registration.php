<?php
session_start();
require_once '../include/head_php.inc';

$sql = "SELECT ct.id, ct.form_number, ct.title, ct.first_name, ct.last_name, ct.phone, ct.email, ct.created_at, ct.register_status, ct.course_registration_id,
               cr.course_id, cr.coordinator_title, cr.coordinator_first_name, cr.coordinator_last_name, cr.coordinator_phone, cr.coordinator_email
            FROM course_trainee ct 
                INNER JOIN course_registration cr 
                    ON ct.course_registration_id = cr.id
            ORDER BY ct.id DESC";

if ($result = $db->query($sql)) {
    $traineeList = array();
    while ($row = $result->fetch_assoc()) {
        $trainee = array();
        $trainee['id'] = (int)$row['id'];
        $trainee['form_number'] = $row['form_number'];
        $trainee['title'] = $row['title'];
        $trainee['first_name'] = $row['first_name'];
        $trainee['last_name'] = $row['last_name'];
        $trainee['phone'] = $row['phone'];
        $trainee['email'] = $row['email'];
        $trainee['created_at'] = $row['created_at'];
        $trainee['register_status'] = $row['register_status'];
        $trainee['coordinator'] = array(
            'title' => $row['coordinator_title'],
            'first_name' => $row['coordinator_first_name'],
            'last_name' => $row['coordinator_last_name'],
            'phone' => $row['coordinator_phone'],
            'email' => $row['coordinator_email']
        );
        $trainee['course_id'] = (int)$row['course_id'];

        array_push($traineeList, $trainee);
    }
    $result->close();
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    $db->close();
    exit();
}
?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <?php require_once('../include/head.inc'); ?>
        <!-- DataTables -->
        <link rel="stylesheet" href="../bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
        <style>

        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">

    <!-- Print receipt modal -->
    <div class="modal fade" id="printReceiptModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">พิมพ์ใบเสร็จรับเงิน, ใบสมัครเลขที่ <span id="spanFormNumber"></span></h4>
                </div>
                <div class="modal-body">
                    <form action="print_receipt.php" method="get"
                          id="formPrintReceipt" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputTraineeId" name="trainee_id">

                            <!--ชื่อ-นามสกุลผู้สมัคร-->
                            <div class="form-group">
                                <label for="inputTraineeName">ผู้สมัคร:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" disabled
                                           id="inputTraineeName">
                                </div>
                            </div>
                            <!--หลักสูตรที่สมัคร-->
                            <div class="form-group">
                                <label for="inputCourseName">หลักสูตรที่สมัคร:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-font"></i>
                                    </span>
                                    <input type="text" class="form-control" disabled
                                           id="inputCourseName">
                                </div>
                            </div>
                            <!--ราคาเต็ม-->
                            <div class="form-group">
                                <label for="inputCourseFee">ราคาเต็ม (บาท):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-dollar"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputCourseFee"
                                           placeholder="กรอกราคาเต็มของหลักสูตรนี้" required
                                           oninvalid="this.setCustomValidity('กรอกราคาเต็มของหลักสูตรนี้')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                            <!--ส่วนลด-->
                            <div class="form-group" style="display: none">
                                <label for="inputDiscount">ส่วนลด (บาท):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-dollar"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputDiscount"
                                           placeholder="กรอกส่วนลด"
                                           oninvalid="this.setCustomValidity('กรอกส่วนลด')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                            <!--ราคาที่ลูกค้าจ่ายจริง-->
                            <div class="form-group">
                                <label for="inputPaid">ราคาที่ลูกค้าจ่ายจริง (บาท):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-dollar"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputPaid" name="paid"
                                           placeholder="กรอกราคาที่ลูกค้าจ่ายจริง" required
                                           oninvalid="this.setCustomValidity('กรอกราคาที่ลูกค้าจ่ายจริง')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                            <div id="responseText"
                                 style="text-align: center; color: red; margin-top: 25px; margin-bottom: 20px;">
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button id="buttonPrint" type="submit"
                                    class="btn btn-info pull-right">
                                <span class="fa fa-save"></span>&nbsp;
                                พิมพ์
                            </button>
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </div>
                <!--<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>-->
            </div>
        </div>
    </div>

    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    ใบสมัคร
                    <small>บริการฝึกอบรม</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                            </div>
                            <div class="box-body">
                                <table id="tableCourse" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 10%; text-align: center">เลขที่</th>
                                        <!--<th style="width: 50%; text-align: center">หลักสูตรที่สมัคร</th>-->
                                        <!--<th style="width: 10%; text-align: center">จำนวน</th>-->
                                        <!--<th style="width: 15%; text-align: center">ผู้กรอกใบสมัคร</th>-->
                                        <th style="width: 23%; text-align: center">ผู้สมัคร</th>
                                        <th style="width: 22%; text-align: center">ผู้ประสานงาน</th>
                                        <th style="width: 30%; text-align: center">หลักสูตรที่สมัคร</th>
                                        <th style="width: 15%; text-align: center">วัน/เวลาที่สมัคร</th>
                                        <th style="text-align: center">สถานะ</th>
                                        <th style="text-align: center" nowrap>ใบเสร็จ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($traineeList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="7" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($traineeList as $trainee) {
                                            $traineeId = $trainee['id'];
                                            $formNumber = $trainee['form_number'];
                                            $traineeDetails = sprintf(
                                                '%s %s %s<br><i class="fa fa-phone" style="color: black"></i> %s<br><i class="fa fa-envelope-o" style="color: black"></i> <a href="mailto:%s">%s</a>',
                                                $trainee['title'], $trainee['first_name'], $trainee['last_name'],
                                                $trainee['phone'], $trainee['email'], $trainee['email']
                                            );
                                            $coordinatorDetails = sprintf(
                                                '%s %s %s<br><i class="fa fa-phone" style="color: black"></i> %s<br><i class="fa fa-envelope-o" style="color: black"></i> <a href="mailto:%s">%s</a>',
                                                $trainee['coordinator']['title'], $trainee['coordinator']['first_name'], $trainee['coordinator']['last_name'],
                                                $trainee['coordinator']['phone'], $trainee['coordinator']['email'], $trainee['coordinator']['email']
                                            );
                                            $createdAt = $trainee['created_at'];
                                            $dateTimePart = explode(' ', $createdAt);
                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $timePart = explode(':', $dateTimePart[1]);
                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                            $displayDateTime = "$displayDate<br>$displayTime";
                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';

                                            $registerStatus = $trainee['register_status'];

                                            $courseId = $trainee['course_id'];
                                            $sql = "SELECT cm.title, c.batch_number, c.begin_date, c.end_date, c.application_fee 
                                                        FROM course_master cm 
                                                            INNER JOIN course c ON c.course_master_id = cm.id 
                                                        WHERE c.id=$courseId";
                                            if ($result = $db->query($sql)) {
                                                if ($row = $result->fetch_assoc()) {
                                                    $courseDetails = sprintf(
                                                        '%s รุ่นที่ %d',
                                                        $row['title'], $row['batch_number']
                                                    );
                                                    $courseApplicationFee = (int)$row['application_fee'];
                                                } else {
                                                    $courseDetails = '<span style="color: red">ไม่พบข้อมูล!</span>';
                                                }
                                            } else {
                                                $courseDetails = '<span style="color: red">เกิดข้อผิดพลาดในการอ่านข้อมูล!</span>';
                                            }
                                            ?>

                                            <tr>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $formNumber; ?></td>
                                                <td style="vertical-align: middle"><?php echo $traineeDetails; ?></td>
                                                <td style="vertical-align: middle"><?php echo ($trainee['coordinator']['first_name'] == null ? '&nbsp;' : $coordinatorDetails); ?></td>
                                                <td style="vertical-align: middle"><?php echo $courseDetails; ?></td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>

                                                <td style="vertical-align: middle; text-align: center" nowrap>
                                                    <form method="post" action="course_add_edit.php">
                                                        <input type="hidden" name="traineeId" value="<?php echo $traineeId; ?>"/>
                                                        <?php
                                                        switch ($registerStatus) {
                                                            case 'start':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-warning" style="width: 90px;">
                                                                    รอชำระเงิน
                                                                </button>
                                                                <?php
                                                                break;
                                                            case 'wait-approve':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-info" style="width: 90px;">
                                                                    แจ้งชำระเงิน
                                                                </button>
                                                                <?php
                                                                break;
                                                            case 'complete':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-success" style="width: 90px;">
                                                                    สมบูรณ์
                                                                </button>
                                                                <?php
                                                                break;
                                                            case 'cancel':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-danger" style="width: 90px;">
                                                                    ยกเลิก
                                                                </button>
                                                                <?php
                                                                break;
                                                        }
                                                        ?>
                                                    </form>
                                                </td>
                                                <td style="vertical-align: middle; text-align: center" nowrap>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                            <span class="caret"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu pull-right" role="menu">
                                                            <li><a href="javascript:void(0)" onClick="onClickPrintRegForm()"><i class="fa fa-print"></i>ใบสมัคร</a></li>
                                                            <li><a href="javascript:void(0)" onClick="onClickPrintReceipt('<?php echo $formNumber; ?>', <?php echo $traineeId; ?>, '<?php echo "{$trainee['title']} {$trainee['first_name']} {$trainee['last_name']}"; ?>', '<?php echo $courseDetails; ?>', <?php echo $courseApplicationFee; ?>)"><i class="fa fa-print"></i>ใบเสร็จรับเงิน</a></li>
                                                            <li><a href="javascript:void(0)" onClick="onClickPrintCertificate()"><i class="fa fa-print"></i>ใบรับรองการผ่านอบรม</a></li>
                                                            <!--<li class="divider"></li>-->
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>

                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php require_once('../include/footer.inc'); ?>
    </div>
    <!-- ./wrapper -->

    <script>
        $(document).ready(function () {
            $('#tableCourse').DataTable({
                order: [[4, 'desc']],
                language: {
                    lengthMenu: "แสดงหน้าละ _MENU_ แถวข้อมูล",
                    zeroRecords: "ไม่มีข้อมูล",
                    emptyTable: "ไม่มีข้อมูล",
                    info: "หน้าที่ _PAGE_ จากทั้งหมด _PAGES_ หน้า",
                    infoEmpty: "แสดง 0 แถวข้อมูล",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ แถวข้อมูล)",
                    search: "ค้นหา:",
                    thousands: ",",
                    loadingRecords: "รอสักครู่...",
                    processing: "กำลังประมวลผล...",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    },
                }
            });
        });

        function onClickPrintReceipt(formNumber, traineeId, traineeName, courseName, courseApplicationFee) {
            $('#printReceiptModal #spanFormNumber').text(formNumber);
            $('#formPrintReceipt #inputTraineeId').val(traineeId);
            $('#formPrintReceipt #inputTraineeName').val(traineeName);
            $('#formPrintReceipt #inputCourseName').val(courseName);
            $('#formPrintReceipt #inputCourseFee').val(courseApplicationFee);
            $('#printReceiptModal').modal('show');
        }

        $('#inputDiscount').on('input', e => {
            const courseFee = $('#formPrintReceipt #inputCourseFee').val();
            const discount = $('#inputDiscount').val();
            if (discount.trim() === '' || isNaN(discount)) {
                $('#inputPaid').val('');
            } else {
                $('#inputPaid').val(courseFee - discount);
            }
        });
        $('#inputPaid').on('input', e => {
            const courseFee = $('#formPrintReceipt #inputCourseFee').val();
            const paid = $('#inputPaid').val();
            if (paid.trim() === '' || isNaN(paid)) {
                $('#inputDiscount').val('');
            } else {
                $('#inputDiscount').val(courseFee - paid);
            }
        });

        function onClickTraineeCountLink(element, title, message) {
            BootstrapDialog.show({
                title,
                message,
                buttons: [{
                    label: 'ปิด',
                    action: function (self) {
                        self.close();
                    },
                    cssClass: 'btn-primary'
                }]
            });
        }

        function doDeleteCourse(courseId) {
            $.post(
                '../api/api.php/delete_course',
                {
                    courseId: courseId,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบหลักสูตร - ผิดพลาด',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                            }
                        }]
                    });
                }
            }).fail(function () {
                BootstrapDialog.show({
                    title: 'ลบหลักสูตร - ผิดพลาด',
                    message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server',
                    buttons: [{
                        label: 'ปิด',
                        action: function (self) {
                            self.close();
                        }
                    }]
                });
            });
        }

    </script>

    <?php require_once('../include/foot.inc'); ?>
    <!-- DataTables -->
    <script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    </body>
    </html>

<?php
$db->close();
?>