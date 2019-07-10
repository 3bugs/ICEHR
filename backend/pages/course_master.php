<?php
require_once '../include/head_php.inc';

$serviceType = $_GET['service_type'];
if (!isset($serviceType)) {
    echo '<div style="color: red">ERROR: ไม่ได้ระบุ service type</div>';
    $db->close();
    exit();
} else if (!array_key_exists($serviceType, $serviceTypeText)) {
    echo '<div style="color: red">ERROR: ระบุ service type ไม่ถูกต้อง</div>';
    $db->close();
    exit();
}

$sql = "SELECT cm.id, cm.title, cc.id AS category_id, cc.title AS category_title 
        FROM course_master cm 
            LEFT JOIN training_course_category cc 
                ON cm.category = cc.id 
        WHERE service_type = '$serviceType'";
if ($result = $db->query($sql)) {
    $courseMasterList = array();
    while ($row = $result->fetch_assoc()) {
        $courseMaster = array();
        $courseMaster['id'] = (int)$row['id'];
        $courseMaster['title'] = $row['title'];
        $courseMaster['category_id'] = (int)$row['category_id'];
        $courseMaster['category_title'] = $row['category_title'];

        array_push($courseMasterList, $courseMaster);
    }
    $result->close();
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error;
    $db->close();
    exit();
}

$trainingCourseCategoryList = array();
$sql = "SELECT * FROM training_course_category";
if ($result = $db->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $trainingCourseCategory = array();
        $trainingCourseCategory['id'] = (int)$row['id'];
        $trainingCourseCategory['title'] = $row['title'];

        array_push($trainingCourseCategoryList, $trainingCourseCategory);
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

    <!-- Add Course Master Modal -->
    <div class="modal fade" id="addCourseMasterModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">เพิ่มชื่อหลักสูตร</h4>
                </div>
                <div class="modal-body">
                    <form id="formAddCourseMaster" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <input type="hidden"
                               id="inputServiceType"
                               value="<?php echo $serviceType; ?>"/>
                        <div class="box-body">

                            <!--ชื่อคอร์ส-->
                            <div class="form-group">
                                <label for="inputCourseMasterTitle">ชื่อหลักสูตร:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputCourseMasterTitle"
                                           placeholder="กรอกชื่อหลักสูตร" required
                                           oninvalid="this.setCustomValidity('กรอกชื่อหลักสูตร')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <?php
                            if ($serviceType === SERVICE_TYPE_TRAINING) {
                                ?>
                                <!--หมวดหมู่-->
                                <div class="form-group">
                                    <label for="selectCourseCategory">หมวดหมู่:</label>
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-reorder"></i>
                                    </span>
                                        <select id="selectCourseCategory" class="form-control"
                                                required
                                                oninvalid="this.setCustomValidity('เลือกหมวดหมู่หลักสูตร')"
                                                oninput="this.setCustomValidity('')">
                                            <option value="" disabled selected>-- เลือกหมวดหมู่หลักสูตร --</option>
                                            <?php
                                            foreach ($trainingCourseCategoryList as $trainingCourseCategory) {
                                                ?>
                                                <option value="<?php echo $trainingCourseCategory['id']; ?>">
                                                    <?php echo $trainingCourseCategory['title']; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

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

    <!-- Edit Course Master Modal -->
    <div class="modal fade" id="editCourseMasterModal" role="dialog">
        <div class="modal-dialog modal-md">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">แก้ไขชื่อหลักสูตร</h4>
                </div>
                <div class="modal-body">
                    <form id="formEditCourseMaster" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputCourseMasterId">

                            <!--ชื่อคอร์ส-->
                            <div class="form-group">
                                <label for="inputCourseTitle">ชื่อหลักสูตร:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-pencil"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputCourseMasterTitle"
                                           placeholder="กรอกชื่อหลักสูตร" required
                                           oninvalid="this.setCustomValidity('กรอกชื่อหลักสูตร')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>

                            <?php
                            if ($serviceType === SERVICE_TYPE_TRAINING) {
                                ?>
                                <!--หมวดหมู่-->
                                <div class="form-group">
                                    <label for="selectCourseCategory">หมวดหมู่:</label>
                                    <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-reorder"></i>
                                    </span>
                                        <select id="selectCourseCategory" class="form-control"
                                                required
                                                oninvalid="this.setCustomValidity('เลือกหมวดหมู่หลักสูตร')"
                                                oninput="this.setCustomValidity('')">
                                            <option value="" disabled selected>-- เลือกหมวดหมู่หลักสูตร --</option>
                                            <?php
                                            foreach ($trainingCourseCategoryList as $trainingCourseCategory) {
                                                ?>
                                                <option value="<?php echo $trainingCourseCategory['id']; ?>">
                                                    <?php echo $trainingCourseCategory['title']; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>

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
                    ชื่อหลักสูตร
                    <small><?php echo $serviceTypeText[$serviceType]; ?></small>
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
                                        data-toggle="modal" data-target="#addCourseMasterModal">
                                    <span class="fa fa-plus"></span>&nbsp;
                                    เพิ่มชื่อหลักสูตร
                                </button>
                            </div>
                            <div class="box-body">
                                <table id="tableCourseMaster" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <?php
                                        if ($serviceType === SERVICE_TYPE_TRAINING) {
                                            ?>
                                            <th style="text-align: center; width: 70%;">ชื่อหลักสูตร</th>
                                            <th style="text-align: center; width: 30%;">หมวดหมู่</th>
                                            <?php
                                        } else {
                                            ?>
                                            <th style="text-align: center; width: 100%;">ชื่อหลักสูตร</th>
                                            <?php
                                        }
                                        ?>
                                        <th style="text-align: center;" nowrap>จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($courseMasterList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="5" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($courseMasterList as $courseMaster) {
                                            $courseMasterId = $courseMaster['id'];
                                            $courseMasterTitle = $courseMaster['title'];
                                            $courseMasterCategoryId = $courseMaster['category_id'];
                                            $courseMasterCategoryTitle = $courseMaster['category_title'];
                                            ?>
                                            <tr style="">
                                                <td style=""><?php echo $courseMasterTitle; ?></td>
                                                <?php
                                                if ($serviceType === SERVICE_TYPE_TRAINING) {
                                                    ?>
                                                    <td style=""><?php echo $courseMasterCategoryTitle; ?></td>
                                                    <?php
                                                }
                                                ?>
                                                <td style="text-align: center" nowrap>
                                                    <button type="button" class="btn btn-warning"
                                                            style="margin-left: 6px; margin-right: 3px"
                                                            onclick="onClickEdit(this, <?php echo $courseMasterId; ?>, '<?php echo $courseMasterTitle; ?>', <?php echo $courseMasterCategoryId; ?>)">
                                                        <span class="fa fa-edit"></span>&nbsp;
                                                        แก้ไข
                                                    </button>
                                                    <button type="button" class="btn btn-danger"
                                                            style="margin-left: 3px; margin-right: 6px"
                                                            onclick="onClickDelete(this, <?php echo $courseMasterId; ?>, '<?php echo $courseMasterTitle; ?>')">
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
            $('#formAddCourseMaster #divLoading').hide();
            $('#formEditCourseMaster #divLoading').hide();

            $('#tableCourseMaster').DataTable({
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
            $('#formAddCourseMaster').submit(event => {
                event.preventDefault();
                doAddCourseMaster();
            });
            $('#formEditCourseMaster').submit(event => {
                event.preventDefault();
                doUpdateCourseMaster();
            });
        });

        function doAddCourseMaster() {
            $('#formAddCourseMaster #buttonSave').prop('disabled', true);
            $('#formAddCourseMaster #divLoading').show();
            $.post(
                '../api/api.php/add_course_master',
                {
                    courseMasterTitle: $('#formAddCourseMaster #inputCourseMasterTitle').val(),
                    courseCategory: $('#formAddCourseMaster #selectCourseCategory').val(),
                    serviceType: $('#formAddCourseMaster #inputServiceType').val(),
                }
            ).done(function (data) {
                $('#formAddCourseMaster #buttonSave').prop('disabled', false);
                $('#formAddCourseMaster #divLoading').hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formAddCourseMaster #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formAddCourseMaster #buttonSave').prop('disabled', false);
                $('#formAddCourseMaster #divLoading').hide();
                $('#formAddCourseMaster #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        function onClickEdit(element, courseMasterId, courseMasterTitle, courseMasterCategoryId) {
            $('#formEditCourseMaster #inputCourseMasterId').val(courseMasterId);
            $('#formEditCourseMaster #inputCourseMasterTitle').val(courseMasterTitle);
            $(`#formEditCourseMaster #selectCourseCategory option[value=${courseMasterCategoryId}]`).prop('selected', true);
            $('#editCourseMasterModal').modal('show');
        }

        function doUpdateCourseMaster() {
            $('#formEditCourseMaster #buttonSave').prop('disabled', true);
            $('#formEditCourseMaster #divLoading').show();
            $.post(
                '../api/api.php/update_course_master',
                {
                    courseMasterId: $('#formEditCourseMaster #inputCourseMasterId').val(),
                    courseMasterTitle: $('#formEditCourseMaster #inputCourseMasterTitle').val(),
                    courseCategory: $('#formEditCourseMaster #selectCourseCategory').val(),
                }
            ).done(function (data) {
                $('#formEditCourseMaster #buttonSave').prop('disabled', false);
                $('#formEditCourseMaster #divLoading').hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    $('#formEditCourseMaster #responseText').text(data.error_message);
                }
            }).fail(function () {
                $('#formEditCourseMaster #buttonSave').prop('disabled', false);
                $('#formEditCourseMaster #divLoading').hide();
                $('#formEditCourseMaster #responseText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
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