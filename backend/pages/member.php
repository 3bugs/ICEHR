<?php
require_once '../include/head_php.inc';

$sql = "SELECT m.id, m.title, m.first_name, m.last_name, m.birth_date, TIMESTAMPDIFF(YEAR, m.birth_date, CURDATE()) AS age, m.phone, m.email, 
       m.job_position, m.organization_name, m.organization_type, o.name AS organization_type_text, m.organization_type_custom, m.organization_phone, 
       m.address, m.sub_district, m.district, m.province, m.postal_code, m.tax_id
            FROM member m 
                LEFT JOIN organization_type o 
                    ON m.organization_type = o.id 
            ORDER BY id DESC";

if ($result = $db->query($sql)) {
    $memberList = array();
    while ($row = $result->fetch_assoc()) {
        $member = array();
        $member['id'] = (int)$row['id'];
        $member['title'] = $row['title'];
        $member['first_name'] = $row['first_name'];
        $member['last_name'] = $row['last_name'];
        $member['birth_date'] = $row['birth_date'];
        $member['age'] = (int)$row['age'];
        $member['phone'] = $row['phone'];
        $member['email'] = $row['email'];
        $member['job_position'] = $row['job_position'];
        $member['organization_name'] = $row['organization_name'];
        $member['organization_type'] = (int)$row['organization_type'];
        $member['organization_type_text'] = $row['organization_type_text'];
        $member['organization_type_custom'] = $row['organization_type_custom'];
        $member['address'] = $row['address'];
        $member['sub_district'] = $row['sub_district'];
        $member['district'] = $row['district'];
        $member['province'] = $row['province'];
        $member['postal_code'] = $row['postal_code'];
        $member['organization_phone'] = $row['organization_phone'];
        $member['tax_id'] = $row['tax_id'];

        array_push($memberList, $member);
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
                    สมาชิกเว็บไซท์
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">&nbsp;</h3>
                            </div>
                            <div class="box-body">
                                <table id="tableMember" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 15%; text-align: center">ชื่อ-นามสกุล</th>
                                        <th style="width: 10%; text-align: center">เกิด, อายุ</th>
                                        <th style="width: 15%; text-align: center">โทร, อีเมล</th>
                                        <th style="width: 20%; text-align: center">ตำแหน่ง, หน่วยงาน</th>
                                        <th style="width: 25%; text-align: center">ที่อยู่หน่วยงาน</th>
                                        <th style="width: 15%; text-align: center">เลขภาษี</th>
                                        <th style="text-align: center">จัดการ</th>
                                        <?php
                                        if ($_SESSION[KEY_SESSION_USER_ROLE] == 'super_admin') {
                                            ?>
                                            <!--<th style="text-align: center">แก้ไข</th>-->
                                            <th style="width: 10px; text-align: center">ลบ</th>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (sizeof($memberList) == 0) {
                                        ?>
                                        <tr valign="middle">
                                            <td colspan="20" align="center">ไม่มีข้อมูล</td>
                                        </tr>
                                        <?php
                                    } else {
                                        foreach ($memberList as $member) {
                                            $thaiMonth = array(
                                                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                                                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
                                            );
                                            $memberId = $member['id'];
                                            $memberTitle = $member['title'];
                                            $memberFirstName = $member['first_name'];
                                            $memberLastName = $member['last_name'];

                                            $memberBirthDate = $member['birth_date'];
                                            $displayBirthDate = getThaiShortDate(date_create($memberBirthDate));
                                            $dateHidden = "<span style=\"display: none\">$memberBirthDate</span></span>";
                                            $memberAge = $member['age'];

                                            $memberPhone = $member['phone'];
                                            $memberEmail = $member['email'];
                                            $memberJobPosition = $member['job_position'];
                                            $memberOrganizationName = $member['organization_name'];
                                            $memberOrganizationType = $member['organization_type'];
                                            $memberOrganizationTypeText = $member['organization_type_text'];
                                            $memberOrganizationTypeCustom = $member['organization_type_custom'];
                                            $memberTaxId = $member['tax_id'];

                                            $organizationAddress = $member['address'] == null ? 'N/A' : sprintf(
                                                "%s %s %s %s %s<br>โทร: %s",
                                                $member['address'], $member['sub_district'], $member['district'],
                                                $member['province'], $member['postal_code'], $member['organization_phone']
                                            );

                                            ?>
                                            <tr style="">
                                                <td style="vertical-align: top">
                                                    <span style="display: none;"><?php echo "$memberFirstName $memberLastName"; ?></span>
                                                    <?php echo "$memberTitle<br/>$memberFirstName $memberLastName"; ?>
                                                </td>
                                                <td style="vertical-align: top"><?php echo "$dateHidden $displayBirthDate<br/>($memberAge ปี)"; ?></td>
                                                <td style="vertical-align: top"><?php echo "$memberPhone<br/><a href=\"mailto:$memberEmail\">$memberEmail</a>"; ?></td>
                                                <td style="vertical-align: top">
                                                    <?php echo $memberJobPosition . '<br/><strong>' . $memberOrganizationName . '</strong><br/>'
                                                        . ($memberOrganizationType === 9999 ? "($memberOrganizationTypeCustom)" : "($memberOrganizationTypeText)");
                                                    ?>
                                                </td>
                                                <td style="vertical-align: top"><?php echo $organizationAddress; ?></td>
                                                <td style="vertical-align: top"><?php echo ($member['address'] == null ? 'N/A' : $memberTaxId); ?></td>
                                                <td nowrap>
                                                    <button type="button" class="btn btn-info">
                                                        <span class="fa fa-info"></span>&nbsp;
                                                        รายละเอียด
                                                    </button>
                                                </td>

                                                <?php
                                                if ($_SESSION[KEY_SESSION_USER_ROLE] == 'super_admin') {
                                                    ?>
                                                    <!--<td style="text-align: center">
                                                        <form method="post" action="">
                                                            <input type="hidden" name="edit_mode" value="true">
                                                            <input type="hidden" name="election_id"
                                                                   value="<?php /*echo $member['id']; */ ?>">
                                                            <button type="submit" class="btn btn-warning">
                                                                <span class="fa fa-edit"></span>&nbsp;
                                                                แก้ไข
                                                            </button>
                                                        </form>
                                                    </td>-->
                                                    <td style="width: 10px; text-align: center">
                                                        <button type="button" class="btn btn-danger"
                                                                onclick="onClickDelete(this, <?php echo $memberId; ?>,
                                                                        '<?php echo "$memberTitle $memberFirstName $memberLastName"; ?>')">
                                                            <span class="fa fa-remove"></span>&nbsp;
                                                            ลบ
                                                        </button>
                                                    </td>
                                                    <?php
                                                }
                                                ?>
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
            $('#tableMember').DataTable({
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

        function doDeleteMember(memberId) {
            $.post(
                '../api/api.php/delete_member',
                {
                    member_id: memberId
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'Delete Member',
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
                    title: 'Delete Member',
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

        function onClickDelete(element, memberId, memberDisplayName) {
            BootstrapDialog.show({
                title: 'Confirm Delete Member',
                message: 'ยืนยันลบสมาชิก \'' + memberDisplayName + '\' ?',
                buttons: [
                    {
                        label: 'ยกเลิก',
                        action: function (self) {
                            self.close();
                        }
                    },
                    {
                        label: 'ลบ',
                        action: function (self) {
                            self.close();
                            doDeleteMember(memberId);
                        }
                    }
                ]
            });
        }

        function onClickEdit(element, electionId, dateString) {
            window.location.href = 'election_add.php?edit=true&election_id=' + electionId;
        }

        function doChangeStatus(electionId, newStatus) {
            $.post(
                '../api/api.php/update_election_status',
                {
                    election_id: electionId,
                    new_status: newStatus
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'Change Election Status',
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
                    title: 'Change Election Status',
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

        function onChangeStatus(element, electionId, dateString) {
            BootstrapDialog.show({
                title: 'Confirm Change Election Status',
                message: 'ยืนยันเปลี่ยนสถานะการเลือกตั้ง วันที่ ' + dateString + ' ?',
                buttons: [
                    {
                        label: 'ยกเลิก',
                        action: function (self) {
                            self.close();
                            location.reload(true);
                        }
                    },
                    {
                        label: 'เปลี่ยนสถานะ',
                        action: function (self) {
                            self.close();
                            const newStatus = element.checked ? 1 : 0;
                            doChangeStatus(electionId, newStatus);
                        }
                    }
                ]
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