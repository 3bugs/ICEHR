<?php
require_once '../include/head_php.inc';

$validTableNameList = array(
    'name_title', 'training_course_category'
);
$tableName = $_GET['table_name'];
if (!in_array($tableName, $validTableNameList)) {
    echo 'ERROR: ชื่อตารางข้อมูลไม่ถูกต้อง! (Invalid data table name!)';
    $db->close();
    exit();
}

$headerList = array(
    "name_title" => 'คำนำหน้าชื่อ',
    "training_course_category" => 'หมวดหมู่หลักสูตร'
);
$subHeaderList = array(
    "training_course_category" => $serviceTypeText[SERVICE_TYPE_TRAINING]
);
$header = $headerList[$tableName];
$subHeader = $subHeaderList[$tableName];

/* ------------------------------------------------------------------ */

$sql = "SELECT * FROM $tableName ORDER BY id";
if ($result = $db->query($sql)) {
    $dataList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($dataList, $row);
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

    <!-- Add Data Modal -->
    <div class="modal fade" id="addModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">เพิ่ม<?= $header; ?></h4>
                </div>
                <div class="modal-body">
                    <form id="formAdd" role="form"
                          style="margin-top: 0; margin-bottom: 0">

                        <div class="box-body">
                            <!--title-->
                            <div class="form-group">
                                <label for="inputTitle"><?= $header; ?>:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputTitle"
                                           placeholder="กรอก<?= $header; ?>" required
                                           oninvalid="this.setCustomValidity('กรอก<?= $header; ?>')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <div id="divLoading" style="text-align: center">
                                <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                            </div>
                            <div id="responseText"
                                 style="text-align: center; color: red; margin-top: 25px; margin-bottom: 20px;">
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button id="buttonSave" type="submit"
                                    class="btn btn-info pull-right">
                                <span class="fa fa-save"></span>&nbsp;
                                บันทึก
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

    <!-- Edit Data Modal -->
    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">แก้ไข<?= $header; ?></h4>
                </div>
                <div class="modal-body">
                    <form id="formEdit" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputId">

                            <!--title-->
                            <div class="form-group">
                                <label for="inputTitle"><?= $header; ?>:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputTitle"
                                           placeholder="กรอก<?= $header; ?>" required
                                           oninvalid="this.setCustomValidity('กรอก<?= $header; ?>')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <div id="divLoading" style="text-align: center">
                                <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                            </div>
                            <div id="responseText"
                                 style="text-align: center; color: red; margin-top: 25px; margin-bottom: 20px;">
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button id="buttonSave" type="submit"
                                    class="btn btn-info pull-right">
                                <span class="fa fa-save"></span>&nbsp;
                                บันทึก
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
                    <?= $header; ?>
                    <small><?= $subHeader; ?></small>
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
                                if (($tableName === 'training_course_category' && currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER))
                                    || ($tableName === 'name_title' /*&& currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER)*/)) {
                                    ?>
                                    <button type="button" class="btn btn-success pull-right"
                                            data-toggle="modal" data-target="#addModal"
                                            style="margin-right: 5px">
                                        <span class="fa fa-plus"></span>&nbsp;
                                        เพิ่ม<?= $header; ?>
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="box-body">
                                <table id="table" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 100%;"><?= $header; ?></th>
                                        <th style="text-align: center;" nowrap>จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($dataList) == 0) {
                                        ?>
                                        <!--<tr valign="middle">
                                            <td colspan="5" align="center">ไม่มีข้อมูล</td>
                                        </tr>-->
                                        <?php
                                    } else {
                                        foreach ($dataList as $data) {
                                            $id = $data['id'];
                                            $title = $data['title'];
                                            ?>
                                            <tr style="">
                                                <td style=""><?= $title; ?></td>
                                                <td style="text-align: center" nowrap>
                                                    <?php
                                                    if (($tableName === 'training_course_category' && currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER))
                                                        || ($tableName === 'name_title' /*&& currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_COURSE_MASTER)*/)) {
                                                        ?>
                                                        <button type="button" class="btn btn-warning"
                                                                style="margin-left: 6px; margin-right: 3px"
                                                                onclick="onClickEdit(this, <?= $id; ?>, '<?= $title; ?>')">
                                                            <span class="fa fa-edit"></span>&nbsp;
                                                            แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                                style="margin-left: 3px; margin-right: 6px"
                                                                onclick="onClickDelete(this, <?= $id; ?>, '<?= $title; ?>')">
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
            $('#formAdd #divLoading').hide();
            $('#formEdit #divLoading').hide();

            $('#formAdd').submit(event => {
                event.preventDefault();
                doAdd();
            });
            $('#formEdit').submit(event => {
                event.preventDefault();
                doUpdate();
            });

            $('#table').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[0, 'asc']],
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

        function doAdd() {
            $('#formAdd #responseText').text('');
            $('#formAdd #buttonSave').prop('disabled', true);
            $('#formAdd #divLoading').show();
            $.post(
                '../api/api.php/add',
                {
                    tableName: '<?= $tableName; ?>',
                    title: $('#formAdd #inputTitle').val()
                }
            ).done(function (data) {
                $('#formAdd #buttonSave').prop('disabled', false);
                $('#formAdd #divLoading').hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formAdd #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formAdd #buttonSave').prop('disabled', false);
                $('#formAdd #divLoading').hide();
                $('#formAdd #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickEdit(element, id, title) {
            $('#formEdit #inputId').val(id);
            $('#formEdit #inputTitle').val(title);
            $('#editModal').modal('show');
        }

        function doUpdate() {
            $('#formEdit #responseText').text('');
            $('#formEdit #buttonSave').prop('disabled', true);
            $('#formEdit #divLoading').show();
            $.post(
                '../api/api.php/update',
                {
                    tableName: '<?= $tableName; ?>',
                    id: $('#formEdit #inputId').val(),
                    title: $('#formEdit #inputTitle').val()
                }
            ).done(function (data) {
                $('#formEdit #buttonSave').prop('disabled', false);
                $('#formEdit #divLoading').hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formEdit #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formEdit #buttonSave').prop('disabled', false);
                $('#formEdit #divLoading').hide();
                $('#formEdit #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickDelete(element, id, title) {
            BootstrapDialog.show({
                title: 'ลบ<?= $header; ?>',
                message: 'ยืนยันลบ \'' + title + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDelete(id);
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

        function doDelete(id) {
            $.post(
                '../api/api.php/delete',
                {
                    tableName: '<?= $tableName; ?>',
                    id: id,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบ<?= $header; ?> - ผิดพลาด',
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
                    title: 'ลบ<?= $header; ?> - ผิดพลาด',
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