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
                                                '%s<br><i class="fa fa-calendar" style="color: black"></i> %d วัน<br><i class="fa fa-users" style="color: black"></i> %d ผู้เข้าอบรม',
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
        });

        function onClickStatus(id, oldStatus) {
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
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';
?>