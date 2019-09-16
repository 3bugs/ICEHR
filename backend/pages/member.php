<?php
require_once '../include/head_php.inc';

$sql = "SELECT m.id, m.title, m.first_name, m.last_name, m.birth_date, TIMESTAMPDIFF(YEAR, m.birth_date, CURDATE()) AS age, m.phone, m.email, 
               m.job_position, m.organization_name, m.organization_type, ot.name AS organization_type_name, m.organization_type_custom, m.organization_phone, 
               m.address, m.sub_district, m.district, m.province, m.postal_code, m.tax_id
        FROM member m
            LEFT JOIN organization_type ot
                ON m.organization_type = ot.id 
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
        $member['organization_type_name'] = $row['organization_type_name'];
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

define('MEMBER_TYPE_ORGANIZATION', 'tableMemberOrganization');
define('MEMBER_TYPE_PERSON', 'tableMemberPerson');

$organizationMemberList = array();
$personMemberList = array();
foreach ($memberList as $member) {
    if (is_null($member['address'])) {
        array_push($personMemberList, $member);
    } else {
        array_push($organizationMemberList, $member);
    }
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
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tabOrganization" data-toggle="tab">องค์กร/บริษัท</a></li>
                                        <li><a href="#tabPerson" data-toggle="tab">บุคคล</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tabOrganization">
                                            <?= generateTable($organizationMemberList, MEMBER_TYPE_ORGANIZATION); ?>
                                        </div>
                                        <!-- /.tab-pane -->

                                        <div class="tab-pane" id="tabPerson">
                                            <?= generateTable($personMemberList, MEMBER_TYPE_PERSON); ?>
                                        </div>
                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div>
                                <!-- nav-tabs-custom -->
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
                                        <a target="_blank" href="excel_member_list.php"
                                           class="btn btn-default"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;รายชื่อสมาชิกเว็บไซต์</a>
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
            $('#tableMemberOrganization').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[0, 'desc']],
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

            $('#tableMemberPerson').DataTable({
                stateSave: true,
                stateDuration: -1, // sessionStorage
                order: [[0, 'desc']],
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
    </script>

    <?php require_once('../include/foot.inc'); ?>
    <!-- DataTables -->
    <script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    </body>
    </html>

<?php
require_once '../include/foot_php.inc';

function generateTable($memberList, $memberType)
{
    ?>
    <table id="<?= $memberType; ?>" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th style="width: 15%; text-align: center">ชื่อ-นามสกุล</th>
            <th style="width: 10%; text-align: center">วันเกิด / อายุ</th>
            <th style="width: 15%; text-align: center">เบอร์โทร / อีเมล</th>
            <th style="width: 20%; text-align: center">ตำแหน่ง / หน่วยงาน</th>
            <?php
            if ($memberType === MEMBER_TYPE_ORGANIZATION) {
                ?>
                <th style="width: 25%; text-align: center">ที่อยู่หน่วยงาน</th>
                <th style="width: 15%; text-align: center">เลขภาษี</th>
                <?php
            }
            ?>
            <?php
            if (TRUE /*$_SESSION[KEY_SESSION_USER_ROLE] == 'super_admin'*/) {
                ?>
                <!--<th style="text-align: center">แก้ไข</th>-->
                <th style="width: 10px; text-align: center">จัดการ</th>
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
                $memberOrganizationTypeText = $member['organization_type_name'];
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
                        <span style="display: none;"><?= "$memberFirstName $memberLastName"; ?></span>
                        <?= "$memberTitle<br/>$memberFirstName $memberLastName"; ?>
                    </td>
                    <td style="vertical-align: top"><?= "$dateHidden $displayBirthDate<br/>($memberAge ปี)"; ?></td>
                    <td style="vertical-align: top"><?= "$memberPhone<br/><a href=\"mailto:$memberEmail\">$memberEmail</a>"; ?></td>
                    <td style="vertical-align: top">
                        <?= $memberJobPosition . '<br/><strong>' . $memberOrganizationName . '</strong><br/>'
                            . ($memberOrganizationType === 9999 ? "($memberOrganizationTypeCustom)" : "($memberOrganizationTypeText)");
                        ?>
                    </td>
                    <?php
                    if ($memberType === MEMBER_TYPE_ORGANIZATION) {
                        ?>
                        <td style="vertical-align: top"><?= $organizationAddress; ?></td>
                        <td style="vertical-align: top"><?= $memberTaxId; ?></td>
                        <?php
                    }
                    ?>
                    <?php
                    if (TRUE /*$_SESSION[KEY_SESSION_USER_ROLE] == 'super_admin'*/) {
                        ?>
                        <td style="text-align: left" nowrap>
                            <!--<button type="button" class="btn btn-info" style="margin-right: 3px">
                                <span class="fa fa-info"></span>&nbsp;
                                รายละเอียด
                            </button>-->
                            <button type="button" class="btn btn-danger"
                                    onclick="onClickDelete(this, <?= $memberId; ?>,
                                            '<?= "$memberTitle $memberFirstName $memberLastName"; ?>')">
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
    <?php
}

?>