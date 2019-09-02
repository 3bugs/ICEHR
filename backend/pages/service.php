<?php
require_once '../include/head_php.inc';

$sql = "SELECT * FROM service";
if ($result = $db->query($sql)) {
    $serviceList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($serviceList, $row);
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">แก้ไขข้อมูลบริการ</h4>
                </div>
                <div class="modal-body">
                    <form id="formEdit" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputId">

                            <!--ชื่อบริการ-->
                            <div class="form-group">
                                <label for="inputTitle">ชื่อบริการ:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputTitle"
                                           placeholder="กรอกชื่อบริการ" required
                                           oninvalid="this.setCustomValidity('กรอกชื่อบริการ')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <!--รายละเอียดแบบย่อ-->
                            <div class="form-group">
                                <label for="inputDetails">รายละเอียดแบบย่อ (ไม่เกิน 100 ตัวอักษร):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-tag"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputDetails" maxlength="100"
                                           placeholder="กรอกรายละเอียดแบบย่อ (ไม่เกิน 100 ตัวอักษร)" required
                                           oninvalid="this.setCustomValidity('กรอกรายละเอียดแบบย่อ (ไม่เกิน 100 ตัวอักษร)')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <!--Link-->
                            <div id="divUrl" class="form-group">
                                <label for="inputUrl">Link:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-link"></i>
                                    </span>
                                    <input type="url" class="form-control"
                                           id="inputUrl" maxlength="500"
                                           placeholder="กรอก Link"
                                           oninvalid="this.setCustomValidity('กรอก Link')"
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
                    บริการของหน่วยงาน
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                                <!--<button type="button" class="btn btn-success pull-right"
                                        data-toggle="modal" data-target="#addCourseMasterModal">
                                    <span class="fa fa-plus"></span>&nbsp;
                                    เพิ่มชื่อหลักสูตร
                                </button>-->
                            </div>
                            <div class="box-body">
                                <table id="tableService" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 25%;">ชื่อบริการ</th>
                                        <th style="text-align: center; width: 75%;">รายละเอียดแบบย่อ</th>
                                        <th style="text-align: center;" nowrap>Link</th>
                                        <th style="text-align: center;" nowrap>จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($serviceList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="5" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($serviceList as $service) {
                                            $serviceId = $service['id'];
                                            $serviceTitle = $service['title'];
                                            $serviceDetails = $service['details'];
                                            $serviceUrl = $service['url'];
                                            $serviceSlug = $service['slug'];
                                            ?>
                                            <tr style="">
                                                <td style=""><?= $serviceTitle; ?></td>
                                                <td style=""><?= $serviceDetails; ?></td>
                                                <td style="text-align: center"><?= $serviceSlug === 'hr-intelligence' ? '<a target="_blank" href="' . $serviceUrl . '" title="' . $serviceUrl . '"><i class="fa fa-link"></i></a>' : '&nbsp;'; ?></td>
                                                <td style="text-align: right" nowrap>
                                                    <?php
                                                    if (currentUserHasPermission(PERMISSION_MANAGE_WEB_CONTENT)) {
                                                        ?>
                                                        <button type="button" class="btn btn-warning"
                                                                style="margin-left: 6px; margin-right: 6px"
                                                                onclick="onClickEdit(
                                                                        this,
                                                                <?= $serviceId; ?>,
                                                                        '<?= $serviceTitle; ?>',
                                                                        '<?= $serviceDetails; ?>',
                                                                        '<?= $serviceUrl; ?>',
                                                                <?= $serviceSlug === 'hr-intelligence' ? 'true' : 'false'; ?>)">
                                                            <span class="fa fa-edit"></span>&nbsp;
                                                            แก้ไข
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
            $('#formEdit #divLoading').hide();

            $('#tableService').DataTable({
                ordering: false,
                stateSave: true,
                stateDuration: -1, // sessionStorage
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

            $('#formEdit').submit(event => {
                event.preventDefault();
                if (validateForm()) {
                    doUpdateService();
                }
            });
        });

        function onClickEdit(element, id, title, details, url, showUrl) {
            $('#formEdit #inputId').val(id);
            $('#formEdit #inputTitle').val(title);
            $('#formEdit #inputDetails').val(details);

            const divUrl = $('#formEdit #divUrl');
            if (showUrl) {
                $('#formEdit #inputUrl').val(url);
                divUrl.show();
            } else {
                divUrl.hide();
            }

            $('#editModal').modal('show');
        }

        function validateForm() {
            let valid = true;

            /*let fee = $('#formEdit #inputDetails').val();
            if (fee <= 0) {
                alert('กรอกค่าสมัครเป็นจำนวนเต็มบวก');
                valid = false;
            }*/
            return valid;
        }

        function doUpdateService() {
            $('#formEdit #buttonSave').prop('disabled', true);
            $('#formEdit #divLoading').show();
            $.post(
                '../api/api.php/update_service',
                {
                    id: $('#formEdit #inputId').val(),
                    title: $('#formEdit #inputTitle').val().trim(),
                    details: $('#formEdit #inputDetails').val(),
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formEdit #buttonSave').prop('disabled', false);
                    $('#formEdit #divLoading').hide();
                    $('#formEdit #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formEdit #buttonSave').prop('disabled', false);
                $('#formEdit #divLoading').hide();
                $('#formEdit #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        /*function onClickDelete(element, id, title) {
            BootstrapDialog.show({
                title: 'ลบชื่อหลักสูตร',
                message: 'ยืนยันลบชื่อหลักสูตร \'' + title + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteCourseMaster(id);
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

        function doDeleteCourseMaster(courseMasterId) {
            $.post(
                '../api/api.php/delete_course_master',
                {
                    courseMasterId: courseMasterId,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบชื่อหลักสูตร - ผิดพลาด',
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
                    title: 'ลบชื่อหลักสูตร - ผิดพลาด',
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