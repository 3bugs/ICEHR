<?php
require_once '../include/head_php.inc';

$sql = "SELECT id, title, first_name, last_name, pid, signature_image, phone, email, status
        FROM trainer
        WHERE status <> 'deleted'";
if ($result = $db->query($sql)) {
    $trainerList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($trainerList, $row);
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
        <!--Lightbox-->
        <!--<link href="../dist/lightbox/css/lightbox.css" rel="stylesheet">-->

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
                    วิทยากร
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
                                if (currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER)) {
                                    ?>
                                    <button type="button" class="btn btn-success pull-right"
                                            onclick="onClickAdd(this)">
                                        <span class="fa fa-plus"></span>&nbsp;
                                        เพิ่มวิทยากร
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="box-body">
                                <table id="tableTrainer" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 50%; text-align: center">ชื่อ-นามสกุล</th>
                                        <th style="width: 50%; text-align: center">เลขประจำตัวประชาชน</th>
                                        <th style="text-align: center">สถานะ</th>
                                        <th style="text-align: center">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($trainerList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($trainerList as $trainer) {
                                            $thaiMonth = array(
                                                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                                                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                                            );
                                            $trainerInfo = sprintf(
                                                '%s %s %s<br><i class="fa fa-phone" style="color: black"></i> %s<br><i class="fa fa-envelope-o" style="color: black"></i> <a href="mailto:%s">%s</a>',
                                                $trainer['title'], $trainer['first_name'], $trainer['last_name'],
                                                $trainer['phone'], $trainer['email'], $trainer['email']
                                            );
                                            $displayPid = formatPid($trainer['pid']);
                                            ?>
                                            <tr style="">
                                                <td style="vertical-align: top"><?= $trainerInfo; ?></td>
                                                <td style="vertical-align: top; text-align: center"><?= $displayPid; ?></td>
                                                <td style="text-align: center; vertical-align: top">
                                                    <span style="display: none">
                                                        <?= $trainer['status'] == 'active' ? 'on' : 'off' ?>>
                                                    </span>
                                                    <input name="status" type="checkbox"
                                                           data-toggle="toggle"
                                                           onChange="onChangeStatus(this, <?= $trainer['id']; ?>, '<?= "{$trainer['title']} {$trainer['first_name']} {$trainer['last_name']}"; ?>')"
                                                        <?= $trainer['status'] == 'active' ? 'checked' : '' ?>>
                                                </td>

                                                <td style="text-align: center" nowrap>
                                                    <?php
                                                    if (currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER)) {
                                                        ?>
                                                        <form method="get" action="trainer_add_edit.php" style="display: inline">
                                                            <input type="hidden" name="trainer_id" value="<?= $trainer['id']; ?>"/>
                                                            <button type="submit" class="btn btn-warning" style="margin-right: 3px">
                                                                <span class="fa fa-pencil"></span>&nbsp;
                                                                แก้ไข
                                                            </button>
                                                        </form>
                                                        <?php
                                                    }
                                                    ?>

                                                    <?php
                                                    if (currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER)) {
                                                        ?>
                                                        <button type="button" class="btn btn-danger"
                                                                onclick="onClickDelete(
                                                                        this,
                                                                <?= $trainer['id']; ?>,
                                                                        '<?= "{$trainer['title']} {$trainer['first_name']} {$trainer['last_name']}"; ?>'
                                                                        )">
                                                            <span class="fa fa-remove"></span>&nbsp;
                                                            ลบ
                                                        </button>
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
            $('#tableTrainer').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
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
            window.location.href = 'trainer_add_edit.php';
        }

        function onChangeStatus(element, trainerId, trainerDisplayName) {
            let result = confirm("ยืนยัน '" + (element.checked ? 'เปิด' : 'ปิด') + "' วิทยากรนี้ (" + trainerDisplayName + ")?");
            if (result) {
                doChangeStatus(trainerId, (element.checked ? 'active' : 'inactive'));
            } else {
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
            }
        }

        function onClickDelete(element, trainerId, trainerDisplayName) {
            let result = confirm("ยืนยันลบวิทยากรนี้ (" + trainerDisplayName + ")?");
            if (result) {
                doChangeStatus(trainerId, 'deleted');
            } else {
                //do nothing
            }
        }

        function doChangeStatus(trainerId, newStatus) {
            let title = 'แก้ไขสถานะวิทยากร';
            if (newStatus === 'deleted') {
                title = 'ลบวิทยากร';
            }

            $.post(
                '../api/api.php/update_trainer_status',
                {
                    trainerId: trainerId,
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

        /*function onChangeStatus_Old(element, userId) {
            BootstrapDialog.show({
                title: "Change User's Status",
                message: 'ยืนยันเปลี่ยนสถานะผู้ใช้งาน?',
                buttons: [
                    {
                        label: 'ยกเลิก',
                        action: function (self) {
                            self.close();
                            location.reload(true);
                        }
                    },
                    {
                        label: 'เปลี่ยนสถานะ',
                        action: function (self) {
                            self.close();
                            const newStatus = element.checked ? 1 : 0;
                            doChangeStatus(electionId, newStatus);
                        }
                    }
                ]
            });
        }*/

        /*function doDeleteUser(userId) {
            $.post(
                '../api/api.php/delete_user',
                {
                    member_id: memberId
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'Delete Member',
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
                    title: 'Delete Member',
                    message: 'เกิดข้อผิดพลาดในการเชื่อมต่อ Server',
                    buttons: [{
                        label: 'ปิด',
                        action: function (self) {
                            self.close();
                        }
                    }]
                });
            });
        }*/

        /*function onClickDelete_Old(element, userId, username, userDisplayName) {
            BootstrapDialog.show({
                title: 'Delete User',
                message: 'ยืนยันลบสมาชิก \'' + userDisplayName + '\' ?',
                buttons: [
                    {
                        label: 'ยกเลิก',
                        action: function (self) {
                            self.close();
                        }
                    },
                    {
                        label: 'ลบ',
                        action: function (self) {
                            self.close();
                            //doDeleteMember(memberId);
                        }
                    }
                ]
            });
        }*/

        /*function onClickEdit(element, electionId, dateString) {
            window.location.href = 'election_add.php?edit=true&election_id=' + electionId;
        }*/
    </script>

    <?php require_once('../include/foot.inc'); ?>
    <!-- DataTables -->
    <script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <!--Lightbox-->
    <!--<script src="../dist/lightbox/js/lightbox.js"></script>-->
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>