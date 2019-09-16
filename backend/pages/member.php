<?php
require_once '../include/head_php.inc';

$sql = "SELECT m.id, m.member_type, m.title, m.first_name, m.last_name, m.birth_date, TIMESTAMPDIFF(YEAR, m.birth_date, CURDATE()) AS age, m.phone, m.email, 
               m.job_position, m.organization_name, m.organization_type, ot.name AS organization_type_name, m.organization_type_custom, m.organization_phone, 
               m.address, m.sub_district, m.district, m.province, m.postal_code, m.tax_id, m.created_at
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
        $member['member_type'] = $row['member_type'];
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
        $member['created_at'] = $row['created_at'];

        array_push($memberList, $member);
    }
    $result->close();
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    $db->close();
    exit();
}

$sql = "SELECT id, name FROM organization_type ORDER BY id";
if ($result = $db->query($sql)) {
    $organizationTypeList = array();
    while ($row = $result->fetch_assoc()) {
        array_push($organizationTypeList, $row);
    }
    $result->close();
} else {
    echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล';
    $db->close();
    exit();
}

//define('MEMBER_TYPE_ORGANIZATION', 'tableMemberOrganization');
//define('MEMBER_TYPE_PERSON', 'tableMemberPerson');

$organizationMemberList = array();
$personMemberList = array();
foreach ($memberList as $member) {
    if ($member['member_type'] === MEMBER_TYPE_PERSON) {
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

    <!-- Edit modal -->
    <div class="modal fade" id="editModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal">&times;
                    </button>-->
                    <h4 class="modal-title">
                        ข้อมูลสมาชิกเว็บไซต์
                        <div class="pull-right">
                            <small>สมัครสมาชิกเมื่อ <span id="spanCreatedAt"></span></small>
                        </div>
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
                          action="../api/api.php/update_member"
                          method="post"
                          autocomplete="off"
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
                                        <input type="text" class="form-control" required
                                               id="inputTitle" name="title"
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
                                        <input type="text" class="form-control" required
                                               id="inputFirstName" name="firstName"
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
                                        <input type="text" class="form-control" required
                                               id="inputLastName" name="lastName"
                                               oninvalid="this.setCustomValidity('กรอกนามสกุล')"
                                               oninput="this.setCustomValidity('')">
                                    </div>
                                </div>
                            </div>

                            <!--เบอร์โทร อีเมล-->
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="inputPhone">เบอร์โทร:</label>
                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-phone"></i>
                                                    </span>
                                        <input type="text" class="form-control" required
                                               id="inputPhone" name="phone"
                                               oninvalid="this.setCustomValidity('กรอกเบอร์โทร')"
                                               oninput="this.setCustomValidity('')">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputEmail">อีเมล:</label>
                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope-o"></i>
                                                    </span>
                                        <input type="email" class="form-control" disabled
                                               id="inputEmail" name="email"
                                               oninvalid="this.setCustomValidity('กรอกอีเมลที่มีรูปแบบถูกต้อง')"
                                               oninput="this.setCustomValidity('')">
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputJobPosition">ตำแหน่งงาน:</label>
                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-tag"></i>
                                                    </span>
                                        <input type="text" class="form-control"
                                               id="inputJobPosition" name="jobPosition">
                                    </div>
                                </div>
                            </div>

                            <label style="display: block; text-align: center; margin-top: 10px">ข้อมูลหน่วยงาน</label>
                            <div style="padding: 15px 15px 5px 15px; border: 0px solid grey; background: #f4f4f4">
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

                                <!--ประเภทหน่วยงาน-->
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="inputOrganizationType">ประเภทหน่วยงาน:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-navicon"></i>
                                                    </span>
                                            <select id="selectOrganizationType" class="form-control"
                                                    name="organizationType"
                                                    oninvalid="this.setCustomValidity('เลือกประเภทหน่วยงาน')"
                                                    oninput="this.setCustomValidity('')">
                                                <option value="0" disabled selected>-- เลือกประเภทหน่วยงาน --</option>
                                                <?php
                                                foreach ($organizationTypeList as $organizationType) {
                                                    ?>
                                                    <option value="<?= $organizationType['id']; ?>">
                                                        <?= $organizationType['name']; ?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                                <option value="9999">อื่นๆ (กรอกเอง)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputOrganizationTypeCustom">ประเภทหน่วยงาน (กรอกเอง):</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-pencil"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputOrganizationTypeCustom" name="organizationTypeCustom">
                                        </div>
                                    </div>
                                </div>

                                <!--ที่อยู่-->
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="inputAddress">ที่อยู่:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputAddress" name="address">
                                        </div>
                                    </div>
                                </div>

                                <!--แขวง, เขต-->
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="inputSubDistrict">แขวง/ตำบล:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputSubDistrict" name="subDistrict">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputDistrict">เขต/อำเภอ:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputDistrict" name="district">
                                        </div>
                                    </div>
                                </div>

                                <!--จังหวัด, รหัสไปรษณีย์-->
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="inputProvince">จังหวัด:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputProvince" name="province">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPostalCode">รหัสไปรษณีย์:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputPostalCode" name="postalCode">
                                        </div>
                                    </div>
                                </div>

                                <!--เบอร์โทรหน่วยงาน-->
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="inputOrganizationPhone">เบอร์โทรหน่วยงาน:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-phone-square"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputOrganizationPhone" name="organizationPhone">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputTaxId">เลขประจำตัวผู้เสียภาษี:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-slack"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   maxlength="13"
                                                   id="inputTaxId" name="taxId">
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                if (currentUserHasPermission(PERMISSION_MANAGE_WEB_CONTENT)) {
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
                    สมาชิกเว็บไซต์
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
        let shouldReload = false;

        $(document).ready(function () {
            $('#<?= MEMBER_TYPE_ORGANIZATION; ?>').DataTable({
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

            $('#<?= MEMBER_TYPE_PERSON; ?>').DataTable({
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

            $('#selectOrganizationType').change(function () {
                //alert(this.value);
                $("#inputOrganizationTypeCustom").prop("disabled", parseInt(this.value) !== 9999);
                if (parseInt(this.value) === 9999) {
                    $("#inputOrganizationTypeCustom").focus();
                }
            });

            $('#editModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    location.reload(true);
                }
            });

            $('#formEdit').on('submit', (event) => {
                event.preventDefault();
                if (validateForm()) {
                    if (confirm("ยืนยันแก้ไขข้อมูล?")) {
                        doUpdate();
                    }
                }
            });
        });

        /*$(document).ready(function() {
            if (location.hash) {
                $("a[href='" + location.hash + "']").tab("show");
            }
            $(document.body).on("click", "a[data-toggle='tab']", function(event) {
                location.hash = this.getAttribute("href");
            });
        });
        $(window).on("popstate", function() {
            var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
            $("a[href='" + anchor + "']").tab("show");
        });*/

        $(document).ready(function () {
            if (location.hash) {
                $('a[href=\'' + location.hash + '\']').tab('show');
            }
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('a[href="' + activeTab + '"]').tab('show');
            }
        });
        
        $('body').on('click', 'a[data-toggle=\'tab\']', function (e) {
            e.preventDefault();
            var tab_name = this.getAttribute('href');
            if (history.pushState) {
                history.pushState(null, null, tab_name);
            } else {
                location.hash = tab_name;
            }
            localStorage.setItem('activeTab', tab_name);

            $(this).tab('show');
            return false;
        });
        $(window).on('popstate', function () {
            var anchor = location.hash ||
                $('a[data-toggle=\'tab\']').first().attr('href');
            $('a[href=\'' + anchor + '\']').tab('show');
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

        function onClickEdit(id, title, firstName, lastName, birthDate, phone, email, jobPosition,
                             organizationName, organizationType, organizationTypeText, organizationTypeCustom,
                             address, subDistrict, district, province, postalCode, organizationPhone, taxId,
                             displayCreatedDate, displayCreatedTime) {
            $('#editModal #alertSuccess').hide();
            $('#editModal #alertError').hide();
            $('#editModal #spanLoading').hide();

            $('#formEdit #inputId').val(id);
            $('#formEdit #inputTitle').val(title);
            $('#formEdit #inputFirstName').val(firstName);
            $('#formEdit #inputLastName').val(lastName);
            $('#formEdit #inputPhone').val(phone);
            $('#formEdit #inputEmail').val(email);
            $('#formEdit #inputJobPosition').val(jobPosition);

            $('#formEdit #inputOrganizationName').val(organizationName);
            $('#formEdit #selectOrganizationType').val(organizationType);
            $('#formEdit #inputOrganizationTypeCustom').val('');
            if (organizationType === 9999) {
                $('#formEdit #inputOrganizationTypeCustom').val(organizationTypeCustom);
            }
            $("#inputOrganizationTypeCustom").prop("disabled", organizationType !== 9999);

            $('#formEdit #inputAddress').val(address);
            $('#formEdit #inputSubDistrict').val(subDistrict);
            $('#formEdit #inputDistrict').val(district);
            $('#formEdit #inputProvince').val(province);
            $('#formEdit #inputPostalCode').val(postalCode);
            $('#formEdit #inputOrganizationPhone').val(organizationPhone);
            $('#formEdit #inputTaxId').val(taxId);

            $('#editModal #spanCreatedAt').text(displayCreatedDate + ', ' + displayCreatedTime);

            $('#editModal').modal('show');
        }

        function validateForm() {
            let valid = true;

            const selectOrganizationType = $('#selectOrganizationType');
            const inputOrganizationTypeCustom = $('#inputOrganizationTypeCustom');

            if (parseInt(selectOrganizationType.val()) === 9999 && inputOrganizationTypeCustom.val().trim() === '') {
                alert('กรอกประเภทหน่วยงาน');
                inputOrganizationTypeCustom.focus();
                valid = false;
            }

            return valid;
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
            <th style="text-align: center">วัน/เวลาที่สมัคร</th>
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
                $birthDateHidden = "<span style=\"display: none\">$memberBirthDate</span></span>";
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

                $createdAt = $member['created_at'];
                $dateTimePart = explode(' ', $createdAt);
                $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                $timePart = explode(':', $dateTimePart[1]);
                $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                $displayDateTime = "$displayDate<br>$displayTime";
                $createdDateHidden = '<span style="display: none">' . $createdAt . '</span></span>';

                ?>
                <tr style="">
                    <td style="vertical-align: top">
                        <span style="display: none;"><?= "$memberFirstName $memberLastName"; ?></span>
                        <?= "$memberTitle<br/>$memberFirstName $memberLastName"; ?>
                    </td>
                    <td style="vertical-align: top"><?= "$birthDateHidden $displayBirthDate<br/>($memberAge ปี)"; ?></td>
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
                    <td style="vertical-align: top; text-align: center" nowrap><?= "{$createdDateHidden}{$displayDateTime}"; ?></td>
                    <td style="text-align: left" nowrap>
                        <button type="button" class="btn btn-info"
                                style="margin-right: 3px"
                                onclick="onClickEdit(
                                <?= $memberId; ?>,
                                        '<?= htmlentities($memberTitle); ?>',
                                        '<?= htmlentities($memberFirstName); ?>',
                                        '<?= htmlentities($memberLastName); ?>',
                                        '<?= $memberBirthDate; ?>',
                                        '<?= $memberPhone; ?>',
                                        '<?= $memberEmail; ?>',
                                        '<?= htmlentities($memberJobPosition); ?>',
                                        '<?= htmlentities($memberOrganizationName); ?>',
                                <?= $memberOrganizationType == null ? 0 : $memberOrganizationType; ?>,
                                        '<?= $memberOrganizationTypeText; ?>',
                                        '<?= htmlentities($memberOrganizationTypeCustom); ?>',
                                        '<?= htmlentities($member['address']); ?>',
                                        '<?= $member['sub_district']; ?>',
                                        '<?= $member['district']; ?>',
                                        '<?= $member['province']; ?>',
                                        '<?= $member['postal_code']; ?>',
                                        '<?= $member['organization_phone']; ?>',
                                        '<?= $member['tax_id']; ?>',
                                        '<?= $displayDate; ?>',
                                        '<?= $displayTime; ?>',
                                        )">
                            <span class="fa fa-info"></span>&nbsp;
                            รายละเอียด
                        </button>
                        <?php
                        if (currentUserHasPermission(PERMISSION_MANAGE_WEB_CONTENT)) {
                            ?>
                            <button type="button" class="btn btn-danger"
                                    onclick="onClickDelete(
                                            this,
                                    <?= $memberId; ?>,
                                            '<?= "$memberTitle $memberFirstName $memberLastName"; ?>'
                                            )">
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
    <?php
}

?>