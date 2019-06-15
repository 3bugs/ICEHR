<?php
require_once '../include/head_php.inc';

/*$sql = "SELECT cr.id, cr.member_id, m.first_name AS member_first_name, m.last_name AS member_last_name, m.email AS member_email, "
    . "     cr.form_number, cm.title, c.batch_number, cr.status, cr.created_at, "
    . "     cr.coordinator_title, cr.coordinator_first_name, cr.coordinator_last_name, cr.coordinator_phone, "
    . "     cr.coordinator_email, cr.coordinator_organization_name, cr.coordinator_job_position "
    . " FROM course_registration cr INNER JOIN member m INNER JOIN course c INNER JOIN course_master cm "
    . " ON cr.member_id = m.id AND cr.course_id = c.id AND c.course_master_id = cm.id "
    . " ORDER BY cr.created_at DESC";*/

$sql = "SELECT crm.*, cm.title, c.batch_number FROM
            (SELECT cr.id, cr.member_id, m.first_name AS member_first_name, m.last_name AS member_last_name, m.email AS member_email,
                cr.form_number, cr.course_id, cr.coordinator_first_name, cr.coordinator_last_name, cr.coordinator_phone, cr.coordinator_email,
                cr.coordinator_job_position, cr.coordinator_organization_name, cr.status, cr.created_at
            FROM course_registration cr 
            LEFT JOIN member m 
                ON cr.member_id = m.id) crm
        INNER JOIN course c INNER JOIN course_master cm
            ON crm.course_id = c.id AND c.course_master_id = cm.id
        ORDER BY crm.created_at DESC";

if ($result = $db->query($sql)) {
    $courseRegList = array();
    while ($row = $result->fetch_assoc()) {
        $courseReg = array();
        $courseReg['id'] = (int)$row['id'];
        $courseRegId = $courseReg['id'];

        $memberId = (int)$row['member_id'];
        $courseReg['member'] = array(
            'id' => $memberId,
            'email' => $memberId === 0 ? '' : $row['member_email'],
            'first_name' => $memberId === 0 ? 'Guest' : $row['member_first_name'],
            'last_name' => $memberId === 0 ? '' : $row['member_last_name']
        );
        $courseReg['form_number'] = $row['form_number'];
        $courseReg['course_name'] = $row['title'] . ' รุ่นที่ ' . $row['batch_number'];
        $courseReg['status'] = $row['status'];
        $courseReg['created_at'] = $row['created_at'];
        $courseReg['coordinator'] = array(
            'title' => $row['coordinator_title'],
            'first_name' => $row['coordinator_first_name'],
            'last_name' => $row['coordinator_last_name'],
            'phone' => $row['coordinator_phone'],
            'email' => $row['coordinator_email'],
            'job_position' => $row['coordinator_job_position'],
            'organization_name' => $row['coordinator_organization_name']
        );
        $courseReg['trainee_list'] = array();

        $traineeSql = "SELECT * FROM course_trainee WHERE course_registration_id=$courseRegId";
        if ($traineeResult = $db->query($traineeSql)) {
            while ($traineeRow = $traineeResult->fetch_assoc()) {
                $trainee['title'] = $traineeRow['title'];
                $trainee['first_name'] = $traineeRow['first_name'];
                $trainee['last_name'] = $traineeRow['last_name'];
                $trainee['phone'] = $traineeRow['phone'];

                array_push($courseReg['trainee_list'], $trainee);
            }
            $traineeResult->close();
        } else {
            echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
            $result->close();
            $db->close();
            exit();
        }

        array_push($courseRegList, $courseReg);
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
    <div class="wrapper">
        <?php require_once('../include/header.inc'); ?>
        <?php require_once('../include/sidebar.inc'); ?>

        <div class="modal fade" id="modalTraineeList">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="title">Default Modal</h4>
                    </div>
                    <div class="modal-body" id="body">
                        <p>One fine body&hellip;</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

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
                                        <th style="width: 50%; text-align: center">หลักสูตรที่สมัคร</th>
                                        <th style="width: 10%; text-align: center">จำนวน</th>
                                        <th style="width: 15%; text-align: center">ผู้กรอกใบสมัคร</th>
                                        <th style="width: 15%; text-align: center">วัน/เวลาที่สมัคร</th>
                                        <th style="text-align: center">สถานะ</th>
                                        <th style="text-align: center" nowrap>ใบเสร็จ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($courseRegList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="7" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($courseRegList as $courseReg) {
                                            $formNumber = $courseReg['form_number'];
                                            $courseName = $courseReg['course_name'];

                                            $traineeCount = sizeof($courseReg['trainee_list']);

                                            $memberId = $courseReg['member']['id'];
                                            $memberName = $courseReg['member']['first_name'] . ' ' . $courseReg['member']['last_name'];
                                            $memberEmail = $courseReg['member']['email'];

                                            $createdAt = $courseReg['created_at'];
                                            $dateTimePart = explode(' ', $createdAt);
                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $timePart = explode(':', $dateTimePart[1]);
                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                            $displayDateTime = "$displayDate<br>$displayTime";
                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';

                                            ?>

                                            <tr>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $formNumber; ?></td>
                                                <td style="vertical-align: middle"><?php echo $courseName; ?></td>

                                                <?php
                                                $title = "ใบสมัครเลขที่ $formNumber";

                                                $traineeListText = "<h4>รายชื่อผู้สมัครอบรม ($traineeCount ท่าน)</h4>";
                                                foreach ($courseReg['trainee_list'] as $trainee) {
                                                    $traineeFirstName = $trainee['first_name'];
                                                    $traineeLastName = $trainee['last_name'];
                                                    $traineePhone = $trainee['phone'];
                                                    $traineeListText .= "<strong>$traineeFirstName $traineeLastName</strong><ul><li>โทร. $traineePhone</li></ul>";
                                                }
                                                if (sizeof($courseReg['trainee_list']) > 1) {
                                                    $coordinatorFirstName = $courseReg['coordinator']['first_name'];
                                                    $coordinatorLastName = $courseReg['coordinator']['last_name'];
                                                    $coordinatorPhone = $courseReg['coordinator']['phone'];
                                                    $coordinatorEmail = $courseReg['coordinator']['email'];
                                                    $coordinatorJobPosition = $courseReg['coordinator']['job_position'];
                                                    $coordinatorOrganizationName = $courseReg['coordinator']['organization_name'];

                                                    $traineeListText .= "<hr><h4>ผู้ประสานงาน</h4>";
                                                    $traineeListText .= "<strong>$coordinatorFirstName $coordinatorLastName</strong><ul><li>ตำแหน่ง: $coordinatorJobPosition</li><li>ชื่อหน่วยงาน: $coordinatorOrganizationName</li><li>อีเมล: $coordinatorEmail</li><li>โทร: $coordinatorPhone</li></ul>";
                                                }
                                                ?>

                                                <td style="vertical-align: middle; text-align: center; cursor: pointer"
                                                    onclick="onClickTraineeCountLink(this, '<?php echo $title; ?>', '<?php echo $traineeListText; ?>')">
                                                    <a href="javascript:void(0)"><?php echo $traineeCount; ?></a>
                                                </td>
                                                <td style="vertical-align: middle"><?php echo "$memberName<br>$memberEmail"; ?></td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>

                                                <td style="vertical-align: middle; text-align: center" nowrap>
                                                    <form method="post" action="course_add_edit.php">
                                                        <input type="hidden" name="courseRegId" value="<?php echo $courseRegId; ?>"/>
                                                        <?php
                                                        switch ($courseReg['status']) {
                                                            case 'started':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-warning" style="width: 90px;">
                                                                    รอชำระเงิน
                                                                </button>
                                                                <?php
                                                                break;
                                                            case 'approve_waiting':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-info" style="width: 90px;">
                                                                    แจ้งชำระเงิน
                                                                </button>
                                                                <?php
                                                                break;
                                                            case 'completed':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-success" style="width: 90px;">
                                                                    สมบูรณ์
                                                                </button>
                                                                <?php
                                                                break;
                                                            case 'canceled':
                                                                ?>
                                                                <button type="button" class="btn-xs btn-danger" style="width: 90px;">
                                                                    ยกเลิก
                                                                </button>
                                                                <?php
                                                                break;
                                                        }
                                                        $courseReg['status']
                                                        ?>

                                                        <!--<button type="button" class="btn btn-danger"
                                                                onclick="onClickDelete(this, <?php /*echo $courseId; */ ?>, '<?php /*echo $courseName; */ ?>')">
                                                            <span class="fa fa-pencil"></span>&nbsp;
                                                            ลบ
                                                        </button>-->
                                                    </form>
                                                </td>
                                                <td style="vertical-align: middle; text-align: center" nowrap>
                                                    <?php
                                                    if ($courseReg['status'] === 'completed') {
                                                        ?>
                                                        <a class="btn" style="padding: 0">
                                                            <i class="fa fa-print"></i> พิมพ์
                                                        </a>
                                                        <?php
                                                    }
                                                    ?>
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
require_once '../include/foot_php.inc';
?>