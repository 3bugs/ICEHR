<?php
require_once '../include/head_php.inc';

$sql = "SELECT id, username, first_name, last_name, phone_office, email, role FROM user";
if ($result = $db->query($sql)) {
    $userList = array();
    while ($row = $result->fetch_assoc()) {
        $user = array();
        $user['id'] = (int)$row['id'];
        $user['username'] = $row['username'];
        $user['first_name'] = $row['first_name'];
        $user['last_name'] = $row['last_name'];
        $user['phone_office'] = $row['phone_office'];
        $user['email'] = $row['email'];
        $user['role'] = $row['role'];

        array_push($userList, $user);
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
                    ผู้ใช้งานระบบ
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
                                <table id="tableUser" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 15%; text-align: center">Username</th>
                                        <th style="width: 30%; text-align: center">ชื่อ-นามสกุล</th>
                                        <th style="width: 20%; text-align: center">เบอร์โทร</th>
                                        <th style="width: 20%; text-align: center">อีเมล</th>
                                        <th style="width: 15%; text-align: center">สิทธิ์การใช้งาน</th>
                                        <th style="text-align: center">จัดการ</th>
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
                                    if (sizeof($userList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($userList as $user) {
                                            $thaiMonth = array(
                                                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                                                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                                            );
                                            $userId = $user['id'];
                                            $userUsername = $user['username'];
                                            $userFirstName = $user['first_name'];
                                            $userLastName = $user['last_name'];
                                            $userPhoneOffice = $user['phone_office'];
                                            $userEmail = $user['email'];
                                            $userRole = $user['role'];
                                            ?>
                                            <tr style="">
                                                <td style="vertical-align: middle"><?php echo $userUsername; ?></td>
                                                <td style="vertical-align: middle"><?php echo "$userFirstName $userLastName"; ?></td>
                                                <td style="vertical-align: middle"><?php echo $userPhoneOffice; ?></td>
                                                <td style="vertical-align: middle"><?php echo $userEmail; ?></td>
                                                <td style="vertical-align: middle"><?php echo $userRole; ?></td>

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
                                                                onclick="onClickDelete(this, <?php echo $userId; ?>,
                                                                        '<?php echo "$memberTitle $userUsername $userFirstName"; ?>')">
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
            $('#tableUser').DataTable({
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
require_once '../include/foot_php.inc';
?>