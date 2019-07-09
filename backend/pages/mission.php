<?php
require_once '../include/head_php.inc';

$sql = "SELECT a.id,a.title,a.status,a.created_at,a.created_by,u.username "
    . " FROM article a INNER JOIN user u ON a.created_by = u.id "
    . " WHERE a.page_layout ='mission' "
    . " ORDER BY a.created_at DESC";
// echo $sql;
// exit();
//$sql = "SELECT * FROM course ORDER BY begin_date";
if ($result = $db->query($sql)) {
    $itemsList = array();
    while ($row = $result->fetch_assoc()) {
        $course = array();
        $course['id'] = (int)$row['id'];
        $course['name'] = $row['title'];
        $course['status'] = $row['status'];
        $course['created_at'] = $row['created_at'];
        $course['username'] = $row['username'];
        array_push($itemsList, $course);
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
                    ภารกิจ
                    <small>ภารกิจ</small>
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
                                    เพิ่มภารกิจ
                                </button>
                            </div>
                            <div class="box-body">
                                <table id="tableCourse" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 60%; text-align: center">หัวข้อ</th>
                                        <th style="width: 20%; text-align: center">สถานะ</th>
                                        <th style="width: 20%; text-align: center">วันที่สร้าง</th>
                                        <th style="width: 20%; text-align: center">สร้างโดย</th>
                                        <th style="text-align: center">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($itemsList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="4" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($itemsList as $item) {
                                            $itemId = $item['id'];
                                            $itemName = $item['name'];
                                            $status = $item['status'];
                                            $created_at = $item['created_at'];
                                            $created_by = $item['username'];
                                            ?>
                                            <tr style="">
                                                <td style="vertical-align: middle"><?php echo $itemName; ?></td>
                                                <td style="vertical-align: middle; text-align: center">
                                                    <span class="label label-info label-green">
                                                        <?php echo ($status ==='publish' ? 'เผยแพร่':'ไม่เผยแพร่') ?> 
                                                    </span>
                                                </td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $created_at; ?></td>
                                                <td style="vertical-align: middle; text-align: center"><?php echo $created_by; ?></td>

                                                <td style="text-align: center" nowrap>
                                                    <form method="post" action="mission_add_edit.php">
                                                        <input type="hidden" name="itemId" value="<?php echo $itemId; ?>"/>
                                                        <button type="submit" class="btn btn-warning">
                                                            <span class="fa fa-pencil"></span>&nbsp;
                                                            แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                                onclick="onClickDelete(this, <?php echo $itemId; ?>, '<?php echo $itemName; ?>')">
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
            window.location.href = 'mission_add_edit.php';
        }

        function onClickDelete(element, articleId, itemName) {
            BootstrapDialog.show({
                title: 'ลบภารกิจ',
                message: 'ยืนยันลบภารกิจ \'' + itemName + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteArticle(articleId);
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

        function doDeleteArticle(articleId) {
            $.post(
                '../api/api.php/delete_article',
                {
                    articleId: articleId,
                }
            ).done(function (data) {
                console.log(data);
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบภารกิจ - ผิดพลาด',
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
                    title: 'ลบภารกิจ - ผิดพลาด',
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