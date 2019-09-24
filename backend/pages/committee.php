<?php
require_once '../include/head_php.inc';

$sql = "SELECT * FROM committee ORDER BY sort_index";
if ($result = $db->query($sql)) {
    $committeeList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($committeeList, $row);
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
        <!--Lightbox-->
        <link href="../dist/lightbox/css/lightbox.css" rel="stylesheet">
        <!--jQuery UI (ใช้ sortable)-->
        <link rel="stylesheet" href="../bower_components/jquery-ui/themes/base/jquery-ui.css">

        <style>
            #sortableItems {
                list-style-type: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            #sortableItems li {
                cursor: pointer;
                margin: 0 3px 3px 3px;
                padding: 0.4em;
                padding-left: 1.5em;
                font-size: 1.0em;
            }

            #sortableItems li span {
                position: absolute;
                margin-left: -1.3em;
            }
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title" id="title">เพิ่ม/แก้ไขคณะกรรมการ</h4>
                </div>
                <div class="modal-body">
                    <form id="formEdit" role="form" method="post"
                          action="../api/api.php/add_update_committee"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputId" name="id">

                            <!--คำนำหน้า-ชื่อ-นามสกุล-->
                            <div class="row">
                                <!--คำนำหน้าชื่อ-->
                                <div class="form-group col-md-2">
                                    <label for="inputTitle">คำนำหน้าชื่อ:</label>
                                    <div class="input-group">
                                        <!--<span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>-->
                                        <input type="text" class="form-control"
                                               id="inputTitle" name="title"
                                               placeholder="กรอกคำนำหน้าชื่อ" required
                                               oninvalid="this.setCustomValidity('กรอกคำนำหน้าชื่อ')"
                                               oninput="this.setCustomValidity('')">
                                    </div>
                                </div>

                                <!--ชื่อ-->
                                <div class="form-group col-md-5">
                                    <label for="inputFirstName">ชื่อ:</label>
                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user-o"></i>
                                                    </span>
                                        <input type="text" class="form-control"
                                               id="inputFirstName" name="firstName"
                                               placeholder="กรอกชื่อ" required
                                               oninvalid="this.setCustomValidity('กรอกชื่อ')"
                                               oninput="this.setCustomValidity('')">
                                    </div>
                                </div>
                                <!--นามสกุล-->
                                <div class="form-group col-md-5">
                                    <label for="inputLastName">นามสกุล:</label>
                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-user-o"></i>
                                                            </span>
                                        <input type="text" class="form-control"
                                               id="inputLastName" name="lastName"
                                               placeholder="กรอกนามสกุล" required
                                               oninvalid="this.setCustomValidity('กรอกนามสกุล')"
                                               oninput="this.setCustomValidity('')">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputPosition">ตำแหน่ง:</label>
                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-tag"></i>
                                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputPosition" name="position"
                                           placeholder="กรอกตำแหน่ง" required
                                           oninvalid="this.setCustomValidity('กรอกตำแหน่ง')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <!--รูปภาพ-->
                            <div class="form-group">
                                <label for="inputImage">รูปภาพ:</label>
                                <div class="input-group">
                                    <span class="input-group-addon" style="padding: 0px; border: 1px solid lightgrey">
                                        <img id="imgPreview" width="100px" height="100px">
                                    </span>
                                    <input id="inputImage" name="image"
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

    <!-- Sort Modal -->
    <div class="modal fade" id="sortItemModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">เรียงลำดับการแสดงในหน้าเว็บ</h4>
                </div>
                <div class="modal-body">
                    <form id="formSortItem" role="form"
                          style="margin-top: 0; margin-bottom: 0">

                        <div class="box-body">
                            <ul id="sortableItems">
                                <?php
                                foreach ($committeeList as $committee) {
                                    //if ($committee['status'] === 'publish') {
                                        ?>
                                        <li class="ui-state-default">
                                            <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                            <?= "{$committee['title']} {$committee['first_name']} {$committee['last_name']}"; ?>
                                            <input type="hidden" value="<?= $committee['id']; ?>"/>
                                        </li>
                                        <?php
                                    //}
                                }
                                ?>
                            </ul>

                            <div id="divLoading" style="text-align: center; margin-top: 25px">
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
                    คณะกรรมการ
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                                <button type="button" class="btn btn-info pull-right"
                                        data-toggle="modal" data-target="#sortItemModal">
                                    <span class="fa fa-sort"></span>&nbsp;
                                    เรียงลำดับ
                                </button>
                                <button type="button" class="btn btn-success pull-right"
                                        onclick="onClickAdd(this)"
                                        style="margin-right: 5px"
                                        data-toggle_x="modal" data-target_x="#addCourseMasterModal">
                                    <span class="fa fa-plus"></span>&nbsp;
                                    เพิ่มคณะกรรมการ
                                </button>
                            </div>
                            <div class="box-body">
                                <table id="tableCommittee" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 10%;">ลำดับ</th>
                                        <th style="text-align: center;">รูปภาพ</th>
                                        <th style="text-align: center; width: 50%;">ชื่อ-นามสกุล</th>
                                        <th style="text-align: center; width: 50%;">ตำแหน่ง</th>
                                        <!--<th style="text-align: center">สถานะ</th>-->
                                        <th style="text-align: center;" nowrap>จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($committeeList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="5" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($committeeList as $committee) {
                                            $id = $committee['id'];

                                            $hiddenName = "{$committee['first_name']} {$committee['last_name']}"; // for sorting
                                            $displayName = sprintf(
                                                '%s<br>%s %s',
                                                $committee['title'], $committee['first_name'], $committee['last_name']
                                            );

                                            $position = $committee['position'];

                                            $image = sprintf(
                                                '<a href="%s" data-lightbox="coverImage" data-title="%s"><img src="%s" width="60px" style="border-radius: 50%%;"/></a>',
                                                UPLOAD_DIR_USER_ASSETS . $committee['image_file_name'],
                                                "{$committee['title']} {$committee['first_name']} {$committee['last_name']}",
                                                UPLOAD_DIR_USER_ASSETS . $committee['image_file_name']
                                            );
                                            ?>
                                            <tr style="">
                                                <td style="text-align: center"><?= $committee['sort_index'] != null ? $committee['sort_index'] : '<span style="display: none">999999</span>'; ?></td>
                                                <td style=""><?= $image; ?></td>
                                                <td style="">
                                                    <span style="display: none"><?= $hiddenName; ?></span>
                                                    <?= $displayName; ?>
                                                </td>
                                                <td style=""><?= $position; ?></td>

                                                <td style="text-align: right" nowrap>
                                                    <?php
                                                    if (currentUserHasPermission(PERMISSION_MANAGE_WEB_CONTENT)) {
                                                        ?>
                                                        <button type="button" class="btn btn-warning"
                                                                style="margin-left: 6px; margin-right: 3px"
                                                                onclick="onClickEdit(
                                                                        this,
                                                                <?= $id; ?>,
                                                                        '<?= $committee['title']; ?>',
                                                                        '<?= $committee['first_name']; ?>',
                                                                        '<?= $committee['last_name']; ?>',
                                                                        '<?= $position; ?>',
                                                                        '<?= $committee['image_file_name']; ?>'
                                                                        )">
                                                            <span class="fa fa-edit"></span>&nbsp;
                                                            แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                                style="margin-left: 3px; margin-right: 6px"
                                                                onclick="onClickDelete(
                                                                        this,
                                                                <?= $id; ?>,
                                                                        '<?= $displayName; ?>',
                                                                        '<?= $position; ?>'
                                                                        )">
                                                            <span class="fa fa-edit"></span>&nbsp;
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
        let sortableItems = $('#sortableItems');

        $(document).ready(function () {
            $('#formEdit #divLoading').hide();

            $('#tableCommittee').DataTable({
                ordering: true,
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

            $('#formEdit').submit(event => {
                event.preventDefault();
                if (validateForm()) {
                    doAddUpdateCommittee();
                }
            });

            $('#formSortItem').submit(event => {
                event.preventDefault();
                sortItems();
            });

            $('#formSortItem #divLoading').hide();
            sortableItems.sortable();
            sortableItems.disableSelection();
        });

        function sortItems() {
            let sortValue = '';
            sortableItems.children('li').each((index, listItem) => {
                let itemId = $(listItem).find('input').val();
                //msg += $(listItem).text().trim() + ' [' + departmentId + '-' + (index + 1) + '],';
                sortValue += itemId + '-' + (index + 1) + ',';
            });
            doSortItems(sortValue.slice(0, -1));
            //alert(sortValue);
        }

        function doSortItems(sortValue) {
            $('#formSortItem #responseText').text('');
            $('#formSortItem #buttonSave').prop('disabled', true);
            $('#formSortItem #divLoading').show();
            $.post(
                '../api/api.php/sort_committee',
                {
                    sortValue: sortValue
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formSortItem #buttonSave').prop('disabled', false);
                    $('#formSortItem #divLoading').hide();
                    $('#formSortItem #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formSortItem #buttonSave').prop('disabled', false);
                $('#formSortItem #divLoading').hide();
                $('#formSortItem #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        $(function () {
            const setIconPreview = function (input, placeToInsertImagePreview) {
                //$(placeToInsertImagePreview).empty();
                //$(placeToInsertImagePreview).hide();

                $('#formEdit #imgPreview').attr('src', '<?= UPLOAD_DIR_USER_ASSETS; ?>' + 'blank.png');

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

            $('#inputImage').on('change', function () {
                setIconPreview(this, 'div#cover-image-upload-preview');
            });
        });

        function onClickAdd(element) {
            $('#editModal #title').text('เพิ่มคณะกรรมการ');
            $('#formEdit #inputImage').attr('required', 'true');

            $('#formEdit #inputId').val('0');
            $('#formEdit #inputTitle').val('');
            $('#formEdit #inputFirstName').val('');
            $('#formEdit #inputLastName').val('');
            $('#formEdit #inputPosition').val('');
            $('#formEdit #imgPreview').attr('src', '<?= UPLOAD_DIR_USER_ASSETS; ?>' + 'blank.png');

            $('#formEdit #responseText').text('');
            $('#editModal').modal('show');
        }

        function onClickEdit(element, id, title, firstName, lastName, position, imageFileName) {
            $('#editModal #title').text('แก้ไขคณะกรรมการ');
            $('#formEdit #inputImage').removeAttr('required');

            $('#formEdit #inputId').val(id);
            $('#formEdit #inputTitle').val(title);
            $('#formEdit #inputFirstName').val(firstName);
            $('#formEdit #inputLastName').val(lastName);
            $('#formEdit #inputPosition').val(position);
            $('#formEdit #imgPreview').attr('src', '<?= UPLOAD_DIR_USER_ASSETS; ?>' + imageFileName);

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

        function doAddUpdateCommittee() {
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

        /*function onChangeStatus(element, userHasPermission, serviceId, serviceTitle) {
            if (userHasPermission) {
                let result = confirm("ยืนยัน" + (element.checked ? 'เปิดการแสดงผล' : 'ปิดการแสดงผล') + " '" + serviceTitle + "' ?");
                if (result) {
                    doChangeStatus(serviceId, (element.checked ? 1 : 0));
                } else {
                    /!*รีโหลด เพื่อให้สถานะ checkbox กลับมาเหมือนเดิม*!/
                    location.reload(true);
                }
            } else {
                alert("คุณไม่มีสิทธิ์แก้ไขสถานะของบริการ\n\n'" + serviceTitle + "'");
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
        }*/

        function onClickDelete(element, id, displayName, position) {
            BootstrapDialog.show({
                title: 'ลบคณะกรรมการ',
                message: 'ยืนยันลบ \'' + displayName + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteCommittee(id);
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

        function doDeleteCommittee(id) {
            $.post(
                '../api/api.php/delete_committee',
                {
                    id: id,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบคณะกรรมการ - ผิดพลาด',
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
                    title: 'ลบคณะกรรมการ - ผิดพลาด',
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
    <!--jQuery Form Plugin-->
    <script src="../dist/js/jquery.form.js"></script>
    <!--Lightbox-->
    <script src="../dist/lightbox/js/lightbox.js"></script>
    <!--jQuery UI (ใช้ sortable)-->
    <script src="../bower_components/jquery-ui/jquery-ui.js"></script>
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>