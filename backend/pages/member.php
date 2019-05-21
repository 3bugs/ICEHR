<?php
require_once '../include/head_php.inc';

$sql = "SELECT id, title, first_name, last_name, email FROM member";
if ($result = $db->query($sql)) {
    $memberList = array();
    while ($row = $result->fetch_assoc()) {
        $member = array();
        $member['id'] = (int)$row['id'];
        $member['title'] = $row['title'];
        $member['first_name'] = $row['first_name'];
        $member['last_name'] = $row['last_name'];
        $member['age'] = (int)$row['age'];
        $member['job_position'] = $row['job_position'];
        $member['organization_name'] = $row['organization_name'];
        $member['organization_type'] = $row['organization_type'];
        $member['phone'] = $row['phone'];
        $member['email'] = $row['email'];
        $member['password'] = $row['password'];

        array_push($memberList, $member);
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
                    สมาชิกเว็บไซท์
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
                                <table id="tableMember" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 15%; text-align: center">คำนำหน้า</th>
                                        <th style="width: 30%; text-align: center">ชื่อ-นามสกุล</th>
                                        <th style="width: 27%; text-align: center">อีเมล</th>
                                        <th style="width: 28%; text-align: center">เบอร์โทร</th>
                                        <th style="text-align: center">รายละเอียด</th>
                                        <?php
                                        if ($_SESSION[KEY_SESSION_USER_ROLE] == 'super_admin') {
                                            ?>
                                            <!--<th style="text-align: center">แก้ไข</th>-->
                                            <th style="width: 10px; text-align: center">ลบ</th>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($memberList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="4" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($memberList as $member) {
                                            $thaiMonth = array(
                                                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                                                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                                            );
                                            $memberId = $member['id'];
                                            $memberTitle = $member['title'];
                                            $memberFirstName = $member['first_name'];
                                            $memberLastName = $member['last_name'];
                                            $memberEmail = $member['email'];
                                            $memberPhone = $member['phone'];
                                            ?>
                                            <tr style="">
                                                <td style="vertical-align: middle"><?php echo $memberTitle; ?></td>
                                                <td style="vertical-align: middle"><?php echo "$memberFirstName $memberLastName"; ?></td>
                                                <td style="vertical-align: middle"><?php echo $memberEmail; ?></td>
                                                <td style="vertical-align: middle"><?php echo $memberPhone; ?></td>

                                                <td style="text-align: center" nowrap>
                                                    <button type="button" class="btn btn-info">
                                                        <span class="fa fa-info"></span>&nbsp;
                                                        รายละเอียด
                                                    </button>
                                                </td>

                                                <?php
                                                if ($_SESSION[KEY_SESSION_USER_ROLE] == 'super_admin') {
                                                    ?>
                                                    <!--<td style="text-align: center">
                                                        <form method="post" action="">
                                                            <input type="hidden" name="edit_mode" value="true">
                                                            <input type="hidden" name="election_id"
                                                                   value="<?php /*echo $member['id']; */?>">
                                                            <button type="submit" class="btn btn-warning">
                                                                <span class="fa fa-edit"></span>&nbsp;
                                                                แก้ไข
                                                            </button>
                                                        </form>
                                                    </td>-->
                                                    <td style="width: 10px; text-align: center">
                                                        <button type="button" class="btn btn-danger"
                                                                onclick="onClickDelete(this, <?php echo $memberId; ?>,
                                                                        '<?php echo "$memberTitle $memberFirstName $memberLastName"; ?>')">
                                                            <span class="fa fa-remove"></span>&nbsp;
                                                            ลบ
                                                        </button>
                                                    </td>
                                                    <?php
                                                }
                                                ?>
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
            $('#tableMember').DataTable({
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

        function doDeleteMember(memberId) {
            $.post(
                '../api/api.php/delete_member',
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
        }

        function onClickDelete(element, memberId, memberDisplayName) {
            BootstrapDialog.show({
                title: 'Confirm Delete Member',
                message: 'ยืนยันลบสมาชิก \'' + memberDisplayName + '\' ?',
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
                            doDeleteMember(memberId);
                        }
                    }
                ]
            });
        }

        function onClickEdit(element, electionId, dateString) {
            window.location.href = 'election_add.php?edit=true&election_id=' + electionId;
        }

        function doChangeStatus(electionId, newStatus) {
            $.post(
                '../api/api.php/update_election_status',
                {
                    election_id: electionId,
                    new_status: newStatus
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'Change Election Status',
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
                    title: 'Change Election Status',
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

        function onChangeStatus(element, electionId, dateString) {
            BootstrapDialog.show({
                title: 'Confirm Change Election Status',
                message: 'ยืนยันเปลี่ยนสถานะการเลือกตั้ง วันที่ ' + dateString + ' ?',
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