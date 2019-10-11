<?php
require_once '../include/head_php.inc';

$serviceType = $_GET['service_type'];
if (!isset($serviceType)) {
    echo '<div style="color: red">ERROR: ไม่ได้ระบุ service type</div>';
    $db->close();
    exit();
} else if (!array_key_exists($serviceType, $serviceTypeText)) {
    echo '<div style="color: red">ERROR: ระบุ service type ไม่ถูกต้อง</div>';
    $db->close();
    exit();
}

$sql = "SELECT c.id, c.batch_number, c.details, c.begin_date, c.end_date, c.application_fee, 
               c.place, c.trainee_limit, c.responsible_user_id, c.status, cm.title 
        FROM course c 
            INNER JOIN course_master cm 
                ON c.course_master_id = cm.id 
        WHERE cm.service_type = '$serviceType'
        ORDER BY c.begin_date DESC";

if ($result = $db->query($sql)) {
    $courseList = array();
    while ($row = $result->fetch_assoc()) {
        $course = array();
        $course['id'] = (int)$row['id'];
        /*บริการใบขับขี่ ไม่ต้องมีคำว่า "รุ่นที่ xx"*/
        $course['name'] = $row['title']
            . ($serviceType === SERVICE_TYPE_DRIVING_LICENSE ? '' : (' รุ่นที่ ' . $row['batch_number']));
        $course['details'] = $row['details'];
        $course['application_fee'] = (int)$row['application_fee'];
        $course['place'] = $row['place'];
        $course['begin_date'] = $row['begin_date'];
        $course['end_date'] = $row['end_date'];
        $course['trainee_limit'] = (int)$row['trainee_limit'];
        $course['responsible_user_id'] = (int)$row['responsible_user_id'];
        $course['status'] = $row['status'];

        switch ($serviceType) {
            case SERVICE_TYPE_TRAINING:
                $sql = "SELECT cr.id 
                        FROM course_registration cr 
                            INNER JOIN course_trainee ct 
                                ON cr.id = ct.course_registration_id 
                        WHERE course_id = {$course['id']} AND ct.register_status <> 'cancel'";
                break;
            case SERVICE_TYPE_SOCIAL:
                $sql = "SELECT cr.id 
                        FROM course_registration_social cr 
                        WHERE cr.course_id = {$course['id']} AND cr.register_status <> 'cancel'";
                break;
            case SERVICE_TYPE_DRIVING_LICENSE:
                $sql = "SELECT cr.id 
                        FROM course_registration_driving_license cr 
                        WHERE cr.course_id = {$course['id']} AND cr.register_status <> 'cancel'";
                break;
        }

        if ($resultCount = $db->query($sql)) {
            $course['trainee_count'] = $resultCount->num_rows;
            $resultCount->close();
        } else {
            echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
            $db->close();
            exit();
        }

        array_push($courseList, $course);
    }
    $result->close();
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
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

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    หลักสูตร
                    <small><?= $serviceTypeText[$serviceType]; ?></small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                                <?php
                                if (($serviceType === SERVICE_TYPE_TRAINING && currentUserHasPermission(PERMISSION_COURSE_TRAINING_CREATE))
                                    || ($serviceType === SERVICE_TYPE_SOCIAL && currentUserHasPermission(PERMISSION_COURSE_SOCIAL_CREATE))
                                    || ($serviceType === SERVICE_TYPE_DRIVING_LICENSE && currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_CREATE))) {
                                    ?>
                                    <form method="post" action="course_add_edit.php" style="display: inline">
                                        <input type="hidden" name="service_type" value="<?= $serviceType; ?>"/>
                                        <button type="submit" class="btn btn-success pull-right">
                                            <span class="fa fa-plus"></span>&nbsp;
                                            เพิ่มหลักสูตร
                                        </button>
                                    </form>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="box-body">
                                <table id="tableCourse" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <?php
                                        if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) {
                                            ?>
                                            <th style="width: 50%; text-align: center">หลักสูตร</th>
                                            <th style="width: 10%; text-align: center">ค่าสมัคร</th>
                                            <th style="width: 20%; text-align: center">วันที่อบรม</th>
                                            <th style="width: 20%; text-align: center">สมัคร / รับได้</th>
                                            <th style="text-align: center">สถานะ</th>
                                            <th style="text-align: center">จัดการ</th>
                                            <?php
                                        } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
                                            ?>
                                            <th style="width: 60%; text-align: center">หลักสูตร</th>
                                            <th style="width: 20%; text-align: center">วันที่อบรม</th>
                                            <th style="width: 20%; text-align: center">สมัคร / รับได้</th>
                                            <th style="text-align: center">สถานะ</th>
                                            <th style="text-align: center">จัดการ</th>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($courseList) == 0) {
                                        ?>
                                        <!--<tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>-->
                                        <?php
                                    } else {
                                        foreach ($courseList as $course) {
                                            $courseId = $course['id'];
                                            $courseName = $course['name'];
                                            $courseApplicationFee = $course['application_fee'] === 0 ? 'ฟรี' : number_format($course['application_fee']);
                                            $beginDate = date_create($course['begin_date']);
                                            $endDate = date_create($course['end_date']);
                                            $courseDate = ($course['begin_date'] === $course['end_date'] ? getThaiShortDate($beginDate) : getThaiIntervalShortDate($beginDate, $endDate));
                                            $courseDateHidden = '<span style="display: none">' . $course['begin_date'] . '</span></span>';
                                            $traineeCount = $course['trainee_count'];
                                            $traineeLimit = $course['trainee_limit'];
                                            $responsibleUserId = $course['responsible_user_id'];
                                            $status = $course['status'];

                                            $userHasPermission = $responsibleUserId === (int)$_SESSION[KEY_SESSION_USER_ID] // ถ้าเป็นผู้รับผิดชอบหลักสูตร ให้แสดงปุ่มแก้ไข
                                                || ($serviceType === SERVICE_TYPE_TRAINING && currentUserHasPermission(PERMISSION_COURSE_TRAINING_UPDATE))
                                                || ($serviceType === SERVICE_TYPE_SOCIAL && currentUserHasPermission(PERMISSION_COURSE_SOCIAL_UPDATE))
                                                || ($serviceType === SERVICE_TYPE_DRIVING_LICENSE && currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_UPDATE));
                                            ?>

                                            <tr style="">
                                                <td style="vertical-align: top"><?= $courseName; ?>&nbsp;&nbsp;
                                                    <a target="_blank" title="ไปหน้าเว็บของหลักสูตร"
                                                       href="<?= HOST_FRONTEND . "/service-{$serviceType}/{$courseId}" ?>"><i class="fa fa-external-link"></i></a>
                                                </td>
                                                <?php
                                                if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) {
                                                    ?>
                                                    <td style="vertical-align: top; text-align: center"><?= $courseApplicationFee; ?></td>
                                                    <?php
                                                }
                                                ?>
                                                <td style="vertical-align: top; text-align: center"><?= $courseDateHidden . $courseDate; ?></td>
                                                <td style="vertical-align: top; text-align: center"><?= "<strong>$traineeCount</strong> / $traineeLimit"; ?></td>

                                                <td style="text-align: center; vertical-align: top">
                                                    <span style="display: none">
                                                        <?= $status === 'normal' ? 'on' : 'off' ?>>
                                                    </span>
                                                    <input name="status" type="checkbox"
                                                           class="my-toggle"
                                                           data-toggle="toggle"
                                                           onChange="onChangeStatus(this, <?= $userHasPermission ? 'true' : 'false'; ?>, <?= $courseId; ?>, '<?= htmlentities($courseName); ?>')"
                                                        <?= $status === 'normal' ? 'checked' : '' ?>>
                                                </td>

                                                <td style="text-align: right" nowrap>
                                                    <form method="post" action="course_add_edit.php" style="display: inline">
                                                        <input type="hidden" name="course_id" value="<?= $courseId; ?>"/>
                                                        <input type="hidden" name="service_type" value="<?= $serviceType; ?>"/>

                                                        <?php
                                                        if ($userHasPermission) {
                                                            ?>
                                                            <button type="submit" class="btn btn-warning" style="margin-right: 3px">
                                                                <span class="fa fa-pencil"></span>&nbsp;
                                                                แก้ไข
                                                            </button>
                                                            <?php
                                                        }
                                                        ?>
                                                    </form>
                                                    <form method="get" action="course_details.php" style="display: inline">
                                                        <input type="hidden" name="course_id" value="<?= $courseId; ?>"/>
                                                        <button type="submit" class="btn btn-info">
                                                            <span class="fa fa-files-o"></span>&nbsp;
                                                            ใบสมัคร
                                                        </button>
                                                    </form>
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
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[<?= $serviceType === SERVICE_TYPE_DRIVING_LICENSE ? '1' : '2'; ?>, 'desc']],
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
                },
                drawCallback: function(row, data) {
                    $('.my-toggle').bootstrapToggle();
                }
            });
        });

        function onClickAdd() {
            //window.location.href = 'course_add_edit.php?service_type=<?= $serviceType; ?>';
        }

        function onChangeStatus(element, userHasPermission, courseId, courseName) {
            if (userHasPermission) {
                let result = confirm("ยืนยัน" + (element.checked ? 'เปิดหลักสูตร' : 'ยกเลิกหลักสูตร') + "\n\n'" + courseName + "' ?");
                if (result) {
                    doChangeStatus(courseId, (element.checked ? 'normal' : 'canceled'));
                } else {
                    /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                    location.reload(true);
                }
            } else {
                alert("คุณไม่มีสิทธิ์แก้ไขสถานะหลักสูตรนี้\n\n'" + courseName + "'");
                location.reload(true);
            }
        }

        function doChangeStatus(courseId, newStatus) {
            let title = 'แก้ไขสถานะหลักสูตร';

            $.post(
                '../api/api.php/update_course_status',
                {
                    courseId: courseId,
                    newStatus: newStatus
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: title + ' - ผิดพลาด',
                        message: data.error_message,
                        buttons: [{
                            label: 'ปิด',
                            action: function (self) {
                                self.close();
                                location.reload(true);
                            }
                        }]
                    });
                }
            }).fail(function () {
                BootstrapDialog.show({
                    title: title + ' - ผิดพลาด',
                    message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server',
                    buttons: [{
                        label: 'ปิด',
                        action: function (self) {
                            self.close();
                            location.reload(true);
                        }
                    }]
                });
            });
        }

        function onClickDelete(element, courseId, courseName) {
            BootstrapDialog.show({
                title: 'ลบหลักสูตร',
                message: 'ยืนยันลบหลักสูตร \'' + courseName + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteCourse(courseId);
                        self.close();
                    },
                    cssClass: 'btn-primary'
                }, {
                    label: 'ยกเลิก',
                    action: function (self) {
                        self.close();
                    }
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