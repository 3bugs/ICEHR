<?php
require_once '../include/head_php.inc';

$sql = "SELECT u.id, u.username, u.title, u.first_name, u.last_name, u.position, ud.name AS department, u.email, 
               u.phone, u.phone_office, u.phone_extension, u.image_file_name, u.status, u.permissions, u.show_on_web
        FROM user u INNER JOIN user_department ud ON u.department_id = ud.id
        WHERE u.status <> 'deleted'";
if ($result = $db->query($sql)) {
    $userList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($userList, $row);
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
        <link href="../dist/lightbox/css/lightbox.css" rel="stylesheet">

        <style>
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini fixed">
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
                                <?php
                                if (currentUserHasPermission(PERMISSION_USER_CREATE)) {
                                    ?>
                                    <button type="button" class="btn btn-success pull-right"
                                            onclick="onClickAdd(this)">
                                        <span class="fa fa-plus"></span>&nbsp;
                                        เพิ่มผู้ใช้
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="box-body">
                                <table id="tableUser" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center">รูปภาพ</th>
                                        <!--<th style="width: 20%; text-align: center">ชื่อผู้ใช้</th>-->
                                        <th style="width: 40%; text-align: center">ชื่อ-นามสกุล</th>
                                        <th style="width: 40%; text-align: center">ฝ่าย / ตำแหน่ง</th>
                                        <th style="width: 20%; text-align: center">อีเมล</th>
                                        <!--<th style="width: 35%; text-align: center">สิทธิ์</th>-->
                                        <th style="text-align: center">แสดง</th>
                                        <th style="text-align: center">สถานะ</th>
                                        <th style="text-align: center">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($userList) == 0) {
                                        ?>
                                        <!--<tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>-->
                                        <?php
                                    } else {
                                        foreach ($userList as $user) {
                                            $thaiMonth = array(
                                                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                                                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                                            );
                                            $userInfo = sprintf(
                                                '%s%s %s %s%s<br><i class="fa fa-phone" style="color: black"></i> %s<br><i class="fa fa-envelope-o" style="color: black"></i> <a href="mailto:%s">%s</a>',
                                                (int)$user['id'] === (int)$_SESSION[KEY_SESSION_USER_ID] ? '<strong>' : '',
                                                $user['title'], $user['first_name'], $user['last_name'],
                                                (int)$user['id'] === (int)$_SESSION[KEY_SESSION_USER_ID] ? '</strong>' : '',
                                                $user['phone'], $user['email'], $user['email']
                                            );
                                            $departmentAndPosition = sprintf(
                                                '%s / %s<br><i class="fa fa-phone" style="color: black"></i> %s (ต่อ %s)',
                                                $user['department'], $user['position'], $user['phone_office'], $user['phone_extension']
                                            );
                                            $image = sprintf(
                                                '<a href="%s" data-lightbox="coverImage" data-title="%s"><img src="%s" width="60px" style="border-radius: 50%%;"/></a>',
                                                UPLOAD_DIR_USER_ASSETS . $user['image_file_name'],
                                                "{$user['title']} {$user['first_name']} {$user['last_name']}",
                                                UPLOAD_DIR_USER_ASSETS . $user['image_file_name']
                                            );
                                            $permissionTags = createPermissionTags((int)$user['permissions']);
                                            ?>
                                            <tr style="">
                                                <td style="text-align: center; vertical-align: top"><?= $image; ?></td>
                                                <!--<td style="vertical-align: top; font-family: monospace,serif">
                                                    <?/*= $user['username'] . ((int)$user['id'] === (int)$_SESSION[KEY_SESSION_USER_ID] ? '<br/><span class="label label-success"><i class="fa fa-chevron-up" style=""></i>&nbsp;YOU</span>' : ''); */?>
                                                </td>-->
                                                <td style="vertical-align: top"><?= $userInfo; ?></td>
                                                <td style="vertical-align: top"><?= $departmentAndPosition; ?></td>
                                                <td style="vertical-align: top"><?= $user['email']; ?></td>

                                                <!--<td style="vertical-align: top"><?/*= $permissionTags; */?></td>-->

                                                <td style="text-align: center; vertical-align: top">
                                                    <span style="display: none">
                                                        <?= $user['show_on_web']; ?>>
                                                    </span>
                                                    <input name="status" type="checkbox"
                                                           class="my-toggle"
                                                           data-toggle="toggle"
                                                           onChange="onChangeShowOnWeb(this, <?= $user['id']; ?>, '<?= $user['username']; ?>', '<?= "{$user['title']} {$user['first_name']} {$user['last_name']}"; ?>', <?= currentUserHasPermission(PERMISSION_USER_UPDATE) ? 'true' : 'false' ?>)"
                                                        <?= (int)$user['show_on_web'] === 1 ? 'checked' : '' ?>>
                                                </td>

                                                <td style="text-align: center; vertical-align: top">
                                                    <span style="display: none">
                                                        <?= $user['status'] == 'active' ? 'on' : 'off' ?>>
                                                    </span>
                                                    <input name="status" type="checkbox"
                                                           class="my-toggle"
                                                           data-toggle="toggle"
                                                           onChange="onChangeStatus(this, <?= $user['id']; ?>, '<?= $user['username']; ?>', '<?= "{$user['title']} {$user['first_name']} {$user['last_name']}"; ?>', <?= currentUserHasPermission(PERMISSION_USER_UPDATE) ? 'true' : 'false' ?>)"
                                                        <?= $user['status'] == 'active' ? 'checked' : '' ?>>
                                                </td>

                                                <td style="text-align: center" nowrap>
                                                    <?php
                                                    if (currentUserHasPermission(PERMISSION_USER_UPDATE) || ((int)$user['id'] === (int)$_SESSION[KEY_SESSION_USER_ID])) {
                                                        ?>
                                                        <form method="get" action="user_add_edit.php" style="display: inline">
                                                            <input type="hidden" name="user_id" value="<?= $user['id']; ?>"/>
                                                            <button type="submit" class="btn btn-warning" style="margin-right: 3px">
                                                                <span class="fa fa-pencil"></span>&nbsp;
                                                                แก้ไข
                                                            </button>
                                                        </form>
                                                        <?php
                                                    }
                                                    ?>

                                                    <?php
                                                    if (currentUserHasPermission(PERMISSION_USER_DELETE)) {
                                                        ?>
                                                        <button type="button" class="btn btn-danger"
                                                                onclick="onClickDelete(
                                                                        this,
                                                                <?= $user['id']; ?>,
                                                                        '<?= $user['username']; ?>',
                                                                        '<?= "{$user['title']} {$user['first_name']} {$user['last_name']}"; ?>'
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
            $('#tableUser').DataTable({
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
                },
                drawCallback: function(row, data) {
                    $('.my-toggle').bootstrapToggle();
                }
            });
        });

        function onClickAdd() {
            window.location.href = 'user_add_edit.php';
        }

        function onChangeShowOnWeb(element, userId, username, userDisplayName, hasPermission) {
            if (!hasPermission) {
                alert('คุณไม่มีสิทธิ์สำหรับการดำเนินการนี้');
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
                return;
            }

            let result = confirm("ยืนยัน '" + (element.checked ? 'เปิด' : 'ยกเลิก') + "' การแสดงผลผู้ใช้คนนี้ (" + userDisplayName + ") บนหน้าเว็บ?");
            if (result) {
                doChangeShowOnWeb(userId, (element.checked ? 1 : 0));
            } else {
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
            }
        }

        function onChangeStatus(element, userId, username, userDisplayName, hasPermission) {
            if (!hasPermission) {
                alert('คุณไม่มีสิทธิ์สำหรับการดำเนินการนี้');
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
                return;
            }

            let result = confirm("ยืนยัน '" + (element.checked ? 'เปิด' : 'ยกเลิก') + "' การใช้งานระบบ สำหรับผู้ใช้คนนี้ (" + userDisplayName + ")?");
            if (result) {
                doChangeStatus(userId, (element.checked ? 'active' : 'inactive'));
            } else {
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
            }
        }

        function onClickDelete(element, userId, username, userDisplayName) {
            let result = confirm("ยืนยันลบผู้ใช้คนนี้ (" + userDisplayName + ")?");
            if (result) {
                doChangeStatus(userId, 'deleted');
            } else {
                //do nothing
            }
        }

        function doChangeShowOnWeb(userId, newShowOnWeb) {
            let title = 'แก้ไขสถานะการแสดงผลบนหน้าเว็บของผู้ใช้งานระบบ';
            if (newShowOnWeb === 'deleted') {
                title = 'ลบผู้ใช้งานระบบ';
            }

            $.post(
                '../api/api.php/update_user_show_on_web',
                {
                    userId: userId,
                    newShowOnWeb: newShowOnWeb
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

        function doChangeStatus(userId, newStatus) {
            let title = 'แก้ไขสถานะผู้ใช้งานระบบ';
            if (newStatus === 'deleted') {
                title = 'ลบผู้ใช้งานระบบ';
            }

            $.post(
                '../api/api.php/update_user_status',
                {
                    userId: userId,
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
    <script src="../dist/lightbox/js/lightbox.js"></script>
    </body>
    </html>

<?php
function createPermissionTags($userPermissions)
{
    global $permissionList, $permissionText, $permissionColor;

    $tags = '';
    foreach ($permissionList as $permission) {
        if (userHasPermission($userPermissions, $permission)) {
            $color = $permissionColor[$permission];
            $tags .= '<span style="display: inline-block" class="label label-' . $color . '" style="margin-right: 4px">' . $permissionText[$permission] . '</span>&nbsp;';
        }
    }
    return $tags;
}

require_once '../include/foot_php.inc';
?>