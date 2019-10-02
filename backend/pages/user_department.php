<?php
require_once '../include/head_php.inc';

$sql = "SELECT * FROM user_department ORDER BY sort_index";
if ($result = $db->query($sql)) {
    $departmentList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($departmentList, $row);
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
        <!--jQuery UI (ใช้ sortable)-->
        <link rel="stylesheet" href="../bower_components/jquery-ui/themes/base/jquery-ui.css">
        <style>
            #sortableDepartments, #sortableUsers {
                list-style-type: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            #sortableDepartments li, #sortableUsers li {
                cursor: pointer;
                margin: 0 3px 3px 3px;
                padding: 0.4em;
                padding-left: 1.5em;
                font-size: 1.0em;
            }

            #sortableDepartments li span, #sortableUsers li span {
                position: absolute;
                margin-left: -1.3em;
            }
        </style>
    </head>
    <body class="hold-transition skin-blue sidebar-mini">

    <!-- Sort Department Modal -->
    <div class="modal fade" id="sortDepartmentModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">เรียงลำดับฝ่าย</h4>
                </div>
                <div class="modal-body">
                    <form id="formSortDepartment" role="form"
                          style="margin-top: 0; margin-bottom: 0">

                        <div class="box-body">
                            <ul id="sortableDepartments">
                                <?php
                                foreach ($departmentList as $department) {
                                    ?>
                                    <li class="ui-state-default">
                                        <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                        <?= $department['name']; ?>
                                        <input type="hidden" value="<?= $department['id']; ?>"/>
                                    </li>
                                    <?php
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

    <!-- Sort User Modal -->
    <div class="modal fade" id="sortUserModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">เรียงลำดับผู้ใช้งานในฝ่าย '<span id="spanDepartmentName"></span>'</h4>
                </div>
                <div class="modal-body">
                    <form id="formSortUser" role="form"
                          style="margin-top: 0; margin-bottom: 0">

                        <div class="box-body">
                            <ul id="sortableUsers">
                                <?php
                                /*                                foreach ($departmentList as $department) {
                                                                    */ ?><!--
                                    <li class="ui-state-default">
                                        <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                        <? /*= $department['name']; */ ?>
                                        <input type="hidden" value="<? /*= $department['id']; */ ?>"/>
                                    </li>
                                    --><?php
                                /*                                }
                                                                */ ?>
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

    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">เพิ่มชื่อฝ่าย</h4>
                </div>
                <div class="modal-body">
                    <form id="formAddDepartment" role="form"
                          style="margin-top: 0; margin-bottom: 0">

                        <div class="box-body">
                            <!--ชื่อฝ่าย-->
                            <div class="form-group">
                                <label for="inputDepartmentName">ชื่อฝ่าย:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputDepartmentName"
                                           placeholder="กรอกชื่อฝ่าย" required
                                           oninvalid="this.setCustomValidity('กรอกชื่อฝ่าย')"
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

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">แก้ไขชื่อฝ่าย</h4>
                </div>
                <div class="modal-body">
                    <form id="formEditDepartment" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputDepartmentId">

                            <!--ชื่อฝ่าย-->
                            <div class="form-group">
                                <label for="inputDepartmentName">ชื่อฝ่าย:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputDepartmentName"
                                           placeholder="กรอกชื่อฝ่าย" required
                                           oninvalid="this.setCustomValidity('กรอกชื่อฝ่าย')"
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
                    ชื่อฝ่าย
                    <small>ผู้ใช้งานระบบ</small>
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
                                if (currentUserHasPermission(PERMISSION_USER_DEPARTMENT_MANAGE)) {
                                    ?>
                                    <button type="button" class="btn btn-info pull-right"
                                            data-toggle="modal" data-target="#sortDepartmentModal">
                                        <span class="fa fa-sort"></span>&nbsp;
                                        เรียงลำดับฝ่าย
                                    </button>
                                    <button type="button" class="btn btn-success pull-right"
                                            data-toggle="modal" data-target="#addDepartmentModal"
                                            style="margin-right: 5px">
                                        <span class="fa fa-plus"></span>&nbsp;
                                        เพิ่มชื่อฝ่าย
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="box-body">
                                <table id="tableDepartment" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 100%;">ชื่อฝ่าย</th>
                                        <th style="text-align: center;" nowrap>ลำดับการแสดงในหน้าเว็บ</th>
                                        <th style="text-align: center;" nowrap>จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($departmentList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="5" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($departmentList as $department) {
                                            $departmentId = $department['id'];
                                            $departmentName = $department['name'];
                                            $sortIndex = $department['sort_index'];
                                            ?>
                                            <tr style="">
                                                <td style=""><?= $departmentName; ?></td>
                                                <td style="text-align: center"><?= $sortIndex; ?></td>
                                                <td style="text-align: center" nowrap>
                                                    <?php
                                                    if (currentUserHasPermission(PERMISSION_USER_DEPARTMENT_MANAGE)) {
                                                        ?>
                                                        <button type="button" class="btn btn-info"
                                                                style="margin-left: 6px; margin-right: 3px"
                                                                onclick="onClickSortUsers(this, <?= $departmentId; ?>, '<?= $departmentName; ?>')">
                                                            <span class="fa fa-sort"></span>&nbsp;
                                                            เรียงลำดับผู้ใช้
                                                        </button>
                                                        <button type="button" class="btn btn-warning"
                                                                style="margin-left: 6px; margin-right: 3px"
                                                                onclick="onClickEdit(this, <?= $departmentId; ?>, '<?= $departmentName; ?>')">
                                                            <span class="fa fa-edit"></span>&nbsp;
                                                            แก้ไข
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                                style="margin-left: 3px; margin-right: 6px"
                                                                onclick="onClickDelete(this, <?= $departmentId; ?>, '<?= $departmentName; ?>')">
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
        const sortableDepartments = $('#sortableDepartments');
        const sortableUsers = $('#sortableUsers');

        $(document).ready(function () {
            $('#formSortDepartment #divLoading').hide();
            $('#formAddDepartment #divLoading').hide();
            $('#formEditDepartment #divLoading').hide();

            $('#tableDepartment').DataTable({
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
            $('#formSortDepartment').submit(event => {
                event.preventDefault();
                sortDepartments();
            });
            $('#formSortUser').submit(event => {
                event.preventDefault();
                sortUsers();
            });
            $('#formAddDepartment').submit(event => {
                event.preventDefault();
                doAddDepartment();
            });
            $('#formEditDepartment').submit(event => {
                event.preventDefault();
                doUpdateDepartment();
            });

            sortableDepartments.sortable();
            sortableDepartments.disableSelection();
        });

        function sortDepartments() {
            let sortValue = '';
            sortableDepartments.children('li').each((index, listItem) => {
                let departmentId = $(listItem).find('input').val();
                //msg += $(listItem).text().trim() + ' [' + departmentId + '-' + (index + 1) + '],';
                sortValue += departmentId + '-' + (index + 1) + ',';
            });
            doSortDepartments(sortValue.slice(0, -1));
            //alert(sortValue);
        }

        function doSortDepartments(sortValue) {
            $('#formSortDepartment #responseText').text('');
            $('#formSortDepartment #buttonSave').prop('disabled', true);
            $('#formSortDepartment #divLoading').show();
            $.post(
                '../api/api.php/sort_user_department',
                {
                    sortValue: sortValue
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formSortDepartment #buttonSave').prop('disabled', false);
                    $('#formSortDepartment #divLoading').hide();
                    $('#formSortDepartment #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formSortDepartment #buttonSave').prop('disabled', false);
                $('#formSortDepartment #divLoading').hide();
                $('#formSortDepartment #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function sortUsers() {
            let sortValue = '';
            sortableUsers.children('li').each((index, listItem) => {
                let userId = $(listItem).find('input').val();
                sortValue += userId + '-' + (index + 1) + ',';
            });
            doSortUsers(sortValue.slice(0, -1));
            //alert(sortValue);
        }

        function doSortUsers(sortValue) {
            $('#formSortUser #responseText').text('');
            $('#formSortUser #buttonSave').prop('disabled', true);
            $('#formSortUser #divLoading').show();
            $.post(
                '../api/api.php/sort_user',
                {
                    sortValue: sortValue
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formSortUser #buttonSave').prop('disabled', false);
                    $('#formSortUser #divLoading').hide();
                    $('#formSortUser #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formSortUser #buttonSave').prop('disabled', false);
                $('#formSortUser #divLoading').hide();
                $('#formSortUser #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function doAddDepartment() {
            $('#formAddDepartment #responseText').text('');
            $('#formAddDepartment #buttonSave').prop('disabled', true);
            $('#formAddDepartment #divLoading').show();
            $.post(
                '../api/api.php/add_user_department',
                {
                    departmentName: $('#formAddDepartment #inputDepartmentName').val()
                }
            ).done(function (data) {
                $('#formAddDepartment #buttonSave').prop('disabled', false);
                $('#formAddDepartment #divLoading').hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formAddDepartment #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formAddDepartment #buttonSave').prop('disabled', false);
                $('#formAddDepartment #divLoading').hide();
                $('#formAddDepartment #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickSortUsers(element, departmentId, departmentName) {
            $('#sortUserModal #spanDepartmentName').text(departmentName);
            $('#sortUserModal').modal('show');

            doGetUsersByDepartment(departmentId);
        }

        function doGetUsersByDepartment(departmentId) {
            $('#formSortUser #responseText').text('');
            $('#formSortUser #buttonSave').prop('disabled', true);
            $('#formSortUser #divLoading').show();
            const sortableUsers = $('#formSortUser #sortableUsers');
            sortableUsers.empty();

            $.post(
                '../api/api.php/get_user_by_department',
                {
                    departmentId: departmentId
                }
            ).done(function (data) {
                $('#formSortUser #divLoading').hide();
                if (data.error_code === 0) {
                    if (data.data_list.length > 0) {
                        $('#formSortUser #buttonSave').prop('disabled', false);

                        //แสดง sortable ใน Modal
                        data.data_list.forEach((user, index) => {
                            sortableUsers.append(
                                `<li class="ui-state-default">
                                    <span class="ui-icon ui-icon-arrowthick-2-n-s" style="margin-top: 15px"></span>
                                    <img src="<?= UPLOAD_DIR_USER_ASSETS; ?>/${user.image_file_name}" width="50px" style="border-radius: 50%;"/>&nbsp;
                                    ${user.title} ${user.first_name} ${user.last_name},&nbsp;&nbsp;${user.position},&nbsp;&nbsp;<i class="fa fa-user" style="color: #a0a0a0"></i> ${user.username}
                                    <input type="hidden" value="${user.id}"/>
                                </li>`
                            );
                        });
                        sortableUsers.sortable();
                        sortableUsers.disableSelection();
                    } else {
                        $('#formSortUser #responseText').text('ไม่มีข้อมูลผู้ใช้ในฝ่ายนี้');
                    }
                } else {
                    $('#formSortUser #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formSortUser #divLoading').hide();
                $('#formSortUser #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickEdit(element, departmentId, departmentName) {
            $('#formEditDepartment #inputDepartmentId').val(departmentId);
            $('#formEditDepartment #inputDepartmentName').val(departmentName);
            $('#editDepartmentModal').modal('show');
        }

        function doUpdateDepartment() {
            $('#formEditDepartment #responseText').text('');
            $('#formEditDepartment #buttonSave').prop('disabled', true);
            $('#formEditDepartment #divLoading').show();
            $.post(
                '../api/api.php/update_user_department',
                {
                    departmentId: $('#formEditDepartment #inputDepartmentId').val(),
                    departmentName: $('#formEditDepartment #inputDepartmentName').val()
                }
            ).done(function (data) {
                $('#formEditDepartment #buttonSave').prop('disabled', false);
                $('#formEditDepartment #divLoading').hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formEditDepartment #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formEditDepartment #buttonSave').prop('disabled', false);
                $('#formEditDepartment #divLoading').hide();
                $('#formEditDepartment #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickDelete(element, departmentId, departmentName) {
            BootstrapDialog.show({
                title: 'ลบชื่อฝ่าย',
                message: 'ยืนยันลบชื่อฝ่าย \'' + departmentName + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteDepartment(departmentId);
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

        function doDeleteDepartment(departmentId) {
            $.post(
                '../api/api.php/delete_user_department',
                {
                    departmentId: departmentId,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบชื่อฝ่าย - ผิดพลาด',
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
                    title: 'ลบชื่อฝ่าย - ผิดพลาด',
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
    <!--jQuery UI (ใช้ sortable)-->
    <script src="../bower_components/jquery-ui/jquery-ui.js"></script>

    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>