<?php
require_once '../include/head_php.inc';

$sql = "SELECT c.id, c.batch_number, c.details, c.begin_date, c.end_date, c.place, cm.title "
    . " FROM course c INNER JOIN course_master cm ON c.course_master_id = cm.id "
    . " ORDER BY c.begin_date DESC";

//$sql = "SELECT * FROM course ORDER BY begin_date";
if ($result = $db->query($sql)) {
    $courseList = array();
    while ($row = $result->fetch_assoc()) {
        $course = array();
        $course['id'] = (int)$row['id'];
        $course['name'] = $row['title'] . ' รุ่นที่ ' . $row['batch_number'];
        $course['details'] = $row['details'];
        $course['application_fee'] = (int)$row['application_fee'];
        $course['place'] = $row['place'];
        $course['begin_date'] = $row['begin_date'];
        $course['end_date'] = $row['end_date'];

        $sql = "SELECT cr.id FROM course_registration cr INNER JOIN course_trainee ct ON cr.id = ct.course_registration_id WHERE course_id = " . $course['id'];
        if ($resultCount = $db->query($sql)) {
            $course['trainee_count'] = $resultCount->num_rows;
            $resultCount->close();
        } else {
            echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
            $db->close();
            exit();
        }

        array_push($courseList, $course);
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

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    หลักสูตร
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
                                <button type="button" class="btn btn-success pull-right"
                                        onclick="onClickAdd(this)">
                                    <span class="fa fa-plus"></span>&nbsp;
                                    เพิ่มหลักสูตร
                                </button>
                            </div>
                            <div class="box-body">
                                <table id="tableCourse" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 60%; text-align: center">หลักสูตร</th>
                                        <th style="width: 20%; text-align: center">วันที่อบรม</th>
                                        <th style="width: 20%; text-align: center">จำนวนผู้สมัคร (คน)</th>
                                        <th style="text-align: center">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($courseList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="4" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($courseList as $course) {
                                            $courseId = $course['id'];
                                            $courseName = $course['name'];
                                            $beginDate = date_create($course['begin_date']);
                                            $endDate = date_create($course['end_date']);
                                            $courseDate = ($course['begin_date'] === $course['end_date'] ? getThaiShortDate($beginDate) : getThaiIntervalShortDate($beginDate, $endDate));
                                            $courseDateHidden = '<span style="display: none">' . $course['begin_date'] . '</span></span>';
                                            $traineeCount = $course['trainee_count'];
                                            ?>

                                            <tr style="">
                                                <td style="vertical-align: middle"><?php echo $courseName; ?></td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $courseDateHidden . $courseDate; ?></td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $traineeCount; ?></td>

                                                <td style="text-align: center" nowrap>
                                                    <form method="post" action="course_add_edit.php">
                                                        <input type="hidden" name="courseId" value="<?php echo $courseId; ?>"/>
                                                        <button type="submit" class="btn btn-warning">
                                                            <span class="fa fa-pencil"></span>&nbsp;
                                                            แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                                onclick="onClickDelete(this, <?php echo $courseId; ?>, '<?php echo $courseName; ?>')">
                                                            <span class="fa fa-pencil"></span>&nbsp;
                                                            ลบ
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
                order: [[1, 'desc']],
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

        function onClickAdd() {
            window.location.href = 'course_add_edit.php';
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
                },{
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
$db->close();
?>