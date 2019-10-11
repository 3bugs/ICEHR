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

$userHasPermission = currentUserHasPermission(PERMISSION_MANAGE_WEB_CONTENT);
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
                    <h4 class="modal-title" id="title">เพิ่ม/แก้ไขบริการ</h4>
                </div>
                <div class="modal-body">
                    <form id="formEdit" role="form" method="post"
                          action="../api/api.php/add_update_service"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputId" name="id">

                            <!--ชื่อบริการ-->
                            <div class="form-group">
                                <label for="inputTitle">ชื่อบริการ:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputTitle" name="title"
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
                                           id="inputDetails" name="details" maxlength="100"
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
                                    <input type="text" class="form-control"
                                           id="inputUrl" name="url" maxlength="500"
                                           placeholder="กรอก Link" required
                                           oninvalid="this.setCustomValidity('กรอก Link')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <!--Icon-->
                            <div class="form-group">
                                <label for="inputIcon">Icon:</label>
                                <div class="input-group">
                                    <span class="input-group-addon" style="padding: 5px; border: 1px solid lightgrey">
                                        <img id="imgPreview" width="28px" height="28px">
                                    </span>
                                    <input id="inputIcon" name="icon"
                                           type="file" accept="image/*"/>
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
                                <?php
                                if ($userHasPermission) {
                                    ?>
                                    <button type="button" class="btn btn-success pull-right"
                                            onclick="onClickAdd(this)"
                                            data-toggle_x="modal" data-target_x="#addCourseMasterModal">
                                        <span class="fa fa-plus"></span>&nbsp;
                                        เพิ่มบริการ
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="box-body">
                                <table id="tableService" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 25%;">ชื่อบริการ</th>
                                        <th style="text-align: center; width: 75%;">รายละเอียดแบบย่อ</th>
                                        <!--<th style="text-align: center;" nowrap>Link</th>-->
                                        <th style="text-align: center;" nowrap>Icon</th>
                                        <th style="text-align: center">สถานะ</th>
                                        <th style="text-align: center;" nowrap>จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($serviceList) == 0) {
                                        ?>
                                        <!--<tr valign="middle">
                                            <td colspan="5" align="center">ไม่มีข้อมูล</td>
                                        </tr>-->
                                        <?php
                                    } else {
                                        foreach ($serviceList as $service) {
                                            $serviceId = $service['id'];
                                            $serviceTitle = $service['title'];
                                            $serviceDetails = $service['details'];
                                            $serviceUrl = $service['url'];
                                            $serviceIconFileName = $service['icon_file_name'];
                                            $serviceStatus = (int)$service['status'] === 1;
                                            $serviceSlug = $service['slug'];
                                            ?>
                                            <tr style="">
                                                <td style=""><?= $serviceTitle; ?></td>
                                                <td style=""><?= $serviceDetails; ?></td>
                                                <!--<td style="text-align: center"><a target="_blank" href="<?/*= $serviceUrl; */ ?>" title="<?/*= $serviceUrl; */ ?>"><i class="fa fa-link"></i></a></td>-->
                                                <td style="text-align: center"><img src="<?= UPLOAD_DIR_SERVICE_ICONS . $serviceIconFileName ?>" width="28px" height="28px"></td>

                                                <td style="text-align: center; vertical-align: top">
                                                    <span style="display: none">
                                                        <?= $serviceStatus ? 'on' : 'off' ?>>
                                                    </span>
                                                    <input name="status" type="checkbox"
                                                           data-toggle="toggle"
                                                           onChange="onChangeStatus(
                                                                   this,
                                                           <?= $serviceId; ?>,
                                                                   '<?= htmlentities($serviceTitle); ?>',
                                                           <?= $userHasPermission; ?>
                                                                   )"
                                                        <?= $serviceStatus ? 'checked' : '' ?>>
                                                </td>

                                                <td style="text-align: right" nowrap>
                                                    <?php
                                                    if ($userHasPermission) {
                                                        ?>
                                                        <button type="button" class="btn btn-warning"
                                                                style="margin-left: 6px; margin-right: 6px"
                                                                onclick="onClickEdit(
                                                                        this,
                                                                <?= $serviceId; ?>,
                                                                        '<?= $serviceTitle; ?>',
                                                                        '<?= $serviceDetails; ?>',
                                                                        '<?= $serviceUrl; ?>',
                                                                        '<?= $serviceIconFileName; ?>',
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
                order: [[1, 'asc']],
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

        $(function () {
            const setIconPreview = function (input, placeToInsertImagePreview) {
                //$(placeToInsertImagePreview).empty();
                //$(placeToInsertImagePreview).hide();

                $('#formEdit #imgPreview').attr('src', '<?= UPLOAD_DIR_SERVICE_ICONS; ?>' + 'blank.png');

                if (input.files) {
                    let fileCount = 1; //input.files.length;

                    for (let i = 0; i < fileCount; i++) {
                        //$(placeToInsertImagePreview).show();
                        let reader = new FileReader();

                        reader.onload = function (event) {
                            //$($.parseHTML('<img style="width: auto; height: 120px; margin: 3px">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                            $('#imgPreview').attr('src', event.target.result);
                        };
                        reader.readAsDataURL(input.files[i]);
                    }
                }
            };

            $('#inputIcon').on('change', function () {
                setIconPreview(this, 'div#cover-image-upload-preview');
            });
        });

        function onClickAdd(element) {
            $('#editModal #title').text('เพิ่มบริการ');
            $('#formEdit #inputIcon').attr('required', 'true');

            $('#formEdit #inputId').val('0');
            $('#formEdit #inputTitle').val('');
            $('#formEdit #inputDetails').val('');
            $('#formEdit #inputUrl').val('');
            $('#formEdit #imgPreview').attr('src', '<?= UPLOAD_DIR_SERVICE_ICONS; ?>' + 'blank.png');

            $('#formEdit #responseText').text('');
            $('#editModal').modal('show');
        }

        function onClickEdit(element, id, title, details, url, iconFileName, showUrl) {
            $('#editModal #title').text('แก้ไขบริการ');
            $('#formEdit #inputIcon').removeAttr('required');

            $('#formEdit #inputId').val(id);
            $('#formEdit #inputTitle').val(title);
            $('#formEdit #inputDetails').val(details);
            $('#formEdit #inputUrl').val(url);
            $('#formEdit #imgPreview').attr('src', '<?= UPLOAD_DIR_SERVICE_ICONS; ?>' + iconFileName);

            /*const divUrl = $('#formEdit #divUrl');
            if (showUrl) {
                $('#formEdit #inputUrl').val(url);
                divUrl.show();
            } else {
                divUrl.hide();
            }*/

            $('#formEdit #responseText').text('');
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

        /*function doUpdateService_old() {
            $('#formEdit #buttonSave').prop('disabled', true);
            $('#formEdit #divLoading').show();
            $.post(
                '../api/api.php/update_service',
                {
                    id: $('#formEdit #inputId').val(),
                    title: $('#formEdit #inputTitle').val().trim(),
                    details: $('#formEdit #inputDetails').val(),
                    url: $('#formEdit #inputUrl').val(),
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
        }*/

        function doUpdateService() {
            $('#formEdit #buttonSave').prop('disabled', true);
            $('#formEdit #divLoading').show();

            $('#formEdit').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    if (data.error_code === 0) {
                        location.reload(true);
                    } else {
                        $('#formEdit #buttonSave').prop('disabled', false);
                        $('#formEdit #divLoading').hide();
                        $('#formEdit #responseText').text(data.error_message);
                    }
                },
                error: () => {
                    $('#formEdit #buttonSave').prop('disabled', false);
                    $('#formEdit #divLoading').hide();
                    $('#formEdit #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                }
            });
        }

        function onChangeStatus(element, serviceId, serviceTitle, userHasPermission) {
            if (!userHasPermission) {
                alert('คุณไม่มีสิทธิ์ในการดำเนินการนี้');
                location.reload(true);
                return;
            }
            
            let result = confirm("ยืนยัน" + (element.checked ? 'เปิดการแสดงผล' : 'ปิดการแสดงผล') + " '" + serviceTitle + "' ?");
            if (result) {
                doChangeStatus(serviceId, (element.checked ? 1 : 0));
            } else {
                /*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*/
                location.reload(true);
            }
        }

        function doChangeStatus(serviceId, newStatus) {
            let title = 'แก้ไขสถานะบริการ';

            $.post(
                '../api/api.php/update_service_status',
                {
                    serviceId: serviceId,
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
    <!--jQuery Form Plugin-->
    <script src="../dist/js/jquery.form.js"></script>
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>