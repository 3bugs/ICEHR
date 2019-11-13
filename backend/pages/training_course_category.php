<?php
require_once '../include/head_php.inc';

$sql = "SELECT * FROM training_course_category ORDER BY id";
if ($result = $db->query($sql)) {
    $categoryList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($categoryList, $row);
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

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">เพิ่มชื่อหมวดหมู่</h4>
                </div>
                <div class="modal-body">
                    <form id="formAddCategory" role="form"
                          style="margin-top: 0; margin-bottom: 0">

                        <div class="box-body">
                            <!--ชื่อหมวดหมู่-->
                            <div class="form-group">
                                <label for="inputCategoryTitle">ชื่อหมวดหมู่:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputCategoryTitle"
                                           placeholder="กรอกชื่อหมวดหมู่" required
                                           oninvalid="this.setCustomValidity('กรอกชื่อหมวดหมู่')"
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

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">แก้ไขชื่อหมวดหมู่</h4>
                </div>
                <div class="modal-body">
                    <form id="formEditCategory" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputCategoryId">

                            <!--ชื่อหมวดหมู่-->
                            <div class="form-group">
                                <label for="inputCategoryTitle">ชื่อหมวดหมู่:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputCategoryTitle"
                                           placeholder="กรอกชื่อหมวดหมู่" required
                                           oninvalid="this.setCustomValidity('กรอกชื่อหมวดหมู่')"
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
                    ชื่อหมวดหมู่หลักสูตร
                    <small>บริการวิชาการ</small>
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
                                        data-toggle="modal" data-target="#addCategoryModal"
                                        style="margin-right: 5px">
                                    <span class="fa fa-plus"></span>&nbsp;
                                    เพิ่มชื่อหมวดหมู่
                                </button>
                            </div>
                            <div class="box-body">
                                <table id="tableCategory" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="text-align: center; width: 100%;">ชื่อหมวดหมู่</th>
                                        <th style="text-align: center;" nowrap>จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($categoryList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="5" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($categoryList as $category) {
                                            $categoryId = $category['id'];
                                            $categoryTitle = $category['title'];
                                            ?>
                                            <tr style="">
                                                <td style=""><?= $categoryTitle; ?></td>
                                                <td style="text-align: center" nowrap>
                                                    <button type="button" class="btn btn-warning"
                                                            style="margin-left: 6px; margin-right: 3px"
                                                            onclick="onClickEdit(this, <?= $categoryId; ?>, '<?= $categoryTitle; ?>')">
                                                        <span class="fa fa-edit"></span>&nbsp;
                                                        แก้ไข
                                                    </button>
                                                    <button type="button" class="btn btn-danger"
                                                            style="margin-left: 3px; margin-right: 6px"
                                                            onclick="onClickDelete(this, <?= $categoryId; ?>, '<?= $categoryTitle; ?>')">
                                                        <span class="fa fa-remove"></span>&nbsp;
                                                        ลบ
                                                    </button>
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
            $('#formAddCategory #divLoading').hide();
            $('#formEditCategory #divLoading').hide();

            $('#tableCategory').DataTable({
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
            $('#formAddCategory').submit(event => {
                event.preventDefault();
                doAddCategory();
            });
            $('#formEditCategory').submit(event => {
                event.preventDefault();
                doUpdateCategory();
            });
        });

        function doAddCategory() {
            $('#formAddCategory #responseText').text('');
            $('#formAddCategory #buttonSave').prop('disabled', true);
            $('#formAddCategory #divLoading').show();
            $.post(
                '../api/api.php/add_training_course_category',
                {
                    categoryTitle: $('#formAddCategory #inputCategoryTitle').val()
                }
            ).done(function (data) {
                $('#formAddCategory #buttonSave').prop('disabled', false);
                $('#formAddCategory #divLoading').hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formAddCategory #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formAddCategory #buttonSave').prop('disabled', false);
                $('#formAddCategory #divLoading').hide();
                $('#formAddCategory #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickEdit(element, categoryId, categoryTitle) {
            $('#formEditCategory #inputCategoryId').val(categoryId);
            $('#formEditCategory #inputCategoryTitle').val(categoryTitle);
            $('#editCategoryModal').modal('show');
        }

        function doUpdateCategory() {
            $('#formEditCategory #responseText').text('');
            $('#formEditCategory #buttonSave').prop('disabled', true);
            $('#formEditCategory #divLoading').show();
            $.post(
                '../api/api.php/update_training_course_category',
                {
                    categoryId: $('#formEditCategory #inputCategoryId').val(),
                    categoryTitle: $('#formEditCategory #inputCategoryTitle').val()
                }
            ).done(function (data) {
                $('#formEditCategory #buttonSave').prop('disabled', false);
                $('#formEditCategory #divLoading').hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formEditCategory #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formEditCategory #buttonSave').prop('disabled', false);
                $('#formEditCategory #divLoading').hide();
                $('#formEditCategory #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickDelete(element, categoryId, categoryTitle) {
            BootstrapDialog.show({
                title: 'ลบชื่อหมวดหมู่',
                message: 'ยืนยันลบชื่อหมวดหมู่ \'' + categoryTitle + '\' ?',
                buttons: [{
                    label: 'ลบ',
                    action: function (self) {
                        doDeleteCategory(categoryId);
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

        function doDeleteCategory(categoryId) {
            $.post(
                '../api/api.php/delete_training_course_category',
                {
                    categoryId: categoryId,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบชื่อหมวดหมู่ - ผิดพลาด',
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
                    title: 'ลบชื่อหมวดหมู่ - ผิดพลาด',
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