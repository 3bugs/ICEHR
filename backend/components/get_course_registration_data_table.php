<?php
require_once '../api/global.php';

/**
 * @param mysqli $db
 * @param string $serviceType
 * @param int $paramCourseId
 */
function getCourseRegistrationDataTable($db, $serviceType, $paramCourseId = null)
{
    $whereClause = ' TRUE ';
    if ($paramCourseId !== null) {
        $whereClause = " course_id = $paramCourseId ";
    }
    switch ($serviceType) {
        case SERVICE_TYPE_TRAINING:
            $sql = "SELECT ct.id, ct.form_number, ct.title, ct.first_name, ct.last_name, ct.phone, ct.email, ct.register_status, ct.created_at, ct.course_registration_id, ct.paid_amount,
                           cr.course_id, cr.coordinator_title, cr.coordinator_first_name, cr.coordinator_last_name, cr.coordinator_phone, cr.coordinator_email
                    FROM course_trainee ct 
                        INNER JOIN course_registration cr 
                            ON ct.course_registration_id = cr.id
                    WHERE $whereClause
                    ORDER BY ct.id DESC ";
            break;

        case SERVICE_TYPE_SOCIAL:
            $sql = "SELECT cr.id, cr.form_number, cr.title, cr.first_name, cr.last_name, cr.phone, cr.email, cr.contact_name, cr.contact_phone, 
                           cr.register_status, cr.created_at, cr.course_id
                    FROM course_registration_social cr  
                    WHERE $whereClause
                    ORDER BY id DESC";
            break;

        case SERVICE_TYPE_DRIVING_LICENSE:
            $sql = "SELECT cr.id, cr.form_number, cr.title, cr.first_name, cr.last_name, cr.phone, cr.register_status, cr.created_at, cr.course_id, cr.paid_amount,
                    ct.title AS course_type_title, ct.application_fee AS fee
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
            $trainee['form_number'] = $row['form_number'];
            $trainee['title'] = $row['title'];
            $trainee['first_name'] = $row['first_name'];
            $trainee['last_name'] = $row['last_name'];
            $trainee['phone'] = $row['phone'];
            $trainee['email'] = $row['email'];
            $trainee['created_at'] = $row['created_at'];
            $trainee['register_status'] = $row['register_status'];
            $trainee['paid_amount'] = $row['paid_amount'];
            $trainee['coordinator'] = array(
                'title' => $row['coordinator_title'],
                'first_name' => $row['coordinator_first_name'],
                'last_name' => $row['coordinator_last_name'],
                'phone' => $row['coordinator_phone'],
                'email' => $row['coordinator_email']
            );
            $trainee['course_id'] = (int)$row['course_id'];

            /*สำหรับบริการสังคม*/
            $trainee['contact_name'] = $row['contact_name'];
            $trainee['contact_phone'] = $row['contact_phone'];

            /*สำหรับบริการใบขับขี่*/
            $trainee['driving_license_course_type'] = $row['course_type_title'];
            $trainee['driving_license_course_fee'] = (int)$row['fee'];

            array_push($traineeList, $trainee);
        }
        $result->close();
    } else {
        echo 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: ' . $db->error . $sql;
        $db->close();
        exit();
    }

    ?>

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
                                        <i class="fa fa-dollar"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputCourseFee"
                                           placeholder="กรอกราคาเต็มของหลักสูตรนี้" required
                                           oninvalid="this.setCustomValidity('กรอกราคาเต็มของหลักสูตรนี้')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                            <!--ส่วนลด-->
                            <div class="form-group" style="display: none">
                                <label for="inputDiscount">ส่วนลด (บาท):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-dollar"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputDiscount"
                                           placeholder="กรอกส่วนลด"
                                           oninvalid="this.setCustomValidity('กรอกส่วนลด')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                            <!--ราคาที่ลูกค้าจ่ายจริง-->
                            <div class="form-group">
                                <label for="inputPaid">ราคาที่ลูกค้าจ่ายจริง (บาท):</label>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-dollar"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                           id="inputPaid" name="paid"
                                           placeholder="กรอกราคาที่ลูกค้าจ่ายจริง" required
                                           oninvalid="this.setCustomValidity('กรอกราคาที่ลูกค้าจ่ายจริง')"
                                           oninput="this.setCustomValidity('')">
                                </div>
                            </div>
                            <div id="responseText"
                                 style="text-align: center; color: red; margin-top: 25px; margin-bottom: 20px;">
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <button id="buttonPrint" type="submit"
                                    class="btn btn-info pull-right">
                                <span class="fa fa-save"></span>&nbsp;
                                พิมพ์
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

                        <!--สถานะการลงทะเบียน-->
                        <div class="btn-group pull-right">
                            <button id="buttonStatus" type="button" class="btn btn-default">
                                <span id="spanCurrentStatus" style="color: white">&nbsp;</span>
                            </button>
                            <button id="buttonStatusDropDown" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret" style="color: white"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:void(0)" onClick="updateRegisterStatus('start')">ไม่ได้ชำระเงิน</a></li>
                                <li><a href="javascript:void(0)" onClick="updateRegisterStatus('wait-approve')">แจ้งชำระเงิน</a></li>
                                <li><a href="javascript:void(0)" onClick="updateRegisterStatus('complete')">สมบูรณ์</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0)" onClick="updateRegisterStatus('cancel')">ยกเลิก</a></li>
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
                                <li class="active"><a href="#tab_1" data-toggle="tab">ข้อมูลใบสมัคร</a></li>
                                <li><a href="#tab_2" data-toggle="tab">การแจ้งโอนเงิน</a></li>
                                <!--<li><a href="#tab_3" data-toggle="tab">ยอดเงินจ่ายจริง</a></li>-->
                            </ul>
                            <div class="tab-content">

                                <!--แท็บ 1 - ข้อมูลใบสมัคร-->
                                <div class="tab-pane active" id="tab_1">
                                    <div class="box-body">
                                        <input type="hidden" id="inputTraineeId">
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
                                        if ($serviceType === SERVICE_TYPE_TRAINING) {
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
                                                <div class="form-group col-md-6">
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
                                                <div class="form-group col-md-6">
                                                    <label for="inputPaidAmount">ยอดเงินที่ลูกค้าจ่ายจริง (บาท):</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <strong>฿</strong>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputPaidAmount">
                                                    </div>
                                                    <div style="color: red; margin-top: 5px">ยอดเงินจ่ายจริง จะให้กรอกตอนเปลี่ยนสถานะการลงทะเบียนที่ช่องมุมบนขวาเป็น "สมบูรณ์"</div>
                                                </div>
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
                                                <div class="form-group col-md-6">
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
                                                <div class="form-group col-md-6">
                                                    <label for="inputPaidAmount">ยอดเงินที่ลูกค้าจ่ายจริง (บาท):</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <strong>฿</strong>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputPaidAmount">
                                                    </div>
                                                    <div style="color: red; margin-top: 5px">ยอดเงินจ่ายจริง จะให้กรอกตอนเปลี่ยนสถานะการลงทะเบียนที่ช่องมุมบนขวาเป็น "สมบูรณ์"</div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.tab-pane -->

                                <!--แท็บ 2 - หลักฐานการโอนเงิน-->
                                <div class="tab-pane" id="tab_2">
                                    <div class="box-body">
                                        <div id="paymentNotificationDetails">
                                            <div class="row">
                                                <!--ผู้แจ้งโอนเงิน (สมาชิกที่ login)-->
                                                <div class="form-group col-md-8">
                                                    <label for="inputMemberName">แจ้งโดย:</label>
                                                    <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-user"></i>
                                                            </span>
                                                        <input type="text" class="form-control" disabled
                                                               id="inputMemberName">
                                                    </div>
                                                </div>
                                                <!--วัน/เวลาที่แจ้ง-->
                                                <div class="form-group col-md-4">
                                                    <label for="inputNotificationDateTime">เมื่อ:</label>
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
                                                <div class="col-md-12" style="text-align: center">
                                                    <img id="imgPaymentSlip" style="height: 600px; width: 600px; object-fit: contain"/>
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

                                <!--แท็บ 3 - ยอดเงินจ่ายจริง-->
                                <!--<div class="tab-pane" id="tab_3">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="inputPaidAmount">ยอดเงินที่ลูกค้าจ่ายจริง (บาท):</label>
                                                <div class="input-group">
                                                            <span class="input-group-addon">
                                                                <strong>฿</strong>
                                                            </span>
                                                    <input type="text" class="form-control"
                                                           id="inputPaidAmount">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>-->
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

    <!--Data table-->
    <table id="tableRegistration" class="table table-bordered table-striped">
        <thead>
        <tr>
            <?php
            if ($serviceType === SERVICE_TYPE_TRAINING) {
                ?>
                <th style="width: 10%; text-align: center">เลขที่</th>
                <th style="width: 20%; text-align: center">ผู้สมัคร</th>
                <th style="width: 20%; text-align: center">ผู้ประสานงาน</th>
                <th style="width: 27%; text-align: center">หลักสูตรที่สมัคร</th>
                <th style="width: 10%; text-align: center">วันอบรม</th>
                <th style="width: 13%; text-align: center">วัน/เวลาที่สมัคร</th>
                <th style="text-align: center">สถานะ</th>
                <th style="text-align: center" nowrap>พิมพ์</th>
                <?php
            } else if ($serviceType === SERVICE_TYPE_SOCIAL) {
                ?>
                <th style="width: 10%; text-align: center">เลขที่</th>
                <th style="width: 20%; text-align: center">ผู้สมัคร</th>
                <th style="width: 20%; text-align: center">ผู้ติดต่อ</th>
                <th style="width: 27%; text-align: center">หลักสูตรที่สมัคร</th>
                <th style="width: 10%; text-align: center">วันอบรม</th>
                <th style="width: 13%; text-align: center">วัน/เวลาที่สมัคร</th>
                <th style="text-align: center" nowrap>พิมพ์</th>
                <?php
            } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
                ?>
                <th style="width: 15%; text-align: center">เลขที่</th>
                <th style="width: 25%; text-align: center">ผู้สมัคร</th>
                <th style="width: 35%; text-align: center">ประเภท/ราคา</th>
                <th style="width: 10%; text-align: center">วันอบรม</th>
                <th style="width: 20%; text-align: center">วัน/เวลาที่สมัคร</th>
                <th style="text-align: center">สถานะ</th>
                <th style="text-align: center" nowrap>พิมพ์</th>
                <?php
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
            <tr valign="middle">
                <td colspan="20" align="center">ไม่มีข้อมูล</td>
            </tr>
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

                $paidAmount = $trainee['paid_amount'] == null ? '' : $trainee['paid_amount'];

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
                        $courseApplicationFee = (int)$row['application_fee'];

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
                        <td style="vertical-align: top"><?php echo $courseDetails; ?></td>
                        <?php
                    } else if ($serviceType === SERVICE_TYPE_SOCIAL) {
                        ?>
                        <td style="vertical-align: top"><?php echo "{$trainee['contact_name']}<br/><i class=\"fa fa-phone\" style=\"color: black\"></i> {$trainee['contact_phone']}"; ?></td>
                        <td style="vertical-align: top"><?php echo $courseDetails; ?></td>
                        <?php
                    } else if ($serviceType === SERVICE_TYPE_DRIVING_LICENSE) {
                        ?>
                        <td style="vertical-align: top">
                            <?php
                            echo sprintf(
                                '%s<br/><strong>฿</strong> %s บาท',
                                $trainee['driving_license_course_type'],
                                $trainee['driving_license_course_fee']
                            );
                            ?>
                        </td>
                        <?php
                    }
                    ?>

                    <td style="vertical-align: top; text-align: center"><?php echo $courseDateHidden . $courseDate; ?></td>
                    <td style="vertical-align: top; text-align: center"><?php echo($dateHidden . $displayDateTime); ?></td>

                    <?php
                    if ($serviceType !== SERVICE_TYPE_SOCIAL) {
                        ?>
                        <td style="vertical-align: top; text-align: center" nowrap>
                            <?php
                            $btnClass = '';
                            $btnText = '';
                            $sortOrder = 0;
                            switch ($registerStatus) {
                                case 'start':
                                    $btnClass = 'btn-info';
                                    $btnText = 'ยังไม่ชำระเงิน';
                                    $sortOrder = 3;
                                    break;
                                case 'wait-approve':
                                    $btnClass = 'btn-warning';
                                    $btnText = 'แจ้งชำระเงิน';
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
                                            '<?php echo $formNumber; ?>',
                                    <?php echo $traineeId; ?>,
                                            '<?php echo "{$trainee['title']} {$trainee['first_name']} {$trainee['last_name']}  •  {$trainee['phone']}  •  {$trainee['email']}"; ?>',
                                            '<?php echo($trainee['coordinator']['first_name'] ? "{$trainee['coordinator']['title']} {$trainee['coordinator']['first_name']} {$trainee['coordinator']['last_name']}  •  {$trainee['coordinator']['phone']}  •  {$trainee['coordinator']['email']}" : ''); ?>',
                                            '<?php echo($serviceType === SERVICE_TYPE_TRAINING ? $courseDetails : $trainee['driving_license_course_type']); ?>',
                                            '<?php echo($serviceType === SERVICE_TYPE_TRAINING ? number_format((string)$courseApplicationFee) : number_format((string)$trainee['driving_license_course_fee'])); ?>',
                                            '<?php echo $paidAmount; ?>'
                                            )">
                                <?php echo $btnText ?>
                            </button>

                            <?php
                            if ($paidAmount !== '') {
                                ?>
                                <div style="text-align: center; margin-top: 5px">
                                    <?php echo number_format($paidAmount); ?>&nbsp;บาท
                                </div>
                                <?php
                            }
                            ?>

                            <input type="hidden" name="traineeId" value="<?php echo $traineeId; ?>"/>
                            <input id="inputRegisterStatus<?php echo $traineeId; ?>" type="hidden" value="<?php echo $registerStatus; ?>"/>
                        </td>
                        <?php
                    }
                    ?>
                    <td style="vertical-align: top; text-align: center" nowrap>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li><a target="_blank" href="print_registration_form.php?service_type=<?php echo $serviceType; ?>&form_number=<?php echo $formNumber; ?>">
                                        <i class="fa fa-print"></i>ใบสมัคร
                                    </a>
                                </li>
                                <li><a href="javascript:void(0)"
                                       onClick="onClickPrintRegForm()">
                                        <i class="fa fa-print"></i>รูปสลิปการโอนเงิน
                                    </a>
                                </li>
                                <li><a href="javascript:void(0)"
                                       onClick="onClickPrintReceipt('<?php echo $formNumber; ?>', <?php echo $traineeId; ?>, '<?php echo "{$trainee['title']} {$trainee['first_name']} {$trainee['last_name']}"; ?>', '<?php echo $courseDetails; ?>', <?php echo $courseApplicationFee; ?>)">
                                        <i class="fa fa-print"></i>ใบเสร็จรับเงิน
                                    </a>
                                </li>
                                <li><a href="javascript:void(0)"
                                       onClick="onClickPrintCertificate()">
                                        <i class="fa fa-print"></i>ใบรับรองการผ่านอบรม
                                    </a>
                                </li>
                                <!--<li class="divider"></li>-->
                            </ul>
                        </div>
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
                }
            });

            $('#manageRegisterStatusModal').on('hidden.bs.modal', () => {
                if (shouldReload) {
                    location.reload(true);
                }
            });
        });

        function onClickPrintReceipt(formNumber, traineeId, traineeName, courseName, courseApplicationFee) {
            const registerStatus = $('#inputRegisterStatus' + traineeId).val();
            const canPrint = registerStatus === 'complete';

            $('#printReceiptModal #spanFormNumber').text(formNumber);
            $('#formPrintReceipt #inputTraineeId').val(traineeId);
            $('#formPrintReceipt #inputTraineeName').val(traineeName);
            $('#formPrintReceipt #inputCourseName').val(courseName);
            $('#formPrintReceipt #inputCourseFee').val(courseApplicationFee);
            $('#formPrintReceipt #buttonPrint').prop('disabled', !canPrint);
            const divCallout = $('#formPrintReceipt #alertCanNotPrint');
            canPrint ? divCallout.hide() : divCallout.show();

            $('#printReceiptModal').modal('show');
        }

        function onClickStatus(formNumber, traineeId, traineeName, coordinatorName, courseName, courseApplicationFee, paidAmount) {
            doGetPaymentNotification(traineeId);

            $('#manageRegisterStatusModal #spanFormNumber').text(formNumber);
            $('#manageRegisterStatusModal #inputTraineeId').val(traineeId);
            //$('#formManageRegisterStatus #inputRegisterStatus').val(registerStatus);
            $('#manageRegisterStatusModal #inputTraineeName').val(traineeName);
            $('#manageRegisterStatusModal #inputCoordinatorName').val(coordinatorName);

            coordinatorName ? $('#manageRegisterStatusModal #divCoordinatorName').show()
                : $('#manageRegisterStatusModal #divCoordinatorName').hide();

            $('#manageRegisterStatusModal #inputCourseName').val(courseName);
            $('#manageRegisterStatusModal #inputCourseFee').val(courseApplicationFee);
            $('#manageRegisterStatusModal #inputPaidAmount').val(paidAmount);
            $('#manageRegisterStatusModal #alertSuccess').hide();
            $('#manageRegisterStatusModal #alertError').hide();
            $('#spanLoading').hide();

            const registerStatus = $('#inputRegisterStatus' + traineeId).val();
            setButtonStatusClass(registerStatus);

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

        function setButtonStatusClass(registerStatus) {
            let statusClass, statusText;
            switch (registerStatus) {
                case 'start':
                    statusClass = 'btn-info';
                    statusText = 'ยังไม่ได้ชำระเงิน';
                    break;
                case 'wait-approve':
                    statusClass = 'btn-warning';
                    statusText = 'แจ้งชำระเงิน';
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

        function updateRegisterStatus(newStatus) {
            $('#manageRegisterStatusModal #alertSuccess').hide();
            $('#manageRegisterStatusModal #alertError').hide();

            const traineeId = $('#manageRegisterStatusModal #inputTraineeId').val();
            const oldStatus = $('#inputRegisterStatus' + traineeId).val();
            //const paidAmount = $('#manageRegisterStatusModal #inputPaidAmount').val(); // ยอดเงินจ่ายจริง

            //const oldStatus = $('#formManageRegisterStatus #inputRegisterStatus').val();
            //alert('Trainee ID: ' + traineeId + ', Old Status: ' + oldStatus + ', New Status: ' + newStatus);

            if (oldStatus !== newStatus) {
                let text = '';
                switch (newStatus) {
                    case 'start':
                        text = 'ยังไม่ได้ชำระเงิน';
                        break;
                    case 'wait-approve':
                        text = 'แจ้งชำระเงิน';
                        break;
                    case 'complete':
                        text = 'สมบูรณ์';
                        break;
                    case 'cancel':
                        text = 'ยกเลิก';
                        break;
                }

                if (newStatus === 'complete') {
                    const paidAmount = prompt('กรอกยอดเงินที่ลูกค้าจ่ายจริง:', '');
                    if (paidAmount === null) {
                        // user กด Cancel
                    } else if (paidAmount.trim() === '') {
                        alert('ผิดพลาด: ไม่ได้กรอกยอดเงิน');
                    } else if (isNaN(paidAmount)) {
                        alert('ผิดพลาด: ต้องกรอกยอดเงินเป็นตัวเลข');
                    } else {
                        if (confirm("ยืนยันเปลี่ยนสถานะการลงทะเบียนเป็น '" + text + "' ?"
                            + '\n\nเมื่อกด OK ระบบจะบันทึกสถานะใหม่และยอดเงินที่ลูกค้าจ่ายจริงลงฐานข้อมูลทันที')) {
                            doUpdateRegisterStatus(traineeId, newStatus, paidAmount);
                        }
                    }
                } else {
                    if (confirm("ยืนยันเปลี่ยนสถานะการลงทะเบียนเป็น '" + text + "' ?"
                        + '\n\nเมื่อกด OK ระบบจะบันทึกสถานะใหม่ลงฐานข้อมูลทันที')) {
                        doUpdateRegisterStatus(traineeId, newStatus, 'NULL');
                    }
                }
            }
        }

        function doUpdateRegisterStatus(traineeId, newStatus, paidAmount) {
            $('#spanLoading').show();

            $.post(
                '../api/api.php/update_register_status',
                {
                    serviceType: '<?php echo $serviceType; ?>',
                    traineeId: traineeId,
                    registerStatus: newStatus,
                    paidAmount: paidAmount,
                }
            ).done(function (data) {
                $('#spanLoading').hide();
                if (data.error_code === 0) {
                    $('#manageRegisterStatusModal #alertSuccessText').text(data.error_message);
                    $('#manageRegisterStatusModal #alertSuccess').show();

                    //$('#formManageRegisterStatus #inputRegisterStatus').val(newStatus);
                    $('#inputRegisterStatus' + traineeId).val(newStatus);
                    setButtonStatusClass(newStatus);

                    shouldReload = true;
                    //updateStatusButtonInTable(traineeId, oldStatus, newStatus);
                } else {
                    $('#manageRegisterStatusModal #alertErrorText').text(data.error_message);
                    $('#manageRegisterStatusModal #alertError').show();
                }
            }).fail(function () {
                $('#spanLoading').hide();
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
                    statusText = 'แจ้งชำระเงิน';
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
                    if (data.data_list.length > 0) {
                        const {
                            member, slip_file_name, created_at, notification_date_format, notification_time_format
                        } = data.data_list[0];

                        const inputMemberName = $('#manageRegisterStatusModal #inputMemberName');
                        const inputNotificationDateTime = $('#manageRegisterStatusModal #inputNotificationDateTime');
                        //const inputMemberPhone = $('#manageRegisterStatusModal #inputMemberPhone');
                        //const inputMemberEmail = $('#manageRegisterStatusModal #inputMemberEmail');
                        const imgPaymentSlip = $('#imgPaymentSlip');

                        if (member.id === 0) { // user ไม่ได้ login
                            inputMemberName.val('ผู้ใช้ไม่ได้ login');
                            /*inputMemberPhone.val('');
                            inputMemberEmail.val('');*/
                        } else {
                            inputMemberName.val(`${member.title} ${member.first_name} ${member.last_name}  •  ${member.phone}  •  ${member.email}`);
                            /*inputMemberPhone.val(`${member.phone}`);
                            inputMemberEmail.val(`${member.email}`);*/
                        }

                        inputNotificationDateTime.val(`${notification_date_format}  •  ${notification_time_format}`);
                        imgPaymentSlip.attr('src', '../uploads/slip_images/' + slip_file_name);

                        paymentNotificationDetails.show();
                    } else {
                        noPaymentNotificationAlert.show();
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