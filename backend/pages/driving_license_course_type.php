<?php
require_once '../include/head_php.inc';

$sql = "SELECT * FROM driving_license_course_type ORDER BY id";
if ($result = $db->query($sql)) {
    $courseTypeList = array();
    while ($row = $result->fetch_assoc()) {
        $courseType = array();
        $courseType['id'] = (int)$row['id'];
        $courseType['title'] = $row['title'];
        $courseType['title_en'] = $row['title_en'];
        $courseType['application_fee'] = (int)$row['application_fee'];

        array_push($courseTypeList, $courseType);
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
    <body class="hold-transition skin-blue sidebar-mini fixed">

    <!-- Edit Course Type Modal -->
    <div class="modal fade" id="editCourseTypeModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">แก้ไขข้อมูลประเภทหลักสูตร</h4>
                </div>
                <div class="modal-body">
                    <form id="formEditCourseType" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputCourseTypeId">

                            <!--ชื่อประเภทหลักสูตร-->
                            <div class="form-group">
                                <label for="inputCourseTypeTitle">ชื่อประเภทหลักสูตร:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputCourseTypeTitle"
                                           placeholder="กรอกชื่อประเภทหลักสูตร" required
                                           oninvalid="this.setCustomValidity('กรอกชื่อประเภทหลักสูตร')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <!--ชื่อประเภทหลักสูตร ภาษาอังกฤษ-->
                            <div class="form-group">
                                <label for="inputCourseTypeTitleEn">ชื่อประเภทหลักสูตร ภาษาอังกฤษ:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputCourseTypeTitleEn"
                                           placeholder="กรอกชื่อประเภทหลักสูตร ภาษาอังกฤษ" required
                                           oninvalid="this.setCustomValidity('กรอกชื่อประเภทหลักสูตร ภาษาอังกฤษ')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <!--ราคา-->
                            <div class="form-group">
                                <label for="inputCourseTypeFee">ค่าสมัคร (บาท):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <strong>฿</strong>
                                    </span>
                                    <input type="number" class="form-control"
                                           id="inputCourseTypeFee"
                                           placeholder="กรอกราคา" required
                                           oninvalid="this.setCustomValidity('กรอกราคา')"
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
                    ประเภทหลักสูตร
                    <small><?= $serviceTypeText[SERVICE_TYPE_DRIVING_LICENSE]; ?></small>
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
                                <table id="tableCourseType" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 80%;">ชื่อประเภทหลักสูตร</th>
                                        <th style="text-align: center; width: 20%;">ค่าสมัคร (บาท)</th>
                                        <th style="text-align: center;" nowrap>จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($courseTypeList) == 0) {
                                        ?>
                                        <!--<tr valign="middle">
                                            <td colspan="5" align="center">ไม่มีข้อมูล</td>
                                        </tr>-->
                                        <?php
                                    } else {
                                        foreach ($courseTypeList as $courseType) {
                                            $courseTypeId = $courseType['id'];
                                            $courseTypeTitle = $courseType['title'];
                                            $courseTypeTitleEn = $courseType['title_en'];
                                            $courseTypeFee = $courseType['application_fee'];
                                            ?>
                                            <tr style="">
                                                <td style=""><?= $courseType['title'] . ($courseTypeTitleEn ? " /<br>{$courseTypeTitleEn}" : ''); ?></td>
                                                <td style="text-align: center"><?= $courseTypeFee; ?></td>
                                                <td style="text-align: center" nowrap>
                                                    <?php
                                                    if (currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_COURSE_MASTER)) {
                                                        ?>
                                                        <button type="button" class="btn btn-warning"
                                                                style="margin-left: 6px; margin-right: 6px"
                                                                    onclick="onClickEdit(
                                                                        this,
                                                                    <?= $courseTypeId; ?>,
                                                                            '<?= htmlentities($courseTypeTitle, ENT_QUOTES); ?>',
                                                                            '<?= htmlentities($courseTypeTitleEn, ENT_QUOTES); ?>',
                                                                    <?= $courseTypeFee; ?>)">
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
            $('#formEditCourseType #divLoading').hide();

            $('#tableCourseType').DataTable({
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

            $('#formEditCourseType').submit(event => {
                event.preventDefault();
                if (validateForm()) {
                    doUpdateCourseType();
                }
            });
        });

        function onClickEdit(element, courseTypeId, courseTypeTitle, courseTypeTitleEn, courseTypeFee) {
            $('#formEditCourseType #inputCourseTypeId').val(courseTypeId);
            $('#formEditCourseType #inputCourseTypeTitle').val(courseTypeTitle);
            $('#formEditCourseType #inputCourseTypeTitleEn').val(courseTypeTitleEn);
            $('#formEditCourseType #inputCourseTypeFee').val(courseTypeFee);
            $('#editCourseTypeModal').modal('show');
        }

        function validateForm() {
            let valid = true;

            let fee = $('#formEditCourseType #inputCourseTypeFee').val();
            if (fee <= 0) {
                alert('กรอกค่าสมัครเป็นจำนวนเต็มบวก');
                valid = false;
            }
            return valid;
        }

        function doUpdateCourseType() {
            $('#formEditCourseType #buttonSave').prop('disabled', true);
            $('#formEditCourseType #divLoading').show();
            $.post(
                '../api/api.php/update_driving_license_course_type',
                {
                    id: $('#formEditCourseType #inputCourseTypeId').val(),
                    title: $('#formEditCourseType #inputCourseTypeTitle').val().trim(),
                    titleEn: $('#formEditCourseType #inputCourseTypeTitleEn').val().trim(),
                    fee: $('#formEditCourseType #inputCourseTypeFee').val(),
                }
            ).done(function (data) {
                $('#formEditCourseType #buttonSave').prop('disabled', false);
                $('#formEditCourseType #divLoading').hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formEditCourseType #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formEditCourseType #buttonSave').prop('disabled', false);
                $('#formEditCourseType #divLoading').hide();
                $('#formEditCourseType #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickDelete(element, courseMasterId, courseMasterTitle) {
            BootstrapDialog.show({
                title: 'ลบชื่อหลักสูตร',
                message: 'ยืนยันลบชื่อหลักสูตร \'' + courseMasterTitle + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteCourseMaster(courseMasterId);
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