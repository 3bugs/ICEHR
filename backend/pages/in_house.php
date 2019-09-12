<?php
require_once '../include/head_php.inc';

$sql = "SELECT * FROM in_house ORDER BY created_at DESC";
if ($result = $db->query($sql)) {
    $inHouseList = array();
    while ($row = $result->fetch_assoc()) {
        $inHouse = array();
        $inHouse['id'] = (int)$row['id'];
        $inHouse['title'] = $row['title'];
        $inHouse['first_name'] = $row['first_name'];
        $inHouse['last_name'] = $row['last_name'];
        $inHouse['organization_name'] = $row['organization_name'];
        $inHouse['phone'] = $row['phone'];
        $inHouse['email'] = $row['email'];
        $inHouse['course'] = $row['course'];
        $inHouse['num_day'] = $row['num_day'];
        $inHouse['num_trainee'] = $row['num_trainee'];
        $inHouse['place'] = $row['place'];
        $inHouse['remark'] = $row['remark'];
        $inHouse['status'] = $row['status'];
        $inHouse['created_at'] = $row['created_at'];

        array_push($inHouseList, $inHouse);
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

    <!-- Edit modal -->
    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal">&times;
                    </button>-->
                    <h4 class="modal-title">
                        ข้อมูลผู้ติดต่อ
                    </h4>
                </div>
                <div class="modal-body">

                    <div id="spanLoading" style="text-align: center">
                        <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                    </div>
                    <div id="alertSuccess" class="alert alert-success alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertSuccess').hide()">&times;</button>
                        <i class="icon fa fa-check"></i><span id="alertSuccessText"></span>
                    </div>
                    <div id="alertError" class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertError').hide()">&times;</button>
                        <i class="icon fa fa-warning"></i><span id="alertErrorText"></span>
                    </div>

                    <form id="formEdit" role="form"
                          action="../api/api.php/update_in_house"
                          method="post"
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
                                               id="inputTitle" name="title">
                                    </div>
                                </div>

                                <!--ชื่อ-->
                                <div class="form-group col-md-5">
                                    <label for="inputFirstName">ชื่อ:</label>
                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user"></i>
                                                    </span>
                                        <input type="text" class="form-control"
                                               id="inputFirstName" name="firstName">
                                    </div>
                                </div>
                                <!--นามสกุล-->
                                <div class="form-group col-md-5">
                                    <label for="inputLastName">นามสกุล:</label>
                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-user"></i>
                                                            </span>
                                        <input type="text" class="form-control"
                                               id="inputLastName" name="lastName">
                                    </div>
                                </div>
                            </div>

                            <!--เบอร์โทร อีเมล-->
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="inputPhone">เบอร์โทร:</label>
                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-phone"></i>
                                                    </span>
                                        <input type="text" class="form-control"
                                               id="inputPhone" name="phone">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputEmail">อีเมล:</label>
                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope-o"></i>
                                                    </span>
                                        <input type="text" class="form-control"
                                               id="inputEmail" name="email">
                                    </div>
                                </div>
                            </div>

                            <!--ชื่อหน่วยงาน-->
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="inputOrganizationName">ชื่อหน่วยงาน:</label>
                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-font"></i>
                                                    </span>
                                        <input type="text" class="form-control"
                                               id="inputOrganizationName" name="organizationName">
                                    </div>
                                </div>
                            </div>

                            <label for="inputMoo" style="display: block; text-align: center;">การอบรมที่ต้องการ</label>
                            <div style="padding: 15px 15px 5px 15px; border: 0px solid grey; background: #f4f4f4">
                                <!--หลักสูตรที่ต้องการ-->
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="inputCourse">หลักสูตร:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-files-o"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputCourse" name="course">
                                        </div>
                                    </div>
                                </div>

                                <!--จำนวนวัน จำนวนผู้เข้าอบรม-->
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="inputNumDay">จำนวนวัน:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar-check-o"></i>
                                                    </span>
                                            <input type="number" class="form-control"
                                                   id="inputNumDay" name="numDay">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputNumTrainee">จำนวนผู้เข้าอบรม:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-users"></i>
                                                    </span>
                                            <input type="number" class="form-control"
                                                   id="inputNumTrainee" name="numTrainee">
                                        </div>
                                    </div>
                                </div>

                                <!--สถานที่-->
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="inputPlace">สถานที่:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-home"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputPlace" name="place">
                                        </div>
                                    </div>
                                </div>

                                <!--อื่นๆ-->
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="inputRemark">อื่นๆ:</label>
                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-ellipsis-h"></i>
                                                            </span>
                                            <input type="text" class="form-control"
                                                   id="inputRemark" name="remark">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!--/การอบรม-->

                            <div id="spanLoading" style="text-align: center; margin-top: 15px">
                                <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                            </div>
                            <div id="alertSuccess" class="alert alert-success alert-dismissible" style="margin-top: 15px">
                                <button type="button" class="close" aria-hidden="true" onClick="$('#alertSuccess').hide()">&times;</button>
                                <i class="icon fa fa-check"></i><span id="alertSuccessText"></span>
                            </div>
                            <div id="alertError" class="alert alert-danger alert-dismissible" style="margin-top: 15px">
                                <button type="button" class="close" aria-hidden="true" onClick="$('#alertError').hide()">&times;</button>
                                <i class="icon fa fa-warning"></i><span id="alertErrorText"></span>
                            </div>

                            <div style="margin-top: 15px; text-align: center">
                                <?php
                                if (currentUserHasPermission(PERMISSION_MANAGE_IN_HOUSE)) {
                                    ?>
                                    <button type="submit" class="btn btn-block btn-primary" style="display: inline; width: 150px;">
                                        <i class="fa fa-save"></i>&nbsp;&nbsp;บันทึกการแก้ไข
                                    </button>
                                    <?php
                                } else {
                                    ?>
                                    <button type="button" class="btn btn-block btn-danger" style="display: inline; width: 240px;">
                                        <i class="fa fa-save"></i>&nbsp;&nbsp;คุณไม่มีสิทธิ์บันทึกการแก้ไข
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <!-- /.box-body -->

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                </div>
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
                    In-House Training
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <!--ตารางรายชื่อผู้ติดต่อ-->
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                            </div>
                            <div class="box-body">
                                <table id="tableUser" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 20%; text-align: center">ผู้ติดต่อ</th>
                                        <th style="width: 15%; text-align: center">หน่วยงาน</th>
                                        <th style="width: 20%; text-align: center">หลักสูตรที่ต้องการ</th>
                                        <th style="width: 15%; text-align: center">สถานที่</th>
                                        <th style="width: 15%; text-align: center">อื่นๆ</th>
                                        <th style="width: 15%; text-align: center">วัน/เวลาที่ส่งข้อมูล</th>
                                        <th style="text-align: center">สถานะ</th>
                                        <th style="text-align: center">จัดการ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($inHouseList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($inHouseList as $inHouse) {
                                            $contactPerson = sprintf(
                                                '%s %s %s<br><i class="fa fa-phone" style="color: black"></i> %s<br><i class="fa fa-envelope-o" style="color: black"></i> <a href="mailto:%s">%s</a>',
                                                $inHouse['title'], $inHouse['first_name'], $inHouse['last_name'],
                                                $inHouse['phone'], $inHouse['email'], $inHouse['email']
                                            );

                                            $course = sprintf(
                                                '%s<br><i class="fa fa-calendar-check-o" style="color: black"></i> %d วัน<br><i class="fa fa-users" style="color: black"></i> %d ผู้เข้าอบรม',
                                                $inHouse['course'], $inHouse['num_day'], $inHouse['num_trainee']
                                            );

                                            $createdAt = $inHouse['created_at'];
                                            $dateTimePart = explode(' ', $createdAt);
                                            $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                                            $timePart = explode(':', $dateTimePart[1]);
                                            $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                                            $displayDateTime = "$displayDate<br>$displayTime";
                                            $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';

                                            $btnClass = '';
                                            $btnText = '';
                                            switch ($inHouse['status']) {
                                                case 'wait':
                                                    $btnClass = 'btn-warning';
                                                    $btnText = 'ยังไม่ได้ติดต่อ';
                                                    break;
                                                case 'complete':
                                                    $btnClass = 'btn-success';
                                                    $btnText = 'ติดต่อกลับแล้ว';
                                                    break;
                                            }
                                            ?>
                                            <tr style="">
                                                <td style="vertical-align: top"><?php echo $contactPerson; ?></td>
                                                <td style="vertical-align: top"><?php echo $inHouse['organization_name']; ?></td>
                                                <td style="vertical-align: top"><?php echo $course; ?></td>
                                                <td style="vertical-align: top"><?php echo $inHouse['place']; ?></td>
                                                <td style="vertical-align: top"><?php echo $inHouse['remark']; ?></td>
                                                <td style="vertical-align: top; text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>

                                                <td style="text-align: center" nowrap>
                                                    <button type="button" class="btn-xs <?php echo $btnClass; ?>" style="width: 90px;"
                                                            onClick="onClickStatus(<?php echo $inHouse['id']; ?>, '<?php echo $inHouse['status']; ?>')">
                                                        <?php echo $btnText ?>
                                                    </button>
                                                    <div id="spanLoading<?php echo $inHouse['id']; ?>" style="text-align: center; display: none">
                                                        <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                                    </div>
                                                </td>

                                                <td nowrap>
                                                    <form method="get" action="academic_papers_add_edit.php" style="display: inline; margin: 0">
                                                        <?php
                                                        if (currentUserHasPermission(PERMISSION_MANAGE_IN_HOUSE)) {
                                                            ?>
                                                            <button type="button" class="btn btn-warning"
                                                                    style="margin-left: 3px"
                                                                    onClick="onClickEdit(
                                                                    <?= $inHouse['id']; ?>,
                                                                            '<?= $inHouse['title']; ?>',
                                                                            '<?= $inHouse['first_name']; ?>',
                                                                            '<?= $inHouse['last_name']; ?>',
                                                                            '<?= $inHouse['phone']; ?>',
                                                                            '<?= $inHouse['email']; ?>',
                                                                            '<?= $inHouse['organization_name']; ?>',
                                                                            '<?= $inHouse['course']; ?>',
                                                                    <?= $inHouse['num_day']; ?>,
                                                                    <?= $inHouse['num_trainee']; ?>,
                                                                            '<?= $inHouse['place']; ?>',
                                                                            '<?= $inHouse['remark']; ?>',
                                                                            )">
                                                                <span class="fa fa-pencil"></span>&nbsp;
                                                                แก้ไข
                                                            </button>
                                                            <button type="button" class="btn btn-danger"
                                                                    style="margin-left: 3px; margin-right: 3px"
                                                                    onclick="onClickDelete(
                                                                            this,
                                                                    <?= $inHouse['id']; ?>,
                                                                            '<?= "{$inHouse['first_name']} {$inHouse['last_name']}"; ?>'
                                                                            )">
                                                                <span class="fa fa-remove"></span>&nbsp;
                                                                ลบ
                                                            </button>
                                                            <?php
                                                        }
                                                        ?>
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

                        <!--Export-->
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Export</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"
                                            data-toggle="tooltip" title="ย่อ">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <!-- /.box-tools -->
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="margin">
                                    <div class="btn-group" style="margin-right: 6px">
                                        <a target="_blank" href="excel_in_house.php"
                                           class="btn btn-default"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;รายชื่อผู้ติดต่อ</a>
                                    </div>
                                </div>
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
        let shouldReload = false;

        $(document).ready(function () {
            $('#tableUser').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[5, 'desc']],
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

            $('#editModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    location.reload(true);
                }
            });

            $('#formEdit').on('submit', (event) => {
                event.preventDefault();
                if (confirm("ยืนยันแก้ไขข้อมูล?")) {
                    doUpdate();
                }
            });
        });

        function onClickEdit(id, title, firstName, lastName, phone, email, organizationName,
                             course, numDay, numTrainee, place, remark) {
            $('#editModal #alertSuccess').hide();
            $('#editModal #alertError').hide();
            $('#editModal #spanLoading').hide();

            $('#formEdit #inputId').val(id);
            $('#formEdit #inputTitle').val(title);
            $('#formEdit #inputFirstName').val(firstName);
            $('#formEdit #inputLastName').val(lastName);
            $('#formEdit #inputPhone').val(phone);
            $('#formEdit #inputEmail').val(email);
            $('#formEdit #inputOrganizationName').val(organizationName);
            $('#formEdit #inputCourse').val(course);
            $('#formEdit #inputNumDay').val(numDay);
            $('#formEdit #inputNumTrainee').val(numTrainee);
            $('#formEdit #inputPlace').val(place);
            $('#formEdit #inputRemark').val(remark);

            $('#editModal').modal('show');
        }

        function doUpdate() {
            const loadingIcon = $('#editModal #spanLoading');

            loadingIcon.show();
            $('#editModal #alertSuccess').hide();
            $('#editModal #alertError').hide();

            $('#formEdit').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    loadingIcon.hide();

                    if (data.error_code === 0) {
                        $('#editModal #alertSuccessText').text(data.error_message);
                        $('#editModal #alertSuccess').show();

                        shouldReload = true;
                    } else {
                        $('#editModal #alertErrorText').text(data.error_message);
                        $('#editModal #alertError').show();
                    }
                },
                error: () => {
                    loadingIcon.hide();
                    $('#editModal #alertErrorText').val('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                    $('#editModal #alertError').show();
                }
            });
        }

        function onClickDelete(element, id, displayName) {
            BootstrapDialog.show({
                title: 'ลบข้อมูลผู้ติดต่อ In-House Training',
                message: 'ยืนยันลบข้อมูลการติดต่อจาก \'' + displayName + '\' ?',
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
                '../api/api.php/delete_in_house',
                {
                    id: id,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบข้อมูลผู้ติดต่อ In-House Training - ผิดพลาด',
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
                    title: 'ลบข้อมูลผู้ติดต่อ In-House Training - ผิดพลาด',
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

        function onClickStatus(id, oldStatus) {
            <?php
            if (currentUserHasPermission(PERMISSION_MANAGE_IN_HOUSE)) {
            ?>
            let newStatus = null;
            switch (oldStatus) {
                case 'wait':
                    newStatus = 'complete';
                    newStatusText = 'ติดต่อกลับแล้ว';
                    break;
                case 'complete':
                    newStatus = 'wait';
                    newStatusText = 'ยังไม่ได้ติดต่อกลับ';
                    break;
            }
            BootstrapDialog.show({
                title: 'ยืนยันเปลี่ยนสถานะ',
                message: 'ยืนยันเปลี่ยนสถานะรายการนี้เป็น \'' + newStatusText + '\' ?',
                buttons: [
                    {
                        label: 'ยกเลิก',
                        action: function (self) {
                            self.close();
                        }
                    },
                    {
                        label: 'ตกลง',
                        action: function (self) {
                            self.close();
                            doUpdateStatus(id, newStatus);
                        }
                    }
                ]
            });
            <?php
            } else {
            ?>
            alert('คุณไม่มีสิทธิ์แก้ไขสถานะ');
            <?php
            }
            ?>
        }

        function doUpdateStatus(id, newStatus) {
            $('#spanLoading' + id).show();
            $.post(
                '../api/api.php/update_in_house_status',
                {
                    id: id,
                    newStatus: newStatus
                }
            ).done(function (data) {
                $('#spanLoading' + id).hide();
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ผิดพลาด',
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
                $('#spanLoading' + id).hide();
                BootstrapDialog.show({
                    title: 'ผิดพลาด',
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
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>