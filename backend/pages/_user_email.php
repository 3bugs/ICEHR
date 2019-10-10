<?php
$_GET['user'] = 1;
require_once '../include/head_php.inc';

$sql = "SELECT * FROM user WHERE status = 'active'";
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
    <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php /*require_once('../include/header.inc'); */?>
        <?php /*require_once('../include/sidebar.inc'); */?>

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
                                        <th style="text-align: center">รูปภาพ</th>
                                        <th style="width: 50%; text-align: center">ชื่อ-นามสกุล</th>
                                        <th style="width: 50%; text-align: center">อีเมล</th>
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
                                            ?>
                                            <tr style="">
                                                <td style="text-align: center; vertical-align: top"><?= $image; ?></td>
                                                <td style="vertical-align: top"><?= $userInfo; ?></td>
                                                <td style="vertical-align: top"><?= $user['email']; ?></td>
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
require_once '../include/foot_php.inc';
?>