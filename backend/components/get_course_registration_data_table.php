<?php
require_once '../api/global.php';

/**
 * @param mysqli $db
 * @param string $serviceType
 * @param int $paramCourseId
 * @param int $responsibleUserId
 */
function getCourseRegistrationDataTable($db, $serviceType, $paramCourseId = null, $responsibleUserId = null)
{
    $whereClause = ' TRUE ';
    if ($paramCourseId !== null) {
        $whereClause = " course_id = $paramCourseId ";
    }
    switch ($serviceType) {
        case SERVICE_TYPE_TRAINING:
            $sql = "SELECT ct.id, ct.form_number, ct.title, ct.first_name, ct.last_name, ct.phone, ct.email, 
                           ct.register_status, ct.created_at, ct.course_registration_id, ct.paid_amount, ct.payment_date , ct.receipt_number,
                           ct.job_position, ct.organization_name, ct.organization_type, ct.organization_type_custom,
                           cr.course_id, cr.coordinator_title, cr.coordinator_first_name, cr.coordinator_last_name, 
                           cr.coordinator_phone, cr.coordinator_email, cr.coordinator_job_position, 
                           cr.coordinator_organization_name, cr.coordinator_organization_type, cr.coordinator_organization_type_custom,
                           cr.receipt_name, cr.receipt_address, cr.receipt_sub_district, cr.receipt_district, cr.receipt_province,
                           cr.receipt_postal_code, cr.receipt_organization_phone, cr.receipt_tax_id
                    FROM course_trainee ct 
                        INNER JOIN course_registration cr 
                            ON ct.course_registration_id = cr.id
                    WHERE $whereClause
                    ORDER BY ct.id DESC ";
            break;

        case SERVICE_TYPE_SOCIAL:
            $sql = "SELECT cr.id, cr.form_number, cr.title, cr.first_name, cr.last_name, cr.phone, cr.email, 
                           cr.occupation, cr.work_place, cr.address, cr.sub_district, cr.district, cr.province, cr.postal_code,
                           cr.contact_name, cr.contact_phone, cr.disease,
                           cr.receipt_name, cr.receipt_address, cr.receipt_sub_district, cr.receipt_district, cr.receipt_province, cr.receipt_postal_code,
                           cr.register_status, cr.created_at, cr.course_id, cr.paid_amount, cr.payment_date, cr.receipt_number
                    FROM course_registration_social cr  
                    WHERE $whereClause
                    ORDER BY id DESC";
            break;

        case SERVICE_TYPE_DRIVING_LICENSE:
            $sql = "SELECT cr.id, cr.form_number, 
                           cr.title, cr.first_name, cr.last_name, cr.pid, cr.address, cr.moo, cr.soi, cr.road, 
                           cr.sub_district, cr.district, cr.province, cr.phone, 
                           cr.pid_file_name, cr.pid_file_name_2, cr.pid_file_name_3, cr.pid_file_name_4, cr.pid_file_name_5, 
                           cr.register_status, cr.doc_status, cr.created_at, cr.course_id, cr.paid_amount, cr.payment_date, cr.receipt_number,
                           cr.license_type, cr.subject_1_result, cr.subject_2_result, cr.subject_3_result, cr.subject_4_result,
                           cr.certificate_number,
                           ct.id AS course_type_id, ct.title AS course_type_title, ct.application_fee AS fee
                    FROM course_registration_driving_license cr 
                        INNER JOIN driving_license_course_type ct 
                            ON cr.course_type = ct.id 
                    WHERE $whereClause
                    ORDER BY id DESC";
            break;

        default:
            echo '<div style="color: red; text-align: center">ERROR: ไม่ได้ระบุ Service Type หรือระบุ Service Type ไม่ถูกต้อง</div>';
            return;
    }

    if ($result = $db->query($sql)) {
        $traineeList = array();
        while ($row = $result->fetch_assoc()) {
            $trainee = array();
            $trainee['id'] = (int)$row['id'];
            $trainee['course_registration_id'] = (int)$row['course_registration_id'];
            $trainee['form_number'] = $row['form_number'];
            $trainee['title'] = $row['title'];
            $trainee['first_name'] = $row['first_name'];
            $trainee['last_name'] = $row['last_name'];

            $trainee['job_position'] = $row['job_position'];
            $trainee['organization_name'] = $row['organization_name'];
            $trainee['organization_type'] = $row['organization_type'];
            $trainee['organization_type_custom'] = $row['organization_type_custom'];

            $trainee['address'] = $row['address'];
            $trainee['moo'] = $row['moo'];
            $trainee['soi'] = $row['soi'];
            $trainee['road'] = $row['road'];
            $trainee['sub_district'] = $row['sub_district'];
            $trainee['district'] = $row['district'];
            $trainee['province'] = $row['province'];
            $trainee['postal_code'] = $row['postal_code']; // สำหรับสังคม

            $trainee['phone'] = $row['phone'];
            $trainee['email'] = $row['email'];
            $trainee['created_at'] = $row['created_at'];
            $trainee['register_status'] = $row['register_status'];
            $trainee['paid_amount'] = $row['paid_amount'];
            $trainee['payment_date'] = $row['payment_date'];
            $trainee['receipt_number'] = $row['receipt_number'];
            $trainee['coordinator'] = array(
                'title' => $row['coordinator_title'],
                'first_name' => $row['coordinator_first_name'],
                'last_name' => $row['coordinator_last_name'],
                'phone' => $row['coordinator_phone'],
                'email' => $row['coordinator_email'],
                'job_position' => $row['coordinator_job_position'],
                'organization_name' => $row['coordinator_organization_name'],
                'organization_type' => $row['coordinator_organization_type'],
                'organization_type_custom' => $row['coordinator_organization_type_custom']
            );
            $trainee['receipt'] = array(
                'name' => $row['receipt_name'],
                'address' => $row['receipt_address'],
                'sub_district' => $row['receipt_sub_district'],
                'district' => $row['receipt_district'],
                'province' => $row['receipt_province'],
                'postal_code' => $row['receipt_postal_code'],
                'organization_phone' => $row['receipt_organization_phone'],
                'tax_id' => $row['receipt_tax_id'],
            );
            $trainee['course_id'] = (int)$row['course_id'];

            /*สำหรับบริการสังคม*/
            $trainee['occupation'] = $row['occupation'];
            $trainee['work_place'] = $row['work_place'];
            $trainee['contact_name'] = $row['contact_name'];
            $trainee['contact_phone'] = $row['contact_phone'];
            $trainee['disease'] = $row['disease'];

            /*สำหรับบริการใบขับขี่*/
            $trainee['pid'] = $row['pid'];
            $trainee['pid_file_name'] = $row['pid_file_name'];
            $trainee['pid_file_name_2'] = $row['pid_file_name_2'];
            $trainee['pid_file_name_3'] = $row['pid_file_name_3'];
            $trainee['pid_file_name_4'] = $row['pid_file_name_4'];
            $trainee['pid_file_name_5'] = $row['pid_file_name_5'];
            $trainee['doc_status'] = (int)$row['doc_status'];
            $trainee['driving_license_course_type_id'] = (int)$row['course_type_id'];
            $trainee['driving_license_course_type'] = $row['course_type_title'];
            $trainee['driving_license_course_fee'] = (int)$row['fee'];
            $trainee['license_type'] = (int)$row['license_type'];
            $trainee['subject_1_result'] = $row['subject_1_result'];
            $trainee['subject_2_result'] = $row['subject_2_result'];
            $trainee['subject_3_result'] = $row['subject_3_result'];
            $trainee['subject_4_result'] = $row['subject_4_result'];
            $trainee['certificate_number'] = (int)$row['certificate_number'];

            switch ($trainee['driving_license_course_type_id']) {
                case 1:
                    $trainee['result_status'] = $trainee['subject_1_result'] != NULL
                        && $trainee['subject_2_result'] != NULL
                        && $trainee['subject_3_result'] != NULL
                        && $trainee['subject_4_result'] != NULL;
                    $trainee['certificate_status'] = $trainee['subject_1_result'] == 1
                        && $trainee['subject_2_result'] == 1
                        && $trainee['subject_3_result'] == 1
                        && $trainee['subject_4_result'] == 1;
                    break;
                case 2:
                    $trainee['result_status'] = $trainee['subject_1_result'] != NULL;
                    $trainee['certificate_status'] = $trainee['subject_1_result'] == 1;
                    break;
                case 3:
                    $trainee['result_status'] = $trainee['subject_1_result'] != NULL;
                    $trainee['certificate_status'] = $trainee['subject_1_result'] == 1;
                    break;
            }

            array_push($traineeList, $trainee);
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
        $db->close();
        exit();
    }

    $sql = "SELECT id, title FROM name_title ORDER BY id";
    if ($result = $db->query($sql)) {
        $nameTitleList = array();
        while ($row = $result->fetch_assoc()) {
            $nameTitle = array();
            $nameTitle['id'] = (int)$row['id'];
            $nameTitle['title'] = $row['title'];
            array_push($nameTitleList, $nameTitle);
        }
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
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

    if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
        $sql = "SELECT * FROM driving_license_course_type ORDER BY id";
        if ($result = $db->query($sql)) {
            $drivingLicenseCourseTypeList = array();
            while ($row = $result->fetch_assoc()) {
                $drivingLicenseCourseType = array();
                $drivingLicenseCourseType['id'] = (int)$row['id'];
                $drivingLicenseCourseType['title'] = $row['title'];
                $drivingLicenseCourseType['application_fee'] = (int)$row['application_fee'];
                array_push($drivingLicenseCourseTypeList, $drivingLicenseCourseType);
            }
        } else {
            echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
            $db->close();
            exit();
        }
    }
    ?>

    <!-- Doc training modal -->
    <div class="modal fade" id="editDocTrainingModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal">&times;
                    </button>-->
                    <h4 class="modal-title">
                        ข้อมูลใบสมัครเลขที่ <span id="spanFormNumber"></span>
                        <div class="pull-right">
                            <small>กรอกใบสมัครเมื่อ <span id="spanCreatedAt"></span></small>
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

                    <form id="formEditDocTraining" role="form"
                          action="../api/api.php/update_trainee_training"
                          method="post"
                          autocomplete="off"
                          style="margin-top: 0; margin-bottom: 0">

                        <div class="box-body">
                            <input type="hidden" id="inputId" name="id">
                            <input type="hidden" id="inputCourseRegId" name="courseRegId">

                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tabTrainee" data-toggle="tab">ข้อมูลผู้สมัคร</a></li>
                                    <li id="tabHeadCoordinator" disabled><a href="#tabCoordinator" data-toggle="tab">ข้อมูลผู้ประสานงาน</a></li>
                                    <li><a href="#tabReceipt" data-toggle="tab">ข้อมูลการออกใบเสร็จ</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tabTrainee">
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
                                                    <input type="email" class="form-control" required
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
                                                    <input type="text" class="form-control" required
                                                           id="inputJobPosition" name="jobPosition">
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
                                                    <input type="text" class="form-control" required
                                                           id="inputOrganizationName" name="organizationName">
                                                </div>
                                            </div>
                                        </div>

                                        <!--ประเภทหน่วยงาน-->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="selectOrganizationType">ประเภทหน่วยงาน:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-navicon"></i>
                                                    </span>
                                                    <select id="selectOrganizationType" class="form-control"
                                                            name="organizationType" required
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
                                    </div>
                                    <!-- /.tab-pane -->

                                    <div class="tab-pane" id="tabCoordinator">
                                        <!--คำนำหน้า-ชื่อ-นามสกุล ผู้ประสานงาน-->
                                        <div class="row">
                                            <!--คำนำหน้าชื่อ-->
                                            <div class="form-group col-md-2">
                                                <label for="inputCoordinatorTitle">คำนำหน้าชื่อ:</label>
                                                <div class="input-group">
                                                    <!--<span class="input-group-addon">
                                                        <i class="fa fa-user"></i>
                                                    </span>-->
                                                    <input type="text" class="form-control" required
                                                           id="inputCoordinatorTitle" name="coordinatorTitle"
                                                           oninvalid="this.setCustomValidity('กรอกคำนำหน้าชื่อ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>

                                            <!--ชื่อ-->
                                            <div class="form-group col-md-5">
                                                <label for="inputCoordinatorFirstName">ชื่อ:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user-o"></i>
                                                    </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputCoordinatorFirstName" name="coordinatorFirstName"
                                                           oninvalid="this.setCustomValidity('กรอกชื่อ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                            <!--นามสกุล-->
                                            <div class="form-group col-md-5">
                                                <label for="inputCoordinatorLastName">นามสกุล:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-user-o"></i>
                                                            </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputCoordinatorLastName" name="coordinatorLastName"
                                                           oninvalid="this.setCustomValidity('กรอกนามสกุล')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--เบอร์โทร อีเมล ผู้ประสานงาน-->
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="inputCoordinatorPhone">เบอร์โทร:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-phone"></i>
                                                    </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputCoordinatorPhone" name="coordinatorPhone"
                                                           oninvalid="this.setCustomValidity('กรอกเบอร์โทร')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputCoordinatorEmail">อีเมล:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope-o"></i>
                                                    </span>
                                                    <input type="email" class="form-control" required
                                                           id="inputCoordinatorEmail" name="coordinatorEmail"
                                                           oninvalid="this.setCustomValidity('กรอกอีเมลที่มีรูปแบบถูกต้อง')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inputCoordinatorJobPosition">ตำแหน่งงาน:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-tag"></i>
                                                    </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputCoordinatorJobPosition" name="coordinatorJobPosition">
                                                </div>
                                            </div>
                                        </div>

                                        <!--ชื่อหน่วยงาน ผู้ประสานงาน-->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="inputCoordinatorOrganizationName">ชื่อหน่วยงาน:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-font"></i>
                                                    </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputCoordinatorOrganizationName" name="coordinatorOrganizationName">
                                                </div>
                                            </div>
                                        </div>

                                        <!--ประเภทหน่วยงาน ผู้ประสานงาน-->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="selectCoordinatorOrganizationType">ประเภทหน่วยงาน:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-navicon"></i>
                                                    </span>
                                                    <select id="selectCoordinatorOrganizationType" class="form-control"
                                                            name="coordinatorOrganizationType" required
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
                                                <label for="inputCoordinatorOrganizationTypeCustom">ประเภทหน่วยงาน (กรอกเอง):</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-pencil"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputCoordinatorOrganizationTypeCustom" name="coordinatorOrganizationTypeCustom">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->

                                    <div class="tab-pane" id="tabReceipt">
                                        <!--ชื่อสำหรับออกใบเสร็จ-->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="inputReceiptName">ชื่อสำหรับออกใบเสร็จ:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-font"></i>
                                                            </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputReceiptName" name="receiptName"
                                                           oninvalid="this.setCustomValidity('กรอกชื่อสำหรับออกใบเสร็จ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--ที่อยู่-->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="inputReceiptAddress">ที่อยู่:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputReceiptAddress" name="receiptAddress"
                                                           oninvalid="this.setCustomValidity('กรอกที่อยู่')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--แขวง, เขต-->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptSubDistrict">แขวง/ตำบล:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputReceiptSubDistrict" name="receiptSubDistrict"
                                                           oninvalid="this.setCustomValidity('กรอกแขวง/ตำบล')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptDistrict">เขต/อำเภอ:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputReceiptDistrict" name="receiptDistrict"
                                                           oninvalid="this.setCustomValidity('กรอกเขต/อำเภอ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--จังหวัด, รหัสไปรษณีย์-->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptProvince">จังหวัด:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputReceiptProvince" name="receiptProvince"
                                                           oninvalid="this.setCustomValidity('กรอกจังหวัด')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptPostalCode">รหัสไปรษณีย์:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control" required
                                                           maxlength="5"
                                                           id="inputReceiptPostalCode" name="receiptPostalCode"
                                                           oninvalid="this.setCustomValidity('กรอกรหัสไปรษณีย์')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--เบอร์โทรหน่วยงาน, เลขภาษี-->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptOrganizationPhone">เบอร์โทรหน่วยงาน:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-phone-square"></i>
                                                            </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputReceiptOrganizationPhone"
                                                           name="receiptOrganizationPhone"
                                                           oninvalid="this.setCustomValidity('กรอกเบอร์โทรหน่วยงาน')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptTaxId">เลขประจำตัวผู้เสียภาษี:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-slack"></i>
                                                            </span>
                                                    <input type="text" class="form-control" required
                                                           maxlength="13"
                                                           id="inputReceiptTaxId" name="receiptTaxId"
                                                           oninvalid="this.setCustomValidity('กรอกเลขประจำตัวผู้เสียภาษี')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div>
                            <!-- nav-tabs-custom -->

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
                                if ($responsibleUserId === (int)$_SESSION[KEY_SESSION_USER_ID]
                                    || ($serviceType === SERVICE_TYPE_TRAINING && currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_REGISTRATION))) {
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

    <!-- Doc social modal -->
    <div class="modal fade" id="editDocSocialModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal">&times;
                    </button>-->
                    <h4 class="modal-title">
                        ข้อมูลใบสมัครเลขที่ <span id="spanFormNumber"></span>
                        <div class="pull-right">
                            <small>กรอกใบสมัครเมื่อ <span id="spanCreatedAt"></span></small>
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

                    <form id="formEditDocSocial" role="form"
                          action="../api/api.php/update_trainee_social"
                          method="post"
                          autocomplete="off"
                          style="margin-top: 0; margin-bottom: 0">

                        <div class="box-body">
                            <input type="hidden" id="inputId" name="id">

                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tabTraineeSocial" data-toggle="tab">ข้อมูลผู้สมัคร</a></li>
                                    <li id="tabHeadReceipt"><a href="#tabReceiptSocial" data-toggle="tab">ข้อมูลการออกใบเสร็จ</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tabTraineeSocial">
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
                                            <div class="form-group col-md-6">
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
                                            <div class="form-group col-md-6">
                                                <label for="inputEmail">อีเมล:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope-o"></i>
                                                    </span>
                                                    <input type="email" class="form-control" required
                                                           id="inputEmail" name="email"
                                                           oninvalid="this.setCustomValidity('กรอกอีเมลที่มีรูปแบบถูกต้อง')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--อาชีพ สถานที่ทำงาน-->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="inputOccupation">อาชีพ:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-tag"></i>
                                                    </span>
                                                    <input type="text" class="form-control" required
                                                           id="inputOccupation" name="occupation">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputWorkPlace">สถานที่ทำงาน:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-home"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputWorkPlace" name="workPlace">
                                                </div>
                                            </div>
                                        </div>

                                        <label style="display: block; text-align: center; margin-top: 5px">กรณีฉุกเฉิน</label>
                                        <div style="padding: 15px 15px 5px 15px; border: 0px solid grey; background: #FFD9E0">
                                            <!--โรคประจำตัว-->
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="inputDisease">โรคประจำตัว:</label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-frown-o"></i>
                                                    </span>
                                                        <input type="text" class="form-control" required
                                                               id="inputDisease" name="disease">
                                                    </div>
                                                </div>
                                            </div>

                                            <!--ชื่อและเบอร์โทรผู้ติดต่อ กรณีฉุกเฉิน-->
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="inputContactName">ชื่อผู้ติดต่อกรณีฉุกเฉิน:</label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user-circle"></i>
                                                    </span>
                                                        <input type="text" class="form-control" required
                                                               id="inputContactName" name="contactName">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputContactPhone">เบอร์โทรผู้ติดต่อกรณีฉุกเฉิน:</label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-phone-square"></i>
                                                    </span>
                                                        <input type="text" class="form-control" required
                                                               id="inputContactPhone" name="contactPhone">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <label style="display: block; text-align: center; margin-top: 15px">ที่อยู่</label>
                                        <div style="padding: 15px 15px 5px 15px; border: 0px solid grey; background: #f4f4f4">
                                            <!--ที่อยู่-->
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="inputAddress">ที่อยู่:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                        <input type="text" class="form-control"
                                                               id="inputAddress" name="address"
                                                               oninvalid="this.setCustomValidity('กรอกที่อยู่')"
                                                               oninput="this.setCustomValidity('')">
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
                                                               id="inputSubDistrict" name="subDistrict"
                                                               oninvalid="this.setCustomValidity('กรอกแขวง/ตำบล')"
                                                               oninput="this.setCustomValidity('')">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputDistrict">เขต/อำเภอ:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                        <input type="text" class="form-control"
                                                               id="inputDistrict" name="district"
                                                               oninvalid="this.setCustomValidity('กรอกเขต/อำเภอ')"
                                                               oninput="this.setCustomValidity('')">
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
                                                               id="inputProvince" name="province"
                                                               oninvalid="this.setCustomValidity('กรอกจังหวัด')"
                                                               oninput="this.setCustomValidity('')">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="inputPostalCode">รหัสไปรษณีย์:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                        <input type="text" class="form-control"
                                                               maxlength="5"
                                                               id="inputPostalCode" name="postalCode"
                                                               oninvalid="this.setCustomValidity('กรอกรหัสไปรษณีย์')"
                                                               oninput="this.setCustomValidity('')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->

                                    <div class="tab-pane" id="tabReceiptSocial">
                                        <!--ชื่อสำหรับออกใบเสร็จ-->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="inputReceiptName">ชื่อสำหรับออกใบเสร็จ:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-font"></i>
                                                            </span>
                                                    <input type="text" class="form-control"
                                                           id="inputReceiptName" name="receiptName"
                                                           oninvalid="this.setCustomValidity('กรอกชื่อสำหรับออกใบเสร็จ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--ที่อยู่-->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="inputReceiptAddress">ที่อยู่:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control"
                                                           id="inputReceiptAddress" name="receiptAddress"
                                                           oninvalid="this.setCustomValidity('กรอกที่อยู่')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--แขวง, เขต-->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptSubDistrict">แขวง/ตำบล:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control"
                                                           id="inputReceiptSubDistrict" name="receiptSubDistrict"
                                                           oninvalid="this.setCustomValidity('กรอกแขวง/ตำบล')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptDistrict">เขต/อำเภอ:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control"
                                                           id="inputReceiptDistrict" name="receiptDistrict"
                                                           oninvalid="this.setCustomValidity('กรอกเขต/อำเภอ')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>

                                        <!--จังหวัด, รหัสไปรษณีย์-->
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptProvince">จังหวัด:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control"
                                                           id="inputReceiptProvince" name="receiptProvince"
                                                           oninvalid="this.setCustomValidity('กรอกจังหวัด')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="inputReceiptPostalCode">รหัสไปรษณีย์:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                    <input type="text" class="form-control"
                                                           maxlength="5"
                                                           id="inputReceiptPostalCode" name="receiptPostalCode"
                                                           oninvalid="this.setCustomValidity('กรอกรหัสไปรษณีย์')"
                                                           oninput="this.setCustomValidity('')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div>
                            <!-- nav-tabs-custom -->

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
                                if ($responsibleUserId === (int)$_SESSION[KEY_SESSION_USER_ID]
                                    || ($serviceType === SERVICE_TYPE_SOCIAL && currentUserHasPermission(PERMISSION_COURSE_SOCIAL_MANAGE_REGISTRATION))) {
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

    <!-- Print receipt modal -->
    <div class="modal fade" id="printReceiptModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;
                    </button>
                    <h4 class="modal-title">พิมพ์ใบเสร็จรับเงิน: ใบสมัครเลขที่ <span id="spanFormNumber"></span></h4>
                </div>
                <div class="modal-body">
                    <div id="spanLoading" style="text-align: center">
                        <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                    </div>
                    <div id="alertReceiptSuccess" class="alert alert-success alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertReceiptSuccess').hide()">&times;</button>
                        <i class="icon fa fa-check"></i><span id="alertReceiptSuccessText"></span>
                    </div>
                    <div id="alertReceiptError" class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertReceiptError').hide()">&times;</button>
                        <i class="icon fa fa-warning"></i><span id="alertReceiptErrorText"></span>
                    </div>

                    <form id="formPrintReceipt" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <div class="box-body">
                            <input type="hidden" id="inputTraineeId" name="trainee_id">

                            <div id="alertCanNotPrint" class="alert alert-danger alert-dismissible">
                                <!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>-->
                                <i class="icon fa fa-warning"></i>ไม่สามารถพิมพ์ใบเสร็จรับเงินของใบสมัครนี้ได้ เนื่องจากสถานะการชำระเงินยังไม่สมบูรณ์
                            </div>

                            <!--<div class="callout callout-danger" id="divCallout">
                                <p>ไม่สามารถพิมพ์ใบเสร็จรับเงินของใบสมัครนี้ได้ เนื่องจากสถานะการชำระเงินยังไม่สมบูรณ์</p>
                            </div>-->

                            <!--ชื่อ-นามสกุลผู้สมัคร-->
                            <div class="form-group">
                                <label for="inputTraineeName">ผู้สมัคร:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" disabled
                                           id="inputTraineeName">
                                </div>
                            </div>
                            <!--หลักสูตรที่สมัคร-->
                            <div class="form-group">
                                <label for="inputCourseName">หลักสูตรที่สมัคร:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-font"></i>
                                    </span>
                                    <input type="text" class="form-control" disabled
                                           id="inputCourseName">
                                </div>
                            </div>
                            <!--ราคาเต็ม-->
                            <div class="form-group">
                                <label for="inputCourseFee">ราคาเต็ม (บาท):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <strong>฿</strong>
                                    </span>
                                    <input type="text" class="form-control" disabled
                                           id="inputCourseFee"
                                           placeholder="ราคาเต็มของหลักสูตรนี้">
                                </div>
                            </div>
                            <!--ส่วนลด-->
                            <div class="form-group" style="display: none">
                                <label for="inputDiscount">ส่วนลด (บาท):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <strong>฿</strong>
                                    </span>
                                    <input type="text" class="form-control" disabled
                                           id="inputDiscount"
                                           placeholder="ส่วนลด">
                                </div>
                            </div>
                            <!--ราคาที่ลูกค้าจ่ายจริง-->
                            <div class="form-group">
                                <label for="inputPaidAmount">ราคาที่ลูกค้าจ่ายจริง (บาท):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <strong>฿</strong>
                                    </span>
                                    <input type="text" class="form-control" disabled
                                           id="inputPaidAmount" name="paid"
                                           placeholder="ไม่มีข้อมูล">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputReceiptNumber">เลขที่ใบเสร็จ:</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-sticky-note-o"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputReceiptNumber" name="receiptNumber"
                                           placeholder="กรอกเลขที่ใบเสร็จ" required
                                           oninvalid="this.setCustomValidity('กรอกเลขที่ใบเสร็จ')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                            <div id="responseText"
                                 style="text-align: center; color: red; margin-top: 25px; margin-bottom: 20px;">
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <?php
                            if ($responsibleUserId === (int)$_SESSION[KEY_SESSION_USER_ID]
                                || ($serviceType === SERVICE_TYPE_TRAINING && currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_REGISTRATION))
                                || ($serviceType === SERVICE_TYPE_SOCIAL && currentUserHasPermission(PERMISSION_COURSE_SOCIAL_MANAGE_REGISTRATION))
                                || ($serviceType === SERVICE_TYPE_DRIVING_LICENSE && currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION))) {
                                ?>
                                <button id="buttonPrintReceipt" type="submit"
                                        class="btn btn-info pull-right">
                                    <span class="fa fa-print"></span>&nbsp;
                                    พิมพ์
                                </button>
                                <?php
                            } else {
                                ?>
                                <button id="buttonCantPrintReceipt" type="button"
                                        class="btn btn-danger pull-right">
                                    <span class="fa fa-print"></span>&nbsp;
                                    คุณไม่มีสิทธิ์สำหรับการพิมพ์ใบเสร็จ
                                </button>
                                <?php
                            }
                            ?>
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

    <div class="modal fade" id="confirmPaymentModal" role="dialog" style="z-index: 1080 !important;">
        <div class="modal-dialog modal-md">
            <form id="formConfirmPayment" role="form" autocomplete="off"
                  style="margin-top: 20px; margin-bottom: 0">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">
                            ยอดเงินและวันที่โอนเงินจริง
                        </h4>
                    </div>

                    <div class="modal-body">
                        <div style="color: orangered; text-align: center; margin-top: 5px">
                            <i class="fa fa-pencil"></i> กรอกข้อมูลจริงตามการตรวจสอบ
                        </div>
                        <div style="color: orangered; text-align: center; margin-top: 10px">
                            ข้อมูลที่แสดงเริ่มต้น (ถ้ามี) คือข้อมูลตามที่ลูกค้ากรอกตอนแจ้งโอนเงิน<br>ซึ่งอาจไม่ใช่ข้อมูลที่ถูกต้อง
                        </div>

                        <div class="form-group col-md-12">
                            <input type="hidden" class="form-control"
                                   id="inputTraineeId" disabled
                                   oninvalid="this.setCustomValidity('Trainee ID')"
                                   oninput="this.setCustomValidity('')">
                        </div>

                        <div class="row">
                            <!--ยอดเงินจ่ายจริง-->
                            <div class="form-group col-md-6">
                                <label for="inputAmountConfirm">ยอดเงินที่ลูกค้าจ่ายจริง (บาท):</label>
                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <strong>฿</strong>
                                                            </span>
                                    <input type="number" class="form-control"
                                           id="inputAmountConfirm"
                                           placeholder="กรอกยอดเงินที่ลูกค้าจ่ายจริง" required
                                           oninvalid="this.setCustomValidity('กรอกยอดเงินที่ลูกค้าจ่ายจริง')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                            <!--วันที่โอนเงิน-->
                            <div class="form-group col-md-6">
                                <label for="inputPaymentDateConfirm">วันที่ลูกค้าโอนเงิน:</label>
                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar-check-o"></i>
                                                            </span>
                                    <input type="text" class="form-control"
                                           id="inputPaymentDateConfirm"
                                           placeholder="ระบุวันที่ลูกค้าโอนเงิน" required
                                           oninvalid="this.setCustomValidity('ระบุวันที่ลูกค้าโอนเงิน')"
                                           oninput="this.setCustomValidity('')">
                                    <span class="input-group-btn">
                                    <!--<button type="button" class="btn btn-info btn-flat">
                                        <i class="fa fa-save"></i>&nbsp;&nbsp;บันทึก
                                    </button>-->
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info">OK</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Manage register status modal -->
    <div class="modal fade" id="manageRegisterStatusModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal">&times;
                    </button>-->
                    <h4 class="modal-title">
                        จัดการสถานะการลงทะเบียน: ใบสมัครเลขที่ <span id="spanFormNumber"></span>

                        <!--สถานะการลงทะเบียน สำหรับคอร์สเสียเงิน-->
                        <div class="btn-group pull-right" id="buttonStatusForPaidCourse">
                            <button id="buttonStatus" type="button" class="btn btn-default">
                                <span id="spanCurrentStatus" style="color: white">&nbsp;</span>
                            </button>
                            <button id="buttonStatusDropDown" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret" style="color: white"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <?php
                            if ($responsibleUserId === (int)$_SESSION[KEY_SESSION_USER_ID]
                                || ($serviceType === SERVICE_TYPE_TRAINING && currentUserHasPermission(PERMISSION_COURSE_TRAINING_MANAGE_REGISTRATION))
                                || ($serviceType === SERVICE_TYPE_SOCIAL && currentUserHasPermission(PERMISSION_COURSE_SOCIAL_MANAGE_REGISTRATION))
                                || ($serviceType === SERVICE_TYPE_DRIVING_LICENSE && currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION))) {
                                ?>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="javascript:void(0)" onClick="updateRegisterStatus(this, 'start')">ยังไม่ได้ชำระเงิน</a></li>
                                    <li><a href="javascript:void(0)" onClick="updateRegisterStatus(this, 'wait-approve')">รอตรวจสอบ</a></li>
                                    <li><a href="javascript:void(0)" onClick="updateRegisterStatus(this, 'complete')">สมบูรณ์</a></li>
                                    <li class="divider"></li>
                                    <li><a href="javascript:void(0)" onClick="updateRegisterStatus(this, 'cancel')">ยกเลิกใบสมัคร</a></li>
                                </ul>
                                <?php
                            } else {
                                ?>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="javascript:void(0)">คุณไม่มีสิทธิ์แก้ไขสถานะใบสมัคร</a></li>
                                </ul>
                                <?php
                            }
                            ?>
                        </div>
                        <!--สถานะการลงทะเบียน สำหรับคอร์สฟรี-->
                        <div class="btn-group pull-right" id="buttonStatusForFreeCourse">
                            <button id="buttonStatus" type="button" class="btn btn-default">
                                <span id="spanCurrentStatus" style="color: white">&nbsp;</span>
                            </button>
                            <button id="buttonStatusDropDown" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret" style="color: white"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:void(0)" onClick="updateRegisterStatus(this, 'start')">สมัคร</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0)" onClick="updateRegisterStatus(this, 'cancel')">ยกเลิกใบสมัคร</a></li>
                            </ul>
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

                    <form id="formManageRegisterStatus" role="form"
                          style="margin-top: 0; margin-bottom: 0">
                        <!-- Custom Tabs -->
                        <div class="nav-tabs-custom" style="margin-bottom: 0">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_info" data-toggle="tab">ข้อมูลใบสมัคร</a></li>
                                <li id="tabPaymentNotification"><a href="#tab_noti" data-toggle="tab">การแจ้งโอนเงิน</a></li>
                            </ul>
                            <div class="tab-content">

                                <!--แท็บ: ข้อมูลใบสมัคร-->
                                <div class="tab-pane active" id="tab_info">
                                    <div class="box-body">
                                        <input type="hidden" id="inputTraineeId">
                                        <input type="hidden" id="inputCoursePaidType">
                                        <!--<input type="hidden" id="inputRegisterStatus">-->

                                        <!--ชื่อ-นามสกุล-เบอร์โทร-อีเมล ผู้สมัคร-->
                                        <div class="form-group">
                                            <label for="inputTraineeName">ผู้สมัคร:</label>
                                            <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user"></i>
                                                    </span>
                                                <input type="text" class="form-control" disabled
                                                       id="inputTraineeName">
                                            </div>
                                        </div>
                                        <!--ชื่อ-นามสกุล-เบอร์โทร-อีเมล ผู้ประสานงาน-->
                                        <div class="form-group" id="divCoordinatorName">
                                            <label for="inputCoordinatorName">ผู้ประสานงาน:</label>
                                            <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user"></i>
                                                    </span>
                                                <input type="text" class="form-control" disabled
                                                       id="inputCoordinatorName">
                                            </div>
                                        </div>
                                        <?php
                                        if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) {
                                            ?>
                                            <!--หลักสูตรที่สมัคร-->
                                            <div class="form-group">
                                                <label for="inputCourseName">หลักสูตรที่สมัคร:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-font"></i>
                                                    </span>
                                                    <input type="text" class="form-control" disabled
                                                           id="inputCourseName">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <!--ราคาเต็ม-->
                                                <div class="form-group col-md-4">
                                                    <label for="inputCourseFee">ราคาปกติ (บาท):</label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <strong>฿</strong>
                                                    </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputCourseFee">
                                                    </div>
                                                </div>
                                                <!--จ่ายจริง-->
                                                <div class="form-group col-md-4">
                                                    <label for="inputPaidAmount">ยอดเงินที่ลูกค้าจ่ายจริง (บาท):</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <strong>฿</strong>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputPaidAmount">
                                                    </div>
                                                </div>
                                                <!--วันโอน-->
                                                <div class="form-group col-md-4">
                                                    <label for="inputPaymentDateConfirm">วันที่ลูกค้าโอนเงิน:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar-check-o"></i>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputPaymentDateConfirm">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="divMessage" style="padding-top: 0">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-8" style="color: orangered; margin-top: 0">ยอดเงินจ่ายจริงและวันที่โอนเงิน จะให้กรอกตอนเปลี่ยนสถานะการลงทะเบียนที่ช่องมุมบนขวาเป็น "สมบูรณ์"</div>
                                            </div>
                                            <?php
                                        } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
                                            ?>
                                            <!--ประเภทการอบรม-->
                                            <div class="form-group">
                                                <label for="inputCourseName">ประเภทการอบรม:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-font"></i>
                                                            </span>
                                                    <input type="text" class="form-control" disabled
                                                           id="inputCourseName">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <!--ราคา-->
                                                <div class="form-group col-md-4">
                                                    <label for="inputCourseFee">ราคา (บาท):</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <strong>฿</strong>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputCourseFee">
                                                    </div>
                                                </div>
                                                <!--จ่ายจริง-->
                                                <div class="form-group col-md-4">
                                                    <label for="inputPaidAmount">ยอดเงินที่ลูกค้าจ่ายจริง (บาท):</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <strong>฿</strong>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputPaidAmount">
                                                    </div>
                                                </div>
                                                <!--วันโอน-->
                                                <div class="form-group col-md-4">
                                                    <label for="inputPaymentDateConfirm">วันที่ลูกค้าโอนเงิน:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar-check-o"></i>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputPaymentDateConfirm">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" style="padding-top: 0">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-8" style="color: orangered; margin-top: 0">ยอดเงินจ่ายจริงและวันที่โอนเงิน จะให้กรอกตอนเปลี่ยนสถานะการลงทะเบียนที่ช่องมุมบนขวาเป็น "สมบูรณ์"</div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.tab-pane -->

                                <!--แท็บ: หลักฐานการโอนเงิน-->
                                <div class="tab-pane" id="tab_noti">
                                    <div class="box-body">
                                        <div id="paymentNotificationDetails">
                                            <div class="row">
                                                <!--ผู้แจ้งโอนเงิน (สมาชิกที่ login)-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputMemberName">แจ้งโดย:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-user"></i>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputMemberName">
                                                    </div>
                                                </div>
                                                <!--วัน/เวลาที่ส่งหลักฐาน-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputNotificationDateTime">วันเวลาที่ส่งหลักฐาน:
                                                        <span style="color: orangered; font-weight: normal; display: none">
                                                            (วันเวลาที่แจ้ง ไม่ใช่วันเวลาที่โอน)
                                                        </span>
                                                    </label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-clock-o"></i>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputNotificationDateTime">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!--เบอร์โทรของผู้แจ้งโอน-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputMemberPhone">เบอร์โทรผู้แจ้งโอน:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-phone"></i>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputMemberPhone">
                                                    </div>
                                                </div>
                                                <!--อีเมลของผู้แจ้งโอน-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputMemberEmail">อีเมลผู้แจ้งโอน:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope"></i>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputMemberEmail">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!--ยอดเงินที่แจ้งโอน-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputAmount">ยอดเงินที่แจ้งโอน <span style="color: orangered;"><small>(ตามที่ลูกค้ากรอกข้อมูล)</small></span>:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <strong>฿</strong>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputAmount">
                                                    </div>
                                                </div>
                                                <!--วันที่โอนเงิน-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputPaymentDate">วันที่โอนเงิน <span style="color: orangered;"><small>(ตามที่ลูกค้ากรอกข้อมูล)</small></span>:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </span>

                                                        <input type="text" class="form-control" disabled
                                                               id="inputPaymentDate">
                                                        <!--<span class="input-group-btn">
                                                            <button type="button" class="btn btn-info btn-flat"
                                                                    onClick="updatePaymentDate()">
                                                                <i class="fa fa-save"></i>&nbsp;&nbsp;บันทึก
                                                            </button>
                                                        </span>-->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" style="margin-top: 10px">
                                                <div class="col-md-12" style="text-align: center">
                                                    <!--<img id="imgPaymentSlip" style="height: 600px; width: 600px; object-fit: contain"/>-->

                                                    <p style="text-align: center">
                                                        <span style="color: orangered">เมื่อคลิก PDF จะแสดงในหน้าจอใหม่</span>
                                                    </p>
                                                    <?php
                                                    $thumbnailSize = 150;
                                                    ?>
                                                    <a href="#" data-lightbox="slipImage" data-title="">
                                                        <img id="imgPaymentSlip" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/>
                                                    </a>&nbsp;
                                                    <a href="#" data-lightbox="slipImage" data-title="">
                                                        <img id="imgPaymentSlip2" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/></a>&nbsp;
                                                    <a href="#" data-lightbox="slipImage" data-title="">
                                                        <img id="imgPaymentSlip3" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/></a>&nbsp;
                                                    <a href="#" data-lightbox="slipImage" data-title="">
                                                        <img id="imgPaymentSlip4" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/></a>&nbsp;
                                                    <a href="#" data-lightbox="slipImage" data-title="">
                                                        <img id="imgPaymentSlip5" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="spanGetPaymentNotificationLoading" style="text-align: center">
                                            <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                        </div>
                                        <div id="alertNoPaymentNotification" class="alert alert-warning alert-dismissible" style="margin-bottom: 5px">
                                            <i class="icon fa fa-warning"></i>ไม่พบข้อมูลการแจ้งโอนเงินสำหรับใบสมัครนี้
                                        </div>
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- nav-tabs-custom -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage doc status modal -->
    <div class="modal fade" id="manageDocStatusModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal">&times;
                    </button>-->
                    <h4 class="modal-title">
                        ข้อมูลและเอกสารผู้สมัคร: ใบสมัครเลขที่ <span id="spanFormNumber"></span>

                        <!--สถานะเอกสาร-->
                        <div class="btn-group pull-right">
                            <button id="buttonDocStatus" type="button" class="btn btn-default">
                                <span id="spanCurrentDocStatus" style="color: white">&nbsp;</span>
                            </button>
                            <button id="buttonDocStatusDropDown" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret" style="color: white"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <?php
                            if ($responsibleUserId === (int)$_SESSION[KEY_SESSION_USER_ID]
                                || ($serviceType === SERVICE_TYPE_DRIVING_LICENSE && currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION))) {
                                ?>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="javascript:void(0)" onClick="updateDocStatus(0)">รอตรวจสอบ</a></li>
                                    <li><a href="javascript:void(0)" onClick="updateDocStatus(1)">สมบูรณ์</a></li>
                                </ul>
                                <?php
                            }
                            ?>
                        </div>
                    </h4>
                </div>
                <div class="modal-body">

                    <div id="spanLoading" style="text-align: center">
                        <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                    </div>
                    <div id="alertDocSuccess" class="alert alert-success alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertDocSuccess').hide()">&times;</button>
                        <i class="icon fa fa-check"></i><span id="alertDocSuccessText"></span>
                    </div>
                    <div id="alertDocError" class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" aria-hidden="true" onClick="$('#alertDocError').hide()">&times;</button>
                        <i class="icon fa fa-warning"></i><span id="alertDocErrorText"></span>
                    </div>

                    <form id="formManageDocStatus" role="form"
                          action="../api/api.php/update_driving_license_registration"
                          method="post"
                          style="margin-top: 0; margin-bottom: 0">
                        <!-- Custom Tabs -->
                        <div class="nav-tabs-custom" style="margin-bottom: 0">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab_doc_info" data-toggle="tab">ข้อมูลใบสมัคร</a></li>
                                <li><a href="#tab_doc_pid" data-toggle="tab">สำเนาบัตร/เอกสาร</a></li>
                            </ul>
                            <div class="tab-content">

                                <!--แท็บ: ข้อมูลใบสมัคร-->
                                <div class="tab-pane active" id="tab_doc_info">
                                    <div class="box-body">
                                        <input type="hidden" id="inputTraineeId" name="traineeId">

                                        <!--คำนำหน้า-ชื่อ-นามสกุล-->
                                        <div class="row">
                                            <!--คำนำหน้าชื่อ-->
                                            <div class="form-group col-md-2">
                                                <label for="inputTraineeTitle">คำนำหน้าชื่อ:</label>
                                                <div class="input-group">
                                                    <!--<span class="input-group-addon">
                                                        <i class="fa fa-user"></i>
                                                    </span>-->
                                                    <input type="text" class="form-control"
                                                           id="inputTraineeTitle" name="traineeTitle">
                                                </div>
                                            </div>
                                            <!--<div class="form-group col-md-2">
                                                <label for="selectTraineeTitle">คำนำหน้าชื่อ:</label>
                                                <select class="form-control" id="selectTraineeTitle" name="traineeTitle">
                                                    <option value="" selected disabled>-- เลือกคำนำหน้า --</option>
                                                    <?php
                                            /*                                                    foreach ($nameTitleList as $nameTitle) {
                                                                                                    */ ?>
                                                        <option value="<?php /*echo $nameTitle['title']; */ ?>" <?php /*echo($trainee['title'] === $nameTitle['title'] ? 'checked' : ''); */ ?>><?php /*echo $nameTitle['title']; */ ?></option>
                                                        <?php
                                            /*                                                    }
                                                                                                */ ?>
                                                </select>
                                            </div>-->
                                            <!--ชื่อ-->
                                            <div class="form-group col-md-5">
                                                <label for="inputTraineeFirstName">ชื่อ:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputTraineeFirstName" name="traineeFirstName">
                                                </div>
                                            </div>
                                            <!--นามสกุล-->
                                            <div class="form-group col-md-5">
                                                <label for="inputTraineeLastName">นามสกุล:</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-user"></i>
                                                            </span>
                                                    <input type="text" class="form-control"
                                                           id="inputTraineeLastName" name="traineeLastName">
                                                </div>
                                            </div>
                                        </div>

                                        <!--เลขประจำตัวประชาชน-->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="inputTraineePid">เลขประจำตัวประชาชน:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-credit-card"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputTraineePid" name="traineePid">
                                                </div>
                                            </div>
                                        </div>

                                        <!--เบอร์โทร-->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="inputPhone">เบอร์โทร:</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-phone"></i>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           id="inputPhone" name="traineePhone">
                                                </div>
                                            </div>
                                        </div>

                                        <label style="display: block; text-align: center;">ที่อยู่ตามบัตรประชาชน</label>
                                        <div style="padding: 15px 15px 5px 15px; border: 0px solid grey; background: #f4f4f4">
                                            <!--บ้านเลขที่-หมู่-->
                                            <div class="row">
                                                <!--บ้านเลขที่-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputAddress">บ้านเลขที่:</label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope-o"></i>
                                                    </span>
                                                        <input type="text" class="form-control"
                                                               id="inputAddress" name="address">
                                                    </div>
                                                </div>
                                                <!--หมู่-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputMoo">หมู่:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope-o"></i>
                                                            </span>
                                                        <input type="text" class="form-control"
                                                               id="inputMoo" name="moo">
                                                    </div>
                                                </div>
                                            </div>

                                            <!--ซอย-ถนน-->
                                            <div class="row">
                                                <!--ซอย-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputSoi">ซอย:</label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope-o"></i>
                                                    </span>
                                                        <input type="text" class="form-control"
                                                               id="inputSoi" name="soi">
                                                    </div>
                                                </div>
                                                <!--ถนน-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputRoad">ถนน:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope-o"></i>
                                                            </span>
                                                        <input type="text" class="form-control"
                                                               id="inputRoad" name="road">
                                                    </div>
                                                </div>
                                            </div>

                                            <!--แขวง-เขต-->
                                            <div class="row">
                                                <!--แขวง-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputSubDistrict">แขวง/ตำบล:</label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope-o"></i>
                                                    </span>
                                                        <input type="text" class="form-control"
                                                               id="inputSubDistrict" name="subDistrict">
                                                    </div>
                                                </div>
                                                <!--เขต-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputDistrict">เขต/อำเภอ:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-envelope-o"></i>
                                                            </span>
                                                        <input type="text" class="form-control"
                                                               id="inputDistrict" name="district">
                                                    </div>
                                                </div>
                                            </div>

                                            <!--จังหวัด-->
                                            <div class="row">
                                                <!--จังหวัด-->
                                                <div class="form-group col-md-6">
                                                    <label for="inputProvince">จังหวัด:</label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-envelope-o"></i>
                                                    </span>
                                                        <input type="text" class="form-control"
                                                               id="inputProvince" name="province">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/ที่อยู่ตามบัตรประชาชน-->

                                        <!--ประเภทหลักสูตร-->
                                        <div style="padding: 5px 15px 5px 15px; margin-top: 15px; border: 1px solid #eee;">
                                            <?php
                                            foreach ($drivingLicenseCourseTypeList as $courseType) {
                                                ?>
                                                <div class="radio">
                                                    <label><input type="radio" name="courseType" value="<?php echo $courseType['id']; ?>">
                                                        <?php echo $courseType['title']; ?>
                                                    </label>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <!--ประเภทใบอนุญาต-->
                                        <div style="padding: 10px 15px 10px 15px; margin-top: 15px; border: 1px solid #eee;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" id="checkBoxLicenseTypeCar" name="licenseTypeCar" value="1">
                                                        <!--<i class="fa fa-car"></i>-->รถยนต์ส่วนบุคคล<span class="spanTempWord">ชั่วคราว</span>
                                                    </label>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" id="checkBoxLicenseTypeBicycle" name="licenseTypeBicycle" value="1">
                                                        <!--<i class="fa fa-motorcycle"></i>-->รถจักรยานยนต์ส่วนบุคคล<span class="spanTempWord">ชั่วคราว</span>
                                                    </label>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" id="checkBoxLicenseTypeTricycle" name="licenseTypeTricycle" value="1">
                                                        <!--<img width="20px" src="../images/ic_tuktuk.png">-->รถสามล้อส่วนบุคคล<span class="spanTempWord">ชั่วคราว</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="spanLoading" style="text-align: center; margin-top: 15px">
                                            <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                        </div>
                                        <div id="alertDocSuccess" class="alert alert-success alert-dismissible" style="margin-top: 15px">
                                            <button type="button" class="close" aria-hidden="true" onClick="$('#alertDocSuccess').hide()">&times;</button>
                                            <i class="icon fa fa-check"></i><span id="alertDocSuccessText"></span>
                                        </div>
                                        <div id="alertDocError" class="alert alert-danger alert-dismissible" style="margin-top: 15px">
                                            <button type="button" class="close" aria-hidden="true" onClick="$('#alertDocError').hide()">&times;</button>
                                            <i class="icon fa fa-warning"></i><span id="alertDocErrorText"></span>
                                        </div>

                                        <div style="margin-top: 15px; text-align: center">
                                            <?php
                                            if ($responsibleUserId === (int)$_SESSION[KEY_SESSION_USER_ID]
                                                || ($serviceType === SERVICE_TYPE_DRIVING_LICENSE && currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION))) {
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
                                </div>
                                <!-- /.tab-pane -->

                                <!--แท็บ: สำเนาบัตร/เอกสาร-->
                                <div class="tab-pane" id="tab_doc_pid">
                                    <div class="box-body">
                                        <div id="docDetails" class="row">
                                            <div class="col" style="text-align: left">
                                                <p style="text-align: center">
                                                    <span style="color: orangered">เมื่อคลิก PDF จะแสดงในหน้าจอใหม่</span>
                                                </p>
                                                <?php
                                                $thumbnailSize = 150;
                                                ?>
                                                <a href="#" data-lightbox="pidImage" data-title="">
                                                    <img id="imgPid" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/>
                                                </a>&nbsp;
                                                <a href="#" data-lightbox="pidImage" data-title="">
                                                    <img id="imgPid2" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/></a>&nbsp;
                                                <a href="#" data-lightbox="pidImage" data-title="">
                                                    <img id="imgPid3" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/></a>&nbsp;
                                                <a href="#" data-lightbox="pidImage" data-title="">
                                                    <img id="imgPid4" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/></a>&nbsp;
                                                <a href="#" data-lightbox="pidImage" data-title="">
                                                    <img id="imgPid5" style="height: <?= $thumbnailSize; ?>px; width: <?= $thumbnailSize; ?>px; object-fit: contain"/></a>
                                            </div>
                                        </div>
                                        <div id="alertNoDoc" class="alert alert-warning alert-dismissible" style="margin-bottom: 5px">
                                            <i class="icon fa fa-warning"></i>ไม่พบภาพสำเนาบัตรประชาชน/พาสปอร์ต/เอกสาร สำหรับใบสมัครนี้
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.tab-pane -->

                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- nav-tabs-custom -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Record result modal -->
    <div class="modal fade" id="recordResultModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal">&times;
                    </button>-->
                    <h4 class="modal-title">
                        บันทึกผลการอบรม: ใบสมัครเลขที่ <span id="spanFormNumber"></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="formRecordResult" role="form"
                          action="../api/api.php/save_driving_license_course_result"
                          method="post"
                          style="margin-top: 0; margin-bottom: 0">

                        <div>
                            <div class="box-body">
                                <input type="hidden" id="inputTraineeId" name="traineeId">

                                <!--คำนำหน้า-ชื่อ-นามสกุล-->
                                <div class="row">
                                    <!--คำนำหน้าชื่อ-->
                                    <div class="form-group col-md-2">
                                        <label for="inputTraineeTitle">คำนำหน้าชื่อ:</label>
                                        <div class="input-group">
                                            <!--<span class="input-group-addon">
                                                <i class="fa fa-user"></i>
                                            </span>-->
                                            <input type="text" class="form-control"
                                                   id="inputTraineeTitle" readonly>
                                        </div>
                                    </div>
                                    <!--<div class="form-group col-md-2">
                                        <label for="selectTraineeTitle">คำนำหน้าชื่อ:</label>
                                        <select class="form-control" id="selectTraineeTitle" readonly>
                                            <option value="" selected disabled>-- เลือกคำนำหน้า --</option>
                                            <?php
                                    /*                                            foreach ($nameTitleList as $nameTitle) {
                                                                                    */ ?>
                                                <option value="<?php /*echo $nameTitle['title']; */ ?>" disabled><?php /*echo $nameTitle['title']; */ ?></option>
                                                <?php
                                    /*                                            }
                                                                                */ ?>
                                        </select>
                                    </div>-->
                                    <!--ชื่อ-->
                                    <div class="form-group col-md-5">
                                        <label for="inputTraineeFirstName">ชื่อ:</label>
                                        <div class="input-group">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-user"></i>
                                                    </span>
                                            <input type="text" class="form-control"
                                                   id="inputTraineeFirstName" readonly>
                                        </div>
                                    </div>
                                    <!--นามสกุล-->
                                    <div class="form-group col-md-5">
                                        <label for="inputTraineeLastName">นามสกุล:</label>
                                        <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-user"></i>
                                                            </span>
                                            <input type="text" class="form-control"
                                                   id="inputTraineeLastName" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!--ประเภทหลักสูตร-->
                                <div style="padding: 5px 15px 5px 15px; margin-top: 15px; border: 1px solid #eee;">
                                    <?php
                                    foreach ($drivingLicenseCourseTypeList as $courseType) {
                                        ?>
                                        <div class="radio">
                                            <label><input type="radio" name="courseType" value="<?php echo $courseType['id']; ?>" disabled>
                                                <?php echo $courseType['title']; ?>
                                            </label>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <!--ประเภทใบอนุญาต-->
                                <div style="padding: 10px 15px 10px 15px; margin-top: 15px; border: 1px solid #eee;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" id="checkBoxLicenseTypeCar" name="licenseTypeCar" disabled>
                                                <!--<i class="fa fa-car"></i>-->รถยนต์ส่วนบุคคล<span class="spanTempWord">ชั่วคราว</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" id="checkBoxLicenseTypeBicycle" name="licenseTypeBicycle" disabled>
                                                <!--<i class="fa fa-motorcycle"></i>-->รถจักรยานยนต์ส่วนบุคคล<span class="spanTempWord">ชั่วคราว</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" id="checkBoxLicenseTypeTricycle" name="licenseTypeTricycle" disabled>
                                                <!--<img width="20px" src="../images/ic_tuktuk.png">-->รถสามล้อส่วนบุคคล<span class="spanTempWord">ชั่วคราว</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!--ผลการอบรม-->
                                <div style="margin-top: 25px;"><label>ผลการอบรม:</label></div>
                                <div style="padding: 0 15px 0 15px; border: 2px solid #375980;">
                                    <div id="rowSubject1" class="row" style="margin-bottom: 0px">
                                        <div class="col-sm-7">
                                            <div class="radio"><span id="spanSubject1Name"></span></div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="radio"><label><input type="radio" name="subject1Result" value="1"> ผ่าน</label></div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="radio"><label><input type="radio" name="subject1Result" value="0"> ไม่ผ่าน</label></div>
                                        </div>
                                    </div>
                                    <div id="rowSubject2" class="row" style="background: #f0f0f0; margin-bottom: 0px">
                                        <div class="col-sm-7">
                                            <div class="radio">การขับรถเชิงป้องกันอุบัติเหตุ</div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="radio"><label><input type="radio" name="subject2Result" value="1"> ผ่าน</label></div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="radio"><label><input type="radio" name="subject2Result" value="0"> ไม่ผ่าน</label></div>
                                        </div>
                                    </div>
                                    <div id="rowSubject3" class="row" style="margin-bottom: 0px">
                                        <div class="col-sm-7">
                                            <div class="radio">จิตสำนึกและมารยาทในการขับรถ</div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="radio"><label><input type="radio" name="subject3Result" value="1"> ผ่าน</label></div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="radio"><label><input type="radio" name="subject3Result" value="0"> ไม่ผ่าน</label></div>
                                        </div>
                                    </div>
                                    <div id="rowSubject4" class="row" style="background: #f0f0f0">
                                        <div class="col-sm-7">
                                            <div class="radio">ข้อปฏิบัติเมื่อเกิดเหตุฉุกเฉิน การให้ความช่วยเหลือและปฐมพยาบาล</div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="radio"><label><input type="radio" name="subject4Result" value="1"> ผ่าน</label></div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="radio"><label><input type="radio" name="subject4Result" value="0"> ไม่ผ่าน</label></div>
                                        </div>
                                    </div>
                                </div>

                                <div id="spanLoading" style="text-align: center; margin-top: 15px">
                                    <img src="../images/ic_loading4.gif" height="32px"/>&nbsp;รอสักครู่
                                </div>
                                <div id="alertRecordResultSuccess" class="alert alert-success alert-dismissible" style="margin-top: 15px">
                                    <button type="button" class="close" aria-hidden="true" onClick="$('#alertRecordResultSuccess').hide()">&times;</button>
                                    <i class="icon fa fa-check"></i><span id="alertRecordResultSuccessText"></span>
                                </div>
                                <div id="alertRecordResultError" class="alert alert-danger alert-dismissible" style="margin-top: 15px">
                                    <button type="button" class="close" aria-hidden="true" onClick="$('#alertRecordResultError').hide()">&times;</button>
                                    <i class="icon fa fa-warning"></i><span id="alertRecordResultErrorText"></span>
                                </div>

                                <div style="margin-top: 15px; text-align: center">
                                    <?php
                                    if ($responsibleUserId === (int)$_SESSION[KEY_SESSION_USER_ID]
                                        || ($serviceType === SERVICE_TYPE_DRIVING_LICENSE && currentUserHasPermission(PERMISSION_COURSE_DRIVING_LICENSE_MANAGE_REGISTRATION))) {
                                        ?>
                                        <button type="submit" class="btn btn-block btn-primary" style="display: inline; width: 160px;">
                                            <i class="fa fa-save"></i>&nbsp;&nbsp;บันทึกผลการอบรม
                                        </button>
                                        <?php
                                    } else {
                                        ?>
                                        <button type="button" class="btn btn-block btn-danger" style="display: inline; width: 240px;">
                                            <i class="fa fa-save"></i>&nbsp;&nbsp;คุณไม่มีสิทธิ์บันทึกผลการอบรม
                                        </button>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.tab-pane -->

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <!--Data table-->
    <table id="tableRegistration" class="table table-bordered table-striped">
        <thead>
        <tr>
            <?php
            if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) {
                if ($paramCourseId == NULL) {
                    ?>
                    <th style="width: 10%; text-align: center">เลขที่</th>
                    <th style="width: 20%; text-align: center">ผู้สมัคร</th>
                    <th style="width: 20%; text-align: center"><?= $serviceType === SERVICE_TYPE_TRAINING ? 'ผู้ประสานงาน' : 'ผู้ติดต่อกรณีฉุกเฉิน'; ?></th>
                    <th style="width: 27%; text-align: center">หลักสูตรที่สมัคร</th>
                    <th style="width: 10%; text-align: center">วันอบรม</th>
                    <th style="width: 13%; text-align: center">วัน/เวลาที่สมัคร</th>
                    <th style="text-align: center">สถานะ</th>
                    <th style="text-align: center" nowrap>พิมพ์</th>
                    <?php
                } else {
                    ?>
                    <th style="width: 20%; text-align: center">เลขที่</th>
                    <th style="width: 30%; text-align: center">ผู้สมัคร</th>
                    <th style="width: 30%; text-align: center"><?= $serviceType === SERVICE_TYPE_TRAINING ? 'ผู้ประสานงาน' : 'ผู้ติดต่อกรณีฉุกเฉิน'; ?></th>
                    <th style="width: 20%; text-align: center">วัน/เวลาที่สมัคร</th>
                    <th style="text-align: center">สถานะ</th>
                    <th style="text-align: center">ใบสมัคร</th>
                    <th style="text-align: center" nowrap>พิมพ์</th>
                    <?php
                }
            } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
                if ($paramCourseId == NULL) {
                    ?>
                    <th style="width: 15%; text-align: center">เลขที่</th>
                    <th style="width: 25%; text-align: center">ผู้สมัคร</th>
                    <th style="width: 30%; text-align: center">ประเภท/ราคา</th>
                    <th style="width: 10%; text-align: center">วันอบรม</th>
                    <th style="width: 20%; text-align: center">วัน/เวลาที่สมัคร</th>
                    <th style="text-align: center">สถานะ</th>
                    <th style="text-align: center" nowrap>พิมพ์</th>
                    <?php
                } else {
                    ?>
                    <th style="width: 15%; text-align: center">เลขที่</th>
                    <th style="width: 25%; text-align: center">ผู้สมัคร</th>
                    <th style="width: 35%; text-align: center">ประเภท/ราคา</th>
                    <th style="width: 20%; text-align: center">วัน/เวลาที่สมัคร</th>
                    <th style="text-align: center">สถานะ</th>
                    <th style="text-align: center">เอกสาร</th>
                    <th style="text-align: center">บันทึกผล</th>
                    <th style="text-align: center" nowrap>พิมพ์</th>
                    <?php
                }
            }
            ?>
        </tr>
        </thead>
        <tbody>
        <?php
        /*นับจำนวนแต่ละสถานะ เพื่อเอาไว้อัพเดท Widget ด้านบน (ในหน้า course_details.php)*/
        $registerStatusList = array(
            'start' => 0,
            'wait-approve' => 0,
            'complete' => 0,
            'cancel' => 0
        );

        if (sizeof($traineeList) == 0) {
            ?>
            <!--<tr valign="middle">
                <td colspan="20" align="center">ไม่มีข้อมูล</td>
            </tr>-->
            <?php
        } else {
            foreach ($traineeList as $trainee) {
                $traineeId = $trainee['id'];
                $formNumber = $trainee['form_number'];
                if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) {
                    $traineeDetails = sprintf(
                        '%s %s %s<br><i class="fa fa-phone" style="color: black"></i> %s<br><i class="fa fa-envelope-o" style="color: black"></i> <a href="mailto:%s">%s</a>',
                        $trainee['title'], $trainee['first_name'], $trainee['last_name'],
                        $trainee['phone'], $trainee['email'], $trainee['email']
                    );
                } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
                    $traineeDetails = sprintf(
                        '%s %s %s<br><i class="fa fa-phone" style="color: black"></i> %s',
                        $trainee['title'], $trainee['first_name'], $trainee['last_name'],
                        $trainee['phone']
                    );
                }
                $coordinatorDetails = sprintf(
                    '%s %s %s<br><i class="fa fa-phone" style="color: black"></i> %s<br><i class="fa fa-envelope-o" style="color: black"></i> <a href="mailto:%s">%s</a>',
                    $trainee['coordinator']['title'], $trainee['coordinator']['first_name'], $trainee['coordinator']['last_name'],
                    $trainee['coordinator']['phone'], $trainee['coordinator']['email'], $trainee['coordinator']['email']
                );
                $createdAt = $trainee['created_at'];
                $dateTimePart = explode(' ', $createdAt);
                $displayDate = getThaiShortDateWithDayName(date_create($dateTimePart[0]));
                $timePart = explode(':', $dateTimePart[1]);
                $displayTime = $timePart[0] . '.' . $timePart[1] . ' น.';
                $displayDateTime = "$displayDate<br>$displayTime";
                $dateHidden = '<span style="display: none">' . $createdAt . '</span></span>';

                $registerStatus = $trainee['register_status'];
                $registerStatusList[$registerStatus]++;

                $docStatus = $trainee['doc_status'];

                $subjectResults = array();
                $subjectResults[0] = $trainee['subject_1_result'];
                $subjectResults[1] = $trainee['subject_2_result'];
                $subjectResults[2] = $trainee['subject_3_result'];
                $subjectResults[3] = $trainee['subject_4_result'];
                $resultStatus = $trainee['result_status'];
                $certificateStatus = $trainee['certificate_status'];

                $paidAmount = $trainee['paid_amount'] == null ? '' : number_format($trainee['paid_amount']);
                $paymentDate = $trainee['payment_date'] == null ? '' : getThaiShortDateWithDayName(date_create($trainee['payment_date']));

                $courseId = $trainee['course_id'];
                $sql = "SELECT cm.title, c.batch_number, c.begin_date, c.end_date, c.application_fee 
                                                        FROM course_master cm 
                                                            INNER JOIN course c ON c.course_master_id = cm.id 
                                                        WHERE c.id = $courseId";
                if ($result = $db->query($sql)) {
                    if ($row = $result->fetch_assoc()) {
                        $courseDetails = sprintf(
                            '%s รุ่นที่ %d',
                            $row['title'], $row['batch_number']
                        );

                        // กรณีบริการใบขับขี่ จะกำหนด $courseApplicationFee เป็น -1
                        $courseApplicationFee = is_null($row['application_fee']) ? -1 : (int)$row['application_fee'];
                        if ($paramCourseId == null && $courseApplicationFee === 0) {
                            continue;
                        }

                        $beginDate = date_create($row['begin_date']);
                        $endDate = date_create($row['end_date']);
                        $courseDate = ($row['begin_date'] === $row['end_date'] ? getThaiShortDate($beginDate) : getThaiIntervalShortDate($beginDate, $endDate));
                        $courseDateHidden = '<span style="display: none">' . $row['begin_date'] . '</span></span>';
                    } else {
                        $courseDetails = '<span style="color: red">ไม่พบข้อมูล!</span>';
                        $courseDate = '';
                        $courseDateHidden = '';
                    }
                } else {
                    $courseDetails = '<span style="color: red">เกิดข้อผิดพลาดในการอ่านข้อมูล!</span>';
                    $courseDate = '';
                    $courseDateHidden = '';
                }
                ?>

                <tr>
                    <td style="vertical-align: top; text-align: center"><?php echo $formNumber; ?></td>
                    <td style="vertical-align: top"><?php echo $traineeDetails; ?></td>
                    <?php
                    if ($serviceType === SERVICE_TYPE_TRAINING) {
                        ?>
                        <td style="vertical-align: top"><?php echo($trainee['coordinator']['first_name'] == null ? '&nbsp;' : $coordinatorDetails); ?></td>
                        <?php
                        if ($paramCourseId == NULL) {
                            ?>
                            <td style="vertical-align: top">
                                <?php echo $courseDetails; ?><br/>
                                <div><strong>฿</strong> <?= number_format($courseApplicationFee); ?> บาท
                            </td>
                            <?php
                        }
                    } else if ($serviceType === SERVICE_TYPE_SOCIAL) {
                        ?>
                        <td style="vertical-align: top"><?php echo "{$trainee['contact_name']}<br/><i class=\"fa fa-phone\" style=\"color: black\"></i> {$trainee['contact_phone']}"; ?></td>
                        <?php
                        if ($paramCourseId == NULL) {
                            ?>
                            <td style="vertical-align: top">
                                <?php echo $courseDetails; ?>
                                <div><strong>฿</strong> <?= $courseApplicationFee === 0 ? 'ฟรี' : (number_format($courseApplicationFee) . ' บาท'); ?>
                            </td>
                            <?php
                        }
                    } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
                        ?>
                        <td style="vertical-align: top">
                            <?php
                            $licenseType = (int)$trainee['license_type'];
                            $licenseTypeTag = '';
                            $licenseTypeTag .= (($licenseType & 1) === 1) ? '<i class="icon fa fa-car"></i>&nbsp;' : '';
                            $licenseTypeTag .= (($licenseType & 2) === 2) ? '<i class="icon fa fa-motorcycle"></i>&nbsp;' : '';
                            $licenseTypeTag .= (($licenseType & 4) === 4) ? '<img width="20px" src="../images/ic_tuktuk.png">&nbsp;' : '';

                            echo sprintf(
                                '%s<br/><div><strong>฿</strong> %s บาท<div class="pull-right">%s</div></div>',
                                $trainee['driving_license_course_type'],
                                $trainee['driving_license_course_fee'],
                                $licenseTypeTag
                            );
                            ?>
                        </td>
                        <?php
                    }
                    ?>

                    <?php
                    if ($paramCourseId == NULL) {
                        ?>
                        <td style="vertical-align: top; text-align: center"><?php echo $courseDateHidden . $courseDate; ?></td>
                        <?php
                    }
                    ?>
                    <td style="vertical-align: top; text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>

                    <?php
                    if (TRUE /*$serviceType !== SERVICE_TYPE_SOCIAL*/) {
                        ?>
                        <td style="vertical-align: top; text-align: center" nowrap>
                            <?php
                            $btnClass = '';
                            $btnText = '';
                            $sortOrder = 0;
                            switch ($registerStatus) {
                                case 'start':
                                    $btnClass = 'btn-info';
                                    $btnText = $courseApplicationFee === 0 ? 'สมัคร' : 'ยังไม่ชำระเงิน';
                                    $sortOrder = 3;
                                    break;
                                case 'wait-approve':
                                    $btnClass = 'btn-warning';
                                    $btnText = 'รอตรวจสอบ';
                                    $sortOrder = 1;
                                    break;
                                case 'complete':
                                    $btnClass = 'btn-success';
                                    $btnText = 'สมบูรณ์';
                                    $sortOrder = 2;
                                    break;
                                case 'cancel':
                                    $btnClass = 'btn-danger';
                                    $btnText = 'ยกเลิก';
                                    $sortOrder = 4;
                                    break;
                            }
                            ?>
                            <span style="display: none"><?php echo $sortOrder; ?></span>

                            <button id="buttonStatus<?php echo $traineeId; ?>" type="button" class="btn-xs <?php echo $btnClass; ?>" style="width: 90px;"
                                    onClick="onClickStatus(
                                            '<?= $formNumber; ?>',
                                    <?= $traineeId; ?>,
                                            '<?= $trainee['title'] . ' ' . htmlentities($trainee['first_name']) . ' ' . htmlentities($trainee['last_name']) . "  •  {$trainee['phone']}  •  {$trainee['email']}"; ?>',
                                            '<?= ($trainee['coordinator']['first_name'] ? "{$trainee['coordinator']['title']} {$trainee['coordinator']['first_name']} {$trainee['coordinator']['last_name']}  •  {$trainee['coordinator']['phone']}  •  {$trainee['coordinator']['email']}" : ''); ?>',
                                            '<?= (($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) ? htmlentities($courseDetails) : htmlentities($trainee['driving_license_course_type'])); ?>',
                                            '<?= (($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) ? number_format((string)$courseApplicationFee) : number_format((string)$trainee['driving_license_course_fee'])); ?>',
                                            '<?= $paidAmount; ?>',
                                            '<?= $paymentDate; ?>',
                                            '<?= $trainee['pid_file_name']; ?>'
                                            )">
                                <?= $btnText; ?>
                            </button>

                            <?php
                            if ($paidAmount !== '') {
                                ?>
                                <div style="text-align: center; margin-top: 5px">
                                    <?= $paidAmount; ?>&nbsp;บาท
                                </div>
                                <?php
                            }
                            ?>

                            <input type="hidden" name="traineeId" value="<?php echo $traineeId; ?>"/>
                            <input id="inputRegisterStatus<?php echo $traineeId; ?>" type="hidden" value="<?php echo $registerStatus; ?>"/>
                        </td>

                        <?php
                        if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE && $paramCourseId != NULL) {
                            ?>
                            <td style="vertical-align: top; text-align: center" nowrap>
                                <button id="buttonDoc<?= $traineeId; ?>" type="button" class="btn-xs <?= ($docStatus === 1 ? 'btn-success' : 'btn-warning'); ?>"
                                        style="width: 90px;"
                                        onClick="onClickDoc(
                                                '<?= $formNumber; ?>',
                                        <?= $traineeId; ?>,
                                                '<?= $trainee['title']; ?>',
                                                '<?= htmlentities($trainee['first_name']); ?>',
                                                '<?= htmlentities($trainee['last_name']); ?>',
                                                '<?= $trainee['pid']; ?>',
                                                '<?= $trainee['address']; ?>',
                                                '<?= $trainee['moo']; ?>',
                                                '<?= $trainee['soi']; ?>',
                                                '<?= $trainee['road']; ?>',
                                                '<?= $trainee['sub_district']; ?>',
                                                '<?= $trainee['district']; ?>',
                                                '<?= $trainee['province']; ?>',
                                                '<?= $trainee['phone']; ?>',
                                        <?= $trainee['driving_license_course_type_id']; ?>,
                                        <?= $trainee['license_type']; ?>,
                                        <?= $trainee['doc_status']; ?>,
                                                '<?= $trainee['pid_file_name']; ?>',
                                                '<?= $trainee['pid_file_name_2']; ?>',
                                                '<?= $trainee['pid_file_name_3']; ?>',
                                                '<?= $trainee['pid_file_name_4']; ?>',
                                                '<?= $trainee['pid_file_name_5']; ?>'
                                                )">
                                    <?= ($docStatus === 1 ? 'สมบูรณ์' : 'รอตรวจสอบ'); ?>
                                </button>

                                <input id="inputDocStatus<?= $traineeId; ?>" type="hidden" value="<?= $docStatus; ?>"/>
                            </td>

                            <td style="vertical-align: top; text-align: center" nowrap>
                                <button id="buttonResult<?= $traineeId; ?>" type="button" class="btn-xs <?= ($resultStatus ? 'btn-success' : 'btn-warning'); ?>"
                                        style="width: 90px;"
                                        onclick="onClickResult(
                                                '<?= $formNumber; ?>',
                                        <?= $traineeId; ?>,
                                                '<?= $trainee['title']; ?>',
                                                '<?= htmlentities($trainee['first_name']); ?>',
                                                '<?= htmlentities($trainee['last_name']); ?>',
                                                '<?= $trainee['pid']; ?>',
                                                '<?= $trainee['phone']; ?>',
                                        <?= $trainee['driving_license_course_type_id']; ?>,
                                        <?= $trainee['license_type']; ?>,
                                                [
                                        <?= is_null($trainee['subject_1_result']) ? 'null' : $trainee['subject_1_result']; ?>,
                                        <?= is_null($trainee['subject_2_result']) ? 'null' : $trainee['subject_2_result']; ?>,
                                        <?= is_null($trainee['subject_3_result']) ? 'null' : $trainee['subject_3_result']; ?>,
                                        <?= is_null($trainee['subject_4_result']) ? 'null' : $trainee['subject_4_result']; ?>,
                                                ]
                                                )">
                                    <?php echo($resultStatus ? 'สมบูรณ์' : 'ไม่สมบูรณ์'); ?>
                                </button>

                                <div style="text-align: center; margin-top: 5px">
                                    <?php
                                    if ($certificateStatus) {
                                        ?>
                                        <i class="fa fa-check-square-o"></i>
                                        <?php
                                    }
                                    ?>
                                    <span><?= $trainee['certificate_number'] > 0 ? $trainee['certificate_number'] : ''; ?></span>
                                </div>

                                <input id="inputResultStatus<?= $traineeId; ?>" type="hidden" value="<?= ($resultStatus ? '1' : '0'); ?>"/>
                                <input id="inputCertificateStatus<?= $traineeId; ?>" type="hidden" value="<?= ($certificateStatus ? '1' : '0'); ?>"/>
                            </td>
                            <?php
                        } else if ($serviceType === SERVICE_TYPE_TRAINING && $paramCourseId != NULL) {
                            ?>
                            <td style="vertical-align: top; text-align: center" nowrap>
                                <button id="buttonDoc<?= $traineeId; ?>" type="button" class="btn-xs"
                                        style="width: 90px;"
                                        onClick="onClickDocTraining(
                                                '<?= $formNumber; ?>',
                                        <?= $traineeId; ?>,
                                        <?= $trainee['course_registration_id']; ?>,
                                                '<?= $trainee['title']; ?>',
                                                '<?= htmlentities($trainee['first_name']); ?>',
                                                '<?= htmlentities($trainee['last_name']); ?>',
                                                '<?= $trainee['phone']; ?>',
                                                '<?= $trainee['email']; ?>',
                                                '<?= htmlentities($trainee['job_position']); ?>',
                                                '<?= htmlentities($trainee['organization_name']); ?>',
                                        <?= $trainee['organization_type']; ?>,
                                                '<?= htmlentities($trainee['organization_type_custom']); ?>',
                                                '<?= $trainee['coordinator']['title']; ?>',
                                                '<?= htmlentities($trainee['coordinator']['first_name']); ?>',
                                                '<?= htmlentities($trainee['coordinator']['last_name']); ?>',
                                                '<?= $trainee['coordinator']['phone']; ?>',
                                                '<?= $trainee['coordinator']['email']; ?>',
                                                '<?= htmlentities($trainee['coordinator']['job_position']); ?>',
                                                '<?= htmlentities($trainee['coordinator']['organization_name']); ?>',
                                                '<?= $trainee['coordinator']['organization_type']; ?>',
                                                '<?= htmlentities($trainee['coordinator']['organization_type_custom']); ?>',

                                                '<?= htmlentities($trainee['receipt']['name']); ?>',
                                                '<?= htmlentities($trainee['receipt']['address']); ?>',
                                                '<?= htmlentities($trainee['receipt']['sub_district']); ?>',
                                                '<?= htmlentities($trainee['receipt']['district']); ?>',
                                                '<?= htmlentities($trainee['receipt']['province']); ?>',
                                                '<?= $trainee['receipt']['postal_code']; ?>',
                                                '<?= $trainee['receipt']['organization_phone']; ?>',
                                                '<?= $trainee['receipt']['tax_id']; ?>',

                                                '<?= $displayDate; ?>',
                                                '<?= $displayTime; ?>'
                                                )">
                                    ใบสมัคร
                                </button>

                                <input id="inputDocStatus<?= $traineeId; ?>" type="hidden" value="<?= $docStatus; ?>"/>
                            </td>
                            <?php
                        } else if ($serviceType === SERVICE_TYPE_SOCIAL && $paramCourseId != NULL) {
                            ?>
                            <!--*********************************todo***************************************-->
                            <td style="vertical-align: top; text-align: center" nowrap>
                                <button id="buttonDoc<?= $traineeId; ?>" type="button" class="btn-xs"
                                        style="width: 90px;"
                                        onClick="onClickDocSocial(
                                                '<?= $formNumber; ?>',
                                        <?= $traineeId; ?>,
                                        <?= $courseApplicationFee; ?>,
                                                '<?= $trainee['title']; ?>',
                                                '<?= htmlentities($trainee['first_name']); ?>',
                                                '<?= htmlentities($trainee['last_name']); ?>',
                                                '<?= $trainee['phone']; ?>',
                                                '<?= $trainee['email']; ?>',
                                                '<?= htmlentities($trainee['occupation']); ?>',
                                                '<?= htmlentities($trainee['work_place']); ?>',

                                                '<?= htmlentities($trainee['address']); ?>',
                                                '<?= htmlentities($trainee['sub_district']); ?>',
                                                '<?= htmlentities($trainee['district']); ?>',
                                                '<?= htmlentities($trainee['province']); ?>',
                                                '<?= $trainee['postal_code']; ?>',

                                                '<?= htmlentities($trainee['contact_name']); ?>',
                                                '<?= htmlentities($trainee['contact_phone']); ?>',
                                                '<?= htmlentities($trainee['disease']); ?>',

                                                '<?= htmlentities($trainee['receipt']['name']); ?>',
                                                '<?= htmlentities($trainee['receipt']['address']); ?>',
                                                '<?= htmlentities($trainee['receipt']['sub_district']); ?>',
                                                '<?= htmlentities($trainee['receipt']['district']); ?>',
                                                '<?= htmlentities($trainee['receipt']['province']); ?>',
                                                '<?= $trainee['receipt']['postal_code']; ?>',

                                                '<?= $displayDate; ?>',
                                                '<?= $displayTime; ?>'
                                                )">
                                    ใบสมัคร
                                </button>

                                <input id="inputDocStatus<?= $traineeId; ?>" type="hidden" value="<?= $docStatus; ?>"/>
                            </td>
                            <?php
                        }
                    }
                    ?>
                    <td style="vertical-align: top; text-align: center" nowrap>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <?php
                                if (($serviceType !== SERVICE_TYPE_SOCIAL) || $courseApplicationFee !== 0) {
                                    ?>
                                    <li><a href="javascript:void(0)"
                                           onClick="onClickPrintReceipt(
                                                   '<?= $formNumber; ?>',
                                           <?= $traineeId; ?>,
                                                   '<?= $trainee['title'] . ' ' . htmlentities($trainee['first_name']) . ' ' . htmlentities($trainee['last_name']); ?>',
                                                   '<?= $serviceType === SERVICE_TYPE_DRIVING_LICENSE ? htmlentities($trainee['driving_license_course_type']) : htmlentities($courseDetails); ?>',
                                           <?= $serviceType === SERVICE_TYPE_DRIVING_LICENSE ? $trainee['driving_license_course_fee'] : $courseApplicationFee; ?>,
                                                   '<?= $paidAmount; ?>',
                                                   '<?= $trainee['receipt_number']; ?>'
                                                   )">
                                            <i class="fa fa-print"></i>ใบเสร็จรับเงิน
                                        </a>
                                    </li>
                                    <?php
                                }
                                ?>

                                <?php
                                if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) {
                                    //$printRegFormFile = $serviceType === SERVICE_TYPE_TRAINING ? 'print_ac_registration_form.php' : 'print_so_registration_form.php';

                                    if ($courseApplicationFee !== 0) {
                                        ?>
                                        <li class="divider"></li>
                                        <?php
                                    }
                                    ?>
                                    <li><a target="_blank" href="print_ac_registration_form.php?service_type=<?= $serviceType; ?>&trainee_id=<?= $traineeId; ?>&payment=1">
                                            <i class="fa fa-print"></i>ใบสมัคร
                                        </a>
                                    </li>
                                    <?php
                                    if ($paramCourseId != null) {
                                        ?>
                                        <li><a href="javascript:void(0)"
                                               onClick="onClickPrintAcCertificate('<?= $formNumber; ?>', <?= $traineeId; ?>)">
                                                <i class="fa fa-print"></i>ใบรับรองการผ่านการอบรม
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
                                    ?>
                                    <li class="divider"></li>
                                    <li><a href="javascript:void(0)"
                                           onClick="onClickPrintRegistrationForm('<?= $formNumber; ?>', <?= $traineeId; ?>)">
                                            <i class="fa fa-print"></i>ใบสมัคร
                                        </a>
                                    </li>
                                    <?php
                                    if ($paramCourseId != null) {
                                        ?>
                                        <li><a href="javascript:void(0)"
                                               onClick="onClickPrintResult('<?= $formNumber; ?>', <?= $traineeId; ?>)">
                                                <i class="fa fa-print"></i>ใบบันทึกผลการอบรม
                                            </a>
                                        </li>
                                        <li><a href="javascript:void(0)"
                                               onClick="onClickPrintCertificate('<?= $formNumber; ?>', <?= $traineeId; ?>)">
                                                <i class="fa fa-print"></i>ใบรับรองการผ่านการอบรม
                                            </a>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <div style="text-align: center"><?= $trainee['receipt_number']; ?></div>
                    </td>
                </tr>

                <?php
            }
        }
        ?>
        </tbody>
    </table>

    <script>
        let shouldReload = false;

        $(document).ready(function () {
            $('#tableRegistration').DataTable({
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
                },
            });

            $('#printReceiptModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    location.reload(true);
                }
            });
            $('#manageRegisterStatusModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    location.reload(true);
                }
            });
            $('#manageDocStatusModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    location.reload(true);
                }
            });
            $('#recordResultModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    location.reload(true);
                }
            });
            $('#formManageDocStatus').on('submit', (event) => {
                event.preventDefault();
                if (confirm("ยืนยันแก้ไขข้อมูลใบสมัคร?")) {
                    doUpdateDrivingLicenseRegistration();
                }
            });
            $('#formRecordResult').on('submit', (event) => {
                event.preventDefault();
                if (confirm("ยืนยันบันทึกผลการอบรม?")) {
                    doSaveResult();
                }
            });
            $('#formPrintReceipt').on('submit', (event) => {
                event.preventDefault();
                printReceipt();
            });

            $('#editDocTrainingModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    location.reload(true);
                }
            });

            $('#formEditDocTraining').on('submit', (event) => {
                event.preventDefault();
                if (validateDocTrainingForm()) {
                    if (confirm("ยืนยันแก้ไขข้อมูลใบสมัคร?")) {
                        doUpdateDocTraining();
                    }
                }
            });

            $('#editDocSocialModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    location.reload(true);
                }
            });

            $('#formEditDocSocial').on('submit', (event) => {
                event.preventDefault();
                //if (validateDocSocialForm()) {
                if (confirm("ยืนยันแก้ไขข้อมูลใบสมัคร?")) {
                    doUpdateDocSocial();
                }
                //}
            });

            $('#selectOrganizationType').change(function () {
                //alert(this.value);
                $("#formEditDocTraining #inputOrganizationTypeCustom").prop("disabled", parseInt(this.value) !== 9999);
                if (parseInt(this.value) === 9999) {
                    $("#formEditDocTraining #inputOrganizationTypeCustom").focus();
                }
            });

            $('#selectCoordinatorOrganizationType').change(function () {
                //alert(this.value);
                $("#formEditDocTraining #inputCoordinatorOrganizationTypeCustom").prop("disabled", parseInt(this.value) !== 9999);
                if (parseInt(this.value) === 9999) {
                    $("#formEditDocTraining #inputCoordinatorOrganizationTypeCustom").focus();
                }
            });

            $('#formConfirmPayment').on('submit', (event) => {
                event.preventDefault();

                const traineeId = $('#formConfirmPayment #inputTraineeId').val();
                const paidAmountConfirm = $('#formConfirmPayment #inputAmountConfirm').val();
                const paymentDateConfirm = $('#formConfirmPayment #inputPaymentDateConfirm').val();

                if (confirm("ยืนยันเปลี่ยนสถานะการลงทะเบียนเป็น '" + 'สมบูรณ์' + "' ?"
                    + '\n\nเมื่อกด OK ระบบจะบันทึกสถานะใหม่และยอดเงินที่ลูกค้าจ่ายจริงลงฐานข้อมูลทันที\n----------\n\nยอดเงินที่ลูกค้าจ่ายจริง: '
                    + formatNumber(paidAmountConfirm) + ' บาท')) {

                    $('#confirmPaymentModal').modal('hide');
                    doUpdateRegisterStatus(traineeId, 'complete', paidAmountConfirm, paymentDateConfirm);
                }
            });
        });

        function printReceipt() {
            const loadingIcon = $('#printReceiptModal #spanLoading');
            loadingIcon.show();
            $('#printReceiptModal #alertReceiptSuccess').hide();

            $('#printReceiptModal #alertReceiptError').hide();

            const serviceType = '<?= $serviceType; ?>';
            const traineeId = $('#printReceiptModal #inputTraineeId').val();
            const receiptNumber = $('#formPrintReceipt #inputReceiptNumber').val();

            $.post(
                '../api/api.php/update_receipt_number',
                {
                    serviceType: serviceType,
                    traineeId: traineeId,
                    receiptNumber: receiptNumber,
                }
            ).done(function (data) {
                loadingIcon.hide();

                if (data.error_code === 0) {
                    $('#printReceiptModal #alertReceiptSuccessText').text(data.error_message);
                    $('#printReceiptModal #alertReceiptSuccess').show();

                    shouldReload = true;
                    window.open(`print_receipt.php?service_type=${serviceType}&trainee_id=${traineeId}`, '_blank');
                } else {
                    $('#printReceiptModal #alertReceiptErrorText').text(data.error_message);
                    $('#printReceiptModal #alertReceiptError').show();
                }
            }).fail(function () {
                loadingIcon.hide();
                $('#printReceiptModal #alertReceiptErrorText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                $('#printReceiptModal #alertReceiptError').show();
            });
        }

        function doUpdateDrivingLicenseRegistration() {
            const loadingIcon = $('#manageDocStatusModal #spanLoading');

            loadingIcon.show();
            $('#manageDocStatusModal #alertDocSuccess').hide();
            $('#manageDocStatusModal #alertDocError').hide();

            $('#formManageDocStatus').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    loadingIcon.hide();

                    if (data.error_code === 0) {
                        $('#manageDocStatusModal #alertDocSuccessText').text(data.error_message);
                        $('#manageDocStatusModal #alertDocSuccess').show();

                        shouldReload = true;
                    } else {
                        $('#manageDocStatusModal #alertDocErrorText').text(data.error_message);
                        $('#manageDocStatusModal #alertDocError').show();
                    }
                },
                error: () => {
                    loadingIcon.hide();
                    $('#manageDocStatusModal #alertDocErrorText').val('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                    $('#manageDocStatusModal #alertDocError').show();
                }
            });
        }

        function doSaveResult() {
            const loadingIcon = $('#recordResultModal #spanLoading');

            loadingIcon.show();
            $('#recordResultModal #alertRecordResultSuccess').hide();
            $('#recordResultModal #alertRecordResultError').hide();

            $('#formRecordResult').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    loadingIcon.hide();

                    if (data.error_code === 0) {
                        $('#recordResultModal #alertRecordResultSuccessText').text(data.error_message);
                        $('#recordResultModal #alertRecordResultSuccess').show();

                        shouldReload = true;
                    } else {
                        $('#recordResultModal #alertRecordResultErrorText').text(data.error_message);
                        $('#recordResultModal #alertRecordResultError').show();
                    }
                },
                error: () => {
                    loadingIcon.hide();
                    $('#recordResultModal #alertRecordResultErrorText').val('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                    $('#recordResultModal #alertRecordResultError').show();
                }
            });
        }

        function onClickPrintReceipt(formNumber, traineeId, traineeName, courseName, courseApplicationFee, paidAmount, receiptNumber) {
            const registerStatus = $('#inputRegisterStatus' + traineeId).val();
            const canPrint = registerStatus === 'complete';

            $('#printReceiptModal #spanFormNumber').text(formNumber);
            $('#formPrintReceipt #inputTraineeId').val(traineeId);
            $('#formPrintReceipt #inputTraineeName').val(traineeName);
            $('#formPrintReceipt #inputCourseName').val(courseName);
            $('#formPrintReceipt #inputCourseFee').val(formatNumber(courseApplicationFee));
            $('#formPrintReceipt #inputPaidAmount').val(paidAmount);
            // บังคับให้กรอกเลขที่ใบเสร็จใหม่ เพราะคงไม่มีทางใช้ใบเดิม
            //$('#formPrintReceipt #inputReceiptNumber').val(receiptNumber);
            $('#formPrintReceipt #buttonPrintReceipt').prop('disabled', !canPrint);
            const divCallout = $('#formPrintReceipt #alertCanNotPrint');
            canPrint ? divCallout.hide() : divCallout.show();

            const loadingIcon = $('#printReceiptModal #spanLoading');
            loadingIcon.hide();
            $('#printReceiptModal #alertReceiptSuccess').hide();
            $('#printReceiptModal #alertReceiptError').hide();

            $('#printReceiptModal').modal('show');
        }

        function onClickResult(formNumber, traineeId, traineeTitle, traineeFirstName, traineeLastName, traineePid, traineePhone,
                               courseType, licenseType, subjectResults) {
            const rowSubject1 = $('#recordResultModal #rowSubject1');
            const rowSubject2 = $('#recordResultModal #rowSubject2');
            const rowSubject3 = $('#recordResultModal #rowSubject3');
            const rowSubject4 = $('#recordResultModal #rowSubject4');

            rowSubject1.show();
            if (courseType === 1) {
                rowSubject2.show();
                rowSubject3.show();
                rowSubject4.show();
            } else {
                rowSubject2.hide();
                rowSubject3.hide();
                rowSubject4.hide();
            }

            if (courseType === 1) {
                $('.spanTempWord').show();
            } else {
                $('.spanTempWord').hide();
            }

            let subject1Name = '';
            switch (courseType) {
                case 1:
                    subject1Name = 'ความรู้เกี่ยวกับกฎหมายที่เกี่ยวข้อง';
                    break;
                case 2:
                    subject1Name = 'การขับรถอย่างปลอดภัย, มารยาทในการขับรถ';
                    break;
                case 3:
                    subject1Name = 'การขับรถอย่างปลอดภัย, มารยาทในการขับรถ';
                    break;
            }
            $('#recordResultModal #spanSubject1Name').text(subject1Name);

            $('#recordResultModal #alertRecordResultSuccess').hide();
            $('#recordResultModal #alertRecordResultError').hide();
            $('#recordResultModal #spanLoading').hide();

            $('#recordResultModal #spanFormNumber').text(formNumber);
            $('#recordResultModal #inputTraineeId').val(traineeId);

            //$('#recordResultModal #inputTraineePid').val(traineePid);
            //$('#recordResultModal #inputPhone').val(traineePhone);

            $('#recordResultModal #inputTraineeTitle').val(traineeTitle);
            $('#recordResultModal #inputTraineeFirstName').val(traineeFirstName);
            $('#recordResultModal #inputTraineeLastName').val(traineeLastName);

            $('#recordResultModal input[name=courseType][value=' + courseType + ']').prop('checked', true);

            $('#recordResultModal #checkBoxLicenseTypeTricycle').prop('checked', (licenseType & 4) === 4);
            $('#recordResultModal #checkBoxLicenseTypeBicycle').prop('checked', (licenseType & 2) === 2);
            $('#recordResultModal #checkBoxLicenseTypeCar').prop('checked', (licenseType & 1) === 1);

            for (let i = 0; i < 4; i++) {
                if (subjectResults[i] != null) {
                    $('#recordResultModal input[name=subject' + (i + 1) + 'Result][value=' + subjectResults[i] + ']').prop('checked', true);
                } else {
                    $('#recordResultModal input[name=subject' + (i + 1) + 'Result][value=0]').prop('checked', false);
                    $('#recordResultModal input[name=subject' + (i + 1) + 'Result][value=1]').prop('checked', false);
                }
            }

            $('#recordResultModal').modal('show');
        }

        function onClickPrintRegistrationForm(formNumber, traineeId) {
            if (parseInt($('#inputDocStatus' + traineeId).val()) === 0) {
                if (!confirm(`ใบสมัครเลขที่ ${formNumber} ยังไม่ได้รับการตรวจสอบข้อมูลและการยื่นเอกสาร!\n\nยืนยันพิมพ์ใบสมัครนี้?`)) {
                    return;
                }
            }
            window.open(`print_dl_registration_form.php?trainee_id=${traineeId}&payment=1&thai=1`, '_blank');
        }

        function onClickPrintResult(formNumber, traineeId) {
            if (parseInt($('#inputResultStatus' + traineeId).val()) === 0) {
                if (!confirm(`การบันทึกผลสำหรับใบสมัครเลขที่ ${formNumber} ยังไม่สมบูรณ์!\n\nยืนยันพิมพ์ใบบันทึกผลนี้?`)) {
                    return;
                }
            }
            window.open(`print_dl_result.php?trainee_id=${traineeId}`, '_blank');
        }

        function onClickPrintCertificate(formNumber, traineeId) {
            if (parseInt($('#inputCertificateStatus' + traineeId).val()) === 0) {
                /*if (!confirm(`ใบรับรองการผ่านการอบรมยังไม่มีเลขที่! เนื่องจากผลการอบรมสำหรับใบสมัคร ${formNumber} ยังไม่ผ่าน หรือยังกรอกผลไม่ครบ\n\nยืนยันพิมพ์ใบรับรองการผ่านการอบรม?`)) {
                    return;
                }*/
                alert('ไม่สามารถพิมพ์ใบรับรองการผ่านการอบรมได้ เนื่องจากผลการอบรมยังผ่านไม่ครบทุกวิชา');
            } else {
                window.open(`print_dl_certificate.php?trainee_id=${traineeId}`, '_blank');
            }
        }

        function onClickPrintAcCertificate(formNumber, traineeId) {
            window.open(`word_ac_certificate.php?trainee_id=${traineeId}&service_type=<?= $serviceType; ?>`, '_blank');
        }

        function onClickDocTraining(formNumber, id, courseRegId, title, firstName, lastName, phone, email, jobPosition,
                                    organizationName, organizationType, organizationTypeCustom,
                                    coordinatorTitle, coordinatorFirstName, coordinatorLastName, coordinatorPhone, coordinatorEmail, coordinatorJobPosition,
                                    coordinatorOrganizationName, coordinatorOrganizationType, coordinatorOrganizationTypeCustom,
                                    receiptName, receiptAddress, receiptSubDistrict, receiptDistrict, receiptProvince,
                                    receiptPostalCode, receiptOrganizationPhone, receiptTaxId,
                                    displayCreatedDate, displayCreatedTime) {
            /*ข้อมูลผู้สมัคร*/
            $('#editDocTrainingModal #alertSuccess').hide();
            $('#editDocTrainingModal #alertError').hide();
            $('#editDocTrainingModal #spanLoading').hide();

            $('#editDocTrainingModal #spanFormNumber').text(formNumber);
            $('#formEditDocTraining #inputId').val(id);
            $('#formEditDocTraining #inputCourseRegId').val(courseRegId);

            $('#formEditDocTraining #inputTitle').val(title);
            $('#formEditDocTraining #inputFirstName').val(firstName);
            $('#formEditDocTraining #inputLastName').val(lastName);
            $('#formEditDocTraining #inputPhone').val(phone);
            $('#formEditDocTraining #inputEmail').val(email);
            $('#formEditDocTraining #inputJobPosition').val(jobPosition);

            $('#formEditDocTraining #inputOrganizationName').val(organizationName);
            $('#formEditDocTraining #selectOrganizationType').val(organizationType);
            $('#formEditDocTraining #inputOrganizationTypeCustom').val('');
            if (organizationType === 9999) {
                $('#formEditDocTraining #inputOrganizationTypeCustom').val(organizationTypeCustom);
            }
            $("#formEditDocTraining #inputOrganizationTypeCustom").prop("disabled", organizationType !== 9999);

            /*ข้อมูลผู้ประสานงาน*/
            coordinatorOrganizationType = parseInt(coordinatorOrganizationType);

            const inputCoordinatorTitle = $('#formEditDocTraining #inputCoordinatorTitle');
            const inputCoordinatorFirstName = $('#formEditDocTraining #inputCoordinatorFirstName');
            const inputCoordinatorLastName = $('#formEditDocTraining #inputCoordinatorLastName');
            const inputCoordinatorPhone = $('#formEditDocTraining #inputCoordinatorPhone');
            const inputCoordinatorEmail = $('#formEditDocTraining #inputCoordinatorEmail');
            const inputCoordinatorJobPosition = $('#formEditDocTraining #inputCoordinatorJobPosition');
            const inputCoordinatorOrganizationName = $('#formEditDocTraining #inputCoordinatorOrganizationName');
            const selectCoordinatorOrganizationType = $('#formEditDocTraining #selectCoordinatorOrganizationType');
            const inputCoordinatorOrganizationTypeCustom = $('#formEditDocTraining #inputCoordinatorOrganizationTypeCustom');

            const inputFieldList = [];
            inputFieldList.push(inputCoordinatorTitle);
            inputFieldList.push(inputCoordinatorFirstName);
            inputFieldList.push(inputCoordinatorLastName);
            inputFieldList.push(inputCoordinatorPhone);
            inputFieldList.push(inputCoordinatorEmail);
            inputFieldList.push(inputCoordinatorJobPosition);
            inputFieldList.push(inputCoordinatorOrganizationName);
            inputFieldList.push(selectCoordinatorOrganizationType);
            //inputFieldList.push(inputCoordinatorOrganizationTypeCustom);

            if (coordinatorTitle === '') { // สมัครคนเดียว ไม่มีผู้ประสานงาน
                setRequiredFields(inputFieldList, false);

                inputCoordinatorTitle.val('');
                inputCoordinatorFirstName.val('');
                inputCoordinatorLastName.val('');
                inputCoordinatorPhone.val('');
                inputCoordinatorEmail.val('');
                inputCoordinatorJobPosition.val('');

                inputCoordinatorOrganizationName.val('');
                selectCoordinatorOrganizationType.val('0');
                inputCoordinatorOrganizationTypeCustom.val('');

                $('#formEditDocTraining #tabHeadCoordinator').hide();
                //$('#formEditDocTraining #tabCoordinator').hide();
                //$('#formEditDocTraining #tabHeadCoordinator a').removeAttr('data-toggle');
            } else { // สมัครหลายคน มีผู้ประสานงาน
                setRequiredFields(inputFieldList, true);

                inputCoordinatorTitle.val(coordinatorTitle);
                inputCoordinatorFirstName.val(coordinatorFirstName);
                inputCoordinatorLastName.val(coordinatorLastName);
                inputCoordinatorPhone.val(coordinatorPhone);
                inputCoordinatorEmail.val(coordinatorEmail);
                inputCoordinatorJobPosition.val(coordinatorJobPosition);

                inputCoordinatorOrganizationName.val(coordinatorOrganizationName);
                selectCoordinatorOrganizationType.val(coordinatorOrganizationType);
                inputCoordinatorOrganizationTypeCustom.val('');
                if (coordinatorOrganizationType === 9999) {
                    inputCoordinatorOrganizationTypeCustom.val(coordinatorOrganizationTypeCustom);
                }
                inputCoordinatorOrganizationTypeCustom.prop("disabled", coordinatorOrganizationType !== 9999);

                $('#formEditDocTraining #tabHeadCoordinator').show();
                //$('#formEditDocTraining #tabCoordinator').show();
                //$('#formEditDocTraining #tabHeadCoordinator a').attr('data-toggle', 'tab');
            }

            $('#formEditDocTraining #inputReceiptName').val(receiptName);
            $('#formEditDocTraining #inputReceiptAddress').val(receiptAddress);
            $('#formEditDocTraining #inputReceiptSubDistrict').val(receiptSubDistrict);
            $('#formEditDocTraining #inputReceiptDistrict').val(receiptDistrict);
            $('#formEditDocTraining #inputReceiptProvince').val(receiptProvince);
            $('#formEditDocTraining #inputReceiptPostalCode').val(receiptPostalCode);
            $('#formEditDocTraining #inputReceiptOrganizationPhone').val(receiptOrganizationPhone);
            $('#formEditDocTraining #inputReceiptTaxId').val(receiptTaxId);

            $('#editDocTrainingModal #spanCreatedAt').text(displayCreatedDate + ', ' + displayCreatedTime);

            $('#editDocTrainingModal').modal('show');
        }

        function setRequiredFields(fieldList, required) {
            fieldList.forEach(field => {
                if (required) {
                    field.attr('required', 'true');
                } else {
                    field.removeAttr('required');
                }
            });
        }

        function validateDocTrainingForm() {
            let valid = true;

            const selectOrganizationType = $('#formEditDocTraining #selectOrganizationType');
            const inputOrganizationTypeCustom = $('#formEditDocTraining #inputOrganizationTypeCustom');
            const selectCoordinatorOrganizationType = $('#formEditDocTraining #selectCoordinatorOrganizationType');
            const inputCoordinatorOrganizationTypeCustom = $('#formEditDocTraining #inputCoordinatorOrganizationTypeCustom');

            if (parseInt(selectOrganizationType.val()) === 9999 && inputOrganizationTypeCustom.val().trim() === '') {
                alert('กรอกประเภทหน่วยงานของผู้สมัคร');
                inputOrganizationTypeCustom.focus();
                valid = false;
            } else if (parseInt(selectCoordinatorOrganizationType.val()) === 9999 && inputCoordinatorOrganizationTypeCustom.val().trim() === '') {
                alert('กรอกประเภทหน่วยงานของผู้ประสานงาน');
                inputCoordinatorOrganizationTypeCustom.focus();
                valid = false;
            }

            return valid;
        }

        function doUpdateDocTraining() {
            const loadingIcon = $('#editDocTrainingModal #spanLoading');

            loadingIcon.show();
            $('#editDocTrainingModal #alertSuccess').hide();
            $('#editDocTrainingModal #alertError').hide();

            $('#formEditDocTraining').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    loadingIcon.hide();

                    if (data.error_code === 0) {
                        $('#editDocTrainingModal #alertSuccessText').text(data.error_message);
                        $('#editDocTrainingModal #alertSuccess').show();

                        shouldReload = true;
                    } else {
                        $('#editDocTrainingModal #alertErrorText').text(data.error_message);
                        $('#editDocTrainingModal #alertError').show();
                    }
                },
                error: () => {
                    loadingIcon.hide();
                    $('#editDocTrainingModal #alertErrorText').val('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                    $('#editDocTrainingModal #alertError').show();
                }
            });
        }

        function doUpdateDocSocial() {
            const loadingIcon = $('#editDocSocialModal #spanLoading');

            loadingIcon.show();
            $('#editDocSocialModal #alertSuccess').hide();
            $('#editDocSocialModal #alertError').hide();

            $('#formEditDocSocial').ajaxSubmit({
                dataType: 'json',
                success: (data, statusText) => {
                    loadingIcon.hide();

                    if (data.error_code === 0) {
                        $('#editDocSocialModal #alertSuccessText').text(data.error_message);
                        $('#editDocSocialModal #alertSuccess').show();

                        shouldReload = true;
                    } else {
                        $('#editDocSocialModal #alertErrorText').text(data.error_message);
                        $('#editDocSocialModal #alertError').show();
                    }
                },
                error: () => {
                    loadingIcon.hide();
                    $('#editDocSocialModal #alertErrorText').val('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                    $('#editDocSocialModal #alertError').show();
                }
            });
        }

        function onClickDocSocial(formNumber, id, courseApplicationFee, title, firstName, lastName, phone, email, occupation, workPlace,
                                  address, subDistrict, district, province, postalCode, contactName, contactPhone, disease,
                                  receiptName, receiptAddress, receiptSubDistrict, receiptDistrict, receiptProvince, receiptPostalCode,
                                  displayCreatedDate, displayCreatedTime) {
            /*ข้อมูลผู้สมัคร*/
            $('#editDocSocialModal #alertSuccess').hide();
            $('#editDocSocialModal #alertError').hide();
            $('#editDocSocialModal #spanLoading').hide();

            $('#editDocSocialModal #spanFormNumber').text(formNumber);
            $('#formEditDocSocial #inputId').val(id);

            $('#formEditDocSocial #inputTitle').val(title);
            $('#formEditDocSocial #inputFirstName').val(firstName);
            $('#formEditDocSocial #inputLastName').val(lastName);
            $('#formEditDocSocial #inputPhone').val(phone);
            $('#formEditDocSocial #inputEmail').val(email);
            $('#formEditDocSocial #inputOccupation').val(occupation);
            $('#formEditDocSocial #inputWorkPlace').val(workPlace);

            $('#formEditDocSocial #inputContactName').val(contactName);
            $('#formEditDocSocial #inputContactPhone').val(contactPhone);
            $('#formEditDocSocial #inputDisease').val(disease);

            $('#formEditDocSocial #inputAddress').val(address);
            $('#formEditDocSocial #inputSubDistrict').val(subDistrict);
            $('#formEditDocSocial #inputDistrict').val(district);
            $('#formEditDocSocial #inputProvince').val(province);
            $('#formEditDocSocial #inputPostalCode').val(postalCode);

            /*ข้อมูลการออกใบเสร็จ*/
            const inputReceiptName = $('#formEditDocSocial #inputReceiptName');
            const inputReceiptAddress = $('#formEditDocSocial #inputReceiptAddress');
            const inputReceiptSubDistrict = $('#formEditDocSocial #inputReceiptSubDistrict');
            const inputReceiptDistrict = $('#formEditDocSocial #inputReceiptDistrict');
            const inputReceiptProvince = $('#formEditDocSocial #inputReceiptProvince');
            const inputReceiptPostalCode = $('#formEditDocSocial #inputReceiptPostalCode');

            const inputFieldList = [];
            inputFieldList.push(inputReceiptName);
            inputFieldList.push(inputReceiptAddress);
            inputFieldList.push(inputReceiptSubDistrict);
            inputFieldList.push(inputReceiptDistrict);
            inputFieldList.push(inputReceiptProvince);
            inputFieldList.push(inputReceiptPostalCode);

            if (courseApplicationFee === 0) { // คอร์สฟรี
                setRequiredFields(inputFieldList, false);

                inputReceiptName.val('');
                inputReceiptAddress.val('');
                inputReceiptSubDistrict.val('');
                inputReceiptDistrict.val('');
                inputReceiptProvince.val('');
                inputReceiptPostalCode.val('');

                $('#formEditDocSocial #tabHeadReceipt').hide();
            } else { // คอร์สเสียเงิน
                setRequiredFields(inputFieldList, true);

                inputReceiptName.val(receiptName);
                inputReceiptAddress.val(receiptAddress);
                inputReceiptSubDistrict.val(receiptSubDistrict);
                inputReceiptDistrict.val(receiptDistrict);
                inputReceiptProvince.val(receiptProvince);
                inputReceiptPostalCode.val(receiptPostalCode);

                $('#formEditDocSocial #tabHeadReceipt').show();
            }

            $('#editDocSocialModal #spanCreatedAt').text(displayCreatedDate + ', ' + displayCreatedTime);

            $('#editDocSocialModal').modal('show');
        }

        function onClickDoc(formNumber, traineeId, traineeTitle, traineeFirstName, traineeLastName, traineePid,
                            traineeAddress, traineeMoo, traineeSoi, traineeRoad, traineeSubDistrict, traineeDistrict, traineeProvince, traineePhone,
                            courseType, licenseType, docStatus, pidFileName, pidFileName2, pidFileName3, pidFileName4, pidFileName5) {

            $('#manageDocStatusModal #alertDocSuccess').hide();
            $('#manageDocStatusModal #alertDocError').hide();
            $('#manageDocStatusModal #spanLoading').hide();

            $('#manageDocStatusModal #spanFormNumber').text(formNumber);
            $('#manageDocStatusModal #inputTraineeId').val(traineeId);

            $('#manageDocStatusModal #inputTraineePid').val(traineePid);
            $('#manageDocStatusModal #inputPhone').val(traineePhone);

            $('#manageDocStatusModal #inputTraineeTitle').val(traineeTitle);
            $('#manageDocStatusModal #inputTraineeFirstName').val(traineeFirstName);
            $('#manageDocStatusModal #inputTraineeLastName').val(traineeLastName);
            $('#manageDocStatusModal #inputAddress').val(traineeAddress);
            $('#manageDocStatusModal #inputMoo').val(traineeMoo);
            $('#manageDocStatusModal #inputSoi').val(traineeSoi);
            $('#manageDocStatusModal #inputRoad').val(traineeRoad);
            $('#manageDocStatusModal #inputSubDistrict').val(traineeSubDistrict);
            $('#manageDocStatusModal #inputDistrict').val(traineeDistrict);
            $('#manageDocStatusModal #inputProvince').val(traineeProvince);

            $('#manageDocStatusModal input[name=courseType][value=' + courseType + ']').prop('checked', true);

            if (courseType === 1) {
                $('.spanTempWord').show();
            } else {
                $('.spanTempWord').hide();
            }

            $('#manageDocStatusModal #checkBoxLicenseTypeTricycle').prop('checked', (licenseType & 4) === 4);
            $('#manageDocStatusModal #checkBoxLicenseTypeBicycle').prop('checked', (licenseType & 2) === 2);
            $('#manageDocStatusModal #checkBoxLicenseTypeCar').prop('checked', (licenseType & 1) === 1);

            const docDetails = $('#manageDocStatusModal #docDetails');
            const imgPid = $('#manageDocStatusModal #imgPid');
            imgPid.attr('src', '../images/ic_no_image.png');
            const imgPid2 = $('#manageDocStatusModal #imgPid2');
            imgPid2.attr('src', '../images/ic_no_image.png');
            const imgPid3 = $('#manageDocStatusModal #imgPid3');
            imgPid3.attr('src', '../images/ic_no_image.png');
            const imgPid4 = $('#manageDocStatusModal #imgPid4');
            imgPid4.attr('src', '../images/ic_no_image.png');
            const imgPid5 = $('#manageDocStatusModal #imgPid5');
            imgPid5.attr('src', '../images/ic_no_image.png');
            const noDocAlert = $('#manageDocStatusModal #alertNoDoc');

            if (pidFileName === '') {
                docDetails.hide();
                noDocAlert.show();
            } else {
                docDetails.show();
                noDocAlert.hide();
                imgPid.attr('src', getFileExtension(pidFileName) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + pidFileName);
                imgPid.parent().attr('href', getFileExtension(pidFileName) === 'pdf' ? '../uploads/slip_images/' + pidFileName : '../uploads/slip_images/' + pidFileName);
                setAttributes(getFileExtension(pidFileName) === 'pdf', imgPid.parent(), 'pidImage');

                if (pidFileName2 === '') {
                    imgPid2.parent().attr('href', '../images/ic_no_image.png');
                    imgPid2.parent().removeAttr('data-lightbox');
                    imgPid2.hide();
                } else {
                    imgPid2.attr('src', getFileExtension(pidFileName2) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + pidFileName2);
                    imgPid2.parent().attr('href', getFileExtension(pidFileName2) === 'pdf' ? '../uploads/slip_images/' + pidFileName2 : '../uploads/slip_images/' + pidFileName2);
                    setAttributes(getFileExtension(pidFileName2) === 'pdf', imgPid2.parent(), 'pidImage');
                    imgPid2.show();
                }
                if (pidFileName3 === '') {
                    imgPid3.parent().attr('href', '../images/ic_no_image.png');
                    imgPid3.parent().removeAttr('data-lightbox');
                    imgPid3.hide();
                } else {
                    imgPid3.attr('src', getFileExtension(pidFileName3) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + pidFileName3);
                    imgPid3.parent().attr('href', getFileExtension(pidFileName3) === 'pdf' ? '../uploads/slip_images/' + pidFileName3 : '../uploads/slip_images/' + pidFileName3);
                    setAttributes(getFileExtension(pidFileName3) === 'pdf', imgPid3.parent(), 'pidImage');
                    imgPid3.show();
                }
                if (pidFileName4 === '') {
                    imgPid4.parent().attr('href', '../images/ic_no_image.png');
                    imgPid4.parent().removeAttr('data-lightbox');
                    imgPid4.hide();
                } else {
                    imgPid4.attr('src', getFileExtension(pidFileName4) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + pidFileName4);
                    imgPid4.parent().attr('href', getFileExtension(pidFileName4) === 'pdf' ? '../uploads/slip_images/' + pidFileName4 : '../uploads/slip_images/' + pidFileName4);
                    setAttributes(getFileExtension(pidFileName4) === 'pdf', imgPid4.parent(), 'pidImage');
                    imgPid4.show();
                }
                if (pidFileName5 === '') {
                    imgPid5.parent().attr('href', '../images/ic_no_image.png');
                    imgPid5.parent().removeAttr('data-lightbox');
                    imgPid5.hide();
                } else {
                    imgPid5.attr('src', getFileExtension(pidFileName5) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + pidFileName5);
                    imgPid5.parent().attr('href', getFileExtension(pidFileName5) === 'pdf' ? '../uploads/slip_images/' + pidFileName5 : '../uploads/slip_images/' + pidFileName5);
                    setAttributes(getFileExtension(pidFileName5) === 'pdf', imgPid5.parent(), 'pidImage');
                    imgPid5.show();
                }
            }

            setButtonDocStatusClass(docStatus);
            $('#manageDocStatusModal').modal('show');
        }

        function getFileExtension(fileName) {
            const dotIndex = fileName.lastIndexOf('.');
            return fileName.substring(dotIndex + 1).toLowerCase();
        }

        function setAttributes(isPdf, anchorElement, lightBoxTag) {
            if (isPdf) {
                anchorElement.removeAttr('data-lightbox');
                anchorElement.attr('target', '_blank');
            } else {
                anchorElement.attr('data-lightbox', lightBoxTag);
            }
        }

        function onClickStatus(formNumber, traineeId, traineeName, coordinatorName, courseName, courseApplicationFee, paidAmount, paymentDate, pidFileName) {
            doGetPaymentNotification(traineeId);

            $('#manageRegisterStatusModal #spanFormNumber').text(formNumber);
            $('#manageRegisterStatusModal #inputTraineeId').val(traineeId);
            $('#manageRegisterStatusModal #inputCoursePaidType').val(courseApplicationFee === '0' ? 'free' : 'paid');
            //$('#formManageRegisterStatus #inputRegisterStatus').val(registerStatus);
            $('#manageRegisterStatusModal #inputTraineeName').val(traineeName);
            $('#manageRegisterStatusModal #inputCoordinatorName').val(coordinatorName);

            coordinatorName ? $('#manageRegisterStatusModal #divCoordinatorName').show()
                : $('#manageRegisterStatusModal #divCoordinatorName').hide();

            $('#manageRegisterStatusModal #inputCourseName').val(courseName);
            $('#manageRegisterStatusModal #inputCourseFee').val(courseApplicationFee);

            const registerStatus = $('#inputRegisterStatus' + traineeId).val();

            if (courseApplicationFee === '0') {
                $('#manageRegisterStatusModal #buttonStatusForPaidCourse').hide();
                $('#manageRegisterStatusModal #buttonStatusForFreeCourse').show();
                $('#manageRegisterStatusModal #inputPaidAmount').parent().parent().hide();
                $('#manageRegisterStatusModal #inputPaymentDateConfirm').parent().parent().hide();
                $('#manageRegisterStatusModal #divMessage').hide();

                setButtonStatusClassForFreeCourse(registerStatus);

                $('#formManageRegisterStatus #tabPaymentNotification').hide();
            } else {
                $('#manageRegisterStatusModal #buttonStatusForPaidCourse').show();
                $('#manageRegisterStatusModal #buttonStatusForFreeCourse').hide();
                $('#manageRegisterStatusModal #inputPaidAmount').parent().parent().show();
                $('#manageRegisterStatusModal #inputPaidAmount').val(paidAmount);
                $('#manageRegisterStatusModal #inputPaymentDateConfirm').val(paymentDate);
                $('#manageRegisterStatusModal #divMessage').show();

                setButtonStatusClassForPaidCourse(registerStatus);

                $('#formManageRegisterStatus #tabPaymentNotification').show();
            }

            $('#manageRegisterStatusModal #alertSuccess').hide();
            $('#manageRegisterStatusModal #alertError').hide();
            $('#manageRegisterStatusModal #spanLoading').hide();


            $('#manageRegisterStatusModal').modal('show');
        }

        function removeButtonStatusClass() {
            const buttonStatus = $('#manageRegisterStatusModal #buttonStatus');
            const buttonStatusDropDown = $('#manageRegisterStatusModal #buttonStatusDropDown');

            buttonStatus.removeClass('btn-default');
            buttonStatusDropDown.removeClass('btn-default');
            buttonStatus.removeClass('btn-info');
            buttonStatusDropDown.removeClass('btn-info');
            buttonStatus.removeClass('btn-danger');
            buttonStatusDropDown.removeClass('btn-danger');
            buttonStatus.removeClass('btn-success');
            buttonStatusDropDown.removeClass('btn-success');
            buttonStatus.removeClass('btn-warning');
            buttonStatusDropDown.removeClass('btn-warning');
        }

        function setButtonStatusClassForPaidCourse(registerStatus) {
            let statusClass, statusText;
            switch (registerStatus) {
                case 'start':
                    statusClass = 'btn-info';
                    statusText = 'ยังไม่ได้ชำระเงิน';
                    break;
                case 'wait-approve':
                    statusClass = 'btn-warning';
                    statusText = 'รอตรวจสอบ';
                    break;
                case 'complete':
                    statusClass = 'btn-success';
                    statusText = 'สมบูรณ์';
                    break;
                case 'cancel':
                    statusClass = 'btn-danger';
                    statusText = 'ยกเลิก';
                    break;
            }

            $('#manageRegisterStatusModal #spanCurrentStatus').text(statusText);

            removeButtonStatusClass();
            const buttonStatus = $('#manageRegisterStatusModal #buttonStatus');
            const buttonStatusDropDown = $('#manageRegisterStatusModal #buttonStatusDropDown');
            buttonStatus.addClass(statusClass);
            buttonStatusDropDown.addClass(statusClass);
        }

        function setButtonStatusClassForFreeCourse(registerStatus) {
            let statusClass, statusText;
            switch (registerStatus) {
                case 'start':
                    statusClass = 'btn-info';
                    statusText = 'สมัคร';
                    break;
                case 'cancel':
                    statusClass = 'btn-danger';
                    statusText = 'ยกเลิก';
                    break;
            }

            $('#manageRegisterStatusModal #spanCurrentStatus').text(statusText);

            removeButtonStatusClass();
            const buttonStatus = $('#manageRegisterStatusModal #buttonStatus');
            const buttonStatusDropDown = $('#manageRegisterStatusModal #buttonStatusDropDown');
            buttonStatus.addClass(statusClass);
            buttonStatusDropDown.addClass(statusClass);
        }

        function removeButtonDocStatusClass() {
            const buttonDocStatus = $('#manageDocStatusModal #buttonDocStatus');
            const buttonDocStatusDropDown = $('#manageDocStatusModal #buttonDocStatusDropDown');

            buttonDocStatus.removeClass('btn-default');
            buttonDocStatusDropDown.removeClass('btn-default');
            buttonDocStatus.removeClass('btn-info');
            buttonDocStatusDropDown.removeClass('btn-info');
            buttonDocStatus.removeClass('btn-danger');
            buttonDocStatusDropDown.removeClass('btn-danger');
            buttonDocStatus.removeClass('btn-success');
            buttonDocStatusDropDown.removeClass('btn-success');
            buttonDocStatus.removeClass('btn-warning');
            buttonDocStatusDropDown.removeClass('btn-warning');
        }

        function setButtonDocStatusClass(docStatus) {
            let statusClass, statusText;
            switch (docStatus) {
                case 0:
                    statusClass = 'btn-warning';
                    statusText = 'รอตรวจสอบ';
                    break;
                case 1:
                    statusClass = 'btn-success';
                    statusText = 'สมบูรณ์';
                    break;
            }

            $('#manageDocStatusModal #spanCurrentDocStatus').text(statusText);

            removeButtonDocStatusClass();
            const buttonDocStatus = $('#manageDocStatusModal #buttonDocStatus');
            const buttonDocStatusDropDown = $('#manageDocStatusModal #buttonDocStatusDropDown');
            buttonDocStatus.addClass(statusClass);
            buttonDocStatusDropDown.addClass(statusClass);
        }

        function updateDocStatus(newStatus) {
            $('#manageDocStatusModal #alertDocSuccess').hide();
            $('#manageDocStatusModal #alertDocError').hide();

            const traineeId = $('#manageDocStatusModal #inputTraineeId').val();
            const oldStatus = parseInt($('#inputDocStatus' + traineeId).val());

            //alert('Old Status: ' + oldStatus + ', New Status: ' + newStatus);

            if (oldStatus !== newStatus) {
                let text = '';
                switch (newStatus) {
                    case 0:
                        text = 'รอตรวจสอบ';
                        break;
                    case 1:
                        text = 'สมบูรณ์';
                        break;
                }

                if (confirm("ยืนยันเปลี่ยนสถานะข้อมูล/เอกสารผู้สมัครเป็น '" + text + "' ?"
                    + '\n\nเมื่อกด OK ระบบจะบันทึกสถานะใหม่ลงฐานข้อมูลทันที')) {
                    doUpdateDocStatus(traineeId, newStatus);
                }
            }
        }

        function doUpdateDocStatus(traineeId, newStatus) {
            $('#manageDocStatusModal #spanLoading').show();

            $.post(
                '../api/api.php/update_driving_license_doc_status',
                {
                    traineeId: traineeId,
                    docStatus: newStatus,
                }
            ).done(function (data) {
                $('#manageDocStatusModal #spanLoading').hide();
                if (data.error_code === 0) {
                    $('#manageDocStatusModal #alertDocSuccessText').text(data.error_message);
                    $('#manageDocStatusModal #alertDocSuccess').show();

                    //$('#formManageRegisterStatus #inputRegisterStatus').val(newStatus);
                    $('#inputDocStatus' + traineeId).val(newStatus);
                    setButtonDocStatusClass(newStatus);

                    shouldReload = true;
                    //updateStatusButtonInTable(traineeId, oldStatus, newStatus);
                } else {
                    $('#manageDocStatusModal #alertDocErrorText').text(data.error_message);
                    $('#manageDocStatusModal #alertDocError').show();
                }
            }).fail(function () {
                $('#manageDocStatusModal #spanLoading').hide();
                $('#manageDocStatusModal #alertDocErrorText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                $('#manageDocStatusModal #alertDocError').show();
            });
        }

        function updateRegisterStatus(element, newStatus) {
            $('#manageRegisterStatusModal #alertSuccess').hide();
            $('#manageRegisterStatusModal #alertError').hide();

            const traineeId = $('#manageRegisterStatusModal #inputTraineeId').val();
            const oldStatus = $('#inputRegisterStatus' + traineeId).val();
            //const paidAmount = $('#manageRegisterStatusModal #inputPaidAmount').val(); // ยอดเงินจ่ายจริง

            //const oldStatus = $('#formManageRegisterStatus #inputRegisterStatus').val();
            //alert('Trainee ID: ' + traineeId + ', Old Status: ' + oldStatus + ', New Status: ' + newStatus);

            if (oldStatus !== newStatus) {
                let text = $(element).text();
                /*switch (newStatus) {
                    case 'start':
                        text = 'ยังไม่ได้ชำระเงิน';
                        break;
                    case 'wait-approve':
                        text = 'รอตรวจสอบ';
                        break;
                    case 'complete':
                        text = 'สมบูรณ์';
                        break;
                    case 'cancel':
                        text = 'ยกเลิก';
                        break;
                }*/

                if (newStatus === 'complete') {

                    $('#confirmPaymentModal').modal('show');
                    return;

                    ////////////////////////////////////////////////////////////////////////////////////////

                    const courseFee = $('#manageRegisterStatusModal #inputCourseFee').val();
                    const paidAmount =
                    <?php if ($serviceType === SERVICE_TYPE_TRAINING || $serviceType === SERVICE_TYPE_SOCIAL) { ?>
                        prompt('กรอกยอดเงินที่ลูกค้าจ่ายจริง (ไม่ต้องใส่เครื่องหมาย , ):', courseFee.replace(/\,/g, ''));
                    <?php } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) { ?>
                    courseFee.replace(/\,/g, '');
                    <?php } ?>

                    if (paidAmount === null) {
                        // user กด Cancel
                    } else if (paidAmount.trim() === '') {
                        alert('ผิดพลาด: ไม่ได้กรอกยอดเงิน');
                    } else if (isNaN(paidAmount)) {
                        alert('ผิดพลาด: ต้องกรอกยอดเงินเป็นตัวเลข');
                    } else {
                        if (confirm("ยืนยันเปลี่ยนสถานะการลงทะเบียนเป็น '" + text + "' ?"
                            + '\n\nเมื่อกด OK ระบบจะบันทึกสถานะใหม่และยอดเงินที่ลูกค้าจ่ายจริงลงฐานข้อมูลทันที\n----------\n\nยอดเงินที่ลูกค้าจ่ายจริง: '
                            + formatNumber(paidAmount) + ' บาท')) {
                            doUpdateRegisterStatus(traineeId, newStatus, paidAmount);
                        }
                    }
                } else {
                    if (confirm("ยืนยันเปลี่ยนสถานะการลงทะเบียนเป็น '" + text + "' ?"
                        + '\n\nเมื่อกด OK ระบบจะบันทึกสถานะใหม่ลงฐานข้อมูลทันที')) {
                        doUpdateRegisterStatus(traineeId, newStatus, 'NULL', 'NULL');
                    }
                }
            }
        }

        function doUpdateRegisterStatus(traineeId, newStatus, paidAmount, paymentDate) {
            $('#manageRegisterStatusModal #spanLoading').show();

            $.post(
                '../api/api.php/update_register_status',
                {
                    serviceType: '<?php echo $serviceType; ?>',
                    traineeId: traineeId,
                    registerStatus: newStatus,
                    paidAmount: paidAmount,
                    paymentDate: paymentDate,
                }
            ).done(function (data) {
                $('#manageRegisterStatusModal #spanLoading').hide();
                if (data.error_code === 0) {
                    $('#manageRegisterStatusModal #alertSuccessText').text(data.error_message);
                    $('#manageRegisterStatusModal #alertSuccess').show();

                    //$('#formManageRegisterStatus #inputRegisterStatus').val(newStatus);
                    $('#inputRegisterStatus' + traineeId).val(newStatus);
                    const inputPaidType = $('#manageRegisterStatusModal #inputCoursePaidType');
                    if (inputPaidType.val() === 'paid') {
                        setButtonStatusClassForPaidCourse(newStatus);
                    } else {
                        setButtonStatusClassForFreeCourse(newStatus);
                    }

                    shouldReload = true;
                    //updateStatusButtonInTable(traineeId, oldStatus, newStatus);
                } else {
                    $('#manageRegisterStatusModal #alertErrorText').text(data.error_message);
                    $('#manageRegisterStatusModal #alertError').show();
                }
            }).fail(function () {
                $('#manageRegisterStatusModal #spanLoading').hide();
                $('#manageRegisterStatusModal #alertErrorText').text('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
                $('#manageRegisterStatusModal #alertError').show();
            });
        }

        const registerStatus = {
            'start': <?php echo $registerStatusList['start']; ?>,
            'wait-approve': <?php echo $registerStatusList['wait-approve']; ?>,
            'complete': <?php echo $registerStatusList['complete']; ?>,
            'cancel': <?php echo $registerStatusList['cancel']; ?>,
        };

        function updateStatusButtonInTable(traineeId, oldStatus, newStatus) {
            registerStatus[oldStatus]--;
            registerStatus[newStatus]++;

            /*อัพเดท Widget ด้านบน*/
            const divWidthRow = $('#divWidgetRow');
            if (divWidthRow.length !== 0) {
                divWidthRow.find('#spanStart').text(registerStatus['start']);
                divWidthRow.find('#spanWaitApprove').text(registerStatus['wait-approve']);
                divWidthRow.find('#spanComplete').text(registerStatus['complete']);
                divWidthRow.find('#spanCancel').text(registerStatus['cancel']);
            }
            /*alert(
                'start: ' + registerStatus['start']
                + ', wait-approve: ' + registerStatus['wait-approve']
                + ', complete: ' + registerStatus['complete']
                + ', cancel: ' + registerStatus['cancel']
            );*/

            let statusClass, statusText;
            switch (newStatus) {
                case 'start':
                    statusClass = 'btn-info';
                    statusText = 'ยังไม่ชำระเงิน';
                    break;
                case 'wait-approve':
                    statusClass = 'btn-warning';
                    statusText = 'รอตรวจสอบ';
                    break;
                case 'complete':
                    statusClass = 'btn-success';
                    statusText = 'สมบูรณ์';
                    break;
                case 'cancel':
                    statusClass = 'btn-danger';
                    statusText = 'ยกเลิก';
                    break;
            }

            const buttonStatus = $('#buttonStatus' + traineeId);
            buttonStatus.text(statusText);
            buttonStatus.removeClass('btn-default');
            buttonStatus.removeClass('btn-info');
            buttonStatus.removeClass('btn-danger');
            buttonStatus.removeClass('btn-success');
            buttonStatus.removeClass('btn-warning');
            buttonStatus.addClass(statusClass);
        }

        function doGetPaymentNotification(traineeId) {
            const loadingIcon = $('#spanGetPaymentNotificationLoading');
            const noPaymentNotificationAlert = $('#alertNoPaymentNotification');
            const paymentNotificationDetails = $('#paymentNotificationDetails');
            loadingIcon.show();
            noPaymentNotificationAlert.hide();
            paymentNotificationDetails.hide();

            //const traineeId = $('#manageRegisterStatusModal #inputTraineeId').val();

            $.post(
                '../api/api.php/get_payment_notification',
                {
                    serviceType: '<?php echo $serviceType; ?>',
                    traineeId: traineeId,
                }
            ).done(function (data) {
                loadingIcon.hide();
                if (data.error_code === 0) {
                    $('#confirmPaymentModal #inputTraineeId').val(traineeId);

                    if (data.data_list.length > 0) {
                        const {
                            id, member, amount, transfer_date_format, transfer_date_thai_format, slip_file_name, created_at, notification_date_format, notification_time_format
                        } = data.data_list[0];

                        const inputMemberName = $('#manageRegisterStatusModal #inputMemberName');
                        const inputNotificationDateTime = $('#manageRegisterStatusModal #inputNotificationDateTime');
                        const inputMemberPhone = $('#manageRegisterStatusModal #inputMemberPhone');
                        const inputMemberEmail = $('#manageRegisterStatusModal #inputMemberEmail');
                        const inputAmount = $('#manageRegisterStatusModal #inputAmount');
                        const inputPaymentDate = $('#manageRegisterStatusModal #inputPaymentDate');

                        /*inputPaymentDate.datepicker({
                            language: 'th',
                            thaiyear: true,
                            format: 'dd/mm/yyyy',
                            orientation: 'bottom',
                            autoclose: true
                        }).on('changeDate', e => {
                            e.target.setCustomValidity('');
                        }).datepicker('update', transfer_date_thai_format);*/

                        const imgPaymentSlip = $('#manageRegisterStatusModal #imgPaymentSlip');
                        const imgPaymentSlip2 = $('#manageRegisterStatusModal #imgPaymentSlip2');
                        const imgPaymentSlip3 = $('#manageRegisterStatusModal #imgPaymentSlip3');
                        const imgPaymentSlip4 = $('#manageRegisterStatusModal #imgPaymentSlip4');
                        const imgPaymentSlip5 = $('#manageRegisterStatusModal #imgPaymentSlip5');

                        if (member.id === 0) { // user ไม่ได้ login
                            inputMemberName.val('ผู้ใช้ไม่ได้ login');
                            inputMemberPhone.val('ผู้ใช้ไม่ได้ login');
                            inputMemberEmail.val('ผู้ใช้ไม่ได้ login');
                        } else {
                            //inputMemberName.val(`${member.title} ${member.first_name} ${member.last_name}  •  ${member.phone}  •  ${member.email}`);
                            inputMemberName.val(`${member.title} ${member.first_name} ${member.last_name}`);
                            inputMemberPhone.val(`${member.phone}`);
                            inputMemberEmail.val(`${member.email}`);
                        }

                        inputNotificationDateTime.val(`${notification_date_format}  •  ${notification_time_format}`);
                        inputAmount.val(formatNumber(amount));
                        inputPaymentDate.val(transfer_date_format);

                        //*********** confirmPaymentModal ***********
                        $('#confirmPaymentModal #inputAmountConfirm')[0].setCustomValidity('');
                        $('#confirmPaymentModal #inputAmountConfirm').val(amount);

                        $('#confirmPaymentModal #inputPaymentDateConfirm')[0].setCustomValidity('');
                        $('#confirmPaymentModal #inputPaymentDateConfirm').datepicker({
                            language: 'th',
                            thaiyear: true,
                            format: 'dd/mm/yyyy',
                            orientation: 'bottom',
                            autoclose: true
                        }).on('changeDate', e => {
                            e.target.setCustomValidity('');
                        }).datepicker('update', transfer_date_thai_format);
                        //*******************************************

                        imgPaymentSlip.parent().attr('href', '../images/ic_no_image.png');
                        imgPaymentSlip.parent().removeAttr('data-lightbox');
                        imgPaymentSlip.hide();
                        imgPaymentSlip2.parent().attr('href', '../images/ic_no_image.png');
                        imgPaymentSlip2.parent().removeAttr('data-lightbox');
                        imgPaymentSlip2.hide();
                        imgPaymentSlip3.parent().attr('href', '../images/ic_no_image.png');
                        imgPaymentSlip3.parent().removeAttr('data-lightbox');
                        imgPaymentSlip3.hide();
                        imgPaymentSlip4.parent().attr('href', '../images/ic_no_image.png');
                        imgPaymentSlip4.parent().removeAttr('data-lightbox');
                        imgPaymentSlip4.hide();
                        imgPaymentSlip5.parent().attr('href', '../images/ic_no_image.png');
                        imgPaymentSlip5.parent().removeAttr('data-lightbox');
                        imgPaymentSlip5.hide();

                        for (let i = 0; i < data.data_list.length; i++) {
                            const slipFileName = data.data_list[i].slip_file_name;

                            switch (i) {
                                case 0:
                                    /*imgPaymentSlip.attr('src', '../uploads/slip_images/' + slipFileName);
                                    imgPaymentSlip.show();*/

                                    imgPaymentSlip.attr('src', getFileExtension(slipFileName) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + slipFileName);
                                    imgPaymentSlip.parent().attr('href', getFileExtension(slipFileName) === 'pdf' ? '../uploads/slip_images/' + slipFileName : '../uploads/slip_images/' + slipFileName);
                                    setAttributes(getFileExtension(slipFileName) === 'pdf', imgPaymentSlip.parent(), 'slipImage');
                                    imgPaymentSlip.show();
                                    break;
                                case 1:
                                    imgPaymentSlip2.attr('src', getFileExtension(slipFileName) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + slipFileName);
                                    imgPaymentSlip2.parent().attr('href', getFileExtension(slipFileName) === 'pdf' ? '../uploads/slip_images/' + slipFileName : '../uploads/slip_images/' + slipFileName);
                                    setAttributes(getFileExtension(slipFileName) === 'pdf', imgPaymentSlip2.parent(), 'slipImage');
                                    imgPaymentSlip2.show();
                                    break;
                                case 2:
                                    imgPaymentSlip3.attr('src', getFileExtension(slipFileName) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + slipFileName);
                                    imgPaymentSlip3.parent().attr('href', getFileExtension(slipFileName) === 'pdf' ? '../uploads/slip_images/' + slipFileName : '../uploads/slip_images/' + slipFileName);
                                    setAttributes(getFileExtension(slipFileName) === 'pdf', imgPaymentSlip3.parent(), 'slipImage');
                                    imgPaymentSlip3.show();
                                    break;
                                case 3:
                                    imgPaymentSlip4.attr('src', getFileExtension(slipFileName) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + slipFileName);
                                    imgPaymentSlip4.parent().attr('href', getFileExtension(slipFileName) === 'pdf' ? '../uploads/slip_images/' + slipFileName : '../uploads/slip_images/' + slipFileName);
                                    setAttributes(getFileExtension(slipFileName) === 'pdf', imgPaymentSlip4.parent(), 'slipImage');
                                    imgPaymentSlip4.show();
                                    break;
                                case 4:
                                    imgPaymentSlip5.attr('src', getFileExtension(slipFileName) === 'pdf' ? '../images/pdf.png' : '../uploads/slip_images/' + slipFileName);
                                    imgPaymentSlip5.parent().attr('href', getFileExtension(slipFileName) === 'pdf' ? '../uploads/slip_images/' + slipFileName : '../uploads/slip_images/' + slipFileName);
                                    setAttributes(getFileExtension(slipFileName) === 'pdf', imgPaymentSlip5.parent(), 'slipImage');
                                    imgPaymentSlip5.show();
                                    break;
                            }
                        }
                        //imgPaymentSlip.attr('src', '../uploads/slip_images/' + slip_file_name);

                        paymentNotificationDetails.show();
                    } else {
                        noPaymentNotificationAlert.show();

                        $('#confirmPaymentModal #inputAmountConfirm').val('');
                        $('#confirmPaymentModal #inputPaymentDateConfirm').datepicker({
                            language: 'th',
                            thaiyear: true,
                            format: 'dd/mm/yyyy',
                            orientation: 'bottom',
                            autoclose: true
                        }).on('changeDate', e => {
                            e.target.setCustomValidity('');
                        }).datepicker('update', '');
                    }
                } else {
                    alert(data.error_message);
                }
            }).fail(function () {
                loadingIcon.hide();
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ Server');
            });
        }

        $('#inputDiscount').on('input', e => {
            const courseFee = $('#formPrintReceipt #inputCourseFee').val();
            const discount = $('#inputDiscount').val();
            if (discount.trim() === '' || isNaN(discount)) {
                $('#inputPaid').val('');
            } else {
                $('#inputPaid').val(courseFee - discount);
            }
        });
        $('#inputPaid').on('input', e => {
            const courseFee = $('#formPrintReceipt #inputCourseFee').val();
            const paid = $('#inputPaid').val();
            if (paid.trim() === '' || isNaN(paid)) {
                $('#inputDiscount').val('');
            } else {
                $('#inputDiscount').val(courseFee - paid);
            }
        });

        function onClickTraineeCountLink(element, title, message) {
            BootstrapDialog.show({
                title,
                message,
                buttons: [{
                    label: 'ปิด',
                    action: function (self) {
                        self.close();
                    },
                    cssClass: 'btn-primary'
                }]
            });
        }

        function doDeleteCourse(courseId) {
            $.post(
                '../api/api.php/delete_course',
                {
                    courseId: courseId,
                }
            ).done(function (data) {
                if (data.error_code === 0) {
                    location.reload(true);
                } else {
                    BootstrapDialog.show({
                        title: 'ลบหลักสูตร - ผิดพลาด',
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
                    title: 'ลบหลักสูตร - ผิดพลาด',
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
    <?php
}

?>